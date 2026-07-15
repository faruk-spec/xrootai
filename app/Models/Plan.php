<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'daily_limit',
        'monthly_token_limit',
        'max_file_size',
        'allowed_models',
        'price',
        'concurrent_streams',
    ];

    protected $casts = [
        'allowed_models' => 'array',
        'daily_limit' => 'integer',
        'monthly_token_limit' => 'integer',
        'max_file_size' => 'integer',
        'price' => 'decimal:2',
        'concurrent_streams' => 'integer',
    ];

    /**
     * Get users assigned to this plan.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'plan_id');
    }

    /**
     * Helper to check if a specific model identifier is allowed by this plan.
     */
    public function allowsModel(string $modelIdentifier): bool
    {
        if (empty($this->allowed_models)) {
            return true;
        }

        if (in_array('*', $this->allowed_models) || in_array('all', $this->allowed_models)) {
            return true;
        }

        return in_array(strtolower($modelIdentifier), array_map('strtolower', $this->allowed_models));
    }
}
