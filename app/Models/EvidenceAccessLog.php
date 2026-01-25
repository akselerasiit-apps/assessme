<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvidenceAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_answer_id',
        'evidence_version_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Get the assessment answer
     */
    public function assessmentAnswer(): BelongsTo
    {
        return $this->belongsTo(AssessmentAnswer::class, 'assessment_answer_id');
    }

    /**
     * Get the evidence version
     */
    public function evidenceVersion(): BelongsTo
    {
        return $this->belongsTo(EvidenceVersion::class, 'evidence_version_id');
    }

    /**
     * Get the user who accessed the evidence
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an evidence access
     */
    public static function logAccess(
        int $answerId,
        ?int $versionId,
        string $action
    ): void {
        self::create([
            'assessment_answer_id' => $answerId,
            'evidence_version_id' => $versionId,
            'user_id' => auth()->id(),
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accessed_at' => now(),
        ]);
    }
}
