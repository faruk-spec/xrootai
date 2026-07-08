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
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$buffer, $onChunk) {
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
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            $onChunk("[Connection Error: $error]");
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
