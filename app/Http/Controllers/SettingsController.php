<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\UserSetting;
use App\Services\AI\AIProviderManager;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request, \App\Services\TwoFactorService $twoFactorService, AIProviderManager $aiManager)
    {
        $user = $request->user();
        $settings = UserSetting::where('user_id', $user->id)->first() ?? new UserSetting([
            'theme' => 'system',
            'default_model' => 'mock',
            'system_prompt' => ''
        ]);

        $recoveryCodes = $user->getTwoFactorRecoveryCodesArray();
        $qrCodeUrl = null;
        $secretKey = null;

        if ($request->query('setup') === 'totp') {
            $secretKey = session('totp_pending_secret');
            if (!$secretKey) {
                $secretKey = $twoFactorService->generateSecretKey(16);
                session(['totp_pending_secret' => $secretKey]);
            }
            $otpauthUrl = $twoFactorService->getOtpAuthUrl($user, $secretKey);
            $qrCodeUrl = $twoFactorService->getQrCodeUrl($otpauthUrl);
        }

        // Get available models
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

        return view('user.settings', compact('user', 'settings', 'recoveryCodes', 'qrCodeUrl', 'secretKey', 'models', 'activeTab'));
    }

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

        return redirect()->route('user.settings', ['tab' => 'general'])->with('success', 'Settings updated successfully.');
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

        return redirect()->route('user.settings', ['tab' => 'keys'])->with('success', 'API Keys saved successfully.');
    }
}
