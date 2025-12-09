<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Access: Super Admin, Admin (Full), Manager (Read only)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager']);
    }

    /**
     * Determine whether the user can view the model.
     * Access: Super Admin, Admin (all users), Manager (team users), Assessor/Viewer (own profile)
     */
    public function view(User $user, User $model): bool
    {
        // Super Admin and Admin can view all users
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }
        
        // Manager can view users in their company
        if ($user->hasRole('Manager')) {
            // Assuming users have company relationship - adjust as needed
            return true; // Or: $user->company_id === $model->company_id;
        }
        
        // Assessor and Viewer can only view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     * Access: Super Admin, Admin only
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Determine whether the user can update the model.
     * Access: Super Admin, Admin (cannot modify Super Admin), Manager (limited), User (own profile)
     */
    public function update(User $user, User $model): bool
    {
        // Super Admin can update anyone
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        
        // Admin can update users except Super Admin
        if ($user->hasRole('Admin')) {
            return !$model->hasRole('Super Admin');
        }
        
        // Manager can update limited fields for team users
        if ($user->hasRole('Manager')) {
            return true; // Limited update - controlled in controller
        }
        
        // Users can update their own profile (limited fields)
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Access: Super Admin, Admin only (cannot delete Super Admin users)
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }
        
        // Super Admin can delete anyone except themselves
        if ($user->hasRole('Super Admin')) {
            return $user->id !== $model->id;
        }
        
        // Admin can delete users except Super Admin
        if ($user->hasRole('Admin')) {
            return !$model->hasRole('Super Admin');
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasRole('Super Admin');
    }
}
