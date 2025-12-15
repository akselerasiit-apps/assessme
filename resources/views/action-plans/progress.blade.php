@extends('layouts.app')
@section('title', 'Progress Tracking - ' . $assessment->title)
@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col"><div class="page-pretitle"><a href="{{ route('assessments.action-plans.index', $assessment) }}">Action Plans</a></div><h2 class="page-title">Progress Tracking</h2></div>
            <div class="col-auto ms-auto d-print-none"><a href="{{ route('assessments.action-plans.index', $assessment) }}" class="btn btn-outline-primary">Back to Dashboard</a></div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if($ownerStats)
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Progress by Owner</h3></div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead><tr><th>Owner</th><th>Total</th><th>Open</th><th>In Progress</th><th>Completed</th><th>Avg Progress</th></tr></thead>
                    <tbody>
                        @foreach($ownerStats as $stat)
                        <tr>
                            <td>{{ $stat['user']->name }}</td>
                            <td><span class="badge">{{ $stat['total'] }}</span></td>
                            <td>{{ $stat['open'] }}</td>
                            <td>{{ $stat['in_progress'] }}</td>
                            <td>{{ $stat['completed'] }}</td>
                            <td><div class="progress progress-sm"><div class="progress-bar" style="width: {{ $stat['avg_progress'] }}%"></div></div><small>{{ $stat['avg_progress'] }}%</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Progress by GAMO</h3></div>
            <div class="row g-3 p-3">
                @foreach($byGamo as $data)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2"><span class="badge bg-blue-lt me-2">{{ $data['gamo']->code }}</span><div class="flex-fill">{{ $data['gamo']->name }}</div></div>
                            <div class="progress progress-sm mb-2"><div class="progress-bar" style="width: {{ $data['avg_progress'] }}%"></div></div>
                            <div class="row text-center"><div class="col"><small class="text-muted">{{ $data['total'] }} Total</small></div><div class="col"><small class="text-muted">{{ $data['completed'] }} Done</small></div><div class="col"><small class="text-muted">{{ $data['avg_progress'] }}% Progress</small></div></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">All Recommendations</h3></div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter datatable">
                    <thead><tr><th>GAMO</th><th>Recommendation</th><th>Owner</th><th>Status</th><th>Progress</th></tr></thead>
                    <tbody>
                        @foreach($recommendations as $rec)
                        <tr>
                            <td><span class="badge bg-blue-lt">{{ $rec->gamoObjective->code }}</span></td>
                            <td><a href="{{ route('assessments.recommendations.show', [$assessment, $rec]) }}">{{ Str::limit($rec->title, 60) }}</a></td>
                            <td>{{ $rec->responsiblePerson->name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $rec->status_badge }}">{{ ucfirst(str_replace('_', ' ', $rec->status)) }}</span></td>
                            <td><div class="progress progress-sm"><div class="progress-bar bg-{{ $rec->progress_bar_color }}" style="width: {{ $rec->progress_percentage }}%"></div></div><small>{{ $rec->progress_percentage }}%</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
