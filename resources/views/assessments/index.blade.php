@extends('layouts.app')

@section('title', 'Assessments')

@section('page-header')
    <h2 class="page-title">Assessments</h2>
    <div class="page-subtitle">Manage and track all COBIT 2019 assessments</div>
@endsection

@section('page-actions')
    @can('create assessments')
    <a href="{{ route('assessments.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>
        Create Assessment
    </a>
    @endcan
@endsection

@section('content')
<!-- Status Filter Tabs -->
<div class="card mb-3">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index') }}" class="nav-link {{ !request('status') ? 'active' : '' }}">
                    <i class="ti ti-list me-1"></i>All
                    <span class="badge bg-secondary ms-1">{{ $statusCounts['all'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index', ['status' => 'draft']) }}" class="nav-link {{ request('status') == 'draft' ? 'active' : '' }}">
                    <i class="ti ti-file me-1"></i>Draft
                    <span class="badge bg-secondary ms-1">{{ $statusCounts['draft'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index', ['status' => 'in_progress']) }}" class="nav-link {{ request('status') == 'in_progress' ? 'active' : '' }}">
                    <i class="ti ti-progress me-1"></i>In Progress
                    <span class="badge text-white bg-blue ms-1">{{ $statusCounts['in_progress'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index', ['status' => 'completed']) }}" class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}">
                    <i class="ti ti-circle-check me-1"></i>Completed
                    <span class="badge text-white bg-success ms-1">{{ $statusCounts['completed'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index', ['status' => 'reviewed']) }}" class="nav-link {{ request('status') == 'reviewed' ? 'active' : '' }}">
                    <i class="ti ti-eye-check me-1"></i>Reviewed
                    <span class="badge text-white bg-info ms-1">{{ $statusCounts['reviewed'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.index', ['status' => 'approved']) }}" class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}">
                    <i class="ti ti-rosette me-1"></i>Approved
                    <span class="badge text-white bg-purple ms-1">{{ $statusCounts['approved'] }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('assessments.index') }}" class="row g-3">
            <input type="hidden" name="status" value="{{ request('status') }}">
            
            <div class="col-md-5">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title, code, or description..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-md-4">
                <select name="company_id" class="form-select">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assessments Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Created By</th>
                    <th>Period</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessments as $assessment)
                <tr>
                    <td>
                        <div class="text-muted">{{ $assessment->code }}</div>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $assessment->title }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 300px;">
                            {{ $assessment->description }}
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">{{ $assessment->company->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-azure-lt">{{ ucfirst($assessment->assessment_type) }}</span>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'in_progress' => 'blue',
                                'completed' => 'success',
                                'reviewed' => 'info',
                                'approved' => 'purple',
                                'archived' => 'dark'
                            ];
                            $color = $statusColors[$assessment->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</span>
                    </td>
                    <td>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="progress progress-sm" style="width: 80px;">
                                    <div class="progress-bar" style="width: {{ $assessment->progress_percentage }}%" role="progressbar" aria-valuenow="{{ $assessment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">{{ $assessment->progress_percentage }}%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ $assessment->createdBy?->name }}&background=206bc4&color=fff)"></span>
                            <div>
                                <div class="small">{{ $assessment->createdBy?->name ?? 'N/A' }}</div>
                                <div class="text-muted small">{{ $assessment->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small text-muted">
                            {{ $assessment->assessment_period_start?->format('d M Y') ?? 'N/A' }}
                            <br>to<br>
                            {{ $assessment->assessment_period_end?->format('d M Y') ?? 'N/A' }}
                        </div>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            @can('view', $assessment)
                            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            @endcan
                            
                            @can('answer', $assessment)
                            <a href="{{ route('assessments.answer-new', $assessment) }}" class="btn btn-sm btn-success" title="Answer Assessment (New)">
                                <i class="ti ti-clipboard-check"></i>
                            </a>
                            @endcan
                            
                            @can('update', $assessment)
                            @if(in_array($assessment->status, ['draft', 'in_progress']))
                            <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                <i class="ti ti-pencil"></i>
                            </a>
                            @endif
                            @endcan
                            
                            @can('delete', $assessment)
                            @if($assessment->status === 'draft')
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                onclick="if(confirm('Are you sure you want to delete this assessment?')) { document.getElementById('delete-form-{{ $assessment->id }}').submit(); }">
                                <i class="ti ti-trash"></i>
                            </button>
                            @endif
                            @endcan
                        </div>
                        
                        @can('delete', $assessment)
                        @if($assessment->status === 'draft')
                        <form id="delete-form-{{ $assessment->id }}" 
                            action="{{ route('assessments.destroy', $assessment) }}" 
                            method="POST" 
                            class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-clipboard-off" style="font-size: 3rem;"></i>
                            </div>
                            <p class="empty-title">No assessments found</p>
                            <p class="empty-subtitle text-muted">
                                @if(request('search') || request('status') || request('company_id'))
                                    Try adjusting your filters
                                @else
                                    Get started by creating your first assessment
                                @endif
                            </p>
                            @can('create assessments')
                                @if(!request('search') && !request('status') && !request('company_id'))
                                <div class="empty-action">
                                    <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                                        <i class="ti ti-plus me-1"></i>
                                        Create Assessment
                                    </a>
                                </div>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($assessments->hasPages())
    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">
            Showing <span>{{ $assessments->firstItem() }}</span> to <span>{{ $assessments->lastItem() }}</span> of <span>{{ $assessments->total() }}</span> entries
        </p>
        <ul class="pagination m-0 ms-auto">
            {{ $assessments->links() }}
        </ul>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function deleteAssessment(id) {
    if (confirm('Are you sure you want to delete this assessment? This action cannot be undone.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
