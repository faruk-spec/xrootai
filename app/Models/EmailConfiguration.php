<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class EmailConfiguration extends Model
{
    protected $table = 'email_configurations';

    protected $fillable = [
        'provider_name',
        'provider_slug',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_name',
        'from_email',
        'reply_to',
        'is_active',
        'is_default',
        'connection_status',
        'last_error',
        'last_tested_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'port' => 'integer',
        'last_tested_at' => 'datetime',
    ];

    /**
     * Mutator to securely encrypt password before saving.
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Crypt::encryptString($value);
        } else {
            $this->attributes['password'] = null;
        }
    }

    /**
     * Accessor to decrypt password securely when accessed.
     */
    public function getPasswordAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Fallback if password was stored as plaintext previously
            return $value;
        }
    }

    /**
     * Scope for active configurations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default configuration.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the currently active default email configuration from cache/db.
     */
    public static function getActiveDefault()
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('email_configurations')) {
                return null;
            }

            // Fetch directly or check cached ID to prevent __PHP_Incomplete_Class serialization bugs
            $configId = Cache::rememberForever('active_default_email_config_id', function () {
                $item = self::where('is_active', true)
                    ->where('is_default', true)
                    ->first() ?: self::where('is_active', true)->first();
                return $item ? $item->id : null;
            });

            if (!$configId) {
                return self::where('is_active', true)
                    ->where('is_default', true)
                    ->first() ?: self::where('is_active', true)->first();
            }

            $config = self::find($configId);
            if (!$config || !$config->is_active) {
                Cache::forget('active_default_email_config_id');
                Cache::forget('active_default_email_config');
                return self::where('is_active', true)
                    ->where('is_default', true)
                    ->first() ?: self::where('is_active', true)->first();
            }

            return $config;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Alias for getActiveDefault().
     */
    public static function getActive()
    {
        return self::getActiveDefault();
    }

    /**
     * Apply this mail configuration dynamically to runtime.
     */
    public function applyToRuntime()
    {
        return \App\Services\DynamicMailConfigService::configure($this);
    }

    /**
     * Set this configuration as the sole default and clear cache.
     */
    public function makeDefault()
    {
        self::where('id', '!=', $this->id)->update(['is_default' => false]);
        $this->update(['is_default' => true, 'is_active' => true]);
        Cache::forget('active_default_email_config');
    }

    /**
     * Clear active default cache on save or delete.
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('active_default_email_config');
        });

        static::deleted(function () {
            Cache::forget('active_default_email_config');
        });
    }
}
