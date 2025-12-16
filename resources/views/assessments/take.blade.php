@extends('layouts.app')

@section('title', 'Assessment - Answer Questions')

@section('content')
<div class="page-header d-print-none sticky-top bg-white">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">{{ $assessment->code }}</div>
                <h2 class="page-title">{{ $assessment->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div><i class="ti ti-circle-check alert-icon me-2"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <!-- Progress Sidebar -->
            <div class="col-lg-3">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-progress-check me-2"></i>Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Stats -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Answered</span>
                                <span class="badge bg-success">{{ $answeredCount }}/{{ $totalQuestions }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ ($answeredCount / $totalQuestions) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Overall Progress -->
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Overall Progress</p>
                            <div class="text-center">
                                <div style="font-size: 2rem; font-weight: bold; color: #0d6efd;">
                                    {{ round(($answeredCount / $totalQuestions) * 100) }}%
                                </div>
                            </div>
                        </div>

                        <!-- Question Navigation -->
                        <div class="mb-3">
                            <p class="text-muted small mb-2">Questions</p>
                            <div style="max-height: 400px; overflow-y: auto;">
                                @foreach($questions->getCollection() as $q)
                                    @php
                                        $isAnswered = in_array($q->id, $answeredQuestions);
                                        $isCurrent = $currentQuestion->id === $q->id;
                                    @endphp
                                    <a href="{{ route('assessments.take', ['assessment' => $assessment, 'page' => $loop->iteration]) }}" 
                                       class="list-group-item list-group-item-action {{ $isCurrent ? 'active' : '' }}" 
                                       style="border-left: 3px solid {{ $isAnswered ? '#20c997' : '#e9ecef' }}">
                                        <div class="d-flex align-items-center">
                                            @if($isAnswered)
                                                <i class="ti ti-circle-check text-success me-2"></i>
                                            @else
                                                <i class="ti ti-circle text-muted me-2"></i>
                                            @endif
                                            <span class="small" title="{{ Str::limit($q->question_text, 60) }}">
                                                {{ Str::limit($q->question_text, 45, '...') }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('assessments.review', $assessment) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-eye me-2"></i>Review All Answers
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#saveModal">
                                <i class="ti ti-bookmark me-2"></i>Save Draft
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Question Content -->
            <div class="col-lg-9">
                @if($currentQuestion)
                <form id="answerForm" method="POST" action="{{ route('assessments.answer', [$assessment, $currentQuestion]) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="card mb-3">
                        <div class="card-header bg-primary-lt">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="card-pretitle">
                                        <span class="badge badge-outline {{ 
                                            $currentQuestion->gamoObjective->category == 'EDM' ? 'text-purple' : 
                                            ($currentQuestion->gamoObjective->category == 'APO' ? 'text-blue' : 
                                            ($currentQuestion->gamoObjective->category == 'BAI' ? 'text-green' : 
                                            ($currentQuestion->gamoObjective->category == 'DSS' ? 'text-orange' : 'text-pink'))) 
                                        }}">
                                            {{ $currentQuestion->gamoObjective->code }}
                                        </span>
                                        <span class="ms-2 text-muted">Level {{ $currentQuestion->maturity_level }}</span>
                                    </div>
                                    <h3 class="card-title mt-2">{{ $currentQuestion->question_text }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="badge bg-info">
                                        {{ $questions->currentPage() }} / {{ $totalQuestions }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Guidance -->
                            @if($currentQuestion->guidance)
                            <div class="alert alert-info mb-3">
                                <div class="d-flex">
                                    <div><i class="ti ti-info-circle alert-icon me-2"></i></div>
                                    <div>
                                        <strong>Guidance:</strong> {{ $currentQuestion->guidance }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Evidence Requirement -->
                            @if($currentQuestion->evidence_requirement)
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex">
                                    <div><i class="ti ti-alert-circle alert-icon me-2"></i></div>
                                    <div>
                                        <strong>Evidence Required:</strong> {{ $currentQuestion->evidence_requirement }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Answer Input based on Question Type -->
                            <div class="mb-3">
                                <label class="form-label">Your Answer 
                                    @if($currentQuestion->required)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @switch($currentQuestion->question_type)
                                    @case('text')
                                        <textarea name="answer_text" class="form-control" rows="5" placeholder="Enter your detailed answer..." required>{{ $currentAnswer?->answer_text ?? '' }}</textarea>
                                        @break

                                    @case('yes_no')
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer_text" value="Yes" id="answerYes" {{ $currentAnswer?->answer_text === 'Yes' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="answerYes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answer_text" value="No" id="answerNo" {{ $currentAnswer?->answer_text === 'No' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="answerNo">No</label>
                                        </div>
                                        @break

                                    @case('rating')
                                        <div class="d-flex gap-2">
                                            @for($i = 1; $i <= 5; $i++)
                                            <label class="form-check form-check-single" style="flex: 1;">
                                                <input class="form-check-input" type="radio" name="maturity_level" value="{{ $i }}" 
                                                       {{ $currentAnswer?->maturity_level == $i ? 'checked' : '' }} required>
                                                <span class="form-check-label" style="width: 100%;">
                                                    <span class="badge w-100 py-2" style="background-color: #0d6efd;">
                                                        Level {{ $i }}
                                                    </span>
                                                </span>
                                            </label>
                                            @endfor
                                        </div>
                                        @break

                                    @case('multiple_choice')
                                        <textarea name="answer_json" class="form-control" rows="3" placeholder="Select or list applicable options..." required>{{ $currentAnswer?->answer_json ? json_encode($currentAnswer->answer_json) : '' }}</textarea>
                                        @break

                                    @case('evidence')
                                        <div class="card border-dashed">
                                            <div class="card-body">
                                                <div id="evidence-drop" class="text-center py-5" style="border: 2px dashed #dee2e6; border-radius: 0.25rem; cursor: pointer;">
                                                    <i class="ti ti-cloud-upload" style="font-size: 2.5rem; color: #999;"></i>
                                                    <p class="mt-2 mb-0 text-muted">Drag files here or click to upload</p>
                                                    <small class="text-muted">Max 10MB</small>
                                                </div>
                                                <input type="file" name="evidence_file" id="evidenceFile" class="d-none" accept="*" required>
                                            </div>
                                        </div>
                                        @if($currentAnswer?->evidence_file)
                                        <div class="mt-2 alert alert-success">
                                            <i class="ti ti-file me-2"></i>File uploaded: {{ basename($currentAnswer->evidence_file) }}
                                        </div>
                                        @endif
                                        @break
                                @endswitch
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label class="form-label">Notes <span class="text-muted">(optional)</span></label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Add any notes or flag this question...">{{ $currentAnswer?->notes ?? '' }}</textarea>
                                <small class="form-hint">Use this field to add comments or bookmark this question for later review</small>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($questions->onFirstPage())
                                        <button type="button" class="btn btn-outline-secondary" disabled>
                                            <i class="ti ti-arrow-left me-2"></i>Previous
                                        </button>
                                    @else
                                        <a href="{{ $questions->previousPageUrl() }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-arrow-left me-2"></i>Previous
                                        </a>
                                    @endif
                                </div>

                                <div class="btn-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-2"></i>Save & Next
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#saveDraftModal">
                                        <i class="ti ti-bookmark me-2"></i>Save Draft
                                    </button>
                                </div>

                                <div>
                                    @if($questions->hasMorePages())
                                        <a href="{{ $questions->nextPageUrl() }}" class="btn btn-outline-secondary">
                                            Next<i class="ti ti-arrow-right ms-2"></i>
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                            <i class="ti ti-check-all me-2"></i>Complete Assessment
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-help-circle text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3">No Questions Available</h4>
                        <p class="text-muted">No active questions found for the selected GAMO objectives.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Save Draft Modal -->
<div class="modal fade" id="saveDraftModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save as Draft</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Your answers will be saved as a draft and you can continue later.</p>
                <div id="draftStatus"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSaveDraft">
                    <i class="ti ti-bookmark me-2"></i>Save Draft
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Assessment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You have answered <strong>{{ $answeredCount }}</strong> out of <strong>{{ $totalQuestions }}</strong> questions.</p>
                <p class="text-muted">Please review your answers before submitting.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Continue Answering</button>
                <a href="{{ route('assessments.review', $assessment) }}" class="btn btn-primary">
                    <i class="ti ti-eye me-2"></i>Review Answers
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Evidence file drag and drop
document.addEventListener('DOMContentLoaded', function() {
    const evidenceDrop = document.getElementById('evidence-drop');
    const evidenceFile = document.getElementById('evidenceFile');

    if (evidenceDrop) {
        evidenceDrop.addEventListener('click', () => evidenceFile.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            evidenceDrop.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            evidenceDrop.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            evidenceDrop.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            evidenceDrop.style.backgroundColor = '#f8f9fa';
        }

        function unhighlight(e) {
            evidenceDrop.style.backgroundColor = 'transparent';
        }

        evidenceDrop.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            evidenceFile.files = files;
        }
    }

    // Save Draft functionality
    const confirmSaveDraft = document.getElementById('confirmSaveDraft');
    if (confirmSaveDraft) {
        confirmSaveDraft.addEventListener('click', function() {
            const answerText = document.querySelector('textarea[name="answer_text"]')?.value || '';
            const notes = document.querySelector('textarea[name="notes"]')?.value || '';
            
            fetch('{{ route("assessments.save-draft", $assessment) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    question_id: {{ $currentQuestion?->id ?? 0 }},
                    answer_text: answerText,
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('draftStatus').innerHTML = '<div class="alert alert-success">✓ Draft saved successfully</div>';
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('saveDraftModal')).hide();
                    }, 1500);
                }
            });
        });
    }

    // Auto-save every 30 seconds
    setInterval(function() {
        const answerText = document.querySelector('textarea[name="answer_text"]')?.value || '';
        const notes = document.querySelector('textarea[name="notes"]')?.value || '';
        
        if (answerText || notes) {
            fetch('{{ route("assessments.auto-save", [$assessment, $currentQuestion?->id ?? 0]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    answer_text: answerText,
                    notes: notes
                })
            }).catch(err => console.log('Auto-save:', err));
        }
    }, 30000);
});
</script>
@endpush
@endsection
