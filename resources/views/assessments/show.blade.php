@extends('layouts.app')

@section('title', 'Assessment Detail')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <div class="page-pretitle">Assessment</div>
            <h2 class="page-title">{{ $assessment->title }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @can('answer', $assessment)
                <a href="{{ route('assessments.answer-new', $assessment) }}" class="btn btn-primary">
                    <i class="ti ti-clipboard-check me-1"></i>
                    Answer Assessment
                </a>
                {{-- <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-outline-secondary">
                    <i class="ti ti-clipboard-text me-1"></i>
                    Old Version
                </a> --}}
                @endcan
                
                @can('update', $assessment)
                <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-outline-primary">
                    <i class="ti ti-edit me-1"></i>
                    Edit
                </a>
                @endcan
                
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="#">
                            <i class="ti ti-file-download me-2"></i>Export PDF
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="ti ti-table-export me-2"></i>Export Excel
                        </a>
                        <div class="dropdown-divider"></div>
                        @can('delete', $assessment)
                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); if(confirm('Delete this assessment?')) document.getElementById('delete-form').submit();">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                        <form id="delete-form" action="{{ route('assessments.destroy', $assessment) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<!-- Status Banner -->
<div class="alert alert-{{ $assessment->status == 'approved' ? 'success' : ($assessment->status == 'draft' ? 'secondary' : 'info') }} mb-3">
    <div class="d-flex">
        <div>
            <i class="ti ti-info-circle icon alert-icon"></i>
        </div>
        <div>
            <h4 class="alert-title">Status: {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</h4>
            <div class="text-muted">
                @if($assessment->status == 'draft')
                    This assessment is in draft. Start answering questions to make progress.
                @elseif($assessment->status == 'in_progress')
                    Assessment is in progress. Continue answering questions.
                @elseif($assessment->status == 'completed')
                    Assessment is completed and ready for review.
                @elseif($assessment->status == 'approved')
                    This assessment has been approved.
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Basic Information</h3>
            </div>
            <div class="card-body">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Code</div>
                        <div class="datagrid-content">
                            <span class="badge bg-blue-lt">{{ $assessment->code }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Company</div>
                        <div class="datagrid-content">{{ $assessment->company->name ?? 'N/A' }}</div>
                    </div>
                    {{-- <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Type</div>
                        <div class="datagrid-content">
                            <span class="badge bg-azure-lt">{{ ucfirst($assessment->assessment_type) }}</span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Scope Type</div>
                        <div class="datagrid-content">
                            <span class="badge bg-indigo-lt">{{ ucfirst($assessment->scope_type) }}</span>
                        </div>
                    </div> --}}
                    <div class="datagrid-item">
                        <div class="datagrid-title">Assessment Period</div>
                        <div class="datagrid-content">
                            {{ $assessment->assessment_period_start?->format('d M Y') ?? 'N/A' }} 
                            - 
                            {{ $assessment->assessment_period_end?->format('d M Y') ?? 'N/A' }}
                        </div>
                    </div>
                    @if($assessment->description)
                    <div class="datagrid-item">
                        <div class="datagrid-title">Description</div>
                        <div class="datagrid-content">{{ $assessment->description }}</div>
                    </div>
                    @endif
                    <div class="datagrid-item">
                        &nbsp;
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">Created By</div>
                        <div class="datagrid-content">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ $assessment->createdBy?->name }}&background=206bc4&color=fff)"></span>
                                <div>
                                    <div>{{ $assessment->createdBy?->name ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $assessment->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

        <!-- Design Factors -->
        @if($assessment->designFactors->count() > 0)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-adjustments me-2"></i>
                    Design Factors
                    <span class="badge text-white bg-blue ms-2">{{ $assessment->designFactors->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($assessment->designFactors as $factor)
                    <div class="col-md-6">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="avatar bg-blue-lt">{{ $factor->code }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $factor->name }}</div>
                                        <div class="text-muted small">{{ $factor->description }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- GAMO Objectives -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-target me-2"></i>
                    GAMO Objectives
                    <span class="badge text-white bg-green ms-2">{{ count($selectedGamoIds) }}/{{ $allGamos->count() }}</span>
                </h3>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @php
                    $categories = $allGamos->groupBy('category');
                @endphp
                
                @foreach($categories as $category => $objectives)
                <div class="mb-4">
                    <div class="mb-2">
                        <span class="badge text-white bg-blue">{{ $category }}</span>
                        <span class="text-muted ms-2">{{ $objectives->whereIn('id', $selectedGamoIds)->count() }}/{{ $objectives->count() }} selected</span>
                    </div>
                    <div class="list-group">
                        @foreach($objectives as $gamo)
                        @php
                            $isSelected = in_array($gamo->id, $selectedGamoIds);
                            $selectedGamo = $isSelected ? $assessment->gamoObjectives->firstWhere('id', $gamo->id) : null;
                            $targetLevel = $selectedGamo?->pivot->target_maturity_level ?? 3;
                            $levelNames = [
                                1 => 'Initial',
                                2 => 'Managed', 
                                3 => 'Established',
                                4 => 'Predictable',
                                5 => 'Optimizing'
                            ];
                            $levelColors = [
                                1 => 'bg-red',
                                2 => 'bg-orange',
                                3 => 'bg-yellow',
                                4 => 'bg-cyan',
                                5 => 'bg-green'
                            ];
                        @endphp
                        <div class="list-group-item {{ !$isSelected ? 'opacity-50 bg-light' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if($isSelected)
                                        <span class="badge bg-blue-lt">{{ $gamo->code }}</span>
                                    @else
                                        <span class="badge bg-secondary-lt text-muted">{{ $gamo->code }}</span>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="{{ $isSelected ? 'fw-bold' : 'text-muted' }}">
                                        {{ $gamo->name }}
                                        @if(!$isSelected)
                                            <span class="badge bg-secondary-lt ms-2">Not Selected</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small">{{ $gamo->description }}</div>
                                </div>
                                @if($isSelected)
                                <div class="col-auto">
                                    <div class="text-end">
                                        <div class="small text-muted mb-2">Target / Hasil Asesmen</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <span class="badge text-white {{ $levelColors[$targetLevel] }}">
                                                Level {{ $targetLevel }}
                                            </span>
                                            <span class="text-muted">/</span>
                                            @php
                                                $resultLevel = $assessment->results?->where('gamo_objective_id', $gamo->id)->first()?->capability_level ?? 0;
                                                $resultLevelInt = (int) $resultLevel;
                                            @endphp
                                            @if($resultLevelInt > 0)
                                                <span class="badge text-white {{ $levelColors[$resultLevelInt] ?? 'bg-secondary' }}">
                                                    Level {{ $resultLevelInt }}
                                                </span>
                                            @else
                                            <span class="badge bg-secondary-lt" id="result-level-{{ $gamo->id }}">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Progress Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Progress Summary</h3>
            </div>
            <div class="card-body">
                @php
                    // Hitung GAMO yang sudah dijawab (unique GAMO IDs dari answers)
                    $answeredGamoCount = $assessment->answers->pluck('gamo_objective_id')->unique()->count();
                    $totalGamoCount = $assessment->gamoObjectives->count();
                    $progressPercentage = $totalGamoCount > 0 ? round(($answeredGamoCount / $totalGamoCount) * 100) : 0;
                @endphp
                <div class="h1 m-0 mb-1">{{ $progressPercentage }}%</div>
                <div class="progress progress-sm mb-3">
                    <div class="progress-bar bg-primary" style="width: {{ $progressPercentage }}%" role="progressbar"></div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="text-muted small">Answered</div>
                        <div class="h3 m-0">{{ $answeredGamoCount }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Objectives</div>
                        <div class="h3 m-0">{{ $totalGamoCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Capability Assessment Chart -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-chart-radar me-2"></i>
                    Capability Assessment
                </h3>
            </div>
            <div class="card-body">
                @php
                    // Calculate average target
                    $avgTarget = $assessment->gamoObjectives->avg('pivot.target_maturity_level') ?? 0;
                @endphp
                
                <!-- Metrics Summary -->
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="card card-sm bg-blue-lt">
                            <div class="card-body text-center p-2">
                                <div class="text-muted small mb-1">
                                    <i class="ti ti-target"></i> Target
                                </div>
                                <div class="h2 m-0 text-blue">{{ number_format($avgTarget, 2) }}</div>
                                {{-- <div class="text-muted small">Average Level</div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card card-sm bg-cyan-lt">
                            <div class="card-body text-center p-2">
                                <div class="text-muted small mb-1">
                                    <i class="ti ti-chart-line"></i> Current
                                </div>
                                <div class="h2 m-0 text-cyan" id="avgCurrentCapability">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                {{-- <div class="text-muted small">Average Score</div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card card-sm bg-orange-lt" id="gapCard">
                            <div class="card-body text-center p-2">
                                <div class="text-muted small mb-1">
                                    <i class="ti ti-delta"></i> Gap
                                </div>
                                <div class="h2 m-0 text-orange" id="avgGapDisplay">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                {{-- <div class="text-muted small">
                                    @if($avgGap > 0)
                                        Need Improvement
                                    @elseif($avgGap < 0)
                                        Above Target
                                    @else
                                        On Target
                                    @endif
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Radar Chart -->
                <div style="height: 400px;">
                    <canvas id="capabilityRadarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timeline</h3>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-blue-lt">
                                <i class="ti ti-plus"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Created</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->created_at->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->createdBy?->name }}</div>
                        </div>
                    </div>
                </div>
                
                @if($assessment->reviewed_by)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-info-lt">
                                <i class="ti ti-eye-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Reviewed</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->reviewed_at?->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->reviewedBy?->name }}</div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($assessment->approved_by)
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar bg-success-lt">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Approved</strong>
                            </div>
                            <div class="text-muted small">{{ $assessment->approved_at?->format('d M Y H:i') }}</div>
                            <div class="text-muted small">by {{ $assessment->approvedBy?->name }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Render Capability Radar Chart
    const ctx = document.getElementById('capabilityRadarChart');
    
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    // Prepare data from assessment
    const gamoObjectives = @json($assessment->gamoObjectives);
    const gamoScores = @json($assessment->gamoScores);
    
    if (!gamoObjectives || gamoObjectives.length === 0) {
        ctx.getContext('2d').fillText('No GAMO objectives selected', 10, 50);
        return;
    }
    
    // Create score map for quick lookup
    const scoreMap = {};
    if (gamoScores) {
        gamoScores.forEach(score => {
            scoreMap[score.gamo_objective_id] = score.current_maturity_level || 0;
        });
    }
    
    // Prepare chart data
    const labels = [];
    const realizationData = [];
    const targetData = [];
    
    gamoObjectives.forEach(gamo => {
        labels.push(gamo.code);
        realizationData.push(scoreMap[gamo.id] || 0);
        targetData.push(gamo.pivot?.target_maturity_level || 3);
    });
    
    // Create radar chart
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Realisasi',
                    data: realizationData,
                    backgroundColor: 'rgba(32, 107, 196, 0.2)',
                    borderColor: 'rgba(32, 107, 196, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(32, 107, 196, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(32, 107, 196, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Target',
                    data: targetData,
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    borderColor: 'rgba(76, 175, 80, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(76, 175, 80, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(76, 175, 80, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1,
            scales: {
                r: {
                    beginAtZero: true,
                    min: 0,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 10
                        }
                    },
                    pointLabels: {
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    angleLines: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 10,
                        font: {
                            size: 12
                        },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += 'Level ' + context.parsed.r.toFixed(2);
                            return label;
                        }
                    }
                }
            }
        }
    });
});

// Calculate capability level for each GAMO
const assessmentId = {{ $assessment->id }};
const selectedGamoIds = @json($selectedGamoIds);

$(document).ready(function() {
    // Calculate capability level for each selected GAMO
    selectedGamoIds.forEach(gamoId => {
        calculateGamoCapabilityLevel(gamoId);
    });
});

function calculateGamoCapabilityLevel(gamoId) {
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/activities`,
        method: 'GET',
        success: function(response) {
            const activities = response.activities || {};
            let achievedLevel = 0;
            let achievedCompliance = 0;
            
            // Calculate achieved level based on COBIT 2019 rules
            // Threshold: 85%, Sequential, Skip empty levels
            for (let level = 1; level <= 5; level++) {
                const levelActivities = activities[level] || [];
                
                // Skip level jika tidak ada activities
                if (levelActivities.length === 0) continue;
                
                let totalWeight = 0;
                let weightedScore = 0;
                
                levelActivities.forEach(activity => {
                    const weight = activity.weight || 1;
                    totalWeight += weight;
                    
                    if (activity.answer && activity.answer.capability_score) {
                        weightedScore += weight * activity.answer.capability_score;
                    }
                });
                
                // Calculate compliance for this level
                const compliance = totalWeight > 0 ? ((weightedScore / totalWeight) * 100) : 0;
                
                // Level achieved if compliance >= 85% (COBIT 2019)
                if (compliance >= 85) {
                    achievedLevel = level;
                    achievedCompliance = compliance;
                } else {
                    // Stop if level not achieved
                    break;
                }
            }
            
            // Update display
            const levelColors = {
                1: 'bg-red',
                2: 'bg-orange',
                3: 'bg-yellow',
                4: 'bg-cyan',
                5: 'bg-green'
            };
            
            const $badge = $('#result-level-' + gamoId);
            if (achievedLevel > 0) {
                $badge.removeClass('bg-secondary-lt').addClass('text-white ' + levelColors[achievedLevel]);
                $badge.html('Level ' + achievedLevel);
            } else {
                $badge.html('-');
            }
        },
        error: function() {
            $('#result-level-' + gamoId).html('-');
        }
    });
}

// Calculate overall capability metrics (reuse assessmentId from above)
const avgTarget = {{ $avgTarget }};
const selectedGamoIdsForMetrics = @json($selectedGamoIds);
let capabilityLevels = [];
let completedMetrics = 0;

selectedGamoIdsForMetrics.forEach(gamoId => {
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/activities`,
        method: 'GET',
        success: function(response) {
            const activities = response.activities || {};
            let achievedLevel = 0;
            
            // Calculate achieved level based on COBIT 2019 rules
            for (let level = 1; level <= 5; level++) {
                const levelActivities = activities[level] || [];
                if (levelActivities.length === 0) continue;
                
                let totalWeight = 0;
                let weightedScore = 0;
                
                levelActivities.forEach(activity => {
                    const weight = activity.weight || 1;
                    totalWeight += weight;
                    
                    if (activity.answer && activity.answer.capability_score) {
                        weightedScore += weight * activity.answer.capability_score;
                    }
                });
                
                const compliance = totalWeight > 0 ? ((weightedScore / totalWeight) * 100) : 0;
                
                if (compliance >= 85) {
                    achievedLevel = level;
                } else {
                    break;
                }
            }
            
            capabilityLevels.push(achievedLevel);
            completedMetrics++;
            
            // When all GAMOs calculated, update summary
            if (completedMetrics === selectedGamoIdsForMetrics.length) {
                updateCapabilitySummary();
            }
        },
        error: function() {
            capabilityLevels.push(0);
            completedMetrics++;
            
            if (completedMetrics === selectedGamoIdsForMetrics.length) {
                updateCapabilitySummary();
            }
        }
    });
});

function updateCapabilitySummary() {
    // Calculate average current capability
    const totalCurrent = capabilityLevels.reduce((a, b) => a + b, 0);
    const avgCurrent = capabilityLevels.length > 0 ? (totalCurrent / capabilityLevels.length) : 0;
    
    // Update Current display
    $('#avgCurrentCapability').text(avgCurrent.toFixed(2));
    
    // Calculate and update Gap
    const gap = avgTarget - avgCurrent;
    const gapDisplay = gap > 0 ? '+' + gap.toFixed(2) : gap.toFixed(2);
    
    $('#avgGapDisplay').text(gapDisplay);
    
    // Update Gap card color
    if (gap > 0) {
        $('#gapCard').removeClass('bg-green-lt').addClass('bg-orange-lt');
        $('#avgGapDisplay').removeClass('text-green').addClass('text-orange');
    } else {
        $('#gapCard').removeClass('bg-orange-lt').addClass('bg-green-lt');
        $('#avgGapDisplay').removeClass('text-orange').addClass('text-green');
    }
}
</script>
@endpush
