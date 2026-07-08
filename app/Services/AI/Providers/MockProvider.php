<?php

namespace App\Services\AI\Providers;

class MockProvider extends AbstractProvider
{
    public function streamCompletion(array $messages, array $options, callable $onChunk): void
    {
        $prompt = end($messages)['content'] ?? 'hello';
        
        $responseText = "Hello! I am a simulated AI assistant running locally on XrootAI.\n\n" .
            "You asked me: *\"" . htmlspecialchars($prompt) . "\"*\n\n" .
            "Here is a demonstration of what I can format for you:\n\n" .
            "### 1. Markdown Features\n" .
            "- **Bold text** and *italicized text*\n" .
            "- Ordered and unordered lists\n" .
            "- [Links to websites](https://laravel.com)\n\n" .
            "### 2. Code Block Syntax Highlighting\n" .
            "Here is some PHP code showcasing standard highlights:\n" .
            "```php\n" .
            "<?php\n\n" .
            "namespace App\\Services;\n\n" .
            "class ChatStreamer\n" .
            "{\n" .
            "    public function handleStream(\$response)\n" .
            "    { \n" .
            "        foreach (\$response->getChunks() as \$chunk) {\n" .
            "            echo \"data: \" . json_encode(['text' => \$chunk]) . \"\\n\\n\";\n" .
            "            ob_flush();\n" .
            "            flush();\n" .
            "        }\n" .
            "    }\n" .
            "}\n" .
            "```\n\n" .
            "To test with live models, go to **Settings** and add your **API Keys** for OpenAI, Gemini, or Claude!";

        // Break response into tiny chunks of 3-5 characters to simulate realistic network streaming
        $len = strlen($responseText);
        $chunkSize = 4;
        for ($i = 0; $i < $len; $i += $chunkSize) {
            $chunk = substr($responseText, $i, $chunkSize);
            $onChunk($chunk);
            usleep(20000); // 20ms pause per chunk (fast and responsive)
        }
    }

    public function getAvailableModels(): array
    {
        return [
            [
                'id' => 'mock-default',
                'name' => 'Mock local-model (Simulation)',
                'context' => 4096,
            ]
        ];
    }

    protected function getHeaders(): array
    {
        return [];
    }
}
