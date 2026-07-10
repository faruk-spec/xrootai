<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVerification extends Model
{
    protected $table = 'user_verifications';

    protected $fillable = [
        'user_id',
        'email',
        'type',
        'token',
        'otp',
        'expires_at',
        'attempts',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'attempts' => 'integer',
    ];

    /**
     * Relationship to the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the OTP or token is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if max attempts reached.
     */
    public function hasExceededMaxAttempts(int $maxAttempts = 5): bool
    {
        return $this->attempts >= $maxAttempts;
    }

    /**
     * Scope to find active (unexpired, unverified) verification requests.
     */
    public function scopeActive($query)
    {
        return $query->where('is_verified', false)
            ->where('expires_at', '>', now());
    }
}
