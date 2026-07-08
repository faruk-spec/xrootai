<?php

namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;
use Exception;

class OllamaProvider extends OpenAIProvider
{
    protected string $defaultHost = 'http://localhost:11434';

    public function __construct(?string $host = null, array $config = [])
    {
        $host = $host ? trim($host) : config('services.ollama.host', $this->defaultHost);
        // Ensure no trailing slash
        $host = rtrim($host, '/');
        
        $this->apiKey = 'ollama-no-key'; // Ollama doesn't require a key by default
        $this->baseUrl = "{$host}/v1/chat/completions";
        
        parent::__construct($this->apiKey, $config);
    }

    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        // Strip the "ollama-" prefix from the model name before passing to Ollama
        $options['model'] = str_replace('ollama-', '', $options['model'] ?? 'llama3');
        
        parent::streamCompletion($messages, $options, $onChunk);
    }

    public function getAvailableModels(): array
    {
        $host = rtrim($this->baseUrl ? str_replace('/v1/chat/completions', '', $this->baseUrl) : $this->defaultHost, '/');
        
        $fallbackModels = [
            ['id' => 'ollama-llama3', 'name' => 'Ollama: Llama 3', 'context' => 8192],
            ['id' => 'ollama-mistral', 'name' => 'Ollama: Mistral', 'context' => 8192],
            ['id' => 'ollama-gemma2', 'name' => 'Ollama: Gemma 2', 'context' => 8192],
            ['id' => 'ollama-codegemma', 'name' => 'Ollama: CodeGemma', 'context' => 8192],
        ];

        try {
            // Attempt to dynamically fetch installed models from local Ollama instance
            $response = Http::timeout(1.5)->get("{$host}/api/tags");
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['models']) && is_array($data['models']) && count($data['models']) > 0) {
                    $dynamicModels = [];
                    foreach ($data['models'] as $model) {
                        $name = $model['name'];
                        $cleanId = 'ollama-' . str_replace(':latest', '', $name);
                        $displayName = 'Ollama: ' . ucfirst(str_replace(':latest', '', $name));
                        $dynamicModels[] = [
                            'id' => $cleanId,
                            'name' => $displayName,
                            'context' => 8192,
                        ];
                    }
                    return $dynamicModels;
                }
            }
        } catch (Exception $e) {
            // Fall back quietly to default models if Ollama is not running
        }

        return $fallbackModels;
    }
}
