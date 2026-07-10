<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'provider', 'provider_id',
    'is_approved', 'approved_at', 'approved_by', 'otp_verified_at',
    'pending_email', 'status', 'last_login_at', 'last_login_ip',
    'login_attempts', 'locked_until', 'two_factor_enabled', 'two_factor_type',
    'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'provider',
        'provider_id',
        'is_approved',
        'approved_at',
        'approved_by',
        'otp_verified_at',
        'pending_email',
        'status',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'two_factor_enabled',
        'two_factor_type',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'is_approved' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function verifications()
    {
        return $this->hasMany(UserVerification::class);
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at) || !is_null($this->otp_verified_at);
    }

    public function twoFactorChallenges()
    {
        return $this->hasMany(TwoFactorChallenge::class);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled === true && !empty($this->two_factor_type);
    }

    public function getTwoFactorRecoveryCodesArray(): array
    {
        if (empty($this->two_factor_recovery_codes)) {
            return [];
        }
        try {
            $decoded = json_decode(decrypt($this->two_factor_recovery_codes), true);
            return is_array($decoded) ? $decoded : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getTwoFactorRecoveryCodesArray();
        $code = trim(strtoupper($code));

        if (($key = array_search($code, $codes)) !== false) {
            unset($codes[$key]);
            $this->update([
                'two_factor_recovery_codes' => encrypt(json_encode(array_values($codes))),
            ]);
            return true;
        }

        return false;
    }

    public function isApproved(): bool
    {
        return $this->is_approved === true && $this->status !== 'suspended';
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole(string $role): bool
    {
        if ($this->role === $role) {
            return true;
        }
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin' || $this->hasRole('admin') || $this->hasRole('Super Admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }
}
