<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OAuthProvider;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class OAuthProviderController extends Controller
{
    public function index()
    {
        $providers = OAuthProvider::all();
        return view('admin.oauth.index', compact('providers'));
    }

    public function update(Request $request, OAuthProvider $provider)
    {
        $validated = $request->validate([
            'client_id' => 'required|string|max:255',
            'client_secret' => 'nullable|string',
            'is_active' => 'sometimes',
            'scopes' => 'nullable|string',
            'additional_params' => 'nullable|string', // JSON validation done manually below
            
            // Custom provider fields
            'auth_url' => 'required_if:provider_slug,custom|nullable|url|max:255',
            'token_url' => 'required_if:provider_slug,custom|nullable|url|max:255',
            'user_info_url' => 'required_if:provider_slug,custom|nullable|url|max:255',
        ]);

        $updateData = [
            'client_id' => $validated['client_id'],
            'is_active' => $request->has('is_active'),
        ];

        // Parse comma-separated scopes into array
        if ($request->filled('scopes')) {
            $updateData['scopes'] = array_filter(array_map('trim', explode(',', $request->scopes)));
        } else {
            $updateData['scopes'] = [];
        }

        // Custom endpoints
        if ($provider->provider_slug === 'custom') {
            $updateData['auth_url'] = $validated['auth_url'];
            $updateData['token_url'] = $validated['token_url'];
            $updateData['user_info_url'] = $validated['user_info_url'];
        }

        // Handle Client Secret securely
        if ($request->filled('client_secret')) {
            $updateData['client_secret'] = $validated['client_secret'];
        }

        // Validate and decode additional parameters JSON
        if ($request->filled('additional_params')) {
            $decoded = json_decode($request->additional_params, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['additional_params' => 'The Additional Parameters field must be a valid JSON string.']);
            }
            $updateData['additional_params'] = $decoded;
        } else {
            $updateData['additional_params'] = [];
        }

        // Update redirect callback URL based on app URL dynamically if empty
        $updateData['redirect_url'] = url("/auth/{$provider->provider_slug}/callback");

        $provider->update($updateData);

        ActivityLog::log('update_oauth_provider', "Updated configurations for OAuth provider: {$provider->provider_name}");

        return redirect()->route('admin.oauth.index')
            ->with('success', "OAuth configurations for {$provider->provider_name} saved successfully.");
    }

    public function reset(OAuthProvider $provider)
    {
        $provider->update([
            'client_id' => null,
            'client_secret' => null,
            'is_active' => false,
            'auth_url' => null,
            'token_url' => null,
            'user_info_url' => null,
            'additional_params' => [],
        ]);

        ActivityLog::log('reset_oauth_provider', "Reset and disabled OAuth provider settings for: {$provider->provider_name}");

        return redirect()->route('admin.oauth.index')
            ->with('success', "OAuth provider {$provider->provider_name} has been reset and disabled.");
    }

    public function testConnection(OAuthProvider $provider)
    {
        if (empty($provider->client_id) || empty($provider->client_secret)) {
            return response()->json([
                'success' => false,
                'message' => "Connection test failed: Client ID and Client Secret must be configured.",
            ]);
        }

        if ($provider->provider_slug === 'custom') {
            if (empty($provider->auth_url) || empty($provider->token_url) || empty($provider->user_info_url)) {
                return response()->json([
                    'success' => false,
                    'message' => "Connection test failed: Custom OAuth URL endpoints are not fully configured.",
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "OAuth Connection configuration validated successfully for {$provider->provider_name}.",
        ]);
    }
}
