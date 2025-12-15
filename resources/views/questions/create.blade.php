@extends('layouts.app')

@section('title', 'Create Question')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Question Bank</div>
                <h2 class="page-title">Create New Question</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('questions.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('questions.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Question Details</h3>
                        </div>
                        <div class="card-body">
                            <!-- Code -->
                            <div class="mb-3">
                                <label class="form-label required">Question Code</label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="e.g., EDM01-L1-001" value="{{ old('code') }}" required>
                                <small class="form-hint">Unique identifier for this question</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- GAMO Objective -->
                            <div class="mb-3">
                                <label class="form-label required">GAMO Objective</label>
                                <select name="gamo_objective_id" class="form-select @error('gamo_objective_id') is-invalid @enderror" required>
                                    <option value="">Select GAMO Objective...</option>
                                    @foreach($gamoObjectives as $gamo)
                                        <option value="{{ $gamo->id }}" {{ old('gamo_objective_id') == $gamo->id ? 'selected' : '' }}>
                                            [{{ $gamo->category }}] {{ $gamo->code }} - {{ $gamo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gamo_objective_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Question Text -->
                            <div class="mb-3">
                                <label class="form-label required">Question Text</label>
                                <textarea name="question_text" rows="4" 
                                          class="form-control @error('question_text') is-invalid @enderror" 
                                          placeholder="Enter the question text..." required>{{ old('question_text') }}</textarea>
                                @error('question_text')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Guidance -->
                            <div class="mb-3">
                                <label class="form-label">Guidance <span class="form-label-description">Optional</span></label>
                                <textarea name="guidance" rows="3" 
                                          class="form-control @error('guidance') is-invalid @enderror" 
                                          placeholder="Additional guidance or hints for answering this question...">{{ old('guidance') }}</textarea>
                                <small class="form-hint">Help text to guide assessors</small>
                                @error('guidance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Evidence Requirement -->
                            <div class="mb-3">
                                <label class="form-label">Evidence Requirement <span class="form-label-description">Optional</span></label>
                                <textarea name="evidence_requirement" rows="3" 
                                          class="form-control @error('evidence_requirement') is-invalid @enderror" 
                                          placeholder="What evidence is needed to support this answer?">{{ old('evidence_requirement') }}</textarea>
                                <small class="form-hint">Specify required documentation or evidence</small>
                                @error('evidence_requirement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Configuration Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Configuration</h3>
                        </div>
                        <div class="card-body">
                            <!-- Question Type -->
                            <div class="mb-3">
                                <label class="form-label required">Question Type</label>
                                <select name="question_type" class="form-select @error('question_type') is-invalid @enderror" required>
                                    <option value="">Select type...</option>
                                    <option value="text" {{ old('question_type') == 'text' ? 'selected' : '' }}>Text (Open-ended)</option>
                                    <option value="rating" {{ old('question_type') == 'rating' ? 'selected' : '' }}>Rating (Scale)</option>
                                    <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="yes_no" {{ old('question_type') == 'yes_no' ? 'selected' : '' }}>Yes/No</option>
                                    <option value="evidence" {{ old('question_type') == 'evidence' ? 'selected' : '' }}>Evidence Upload</option>
                                </select>
                                @error('question_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Maturity Level -->
                            <div class="mb-3">
                                <label class="form-label required">Maturity Level</label>
                                <select name="maturity_level" class="form-select @error('maturity_level') is-invalid @enderror" required>
                                    <option value="">Select level...</option>
                                    @foreach($maturityLevels as $level)
                                        <option value="{{ $level }}" {{ old('maturity_level') == $level ? 'selected' : '' }}>
                                            Level {{ $level }} - {{ ['Initial', 'Managed', 'Defined', 'Quantitatively Managed', 'Optimizing'][$level - 1] ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('maturity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Question Order -->
                            <div class="mb-3">
                                <label class="form-label">Question Order <span class="form-label-description">Optional</span></label>
                                <input type="number" name="question_order" min="1" 
                                       class="form-control @error('question_order') is-invalid @enderror" 
                                       value="{{ old('question_order') }}" placeholder="e.g., 1">
                                <small class="form-hint">Display order within maturity level</small>
                                @error('question_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Required Checkbox -->
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="required" value="1" 
                                           {{ old('required', true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Required Question</span>
                                </label>
                                <small class="form-hint d-block">Must be answered in assessments</small>
                            </div>

                            <!-- Active Checkbox -->
                            <div class="mb-0">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Active</span>
                                </label>
                                <small class="form-hint d-block">Include in new assessments</small>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-2"></i>Create Question
                                </button>
                                <a href="{{ route('questions.index') }}" class="btn btn-ghost-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
