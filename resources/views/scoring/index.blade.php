@extends('layouts.app')

@section('title', 'Scoring & Maturity')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment {{ $assessment->code }}</div>
                <h2 class="page-title">Scoring & Maturity Levels</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @can('update', $assessment)
                    <form action="{{ route('scoring.calculate', $assessment) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Recalculate all scores? This will update maturity levels based on current answers.')">
                            <i class="ti ti-calculator me-2"></i>Calculate Scores
                        </button>
                    </form>
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

        <!-- Overall Statistics -->
        <div class="row mb-3">
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Average Maturity</div>
                        <div class="h1 mb-0">{{ number_format($stats['avg_maturity'], 2) }}</div>
                        <div class="text-muted small">Level 0-5 Scale</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Total Objectives</div>
                        <div class="h1 mb-0">{{ $stats['total_objectives'] }}</div>
                        <div class="text-muted small">GAMO Objectives Assessed</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">On Target</div>
                        <div class="h1 mb-0 text-success">{{ $stats['objectives_on_target'] }}</div>
                        <div class="text-muted small">Meeting or Exceeding Target</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Below Target</div>
                        <div class="h1 mb-0 text-warning">{{ $stats['objectives_below_target'] }}</div>
                        <div class="text-muted small">Need Improvement</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maturity Level Legend -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Maturity Level Reference</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-secondary me-2" style="width: 30px;">0</span>
                            <span><strong>Incomplete</strong> - Process not implemented</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-danger me-2" style="width: 30px;">1</span>
                            <span><strong>Initial</strong> - Ad hoc, unpredictable</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-warning me-2" style="width: 30px;">2</span>
                            <span><strong>Managed</strong> - Planned and tracked</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-info me-2" style="width: 30px;">3</span>
                            <span><strong>Defined</strong> - Documented and standardized</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2" style="width: 30px;">4</span>
                            <span><strong>Quantitatively Managed</strong> - Measured</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-success me-2" style="width: 30px;">5</span>
                            <span><strong>Optimizing</strong> - Continuously improving</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scores by Category -->
        @foreach($scoresByCategory as $category => $categoryScores)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <span class="badge badge-outline text-{{ 
                        $category == 'EDM' ? 'purple' : 
                        ($category == 'APO' ? 'blue' : 
                        ($category == 'BAI' ? 'green' : 
                        ($category == 'DSS' ? 'orange' : 'pink'))) 
                    }} me-2">
                        {{ $category }}
                    </span>
                    {{ 
                        $category == 'EDM' ? 'Evaluate, Direct and Monitor' : 
                        ($category == 'APO' ? 'Align, Plan and Organize' : 
                        ($category == 'BAI' ? 'Build, Acquire and Implement' : 
                        ($category == 'DSS' ? 'Deliver, Service and Support' : 'Monitor, Evaluate and Assess')))
                    }}
                </h3>
                <div class="card-subtitle">
                    Average: {{ number_format($categoryScores->avg('current_maturity_level'), 2) }} / 5.00
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead>
                        <tr>
                            <th>GAMO Objective</th>
                            <th class="text-center">Current Level</th>
                            <th class="text-center">Target Level</th>
                            <th class="text-center">Gap</th>
                            <th class="text-center">Completion</th>
                            <th class="text-center">Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryScores as $score)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $score->gamoObjective->code }}</div>
                                <div class="text-muted small">{{ $score->gamoObjective->name }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ 
                                    $score->current_maturity_level == 0 ? 'secondary' : 
                                    ($score->current_maturity_level <= 1 ? 'danger' : 
                                    ($score->current_maturity_level <= 2 ? 'warning' : 
                                    ($score->current_maturity_level <= 3 ? 'info' : 
                                    ($score->current_maturity_level <= 4 ? 'primary' : 'success'))))
                                }}">
                                    {{ number_format($score->current_maturity_level, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-outline text-muted">
                                    {{ number_format($score->target_maturity_level, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php $gap = $score->getMaturityGap(); @endphp
                                <span class="badge bg-{{ $gap > 0 ? 'warning' : 'success' }}-lt">
                                    {{ $gap > 0 ? '+' : '' }}{{ number_format($gap, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="text-muted small">{{ $score->percentage_complete }}%</div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ $score->percentage_complete }}%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ 
                                    $score->status == 'completed' ? 'success' : 
                                    ($score->status == 'in_progress' ? 'primary' : 'secondary') 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $score->status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('scoring.show', [$assessment, $score]) }}" 
                                   class="btn btn-sm btn-ghost-info" title="View Details">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

        @if($scores->isEmpty())
        <div class="card">
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-calculator icon"></i>
                    </div>
                    <p class="empty-title">No scores calculated yet</p>
                    <p class="empty-subtitle text-muted">
                        Answer assessment questions first, then calculate scores to see maturity levels.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-primary">
                            <i class="ti ti-pencil me-2"></i>Answer Questions
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
