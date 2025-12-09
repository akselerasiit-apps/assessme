<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'industry',
        'size',
        'established_year',
    ];

    protected $casts = [
        'established_year' => 'integer',
    ];

    /**
     * Get all assessments for this company
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Scope by company size
     */
    public function scopeBySize($query, string $size)
    {
        return $query->where('size', $size);
    }

    /**
     * Scope by industry
     */
    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }
}
