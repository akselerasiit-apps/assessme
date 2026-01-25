<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'activity_id',
        'level',
        'note_text',
        'created_by',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get the assessment this note belongs to
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the activity (question) this note belongs to
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(GamoQuestion::class, 'activity_id');
    }

    /**
     * Get the user who created this note
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
