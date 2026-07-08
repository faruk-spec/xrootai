<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\UserSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
            'default_model' => ['required', 'string'],
            'system_prompt' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme' => $request->theme,
                'default_model' => $request->default_model,
                'system_prompt' => $request->system_prompt,
            ]
        );

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function updateKeys(Request $request)
    {
        $request->validate([
            'keys' => ['required', 'array'],
        ]);

        $user = $request->user();

        foreach ($request->keys as $provider => $key) {
            if (empty($key)) {
                // Delete key if user cleared it
                ApiKey::where('user_id', $user->id)
                    ->where('provider', $provider)
                    ->delete();
            } else {
                // Update or create encrypted key
                ApiKey::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'provider' => $provider,
                    ],
                    [
                        'encrypted_key' => $key,
                        'is_active' => true,
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'API Keys saved successfully.');
    }
}
