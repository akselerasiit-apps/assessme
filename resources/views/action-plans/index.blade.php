@extends('layouts.app')

@section('title', 'Action Plans - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><a href="{{ route('assessments.show', $assessment) }}">{{ $assessment->code }}</a></div>
                <h2 class="page-title">Action Plan Dashboard</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.action-plans.timeline', $assessment) }}" class="btn btn-outline-primary">Timeline View</a>
                    <a href="{{ route('assessments.action-plans.progress', $assessment) }}" class="btn btn-outline-primary">Progress Tracking</a>
                    <a href="{{ route('assessments.recommendations.index', $assessment) }}" class="btn btn-primary">Recommendations</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible"><div class="d-flex"><div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div><div>{{ session('success') }}</div></div><a class="btn-close" data-bs-dismiss="alert"></a></div>
        @endif

        <!-- Statistics -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="subheader">Total Actions</div><div class="h1 mb-0">{{ $stats['total'] }}</div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="subheader">Completion Rate</div><div class="d-flex align-items-baseline"><div class="h1 mb-0 me-2">{{ $stats['completion_rate'] }}%</div></div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="subheader">Overdue</div><div class="h1 mb-0 text-danger">{{ $stats['overdue'] }}</div></div></div></div>
            <div class="col-sm-6 col-lg-3"><div class="card"><div class="card-body"><div class="subheader">Avg Progress</div><div class="h1 mb-0">{{ $stats['avg_progress'] }}%</div></div></div></div>
        </div>

        <!-- Priority Breakdown -->
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Priority Distribution</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col"><div class="text-center"><span class="badge badge-lg bg-danger mb-2">{{ $priority_stats['critical'] }}</span><div class="text-muted">Critical</div></div></div>
                    <div class="col"><div class="text-center"><span class="badge badge-lg bg-warning mb-2">{{ $priority_stats['high'] }}</span><div class="text-muted">High</div></div></div>
                    <div class="col"><div class="text-center"><span class="badge badge-lg bg-info mb-2">{{ $priority_stats['medium'] }}</span><div class="text-muted">Medium</div></div></div>
                    <div class="col"><div class="text-center"><span class="badge badge-lg bg-secondary mb-2">{{ $priority_stats['low'] }}</span><div class="text-muted">Low</div></div></div>
                </div>
            </div>
        </div>

        <!-- Action Items by Status -->
        <div class="row">
            @foreach(['open' => 'Open', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'closed' => 'Closed'] as $status => $label)
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{ $label }} ({{ $grouped[$status]->count() }})</h3></div>
                    <div class="list-group list-group-flush">
                        @forelse($grouped[$status]->take(5) as $rec)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col">
                                    <a href="{{ route('assessments.recommendations.show', [$assessment, $rec]) }}">{{ Str::limit($rec->title, 50) }}</a>
                                    <div class="text-muted small">
                                        <span class="badge bg-{{ $rec->priority_badge }}">{{ ucfirst($rec->priority) }}</span>
                                        <span class="badge bg-blue-lt ms-1">{{ $rec->gamoObjective->code }}</span>
                                        @if($rec->responsiblePerson) - {{ $rec->responsiblePerson->name }} @endif
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if($rec->target_date)
                                        <small class="text-muted">{{ $rec->target_date->format('M d') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-3">No items</div>
                        @endforelse
                    </div>
                    @if($grouped[$status]->count() > 5)
                    <div class="card-footer text-center"><small class="text-muted">+{{ $grouped[$status]->count() - 5 }} more</small></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
