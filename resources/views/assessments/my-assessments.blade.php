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
<!-- Stats Cards -->
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total</div>
                </div>
                <div class="h1 mb-3">{{ $stats['total'] }}</div>
                <div class="d-flex mb-2">
                    <div>All assessments</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">In Progress</div>
                </div>
                <div class="h1 mb-3">{{ $stats['in_progress'] }}</div>
                <div class="d-flex mb-2">
                    <div>Active assessments</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Completed</div>
                </div>
                <div class="h1 mb-3">{{ $stats['completed'] }}</div>
                <div class="d-flex mb-2">
                    <div>Finished assessments</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Approved</div>
                </div>
                <div class="h1 mb-3">{{ $stats['approved'] }}</div>
                <div class="d-flex mb-2">
                    <div>Final approved</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assessments Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Your Assessments</h3>
        <div class="card-actions">
            <form method="GET" action="{{ route('assessments.my-assessments') }}" class="d-flex gap-2">
                <input 
                    type="search" 
                    name="search" 
                    class="form-control form-control-sm" 
                    placeholder="Search..." 
                    value="{{ request('search') }}"
                    style="width: 200px;"
                >
                <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </form>
        </div>
    </div>
    
    @if($assessments->count() > 0)
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Assessment</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
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
                    <td>{{ $assessment->company }}</td>
                    <td>
                        <span class="badge bg-blue-lt">
                            {{ ucfirst($assessment->assessment_type) }}
                        </span>
                    </td>
                    <td>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="progress" style="width: 100px; height: 8px;">
                                    <div 
                                        class="progress-bar" 
                                        style="width: {{ $assessment->completion_percentage ?? 0 }}%"
                                        role="progressbar"
                                    ></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">{{ $assessment->completion_percentage ?? 0 }}%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($assessment->status == 'draft')
                            <span class="badge bg-secondary">Draft</span>
                        @elseif($assessment->status == 'in_progress')
                            <span class="badge bg-info">In Progress</span>
                        @elseif($assessment->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($assessment->status == 'reviewed')
                            <span class="badge bg-warning">Reviewed</span>
                        @elseif($assessment->status == 'approved')
                            <span class="badge bg-primary">Approved</span>
                        @endif
                    </td>
                    <td>{{ $assessment->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye"></i>
                            </a>
                            
                            @can('update', $assessment)
                            @if(in_array($assessment->status, ['draft', 'in_progress']))
                            <a href="{{ route('assessments.answer', $assessment) }}" class="btn btn-sm btn-outline-info">
                                <i class="ti ti-pencil"></i>
                            </a>
                            @endif
                            @endcan
                            
                            @can('delete', $assessment)
                            <form 
                                action="{{ route('assessments.destroy', $assessment) }}" 
                                method="POST" 
                                class="d-inline"
                                onsubmit="return confirm('Delete this assessment?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
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
