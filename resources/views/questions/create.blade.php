@extends('layouts.app')

@section('title', 'Tambah Aktifitas')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Tambah Aktifitas Baru</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.questions.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left icon-size-md me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('master-data.questions.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Aktifitas</h3>
                        </div>
                        <div class="card-body">
                            <!-- GAMO Objective -->
                            <div class="mb-3">
                                <label class="form-label required">GAMO Objective</label>
                                <select name="gamo_objective_id" id="gamoObjectiveSelect" class="form-select @error('gamo_objective_id') is-invalid @enderror" required>
                                    <option value="" data-code="">Select GAMO Objective...</option>
                                    @foreach($gamoObjectives as $gamo)
                                        <option value="{{ $gamo->id }}" data-code="{{ $gamo->code }}" 
                                                {{ (old('gamo_objective_id', $selectedGamoId ?? null) == $gamo->id) ? 'selected' : '' }}>
                                            [{{ $gamo->category }}] {{ $gamo->code }} - {{ $gamo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gamo_objective_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Code -->
                            <div class="mb-3">
                                <label class="form-label required">Kode Aktifitas</label>
                                <input type="text" name="code" id="questionCodeInput" class="form-control @error('code') is-invalid @enderror" 
                                       placeholder="e.g., EDM01.02.001" value="{{ old('code') }}" required>
                                <small class="form-hint">Auto-filled based on GAMO Objective</small>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Question Text English -->
                            <div class="mb-3">
                                <label class="form-label required">
                                    <i class="ti ti-language me-1"></i>Teks Aktifitas (English)
                                </label>
                                <textarea name="question_text_en" rows="3" 
                                          class="form-control @error('question_text_en') is-invalid @enderror" 
                                          placeholder="Enter activity text in English..." required>{{ old('question_text_en') }}</textarea>
                                @error('question_text_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Question Text Indonesian -->
                            <div class="mb-3">
                                <label class="form-label required">
                                    <i class="ti ti-language me-1"></i>Teks Aktifitas (Bahasa Indonesia)
                                </label>
                                <textarea name="question_text_id" rows="3" 
                                          class="form-control @error('question_text_id') is-invalid @enderror" 
                                          placeholder="Masukkan teks aktifitas dalam Bahasa Indonesia..." required>{{ old('question_text_id') }}</textarea>
                                @error('question_text_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Guidance -->
                            <div class="mb-3">
                                <label class="form-label">Panduan <span class="form-label-description">Optional</span></label>
                                <textarea name="guidance" rows="3" 
                                          class="form-control @error('guidance') is-invalid @enderror" 
                                          placeholder="Panduan tambahan untuk melakukan aktifitas ini...">{{ old('guidance') }}</textarea>
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
                            <!-- Maturity Level -->
                            <div class="mb-3">
                                <label class="form-label required">Maturity Level</label>
                                <select name="maturity_level" id="maturityLevelSelect" class="form-select @error('maturity_level') is-invalid @enderror" required>
                                    <option value="">Select level...</option>
                                    @foreach($maturityLevels as $level)
                                        <option value="{{ $level }}" {{ old('maturity_level') == $level ? 'selected' : '' }}>
                                            Level {{ $level }} - {{ [2 => 'Managed', 3 => 'Established', 4 => 'Predictable', 5 => 'Optimizing'][$level] ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('maturity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Required Checkbox -->
                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="required" value="1" 
                                           {{ old('required', true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Aktifitas Wajib</span>
                                </label>
                                <small class="form-hint d-block">Harus dinilai dalam asesmen</small>
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
                                    <i class="ti ti-check me-2"></i>Simpan Aktifitas
                                </button>
                                <a href="{{ route('master-data.questions.index') }}" class="btn btn-ghost-secondary">
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

@push('scripts')
<script>
$(document).ready(function() {
    const gamoSelect = $('#gamoObjectiveSelect');
    const codeInput = $('#questionCodeInput');
    const levelSelect = $('#maturityLevelSelect');
    
    // Auto-fill question code when GAMO or Level changes
    function updateQuestionCode() {
        const gamoCode = gamoSelect.find(':selected').data('code');
        const level = levelSelect.val();
        
        if (gamoCode && level) {
            // Format: EDM01.02.001 (GAMO.Level.Sequence)
            const prefix = gamoCode + '.' + String(level).padStart(2, '0') + '.';
            
            // Only update if field is empty or has the same prefix
            const currentValue = codeInput.val();
            if (!currentValue || currentValue.startsWith(gamoCode)) {
                codeInput.val(prefix + '001');
            }
        }
    }
    
    gamoSelect.on('change', updateQuestionCode);
    levelSelect.on('change', updateQuestionCode);
});
</script>
@endpush
