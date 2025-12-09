<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentDesignFactor extends Model
{
    protected $fillable = [
        'assessment_id',
        'design_factor_id',
        'selected_value',
        'description',
    ];

    /**
     * Get the assessment
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the design factor
     */
    public function designFactor(): BelongsTo
    {
        return $this->belongsTo(DesignFactor::class);
    }
}
