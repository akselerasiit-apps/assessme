@extends('layouts.app')

@section('title', 'My Assessments')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">My Assessments</h2>
            <div class="text-muted mt-1">Assessments you created or are assigned to</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>
                Create Assessment
            </a>
        </div>
    </div>
@endsection

@section('content')
<!-- Status Filter Tabs -->
<div class="card mb-3">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my') }}" class="nav-link {{ !request('status') ? 'active' : '' }}">
                    <i class="ti ti-list me-1"></i>All
                    <span class="badge bg-secondary ms-1">{{ $stats['total'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my', ['status' => 'draft']) }}" class="nav-link {{ request('status') == 'draft' ? 'active' : '' }}">
                    <i class="ti ti-file me-1"></i>Draft
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my', ['status' => 'in_progress']) }}" class="nav-link {{ request('status') == 'in_progress' ? 'active' : '' }}">
                    <i class="ti ti-progress me-1"></i>In Progress
                    <span class="badge text-white bg-blue ms-1">{{ $stats['in_progress'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my', ['status' => 'completed']) }}" class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}">
                    <i class="ti ti-circle-check me-1"></i>Completed
                    <span class="badge text-white bg-success ms-1">{{ $stats['completed'] }}</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my', ['status' => 'reviewed']) }}" class="nav-link {{ request('status') == 'reviewed' ? 'active' : '' }}">
                    <i class="ti ti-eye-check me-1"></i>Reviewed
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{ route('assessments.my', ['status' => 'approved']) }}" class="nav-link {{ request('status') == 'approved' ? 'active' : '' }}">
                    <i class="ti ti-rosette me-1"></i>Approved
                    <span class="badge text-white bg-purple ms-1">{{ $stats['approved'] }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Search Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('assessments.my') }}" class="row g-3">
            <input type="hidden" name="status" value="{{ request('status') }}">
            
            <div class="col-md-9">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by title or code..." value="{{ request('search') }}">
                </div>
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
    @if($assessments->count() > 0)
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Assessment</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessments as $assessment)
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <a href="{{ route('assessments.show', $assessment) }}" class="text-reset fw-bold">
                                {{ $assessment->title }}
                            </a>
                            <small class="text-muted">{{ $assessment->code }}</small>
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">{{ $assessment->company->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-azure-lt">
                            {{ ucfirst(str_replace('_', ' ', $assessment->assessment_type)) }}
                        </span>
                    </td>
                    <td>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div 
                                        class="progress-bar bg-primary" 
                                        style="width: {{ $assessment->progress_percentage ?? 0 }}%"
                                        role="progressbar"
                                    ></div>
                                </div>
                            </div>
                            <div class="col-auto ps-0">
                                <small class="text-muted">{{ $assessment->progress_percentage ?? 0 }}%</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'in_progress' => 'blue',
                                'completed' => 'success',
                                'reviewed' => 'info',
                                'approved' => 'purple',
                            ];
                            $color = $statusColors[$assessment->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</span>
                    </td>
                    <td>{{ $assessment->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i>
                            </a>
                            
                            @can('update', $assessment)
                            @if(in_array($assessment->status, ['draft', 'in_progress']))
                            <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-info">
                                <i class="ti ti-pencil"></i>
                            </a>
                            @endif
                            @endcan
                            
                            @can('delete', $assessment)
                            @if($assessment->status === 'draft')
                            <button type="button" class="btn btn-sm btn-outline-danger" 
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
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="card-footer">
        {{ $assessments->links() }}
    </div>
    @else
    <div class="empty">
        <div class="empty-icon">
            <i class="ti ti-clipboard-list icon"></i>
        </div>
        <p class="empty-title">No assessments yet</p>
        <p class="empty-subtitle text-muted">
            You haven't created any assessments. Start by creating your first assessment.
        </p>
        <div class="empty-action">
            <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>
                Create Assessment
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
