@extends('layouts.app')
@section('title', 'Timeline - ' . $assessment->title)
@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col"><div class="page-pretitle"><a href="{{ route('assessments.action-plans.index', $assessment) }}">Action Plans</a></div><h2 class="page-title">Action Plan Timeline</h2></div>
            <div class="col-auto ms-auto d-print-none"><a href="{{ route('assessments.action-plans.index', $assessment) }}" class="btn btn-outline-primary">Back to Dashboard</a></div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        @if($startDate && $endDate)
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">Timeline Overview</h3><div class="ms-auto text-muted">{{ $startDate->format('M Y') }} - {{ $endDate->format('M Y') }}</div></div>
        </div>
        @endif

        @forelse($timeline as $month => $recs)
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</h3><span class="badge bg-primary">{{ $recs->count() }} items</span></div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter">
                    <thead><tr><th>GAMO</th><th>Action</th><th>Priority</th><th>Owner</th><th>Due Date</th><th>Progress</th></tr></thead>
                    <tbody>
                        @foreach($recs as $rec)
                        <tr>
                            <td><span class="badge bg-blue-lt">{{ $rec->gamoObjective->code }}</span></td>
                            <td><a href="{{ route('assessments.recommendations.show', [$assessment, $rec]) }}">{{ Str::limit($rec->title, 50) }}</a></td>
                            <td><span class="badge bg-{{ $rec->priority_badge }}">{{ ucfirst($rec->priority) }}</span></td>
                            <td>{{ $rec->responsiblePerson->name ?? '-' }}</td>
                            <td>{{ $rec->target_date->format('M d, Y') }}</td>
                            <td><div class="progress progress-sm"><div class="progress-bar" style="width: {{ $rec->progress_percentage }}%"></div></div></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="card"><div class="card-body text-center text-muted py-5">No items with target dates</div></div>
        @endforelse
    </div>
</div>
@endsection
