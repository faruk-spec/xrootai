<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetCode extends Model
{
    protected $fillable = [
        'email',
        'token',
        'otp',
        'ip_address',
        'user_agent',
        'attempts',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user associated with this password reset code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Check if the reset code/token is still valid and has not expired or been used.
     */
    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    /**
     * Check if the reset code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the reset code has already been consumed.
     */
    public function isUsed(): bool
    {
        return !is_null($this->used_at);
    }
}
