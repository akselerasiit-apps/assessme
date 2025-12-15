@extends('layouts.app')

@section('title', 'Question Detail')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Question Bank</div>
                <h2 class="page-title">Question Detail</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                @can('update questions')
                <a href="{{ route('questions.edit', $question) }}" class="btn btn-primary">
                    <i class="ti ti-edit me-2"></i>Edit
                </a>
                @endcan
                <a href="{{ route('questions.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <!-- Question Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Question Information</h3>
                        <div class="card-actions">
                            @if($question->is_active)
                                <span class="badge bg-green">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Code -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong class="text-muted">Code:</strong>
                            </div>
                            <div class="col-md-9">
                                <code class="fs-4">{{ $question->code }}</code>
                            </div>
                        </div>

                        <!-- GAMO Objective -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <strong class="text-muted">GAMO Objective:</strong>
                            </div>
                            <div class="col-md-9">
                                <span class="badge badge-outline text-{{ 
                                    $question->gamoObjective->category == 'EDM' ? 'purple' : 
                                    ($question->gamoObjective->category == 'APO' ? 'blue' : 
                                    ($question->gamoObjective->category == 'BAI' ? 'green' : 
                                    ($question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                }} me-2">
                                    {{ $question->gamoObjective->code }}
                                </span>
                                {{ $question->gamoObjective->name }}
                            </div>
                        </div>

                        <hr>

                        <!-- Question Text -->
                        <div class="mb-4">
                            <label class="form-label text-muted">Question Text:</label>
                            <div class="fs-4 fw-bold">
                                {{ $question->question_text }}
                            </div>
                        </div>

                        @if($question->guidance)
                        <!-- Guidance -->
                        <div class="mb-4">
                            <label class="form-label text-muted">
                                <i class="ti ti-info-circle me-1"></i>Guidance:
                            </label>
                            <div class="alert alert-info mb-0">
                                {{ $question->guidance }}
                            </div>
                        </div>
                        @endif

                        @if($question->evidence_requirement)
                        <!-- Evidence Requirement -->
                        <div class="mb-4">
                            <label class="form-label text-muted">
                                <i class="ti ti-file-text me-1"></i>Evidence Requirement:
                            </label>
                            <div class="alert alert-warning mb-0">
                                {{ $question->evidence_requirement }}
                            </div>
                        </div>
                        @endif

                        <hr>

                        <!-- Configuration -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Question Type:</strong>
                                <div class="mt-1">
                                    <span class="badge bg-azure-lt fs-5">
                                        {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Maturity Level:</strong>
                                <div class="mt-1">
                                    <span class="badge badge-outline text-cyan fs-5">
                                        Level {{ $question->maturity_level }} - 
                                        {{ ['Initial', 'Managed', 'Defined', 'Quantitatively Managed', 'Optimizing'][$question->maturity_level - 1] ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Required:</strong>
                                <div class="mt-1">
                                    @if($question->required)
                                        <span class="badge bg-red-lt">Yes - Must be answered</span>
                                    @else
                                        <span class="badge bg-secondary-lt">No - Optional</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Question Order:</strong>
                                <div class="mt-1">
                                    <span class="badge bg-secondary-lt">
                                        {{ $question->question_order ?? 'Not set' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage Statistics Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Usage Statistics</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-primary-lt">
                                            <i class="ti ti-file-text fs-1"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Total Answers</div>
                                        <div class="fs-2 fw-bold">{{ $usageStats['answer_count'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div class="avatar avatar-lg bg-success-lt">
                                            <i class="ti ti-clipboard-check fs-1"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Used in Assessments</div>
                                        <div class="fs-2 fw-bold">{{ $usageStats['assessment_count'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($usageStats['answer_count'] > 0)
                        <div class="alert alert-info mb-0 mt-3">
                            <i class="ti ti-info-circle me-2"></i>
                            This question has been answered <strong>{{ $usageStats['answer_count'] }}</strong> time(s) 
                            across <strong>{{ $usageStats['assessment_count'] }}</strong> assessment(s).
                            @can('delete questions')
                                @if($usageStats['answer_count'] > 0)
                                    <br>Cannot be deleted while in use.
                                @endif
                            @endcan
                        </div>
                        @else
                        <div class="alert alert-warning mb-0 mt-3">
                            <i class="ti ti-alert-triangle me-2"></i>
                            This question hasn't been used in any assessments yet.
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @can('update questions')
                            <a href="{{ route('questions.edit', $question) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-2"></i>Edit Question
                            </a>
                            
                            <form action="{{ route('questions.toggle-active', $question) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $question->is_active ? 'warning' : 'success' }} w-100">
                                    <i class="ti ti-{{ $question->is_active ? 'eye-off' : 'eye' }} me-2"></i>
                                    {{ $question->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @endcan
                            
                            @can('delete questions')
                            <form action="{{ route('questions.destroy', $question) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" 
                                        {{ $usageStats['answer_count'] > 0 ? 'disabled' : '' }}>
                                    <i class="ti ti-trash me-2"></i>Delete Question
                                </button>
                            </form>
                            @if($usageStats['answer_count'] > 0)
                                <small class="text-muted">
                                    Cannot delete: question is being used in assessments
                                </small>
                            @endif
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Metadata Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Metadata</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Created At</label>
                            <div class="fw-bold">
                                {{ $question->created_at->format('d M Y') }}
                                <span class="text-muted small d-block">{{ $question->created_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Last Updated</label>
                            <div class="fw-bold">
                                {{ $question->updated_at->format('d M Y') }}
                                <span class="text-muted small d-block">{{ $question->updated_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted small">Database ID</label>
                            <div class="fw-bold">
                                <code>#{{ $question->id }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Info Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">GAMO Category</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="avatar avatar-xl bg-{{ 
                                $question->gamoObjective->category == 'EDM' ? 'purple' : 
                                ($question->gamoObjective->category == 'APO' ? 'blue' : 
                                ($question->gamoObjective->category == 'BAI' ? 'green' : 
                                ($question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                            }}-lt mx-auto mb-3">
                                <span class="fs-1 fw-bold">{{ $question->gamoObjective->category }}</span>
                            </div>
                            <div class="fw-bold">
                                {{ $question->gamoObjective->category }}
                            </div>
                            <div class="text-muted small">
                                @switch($question->gamoObjective->category)
                                    @case('EDM')
                                        Evaluate, Direct and Monitor
                                        @break
                                    @case('APO')
                                        Align, Plan and Organize
                                        @break
                                    @case('BAI')
                                        Build, Acquire and Implement
                                        @break
                                    @case('DSS')
                                        Deliver, Service and Support
                                        @break
                                    @case('MEA')
                                        Monitor, Evaluate and Assess
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
