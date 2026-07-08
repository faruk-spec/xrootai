<?php

namespace App\Services\AI\Providers;

class GeminiProvider extends OpenAIProvider
{
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions';

    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        $this->apiKey = $this->apiKey ?: config('services.gemini.key');
        
        parent::streamCompletion($messages, $options, $onChunk);
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'gemini-1.5-flash', 'name' => 'Gemini 1.5 Flash', 'context' => 1048576],
            ['id' => 'gemini-1.5-pro', 'name' => 'Gemini 1.5 Pro', 'context' => 2097152],
            ['id' => 'gemini-2.0-flash-exp', 'name' => 'Gemini 2.0 Flash (Experimental)', 'context' => 1048576],
        ];
    }
}
