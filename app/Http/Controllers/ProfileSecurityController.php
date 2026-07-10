<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileSecurityController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Display the User Profile Security & 2FA management screen.
     */
    public function index(Request $request)
    {
        return redirect()->route('user.settings', ['tab' => 'security', 'setup' => $request->query('setup')]);
    }

    /**
     * Confirm and activate Authenticator App (TOTP) setup after code check.
     */
    public function confirmTotp(Request $request)
    {
        $request->validate([
            'totp_code' => ['required', 'string', 'max:6'],
        ]);

        $user = Auth::user();
        $pendingSecret = session('totp_pending_secret');

        if (!$pendingSecret) {
            return redirect()->route('user.settings', ['tab' => 'security', 'setup' => 'totp'])
                ->with('error', 'Setup session expired. Please scan the new QR code and try again.');
        }

        if (!$this->twoFactorService->verifyTotpCode($pendingSecret, trim($request->totp_code))) {
            return back()->withErrors(['totp_code' => 'The verification code is incorrect. Make sure your device time is synchronized.']);
        }

        // Activate TOTP 2FA
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_type' => 'totp',
            'two_factor_secret' => encrypt($pendingSecret),
            'two_factor_confirmed_at' => now(),
        ]);

        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes($user, 8);
        session()->forget('totp_pending_secret');

        ActivityLog::log('2fa_enabled_totp', "User enabled Authenticator App 2FA: {$user->email}", $user->id);

        return redirect()->route('user.settings', ['tab' => 'security'])
            ->with('success', 'Authenticator App two-factor authentication enabled successfully! Please save your emergency recovery codes below.');
    }

    /**
     * Update 2FA settings (Enable Email OTP, Disable 2FA, Regenerate codes).
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $action = $request->input('action');

        // Enabling Email OTP 2FA
        if ($action === 'enable_email') {
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_type' => 'email',
                'two_factor_confirmed_at' => now(),
            ]);
            $this->twoFactorService->generateRecoveryCodes($user, 8);
            ActivityLog::log('2fa_enabled_email', "User enabled Email OTP 2FA: {$user->email}", $user->id);

            return redirect()->route('user.settings', ['tab' => 'security'])
                ->with('success', 'Email OTP Two-Factor Authentication has been activated.');
        }

        // Regenerating Recovery Codes
        if ($action === 'regenerate_codes') {
            $this->twoFactorService->generateRecoveryCodes($user, 8);
            ActivityLog::log('2fa_recovery_regenerated', "User regenerated 2FA recovery codes: {$user->email}", $user->id);

            return redirect()->route('user.settings', ['tab' => 'security'])
                ->with('success', 'Emergency recovery codes have been regenerated. Any previous codes are now invalid.');
        }

        // Disabling 2FA
        if ($action === 'disable_2fa') {
            if ($user->password && $request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => 'Incorrect password provided. Two-Factor Authentication could not be disabled.',
                    ]);
                }
            }

            $user->update([
                'two_factor_enabled' => false,
                'two_factor_type' => null,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ]);

            ActivityLog::log('2fa_disabled', "User disabled Two-Factor Authentication: {$user->email}", $user->id);

            return redirect()->route('user.settings', ['tab' => 'security'])
                ->with('success', 'Two-Factor Authentication has been turned off.');
        }

        return redirect()->route('user.settings', ['tab' => 'security']);
    }
}
