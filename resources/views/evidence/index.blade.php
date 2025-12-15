@extends('layouts.app')

@section('title', 'Evidence Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">Evidence Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @can('update', $assessment)
                    <a href="{{ route('evidence.create', $assessment) }}" class="btn btn-primary">
                        <i class="ti ti-upload me-2"></i>Upload Evidence
                    </a>
                    @endcan
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Assessment
                    </a>
                </div>
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

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Evidence Files</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['total_evidence'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Size</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['total_size'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Latest Upload</div>
                        </div>
                        <div class="h1 mb-0 small">
                            {{ $stats['latest_upload'] ? $stats['latest_upload']->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evidence List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evidence Files</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>GAMO</th>
                            <th>Question</th>
                            <th>File</th>
                            <th>Uploaded By</th>
                            <th>Upload Date</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answers as $answer)
                            <tr>
                                <td>
                                    <span class="badge badge-outline text-{{ 
                                        $answer->question->gamoObjective->category == 'EDM' ? 'purple' : 
                                        ($answer->question->gamoObjective->category == 'APO' ? 'blue' : 
                                        ($answer->question->gamoObjective->category == 'BAI' ? 'green' : 
                                        ($answer->question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                    }}">
                                        {{ $answer->question->gamoObjective->code }}
                                    </span>
                                </td>
                                <td style="max-width: 400px;">
                                    <div class="text-truncate">{{ $answer->question->question_text }}</div>
                                    <div class="text-muted small">{{ $answer->question->code }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-file-text text-muted me-2"></i>
                                        <div>
                                            <div class="fw-bold">{{ basename($answer->evidence_file) }}</div>
                                            @if($answer->evidence_encrypted)
                                                <div class="text-success small">
                                                    <i class="ti ti-lock-filled"></i> Encrypted
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $answer->answeredBy->name }}</div>
                                    <div class="text-muted small">{{ $answer->answeredBy->email }}</div>
                                </td>
                                <td>
                                    <div>{{ $answer->updated_at->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ $answer->updated_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('evidence.download', [$assessment, $answer]) }}" 
                                           class="btn btn-sm btn-icon btn-ghost-info" title="Download">
                                            <i class="ti ti-download"></i>
                                        </a>
                                        @can('update', $assessment)
                                        <form action="{{ route('evidence.destroy', [$assessment, $answer]) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this evidence?');">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-file-x icon"></i>
                                        </div>
                                        <p class="empty-title">No evidence files uploaded</p>
                                        <p class="empty-subtitle text-muted">
                                            Upload evidence files to support your assessment answers
                                        </p>
                                        <div class="empty-action">
                                            @can('update', $assessment)
                                            <a href="{{ route('evidence.create', $assessment) }}" class="btn btn-primary">
                                                <i class="ti ti-upload me-2"></i>Upload Evidence
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
            @if($answers->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        Showing {{ $answers->firstItem() }} to {{ $answers->lastItem() }} of {{ $answers->total() }} entries
                    </p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $answers->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
