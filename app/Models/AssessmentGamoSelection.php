<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentGamoSelection extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'assessment_id',
        'gamo_objective_id',
        'is_selected',
        'selection_reason',
        'selected_at',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
        'selected_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the GAMO objective
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Scope selected GAMOs
     */
    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }
}
