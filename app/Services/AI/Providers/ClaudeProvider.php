<?php

namespace App\Services\AI\Providers;

class ClaudeProvider extends AbstractProvider
{
    protected string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        $apiKey = $this->apiKey ?: config('services.claude.key');

        if (empty($apiKey)) {
            $onChunk("[Error: Anthropic API Key is missing. Please add it in settings.]");
            return;
        }

        // Anthropic requires system prompt to be in a top-level field and max_tokens is mandatory
        $systemPrompt = '';
        $filteredMessages = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemPrompt .= ($systemPrompt ? "\n" : "") . $msg['content'];
            } else {
                $filteredMessages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        $body = [
            'model' => $options['model'] ?? 'claude-3-5-sonnet-20241022',
            'messages' => $filteredMessages,
            'max_tokens' => $options['max_tokens'] ?? 4096,
            'stream' => true,
        ];

        if (!empty($systemPrompt)) {
            $body['system'] = $systemPrompt;
        }

        if (isset($options['temperature'])) {
            $body['temperature'] = (float)$options['temperature'];
        }

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
                    $decoded = json_decode($jsonData, true);
                    
                    if ($decoded && isset($decoded['delta']['text'])) {
                        $onChunk($decoded['delta']['text']);
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
            $errorDetail = trim($buffer) ?: "HTTP Status $httpCode";
            $decodedErr = json_decode($errorDetail, true);
            $msg = $decodedErr['error']['message'] ?? $errorDetail;
            $onChunk("[Anthropic/Claude API Error ({$httpCode}): {$msg}]");
        }

        curl_close($ch);
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'claude-3-5-sonnet-20241022', 'name' => 'Claude 3.5 Sonnet', 'context' => 200000],
            ['id' => 'claude-3-5-haiku-20241022', 'name' => 'Claude 3.5 Haiku', 'context' => 200000],
            ['id' => 'claude-3-opus-20240229', 'name' => 'Claude 3 Opus', 'context' => 200000],
        ];
    }

    protected function getHeaders(?string $key = null): array
    {
        return [
            'content-type: application/json',
            'x-api-key: ' . ($key ?: $this->apiKey),
            'anthropic-version: 2023-06-01',
        ];
    }
}
