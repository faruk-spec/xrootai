<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\UserSetting;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;
class SettingsController extends Controller
{
    
    // Display settings page
    public function show(Request $request, TwoFactorService $twoFactorService, AIProviderManager $aiManager)
    {
        $user = $request->user();
        $settings = UserSetting::where('user_id', $user->id)->first() ?? new UserSetting([
            'theme' => 'system',
            'default_model' => 'mock',
            'system_prompt' => '',
        ]);

        $recoveryCodes = $user->getTwoFactorRecoveryCodesArray();
        $qrCodeUrl = null;
        $secretKey = null;

        if ($request->query('setup') === 'totp') {
            $secretKey = session('totp_pending_secret');
            if (! $secretKey) {
                $secretKey = $twoFactorService->generateSecretKey(16);
                session(['totp_pending_secret' => $secretKey]);
            }
            $otpauthUrl = $twoFactorService->getOtpAuthUrl($user, $secretKey);
            $qrCodeUrl = $twoFactorService->getQrCodeUrl($otpauthUrl);
        }

        // Load available AI models
        $models = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('ai_models')) {
            $dbModels = \App\Models\AIModel::with('provider')->where('is_active', true)->get();
            foreach ($dbModels as $model) {
                $models[] = [
                    'id' => $model->model_identifier,
                    'name' => $model->name,
                ];
            }
        }
        if (empty($models)) {
            $models = $aiManager->getAllModels();
        }

        $activeTab = $request->query('tab', session('active_tab', 'general'));

        $apiKeys = ApiKey::where('user_id', $user->id)->get()->keyBy('provider');
        $conversationsCount = $user->conversations()->count();
        $tokenUsage = 48291; // placeholder
        $storageUsed = '14.8 MB'; // placeholder

        return view('user.settings', compact(
            'user',
            'settings',
            'recoveryCodes',
            'qrCodeUrl',
            'secretKey',
            'models',
            'activeTab',
            'apiKeys',
            'conversationsCount',
            'tokenUsage',
            'storageUsed'
        ));
    }


    public function update(Request $request)
    {
        $request->validate([
            'theme' => ['nullable', 'string', 'in:light,dark,system,oled'],
            'default_model' => ['nullable', 'string'],
            'system_prompt' => ['nullable', 'string'],
            'preferences' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $setting = UserSetting::firstOrCreate(['user_id' => $user->id]);

        if ($request->has('theme') && !empty($request->theme)) {
            $setting->theme = $request->theme;
        }
        if ($request->has('default_model') && !empty($request->default_model)) {
            $setting->default_model = $request->default_model;
        }
        if ($request->has('system_prompt')) {
            $setting->system_prompt = $request->system_prompt;
        }
        if ($request->has('preferences') && is_array($request->preferences)) {
            $existingPrefs = is_array($setting->preferences) ? $setting->preferences : [];
            $setting->preferences = array_merge($existingPrefs, $request->preferences);
        }
        $setting->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully.',
                'settings' => $setting
            ]);
        }

        $redirectTab = $request->input('redirect_tab', $request->input('active_tab', 'general'));
        return redirect()->route('user.settings', ['tab' => $redirectTab])->with('success', 'Settings updated successfully.');
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'API Keys updated successfully.'
            ]);
        }

        return redirect()->route('user.settings', ['tab' => 'api-keys'])->with('success', 'API Keys saved successfully.');
    }
}
