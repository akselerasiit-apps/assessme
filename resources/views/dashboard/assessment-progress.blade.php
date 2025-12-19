@extends('layouts.app')

@section('title', 'Assessment Progress Dashboard')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none sticky-top bg-white">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col">
                    <div class="page-pretitle">Tracking</div>
                    <h2 class="page-title">
                        <i class="ti ti-progress me-2"></i>Assessment Progress
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Progress Statistics -->
            <div class="row row-deck row-cards mb-3">
                <!-- Total Assessments -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $progressStats['total'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary-lt"><i class="ti ti-list-check"></i></span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Total Assessments</div>
                        </div>
                    </div>
                </div>

                <!-- Average Progress -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $progressStats['avg_progress'] }}%</div>
                                <div class="ms-auto">
                                    <span class="badge bg-blue-lt"><i class="ti ti-progress"></i></span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Average Progress</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-blue" style="width: {{ $progressStats['avg_progress'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avg Questions Answered -->
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $progressStats['avg_questions_answered'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-success-lt"><i class="ti ti-help-circle"></i></span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Avg Questions/Assessment</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Cards -->
            <div class="row row-deck row-cards mb-3">
                @php
                    $statusColors = [
                        'draft' => ['bg' => 'secondary', 'icon' => 'file-text'],
                        'in_progress' => ['bg' => 'blue', 'icon' => 'clock'],
                        'reviewed' => ['bg' => 'yellow', 'icon' => 'eye'],
                        'completed' => ['bg' => 'success', 'icon' => 'circle-check'],
                        'approved' => ['bg' => 'teal', 'icon' => 'certificate'],
                    ];
                @endphp
                @foreach($progressStats['by_status'] as $status => $count)
                <div class="col-sm-6 col-lg-2.4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h4 mb-0">{{ $count }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-{{ $statusColors[$status]['bg'] }}-lt">
                                        <i class="ti ti-{{ $statusColors[$status]['icon'] }}"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-1 small">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                            @if($progressStats['total'] > 0)
                                <div class="mt-2" style="font-size: 12px;">
                                    {{ round(($count / $progressStats['total']) * 100) }}% of total
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Charts Row -->
            <div class="row row-deck row-cards mb-3">
                <!-- Progress Distribution Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-bar me-2"></i>Progress Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="progressChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-pie me-2"></i>Assessment by Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="statusChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Metrics and Company Progress -->
            <div class="row row-deck row-cards mb-3">
                <!-- Team Assignments -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-users me-2"></i>Team Assignments
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Assessor</th>
                                        <th style="width: 100px;">Assignments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teamMetrics as $member)
                                    <tr>
                                        <td class="fw-semibold">{{ $member->name }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $member->total_assignments }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">No team assignments yet</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Companies by Progress -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-building me-2"></i>Companies by Progress
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Count</th>
                                        <th style="width: 100px;">Avg Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($companiesProgress as $company)
                                    <tr>
                                        <td class="fw-semibold">{{ $company->name }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $company->total_count }}</span>
                                        </td>
                                        <td>
                                            <div class="progress" style="width: 100px; height: 4px;">
                                                <div class="progress-bar" style="width: {{ $company->avg_progress ?? 0 }}%; background-color: #0d6efd;"></div>
                                            </div>
                                            <small class="text-muted">{{ round($company->avg_progress ?? 0) }}%</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No company data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Assessment List -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-filter me-2"></i>Assessments
                            </h3>
                        </div>
                        
                        <!-- Filters -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('dashboard.progress') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ $statusFilter === $status ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Company</label>
                                    <select name="company" class="form-select">
                                        <option value="">All Companies</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ $companyFilter == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti ti-search me-1"></i>Filter
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Assessment Table -->
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th style="width: 200px;">Progress</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessments as $assessment)
                                    <tr>
                                        <td>
                                            <code class="text-primary fw-bold">{{ $assessment->code }}</code>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark" title="{{ $assessment->title }}">
                                                {{ Str::limit($assessment->title, 35) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $assessment->company->name ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = match($assessment->status) {
                                                    'draft' => 'bg-secondary',
                                                    'in_progress' => 'bg-blue',
                                                    'reviewed' => 'bg-yellow',
                                                    'completed' => 'bg-success',
                                                    'approved' => 'bg-teal',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge }}">
                                                {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress" style="width: 150px; height: 6px; margin-right: 8px;">
                                                    <div class="progress-bar" style="width: {{ $assessment->progress_percentage ?? 0 }}%; background-color: #0d6efd;"></div>
                                                </div>
                                                <span class="text-muted" style="min-width: 40px;">{{ $assessment->progress_percentage ?? 0 }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $assessment->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $assessment->updated_at->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if($assessment->status === 'draft' || $assessment->status === 'in_progress')
                                                <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-sm btn-icon btn-ghost-info" title="Continue">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <div>
                                                <i class="ti ti-inbox" style="font-size: 2.5rem;"></i>
                                                <p class="mt-2">No assessments found</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($assessments->hasPages())
                        <div class="card-footer">
                            {{ $assessments->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>

@push('scripts')
<script>
    // Progress Distribution Chart (Bar)
    const progressChart = new ApexCharts(document.getElementById('progressChart'), {
        series: [{
            name: 'Assessments',
            data: [
                {{ $progressBuckets['0-20%'] }},
                {{ $progressBuckets['21-40%'] }},
                {{ $progressBuckets['41-60%'] }},
                {{ $progressBuckets['61-80%'] }},
                {{ $progressBuckets['81-100%'] }}
            ]
        }],
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4
            }
        },
        colors: ['#0d6efd'],
        xaxis: {
            categories: ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'],
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    fontFamily: '"Inter", sans-serif'
                }
            }
        },
        dataLabels: {
            enabled: false
        }
    });
    progressChart.render();

    // Status Distribution Chart (Pie)
    const statusChart = new ApexCharts(document.getElementById('statusChart'), {
        series: [
            {{ $progressStats['by_status']['draft'] }},
            {{ $progressStats['by_status']['in_progress'] }},
            {{ $progressStats['by_status']['reviewed'] }},
            {{ $progressStats['by_status']['completed'] }},
            {{ $progressStats['by_status']['approved'] }}
        ],
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        labels: ['Draft', 'In Progress', 'Reviewed', 'Completed', 'Approved'],
        colors: ['#6c757d', '#0054a6', '#f59f00', '#2fb344', '#20c997'],
        plotOptions: {
            pie: {
                donut: {
                    size: '65%'
                }
            }
        },
        legend: {
            position: 'bottom',
            fontFamily: '"Inter", sans-serif'
        }
    });
    statusChart.render();
</script>
@endpush
