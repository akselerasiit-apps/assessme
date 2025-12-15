@extends('layouts.app')

@section('title', 'Assessment Summary - ' . $assessment->title)

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Assessment Summary Report</div>
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
<!-- Assessment Info -->
<div class="row mb-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assessment Information</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Company</div>
                        <div class="datagrid-content">{{ $assessment->company->name ?? 'N/A' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Type</div>
                        <div class="datagrid-content">
                            <span class="badge bg-blue-lt">{{ ucfirst($assessment->assessment_type) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Scope</div>
                        <div class="datagrid-content">
                            <span class="badge bg-purple-lt">{{ ucfirst($assessment->scope_type) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Status</div>
                        <div class="datagrid-content">
                            @if($assessment->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($assessment->status == 'reviewed')
                                <span class="badge bg-warning">Reviewed</span>
                            @elseif($assessment->status == 'approved')
                                <span class="badge bg-primary">Approved</span>
                            @endif
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Period</div>
                        <div class="datagrid-content">
                            {{ $assessment->assessment_period_start?->format('d M Y') ?? 'N/A' }} - 
                            {{ $assessment->assessment_period_end?->format('d M Y') ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created By</div>
                        <div class="datagrid-content">{{ $assessment->createdBy?->name ?? 'N/A' }}</div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created Date</div>
                        <div class="datagrid-content">{{ $assessment->created_at->format('d M Y H:i') }}</div>
                    </div>
                    @if($assessment->reviewedBy)
                    <div class="datagrid-item">
                        <div class="datagrid-title">Reviewed By</div>
                        <div class="datagrid-content">{{ $assessment->reviewedBy->name }}</div>
                    </div>
                    @endif
                    @if($assessment->approvedBy)
                    <div class="datagrid-item">
                        <div class="datagrid-title">Approved By</div>
                        <div class="datagrid-content">{{ $assessment->approvedBy->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="subheader mb-2">Completion Rate</div>
                <div class="h1 mb-3">{{ $completionRate }}%</div>
                <div class="progress progress-sm mb-2">
                    <div class="progress-bar bg-success" style="width: {{ $completionRate }}%"></div>
                </div>
                <div class="text-muted">{{ $answeredQuestions }} of {{ $totalQuestions }} questions answered</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="subheader mb-2">Evidence Rate</div>
                <div class="h1 mb-3">{{ $evidenceRate }}%</div>
                <div class="progress progress-sm mb-2">
                    <div class="progress-bar bg-info" style="width: {{ $evidenceRate }}%"></div>
                </div>
                <div class="text-muted">{{ $withEvidence }} answers with evidence files</div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Design Factors</div>
                </div>
                <div class="h1 mb-1">{{ $assessment->designFactors->count() }}</div>
                <div class="text-muted">Selected factors</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">GAMO Objectives</div>
                </div>
                <div class="h1 mb-1">{{ $assessment->gamoObjectives->count() }}</div>
                <div class="text-muted">Selected objectives</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Questions</div>
                </div>
                <div class="h1 mb-1">{{ $totalQuestions }}</div>
                <div class="text-muted">Assessment questions</div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">With Evidence</div>
                </div>
                <div class="h1 mb-1">{{ $withEvidence }}</div>
                <div class="text-muted">Supporting documents</div>
            </div>
        </div>
    </div>
</div>

<!-- Maturity Distribution -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">Maturity Level Distribution</h3>
    </div>
    <div class="card-body">
        <canvas id="maturityDistChart" height="60"></canvas>
    </div>
</div>

<!-- Top Performing & Needs Improvement -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-success-lt">
                <h3 class="card-title text-success">Top Performing Objectives</h3>
            </div>
            <div class="list-group list-group-flush">
                @forelse($topPerforming as $score)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-success-lt">{{ $score->gamoObjective->code }}</span>
                        </div>
                        <div class="col">
                            <div class="fw-bold">{{ $score->gamoObjective->name }}</div>
                            <div class="text-muted small">{{ $score->gamoObjective->category }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-success">{{ number_format($score->current_maturity_level, 2) }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted">
                    No scores available yet
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-warning-lt">
                <h3 class="card-title text-warning">Areas Needing Improvement</h3>
            </div>
            <div class="list-group list-group-flush">
                @forelse($needsImprovement as $score)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-warning-lt">{{ $score->gamoObjective->code }}</span>
                        </div>
                        <div class="col">
                            <div class="fw-bold">{{ $score->gamoObjective->name }}</div>
                            <div class="text-muted small">{{ $score->gamoObjective->category }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="badge bg-warning">{{ number_format($score->current_maturity_level, 2) }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted">
                    No scores available yet
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Design Factors & GAMO Objectives -->
<div class="row mt-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Design Factors</h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($assessment->designFactors as $factor)
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="badge bg-blue-lt mb-1">{{ $factor->code }}</div>
                                <div class="small">{{ $factor->name }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">GAMO Objectives by Category</h3>
            </div>
            <div class="card-body">
                @foreach($assessment->gamoObjectives->groupBy('category') as $category => $objectives)
                <div class="mb-3">
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ $category }}</span>
                        <span class="text-muted ms-2">{{ $objectives->count() }} objectives</span>
                    </div>
                    <div class="row g-2">
                        @foreach($objectives as $gamo)
                        <div class="col-auto">
                            <span class="badge bg-blue-lt">{{ $gamo->code }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('maturityDistChart').getContext('2d');
    
    const distribution = {!! json_encode($maturityDistribution) !!};
    const labels = ['Level 0', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5'];
    const data = [
        distribution[0] || 0,
        distribution[1] || 0,
        distribution[2] || 0,
        distribution[3] || 0,
        distribution[4] || 0,
        distribution[5] || 0
    ];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Objectives',
                data: data,
                backgroundColor: [
                    'rgba(128, 128, 128, 0.7)',
                    'rgba(214, 57, 57, 0.7)',
                    'rgba(247, 103, 7, 0.7)',
                    'rgba(66, 153, 225, 0.7)',
                    'rgba(32, 107, 196, 0.7)',
                    'rgba(5, 205, 153, 0.7)'
                ],
                borderColor: [
                    'rgb(128, 128, 128)',
                    'rgb(214, 57, 57)',
                    'rgb(247, 103, 7)',
                    'rgb(66, 153, 225)',
                    'rgb(32, 107, 196)',
                    'rgb(5, 205, 153)'
                ],
                borderWidth: 1
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
                    },
                    title: {
                        display: true,
                        text: 'Number of Objectives'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Maturity Level'
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
                            return 'Objectives: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
