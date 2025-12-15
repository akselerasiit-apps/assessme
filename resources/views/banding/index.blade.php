@extends('layouts.app')

@section('title', 'Banding Requests')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Assessment: {{ $assessment->code }}</div>
                <h2 class="page-title">Banding / Appeal Requests</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Assessment
                    </a>
                    @if(in_array($assessment->status, ['reviewed', 'approved']))
                    <a href="{{ route('banding.create', $assessment) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                        New Banding Request
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Bandings</div>
                    </div>
                    <div class="h1 mb-0">{{ $statistics['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Draft</div>
                    </div>
                    <div class="h1 mb-0 text-secondary">{{ $statistics['draft'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Pending Approval</div>
                    </div>
                    <div class="h1 mb-0 text-warning">{{ $statistics['submitted'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Approved</div>
                    </div>
                    <div class="h1 mb-0 text-success">{{ $statistics['approved'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bandings Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Banding Requests</h3>
        </div>

        @if($bandings->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 14l2 2l4 -4"></path></svg>
            </div>
            <p class="empty-title">No banding requests yet</p>
            <p class="empty-subtitle text-muted">
                Create a new banding request to appeal a maturity score.
            </p>
            @if(in_array($assessment->status, ['reviewed', 'approved']))
            <div class="empty-action">
                <a href="{{ route('banding.create', $assessment) }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    New Banding Request
                </a>
            </div>
            @endif
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>GAMO</th>
                        <th>Round</th>
                        <th>Reason</th>
                        <th class="text-center">Old Maturity</th>
                        <th class="text-center">New Maturity</th>
                        <th class="text-center">Improvement</th>
                        <th>Initiated By</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bandings as $banding)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $banding->gamoObjective->category === 'EDM' ? 'purple' : ($banding->gamoObjective->category === 'APO' ? 'blue' : ($banding->gamoObjective->category === 'BAI' ? 'green' : ($banding->gamoObjective->category === 'DSS' ? 'orange' : 'pink'))) }}-lt">
                                {{ $banding->gamoObjective->gamo_code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-outline">Round {{ $banding->banding_round }}</span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;">
                                {{ $banding->banding_reason }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($banding->old_maturity_level)
                                <span class="badge 
                                    @if($banding->old_maturity_level >= 4) bg-success
                                    @elseif($banding->old_maturity_level >= 3) bg-info
                                    @elseif($banding->old_maturity_level >= 2) bg-warning
                                    @elseif($banding->old_maturity_level >= 1) bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ number_format($banding->old_maturity_level, 1) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($banding->new_maturity_level)
                                <span class="badge 
                                    @if($banding->new_maturity_level >= 4) bg-success
                                    @elseif($banding->new_maturity_level >= 3) bg-info
                                    @elseif($banding->new_maturity_level >= 2) bg-warning
                                    @elseif($banding->new_maturity_level >= 1) bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ number_format($banding->new_maturity_level, 1) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $improvement = $banding->getMaturityImprovement();
                            @endphp
                            @if($improvement !== null)
                                <span class="badge {{ $improvement > 0 ? 'bg-cyan' : 'bg-secondary' }}">
                                    {{ $improvement > 0 ? '+' : '' }}{{ number_format($improvement, 1) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-muted small">{{ $banding->initiatedBy->name }}</div>
                        </td>
                        <td>
                            <span class="badge 
                                @if($banding->status === 'approved') bg-success
                                @elseif($banding->status === 'rejected') bg-danger
                                @elseif($banding->status === 'submitted') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($banding->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $banding->created_at->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('banding.show', [$assessment, $banding]) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                                @if($banding->status === 'draft' && ($banding->initiated_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin'])))
                                <form action="{{ route('banding.submit', [$assessment, $banding]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Submit this banding for approval?')">
                                        Submit
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($bandings->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                Showing <span>{{ $bandings->firstItem() }}</span> to <span>{{ $bandings->lastItem() }}</span> of <span>{{ $bandings->total() }}</span> entries
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $bandings->links() }}
            </ul>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
