<?php

namespace App\Services\AI\Providers;

class DeepSeekProvider extends OpenAIProvider
{
    protected string $baseUrl = 'https://api.deepseek.com/chat/completions';

    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        // DeepSeek requires a different key source
        $this->apiKey = $this->apiKey ?: config('services.deepseek.key');
        
        parent::streamCompletion($messages, $options, $onChunk);
    }

    public function getAvailableModels(): array
    {
        return [
            ['id' => 'deepseek-chat', 'name' => 'DeepSeek V3 (Chat)', 'context' => 64000],
            ['id' => 'deepseek-reasoner', 'name' => 'DeepSeek R1 (Reasoner)', 'context' => 64000],
        ];
    }
}
