@extends('layouts.app')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Admin</div>
                <h2 class="page-title">Role & Permission Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Add Role
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                    </div>
                    <div>
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-cards">
            @foreach($roles as $role)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $role->name }}</h3>
                            @if($role->name !== 'Super Admin')
                                <div class="card-actions">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="editRole({{ $role->id }}, '{{ $role->name }}', {{ json_encode($role->permissions->pluck('name')) }})">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="text-muted">Users:</span>
                                <span class="badge bg-blue">{{ $role->users_count }}</span>
                            </div>
                            <div class="mb-2">
                                <strong class="text-muted d-block mb-2">Permissions:</strong>
                                @if($role->permissions->count() > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($role->permissions as $permission)
                                            <span class="badge bg-azure-lt">{{ $permission->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No permissions assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">All Available Permissions</h3>
            </div>
            <div class="card-body">
                @foreach($permissions as $module => $perms)
                    <div class="mb-3">
                        <h4 class="text-capitalize">{{ $module }}</h4>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($perms as $permission)
                                <span class="badge bg-secondary-lt">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Create Role Modal -->
<div class="modal modal-blur fade" id="createRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach($permissions as $module => $perms)
                                <div class="mb-3">
                                    <h5 class="text-capitalize">{{ $module }}</h5>
                                    <div class="row">
                                        @foreach($perms as $permission)
                                            <div class="col-md-6">
                                                <label class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                                    <span class="form-check-label">{{ $permission->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal modal-blur fade" id="editRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="POST" id="editRoleForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Role Name</label>
                        <input type="text" name="name" id="edit_role_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach($permissions as $module => $perms)
                                <div class="mb-3">
                                    <h5 class="text-capitalize">{{ $module }}</h5>
                                    <div class="row">
                                        @foreach($perms as $permission)
                                            <div class="col-md-6">
                                                <label class="form-check">
                                                    <input class="form-check-input edit-permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                                    <span class="form-check-label">{{ $permission->name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRole(id, name, permissions) {
    // Set form action
    document.getElementById('editRoleForm').action = `/admin/roles/${id}`;
    
    // Set role name
    document.getElementById('edit_role_name').value = name;
    
    // Clear all permission checkboxes
    document.querySelectorAll('.edit-permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check role's permissions
    permissions.forEach(permission => {
        const checkbox = document.querySelector(`.edit-permission-checkbox[value="${permission}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    // Show modal
    new bootstrap.Modal(document.getElementById('editRoleModal')).show();
}
</script>
@endsection
