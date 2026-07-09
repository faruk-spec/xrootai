<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIRoutingRule extends Model
{
    protected $table = 'ai_routing_rules';

    protected $fillable = [
        'name',
        'trigger_type',
        'pattern',
        'target_model_id',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Get the target AI Model for this routing rule.
     */
    public function targetModel(): BelongsTo
    {
        return $this->belongsTo(AIModel::class, 'target_model_id');
    }
}
