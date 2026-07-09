<?php

namespace App\Services\AI\Providers;

class OpenAIProvider extends AbstractProvider
{
    protected string $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        $apiKey = $this->apiKey ?: config('services.openai.key');

        if (empty($apiKey)) {
            $onChunk("[Error: OpenAI API Key is missing. Please add it in settings.]");
            return;
        }

        $body = array_merge([
            'model' => $options['model'] ?? 'gpt-4o-mini',
            'messages' => $messages,
            'stream' => true,
        ], array_intersect_key($options, array_flip(['temperature', 'max_tokens', 'top_p'])));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeaders($apiKey));

        $buffer = '';
        $rawBody = '';
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$buffer, &$rawBody, $onChunk) {
            $rawBody .= $data;
            $buffer .= $data;
            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                $line = trim($line);
                
                if (str_starts_with($line, 'data: ')) {
                    $jsonData = substr($line, 6);
                    if ($jsonData === '[DONE]') {
                        continue;
                    }
                    $decoded = json_decode($jsonData, true);
                    $text = $decoded['choices'][0]['delta']['content'] ?? '';
                    if ($text !== '') {
                        $onChunk($text);
                    }
                }
            }
            return strlen($data);
        });

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            $onChunk("[Connection Error: $error]");
        } elseif ($httpCode >= 400) {
            $errorDetail = trim($rawBody) ?: trim($buffer) ?: "HTTP Status $httpCode";
            $decodedErr = json_decode($errorDetail, true);
            $msg = $decodedErr['error']['message'] ?? $decodedErr['message'] ?? $errorDetail;
            
            if ($httpCode === 401) {
                $onChunk("\n\n⚠️ **AI API Authentication Error (401 Unauthorized)**\n\nThe API key configured for this AI model is invalid, missing, or expired.\n\n* **Provider Error Details:** `{$msg}`\n* **How to fix:** Please navigate to **Admin Panel → AI Providers / Settings** and verify that the exact API key (without extra spaces or line breaks) is saved correctly.");
            } elseif ($httpCode === 429) {
                $onChunk("\n\n⚠️ **Rate Limit Exceeded (429)**\n\nThe AI provider's rate limit or credit quota has been reached.\n\n* **Provider Error Details:** `{$msg}`");
            } else {
                $onChunk("\n\n⚠️ **AI Provider API Error ({$httpCode})**\n\n* **Provider Error Details:** `{$msg}`");
            }
        }

        curl_close($ch);
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'gpt-4o', 'name' => 'GPT-4o', 'context' => 128000],
            ['id' => 'gpt-4o-mini', 'name' => 'GPT-4o Mini', 'context' => 128000],
            ['id' => 'o1-mini', 'name' => 'o1 Mini', 'context' => 128000],
        ];
    }

    protected function getHeaders(?string $key = null): array
    {
        return [
            'Content-Type: application/json',
            'Authorization: Bearer ' . ($key ?: $this->apiKey),
        ];
    }
}
