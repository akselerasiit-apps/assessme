@extends('layouts.app')

@section('title', 'Question Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Question Bank</div>
                <h2 class="page-title">Question Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                @can('create questions')
                <div class="btn-group" role="group">
                    <a href="{{ route('questions.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Add Question
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkImportModal" title="Import questions from CSV">
                        <i class="ti ti-file-import me-2"></i>Bulk Import
                    </button>
                </div>
                @endcan
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

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body border-bottom py-3">
                <form method="GET" action="{{ route('questions.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search questions..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="gamo_id" class="form-select">
                            <option value="">All GAMO Objectives</option>
                            @foreach($gamoObjectives as $gamo)
                                <option value="{{ $gamo->id }}" {{ request('gamo_id') == $gamo->id ? 'selected' : '' }}>
                                    {{ $gamo->code }} - {{ Str::limit($gamo->name, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="maturity_level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($maturityLevels as $level)
                                <option value="{{ $level }}" {{ request('maturity_level') == $level ? 'selected' : '' }}>
                                    Level {{ $level }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter datatable">
                    <thead>
                        <tr>
                            <th style="min-width: 90px;">Code</th>
                            <th style="min-width: 120px;">GAMO</th>
                            <th style="min-width: 300px;">Question</th>
                            <th style="min-width: 120px;">Type</th>
                            <th style="min-width: 80px;">Level</th>
                            <th style="min-width: 80px;">Order</th>
                            <th style="min-width: 100px;">Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td><code>{{ $question->code }}</code></td>
                                <td>
                                    <div>
                                        <span class="badge badge-outline text-{{ 
                                            $question->gamoObjective->category == 'EDM' ? 'purple' : 
                                            ($question->gamoObjective->category == 'APO' ? 'blue' : 
                                            ($question->gamoObjective->category == 'BAI' ? 'green' : 
                                            ($question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                        }}">
                                            {{ $question->gamoObjective->code }}
                                        </span>
                                    </div>
                                    <div class="text-muted small" title="{{ $question->gamoObjective->name }}">
                                        {{ Str::limit($question->gamoObjective->name, 30, '...') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold" title="{{ $question->question_text }}">
                                        {{ Str::limit($question->question_text, 80, '...') }}
                                    </div>
                                    @if($question->required)
                                        <span class="badge bg-red-lt">Required</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-cyan">L{{ $question->maturity_level }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $question->question_order ?? '-' }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('questions.toggle-active', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge {{ $question->is_active ? 'bg-green' : 'bg-secondary' }} border-0" 
                                                style="cursor: pointer;">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('questions.show', $question) }}" class="btn btn-sm btn-icon btn-ghost-info" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @can('update questions')
                                        <a href="{{ route('questions.edit', $question) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete questions')
                                        <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-question-mark icon"></i>
                                        </div>
                                        <p class="empty-title">No questions found</p>
                                        <p class="empty-subtitle text-muted">
                                            Get started by creating your first question
                                        </p>
                                        <div class="empty-action">
                                            @can('create questions')
                                            <a href="{{ route('questions.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-2"></i>Add Question
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($questions->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} entries
                    </p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $questions->appends(request()->query())->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Import Modal -->
<div class="modal modal-blur fade" id="bulkImportModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-header">
                <h5 class="modal-title">Bulk Import Questions</h5>
            </div>
            <form action="{{ route('questions.bulk-import') }}" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                        <small class="form-hint">
                            Upload a CSV file with the following columns: code, gamo_objective_id, question_text, question_type, maturity_level, guidance, evidence_requirement, required, is_active, question_order
                        </small>
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>CSV Format Example:</strong>
                        <pre style="font-size: 0.75rem; margin-top: 0.5rem;"><code>code,gamo_objective_id,question_text,question_type,maturity_level,guidance,evidence_requirement,required,is_active,question_order
EDM01-L1-001,1,What is your current governance structure?,text,1,Describe the organizational hierarchy,Document org chart,1,1,1
EDM01-L1-002,1,Is governance documented?,yes_no,1,Check documentation,Policy documents,1,1,2</code></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-link" data-bs-dismiss="modal">Close</a>
                    <button type="button" class="btn btn-primary" id="submitImportBtn">
                        <i class="ti ti-upload me-2"></i>Import Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Modal (shown after import starts) -->
<div class="modal modal-blur fade" id="importProgressModal" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importing Questions</h5>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <p class="text-muted mb-0">
                    Processing your file... <br>
                    <small id="importStatus">Initializing import...</small>
                </p>
                <div class="progress mt-3">
                    <div id="importProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('submitImportBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const form = document.getElementById('bulkImportForm');
    const fileInput = form.querySelector('input[name="file"]');
    
    if (!fileInput.files.length) {
        alert('Please select a CSV file');
        return;
    }
    
    // Hide import modal and show progress modal
    const importModal = bootstrap.Modal.getInstance(document.getElementById('bulkImportModal'));
    const progressModal = new bootstrap.Modal(document.getElementById('importProgressModal'));
    
    importModal.hide();
    progressModal.show();
    
    // Submit form via AJAX
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('importStatus').textContent = data.message || 'Import completed!';
        document.getElementById('importProgressBar').style.width = '100%';
        
        setTimeout(() => {
            progressModal.hide();
            location.reload();
        }, 2000);
    })
    .catch(error => {
        document.getElementById('importStatus').textContent = 'Import failed: ' + error.message;
        console.error('Error:', error);
        
        setTimeout(() => {
            progressModal.hide();
        }, 3000);
    });
});
</script>
@endpush
@endsection
