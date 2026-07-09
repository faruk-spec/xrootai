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
        // 1. Check AI routing rules in DB first
        $targetModelIdentifier = $model;
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_routing_rules')) {
            $routingRule = \App\Models\AIRoutingRule::with('targetModel')->where('is_active', true)
                ->where('trigger_type', 'pattern')
                ->orderBy('priority', 'desc')
                ->get()
                ->first(function ($rule) use ($model) {
                    if (empty($rule->pattern)) return false;
                    return @preg_match('/' . preg_quote($rule->pattern, '/') . '/i', $model) || stripos($model, $rule->pattern) !== false;
                });

            if ($routingRule && $routingRule->targetModel) {
                $targetModelIdentifier = $routingRule->targetModel->model_identifier;
            }
        }

        $providerName = $this->resolveProviderName($targetModelIdentifier);
        
        $apiKey = null;
        if ($user && $user->exists) {
            // Fetch decrypted key from DB if exists
            $apiKeyRecord = ApiKey::where('user_id', $user->id)
                ->where('provider', $providerName)
                ->where('is_active', true)
                ->first();
                
            $apiKey = $apiKeyRecord ? $apiKeyRecord->encrypted_key : null;
        }
            
        // Fallback to dynamic DB providers config
        $providerRecord = null;
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_providers')) {
            $providerRecord = \App\Models\AIProvider::where('slug', $providerName)->first();
        }

        if (!$apiKey && $providerRecord) {
            $apiKey = $providerRecord->api_key;
        }

        // Fallback to global admin settings keys if user has not set their own key
        if (!$apiKey) {
            $apiKey = match ($providerName) {
                'openai' => \App\Models\SystemSetting::get('model_openai_key'),
                'gemini' => \App\Models\SystemSetting::get('model_gemini_key'),
                'claude' => \App\Models\SystemSetting::get('model_claude_key'),
                'deepseek' => \App\Models\SystemSetting::get('model_deepseek_key'),
                'ollama' => \App\Models\SystemSetting::get('model_ollama_url'),
                default => null,
            };
        }

        $customBaseUrl = $providerRecord && $providerRecord->base_url ? $providerRecord->base_url : null;

        $providerInstance = match ($providerName) {
            'openai' => new OpenAIProvider($apiKey),
            'gemini' => new GeminiProvider($apiKey),
            'claude' => new ClaudeProvider($apiKey),
            'deepseek' => new DeepSeekProvider($apiKey),
            'ollama' => new OllamaProvider($apiKey ?: ($customBaseUrl ?: 'http://localhost:11434')),
            'mock' => new MockProvider(),
            default => throw new InvalidArgumentException("Unsupported provider: {$providerName}"),
        };

        if ($customBaseUrl && method_exists($providerInstance, 'setBaseUrl')) {
            $providerInstance->setBaseUrl($customBaseUrl);
        }

        return $providerInstance;
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
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_models')) {
            $dbModels = \App\Models\AIModel::with('provider')->where('is_active', true)->get();
            if ($dbModels->isNotEmpty()) {
                $allModels = [];
                foreach ($dbModels as $model) {
                    $allModels[] = [
                        'id' => $model->model_identifier,
                        'name' => $model->name,
                        'context' => $model->context_window,
                        'provider' => $model->provider->slug,
                    ];
                }
                return $allModels;
            }
        }

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
