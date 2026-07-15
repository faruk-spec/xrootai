<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [
        'id',
        'two_factor_secret',
        'permissions',
    ];

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
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'plan_id',
        'last_admin_activity_at',
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

    protected static function booted()
    {
        static::saved(function ($user) {
            if ($user->role && ($user->isDirty('role') || !$user->roles()->exists())) {
                $roleRecord = Role::where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), strtolower($user->role))->first();
                if (!$roleRecord && strtolower($user->role) === 'user') {
                    $roleRecord = Role::firstOrCreate(['name' => 'user'], [
                        'description' => 'Standard user account with general application access and chat capabilities.'
                    ]);
                }
                if ($roleRecord) {
                    $user->roles()->syncWithoutDetaching([$roleRecord->id]);
                }
            }
        });
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

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function hasRole(string $role): bool
    {
        if (strcasecmp($this->role, $role) === 0) {
            return true;
        }
        return $this->roles()->where(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), strtolower($role))->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function getHierarchyWeight(): int
    {
        if ($this->isSuperAdmin()) {
            return Role::WEIGHT_SUPER_ADMIN;
        }
        if ($this->hasRole('Admin') || strcasecmp($this->role, 'admin') === 0) {
            return Role::WEIGHT_ADMIN;
        }
        if ($this->hasRole('Manager') || strcasecmp($this->role, 'manager') === 0) {
            return Role::WEIGHT_MANAGER;
        }
        if ($this->hasRole('Developer') || strcasecmp($this->role, 'developer') === 0) {
            return Role::WEIGHT_DEVELOPER;
        }
        if ($this->hasRole('Support Agent') || strcasecmp($this->role, 'support agent') === 0) {
            return Role::WEIGHT_SUPPORT;
        }
        return Role::WEIGHT_USER;
    }

    public function canModifyUser(User $targetUser): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if ($targetUser->isSuperAdmin()) {
            return false;
        }
        return $this->getHierarchyWeight() > $targetUser->getHierarchyWeight();
    }

    public function canAssignRole(string $roleName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        if (strcasecmp($roleName, 'Super Admin') === 0) {
            return false;
        }
        $targetWeight = Role::getWeightByName($roleName);
        return $this->getHierarchyWeight() >= $targetWeight;
    }

    public function hasPermission(string $permission): bool
    {
        // Super Admin and Admin roles get unrestricted administrative access
        if (in_array($this->role, ['admin', 'Super Admin', 'Admin']) || $this->hasRole('admin') || $this->hasRole('Super Admin') || $this->hasRole('Admin')) {
            return true;
        }

        // Check if user has explicit permission assigned via their roles
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->exists();
    }
}
