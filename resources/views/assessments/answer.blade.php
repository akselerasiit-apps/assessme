@extends('layouts.app')

@section('title', 'Answer Questions')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Assessment</div>
            <h2 class="page-title">{{ $assessment->title }}</h2>
            <div class="text-muted mt-1">Answer questions for selected GAMO objectives</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Back to Assessment
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="row g-3">
    <!-- Left: GAMO List -->
    <div class="col-lg-3">
        <div class="card sticky-top" style="top: 1rem;">
            <div class="card-header">
                <h3 class="card-title">GAMO Objectives</h3>
            </div>
            <div class="list-group list-group-flush" id="gamo-nav">
                @foreach($assessment->gamoObjectives->groupBy('category') as $category => $objectives)
                <div class="list-group-item">
                    <div class="fw-bold text-muted mb-2">{{ $category }}</div>
                    @foreach($objectives as $gamo)
                    <a href="#gamo-{{ $gamo->id }}" class="d-block text-reset py-2 gamo-nav-link" data-gamo="{{ $gamo->id }}">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-blue-lt me-2">{{ $gamo->code }}</span>
                            <span class="small">{{ $gamo->name }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right: Questions -->
    <div class="col-lg-9">
        <form action="{{ route('assessments.submit-answer', $assessment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @foreach($assessment->gamoObjectives as $gamo)
            <div class="card mb-3" id="gamo-{{ $gamo->id }}">
                <div class="card-header">
                    <h3 class="card-title">
                        <span class="badge bg-blue me-2">{{ $gamo->code }}</span>
                        {{ $gamo->name }}
                    </h3>
                    <div class="card-subtitle">{{ $gamo->description }}</div>
                </div>
                <div class="card-body">
                    @php
                        $questions = $gamo->questions()->where('is_active', true)->get();
                    @endphp
                    
                    @if($questions->count() > 0)
                        @foreach($questions as $index => $question)
                        @php
                            $existingAnswer = $assessment->answers()
                                ->where('question_id', $question->id)
                                ->first();
                        @endphp
                        
                        <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Question {{ $index + 1 }}
                                    @if($question->is_required)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="text-muted">{{ $question->question_text }}</div>
                            </div>
                            
                            <input type="hidden" name="answers[{{ $question->id }}][question_id]" value="{{ $question->id }}">
                            <input type="hidden" name="answers[{{ $question->id }}][gamo_objective_id]" value="{{ $gamo->id }}">
                            
                            <!-- Answer Text -->
                            <div class="mb-3">
                                <label class="form-label">Your Answer</label>
                                <textarea 
                                    name="answers[{{ $question->id }}][answer_text]" 
                                    rows="3" 
                                    class="form-control @error('answers.'.$question->id.'.answer_text') is-invalid @enderror"
                                    placeholder="Enter your answer here..."
                                    {{ $question->is_required ? 'required' : '' }}
                                >{{ old('answers.'.$question->id.'.answer_text', $existingAnswer?->answer_text) }}</textarea>
                                @error('answers.'.$question->id.'.answer_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Maturity Level -->
                            <div class="mb-3">
                                <label class="form-label">Maturity Level (0-5)</label>
                                <select 
                                    name="answers[{{ $question->id }}][maturity_level]" 
                                    class="form-select @error('answers.'.$question->id.'.maturity_level') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Select Level</option>
                                    @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('answers.'.$question->id.'.maturity_level', $existingAnswer?->maturity_level) == $i ? 'selected' : '' }}>
                                        Level {{ $i }} - 
                                        @if($i == 0) Not Achieved
                                        @elseif($i == 1) Initial/Ad hoc
                                        @elseif($i == 2) Repeatable
                                        @elseif($i == 3) Defined
                                        @elseif($i == 4) Managed
                                        @elseif($i == 5) Optimized
                                        @endif
                                    </option>
                                    @endfor
                                </select>
                                @error('answers.'.$question->id.'.maturity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Evidence Upload -->
                            <div class="mb-3">
                                <label class="form-label">Evidence (Optional)</label>
                                @if($existingAnswer && $existingAnswer->evidence_file)
                                <div class="alert alert-info mb-2">
                                    <i class="ti ti-file me-1"></i>
                                    Current evidence: <strong>{{ basename($existingAnswer->evidence_file) }}</strong>
                                </div>
                                @endif
                                <input 
                                    type="file" 
                                    name="answers[{{ $question->id }}][evidence]" 
                                    class="form-control @error('answers.'.$question->id.'.evidence') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                >
                                <small class="form-hint">Accepted: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max: 10MB)</small>
                                @error('answers.'.$question->id.'.evidence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Notes -->
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea 
                                    name="answers[{{ $question->id }}][notes]" 
                                    rows="2" 
                                    class="form-control"
                                    placeholder="Additional notes or context..."
                                >{{ old('answers.'.$question->id.'.notes', $existingAnswer?->notes) }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-alert-circle"></i>
                            </div>
                            <p class="empty-title">No questions available</p>
                            <p class="empty-subtitle text-muted">
                                Questions for this GAMO objective are not yet configured.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
            
            <!-- Submit Button -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Ready to submit?</h3>
                            <div class="text-muted">Your answers will be saved and you can edit them later.</div>
                        </div>
                        <div>
                            <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary me-2">
                                <i class="ti ti-device-floppy me-1"></i>
                                Save Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary">
                                <i class="ti ti-send me-1"></i>
                                Submit Answers
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Smooth scroll and active nav
document.querySelectorAll('.gamo-nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Update active state
            document.querySelectorAll('.gamo-nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        }
    });
});

// Highlight current section on scroll
let sections = document.querySelectorAll('[id^="gamo-"]');
let navLinks = document.querySelectorAll('.gamo-nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= (sectionTop - 100)) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});
</script>

<style>
.gamo-nav-link {
    border-left: 2px solid transparent;
    transition: all 0.2s;
}

.gamo-nav-link:hover {
    background-color: rgba(32, 107, 196, 0.05);
    border-left-color: #206bc4;
}

.gamo-nav-link.active {
    background-color: rgba(32, 107, 196, 0.1);
    border-left-color: #206bc4;
    font-weight: 600;
}
</style>
@endpush
