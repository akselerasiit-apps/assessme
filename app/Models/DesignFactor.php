<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DesignFactor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'factor_order',
        'is_active',
    ];

    protected $casts = [
        'factor_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get assessments that use this design factor
     */
    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'assessment_design_factors')
            ->withPivot('selected_value', 'description')
            ->withTimestamps();
    }

    /**
     * Scope active design factors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
