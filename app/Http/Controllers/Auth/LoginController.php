<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            return redirect()->route('chat');
        }
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();
        $maxAttempts = (int) SystemSetting::get('auth_login_max_attempts', 5);
        $lockoutMinutes = (int) SystemSetting::get('auth_login_lockout_minutes', 15);

        if ($user && $user->login_attempts >= $maxAttempts) {
            if ($user->updated_at && $user->updated_at->addMinutes($lockoutMinutes)->isFuture()) {
                $remaining = abs($user->updated_at->addMinutes($lockoutMinutes)->diffInMinutes(now())) + 1;
                throw ValidationException::withMessages([
                    'email' => "Account temporarily locked due to too many failed login attempts. Please try again in {$remaining} minutes.",
                ]);
            } else {
                $user->update(['login_attempts' => 0]);
            }
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            if ($user) {
                $user->increment('login_attempts');
                if (SystemSetting::get('auth_track_login_history', true)) {
                    ActivityLog::log('login_failed', "Failed login attempt (#{$user->login_attempts}) for {$user->email} from IP {$request->ip()}", $user->id);
                }
            }
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if user account is approved by admin / not suspended
        if ($user && !$user->isApproved()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => $user->status === 'suspended'
                    ? 'Your account has been suspended by an administrator.'
                    : 'Your account registration is currently pending administrator approval.',
            ]);
        }

        // Two-Factor Authentication (2FA) Check
        if ($user && SystemSetting::get('auth_2fa_enabled', false)) {
            $enforceRolesRaw = SystemSetting::get('auth_2fa_enforce_roles', '');
            $enforcedRoles = !empty(trim($enforceRolesRaw)) ? array_map('trim', explode(',', strtolower($enforceRolesRaw))) : [];
            $roleEnforced = in_array(strtolower($user->role), $enforcedRoles);

            if ($user->hasTwoFactorEnabled() || $roleEnforced) {
                if ($roleEnforced && !$user->hasTwoFactorEnabled()) {
                    $user->update(['two_factor_enabled' => true, 'two_factor_type' => 'email']);
                }

                $trustedCookie = $request->cookie('2fa_trusted_' . $user->id);
                $expectedHash = hash_hmac('sha256', $user->id . '|' . $user->password, config('app.key'));

                if (!$trustedCookie || !hash_equals($expectedHash, (string) $trustedCookie)) {
                    Auth::logout();
                    session(['2fa_user_id' => $user->id, '2fa_remember' => $request->boolean('remember')]);

                    if ($user->two_factor_type === 'email') {
                        app(\App\Services\TwoFactorService::class)->createEmailChallenge($user);
                    }

                    return redirect()->route('two-factor.challenge');
                }
            }
        }

        // Record last login info & activity log
        if ($user) {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'login_attempts' => 0,
            ]);
            if (SystemSetting::get('auth_track_login_history', true)) {
                ActivityLog::log('user_login', "User logged in: {$user->name} ({$user->email}) from IP {$request->ip()}", $user->id);
            }
            if (SystemSetting::get('auth_single_session_per_user', false)) {
                try {
                    Auth::logoutOtherDevices($request->password);
                } catch (\Exception $e) {
                    // Ignore if session driver does not support logoutOtherDevices
                }
            }
        }

        $request->session()->regenerate();

        // Check if email verification is required before allowing dashboard access
        if ($user && !$user->isVerified() && SystemSetting::get('auth_require_email_verification', true) && !SystemSetting::get('auth_allow_login_unverified', false)) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended(route('chat'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
