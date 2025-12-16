@extends('layouts.app')

@section('title', 'Assessment Review')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $assessment->code }}</div>
                <h2 class="page-title">Review Answers</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-pencil me-2"></i>Continue Answering
                    </a>
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-primary">
                        <i class="ti ti-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Statistics Cards -->
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Total Questions</div>
                                <div class="h3 mb-0">{{ $statistics['total_questions'] }}</div>
                            </div>
                            <div class="bg-primary-lt p-3 rounded">
                                <i class="ti ti-help-circle text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Answered</div>
                                <div class="h3 mb-0">{{ $statistics['answered_questions'] }}</div>
                            </div>
                            <div class="bg-success-lt p-3 rounded">
                                <i class="ti ti-circle-check text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Unanswered</div>
                                <div class="h3 mb-0">{{ $statistics['unanswered_questions'] }}</div>
                            </div>
                            <div class="bg-warning-lt p-3 rounded">
                                <i class="ti ti-circle-x text-warning" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">With Evidence</div>
                                <div class="h3 mb-0">{{ $statistics['questions_with_evidence'] }}</div>
                            </div>
                            <div class="bg-info-lt p-3 rounded">
                                <i class="ti ti-file-check text-info" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold">Overall Progress</span>
                    <span class="fw-bold text-primary">
                        {{ round(($statistics['answered_questions'] / $statistics['total_questions']) * 100) }}%
                    </span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ ($statistics['answered_questions'] / $statistics['total_questions']) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Answers by GAMO Objective -->
        @foreach($questionsGroupedByGamo as $gamoId => $groupedAnswers)
            @php
                $gamoObjective = $groupedAnswers->first()->gamoObjective;
                $categoryClass = match($gamoObjective->category) {
                    'EDM' => 'text-purple',
                    'APO' => 'text-blue',
                    'BAI' => 'text-green',
                    'DSS' => 'text-orange',
                    'MEA' => 'text-pink',
                    default => 'text-primary'
                };
            @endphp

            <div class="card mb-3">
                <div class="card-header bg-light-lt">
                    <h5 class="card-title mb-0">
                        <span class="badge badge-outline {{ $categoryClass }}">
                            {{ $gamoObjective->code }}
                        </span>
                        <span class="ms-2">{{ Str::limit($gamoObjective->name, 60) }}</span>
                        <span class="badge bg-secondary ms-auto">
                            {{ $groupedAnswers->whereNotNull('answered_at')->count() }}/{{ $groupedAnswers->count() }}
                        </span>
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">Level</th>
                                <th>Question</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedAnswers as $answer)
                                <tr>
                                    <td>
                                        <span class="badge bg-blue">L{{ $answer->question->maturity_level }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" title="{{ $answer->question->question_text }}">
                                            {{ Str::limit($answer->question->question_text, 80, '...') }}
                                        </div>
                                        @if($answer->notes)
                                            <small class="text-muted d-block mt-1">
                                                <i class="ti ti-bookmark-filled text-warning me-1"></i>{{ Str::limit($answer->notes, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($answer->answered_at)
                                            <span class="badge bg-success">
                                                <i class="ti ti-circle-check me-1"></i>Answered
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="ti ti-circle me-1"></i>Pending
                                            </span>
                                        @endif
                                        @if($answer->evidence_file)
                                            <span class="badge bg-info ms-1" title="Evidence attached">
                                                <i class="ti ti-file-check"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <button type="button" class="btn btn-sm btn-icon btn-ghost-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#answerModal{{ $answer->id }}"
                                                    title="View full answer">
                                                <i class="ti ti-eye"></i>
                                            </button>
                                            <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-sm btn-icon btn-ghost-secondary" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Answer Details Modal -->
                                <div class="modal fade" id="answerModal{{ $answer->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Answer Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Question</label>
                                                    <p class="form-control-plaintext">{{ $answer->question->question_text }}</p>
                                                </div>

                                                @if($answer->answer_text)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Answer</label>
                                                    <p class="form-control-plaintext text-break" style="white-space: pre-wrap;">{{ $answer->answer_text }}</p>
                                                </div>
                                                @endif

                                                @if($answer->maturity_level)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Maturity Level</label>
                                                    <p class="form-control-plaintext">
                                                        <span class="badge bg-blue">Level {{ $answer->maturity_level }}</span>
                                                    </p>
                                                </div>
                                                @endif

                                                @if($answer->notes)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Notes</label>
                                                    <p class="form-control-plaintext text-break" style="white-space: pre-wrap;">{{ $answer->notes }}</p>
                                                </div>
                                                @endif

                                                @if($answer->evidence_file)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Evidence File</label>
                                                    <p class="form-control-plaintext">
                                                        <i class="ti ti-file me-2"></i>{{ basename($answer->evidence_file) }}
                                                    </p>
                                                </div>
                                                @endif

                                                @if($answer->answered_at)
                                                <div class="text-muted small">
                                                    <i class="ti ti-clock me-1"></i>
                                                    Answered on {{ $answer->answered_at->format('M d, Y \a\t g:i A') }}
                                                    @if($answer->answerer)
                                                        by {{ $answer->answerer->name }}
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if($answers->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ti ti-help-circle text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Answers Yet</h4>
                <p class="text-muted">Start answering questions to see them here.</p>
                <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-primary">
                    <i class="ti ti-pencil me-2"></i>Start Answering
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
