<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\ProviderInterface;
use Illuminate\Support\Facades\Http;

abstract class AbstractProvider implements ProviderInterface
{
    protected ?string $apiKey;
    protected array $config;

    public function __construct(?string $apiKey = null, array $config = [])
    {
        $this->apiKey = $apiKey;
        $this->config = $config;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Dynamically override the API base URL.
     */
    public function setBaseUrl(string $baseUrl): void
    {
        if (property_exists($this, 'baseUrl')) {
            if ($this->baseUrl && str_contains($this->baseUrl, '/chat/completions') && !str_contains($baseUrl, '/chat/completions')) {
                $this->baseUrl = rtrim($baseUrl, '/') . '/chat/completions';
            } elseif ($this->baseUrl && str_contains($this->baseUrl, '/messages') && !str_contains($baseUrl, '/messages')) {
                $this->baseUrl = rtrim($baseUrl, '/') . '/messages';
            } else {
                $this->baseUrl = $baseUrl;
            }
        }
    }

    /**
     * Resolves the request parameters.
     */
    protected function getHttpClient()
    {
        return Http::withHeaders($this->getHeaders())
            ->timeout(60);
    }

    /**
     * Get request headers.
     */
    abstract protected function getHeaders(): array;
}
