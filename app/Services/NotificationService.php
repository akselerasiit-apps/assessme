<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Assessment;
use App\Models\User;

class NotificationService
{
    /**
     * Notify assessor when assessee uploads evidence
     */
    public static function notifyEvidenceUploaded(Assessment $assessment, $evidenceId, $evidenceName, User $uploader)
    {
        // Get assessor(s) for this assessment
        // Assuming created_by is the assessor
        $assessor = User::find($assessment->created_by);
        
        if ($assessor && $assessor->id !== $uploader->id) {
            Notification::create([
                'user_id' => $assessor->id,
                'type' => 'evidence_uploaded',
                'title' => 'Evidence Baru Diunggah',
                'message' => "{$uploader->name} mengunggah evidence '{$evidenceName}' untuk assessment {$assessment->title}",
                'assessment_id' => $assessment->id,
                'related_user_id' => $uploader->id,
                'data' => [
                    'evidence_id' => $evidenceId,
                    'evidence_name' => $evidenceName,
                    'assessment_code' => $assessment->code,
                ],
            ]);
        }
    }

    /**
     * Notify assessee when assessor updates rating/OFI
     */
    public static function notifyAssessmentUpdated(Assessment $assessment, $type, $message, User $assessor, $additionalData = [])
    {
        // Get company users (assessee) for this assessment
        $companyUsers = User::where('company_id', $assessment->company_id)
            ->where('id', '!=', $assessor->id)
            ->get();

        $titles = [
            'rating_updated' => 'Penilaian Diperbarui',
            'ofi_added' => 'OFI Ditambahkan',
            'recommendation_added' => 'Rekomendasi Ditambahkan',
            'comment_added' => 'Komentar Ditambahkan',
        ];

        foreach ($companyUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $titles[$type] ?? 'Update Assessment',
                'message' => $message,
                'assessment_id' => $assessment->id,
                'related_user_id' => $assessor->id,
                'data' => array_merge([
                    'assessment_code' => $assessment->code,
                    'assessment_title' => $assessment->title,
                ], $additionalData),
            ]);
        }
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
