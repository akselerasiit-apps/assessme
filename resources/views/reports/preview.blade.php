@extends('layouts.app')

@section('title', 'Report Preview - ' . $assessment->code)

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none sticky-top bg-white">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-file-text me-2"></i>Report Preview
                    </h2>
                    <div class="text-muted mt-1">{{ $assessment->title }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back
                        </a>
                        
                        <!-- Export Dropdown -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="ti ti-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">PDF Reports</h6></li>
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
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Excel Export</h6></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('reports.export-excel', $assessment) }}">
                                        <i class="ti ti-file-spreadsheet me-2"></i>Full Report (Excel)
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Report Content -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">Assessment Report Preview</h3>
                            <p class="mb-0 mt-1">{{ $assessment->code }} | Generated: {{ now()->format('d M Y H:i') }}</p>
                        </div>
                        <div class="card-body">
                            <!-- Assessment Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 40%;">Company</th>
                                            <td>{{ $assessment->company->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assessment Type</th>
                                            <td>{{ ucfirst($assessment->assessment_type) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-success">{{ ucfirst($assessment->status) }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th style="width: 40%;">Created By</th>
                                            <td>{{ $assessment->createdBy->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Period</th>
                                            <td>
                                                {{ $assessment->assessment_period_start?->format('d M Y') ?? '-' }}
                                                to
                                                {{ $assessment->assessment_period_end?->format('d M Y') ?? '-' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created</th>
                                            <td>{{ $assessment->created_at->format('d M Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Statistics -->
                            <h4 class="mb-3">Assessment Statistics</h4>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="h2 mb-0">{{ $totalQuestions }}</div>
                                            <div class="text-muted">Total Questions</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="h2 mb-0 text-primary">{{ $completionRate }}%</div>
                                            <div class="text-muted">Completion Rate</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="h2 mb-0 text-success">{{ $evidenceRate }}%</div>
                                            <div class="text-muted">Evidence Rate</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="h2 mb-0 text-info">{{ number_format($overallMaturity, 2) }}</div>
                                            <div class="text-muted">Overall Maturity</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Maturity by Category -->
                            <h4 class="mb-3">Maturity by GAMO Category</h4>
                            <div class="table-responsive mb-4">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th style="text-align: center;">Current</th>
                                            <th style="text-align: center;">Target</th>
                                            <th style="text-align: center;">Gap</th>
                                            <th style="text-align: center;">Objectives</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($maturityByCategory as $category)
                                        <tr>
                                            <td><strong>{{ $category->category }}</strong></td>
                                            <td style="text-align: center;">{{ number_format($category->avg_maturity, 2) }}</td>
                                            <td style="text-align: center;">{{ number_format($category->avg_target, 2) }}</td>
                                            <td style="text-align: center;">
                                                @php
                                                    $gap = $category->avg_target - $category->avg_maturity;
                                                    $badgeClass = $gap >= 2 ? 'bg-danger' : ($gap >= 1 ? 'bg-warning' : 'bg-success');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ number_format($gap, 2) }}</span>
                                            </td>
                                            <td style="text-align: center;">{{ $category->objective_count }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Top Performers and Needs Improvement -->
                            <div class="row">
                                @if(isset($topPerforming) && $topPerforming->count() > 0)
                                <div class="col-md-6 mb-4">
                                    <h5 class="mb-3">
                                        <i class="ti ti-trophy text-success me-2"></i>Top Performing
                                    </h5>
                                    <div class="list-group list-group-flush">
                                        @foreach($topPerforming as $score)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $score->gamoObjective->code }}</strong>
                                                    <div class="text-muted small">{{ $score->gamoObjective->name }}</div>
                                                </div>
                                                <span class="badge bg-success">{{ number_format($score->current_maturity_level, 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(isset($needsImprovement) && $needsImprovement->count() > 0)
                                <div class="col-md-6 mb-4">
                                    <h5 class="mb-3">
                                        <i class="ti ti-alert-triangle text-warning me-2"></i>Needs Improvement
                                    </h5>
                                    <div class="list-group list-group-flush">
                                        @foreach($needsImprovement as $score)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $score->gamoObjective->code }}</strong>
                                                    <div class="text-muted small">{{ $score->gamoObjective->name }}</div>
                                                </div>
                                                <span class="badge bg-danger">{{ number_format($score->current_maturity_level, 2) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
