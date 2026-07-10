<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserVerification;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

class EmailVerificationService
{
    /**
     * Generate and dispatch a verification OTP + Link to the user.
     */
    public static function generateAndSend(User $user, string $type = 'email_verification', ?string $targetEmail = null): array
    {
        $email = $targetEmail ?: ($user->pending_email ?: $user->email);

        // Expire any existing unverified codes for this email and type
        UserVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->update(['expires_at' => now()]);

        // Determine OTP length and expiry from settings
        $otpLength = (int) SystemSetting::get('auth_verification_otp_length', 6);
        $otpExpiryMinutes = (int) SystemSetting::get('auth_verification_otp_expiry', 15);
        
        // Generate numeric OTP with padding
        $maxVal = (10 ** $otpLength) - 1;
        $otp = str_pad((string) random_int(0, $maxVal), $otpLength, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        $verification = UserVerification::create([
            'user_id' => $user->id,
            'email' => $email,
            'type' => $type,
            'token' => $token,
            'otp' => $otp,
            'expires_at' => now()->addMinutes($otpExpiryMinutes),
            'attempts' => 0,
            'is_verified' => false,
        ]);

        // Configure dynamic mailer and dispatch mail
        DynamicMailConfigService::configure();

        try {
            Mail::to($email)->send(new \App\Mail\VerificationEmailMail($user, $verification));
            ActivityLog::log('send_verification_email', "Sent verification OTP/Link to {$email}", $user->id);
            return ['status' => true, 'message' => "Verification code sent to {$email}.", 'verification' => $verification];
        } catch (Exception $e) {
            ActivityLog::log('send_verification_failed', "Failed sending verification mail to {$email}: " . $e->getMessage(), $user->id);
            return ['status' => false, 'message' => "Failed to send verification email: " . $e->getMessage()];
        }
    }

    /**
     * Verify submitted OTP code for a user.
     */
    public static function verifyOtp(User $user, string $otpString, string $type = 'email_verification'): array
    {
        $maxAttempts = (int) SystemSetting::get('auth_verification_max_attempts', 5);

        $verification = UserVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_verified', false)
            ->latest()
            ->first();

        if (!$verification) {
            return ['status' => false, 'message' => 'No pending verification request found. Please request a new code.'];
        }

        if ($verification->isExpired()) {
            return ['status' => false, 'message' => 'Your verification code has expired. Please request a new one.'];
        }

        if ($verification->hasExceededMaxAttempts($maxAttempts)) {
            return ['status' => false, 'message' => 'Maximum verification attempts exceeded. Please request a new verification code.'];
        }

        if ($verification->otp !== trim($otpString)) {
            $verification->increment('attempts');
            $remaining = max(0, $maxAttempts - $verification->attempts);
            return ['status' => false, 'message' => "Invalid verification code. You have {$remaining} attempt(s) remaining."];
        }

        return self::completeVerification($user, $verification);
    }

    /**
     * Verify via secure link token.
     */
    public static function verifyToken(string $tokenString): array
    {
        $verification = UserVerification::where('token', $tokenString)
            ->where('is_verified', false)
            ->first();

        if (!$verification) {
            return ['status' => false, 'message' => 'Invalid or already processed verification link.'];
        }

        if ($verification->isExpired()) {
            return ['status' => false, 'message' => 'This verification link has expired. Please request a new link.'];
        }

        $user = $verification->user;
        if (!$user) {
            return ['status' => false, 'message' => 'Associated user account no longer exists.'];
        }

        return self::completeVerification($user, $verification);
    }

    /**
     * Mark the verification as successful and update user status.
     */
    protected static function completeVerification(User $user, UserVerification $verification): array
    {
        $verification->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $updateData = [
            'email_verified_at' => now(),
            'otp_verified_at' => now(),
        ];

        // If user had a pending email change, promote it to primary email
        if (!empty($user->pending_email) && $verification->email === $user->pending_email) {
            $updateData['email'] = $user->pending_email;
            $updateData['pending_email'] = null;
        }

        $user->update($updateData);

        ActivityLog::log('email_verified', "User {$user->name} verified their email ({$user->email})", $user->id);

        return ['status' => true, 'message' => 'Your email address has been successfully verified!'];
    }

    /**
     * Admin manual override: directly mark a user as verified.
     */
    public static function verifyManually(User $user, int $adminId): array
    {
        $user->update([
            'email_verified_at' => now(),
            'otp_verified_at' => now(),
            'pending_email' => null,
        ]);

        // Expire any pending verification rows
        UserVerification::where('user_id', $user->id)
            ->where('is_verified', false)
            ->update(['is_verified' => true, 'verified_at' => now()]);

        ActivityLog::log('verify_user_manually', "Admin manually verified email for user {$user->name} ({$user->email})", $adminId);

        return ['status' => true, 'message' => "User {$user->name} has been manually verified."];
    }
}
