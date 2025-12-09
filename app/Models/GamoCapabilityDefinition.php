<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamoCapabilityDefinition extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gamo_objective_id',
        'level',
        'level_name',
        'compliance_score',
        'weight',
        'min_questions',
        'max_questions',
        'required_evidence_count',
        'required_compliance_percentage',
        'additional_requirements',
        'guidance_text',
        'examples',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'compliance_score' => 'decimal:2',
        'weight' => 'integer',
        'min_questions' => 'integer',
        'max_questions' => 'integer',
        'required_evidence_count' => 'integer',
        'required_compliance_percentage' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the GAMO objective this definition belongs to
     */
    public function gamoObjective(): BelongsTo
    {
        return $this->belongsTo(GamoObjective::class, 'gamo_objective_id');
    }

    /**
     * Scope by level
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope active definitions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
