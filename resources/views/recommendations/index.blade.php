@extends('layouts.app')

@section('title', 'Recommendations - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.show', $assessment) }}">{{ $assessment->code }}</a>
                </div>
                <h2 class="page-title">
                    Recommendations & Action Items
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.recommendations.generate', $assessment) }}" 
                       class="btn btn-success d-none d-sm-inline-block"
                       onclick="return confirm('This will auto-generate recommendations based on gap analysis. Continue?')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Auto-Generate
                    </a>
                    <a href="{{ route('assessments.recommendations.create', $assessment) }}" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                        Create Recommendation
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
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M5 12l5 5l10 -10"></path>
                        </svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Recommendations</div>
                        </div>
                        <div class="h1 mb-0">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Open</div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['open'] }}</div>
                            <div class="me-auto">
                                <span class="badge bg-secondary">{{ $stats['total'] > 0 ? round(($stats['open'] / $stats['total']) * 100, 1) : 0 }}%</span>
                            </div>
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
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['in_progress'] }}</div>
                            <div class="me-auto">
                                <span class="badge bg-primary">{{ $stats['total'] > 0 ? round(($stats['in_progress'] / $stats['total']) * 100, 1) : 0 }}%</span>
                            </div>
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
                        <div class="d-flex align-items-baseline">
                            <div class="h1 mb-0 me-2">{{ $stats['completed'] }}</div>
                            <div class="me-auto">
                                <span class="badge bg-success">{{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100, 1) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Overview -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Priority Breakdown</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="text-center">
                                    <span class="badge badge-lg bg-danger mb-2">{{ $stats['critical'] }}</span>
                                    <div class="text-muted">Critical</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <span class="badge badge-lg bg-warning mb-2">{{ $stats['high'] }}</span>
                                    <div class="text-muted">High</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <span class="badge badge-lg bg-info mb-2">{{ $stats['total'] - $stats['critical'] - $stats['high'] }}</span>
                                    <div class="text-muted">Medium/Low</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Recommendations</h3>
                <div class="ms-auto">
                    <a href="{{ route('assessments.action-plans.index', $assessment) }}" class="btn btn-sm btn-outline-primary">
                        View Action Plans
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter datatable">
                    <thead>
                        <tr>
                            <th>GAMO</th>
                            <th>Recommendation</th>
                            <th>Priority</th>
                            <th>Owner</th>
                            <th>Target Date</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recommendations as $recommendation)
                        <tr>
                            <td>
                                <span class="badge bg-blue-lt">{{ $recommendation->gamoObjective->code }}</span>
                            </td>
                            <td>
                                <a href="{{ route('assessments.recommendations.show', [$assessment, $recommendation]) }}">
                                    {{ Str::limit($recommendation->title, 60) }}
                                </a>
                                @if($recommendation->isOverdue())
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @elseif($recommendation->isUpcoming())
                                    <span class="badge bg-warning ms-2">Due Soon</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $recommendation->priority_badge }}">
                                    {{ ucfirst($recommendation->priority) }}
                                </span>
                            </td>
                            <td>
                                @if($recommendation->responsiblePerson)
                                    {{ $recommendation->responsiblePerson->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($recommendation->target_date)
                                    {{ $recommendation->target_date->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">{{ $recommendation->target_date_formatted }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-{{ $recommendation->progress_bar_color }}" 
                                         style="width: {{ $recommendation->progress_percentage }}%" 
                                         role="progressbar" 
                                         aria-valuenow="{{ $recommendation->progress_percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <span class="visually-hidden">{{ $recommendation->progress_percentage }}% Complete</span>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $recommendation->progress_percentage }}%</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $recommendation->status_badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $recommendation->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('assessments.recommendations.edit', [$assessment, $recommendation]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-3" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                                    <path d="M9 12l.01 0" />
                                    <path d="M13 12l2 0" />
                                    <path d="M9 16l.01 0" />
                                    <path d="M13 16l2 0" />
                                </svg>
                                <p>No recommendations yet. Click "Auto-Generate" to create recommendations based on gap analysis.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recommendations->hasPages())
            <div class="card-footer d-flex align-items-center">
                {{ $recommendations->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
