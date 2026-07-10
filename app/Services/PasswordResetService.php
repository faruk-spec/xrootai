<?php

namespace App\Services;

use App\Models\User;
use App\Models\PasswordResetCode;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Models\EmailConfiguration;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordResetSuccessMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    /**
     * Request a new password reset code/token and send email to the user.
     */
    public static function requestReset(User $user, string $ipAddress = '', ?string $userAgent = null): array
    {
        // 1. Check Cooldown Window
        $cooldownSeconds = (int) SystemSetting::get('auth_password_reset_cooldown_seconds', 60);
        $lastRequest = PasswordResetCode::where('email', $user->email)
            ->latest('created_at')
            ->first();

        if ($lastRequest && $lastRequest->created_at->diffInSeconds(now()) < $cooldownSeconds) {
            $remaining = $cooldownSeconds - $lastRequest->created_at->diffInSeconds(now());
            return [
                'status' => false,
                'message' => "Please wait {$remaining} seconds before requesting another password reset.",
            ];
        }

        // 2. Check Hourly Rate Limit
        $maxHourly = (int) SystemSetting::get('auth_password_reset_max_requests_per_hour', 5);
        $hourlyCount = PasswordResetCode::where('email', $user->email)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($hourlyCount >= $maxHourly) {
            ActivityLog::log('password_reset_rate_limit', "Password reset rate limit exceeded for email: {$user->email} from IP: {$ipAddress}", $user->id);
            return [
                'status' => false,
                'message' => 'You have exceeded the maximum number of password reset requests per hour. Please try again later.',
            ];
        }

        // 3. Invalidate any existing unused reset codes for this user
        PasswordResetCode::where('email', $user->email)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        // 4. Generate secure token & OTP digits
        $token = bin2hex(random_bytes(32)); // 64 hex characters
        $otpLength = (int) SystemSetting::get('auth_password_reset_otp_length', 6);
        $min = (int) pow(10, $otpLength - 1);
        $max = (int) pow(10, $otpLength) - 1;
        $otp = (string) random_int($min, $max);

        $expiryMinutes = (int) SystemSetting::get('auth_password_reset_expiry_minutes', 30);

        // 5. Save the new reset code
        $resetCode = PasswordResetCode::create([
            'email' => $user->email,
            'token' => $token,
            'otp' => $otp,
            'ip_address' => substr($ipAddress, 0, 45),
            'user_agent' => substr((string) $userAgent, 0, 500),
            'attempts' => 0,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);

        // 6. Log activity
        ActivityLog::log('password_reset_requested', "Password reset requested for {$user->email} from IP: {$ipAddress}", $user->id);

        // 7. Dispatch Mail
        self::sendResetEmail($user, $resetCode);

        return [
            'status' => true,
            'message' => "We have emailed your password reset link and verification code to {$user->email}.",
            'resetCode' => $resetCode,
        ];
    }

    /**
     * Send the password reset mailable via configured email provider or queue.
     */
    protected static function sendResetEmail(User $user, PasswordResetCode $resetCode): void
    {
        try {
            \App\Services\DynamicMailConfigService::configure();
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetCode));
        } catch (\Exception $e) {
            Log::error("Failed to send PasswordResetMail to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Validate an incoming reset token or OTP code against an email.
     */
    public static function validateResetCode(string $email, string $codeOrToken): array
    {
        $resetCode = PasswordResetCode::where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->where(function ($query) use ($codeOrToken) {
                $query->where('token', $codeOrToken)
                      ->orWhere('otp', $codeOrToken);
            })
            ->latest('created_at')
            ->first();

        if (!$resetCode) {
            return [
                'status' => false,
                'message' => 'The password reset code or link is invalid or has expired. Please request a new one.',
                'resetCode' => null,
            ];
        }

        // Check attempt count
        $maxAttempts = (int) SystemSetting::get('auth_password_reset_max_attempts', 5);
        if ($resetCode->attempts >= $maxAttempts) {
            $resetCode->update(['used_at' => now()]); // Expire immediately after max attempts
            return [
                'status' => false,
                'message' => 'Too many incorrect attempts. This verification code has been deactivated for security.',
                'resetCode' => null,
            ];
        }

        $resetCode->increment('attempts');

        return [
            'status' => true,
            'message' => 'Verification code is valid.',
            'resetCode' => $resetCode,
        ];
    }

    /**
     * Execute the actual password reset.
     */
    public static function resetPassword(PasswordResetCode $resetCode, string $newPassword, string $ipAddress = ''): array
    {
        $user = $resetCode->user;
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User account associated with this email no longer exists.',
            ];
        }

        // Update password
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Mark reset code used
        $resetCode->update([
            'used_at' => now(),
        ]);

        // Log activity
        ActivityLog::log('password_reset_completed', "User {$user->email} successfully reset their password from IP: {$ipAddress}", $user->id);

        // Send security notification email
        try {
            \App\Services\DynamicMailConfigService::configure();
            Mail::to($user->email)->send(new PasswordResetSuccessMail($user, $ipAddress));
        } catch (\Exception $e) {
            Log::error("Failed to send PasswordResetSuccessMail to {$user->email}: " . $e->getMessage());
        }

        return [
            'status' => true,
            'message' => 'Your password has been reset successfully. You can now log in with your new credentials.',
            'user' => $user,
        ];
    }

    /**
     * Build validation rules for new passwords based on SystemSetting parameters.
     */
    public static function getPasswordRules(): array
    {
        $minLength = (int) SystemSetting::get('auth_password_reset_min_length', 8);
        $requireUppercase = SystemSetting::get('auth_password_reset_require_uppercase', true);
        $requireNumbers = SystemSetting::get('auth_password_reset_require_numbers', true);
        $requireSymbols = SystemSetting::get('auth_password_reset_require_symbols', true);

        $rules = ['required', 'string', 'min:' . $minLength, 'confirmed'];

        if ($requireUppercase || $requireNumbers || $requireSymbols) {
            $rules[] = function ($attribute, $value, $fail) use ($requireUppercase, $requireNumbers, $requireSymbols) {
                if ($requireUppercase && !preg_match('/[A-Z]/', $value)) {
                    $fail('The password must contain at least one uppercase letter.');
                }
                if ($requireNumbers && !preg_match('/[0-9]/', $value)) {
                    $fail('The password must contain at least one number.');
                }
                if ($requireSymbols && !preg_match('/[^A-Za-z0-9]/', $value)) {
                    $fail('The password must contain at least one special character/symbol.');
                }
            };
        }

        return $rules;
    }
}
