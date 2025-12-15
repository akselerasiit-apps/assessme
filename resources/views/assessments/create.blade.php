@extends('layouts.app')

@section('title', 'Create Assessment')

@section('page-header')
    <h2 class="page-title">Create New Assessment</h2>
    <div class="page-subtitle">Follow the steps to create a COBIT 2019 assessment</div>
@endsection

@section('content')
<form action="{{ route('assessments.store') }}" method="POST" id="assessment-wizard-form">
    @csrf
    
    <!-- Wizard Progress -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="steps steps-counter">
                <a href="#step1" class="step-item active" data-step="1">
                    <span class="h4">1</span>
                    Basic Information
                </a>
                <a href="#step2" class="step-item" data-step="2">
                    <span class="h4">2</span>
                    Design Factors
                </a>
                <a href="#step3" class="step-item" data-step="3">
                    <span class="h4">3</span>
                    GAMO Objectives
                </a>
                <a href="#step4" class="step-item" data-step="4">
                    <span class="h4">4</span>
                    Review & Create
                </a>
            </div>
        </div>
    </div>

    <!-- Step 1: Basic Information -->
    <div class="wizard-step" id="step1" style="display: block;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Step 1: Basic Information</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required">Assessment Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Enter assessment title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Descriptive name for this assessment</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Company</label>
                        <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" 
                                    {{ (old('company_id') == $company->id || request('company_id') == $company->id) ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(request('company_id'))
                            <small class="form-hint text-success">
                                <i class="ti ti-check me-1"></i>Pre-selected from company list
                            </small>
                        @endif
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Enter assessment description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">Brief description of assessment objectives and scope</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Assessment Type</label>
                        <select name="assessment_type" class="form-select @error('assessment_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="initial" {{ old('assessment_type') == 'initial' ? 'selected' : '' }}>Initial Assessment</option>
                            <option value="periodic" {{ old('assessment_type') == 'periodic' ? 'selected' : '' }}>Periodic Review</option>
                            <option value="specific" {{ old('assessment_type') == 'specific' ? 'selected' : '' }}>Specific Domain</option>
                        </select>
                        @error('assessment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Scope Type</label>
                        <select name="scope_type" class="form-select @error('scope_type') is-invalid @enderror" required>
                            <option value="">Select Scope</option>
                            <option value="full" {{ old('scope_type') == 'full' ? 'selected' : '' }}>Full COBIT Assessment</option>
                            <option value="tailored" {{ old('scope_type') == 'tailored' ? 'selected' : '' }}>Tailored (Custom)</option>
                        </select>
                        @error('scope_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Period Start</label>
                        <input type="date" name="assessment_period_start" class="form-control @error('assessment_period_start') is-invalid @enderror" value="{{ old('assessment_period_start') }}" required>
                        @error('assessment_period_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label required">Period End</label>
                        <input type="date" name="assessment_period_end" class="form-control @error('assessment_period_end') is-invalid @enderror" value="{{ old('assessment_period_end') }}" required>
                        @error('assessment_period_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('assessments.index') }}" class="btn btn-link">Cancel</a>
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                    Next: Design Factors
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 2: Design Factors -->
    <div class="wizard-step" id="step2" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Step 2: Select Design Factors</h3>
                <div class="card-subtitle">Choose the relevant design factors for assessment tailoring</div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Design Factors</strong> help customize the assessment based on your organization's context. Select the factors that apply to your environment.
                </div>
                
                <div class="row g-3">
                    @foreach($designFactors as $factor)
                    <div class="col-md-6">
                        <div class="form-selectgroup-item">
                            <label class="form-selectgroup-label">
                                <input type="checkbox" name="design_factors[]" value="{{ $factor->id }}" class="form-selectgroup-input" {{ in_array($factor->id, old('design_factors', [])) ? 'checked' : '' }}>
                                <span class="form-selectgroup-box">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="fw-bold">{{ $factor->code }} - {{ $factor->name }}</div>
                                            <div class="text-muted small">{{ $factor->description }}</div>
                                        </div>
                                        <div class="ms-2">
                                            <i class="ti ti-check text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-3 text-muted small">
                    <i class="ti ti-info-circle me-1"></i>
                    Selected: <span id="design-factors-count">0</span> of {{ $designFactors->count() }} factors
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link" onclick="prevStep(1)">
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>
                <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                    Next: GAMO Objectives
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 3: GAMO Objectives -->
    <div class="wizard-step" id="step3" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Step 3: Select GAMO Objectives</h3>
                <div class="card-subtitle">Choose governance and management objectives to assess</div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>GAMO (Governance & Management Objectives)</strong> are the core components of COBIT 2019. Select the objectives you want to assess.
                </div>
                
                <!-- Category Tabs -->
                <ul class="nav nav-tabs mb-3" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tab-edm" class="nav-link active" data-bs-toggle="tab">
                            <i class="ti ti-shield-check me-1"></i>EDM (5)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-apo" class="nav-link" data-bs-toggle="tab">
                            <i class="ti ti-target me-1"></i>APO (7)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-bai" class="nav-link" data-bs-toggle="tab">
                            <i class="ti ti-tool me-1"></i>BAI (4)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-dss" class="nav-link" data-bs-toggle="tab">
                            <i class="ti ti-server me-1"></i>DSS (4)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#tab-mea" class="nav-link" data-bs-toggle="tab">
                            <i class="ti ti-chart-line me-1"></i>MEA (3)
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    @foreach(['EDM' => 'Evaluate, Direct, Monitor', 'APO' => 'Align, Plan, Organize', 'BAI' => 'Build, Acquire, Implement', 'DSS' => 'Deliver, Service, Support', 'MEA' => 'Monitor, Evaluate, Assess'] as $category => $categoryName)
                    <div class="tab-pane {{ $category == 'EDM' ? 'active show' : '' }}" id="tab-{{ strtolower($category) }}">
                        <div class="mb-3">
                            <span class="badge bg-blue-lt">{{ $category }}</span>
                            <span class="text-muted ms-2">{{ $categoryName }}</span>
                        </div>
                        
                        <div class="row g-2">
                            @foreach($gamoObjectives->where('category', $category) as $gamo)
                            <div class="col-12">
                                <label class="form-selectgroup-item">
                                    <input type="checkbox" name="gamo_objectives[]" value="{{ $gamo->id }}" class="form-selectgroup-input" {{ in_array($gamo->id, old('gamo_objectives', [])) ? 'checked' : '' }}>
                                    <span class="form-selectgroup-label d-flex align-items-start">
                                        <span class="form-selectgroup-check"></span>
                                        <span class="form-selectgroup-label-content">
                                            <strong>{{ $gamo->code }}</strong> - {{ $gamo->name }}
                                            <span class="d-block text-muted small mt-1">{{ $gamo->description }}</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-3 text-muted small">
                    <i class="ti ti-info-circle me-1"></i>
                    Selected: <span id="gamo-count">0</span> of {{ $gamoObjectives->count() }} objectives
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link" onclick="prevStep(2)">
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>
                <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                    Next: Review
                    <i class="ti ti-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Step 4: Review & Create -->
    <div class="wizard-step" id="step4" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Step 4: Review & Create</h3>
                <div class="card-subtitle">Review your assessment configuration before creating</div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Basic Info Summary -->
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body">
                                <h3 class="card-title">Basic Information</h3>
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Title</div>
                                        <div class="datagrid-content" id="review-title">-</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Company</div>
                                        <div class="datagrid-content" id="review-company">-</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Type</div>
                                        <div class="datagrid-content" id="review-type">-</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Scope</div>
                                        <div class="datagrid-content" id="review-scope">-</div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Period</div>
                                        <div class="datagrid-content" id="review-period">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selection Summary -->
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body">
                                <h3 class="card-title">Selection Summary</h3>
                                <div class="datagrid">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">Design Factors</div>
                                        <div class="datagrid-content">
                                            <span class="badge bg-blue" id="review-df-count">0</span>
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">GAMO Objectives</div>
                                        <div class="datagrid-content">
                                            <span class="badge bg-green" id="review-gamo-count">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <div class="text-muted small mb-1">Selected Design Factors:</div>
                                    <div id="review-df-list" class="text-muted small"></div>
                                </div>
                                <div class="mt-3">
                                    <div class="text-muted small mb-1">Selected GAMO Objectives:</div>
                                    <div id="review-gamo-list" class="text-muted small"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success mt-3">
                    <i class="ti ti-circle-check me-2"></i>
                    Your assessment is ready to be created. Click "Create Assessment" to proceed.
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-link" onclick="prevStep(3)">
                    <i class="ti ti-arrow-left me-1"></i>
                    Previous
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="ti ti-check me-1"></i>
                    Create Assessment
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Wizard navigation
function nextStep(step) {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(el => el.style.display = 'none');
    
    // Show target step
    document.getElementById('step' + step).style.display = 'block';
    
    // Update progress
    document.querySelectorAll('.step-item').forEach(el => {
        el.classList.remove('active');
        if (parseInt(el.dataset.step) <= step) {
            el.classList.add('active');
        }
    });
    
    // Update review if step 4
    if (step === 4) {
        updateReview();
    }
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function prevStep(step) {
    nextStep(step);
}

// Update counters
document.addEventListener('DOMContentLoaded', function() {
    // Design Factors counter
    const dfCheckboxes = document.querySelectorAll('input[name="design_factors[]"]');
    dfCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const count = document.querySelectorAll('input[name="design_factors[]"]:checked').length;
            document.getElementById('design-factors-count').textContent = count;
        });
    });
    
    // GAMO counter
    const gamoCheckboxes = document.querySelectorAll('input[name="gamo_objectives[]"]');
    gamoCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const count = document.querySelectorAll('input[name="gamo_objectives[]"]:checked').length;
            document.getElementById('gamo-count').textContent = count;
        });
    });
    
    // Initialize counts
    document.getElementById('design-factors-count').textContent = document.querySelectorAll('input[name="design_factors[]"]:checked').length;
    document.getElementById('gamo-count').textContent = document.querySelectorAll('input[name="gamo_objectives[]"]:checked').length;
});

