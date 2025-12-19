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
                    <span class="step-number">1</span>
                    Basic Information
                </a>
                <a href="#step2" class="step-item" data-step="2">
                    <span class="step-number">2</span>
                    Design Factors
                </a>
                <a href="#step3" class="step-item" data-step="3">
                    <span class="step-number">3</span>
                    GAMO Objectives
                </a>
                <a href="#step4" class="step-item" data-step="4">
                    <span class="step-number">4</span>
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
                        <label class="card form-check-card border-2 cursor-pointer h-100" style="cursor: pointer; transition: all 0.3s ease;">
                            <div class="card-body">
                                <div class="d-flex align-items-start gap-3">
                                    <input type="checkbox" name="design_factors[]" value="{{ $factor->id }}" class="form-check-input design-factor-check mt-1" {{ in_array($factor->id, old('design_factors', [])) ? 'checked' : '' }}>
                                    <div class="flex-fill">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="fw-bold text-dark">{{ $factor->code }}</span>
                                        </div>
                                        <div class="fw-semibold text-body mb-2">{{ $factor->name }}</div>
                                        <p class="text-muted small mb-0">{{ Str::limit($factor->description, 100, '...') }}</p>
                                    </div>
                                </div>
                            </div>
                        </label>
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
                    @foreach(['EDM' => ['color' => 'purple', 'icon' => 'shield-check', 'name' => 'Evaluate, Direct, Monitor'], 'APO' => ['color' => 'blue', 'icon' => 'target', 'name' => 'Align, Plan, Organize'], 'BAI' => ['color' => 'green', 'icon' => 'tool', 'name' => 'Build, Acquire, Implement'], 'DSS' => ['color' => 'orange', 'icon' => 'server', 'name' => 'Deliver, Service, Support'], 'MEA' => ['color' => 'pink', 'icon' => 'chart-line', 'name' => 'Monitor, Evaluate, Assess']] as $category => $categoryInfo)
                    <div class="tab-pane {{ $category == 'EDM' ? 'active show' : '' }}" id="tab-{{ strtolower($category) }}">
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-{{ $categoryInfo['color'] }}">{{ $category }}</span>
                                <h5 class="mb-0">{{ $categoryInfo['name'] }}</h5>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            @foreach($gamoObjectives->where('category', $category) as $gamo)
                            <div class="col-md-6">
                                <label class="card form-check-card border-2 cursor-pointer h-100" style="cursor: pointer; transition: all 0.3s ease;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start gap-3">
                                            <input type="checkbox" name="gamo_objectives[]" value="{{ $gamo->id }}" class="form-check-input gamo-check mt-1" {{ in_array($gamo->id, old('gamo_objectives', [])) ? 'checked' : '' }}>
                                            <div class="flex-fill">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span class="badge bg-{{ $categoryInfo['color'] }}-lt">{{ $gamo->code }}</span>
                                                    <span class="fw-bold text-dark">{{ $gamo->name }}</span>
                                                </div>
                                                <p class="text-muted small mb-0">{{ Str::limit($gamo->description, 120, '...') }}</p>
                                            </div>
                                        </div>
                                    </div>
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
    
    // Update review if step 4 - ALWAYS sync before showing
    if (step === 4) {
        updateReview();
    }
    
    // Re-apply card styling after navigation (Fix #3)
    reapplyCardStyling();
    
    // Scroll to top
    window.scrollTo(0, 0);
}

function prevStep(step) {
    nextStep(step);
}

// Update counters and styling
document.addEventListener('DOMContentLoaded', function() {
    // Design Factors counter and styling
    const dfCheckboxes = document.querySelectorAll('input[name="design_factors[]"]');
    dfCheckboxes.forEach(cb => {
        // Add click handler
        cb.addEventListener('change', function() {
            updateDesignFactorCount();
            updateCardStyle(this);
        });
        // Initialize styling
        updateCardStyle(cb);
    });
    
    // GAMO counter and styling
    const gamoCheckboxes = document.querySelectorAll('input[name="gamo_objectives[]"]');
    gamoCheckboxes.forEach(cb => {
        // Add click handler
        cb.addEventListener('change', function() {
            updateGamoCount();
            updateCardStyle(this);
        });
        // Initialize styling
        updateCardStyle(cb);
    });
    
    // Initialize counts
    updateDesignFactorCount();
    updateGamoCount();
    
    // Setup tab styling for active state with blue color (Fix #4 - prevent double binding)
    setupTabStyling();
    
    // Setup form validation (Fix #8 - required field notifications)
    setupFormValidation();
});

function updateDesignFactorCount() {
    const count = document.querySelectorAll('input[name="design_factors[]"]:checked').length;
    const elem = document.getElementById('design-factors-count');
    if (elem) elem.textContent = count;
    // Trigger form validation update (Fix #8)
    validateDesignFactors();
}

function updateGamoCount() {
    const count = document.querySelectorAll('input[name="gamo_objectives[]"]:checked').length;
    const elem = document.getElementById('gamo-count');
    if (elem) elem.textContent = count;
    // Trigger form validation update (Fix #8)
    validateGamoObjectives();
}

// Reapply card styling after navigation (Fix #3)
function reapplyCardStyling() {
    document.querySelectorAll('input[name="design_factors[]"], input[name="gamo_objectives[]"]').forEach(cb => {
        updateCardStyle(cb);
    });
}

