@extends('layouts.app')

@section('title', 'Gap Analysis - ' . $assessment->title)

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Gap Analysis Report</div>
            <h2 class="page-title">{{ $assessment->title }}</h2>
            <div class="text-muted mt-1">{{ $assessment->code }}</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Back to Reports
            </a>
        </div>
    </div>
@endsection

@section('content')
<!-- Priority Summary -->
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Critical Gaps</div>
                    <div class="ms-auto">
                        <i class="ti ti-alert-triangle text-danger fs-1"></i>
                    </div>
                </div>
                <div class="h1 mb-1 text-danger">{{ $criticalGaps }}</div>
                <div class="text-muted">Gap ≥ 2.0 levels</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">High Priority</div>
                    <div class="ms-auto">
                        <i class="ti ti-alert-circle text-warning fs-1"></i>
                    </div>
                </div>
                <div class="h1 mb-1 text-warning">{{ $highGaps }}</div>
                <div class="text-muted">Gap 1.0 - 1.9 levels</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Medium Priority</div>
                    <div class="ms-auto">
                        <i class="ti ti-info-circle text-info fs-1"></i>
                    </div>
                </div>
                <div class="h1 mb-1 text-info">{{ $mediumGaps }}</div>
                <div class="text-muted">Gap 0.1 - 0.9 levels</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">On Target</div>
                    <div class="ms-auto">
                        <i class="ti ti-check text-success fs-1"></i>
                    </div>
                </div>
                <div class="h1 mb-1 text-success">{{ $onTarget }}</div>
                <div class="text-muted">Meeting or exceeding</div>
            </div>
        </div>
    </div>
</div>

<!-- Gap Chart -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Gap Distribution by GAMO Objective</h3>
    </div>
    <div class="card-body">
        <canvas id="gapChart" height="80"></canvas>
    </div>
</div>

<!-- Detailed Gap Analysis Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detailed Gap Analysis</h3>
        <div class="card-subtitle">Sorted by gap size (highest priority first)</div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Priority</th>
                    <th>GAMO</th>
                    <th>Objective Name</th>
                    <th>Current</th>
                    <th>Target</th>
                    <th>Gap</th>
                    <th>Progress</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gamoGaps as $gap)
                <tr>
                    <td>
                        @if($gap->gap >= 2)
                            <span class="badge bg-danger">Critical</span>
                        @elseif($gap->gap >= 1)
                            <span class="badge bg-warning">High</span>
                        @elseif($gap->gap > 0)
                            <span class="badge bg-info">Medium</span>
                        @else
                            <span class="badge bg-success">On Target</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-blue-lt">{{ $gap->code }}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ $gap->name }}</span>
                            <small class="text-muted">{{ $gap->category }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="text-primary fw-bold">{{ number_format($gap->current_maturity_level, 2) }}</span>
                    </td>
                    <td>
                        <span class="text-success fw-bold">{{ number_format($gap->target_maturity_level, 2) }}</span>
                    </td>
                    <td>
                        @if($gap->gap > 0)
                            <span class="text-warning fw-bold">{{ number_format($gap->gap, 2) }}</span>
                        @else
                            <span class="text-success fw-bold">{{ number_format($gap->gap, 2) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="progress">
                                    <div 
                                        class="progress-bar {{ $gap->gap >= 2 ? 'bg-danger' : ($gap->gap >= 1 ? 'bg-warning' : 'bg-success') }}" 
                                        style="width: {{ $gap->percentage_complete ?? 0 }}%" 
                                        role="progressbar"
                                    ></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted small">{{ $gap->percentage_complete ?? 0 }}%</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($gap->gap >= 2)
                            <span class="text-danger small">Immediate action required</span>
                        @elseif($gap->gap >= 1)
                            <span class="text-warning small">Plan improvements</span>
                        @elseif($gap->gap > 0)
                            <span class="text-info small">Minor enhancements</span>
                        @else
                            <span class="text-success small">Maintain current level</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Recommendations -->
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Recommended Actions</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-sm bg-danger-lt">
                    <div class="card-body">
                        <h4 class="text-danger">Critical Priority ({{ $criticalGaps }} items)</h4>
                        <ul class="list-unstyled mb-0">
                            <li>• Allocate immediate resources</li>
                            <li>• Assign dedicated team</li>
                            <li>• Weekly progress reviews</li>
                            <li>• Executive oversight</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm bg-warning-lt">
                    <div class="card-body">
                        <h4 class="text-warning">High Priority ({{ $highGaps }} items)</h4>
                        <ul class="list-unstyled mb-0">
                            <li>• Create improvement plan</li>
                            <li>• Set clear milestones</li>
                            <li>• Monthly monitoring</li>
                            <li>• Budget allocation</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm bg-info-lt">
                    <div class="card-body">
                        <h4 class="text-info">Medium Priority ({{ $mediumGaps }} items)</h4>
                        <ul class="list-unstyled mb-0">
                            <li>• Incremental improvements</li>
                            <li>• Regular training</li>
                            <li>• Quarterly reviews</li>
                            <li>• Continuous optimization</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('gapChart').getContext('2d');
    
    const gaps = {!! json_encode($gamoGaps) !!};
    const labels = gaps.map(g => g.code);
    const gapData = gaps.map(g => parseFloat(g.gap));
    
    // Color based on gap size
    const colors = gapData.map(gap => {
        if (gap >= 2) return 'rgba(214, 57, 57, 0.8)'; // Red for critical
        if (gap >= 1) return 'rgba(247, 103, 7, 0.8)'; // Orange for high
        if (gap > 0) return 'rgba(66, 153, 225, 0.8)'; // Blue for medium
        return 'rgba(5, 205, 153, 0.8)'; // Green for on target
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Gap (Target - Current)',
                data: gapData,
                backgroundColor: colors,
                borderColor: colors.map(c => c.replace('0.8', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Gap Level'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'GAMO Objectives'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const gap = context.parsed.y;
                            let priority = 'On Target';
                            if (gap >= 2) priority = 'Critical';
                            else if (gap >= 1) priority = 'High';
                            else if (gap > 0) priority = 'Medium';
                            
                            return [
                                'Gap: ' + gap.toFixed(2) + ' levels',
                                'Priority: ' + priority
                            ];
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
