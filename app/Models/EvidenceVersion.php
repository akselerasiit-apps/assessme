<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_answer_id',
        'version_number',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'file_hash',
        'is_encrypted',
        'version_notes',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the assessment answer that owns the evidence version
     */
    public function assessmentAnswer(): BelongsTo
    {
        return $this->belongsTo(AssessmentAnswer::class, 'assessment_answer_id');
    }

    /**
     * Get the user who uploaded this version
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Get file icon class based on file type
     */
    public function getFileIconAttribute(): string
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        $icons = [
            'pdf' => 'ti-file-type-pdf text-danger',
            'doc' => 'ti-file-type-doc text-primary',
            'docx' => 'ti-file-type-doc text-primary',
            'xls' => 'ti-file-type-xls text-success',
            'xlsx' => 'ti-file-type-xls text-success',
            'jpg' => 'ti-photo text-info',
            'jpeg' => 'ti-photo text-info',
            'png' => 'ti-photo text-info',
            'zip' => 'ti-file-zip text-warning',
        ];

        return $icons[$extension] ?? 'ti-file text-secondary';
    }
}
