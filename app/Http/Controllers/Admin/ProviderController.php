<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIProvider;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = AIProvider::withCount('models')->get();
        return view('admin.providers.index', compact('providers'));
    }

    public function edit(AIProvider $provider)
    {
        return view('admin.providers.edit', compact('provider'));
    }

    public function update(Request $request, AIProvider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'nullable|string|max:255',
            'api_key' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'config' => 'nullable|array',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Only update API key if value was provided
        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }

        $provider->update($validated);

        ActivityLog::log('update_provider', "Updated AI Provider: {$provider->name}");

        return redirect()->route('admin.providers.index')->with('success', "Provider {$provider->name} updated successfully.");
    }

    /**
     * Test the provider credentials using a lightweight connection check.
     */
    public function testConnection(Request $request, AIProvider $provider)
    {
        $apiKey = $request->input('api_key') ?: $provider->api_key;
        $baseUrl = $request->input('base_url') ?: $provider->base_url;

        if ($provider->slug === 'mock') {
            return response()->json(['success' => true, 'message' => 'Connection to mock provider successful.']);
        }

        if (empty($apiKey) && $provider->slug !== 'ollama' && $provider->slug !== 'lmstudio') {
            return response()->json(['success' => false, 'message' => 'API Key is missing.']);
        }

        try {
            if ($provider->slug === 'openai' || $provider->slug === 'deepseek' || $provider->slug === 'groq' || $provider->slug === 'openrouter' || $provider->slug === 'together' || $provider->slug === 'lmstudio') {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                ])->timeout(8)->get("{$baseUrl}/models");

                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Credentials verified. List of models fetched successfully.']);
                }
                return response()->json(['success' => false, 'message' => "API check failed: HTTP {$response->status()} - {$response->body()}"]);
            }

            if ($provider->slug === 'claude') {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                ])->timeout(8)->post("{$baseUrl}/messages", [
                    'model' => 'claude-3-5-sonnet-20241022',
                    'max_tokens' => 1,
                    'messages' => [['role' => 'user', 'content' => 'ping']],
                ]);

                if ($response->successful() || $response->status() === 400) {
                    // Anthropic returns bad request if we request 1 token or send empty body, but successful auth counts
                    return response()->json(['success' => true, 'message' => 'Anthropic Claude credentials verified.']);
                }
                return response()->json(['success' => false, 'message' => "Anthropic check failed: HTTP {$response->status()}"]);
            }

            if ($provider->slug === 'gemini') {
                $response = Http::timeout(8)->get("{$baseUrl}/models?key={$apiKey}");
                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Gemini Credentials verified successfully.']);
                }
                return response()->json(['success' => false, 'message' => "Gemini check failed: HTTP {$response->status()}"]);
            }

            if ($provider->slug === 'ollama') {
                $response = Http::timeout(5)->get("{$baseUrl}/tags");
                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Ollama local connection verified successfully.']);
                }
                return response()->json(['success' => false, 'message' => 'Ollama offline or not running.']);
            }

            return response()->json(['success' => false, 'message' => 'Dynamic testing not supported for this provider yet. Settings saved.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }
}