function updateCardStyle(checkbox) {
    const card = checkbox.closest('.card');
    const label = checkbox.closest('label');
    
    if (checkbox.checked) {
        if (card) {
            card.classList.add('border-success', 'bg-success-lt');
            card.style.borderColor = '#22c55e';
        }
        if (label) {
            label.classList.add('border-success');
        }
    } else {
        if (card) {
            card.classList.remove('border-success', 'bg-success-lt');
            card.style.borderColor = '';
        }
        if (label) {
            label.classList.remove('border-success');
        }
    }
}

function setupTabStyling() {
    // Fix #4: Guard against double-binding
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    // Remove existing listeners by cloning elements
    tabLinks.forEach(link => {
        const newLink = link.cloneNode(true);
        link.parentNode.replaceChild(newLink, link);
    });
    
    // Now add fresh listeners
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(link => {
        link.addEventListener('shown.bs.tab', function() {
            // Remove active styling from all tabs
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(l => {
                l.classList.remove('active');
                l.style.borderBottomColor = 'transparent';
                l.style.color = '';
            });
            // Add active styling to current tab
            this.classList.add('active');
            this.style.borderBottomColor = '#0d6efd';
            this.style.color = '#0d6efd';
        });
        
        // Set initial state for active tab
        if (link.classList.contains('active')) {
            link.style.borderBottomColor = '#0d6efd';
            link.style.color = '#0d6efd';
        }
    });
}

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
        const parent = cb.closest('label');
        const bold = parent.querySelector('.fw-bold');
        return bold ? bold.textContent : cb.value;
    });
    document.getElementById('review-df-list').innerHTML = dfList.length ? dfList.join(', ') : 'None selected';
    
    // GAMO Objectives
    const gamoChecked = document.querySelectorAll('input[name="gamo_objectives[]"]:checked');
    document.getElementById('review-gamo-count').textContent = gamoChecked.length;
    const gamoList = Array.from(gamoChecked).map(cb => {
        const parent = cb.closest('label');
        const bold = parent.querySelector('.fw-bold');
        return bold ? bold.textContent : cb.value;
    });
    document.getElementById('review-gamo-list').innerHTML = gamoList.length ? gamoList.join(', ') : 'None selected';
}

// Fix #8: Validate required fields and show error notifications
function setupFormValidation() {
    const form = document.getElementById('assessment-wizard-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        // Validate design factors
        if (!validateDesignFactors() || !validateGamoObjectives()) {
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    // Check on initial load
    validateDesignFactors();
    validateGamoObjectives();
}

function validateDesignFactors() {
    const dfChecked = document.querySelectorAll('input[name="design_factors[]"]:checked').length;
    const dfError = document.getElementById('design-factors-error') || createErrorElement('design-factors-error');
    
    if (dfChecked === 0) {
        dfError.textContent = 'Please select at least one Design Factor.';
        dfError.style.display = 'block';
        // Find and highlight step 2 button
        const step2Elem = document.getElementById('step2');
        if (step2Elem) step2Elem.classList.add('has-error');
        return false;
    } else {
        dfError.style.display = 'none';
        const step2Elem = document.getElementById('step2');
        if (step2Elem) step2Elem.classList.remove('has-error');
        return true;
    }
}

function validateGamoObjectives() {
    const gamoChecked = document.querySelectorAll('input[name="gamo_objectives[]"]:checked').length;
    const gamoError = document.getElementById('gamo-objectives-error') || createErrorElement('gamo-objectives-error');
    
    if (gamoChecked === 0) {
        gamoError.textContent = 'Please select at least one GAMO Objective.';
        gamoError.style.display = 'block';
        // Find and highlight step 3 button
        const step3Elem = document.getElementById('step3');
        if (step3Elem) step3Elem.classList.add('has-error');
        return false;
    } else {
        gamoError.style.display = 'none';
        const step3Elem = document.getElementById('step3');
        if (step3Elem) step3Elem.classList.remove('has-error');
        return true;
    }
}

function createErrorElement(id) {
    const errorDiv = document.createElement('div');
    errorDiv.id = id;
    errorDiv.className = 'alert alert-danger mt-3';
    errorDiv.style.display = 'none';
    
    const formElement = document.getElementById('assessment-wizard-form');
    if (formElement) {
        formElement.insertBefore(errorDiv, formElement.firstChild);
    }
    
    return errorDiv;
}
</script>
@endpush

<style>
/* Step indicator active state - blue color */
.step-item.active {
    color: #0d6efd !important;
}

.step-item.active .step-number {
    background-color: #0d6efd !important;
    color: white !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    border-radius: 50%;
    font-weight: bold;
    font-size: 1.25rem;
}

.step-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    border-radius: 50%;
    font-weight: bold;
    font-size: 1.25rem;
    background-color: #e9ecef;
    color: #6c757d;
    transition: all 0.3s ease;
}

/* Card selection styling for checkboxes */
.form-check-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e0e0e0;
}

.form-check-card:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa !important;
}

.form-check-card input[type="checkbox"]:checked ~ div {
    color: #0d6efd;
}

/* Tab styling with active underline */
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #626e79;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #e0e0e0;
    color: #0d6efd;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #0d6efd !important;
    color: #0d6efd !important;
    background-color: transparent !important;
}

/* Tab content animation */
.tab-content .tab-pane {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Success card styling */
.card.border-success {
    border-color: #22c55e !important;
}

.card.bg-success-lt {
    background-color: #f0fdf4 !important;
}

/* Badge styling for categories */
.badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.625rem;
}

/* Error state styling (Fix #8) */
.has-error {
    border-color: #dc3545 !important;
}

.has-error .card-body {
    background-color: #fff5f5 !important;
}

/* Alert styling for required field errors */
.alert-danger {
    border-radius: 0.25rem;
    padding: 0.75rem 1rem;
}
</style>
