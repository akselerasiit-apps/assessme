@extends('layouts.app')

@section('title', 'Assessment Progress')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">Progress Tracking</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @if(!$stats['is_complete'])
                    <a href="{{ route('assessments.answer', $assessment) }}" class="btn btn-primary">
                        <i class="ti ti-pencil me-2"></i>Continue Assessment
                    </a>
                    @endif
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Overall Progress Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Overall Progress</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">All Questions</span>
                                <span class="fw-bold">{{ $stats['answered_questions'] }} / {{ $stats['total_questions'] }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: {{ $stats['overall_progress'] }}%" 
                                     role="progressbar" aria-valuenow="{{ $stats['overall_progress'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <span>{{ $stats['overall_progress'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Required Questions</span>
                                <span class="fw-bold">{{ $stats['answered_required'] }} / {{ $stats['total_required'] }}</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar {{ $stats['required_complete'] ? 'bg-success' : 'bg-warning' }}" 
                                     style="width: {{ $stats['required_progress'] }}%" 
                                     role="progressbar" aria-valuenow="{{ $stats['required_progress'] }}" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <span>{{ $stats['required_progress'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Total Questions</div>
                                </div>
                                <div class="h2 mb-0">{{ $stats['total_questions'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Answered</div>
                                </div>
                                <div class="h2 mb-0 text-success">{{ $stats['answered_questions'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Remaining</div>
                                </div>
                                <div class="h2 mb-0 text-orange">{{ $stats['unanswered_questions'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">Status</div>
                                </div>
                                <div class="h3 mb-0">
                                    @if($stats['is_complete'])
                                        <span class="badge bg-success">Complete</span>
                                    @elseif($stats['overall_progress'] > 50)
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">Started</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!$stats['required_complete'])
                <div class="alert alert-warning mt-3">
                    <div class="d-flex">
                        <div><i class="ti ti-alert-triangle alert-icon"></i></div>
                        <div>
                            <h4 class="alert-title">Required Questions Incomplete</h4>
                            <div class="text-muted">
                                You still have <strong>{{ $stats['total_required'] - $stats['answered_required'] }}</strong> required question(s) to answer before submitting this assessment.
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($stats['required_complete'] && !$stats['is_complete'])
                <div class="alert alert-success mt-3">
                    <div class="d-flex">
                        <div><i class="ti ti-check alert-icon"></i></div>
                        <div>
                            <h4 class="alert-title">Required Questions Complete!</h4>
                            <div class="text-muted">
                                All required questions have been answered. You can now submit this assessment or continue with optional questions.
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Progress by GAMO Objective -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress by GAMO Objective</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>GAMO Objective</th>
                            <th class="text-center">Questions</th>
                            <th class="text-center">Required</th>
                            <th style="width: 300px;">Progress</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progressData as $data)
                        <tr>
                            <td>
                                <div>
                                    <span class="badge badge-outline text-{{ 
                                        $data['gamo']->category == 'EDM' ? 'purple' : 
                                        ($data['gamo']->category == 'APO' ? 'blue' : 
                                        ($data['gamo']->category == 'BAI' ? 'green' : 
                                        ($data['gamo']->category == 'DSS' ? 'orange' : 'pink'))) 
                                    }} me-2">
                                        {{ $data['gamo']->code }}
                                    </span>
                                    <span class="fw-bold">{{ $data['gamo']->name }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-lt">
                                    {{ $data['answered_questions'] }} / {{ $data['total_questions'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($data['required_complete'])
                                    <span class="badge bg-success">
                                        <i class="ti ti-check me-1"></i>
                                        {{ $data['required_answered'] }} / {{ $data['required_questions'] }}
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        {{ $data['required_answered'] }} / {{ $data['required_questions'] }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-fill me-2">
                                        <div class="progress-bar {{ $data['progress_percentage'] == 100 ? 'bg-success' : 'bg-primary' }}" 
                                             style="width: {{ $data['progress_percentage'] }}%">
                                        </div>
                                    </div>
                                    <span class="text-muted">{{ $data['progress_percentage'] }}%</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('assessments.answer', $assessment) }}#gamo-{{ $data['gamo']->id }}" 
                                   class="btn btn-sm btn-ghost-primary">
                                    <i class="ti ti-pencil"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
