@extends('layouts.app')

@section('title', 'Upload Evidence - Advanced')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">Upload Evidence</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('assessments.evidence.index', $assessment) }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left icon-size-md me-2"></i>Back to Evidence List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check alert-icon"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle alert-icon"></i></div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row">
            <!-- Upload Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload Evidence Files</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('assessments.evidence.store', $assessment) }}" method="POST" enctype="multipart/form-data" id="evidence-upload-form">
                            @csrf

                            <!-- Question Selection -->
                            <div class="mb-3">
                                <label class="form-label required">Link to Question</label>
                                <select name="answer_id" class="form-select @error('answer_id') is-invalid @enderror" required id="answer-select">
                                    <option value="">Select Question...</option>
                                    @foreach($answers->groupBy(fn($a) => $a->question->gamoObjective->category) as $category => $categoryAnswers)
                                        <optgroup label="{{ $category }} - {{ $categoryAnswers->first()->question->gamoObjective->category }}">
                                            @foreach($categoryAnswers as $answer)
                                                <option value="{{ $answer->id }}" 
                                                    data-gamo="{{ $answer->question->gamoObjective->code }}"
                                                    data-question="{{ $answer->question->question_text }}"
                                                    {{ old('answer_id') == $answer->id ? 'selected' : '' }}>
                                                    {{ $answer->question->gamoObjective->code }} - {{ Str::limit($answer->question->question_text, 80) }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('answer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Select the question this evidence supports</small>
                            </div>

                            <!-- Selected Question Info -->
                            <div id="question-info" class="alert alert-info d-none mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="ti ti-info-circle fs-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Selected Question</h4>
                                        <div class="text-muted"><strong id="info-gamo"></strong></div>
                                        <p class="mb-0" id="info-question"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Drag & Drop Upload Area -->
                            <div class="mb-3">
                                <label class="form-label required">Evidence File</label>
                                <div class="file-drop-area" id="file-drop-area">
                                    <div class="file-drop-icon">
                                        <i class="ti ti-cloud-upload" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="file-drop-text">
                                        <strong>Drag & drop files here</strong> or <span class="file-browse-link">browse</span>
                                    </div>
                                    <div class="file-drop-hint">
                                        Supported: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP (Max: 10MB)
                                    </div>
                                    <input type="file" name="evidence" class="file-input @error('evidence') is-invalid @enderror" id="file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip" required>
                                </div>
                                @error('evidence')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- File Preview -->
                            <div id="file-preview" class="mb-3 d-none">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="ti ti-file fs-2" id="file-icon"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" id="file-name"></div>
                                                    <div class="text-muted small" id="file-size"></div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-ghost-danger" id="remove-file">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" rows="3" class="form-control" placeholder="Add any additional notes about this evidence...">{{ old('notes') }}</textarea>
                                <small class="form-hint">Describe the evidence content or provide context</small>
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label class="form-label">Tags (Optional)</label>
                                <input type="text" name="tags" class="form-control" placeholder="e.g., policy, procedure, screenshot" value="{{ old('tags') }}">
                                <small class="form-hint">Comma-separated tags for easier searching</small>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('assessments.evidence.index', $assessment) }}" class="btn btn-link">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <i class="ti ti-upload me-2"></i>Upload Evidence
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Upload Guidelines -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload Guidelines</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="card-subtitle">Accepted File Types</h4>
                        <ul class="list-unstyled mb-3">
                            <li><i class="ti ti-circle-check text-success me-2"></i>PDF Documents</li>
                            <li><i class="ti ti-circle-check text-success me-2"></i>Word Documents (.doc, .docx)</li>
                            <li><i class="ti ti-circle-check text-success me-2"></i>Excel Files (.xls, .xlsx)</li>
                            <li><i class="ti ti-circle-check text-success me-2"></i>Images (.jpg, .png)</li>
                            <li><i class="ti ti-circle-check text-success me-2"></i>ZIP Archives</li>
                        </ul>

                        <h4 class="card-subtitle">File Size Limit</h4>
                        <p class="text-muted">Maximum file size: <strong>10 MB</strong></p>

                        <h4 class="card-subtitle">Best Practices</h4>
                        <ul class="list-unstyled">
                            <li><i class="ti ti-arrow-right text-primary me-2"></i>Use descriptive file names</li>
                            <li><i class="ti ti-arrow-right text-primary me-2"></i>Ensure files are readable</li>
                            <li><i class="ti ti-arrow-right text-primary me-2"></i>Remove sensitive information</li>
                            <li><i class="ti ti-arrow-right text-primary me-2"></i>Add notes for clarity</li>
                        </ul>

                        <div class="alert alert-info mb-0 mt-3">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Note:</strong> All evidence files are encrypted and stored securely.
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Evidence Summary</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Total Questions</div>
                                <div class="datagrid-content">{{ $answers->count() }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">With Evidence</div>
                                <div class="datagrid-content">
                                    {{ $answers->filter(fn($a) => $a->evidence_file)->count() }}
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Pending</div>
                                <div class="datagrid-content text-warning">
                                    {{ $answers->filter(fn($a) => !$a->evidence_file)->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('file-drop-area');
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    const removeFileBtn = document.getElementById('remove-file');
    const answerSelect = document.getElementById('answer-select');
    const questionInfo = document.getElementById('question-info');

    // Question selection handler
    answerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const gamo = selectedOption.dataset.gamo;
            const question = selectedOption.dataset.question;
            
            document.getElementById('info-gamo').textContent = gamo;
            document.getElementById('info-question').textContent = question;
            questionInfo.classList.remove('d-none');
        } else {
            questionInfo.classList.add('d-none');
        }
    });

    // Drag & Drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('file-drop-active');
    }

    function unhighlight() {
        dropArea.classList.remove('file-drop-active');
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
    }

    // Click to browse
    dropArea.addEventListener('click', function() {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            
            // Validate file size
            if (file.size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit');
                return;
            }

            // Validate file type
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/png',
                'application/zip'
            ];
            
            if (!allowedTypes.includes(file.type)) {
                alert('File type not supported');
                return;
            }

            displayFilePreview(file);
        }
    }

    function displayFilePreview(file) {
        const fileName = file.name;
        const fileSize = formatFileSize(file.size);
        const fileExt = fileName.split('.').pop().toLowerCase();

        // Update preview
        document.getElementById('file-name').textContent = fileName;
        document.getElementById('file-size').textContent = fileSize;
        
        // Update icon based on file type
        const fileIcon = document.getElementById('file-icon');
        fileIcon.className = 'ti fs-2 ';
        
        if (fileExt === 'pdf') {
            fileIcon.classList.add('ti-file-type-pdf', 'text-danger');
        } else if (['doc', 'docx'].includes(fileExt)) {
            fileIcon.classList.add('ti-file-type-doc', 'text-primary');
        } else if (['xls', 'xlsx'].includes(fileExt)) {
            fileIcon.classList.add('ti-file-type-xls', 'text-success');
        } else if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
            fileIcon.classList.add('ti-photo', 'text-info');
        } else if (fileExt === 'zip') {
            fileIcon.classList.add('ti-file-zip', 'text-warning');
        } else {
            fileIcon.classList.add('ti-file', 'text-secondary');
        }

        filePreview.classList.remove('d-none');
        dropArea.classList.add('d-none');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Remove file
    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('d-none');
        dropArea.classList.remove('d-none');
    });

    // Form submission
    document.getElementById('evidence-upload-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Uploading...';
    });
});
</script>
@endpush

@push('styles')
<style>
/* Drag & Drop Styles */
.file-drop-area {
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #f8f9fa;
}

.file-drop-area:hover {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.file-drop-active {
    border-color: #0d6efd;
    background-color: #e7f1ff;
    transform: scale(1.02);
}

.file-drop-icon {
    color: #6c757d;
    margin-bottom: 1rem;
}

.file-drop-text {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 0.5rem;
}

.file-browse-link {
    color: #0d6efd;
    text-decoration: underline;
    cursor: pointer;
}

.file-browse-link:hover {
    color: #0a58ca;
}

.file-drop-hint {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.5rem;
}

.file-input {
    display: none;
}

.card-subtitle {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    margin-top: 1rem;
}

.card-subtitle:first-child {
    margin-top: 0;
}
</style>
@endpush
