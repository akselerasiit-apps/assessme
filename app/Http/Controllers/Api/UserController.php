<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of users
     * Access: Super Admin, Admin (Full), Manager (Read only)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with(['roles', 'permissions'])
            ->when($request->input('role'), function($q) use ($request) {
                return $q->whereHas('roles', function($query) use ($request) {
                    $query->where('name', $request->input('role'));
                });
            })
            ->when($request->input('status'), function($q) use ($request) {
                return $q->where('status', $request->input('status'));
            })
            ->when($request->input('search'), function($q) use ($request) {
                $search = $request->input('search');
                return $q->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));
        
        return UserResource::collection($users);
    }

    /**
     * Store a newly created user
     * Access: Super Admin, Admin only
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'role' => 'required|exists:roles,name',
            'status' => 'nullable|in:active,inactive'
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
            'position' => $validated['position'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);
        
        // Assign role
        $role = Role::findByName($validated['role']);
        $user->assignRole($role);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.created',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'ip_address' => request()->ip()
            ])
            ->log('user.created');
        
        return new UserResource($user->load(['roles', 'permissions']));
    }

    /**
     * Display the specified user
     * Access: Super Admin, Admin (all users), Manager (limited), Assessor (own profile)
     */
    public function show($id)
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($id);
        $this->authorize('view', $user);
        
        return new UserResource($user);
    }

    /**
     * Update the specified user
     * Access: Super Admin, Admin (cannot delete Super Admin users), Manager (limited)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        
        // Prevent modification of Super Admin by non-Super Admin
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return response()->json(['message' => 'Cannot modify Super Admin users'], 403);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'role' => 'sometimes|required|exists:roles,name',
            'status' => 'sometimes|required|in:active,inactive'
        ]);
        
        $oldValues = $user->only(['name', 'email', 'phone', 'department', 'position', 'status']);
        
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $user->update($validated);
        
        // Update role if provided
        if (isset($validated['role'])) {
            $role = Role::findByName($validated['role']);
            $user->syncRoles([$role]);
        }
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.updated',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'old_values' => $oldValues,
                'new_values' => $user->only(['name', 'email', 'phone', 'department', 'position', 'status']),
                'ip_address' => request()->ip()
            ])
            ->log('user.updated');
        
        return new UserResource($user->fresh(['roles', 'permissions']));
    }

    /**
     * Remove the specified user
     * Access: Super Admin, Admin only (cannot delete Super Admin users)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        
        // Prevent deletion of Super Admin by non-Super Admin
        if ($user->hasRole('Super Admin') && !auth()->user()->hasRole('Super Admin')) {
            return response()->json(['message' => 'Cannot delete Super Admin users'], 403);
        }
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 403);
        }
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.deleted',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'deleted_user' => $user->only(['name', 'email', 'department']),
                'ip_address' => request()->ip()
            ])
            ->log('user.deleted');
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Reset user password
     * Access: Super Admin, Admin
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.password_reset',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'ip_address' => request()->ip()
            ])
            ->log('user.password_reset');
        
        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Assign role to user
     * Access: Super Admin only
     */
    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (!auth()->user()->hasRole('Super Admin')) {
            return response()->json(['message' => 'Unauthorized. Only Super Admin can assign roles'], 403);
        }
        
        $validated = $request->validate([
            'role' => 'required|exists:roles,name'
        ]);
        
        $oldRoles = $user->roles->pluck('name')->toArray();
        $role = Role::findByName($validated['role']);
        $user->syncRoles([$role]);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.role_assigned',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'old_roles' => $oldRoles,
                'new_role' => $validated['role'],
                'ip_address' => request()->ip()
            ])
            ->log('user.role_assigned');
        
        return new UserResource($user->fresh(['roles', 'permissions']));
    }

    /**
     * Update user status (activate/deactivate)
     * Access: Super Admin, Admin
     */
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);
        
        $user->update(['status' => $validated['status']]);
        
        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'user.status_updated',
                'entity_type' => 'User',
                'entity_id' => $user->id,
                'status' => $validated['status'],
                'ip_address' => request()->ip()
            ])
            ->log('user.status_updated');
        
        return new UserResource($user);
    }

    /**
     * Bulk import users
     * Access: Super Admin, Admin
     */
    public function bulkImport(Request $request)
    {
        $this->authorize('create', User::class);
        
        $request->validate([
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users,email',
            'users.*.password' => 'required|string|min:8',
            'users.*.role' => 'required|exists:roles,name',
            'users.*.department' => 'nullable|string|max:100',
            'users.*.position' => 'nullable|string|max:100',
        ]);
        
        $createdUsers = [];
        
        foreach ($request->users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'phone' => $userData['phone'] ?? null,
                'department' => $userData['department'] ?? null,
                'position' => $userData['position'] ?? null,
                'status' => 'active',
            ]);
            
            $role = Role::findByName($userData['role']);
            $user->assignRole($role);
            
            $createdUsers[] = $user;
        }
        
        activity()
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'users.bulk_imported',
                'count' => count($createdUsers),
                'ip_address' => request()->ip()
            ])
            ->log('users.bulk_imported');
        
        return response()->json([
            'message' => count($createdUsers) . ' users imported successfully',
            'users' => UserResource::collection(collect($createdUsers)->load(['roles', 'permissions']))
        ]);
    }
}
