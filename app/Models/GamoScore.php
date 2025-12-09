<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamoScore extends Model
{
    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'current_maturity_level',
        'target_maturity_level',
        'capability_score',
        'capability_level',
        'percentage_complete',
        'status',
    ];

    protected $casts = [
        'current_maturity_level' => 'decimal:2',
        'target_maturity_level' => 'decimal:2',
        'capability_score' => 'decimal:2',
        'capability_level' => 'decimal:2',
        'percentage_complete' => 'integer',
    ];

    /**
     * Get the assessment this score belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective this score is for
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
     * Check if target is met
     */
    public function isTargetMet(): bool
    {
        return $this->current_maturity_level >= $this->target_maturity_level;
    }
}
