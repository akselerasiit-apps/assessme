<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentEvidence extends Model
{
    use HasFactory;

    protected $table = 'assessment_evidence';

    protected $fillable = [
        'assessment_id',
        'activity_id',
        'evidence_name',
        'evidence_description',
        'file_path',
        'url',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the assessment this evidence belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the activity (question) this evidence belongs to
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(GamoQuestion::class, 'activity_id');
    }

    /**
     * Get the user who uploaded this evidence
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Check if evidence has a file
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Check if evidence has a URL
     */
    public function hasUrl(): bool
    {
        return !empty($this->url);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}
