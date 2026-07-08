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
