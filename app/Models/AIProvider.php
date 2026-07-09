<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIProvider extends Model
{
    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'api_key',
        'is_active',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function getApiKeyAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setApiKeyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['api_key'] = $value;
            return;
        }
        try {
            \Illuminate\Support\Facades\Crypt::decryptString($value);
            $this->attributes['api_key'] = $value;
        } catch (\Exception $e) {
            $this->attributes['api_key'] = \Illuminate\Support\Facades\Crypt::encryptString($value);
        }
    }

    /**
     * Get models belonging to this provider.
     */
    public function models(): HasMany
    {
        return $this->hasMany(AIModel::class, 'provider_id');
    }
}
