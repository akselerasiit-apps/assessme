@extends('layouts.app')

@section('title', 'Edit Assessment')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Assessment</div>
            <h2 class="page-title">Edit Assessment</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                <i class="ti ti-x me-1"></i>
                Cancel
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <form action="{{ route('assessments.update', $assessment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Basic Information</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required">Assessment Title</label>
                        <input 
                            type="text" 
                            name="title" 
                            class="form-control @error('title') is-invalid @enderror" 
                            value="{{ old('title', $assessment->title) }}"
                            required
                        >
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            class="form-control @error('description') is-invalid @enderror"
                        >{{ old('description', $assessment->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">Company</label>
                        <select 
                            name="company_id" 
                            class="form-select @error('company_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $assessment->company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Assessment Type</label>
                            <select 
                                name="assessment_type" 
                                class="form-select @error('assessment_type') is-invalid @enderror"
                                required
                            >
                                <option value="">Select Type</option>
                                <option value="initial" {{ old('assessment_type', $assessment->assessment_type) == 'initial' ? 'selected' : '' }}>Initial Assessment</option>
                                <option value="periodic" {{ old('assessment_type', $assessment->assessment_type) == 'periodic' ? 'selected' : '' }}>Periodic Review</option>
                                <option value="specific" {{ old('assessment_type', $assessment->assessment_type) == 'specific' ? 'selected' : '' }}>Specific Focus</option>
                            </select>
                            @error('assessment_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Scope Type</label>
                            <select 
                                name="scope_type" 
                                class="form-select @error('scope_type') is-invalid @enderror"
                                required
                            >
                                <option value="">Select Scope</option>
                                <option value="full" {{ old('scope_type', $assessment->scope_type) == 'full' ? 'selected' : '' }}>Full Scope</option>
                                <option value="tailored" {{ old('scope_type', $assessment->scope_type) == 'tailored' ? 'selected' : '' }}>Tailored</option>
                            </select>
                            @error('scope_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Period Start</label>
                            <input 
                                type="date" 
                                name="assessment_period_start" 
                                class="form-control @error('assessment_period_start') is-invalid @enderror" 
                                value="{{ old('assessment_period_start', $assessment->assessment_period_start?->format('Y-m-d')) }}"
                                required
                            >
                            @error('assessment_period_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required">Period End</label>
                            <input 
                                type="date" 
                                name="assessment_period_end" 
                                class="form-control @error('assessment_period_end') is-invalid @enderror" 
                                value="{{ old('assessment_period_end', $assessment->assessment_period_end?->format('Y-m-d')) }}"
                                required
                            >
                            @error('assessment_period_end')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Design Factors -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Design Factors</h3>
                    <div class="card-subtitle">Select applicable design factors for this assessment</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($designFactors as $factor)
                        <div class="col-md-6">
                            <label class="form-check">
                                <input 
                                    type="checkbox" 
                                    name="design_factors[]" 
                                    value="{{ $factor->id }}"
                                    class="form-check-input"
                                    {{ in_array($factor->id, old('design_factors', $assessment->designFactors->pluck('id')->toArray())) ? 'checked' : '' }}
                                >
                                <span class="form-check-label">
                                    <strong>{{ $factor->code }}:</strong> {{ $factor->name }}
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- GAMO Objectives -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">GAMO Objectives</h3>
                    <div class="card-subtitle">Select GAMO objectives to assess</div>
                </div>
                <div class="card-body">
                    @foreach($gamoObjectives->groupBy('category') as $category => $objectives)
                    <div class="mb-4">
                        <h4 class="mb-3">{{ $category }}</h4>
                        <div class="row g-3">
                            @foreach($objectives as $gamo)
                            <div class="col-md-6">
                                <label class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="gamo_objectives[]" 
                                        value="{{ $gamo->id }}"
                                        class="form-check-input"
                                        {{ in_array($gamo->id, old('gamo_objectives', $assessment->gamoObjectives->pluck('id')->toArray())) ? 'checked' : '' }}
                                    >
                                    <span class="form-check-label">
                                        <span class="badge bg-blue-lt me-1">{{ $gamo->code }}</span>
                                        {{ $gamo->name }}
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Submit -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i>
                            Update Assessment
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
