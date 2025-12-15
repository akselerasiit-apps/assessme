@extends('layouts.app')

@section('title', 'GAMO Score Details')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">{{ $score->gamoObjective->code }} - Score Details</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('scoring.index', $assessment) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to Scores
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <!-- Score Summary Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Maturity Score Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">GAMO Objective</label>
                                <div>
                                    <span class="badge badge-outline text-{{ 
                                        $score->gamoObjective->category == 'EDM' ? 'purple' : 
                                        ($score->gamoObjective->category == 'APO' ? 'blue' : 
                                        ($score->gamoObjective->category == 'BAI' ? 'green' : 
                                        ($score->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                    }} me-2">
                                        {{ $score->gamoObjective->code }}
                                    </span>
                                    <span class="fw-bold">{{ $score->gamoObjective->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Category</label>
                                <div class="fw-bold">
                                    {{ $score->gamoObjective->category }} - 
                                    {{ 
                                        $score->gamoObjective->category == 'EDM' ? 'Evaluate, Direct and Monitor' : 
                                        ($score->gamoObjective->category == 'APO' ? 'Align, Plan and Organize' : 
                                        ($score->gamoObjective->category == 'BAI' ? 'Build, Acquire and Implement' : 
                                        ($score->gamoObjective->category == 'DSS' ? 'Deliver, Service and Support' : 'Monitor, Evaluate and Assess')))
                                    }}
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted">Current Maturity Level</label>
                                <div class="h1 mb-0">
                                    <span class="badge bg-{{ 
                                        $score->current_maturity_level == 0 ? 'secondary' : 
                                        ($score->current_maturity_level <= 1 ? 'danger' : 
                                        ($score->current_maturity_level <= 2 ? 'warning' : 
                                        ($score->current_maturity_level <= 3 ? 'info' : 
                                        ($score->current_maturity_level <= 4 ? 'primary' : 'success'))))
                                    }}">
                                        {{ number_format($score->current_maturity_level, 2) }}
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    @if($score->current_maturity_level == 0)
                                        Incomplete
                                    @elseif($score->current_maturity_level <= 1)
                                        Initial
                                    @elseif($score->current_maturity_level <= 2)
                                        Managed
                                    @elseif($score->current_maturity_level <= 3)
                                        Defined
                                    @elseif($score->current_maturity_level <= 4)
                                        Quantitatively Managed
                                    @else
                                        Optimizing
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted">Target Level</label>
                                <div class="h1 mb-0">
                                    <span class="badge badge-outline text-muted">
                                        {{ number_format($score->target_maturity_level, 2) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted">Gap to Target</label>
                                <div class="h1 mb-0">
                                    @php $gap = $score->getMaturityGap(); @endphp
                                    <span class="badge bg-{{ $gap > 0 ? 'warning' : 'success' }}-lt">
                                        {{ $gap > 0 ? '+' : '' }}{{ number_format($gap, 2) }}
                                    </span>
                                </div>
                                <div class="text-muted small mt-1">
                                    @if($score->isTargetMet())
                                        <i class="ti ti-check text-success"></i> Target Met
                                    @else
                                        <i class="ti ti-alert-triangle text-warning"></i> Below Target
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-muted">Capability Score</label>
                                <div class="h1 mb-0">
                                    {{ number_format($score->capability_score ?? 0, 2) }}
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Completion Status</label>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-fill me-2">
                                        <div class="progress-bar" style="width: {{ $score->percentage_complete }}%"></div>
                                    </div>
                                    <span class="fw-bold">{{ $score->percentage_complete }}%</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Assessment Status</label>
                                <div>
                                    <span class="badge bg-{{ 
                                        $score->status == 'completed' ? 'success' : 
                                        ($score->status == 'in_progress' ? 'primary' : 'secondary') 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $score->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Statistics Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Answer Statistics</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="subheader">Total Questions</div>
                                <div class="h2 mb-0">{{ $answerStats['total_questions'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="subheader">Avg Maturity</div>
                                <div class="h2 mb-0">{{ number_format($answerStats['avg_maturity'], 2) }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="subheader">With Evidence</div>
                                <div class="h2 mb-0">{{ $answerStats['with_evidence'] }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="subheader">Avg Capability</div>
                                <div class="h2 mb-0">{{ number_format($answerStats['avg_capability'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answers List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Individual Answers</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th class="text-center">Maturity</th>
                                    <th class="text-center">Evidence</th>
                                    <th class="text-center">Answered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($answers as $answer)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $answer->question->code }}</div>
                                        <div class="text-muted small">{{ Str::limit($answer->question->question_text, 60) }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ 
                                            $answer->maturity_level == 0 ? 'secondary' : 
                                            ($answer->maturity_level <= 1 ? 'danger' : 
                                            ($answer->maturity_level <= 2 ? 'warning' : 
                                            ($answer->maturity_level <= 3 ? 'info' : 
                                            ($answer->maturity_level <= 4 ? 'primary' : 'success'))))
                                        }}">
                                            {{ $answer->maturity_level }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($answer->evidence_file)
                                            <span class="badge bg-success">
                                                <i class="ti ti-file-check"></i> Yes
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="text-muted small">{{ $answer->answeredBy->name }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No answers recorded</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Actions Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('assessments.answer', $assessment) }}#gamo-{{ $score->gamo_objective_id }}" 
                               class="btn btn-primary">
                                <i class="ti ti-pencil me-2"></i>Edit Answers
                            </a>
                            <a href="{{ route('evidence.index', $assessment) }}" class="btn btn-ghost-primary">
                                <i class="ti ti-file-text me-2"></i>View Evidence
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GAMO Description Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Objective Description</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $score->gamoObjective->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
