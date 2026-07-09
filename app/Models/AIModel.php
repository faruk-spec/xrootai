<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIModel extends Model
{
    protected $table = 'ai_models';

    protected $fillable = [
        'provider_id',
        'name',
        'model_identifier',
        'type',
        'context_window',
        'max_tokens',
        'cost_per_million_input',
        'cost_per_million_output',
        'capabilities',
        'allowed_roles',
        'is_active',
    ];

    protected $casts = [
        'capabilities' => 'array',
        'allowed_roles' => 'array',
        'is_active' => 'boolean',
        'cost_per_million_input' => 'float',
        'cost_per_million_output' => 'float',
    ];

    /**
     * Get the provider that owns this model.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(AIProvider::class, 'provider_id');
    }
}
