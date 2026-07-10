<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class TwoFactorController extends Controller
{
    protected TwoFactorService $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show the 2FA verification challenge screen.
     */
    public function showChallengeForm(Request $request)
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        $user = User::find(session('2fa_user_id'));
        if (!$user) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        if ($user->two_factor_type === 'email') {
            $hasActiveChallenge = \App\Models\TwoFactorChallenge::where('user_id', $user->id)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->exists();
            if (!$hasActiveChallenge) {
                $this->twoFactorService->createEmailChallenge($user);
            }
        }

        return view('auth.two-factor-challenge', compact('user'));
    }

    /**
     * Verify the submitted 2FA code or recovery code.
     */
    public function verifyChallenge(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:12'],
        ]);

        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $user = User::find(session('2fa_user_id'));
        if (!$user) {
            session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login')->with('error', 'Account not found. Please log in again.');
        }

        $code = trim($request->code);
        $isValid = $this->twoFactorService->validateChallenge($user, $code);

        if (!$isValid) {
            ActivityLog::log('2fa_failed', "Failed 2FA attempt for {$user->email} from IP {$request->ip()}", $user->id);
            return back()->withErrors(['code' => 'The verification code or recovery code you entered is invalid or expired.'])->withInput();
        }

        // 2FA Verified! Perform actual login
        $remember = session('2fa_remember', false);
        Auth::login($user, $remember);

        // Record last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_attempts' => 0,
        ]);

        if (SystemSetting::get('auth_track_login_history', true)) {
            ActivityLog::log('user_login_2fa', "User logged in via 2FA ({$user->two_factor_type}): {$user->name} ({$user->email})", $user->id);
        }

        if (SystemSetting::get('auth_single_session_per_user', false)) {
            try {
                Auth::logoutOtherDevices($request->password ?? '');
            } catch (\Exception $e) {
                // Ignore
            }
        }

        // If 'Trust this device' checked, set long-lived signed cookie
        if ($request->boolean('remember_device')) {
            $days = (int) SystemSetting::get('auth_2fa_remember_days', 30);
            $token = hash_hmac('sha256', $user->id . '|' . $user->password, config('app.key'));
            Cookie::queue('2fa_trusted_' . $user->id, $token, $days * 1440);
        }

        session()->forget(['2fa_user_id', '2fa_remember']);

        return redirect()->intended(route('chat'));
    }

    /**
     * Resend an Email OTP code.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $user = User::find(session('2fa_user_id'));
        if (!$user || $user->two_factor_type !== 'email') {
            return back()->with('error', 'Email OTP verification is not active on this account.');
        }

        $this->twoFactorService->createEmailChallenge($user);

        return back()->with('status', 'A fresh verification code has been sent to your registered email address.');
    }

    /**
     * Cancel pending 2FA challenge and return to login.
     */
    public function cancelChallenge(Request $request)
    {
        session()->forget(['2fa_user_id', '2fa_remember']);
        return redirect()->route('login');
    }
}
