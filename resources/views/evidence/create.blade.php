@extends('layouts.app')

@section('title', 'Upload Evidence')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">Upload Evidence</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('evidence.index', $assessment) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to Evidence List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('evidence.store', $assessment) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Upload Evidence File</h3>
                        </div>
                        <div class="card-body">
                            <!-- Answer/Question Selection -->
                            <div class="mb-3">
                                <label class="form-label required">Select Question/Answer</label>
                                <select name="answer_id" class="form-select @error('answer_id') is-invalid @enderror" required>
                                    <option value="">Select a question to attach evidence...</option>
                                    @foreach($answers as $answer)
                                        <option value="{{ $answer->id }}" {{ old('answer_id') == $answer->id ? 'selected' : '' }}>
                                            [{{ $answer->question->gamoObjective->code }}] {{ Str::limit($answer->question->question_text, 80) }}
                                            @if($answer->evidence_file)
                                                (Has Evidence - Will Replace)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('answer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Choose which question this evidence supports</small>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-3">
                                <label class="form-label required">Evidence File</label>
                                <input type="file" name="evidence" class="form-control @error('evidence') is-invalid @enderror" required>
                                @error('evidence')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">
                                    Allowed formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, ZIP (Max: 10MB)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div><i class="ti ti-info-circle me-2"></i></div>
                                    <div>
                                        <h4 class="alert-title">Security Notice</h4>
                                        <div class="text-muted">
                                            All uploaded files are encrypted and stored securely. Only authorized users can access the evidence files.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('evidence.index', $assessment) }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-upload me-2"></i>Upload Evidence
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Guidelines Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Upload Guidelines</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h4 class="card-subtitle">Accepted File Types</h4>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar avatar-sm" style="background-color: #d32f2f;">
                                                    <i class="ti ti-file-text"></i>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <strong>Documents</strong>
                                                <div class="text-muted small">PDF, DOC, DOCX</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar avatar-sm" style="background-color: #388e3c;">
                                                    <i class="ti ti-file-spreadsheet"></i>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <strong>Spreadsheets</strong>
                                                <div class="text-muted small">XLS, XLSX</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar avatar-sm" style="background-color: #1976d2;">
                                                    <i class="ti ti-photo"></i>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <strong>Images</strong>
                                                <div class="text-muted small">JPG, JPEG, PNG</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar avatar-sm" style="background-color: #f57c00;">
                                                    <i class="ti ti-file-zip"></i>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <strong>Archives</strong>
                                                <div class="text-muted small">ZIP</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <h4 class="card-subtitle">Best Practices</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Use descriptive filenames
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Keep files under 10MB
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Ensure files are not corrupted
                                    </li>
                                    <li class="mb-2">
                                        <i class="ti ti-check text-success me-2"></i>
                                        Remove sensitive personal data
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Info Card -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h4 class="card-subtitle mb-2">Assessment Details</h4>
                            <dl class="row mb-0">
                                <dt class="col-5">Code:</dt>
                                <dd class="col-7"><code>{{ $assessment->code }}</code></dd>
                                
                                <dt class="col-5">Title:</dt>
                                <dd class="col-7">{{ $assessment->title }}</dd>
                                
                                <dt class="col-5">Company:</dt>
                                <dd class="col-7">{{ $assessment->company->name }}</dd>
                                
                                <dt class="col-5">Status:</dt>
                                <dd class="col-7">
                                    <span class="badge bg-{{ 
                                        $assessment->status == 'draft' ? 'secondary' : 
                                        ($assessment->status == 'in_progress' ? 'primary' : 
                                        ($assessment->status == 'completed' ? 'success' : 'info')) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
