<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'name',
        'description',
    ];

    public const WEIGHT_SUPER_ADMIN = 100;
    public const WEIGHT_ADMIN = 80;
    public const WEIGHT_MANAGER = 60;
    public const WEIGHT_DEVELOPER = 40;
    public const WEIGHT_SUPPORT = 20;
    public const WEIGHT_USER = 10;

    /**
     * Get permissions associated with this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    /**
     * Get users assigned to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    public static function getWeightByName(string $roleName): int
    {
        switch (strtolower($roleName)) {
            case 'super admin':
                return self::WEIGHT_SUPER_ADMIN;
            case 'admin':
                return self::WEIGHT_ADMIN;
            case 'manager':
                return self::WEIGHT_MANAGER;
            case 'developer':
                return self::WEIGHT_DEVELOPER;
            case 'support agent':
                return self::WEIGHT_SUPPORT;
            default:
                return self::WEIGHT_USER;
        }
    }
}
