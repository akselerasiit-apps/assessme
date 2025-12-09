<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentGamoTargetLevel extends Model
{
    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'current_maturity_level',
        'target_maturity_level',
        'priority',
        'estimated_effort',
        'target_achievement_date',
        'gap_analysis',
        'recommended_actions',
        'notes',
    ];

    protected $casts = [
        'current_maturity_level' => 'decimal:2',
        'target_maturity_level' => 'decimal:2',
        'target_achievement_date' => 'date',
    ];

    /**
     * Get the assessment this target level belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective this target level is for
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Calculate maturity gap
     */
    public function getMaturityGap(): float
    {
        return round($this->target_maturity_level - $this->current_maturity_level, 2);
    }

    /**
     * Scope by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope high priority items
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['HIGH', 'CRITICAL']);
    }
}
