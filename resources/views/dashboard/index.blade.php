@extends('layouts.app')

@section('title', 'Dashboard - Assessment Overview')

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
                    <div class="page-pretitle">Welcome back</div>
                    <h2 class="page-title">
                        <i class="ti ti-layout-dashboard me-2"></i>Dashboard
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('assessments.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>New Assessment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- KPI Statistics Row -->
            <div class="row row-deck row-cards mb-3">
                <!-- Total Assessments -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['total_assessments'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary-lt">
                                        <i class="ti ti-help-circle"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Total Assessments</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In Progress Assessments -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['in_progress'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-blue-lt">
                                        <i class="ti ti-clock"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">In Progress</div>
                            @if($stats['total_assessments'] > 0)
                                <div class="mt-3">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-blue" style="width: {{ ($stats['in_progress'] / $stats['total_assessments']) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Completed Assessments -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['completed'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-success-lt">
                                        <i class="ti ti-circle-check"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Completed</div>
                            @if($stats['total_assessments'] > 0)
                                <div class="mt-3">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" style="width: {{ ($stats['completed'] / $stats['total_assessments']) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Average Maturity Level -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ number_format($stats['average_maturity'], 2) }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-orange-lt">
                                        <i class="ti ti-trending-up"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Avg Maturity Level</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-orange" style="width: {{ ($stats['average_maturity'] / 5) * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="text-muted small mt-2">Scale: 0 - 5</div>
                        </div>
                    </div>
                </div>

                <!-- Overall Completion Rate -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['completion_rate'] }}%</div>
                                <div class="ms-auto">
                                    <span class="badge bg-green-lt">
                                        <i class="ti ti-percent"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Questions Answered</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-green" style="width: {{ $stats['completion_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Draft Assessments -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['draft'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-secondary-lt">
                                        <i class="ti ti-file-text"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Draft Assessments</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-secondary" style="width: {{ $stats['total_assessments'] > 0 ? ($stats['draft'] / $stats['total_assessments']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved Assessments -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-baseline">
                                <div class="h3 mb-0">{{ $stats['approved'] }}</div>
                                <div class="ms-auto">
                                    <span class="badge bg-teal-lt">
                                        <i class="ti ti-certificate"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted mt-2">Approved</div>
                            <div class="mt-3">
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-teal" style="width: {{ $stats['total_assessments'] > 0 ? ($stats['approved'] / $stats['total_assessments']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row row-deck row-cards mb-3">
                <!-- Assessment Status Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-pie me-2"></i>Assessment Status Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="assessmentStatusChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Maturity Level Distribution -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-bar me-2"></i>Maturity Level Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="maturityLevelChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- GAMO Categories Selection -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-layout-grid me-2"></i>GAMO Categories Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="gamoDistributionChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Completion Trend -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-chart-line me-2"></i>Completion Trend (Last 7 Days)
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="completionTrendChart" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Assessments Table -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">
                                <i class="ti ti-history me-2"></i>Recent Assessments
                            </h3>
                            <div class="ms-auto">
                                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Maturity</th>
                                        <th>Created</th>
                                        <th style="width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAssessments as $assessment)
                                    <tr>
                                        <td>
                                            <code class="text-primary fw-bold">{{ $assessment->code }}</code>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark" title="{{ $assessment->title }}">
                                                {{ Str::limit($assessment->title, 40) }}
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
                                                    'archived' => 'bg-secondary',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge }}">
                                                {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="width: 100px; height: 4px;">
                                                <div class="progress-bar" style="width: {{ $assessment->progress_percentage ?? 0 }}%; background-color: #0d6efd;"></div>
                                            </div>
                                            <small class="text-muted ms-1">{{ $assessment->progress_percentage ?? 0 }}%</small>
                                        </td>
                                        <td>
                                            @if($assessment->overall_maturity_level)
                                                <span class="badge bg-primary">
                                                    {{ number_format($assessment->overall_maturity_level, 1) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $assessment->created_at->format('d M Y') }}</span>
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
                                                <i class="ti ti-help-circle" style="font-size: 2.5rem;"></i>
                                                <p class="mt-2">No assessments yet</p>
                                                <a href="{{ route('assessments.create') }}" class="btn btn-primary btn-sm">
                                                    Create Assessment
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessments by Company -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-building me-2"></i>Top Companies by Assessment Count
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th style="width: 300px;">Assessments</th>
                                        <th style="width: 100px;">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assessmentsByCompany as $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $item->company->name ?? 'Unknown' }}</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" style="width: {{ ($item->total / $stats['total_assessments']) * 100 }}%; background-color: #0d6efd;"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->total }}</span>
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
        </div>
    </div>
</div>

<!-- Include ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.0/dist/apexcharts.min.js"></script>

@push('scripts')
<script>
    // Assessment Status Chart (Pie)
    const statusChart = new ApexCharts(document.getElementById('assessmentStatusChart'), {
        series: [
            {{ $statusCounts['draft'] }},
            {{ $statusCounts['in_progress'] }},
            {{ $statusCounts['reviewed'] }},
            {{ $statusCounts['completed'] }},
            {{ $statusCounts['approved'] }}
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

    // Maturity Level Bar Chart
    const maturityChart = new ApexCharts(document.getElementById('maturityLevelChart'), {
        series: [{
            name: 'Assessments',
            data: {{ json_encode(array_values($maturityDistribution)) }}
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
            categories: ['Level 0', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5'],
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
    maturityChart.render();

    // GAMO Distribution (Radar)
    const gamoChart = new ApexCharts(document.getElementById('gamoDistributionChart'), {
        series: [{
            name: 'Selected GAMO Objectives',
            data: [
                {{ $gamoDistribution['EDM'] }},
                {{ $gamoDistribution['APO'] }},
                {{ $gamoDistribution['BAI'] }},
                {{ $gamoDistribution['DSS'] }},
                {{ $gamoDistribution['MEA'] }}
            ]
        }],
        chart: {
            type: 'radar',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        colors: ['#0d6efd'],
        xaxis: {
            categories: ['EDM', 'APO', 'BAI', 'DSS', 'MEA'],
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
        stroke: {
            show: true,
            width: 2,
            colors: ['#0d6efd'],
            dashArray: 0
        }
    });
    gamoChart.render();

    // Completion Trend (Line)
    const trendChart = new ApexCharts(document.getElementById('completionTrendChart'), {
        series: [{
            name: 'Completed Assessments',
            data: [{{ implode(',', $completionTrend) }}]
        }],
        chart: {
            type: 'area',
            height: 300,
            fontFamily: '"Inter", -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif',
            toolbar: {
                show: false
            }
        },
        colors: ['#2fb344'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: [{{ implode(',', array_map(fn($d) => "'{$d}'", array_keys($completionTrend))) }}],
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
    trendChart.render();
</script>
@endpush
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Assessments</div>
                </div>
                <div class="h1 mb-3">{{ $stats['total_assessments'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>All assessments in the system</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">In Progress</div>
                </div>
                <div class="h1 mb-3">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>Currently being assessed</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Completed</div>
                </div>
                <div class="h1 mb-3">{{ $stats['completed'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>Successfully completed</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Average Maturity</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['average_maturity'] ?? 0, 1) }}</div>
                <div class="d-flex mb-2">
                    <div>Out of 5.0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-deck row-cards mt-3">
    <!-- Assessments by Status -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assessments by Status</h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Maturity Distribution -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Maturity Level Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="maturityChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Assessments Table -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Assessments</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Maturity</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAssessments ?? [] as $assessment)
                            <tr>
                                <td><span class="text-muted">{{ $assessment->id }}</span></td>
                                <td>{{ $assessment->title }}</td>
                                <td>{{ $assessment->company->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($assessment->status) {
                                            'draft' => 'bg-secondary',
                                            'in_progress' => 'bg-blue',
                                            'under_review' => 'bg-yellow',
                                            'completed' => 'bg-green',
                                            'approved' => 'bg-teal',
                                            'archived' => 'bg-gray',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</span>
                                </td>
                                <td>{{ $assessment->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($assessment->maturity_level)
                                        <span class="badge bg-primary">{{ number_format($assessment->maturity_level, 1) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No assessments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'In Progress', 'Under Review', 'Completed', 'Approved'],
            datasets: [{
                data: [
                    {{ $stats['draft'] ?? 0 }},
                    {{ $stats['in_progress'] ?? 0 }},
                    {{ $stats['under_review'] ?? 0 }},
                    {{ $stats['completed'] ?? 0 }},
                    {{ $stats['approved'] ?? 0 }}
                ],
                backgroundColor: [
                    '#6c757d',
                    '#0054a6',
                    '#f59f00',
                    '#2fb344',
                    '#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Maturity Distribution Chart
    const maturityCtx = document.getElementById('maturityChart').getContext('2d');
    new Chart(maturityCtx, {
        type: 'bar',
        data: {
            labels: ['Level 0', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5'],
            datasets: [{
                label: 'Number of Assessments',
                data: {{ json_encode(array_values($maturityDistribution ?? [0, 0, 0, 0, 0, 0])) }},
                backgroundColor: '#0054a6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
