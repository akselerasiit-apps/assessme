@extends('layouts.app')

@section('title', 'Evidence Preview')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Evidence Management</div>
                <h2 class="page-title">Evidence Preview</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.evidence.download', [$assessment, $answer]) }}" class="btn btn-primary">
                        <i class="ti ti-download icon-size-lg me-2"></i>Download
                    </a>
                    @can('update', $assessment)
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#upload-version-modal">
                        <i class="ti ti-upload icon-size-lg me-2"></i>Upload New Version
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete-modal">
                        <i class="ti ti-trash icon-size-lg me-2"></i>Delete
                    </button>
                    @endcan
                    <a href="{{ route('assessments.evidence.index', $assessment) }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left icon-size-md me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <!-- Main Preview Area -->
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ basename($answer->evidence_file) }}</h3>
                        <div class="card-actions">
                            <a href="{{ route('assessments.evidence.download', [$assessment, $answer]) }}" class="btn btn-sm btn-primary">
                                <i class="ti ti-download"></i> Download
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $extension = pathinfo($answer->evidence_file, PATHINFO_EXTENSION);
                            $fileUrl = route('assessments.evidence.download', [$assessment, $answer]);
                        @endphp

                        @if(in_array(strtolower($extension), ['pdf']))
                            <!-- PDF Preview -->
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $fileUrl }}#toolbar=0" class="border" style="min-height: 600px;"></iframe>
                            </div>
                        @elseif(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                            <!-- Image Preview -->
                            <div class="text-center">
                                <img src="{{ $fileUrl }}" alt="Evidence" class="img-fluid" style="max-height: 600px;">
                            </div>
                        @elseif(in_array(strtolower($extension), ['doc', 'docx']))
                            <!-- Word Document Preview -->
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-file-type-doc fs-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Word Document</h4>
                                        <p class="mb-0">Preview not available. Please download the file to view.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ $fileUrl }}" class="btn btn-primary">
                                        <i class="ti ti-download me-2"></i>Download Document
                                    </a>
                                </div>
                            </div>
                        @elseif(in_array(strtolower($extension), ['xls', 'xlsx']))
                            <!-- Excel File Preview -->
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-file-type-xls fs-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">Excel Spreadsheet</h4>
                                        <p class="mb-0">Preview not available. Please download the file to view.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ $fileUrl }}" class="btn btn-success">
                                        <i class="ti ti-download me-2"></i>Download Spreadsheet
                                    </a>
                                </div>
                            </div>
                        @elseif(in_array(strtolower($extension), ['zip']))
                            <!-- ZIP Archive Preview -->
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-file-zip fs-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">ZIP Archive</h4>
                                        <p class="mb-0">Archive file. Please download to extract contents.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ $fileUrl }}" class="btn btn-warning">
                                        <i class="ti ti-download me-2"></i>Download Archive
                                    </a>
                                </div>
                            </div>
                        @else
                            <!-- Generic File -->
                            <div class="alert alert-secondary">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="ti ti-file fs-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">File Preview</h4>
                                        <p class="mb-0">Preview not available for this file type. Please download to view.</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ $fileUrl }}" class="btn btn-secondary">
                                        <i class="ti ti-download me-2"></i>Download File
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Version History -->
                @if(isset($versions) && $versions->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Version History</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($versions as $version)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-primary">v{{ $version->version }}</span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">{{ basename($version->file_path) }}</div>
                                    <div class="text-muted small">
                                        {{ $version->file_size }} • Uploaded by {{ $version->uploadedBy->name }} • {{ $version->created_at->format('d M Y H:i') }}
                                    </div>
                                    @if($version->notes)
                                    <div class="text-muted small mt-1">
                                        <i class="ti ti-note"></i> {{ $version->notes }}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <div class="btn-list">
                                        <a href="{{ route('assessments.evidence.download-version', [$assessment, $answer, $version]) }}" class="btn btn-sm btn-ghost-primary">
                                            <i class="ti ti-download"></i>
                                        </a>
                                        @if($version->id !== $answer->current_version_id)
                                        <button type="button" class="btn btn-sm btn-ghost-success" onclick="restoreVersion({{ $version->id }})">
                                            <i class="ti ti-restore"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar - File Info -->
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">File Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">File Name</div>
                                <div class="datagrid-content">{{ basename($answer->evidence_file) }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">File Type</div>
                                <div class="datagrid-content">{{ strtoupper(pathinfo($answer->evidence_file, PATHINFO_EXTENSION)) }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Uploaded By</div>
                                <div class="datagrid-content">{{ $answer->answeredBy->name }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Upload Date</div>
                                <div class="datagrid-content">{{ $answer->updated_at->format('d M Y H:i') }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">Encrypted</div>
                                <div class="datagrid-content">
                                    @if($answer->evidence_encrypted)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Linked Question -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Linked Question</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge bg-blue">{{ $answer->question->gamoObjective->code }}</span>
                            <span class="badge bg-{{ ['EDM' => 'purple', 'APO' => 'blue', 'BAI' => 'green', 'DSS' => 'orange', 'MEA' => 'pink'][$answer->question->gamoObjective->category] ?? 'secondary' }}">
                                {{ $answer->question->gamoObjective->category }}
                            </span>
                        </div>
                        <p class="mb-2">{{ $answer->question->question_text }}</p>
                        <a href="{{ route('assessments.take', $assessment) }}#question-{{ $answer->question_id }}" class="btn btn-sm btn-ghost-primary">
                            <i class="ti ti-arrow-right me-1"></i>View Question
                        </a>
                    </div>
                </div>

                <!-- Notes -->
                @if($answer->notes)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Notes</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $answer->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Tags -->
                @if(isset($answer->tags) && $answer->tags)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Tags</h3>
                    </div>
                    <div class="card-body">
                        @foreach(explode(',', $answer->tags) as $tag)
                            <span class="badge bg-azure-lt me-1 mb-1">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Upload New Version Modal -->
<div class="modal modal-blur fade" id="upload-version-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Version</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('assessments.evidence.upload-version', [$assessment, $answer]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Select File</label>
                        <input type="file" name="evidence" class="form-control" required>
                        <small class="form-hint">Max size: 10MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Version Notes</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Describe what changed in this version..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-upload me-2"></i>Upload Version
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Are you sure?</div>
                <div>This will permanently delete the evidence file and all its versions.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('assessments.evidence.destroy', [$assessment, $answer]) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Evidence</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function restoreVersion(versionId) {
    if (confirm('Are you sure you want to restore this version? It will become the current version.')) {
        // Submit restore form
        fetch(`/assessments/{{ $assessment->id }}/evidence/{{ $answer->id }}/restore/${versionId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to restore version');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}
</script>
@endpush
@endsection
