@extends('layouts.app')

@section('title', 'Reports')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h2 class="page-title">Reports</h2>
            <div class="text-muted mt-1">View and export assessment reports</div>
        </div>
    </div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
            <div class="col-md-4">
                <input 
                    type="search" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by title or code..." 
                    value="{{ request('search') }}"
                >
            </div>
            <div class="col-md-3">
                <select name="company_id" class="form-select">
                    <option value="">All Companies</option>
                    @foreach(\App\Models\Company::orderBy('name')->get() as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-search me-1"></i>
                    Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="ti ti-x me-1"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Assessments List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Completed Assessments</h3>
    </div>
    
    @if($assessments->count() > 0)
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Assessment</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Completion</th>
                    <th>Created</th>
                    <th>Reports</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assessments as $assessment)
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ $assessment->title }}</span>
                            <small class="text-muted">{{ $assessment->code }}</small>
                        </div>
                    </td>
                    <td>{{ $assessment->company->name ?? 'N/A' }}</td>
                    <td>
                        @if($assessment->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @elseif($assessment->status == 'reviewed')
                            <span class="badge bg-warning">Reviewed</span>
                        @elseif($assessment->status == 'approved')
                            <span class="badge bg-primary">Approved</span>
                        @endif
                    </td>
                    <td>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="progress" style="width: 80px; height: 8px;">
                                    <div 
                                        class="progress-bar bg-success" 
                                        style="width: {{ $assessment->progress_percentage ?? 0 }}%"
                                        role="progressbar"
                                    ></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted small">{{ $assessment->progress_percentage ?? 0 }}%</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ $assessment->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a 
                                href="{{ route('reports.preview', $assessment) }}?type=summary" 
                                class="btn btn-sm btn-ghost-primary"
                                title="Preview Report"
                            >
                                <i class="ti ti-eye"></i>
                            </a>
                            
                            <!-- Export Dropdown -->
                            <div class="btn-group" role="group">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false"
                                >
                                    <i class="ti ti-download me-1"></i>Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <h6 class="dropdown-header">Export as PDF</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reports.export-pdf', $assessment) }}?type=summary">
                                            <i class="ti ti-file-text me-2"></i>Summary Report
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reports.export-pdf', $assessment) }}?type=maturity">
                                            <i class="ti ti-chart-radar me-2"></i>Maturity Report
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reports.export-pdf', $assessment) }}?type=gap-analysis">
                                            <i class="ti ti-chart-bar me-2"></i>Gap Analysis
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reports.export-pdf', $assessment) }}?type=executive">
                                            <i class="ti ti-briefcase me-2"></i>Executive Summary
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <h6 class="dropdown-header">Export as Excel</h6>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('reports.export-excel', $assessment) }}">
                                            <i class="ti ti-file-spreadsheet me-2"></i>Full Report (Excel)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            
                            <a 
                                href="{{ route('reports.maturity', $assessment) }}" 
                                class="btn btn-sm btn-ghost-info"
                                title="View Maturity Report"
                            >
                                <i class="ti ti-chart-radar"></i>
                            </a>
                            <a 
                                href="{{ route('reports.gap-analysis', $assessment) }}" 
                                class="btn btn-sm btn-ghost-warning"
                                title="View Gap Analysis"
                            >
                                <i class="ti ti-chart-bar"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="card-footer">
        {{ $assessments->links() }}
    </div>
    @else
    <div class="empty">
        <div class="empty-icon">
            <i class="ti ti-chart-bar icon"></i>
        </div>
        <p class="empty-title">No completed assessments</p>
        <p class="empty-subtitle text-muted">
            Complete an assessment to view reports and analytics.
        </p>
        <div class="empty-action">
            <a href="{{ route('assessments.index') }}" class="btn btn-primary">
                <i class="ti ti-clipboard-list me-1"></i>
                Go to Assessments
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
