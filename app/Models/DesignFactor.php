<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DesignFactor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_id',
        'description',
        'description_id',
        'category',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'integer',
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
