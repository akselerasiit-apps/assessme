<?php

namespace App\Policies;

use App\Models\AssessmentAnswer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnswerPolicy
{
    /**
     * Determine whether the user can view any models.
     * UAM: All authenticated users can view answers
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor', 'Viewer']);
    }

    /**
     * Determine whether the user can view the model.
     * UAM:
     * - Super Admin, Admin: view all answers
     * - Manager: view answers in own company assessments
     * - Assessor: view own answers
     * - Viewer: view answers (read-only)
     */
    public function view(User $user, AssessmentAnswer $answer): bool
    {
        // Super Admin & Admin: view all
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Manager: view answers in own company assessments
        if ($user->hasRole('Manager')) {
            return $answer->assessment 
                && $user->company_id === $answer->assessment->company_id;
        }

        // Assessor: view own answers or assigned assessment answers
        if ($user->hasRole('Assessor')) {
            return $answer->answered_by === $user->id
                || ($answer->assessment && $answer->assessment->created_by === $user->id);
        }

        // Viewer: view all answers (read-only)
        if ($user->hasRole('Viewer')) {
            return $answer->assessment 
                && $user->company_id === $answer->assessment->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * UAM: Assessor can create answers for assigned assessments
     *      Admin, Manager can also create answers
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor']);
    }

    /**
     * Determine whether the user can create answer for specific assessment.
     * Additional check for assessment status and assignment
     */
    public function createForAssessment(User $user, $assessment): bool
    {
        // Super Admin & Admin: create for all assessments
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return !in_array($assessment->status, ['approved', 'archived']);
        }

        // Manager: create for own company assessments
        if ($user->hasRole('Manager')) {
            return $user->company_id === $assessment->company_id
                && !in_array($assessment->status, ['approved', 'archived']);
        }

        // Assessor: create for assigned assessments only
        if ($user->hasRole('Assessor')) {
            return ($assessment->created_by === $user->id 
                    || $assessment->answers()->where('answered_by', $user->id)->exists())
                && in_array($assessment->status, ['draft', 'in_progress']);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     * UAM:
     * - Super Admin, Admin: update all answers
     * - Assessor: update own answers (if assessment not locked)
     * - Others: cannot update
     */
    public function update(User $user, AssessmentAnswer $answer): bool
    {
        // Super Admin & Admin: update all
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return $answer->assessment 
                && !in_array($answer->assessment->status, ['approved', 'archived']);
        }

        // Assessor: update own answers only (assessment not locked)
        if ($user->hasRole('Assessor')) {
            return $answer->answered_by === $user->id
                && $answer->assessment
                && in_array($answer->assessment->status, ['draft', 'in_progress']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * UAM:
     * - Super Admin: delete all
     * - Admin: delete if assessment not completed
     * - Assessor: delete own answers (if assessment in draft)
     */
    public function delete(User $user, AssessmentAnswer $answer): bool
    {
        // Super Admin: delete all
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin: delete if assessment not locked
        if ($user->hasRole('Admin')) {
            return $answer->assessment
                && !in_array($answer->assessment->status, ['completed', 'reviewed', 'approved', 'archived']);
        }

        // Assessor: delete own answers in draft only
        if ($user->hasRole('Assessor')) {
            return $answer->answered_by === $user->id
                && $answer->assessment
                && $answer->assessment->status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can upload evidence for answer.
     * UAM: Assessor can upload evidence for own answers
     */
    public function uploadEvidence(User $user, AssessmentAnswer $answer): bool
    {
        // Super Admin & Admin: upload for all
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return $answer->assessment
                && !in_array($answer->assessment->status, ['approved', 'archived']);
        }

        // Assessor: upload evidence for own answers
        if ($user->hasRole('Assessor')) {
            return $answer->answered_by === $user->id
                && $answer->assessment
                && in_array($answer->assessment->status, ['draft', 'in_progress']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete evidence.
     * UAM: Only answer owner or Admin+ can delete evidence
     */
    public function deleteEvidence(User $user, AssessmentAnswer $answer): bool
    {
        // Super Admin & Admin: delete all evidence
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Assessor: delete own evidence only
        if ($user->hasRole('Assessor')) {
            return $answer->answered_by === $user->id
                && $answer->assessment
                && $answer->assessment->status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     * UAM: Super Admin only
     */
    public function restore(User $user, AssessmentAnswer $answer): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * UAM: Super Admin only
     */
    public function forceDelete(User $user, AssessmentAnswer $answer): bool
    {
        return $user->hasRole('Super Admin');
    }
}
