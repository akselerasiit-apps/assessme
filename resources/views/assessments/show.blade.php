@extends('layouts.app')

@section('title', 'Assessment Detail')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Assessment</div>
            <h2 class="page-title">{{ $assessment->title }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @can('answer', $assessment)
                <a href="{{ route('assessments.answer', $assessment) }}" class="btn btn-primary">
                    <i class="ti ti-clipboard-text me-1"></i>
                    Answer Questions
                </a>
                @endcan
                
                @can('update', $assessment)
                <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-outline-primary">
                    <i class="ti ti-edit me-1"></i>
                    Edit
                </a>
                @endcan
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">
                            <i class="ti ti-file-download me-2"></i>Export PDF
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="ti ti-table-export me-2"></i>Export Excel
                        </a>
                        <div class="dropdown-divider"></div>
                        @can('delete', $assessment)
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Delete this assessment?')) document.getElementById('delete-form').submit();">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                        <form id="delete-form" action="{{ route('assessments.destroy', $assessment) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Status Banner -->
<div class="alert alert-{{ $assessment->status == 'approved' ? 'success' : ($assessment->status == 'draft' ? 'secondary' : 'info') }} mb-3">
    <div class="d-flex">
        <div>
            <i class="ti ti-info-circle icon alert-icon"></i>
        </div>
        <div>
            <h4 class="alert-title">Status: {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</h4>
            <div class="text-muted">
                @if($assessment->status == 'draft')
                    This assessment is in draft. Start answering questions to make progress.
                @elseif($assessment->status == 'in_progress')
                    Assessment is in progress. Continue answering questions.
                @elseif($assessment->status == 'completed')
                    Assessment is completed and ready for review.
                @elseif($assessment->status == 'approved')
                    This assessment has been approved.
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Basic Information</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Code</div>
                        <div class="datagrid-content">
                            <span class="badge bg-blue-lt">{{ $assessment->code }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Company</div>
                        <div class="datagrid-content">{{ $assessment->company->name ?? 'N/A' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Type</div>
                        <div class="datagrid-content">
                            <span class="badge bg-azure-lt">{{ ucfirst($assessment->assessment_type) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Scope Type</div>
                        <div class="datagrid-content">
                            <span class="badge bg-indigo-lt">{{ ucfirst($assessment->scope_type) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Period</div>
                        <div class="datagrid-content">
                            {{ $assessment->assessment_period_start?->format('d M Y') ?? 'N/A' }} 
                            - 
                            {{ $assessment->assessment_period_end?->format('d M Y') ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created By</div>
                        <div class="datagrid-content">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ $assessment->createdBy?->name }}&background=206bc4&color=fff)"></span>
                                <div>
                                    <div>{{ $assessment->createdBy?->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $assessment->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($assessment->description)
                    <div class="datagrid-item">
                        <div class="datagrid-title">Description</div>
                        <div class="datagrid-content">{{ $assessment->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Design Factors -->
        @if($assessment->designFactors->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-adjustments me-2"></i>
                    Design Factors
                    <span class="badge bg-blue ms-2">{{ $assessment->designFactors->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($assessment->designFactors as $factor)
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar bg-blue-lt">{{ $factor->code }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $factor->name }}</div>
                                        <div class="text-muted small">{{ $factor->description }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- GAMO Objectives -->
        @if($assessment->gamoObjectives->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-target me-2"></i>
                    GAMO Objectives
                    <span class="badge bg-green ms-2">{{ $assessment->gamoObjectives->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                @php
                    $categories = $assessment->gamoObjectives->groupBy('category');
                @endphp
                
                @foreach($categories as $category => $objectives)
                <div class="mb-4">
                    <div class="mb-2">
                        <span class="badge bg-blue">{{ $category }}</span>
                        <span class="text-muted ms-2">{{ $objectives->count() }} objectives</span>
                    </div>
                    <div class="list-group">
                        @foreach($objectives as $gamo)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-blue-lt">{{ $gamo->code }}</span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ $gamo->name }}</div>
                                    <div class="text-muted small">{{ $gamo->description }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Answers Progress -->
        @if($assessment->answers->count() > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-checklist me-2"></i>
                    Assessment Progress
                </h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Overall Progress</span>
                        <span class="text-muted">{{ $assessment->answers->count() }} questions answered</span>
                    </div>
                    <div class="progress progress-lg">
                        <div class="progress-bar bg-primary" style="width: {{ $assessment->progress_percentage }}%" role="progressbar">
                            {{ $assessment->progress_percentage }}%
                        </div>
                    </div>
                </div>
                
                @if($assessment->gamoScores->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>GAMO</th>
                                <th>Current Level</th>
                                <th>Target Level</th>
                                <th>Gap</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessment->gamoScores as $score)
                            <tr>
                                <td>{{ $score->gamoObjective->code }}</td>
                                <td>
                                    <span class="badge bg-blue">{{ number_format($score->current_maturity_level, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-green">{{ number_format($score->target_maturity_level, 2) }}</span>
                                </td>
                                <td>
                                    @php
                                        $gap = $score->target_maturity_level - $score->current_maturity_level;
                                    @endphp
                                    <span class="badge bg-{{ $gap > 0 ? 'orange' : 'success' }}">
                                        {{ number_format(abs($gap), 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Progress Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Progress Summary</h3>
            </div>
            <div class="card-body">
                <div class="h1 m-0 mb-1">{{ $assessment->progress_percentage }}%</div>
                <div class="progress progress-sm mb-3">
                    <div class="progress-bar bg-primary" style="width: {{ $assessment->progress_percentage }}%" role="progressbar"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="text-muted small">Answered</div>
                        <div class="h3 m-0">{{ $assessment->answers->count() }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Objectives</div>
                        <div class="h3 m-0">{{ $assessment->gamoObjectives->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-blue-lt">
                                <i class="ti ti-plus"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Created</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->created_at->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->createdBy?->name }}</div>
                        </div>
                    </div>
                </div>
                
                @if($assessment->reviewed_by)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-info-lt">
                                <i class="ti ti-eye-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Reviewed</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->reviewed_at?->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->reviewedBy?->name }}</div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($assessment->approved_by)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-success-lt">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Approved</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->approved_at?->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->approvedBy?->name }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
