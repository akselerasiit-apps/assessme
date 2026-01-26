<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssessmentPolicy
{
    /**
     * Determine whether the user can view any models.
     * UAM: All authenticated users can view assessments list
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Assessor', 'Viewer']);
    }

    /**
     * Determine whether the user can view the model.
     * UAM: 
     * - Super Admin, Admin: All assessments
     * - Manager: Own company assessments + team assessments
     * - Assessor: Assigned assessments only
     * - Viewer: All assessments from own company (read-only)
     */
    public function view(User $user, Assessment $assessment): bool
    {
        // Super Admin & Admin: view all
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Manager: view own company assessments
        if ($user->hasRole('Manager')) {
            return $user->company_id === $assessment->company_id;
        }

        // Viewer & Asesi: view all assessments from own company (read-only)
        if ($user->hasAnyRole(['Viewer', 'Asesi'])) {
            return $user->company_id === $assessment->company_id;
        }

        // Assessor: view assigned assessments only
        if ($user->hasRole('Assessor')) {
            return $assessment->created_by === $user->id 
                || $assessment->answers()->where('answered_by', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * UAM: Super Admin, Admin, Manager can create assessments
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager']);
    }

    /**
     * Determine whether the user can update the model.
     * UAM:
     * - Super Admin, Admin: update all
     * - Manager: update own company assessments (not in reviewed/approved status)
     * - Others: cannot update
     */
    public function update(User $user, Assessment $assessment): bool
    {
        // Super Admin & Admin: update all
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        // Manager: update own company assessments (not locked)
        if ($user->hasRole('Manager')) {
            return $user->company_id === $assessment->company_id
                && !in_array($assessment->status, ['reviewed', 'approved', 'archived']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * UAM:
     * - Super Admin: delete all
     * - Admin: delete if status = draft (no answers)
     * - Others: cannot delete
     */
    public function delete(User $user, Assessment $assessment): bool
    {
        // Super Admin: delete all
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Admin: delete only draft assessments without answers
        if ($user->hasRole('Admin')) {
            return $assessment->status === 'draft' 
                && !$assessment->answers()->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can submit answers for assessment.
     * UAM:
     * - Super Admin, Admin: answer all assessments
     * - Manager: answer own company assessments (not locked)
     * - Assessor: answer assigned assessments only
     */
    public function answer(User $user, Assessment $assessment): bool
    {
        // Super Admin & Admin: answer all (except approved/archived)
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return !in_array($assessment->status, ['approved', 'archived']);
        }

        // Manager: answer own company assessments (not locked)
        if ($user->hasRole('Manager')) {
            return $user->company_id === $assessment->company_id
                && !in_array($assessment->status, ['approved', 'archived']);
        }

        // Assessor: answer assigned assessments or created by them
        if ($user->hasRole('Assessor')) {
            return ($assessment->created_by === $user->id 
                    || $assessment->answers()->where('answered_by', $user->id)->exists())
                && in_array($assessment->status, ['draft', 'in_progress']);
        }

        return false;
    }

    /**
     * Determine whether the user can take/fill the assessment.
     * Updated to allow Viewer and Asesi read-only access
     */
    public function takeAssessment(User $user, Assessment $assessment): bool
    {
        // Viewer & Asesi: read-only access to view evidence, summary, OFI
        // Asesi can also upload evidence via uploadEvidence policy
        if ($user->hasAnyRole(['Viewer', 'Asesi'])) {
            return $user->company_id === $assessment->company_id;
        }
        
        // For other roles, use answer policy
        return $this->answer($user, $assessment);
    }

    /**
     * Determine whether the user can upload evidence.
     * Asesi can upload evidence even though they can't answer questions
     */
    public function uploadEvidence(User $user, Assessment $assessment): bool
    {
        // Asesi: can upload evidence for own company assessments
        if ($user->hasRole('Asesi')) {
            return $user->company_id === $assessment->company_id;
        }
        
        // For other roles, same as answer permission
        return $this->answer($user, $assessment);
    }

    /**
     * Determine whether the user can review the assessment.
     * UAM: Only Super Admin can review
     */
    public function review(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('Super Admin') 
            && $assessment->status === 'completed';
    }

    /**
     * Determine whether the user can approve the assessment.
     * UAM: Super Admin approves after review (same as review)
     */
    public function approve(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('Super Admin') 
            && $assessment->status === 'reviewed';
    }

    /**
     * Determine whether the user can archive the assessment.
     * UAM: Super Admin, Admin can archive
     */
    public function archive(User $user, Assessment $assessment): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin'])
            && in_array($assessment->status, ['completed', 'reviewed', 'approved']);
    }

    /**
     * Determine whether the user can restore the model.
     * UAM: Super Admin only
     */
    public function restore(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * UAM: Super Admin only
     */
    public function forceDelete(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can assign assessor to assessment.
     * UAM: Admin, Manager can assign assessor
     */
    public function assignAssessor(User $user, Assessment $assessment): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if ($user->hasRole('Manager')) {
            return $user->company_id === $assessment->company_id
                && !in_array($assessment->status, ['approved', 'archived']);
        }

        return false;
    }
}
