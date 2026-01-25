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
                    <a href="{{ route('master-data.questions.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus icon-size-lg me-2"></i>Add Question
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkImportModal" title="Import questions from CSV">
                        <i class="ti ti-file-import icon-size-lg me-2"></i>Bulk Import
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
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div><i class="ti ti-circle-check alert-icon me-2"></i></div>
                    <div>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div><i class="ti ti-alert-circle alert-icon me-2"></i></div>
                    <div>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <!-- Stats Cards -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Total Questions</div>
                                <div class="h3 mb-0">{{ $totalQuestions ?? 0 }}</div>
                            </div>
                            <div class="bg-primary-lt p-3 rounded">
                                <i class="ti ti-help-circle text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Active</div>
                                <div class="h3 mb-0">{{ $activeCount ?? 0 }}</div>
                            </div>
                            <div class="bg-success-lt p-3 rounded">
                                <i class="ti ti-circle-check text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">Inactive</div>
                                <div class="h3 mb-0">{{ $inactiveCount ?? 0 }}</div>
                            </div>
                            <div class="bg-secondary-lt p-3 rounded">
                                <i class="ti ti-circle-x text-secondary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <div class="text-muted">GAMO Types</div>
                                <div class="h3 mb-0">{{ $gamoCount ?? 0 }}</div>
                            </div>
                            <div class="bg-info-lt p-3 rounded">
                                <i class="ti ti-layout-grid text-info" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filters & Search</h5>
            </div>
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('master-data.questions.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search questions, codes..." value="{{ request('search') }}">
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
                                    {{ $gamo->code }} - {{ Str::limit($gamo->name, 35) }}
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
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100" title="Apply filters">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card mt-3">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Code</th>
                            <th style="width: 100px;">GAMO</th>
                            <th>Question</th>
                            <th style="width: 60px;">Level</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 90px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>
                                    <code class="text-primary fw-bold">{{ $question->code }}</code>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge badge-outline text-{{ 
                                            $question->gamoObjective->category == 'EDM' ? 'purple' : 
                                            ($question->gamoObjective->category == 'APO' ? 'blue' : 
                                            ($question->gamoObjective->category == 'BAI' ? 'green' : 
                                            ($question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                        }}" style="width: fit-content;">
                                            {{ $question->gamoObjective->code }}
                                        </span>
                                        <span class="text-muted small" title="{{ $question->gamoObjective->name }}">
                                            {{ Str::limit($question->gamoObjective->name, 28, '...') }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" title="{{ $question->question_text }}">
                                        {{ Str::limit($question->question_text, 70, '...') }}
                                    </div>
                                    @if($question->required)
                                        <span class="badge bg-red-lt text-xs mt-1">
                                            <i class="ti ti-star-filled"></i> Required
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline text-cyan">L{{ $question->maturity_level }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('master-data.questions.toggle-active', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge border-0 cursor-pointer {{ $question->is_active ? 'bg-success' : 'bg-secondary' }}" 
                                                title="Toggle status">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('master-data.questions.show', $question) }}" class="btn btn-sm btn-icon btn-ghost-info" title="View">
                                            <i class="ti ti-eye icon-size-md"></i>
                                        </a>
                                        @can('update questions')
                                        <a href="{{ route('master-data.questions.edit', $question) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                            <i class="ti ti-edit icon-size-md"></i>
                                        </a>
                                        @endcan
                                        @can('delete questions')
                                        <form action="{{ route('master-data.questions.destroy', $question) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this question? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                                <i class="ti ti-trash icon-size-md"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <i class="ti ti-help-circle text-muted icon-size-xxl"></i>
                                        </div>
                                        <h4 class="text-muted mb-1">No questions found</h4>
                                        <p class="text-muted mb-3">Create your first question or adjust your filters</p>
                                        @can('create questions')
                                        <a href="{{ route('master-data.questions.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus icon-size-lg me-2"></i>Create Question
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($questions->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted small">
                        Showing <strong>{{ $questions->firstItem() }}</strong> to <strong>{{ $questions->lastItem() }}</strong> of <strong>{{ $questions->total() }}</strong> questions
                    </p>
                    <div class="ms-auto">
                        {{ $questions->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="card-footer text-center text-muted small">
                    Total: <strong>{{ $questions->count() }}</strong> questions
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
                <h5 class="modal-title">
                    <i class="ti ti-file-import me-2"></i>Bulk Import Questions
                </h5>
            </div>
            <form action="{{ route('api.questions.bulk-import') }}" method="POST" enctype="multipart/form-data" id="bulkImportForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select CSV File <span class="text-danger">*</span></label>
                        <div class="form-file">
                            <input type="file" name="file" class="form-control" accept=".csv" required id="csvFile">
                            <small class="form-hint d-block mt-2">
                                Maximum 10MB. Format: CSV with columns: code, gamo_objective_id, question_text, question_type, maturity_level, guidance, evidence_requirement, required, is_active, question_order
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <strong>CSV Format Example:</strong>
                        <pre style="font-size: 0.7rem; margin-top: 0.5rem; overflow-x: auto;"><code>code,gamo_objective_id,question_text,question_type,maturity_level,guidance,required,is_active
EDM01-L1-001,1,Governance structure?,text,1,Describe org hierarchy,1,1
EDM01-L1-002,1,Is governance documented?,yes_no,1,Check documentation,1,1</code></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitImportBtn">
                        <i class="ti ti-upload me-2"></i>Import Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal modal-blur fade" id="importProgressModal" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importing Questions</h5>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <p class="text-muted mb-2">Processing your file...</p>
                <p id="importStatus" class="text-muted small" style="min-height: 20px;">Initializing import...</p>
                <div class="progress mt-3" style="height: 6px;">
                    <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
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
    const fileInput = document.getElementById('csvFile');
    
    if (!fileInput.files.length) {
        alert('Please select a CSV file');
        return;
    }
    
    const bulkModal = bootstrap.Modal.getInstance(document.getElementById('bulkImportModal'));
    const progressModal = new bootstrap.Modal(document.getElementById('importProgressModal'));
    
    bulkModal.hide();
    setTimeout(() => progressModal.show(), 200);
    
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
        document.getElementById('importStatus').textContent = data.message || 'Import completed successfully!';
        document.getElementById('importProgressBar').style.width = '100%';
        
        setTimeout(() => {
            progressModal.hide();
            location.reload();
        }, 1500);
    })
    .catch(error => {
        document.getElementById('importStatus').textContent = 'Error: ' + (error.message || 'Import failed');
        document.getElementById('importProgressBar').classList.remove('progress-bar-striped', 'progress-bar-animated');
        document.getElementById('importProgressBar').style.width = '100%';
        document.getElementById('importProgressBar').classList.add('bg-danger');
        
        setTimeout(() => {
            progressModal.hide();
        }, 3000);
    });
});
</script>
@endpush
@endsection
