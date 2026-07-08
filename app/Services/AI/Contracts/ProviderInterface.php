<?php

namespace App\Services\AI\Contracts;

interface ProviderInterface
{
    /**
     * Streams completions chunk by chunk.
     *
     * @param array $messages   Formatted array of chat messages.
     * @param array $options    Additional generation parameters (temperature, max_tokens, etc).
     * @param callable $onChunk Callback invoked for each text chunk received: function(string $chunkText): void
     * @return void
     */
    public function streamCompletion(array $messages, array $options, callable $onChunk): void;

    /**
     * Get the display name and details of models offered by this provider.
     *
     * @return array
     */
    public function getAvailableModels(): array;
}
