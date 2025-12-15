@extends('layouts.app')

@section('title', 'Create Banding Request')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment: {{ $assessment->code }}</div>
                <h2 class="page-title">Create Banding / Appeal Request</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('banding.index', $assessment) }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                    Back to Bandings
                </a>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Form Card -->
        <div class="col-md-8">
            <form action="{{ route('banding.store', $assessment) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Banding Request Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <h4 class="alert-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
                                About Banding Process
                            </h4>
                            <div class="text-muted">
                                Banding is a formal process to appeal assessment scores. You can request a review of maturity levels 
                                by providing additional evidence, revised answers, or clarifications. The request will be reviewed by administrators.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Select GAMO Objective</label>
                            <select name="gamo_objective_id" class="form-select @error('gamo_objective_id') is-invalid @enderror" required>
                                <option value="">Choose GAMO objective to appeal...</option>
                                @foreach($gamoScores as $score)
                                <option value="{{ $score->gamo_objective_id }}" {{ old('gamo_objective_id') == $score->gamo_objective_id ? 'selected' : '' }}>
                                    {{ $score->gamoObjective->gamo_code }} - {{ $score->gamoObjective->objective_name }}
                                    (Current: {{ number_format($score->current_maturity_level, 1) }}, Target: {{ number_format($score->target_maturity_level, 1) }})
                                    @if(isset($existingBandings[$score->gamo_objective_id]))
                                        - Round {{ $existingBandings[$score->gamo_objective_id] + 1 }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('gamo_objective_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Banding Reason (Short Summary)</label>
                            <input type="text" name="banding_reason" class="form-control @error('banding_reason') is-invalid @enderror" 
                                   placeholder="e.g., Additional evidence found, Scoring criteria misunderstood..." 
                                   value="{{ old('banding_reason') }}" required maxlength="255">
                            @error('banding_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Brief summary of why you're requesting this banding (max 255 characters).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Detailed Description</label>
                            <textarea name="banding_description" class="form-control @error('banding_description') is-invalid @enderror" 
                                      rows="6" placeholder="Provide detailed explanation for this banding request..." required>{{ old('banding_description') }}</textarea>
                            @error('banding_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Minimum 50 characters. Explain in detail why the maturity level should be revised.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Proposed New Maturity Level</label>
                            <select name="new_maturity_level" class="form-select @error('new_maturity_level') is-invalid @enderror" required>
                                <option value="">Select maturity level...</option>
                                <option value="0" {{ old('new_maturity_level') == '0' ? 'selected' : '' }}>0 - Incomplete</option>
                                <option value="1" {{ old('new_maturity_level') == '1' ? 'selected' : '' }}>1 - Initial (Ad hoc)</option>
                                <option value="2" {{ old('new_maturity_level') == '2' ? 'selected' : '' }}>2 - Managed</option>
                                <option value="3" {{ old('new_maturity_level') == '3' ? 'selected' : '' }}>3 - Defined</option>
                                <option value="4" {{ old('new_maturity_level') == '4' ? 'selected' : '' }}>4 - Quantitatively Managed</option>
                                <option value="5" {{ old('new_maturity_level') == '5' ? 'selected' : '' }}>5 - Optimizing</option>
                            </select>
                            @error('new_maturity_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Revised Answers (Optional)</label>
                            <textarea name="revised_answers" class="form-control @error('revised_answers') is-invalid @enderror" 
                                      rows="4" placeholder="If you have revised answers to questions, provide them here...">{{ old('revised_answers') }}</textarea>
                            @error('revised_answers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Provide corrected or additional answers if applicable.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Additional Evidence File (Optional)</label>
                            <input type="file" name="additional_evidence_files" class="form-control @error('additional_evidence_files') is-invalid @enderror" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                            @error('additional_evidence_files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Upload supporting documents (PDF, DOC, XLS, Images, ZIP - max 10MB).</small>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('banding.index', $assessment) }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                            Create Banding (Draft)
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Guidelines</h3>
                </div>
                <div class="card-body">
                    <h4>When to Submit Banding:</h4>
                    <ul class="mb-3">
                        <li>You have new evidence not previously considered</li>
                        <li>Assessment criteria were misunderstood</li>
                        <li>Additional context affects the score</li>
                        <li>Scoring appears inconsistent</li>
                    </ul>

                    <h4>Required Information:</h4>
                    <ul class="mb-3">
                        <li>Clear reason for the appeal</li>
                        <li>Detailed explanation (min 50 chars)</li>
                        <li>Proposed new maturity level</li>
                        <li>Supporting evidence (recommended)</li>
                    </ul>

                    <h4>Process:</h4>
                    <ol class="mb-0">
                        <li>Create banding as <strong>draft</strong></li>
                        <li>Review your request</li>
                        <li><strong>Submit</strong> for approval</li>
                        <li>Admin/Super Admin reviews</li>
                        <li>Approved or rejected with notes</li>
                    </ol>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Assessment Info</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Code:</strong> {{ $assessment->code }}
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $assessment->status === 'approved' ? 'success' : 'cyan' }}">
                            {{ ucfirst($assessment->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Overall Maturity:</strong> 
                        @if($assessment->overall_maturity_level)
                            {{ number_format($assessment->overall_maturity_level, 2) }}
                        @else
                            <span class="text-muted">Not calculated</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
