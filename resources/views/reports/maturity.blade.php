@extends('layouts.app')

@section('title', 'Maturity Report - ' . $assessment->title)

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Maturity Report</div>
            <h2 class="page-title">{{ $assessment->title }}</h2>
            <div class="text-muted mt-1">{{ $assessment->code }}</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left icon-size-md me-1"></i>
                Back to Reports
            </a>
        </div>
    </div>
@endsection

@section('content')
<!-- Summary Cards -->
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Overall Maturity</div>
                </div>
                <div class="h1 mb-1">{{ number_format($overallMaturity, 2) }}</div>
                <div class="d-flex mb-2">
                    <div>out of 5.00</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: {{ ($overallMaturity / 5) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Target Level</div>
                </div>
                <div class="h1 mb-1">{{ number_format($overallTarget, 2) }}</div>
                <div class="d-flex mb-2">
                    <div>out of 5.00</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-success" style="width: {{ ($overallTarget / 5) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Gap</div>
                </div>
                <div class="h1 mb-1">{{ number_format($overallTarget - $overallMaturity, 2) }}</div>
                <div class="d-flex mb-2">
                    <div>levels to target</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-warning" style="width: {{ $gapPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Achievement</div>
                </div>
                <div class="h1 mb-1">{{ 100 - $gapPercentage }}%</div>
                <div class="d-flex mb-2">
                    <div>of target reached</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-info" style="width: {{ 100 - $gapPercentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Radar Chart -->
<div class="row mb-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Maturity Level by COBIT Domain</h3>
            </div>
            <div class="card-body">
                <canvas id="maturityRadarChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Maturity Level Guide</h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-secondary">0</span>
                            </div>
                            <div class="col">
                                <strong>Incomplete</strong>
                                <div class="text-muted small">Process not implemented</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-danger">1</span>
                            </div>
                            <div class="col">
                                <strong>Performed</strong>
                                <div class="text-muted small">Process achieves purpose</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-warning">2</span>
                            </div>
                            <div class="col">
                                <strong>Managed</strong>
                                <div class="text-muted small">Process is managed</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-info">3</span>
                            </div>
                            <div class="col">
                                <strong>Established</strong>
                                <div class="text-muted small">Process is defined</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-primary">4</span>
                            </div>
                            <div class="col">
                                <strong>Predictable</strong>
                                <div class="text-muted small">Process is measured</div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="badge bg-success">5</span>
                            </div>
                            <div class="col">
                                <strong>Optimizing</strong>
                                <div class="text-muted small">Process is optimized</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown by Category -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detailed Maturity Breakdown</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Objectives Count</th>
                    <th>Current Maturity</th>
                    <th>Target Maturity</th>
                    <th>Gap</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($maturityByCategory as $category)
                <tr>
                    <td>
                        <span class="badge bg-blue-lt">{{ $category->category }}</span>
                    </td>
                    <td>{{ $category->objective_count }}</td>
                    <td>
                        <span class="text-primary fw-bold">{{ number_format($category->avg_maturity, 2) }}</span>
                    </td>
                    <td>
                        <span class="text-success fw-bold">{{ number_format($category->avg_target, 2) }}</span>
                    </td>
                    <td>
                        <span class="text-warning fw-bold">{{ number_format($category->avg_target - $category->avg_maturity, 2) }}</span>
                    </td>
                    <td>
                        @php
                            $progress = $category->avg_target > 0 ? ($category->avg_maturity / $category->avg_target) * 100 : 0;
                        @endphp
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $progress }}%" role="progressbar"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">{{ number_format($progress, 0) }}%</span>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('maturityRadarChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: {!! json_encode($categories) !!},
            datasets: [
                {
                    label: 'Current Maturity',
                    data: {!! json_encode($currentScores) !!},
                    backgroundColor: 'rgba(32, 107, 196, 0.2)',
                    borderColor: 'rgba(32, 107, 196, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(32, 107, 196, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 4
                },
                {
                    label: 'Target Maturity',
                    data: {!! json_encode($targetScores) !!},
                    backgroundColor: 'rgba(5, 205, 153, 0.2)',
                    borderColor: 'rgba(5, 205, 153, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(5, 205, 153, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1
                    },
                    pointLabels: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.r.toFixed(2);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
