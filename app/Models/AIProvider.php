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
        'api_key' => 'encrypted',
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get models belonging to this provider.
     */
    public function models(): HasMany
    {
        return $this->hasMany(AIModel::class, 'provider_id');
    }
}
