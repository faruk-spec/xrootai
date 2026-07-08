<?php

namespace App\Services\AI;

use App\Models\User;
use App\Models\ApiKey;
use App\Services\AI\Contracts\ProviderInterface;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\ClaudeProvider;
use App\Services\AI\Providers\DeepSeekProvider;
use App\Services\AI\Providers\MockProvider;
use App\Services\AI\Providers\OllamaProvider;
use InvalidArgumentException;

class AIProviderManager
{
    /**
     * Resolve a provider strategy by model identifier.
     */
    public function make(string $model, User $user): ProviderInterface
    {
        $providerName = $this->resolveProviderName($model);
        
        // Fetch decrypted key from DB if exists
        $apiKeyRecord = ApiKey::where('user_id', $user->id)
            ->where('provider', $providerName)
            ->where('is_active', true)
            ->first();
            
        $apiKey = $apiKeyRecord ? $apiKeyRecord->encrypted_key : null;

        return match ($providerName) {
            'openai' => new OpenAIProvider($apiKey),
            'gemini' => new GeminiProvider($apiKey),
            'claude' => new ClaudeProvider($apiKey),
            'deepseek' => new DeepSeekProvider($apiKey),
            'ollama' => new OllamaProvider($apiKey),
            'mock' => new MockProvider(),
            default => throw new InvalidArgumentException("Unsupported provider: {$providerName}"),
        };
    }

    /**
     * Determine the provider name based on model string.
     */
    public function resolveProviderName(string $model): string
    {
        $model = strtolower($model);

        if (str_starts_with($model, 'gpt-') || str_starts_with($model, 'o1-')) {
            return 'openai';
        }
        if (str_starts_with($model, 'gemini-')) {
            return 'gemini';
        }
        if (str_starts_with($model, 'claude-')) {
            return 'claude';
        }
        if (str_starts_with($model, 'deepseek-')) {
            return 'deepseek';
        }
        if (str_starts_with($model, 'ollama-')) {
            return 'ollama';
        }

        return 'mock';
    }

    /**
     * List all models across all enabled providers.
     */
    public function getAllModels(): array
    {
        $providers = [
            new MockProvider(),
            new OpenAIProvider(),
            new GeminiProvider(),
            new ClaudeProvider(),
            new DeepSeekProvider(),
            new OllamaProvider(),
        ];

        $allModels = [];
        foreach ($providers as $provider) {
            $providerName = $this->resolveProviderName($provider->getAvailableModels()[0]['id'] ?? 'mock');
            foreach ($provider->getAvailableModels() as $model) {
                $model['provider'] = $providerName;
                $allModels[] = $model;
            }
        }

        return $allModels;
    }
}
