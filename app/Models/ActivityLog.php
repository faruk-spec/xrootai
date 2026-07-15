<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user associated with this activity log entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mask sensitive fields from data array before logging.
     */
    public static function maskSensitiveData(?array $data): ?array
    {
        if (empty($data)) {
            return $data;
        }

        $sensitiveKeys = [
            'password', 'password_confirmation', 'api_key', 'client_secret',
            'two_factor_secret', 'two_factor_recovery_codes', 'token', 'secret',
            'encrypted_key', 'authorization'
        ];

        foreach ($data as $key => $value) {
            foreach ($sensitiveKeys as $sensitiveKey) {
                if (stripos($key, $sensitiveKey) !== false) {
                    $data[$key] = '******** (MASKED BY AUDIT LOG)';
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Helper to write a log entry.
     */
    public static function log(string $action, string $description, ?int $userId = null, ?array $oldValues = null, ?array $newValues = null): self
    {
        return self::create([
            'user_id' => $userId ?: (auth()->check() ? auth()->id() : null),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => self::maskSensitiveData($oldValues),
            'new_values' => self::maskSensitiveData($newValues),
        ]);
    }
}
