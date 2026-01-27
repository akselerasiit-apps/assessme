<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_id' => 'nullable|exists:companies,id',
            'role' => 'required|exists:roles,name',
        ]);
        
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'company_id' => $validated['company_id'],
                'is_active' => true,
            ]);
            
            $user->syncRoles([$validated['role']]);
            
            DB::commit();
            
            return redirect()->route('admin.users')
                ->with('success', 'User created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }
    
    public function update(Request $request, User $user)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'company_id' => 'nullable|exists:companies,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);
        
        DB::beginTransaction();
        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company_id' => $validated['company_id'],
                'is_active' => $request->has('is_active'),
            ];
            
            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            
            $user->update($userData);
            $user->syncRoles([$validated['role']]);
            
            DB::commit();
            
            return redirect()->route('admin.users')
                ->with('success', 'User updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }
    
    public function destroy(User $user)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        // Prevent deleting current user
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Cannot delete your own account']);
        }
        
        try {
            $user->delete();
            return redirect()->route('admin.users')
                ->with('success', 'User deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }
    
    public function toggleStatus(User $user)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        // Prevent deactivating current user
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Cannot deactivate your own account']);
        }
        
        $user->update(['is_active' => !$user->is_active]);
        
        return back()->with('success', 'User status updated successfully');
    }
}
