@extends('layouts.app')

@section('title', 'Manage Team - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.show', $assessment) }}">{{ $assessment->code }}</a>
                </div>
                <h2 class="page-title">Assessment Team Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                    Back to Assessment
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <div class="d-flex"><div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div><div>{{ session('success') }}</div></div>
                <a class="btn-close" data-bs-dismiss="alert"></a>
            </div>
        @endif

        <div class="row">
            <!-- Add Team Member Form -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Add Team Member</h3>
                    </div>
                    <form action="{{ route('assessments.team.store', $assessment) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">User</label>
                                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select user...</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->roles->pluck('name')->join(', ') }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Role</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="assessor">Assessor</option>
                                    <option value="lead">Team Lead</option>
                                    <option value="reviewer">Reviewer</option>
                                    <option value="observer">Observer</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Responsibilities</label>
                                <textarea name="responsibilities" rows="3" class="form-control" placeholder="Specific responsibilities for this member"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" name="can_edit" value="1" checked class="form-check-input">
                                    <span class="form-check-label">Can edit assessment</span>
                                </label>
                                <label class="form-check">
                                    <input type="checkbox" name="can_approve" value="1" class="form-check-input">
                                    <span class="form-check-label">Can approve assessment</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">Add Member</button>
                        </div>
                    </form>
                </div>

                <!-- Team Roles Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Team Roles</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2"><strong>Team Lead:</strong> Manages team, approves work</div>
                        <div class="mb-2"><strong>Assessor:</strong> Conducts interviews, answers questions</div>
                        <div class="mb-2"><strong>Reviewer:</strong> Reviews submissions</div>
                        <div class="mb-2"><strong>Observer:</strong> View-only access</div>
                    </div>
                </div>
            </div>

            <!-- Current Team Members -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Current Team ({{ $teamMembers->count() }} members)</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Role</th>
                                    <th>Responsibilities</th>
                                    <th>Permissions</th>
                                    <th>Assigned</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamMembers as $member)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2">{{ substr($member->user->name, 0, 2) }}</span>
                                            <div>
                                                <div>{{ $member->user->name }}</div>
                                                <small class="text-muted">{{ $member->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $member->role === 'lead' ? 'primary' : ($member->role === 'assessor' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($member->role) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($member->responsibilities, 50) ?: '-' }}</td>
                                    <td>
                                        @if($member->can_edit)
                                            <span class="badge bg-success-lt">Edit</span>
                                        @endif
                                        @if($member->can_approve)
                                            <span class="badge bg-warning-lt">Approve</span>
                                        @endif
                                        @if(!$member->can_edit && !$member->can_approve)
                                            <span class="text-muted">View only</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $member->assigned_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <form action="{{ route('assessments.team.destroy', [$assessment, $member]) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Remove this team member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-ghost-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        No team members assigned yet. Add members using the form on the left.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