// Update review summary
function updateReview() {
    // Basic info
    document.getElementById('review-title').textContent = document.querySelector('[name="title"]').value || '-';
    const companySelect = document.querySelector('[name="company_id"]');
    document.getElementById('review-company').textContent = companySelect.options[companySelect.selectedIndex]?.text || '-';
    const typeSelect = document.querySelector('[name="assessment_type"]');
    document.getElementById('review-type').textContent = typeSelect.options[typeSelect.selectedIndex]?.text || '-';
    const scopeSelect = document.querySelector('[name="scope_type"]');
    document.getElementById('review-scope').textContent = scopeSelect.options[scopeSelect.selectedIndex]?.text || '-';
    
    const startDate = document.querySelector('[name="assessment_period_start"]').value;
    const endDate = document.querySelector('[name="assessment_period_end"]').value;
    document.getElementById('review-period').textContent = startDate && endDate ? `${startDate} to ${endDate}` : '-';
    
    // Design Factors
    const dfChecked = document.querySelectorAll('input[name="design_factors[]"]:checked');
    document.getElementById('review-df-count').textContent = dfChecked.length;
    const dfList = Array.from(dfChecked).map(cb => {
        const label = cb.closest('.form-selectgroup-label').querySelector('.fw-bold').textContent;
        return label;
    });
    document.getElementById('review-df-list').innerHTML = dfList.length ? dfList.join(', ') : 'None selected';
    
    // GAMO Objectives
    const gamoChecked = document.querySelectorAll('input[name="gamo_objectives[]"]:checked');
    document.getElementById('review-gamo-count').textContent = gamoChecked.length;
    const gamoList = Array.from(gamoChecked).map(cb => {
        const label = cb.closest('.form-selectgroup-label-content').querySelector('strong').textContent;
        return label;
    });
    document.getElementById('review-gamo-list').innerHTML = gamoList.length ? gamoList.join(', ') : 'None selected';
}
</script>
@endpush
