<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorChallenge;
use App\Mail\TwoFactorOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TwoFactorService
{
    /**
     * Generate a secure random Base32 secret string for TOTP.
     */
    public function generateSecretKey(int $length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Build the standard otpauth:// URL for authenticator apps.
     */
    public function getOtpAuthUrl(User $user, string $secret): string
    {
        $appName = \App\Models\SystemSetting::get('general_chatbot_name');
        if (empty($appName) || stripos($appName, 'Laravel') !== false) {
            $appName = config('app.name');
            if (empty($appName) || stripos($appName, 'Laravel') !== false) {
                $appName = request()->getHost() ?: 'XrootAI';
                if ($appName === 'localhost' || $appName === '127.0.0.1') {
                    $appName = 'XrootAI';
                }
            }
        }
        $appNameClean = trim(preg_replace('/[^a-zA-Z0-9_\- ]/', '', $appName));
        if (empty($appNameClean)) {
            $appNameClean = 'XrootAI';
        }
        $email = $user->email;

        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            rawurlencode($appNameClean),
            rawurlencode($email),
            $secret,
            rawurlencode($appNameClean)
        );
    }

    /**
     * Build a reliable external QR code image URL for embedding in setup UIs.
     */
    public function getQrCodeUrl(string $otpauthUrl): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=10&data=' . rawurlencode($otpauthUrl);
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret using RFC 6238.
     */
    public function verifyTotpCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $secretBinary = $this->base32Decode(strtoupper($secret));
        if ($secretBinary === false) {
            return false;
        }

        $currentTimeSlice = floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->calculateTotp($secretBinary, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate emergency recovery codes and save them to the user model.
     */
    public function generateRecoveryCodes(User $user, int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = sprintf('%04d-%04d', random_int(1000, 9999), random_int(1000, 9999));
        }

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ]);

        return $codes;
    }

    /**
     * Create an Email OTP challenge and dispatch the mailable.
     */
    public function createEmailChallenge(User $user): TwoFactorChallenge
    {
        // Invalidate older pending challenges
        TwoFactorChallenge::where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $otp = (string) random_int(100000, 999999);

        $challenge = TwoFactorChallenge::create([
            'user_id' => $user->id,
            'code' => $otp,
            'type' => 'email',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(15),
        ]);

        try {
            \App\Services\DynamicMailConfigService::configure();
            Mail::to($user->email)->send(new TwoFactorOtpMail($otp, $user));
        } catch (\Exception $e) {
            Log::error("Failed sending 2FA OTP email to {$user->email}: " . $e->getMessage());
        }

        return $challenge;
    }

    /**
     * Validate an entered code against Email OTP, TOTP, or Recovery Code.
     */
    public function validateChallenge(User $user, string $code): bool
    {
        $code = trim($code);

        // 1. Check if valid Recovery Code first
        if (str_contains($code, '-') || strlen($code) === 9) {
            return $user->useRecoveryCode($code);
        }

        // 2. Check if user is using TOTP / Authenticator App
        if ($user->two_factor_type === 'totp' && !empty($user->two_factor_secret)) {
            try {
                $secret = decrypt($user->two_factor_secret);
                if ($this->verifyTotpCode($secret, $code)) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::error("Error decrypting 2FA secret for user {$user->id}: " . $e->getMessage());
            }
        }

        // 3. Check active Email/Challenge table OTPs
        $challenge = TwoFactorChallenge::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderBy('id', 'desc')
            ->first();

        if ($challenge) {
            $challenge->increment('attempts');

            if ($challenge->attempts > 5) {
                $challenge->update(['used_at' => now()]);
                return false;
            }

            if ($challenge->code !== null && hash_equals((string) $challenge->code, $code)) {
                $challenge->update(['used_at' => now()]);
                return true;
            }
        }

        // 4. Fallback check if user entered a recovery code without hyphen
        if (strlen($code) === 8 && ctype_digit($code)) {
            $formatted = substr($code, 0, 4) . '-' . substr($code, 4, 4);
            if ($user->useRecoveryCode($formatted)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Base32 decoding helper for RFC 6238 TOTP.
     */
    protected function base32Decode(string $base32): string|false
    {
        $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32CharsFlipped = array_flip(str_split($base32Chars));

        $paddingCharCount = substr_count($base32, '=');
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }

        for ($i = 0; $i < 4; $i++) {
            if ($paddingCharCount == $allowedValues[$i] &&
                substr($base32, -$allowedValues[$i]) != str_repeat('=', $allowedValues[$i])) {
                return false;
            }
        }

        $base32 = str_replace('=', '', $base32);
        $base32 = str_split($base32);
        $binaryString = '';

        for ($i = 0; $i < count($base32); $i = $i + 8) {
            $x = '';
            if (!in_array($base32[$i], str_split($base32Chars))) {
                return false;
            }
            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@$base32CharsFlipped[@$base32[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
            }
        }

        return $binaryString;
    }

    /**
     * Calculate HMAC-SHA1 TOTP code from secret and time slice.
     */
    protected function calculateTotp(string $secretBinary, int $timeSlice): string
    {
        $timePacked = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $timePacked, $secretBinary, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);

        $value = unpack('N', $hashPart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;

        $modulo = pow(10, 6);
        return str_pad((string) ($value % $modulo), 6, '0', STR_PAD_LEFT);
    }
}
