<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller
{
    public function store(Request $request)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $validated['name']]);
            
            if (!empty($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }
            
            DB::commit();
            
            return redirect()->route('admin.roles')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create role: ' . $e->getMessage()]);
        }
    }
    
    public function update(Request $request, Role $role)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        // Prevent editing Super Admin role
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Cannot edit Super Admin role']);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        
        DB::beginTransaction();
        try {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);
            
            DB::commit();
            
            return redirect()->route('admin.roles')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update role: ' . $e->getMessage()]);
        }
    }
    
    public function destroy(Role $role)
    {
        abort_if(!auth()->user()->hasAnyRole(['Super Admin', 'Admin']), 403);
        
        // Prevent deleting Super Admin role
        if ($role->name === 'Super Admin') {
            return back()->withErrors(['error' => 'Cannot delete Super Admin role']);
        }
        
        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete role with assigned users']);
        }
        
        try {
            $role->delete();
            return redirect()->route('admin.roles')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete role: ' . $e->getMessage()]);
        }
    }
}
