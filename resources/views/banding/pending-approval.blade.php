@extends('layouts.app')

@section('title', 'Pending Banding Approvals')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Admin Dashboard</div>
                <h2 class="page-title">Pending Banding Approvals</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('banding.pending-approval') }}" method="GET" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by assessment code or title..." value="{{ request('search') }}">
                </div>
                
                @if(auth()->user()->hasRole('Admin'))
                <div class="col-md-4">
                    <select name="company_id" class="form-select">
                        <option value="">All Companies</option>
                        @foreach(\App\Models\Company::orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bandings Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Approvals ({{ $bandings->total() }})</h3>
        </div>

        @if($bandings->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 12l2 2l4 -4"></path></svg>
            </div>
            <p class="empty-title">No pending banding approvals</p>
            <p class="empty-subtitle text-muted">
                All submitted bandings have been reviewed.
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Assessment</th>
                        <th>Company</th>
                        <th>GAMO</th>
                        <th>Round</th>
                        <th>Reason</th>
                        <th class="text-center">Old → New</th>
                        <th class="text-center">Change</th>
                        <th>Requested By</th>
                        <th>Submitted</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bandings as $banding)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $banding->assessment->code }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 150px;">
                                {{ $banding->assessment->title }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2">{{ substr($banding->assessment->company->name, 0, 2) }}</span>
                                <div class="text-truncate" style="max-width: 120px;">
                                    {{ $banding->assessment->company->name }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $banding->gamoObjective->category === 'EDM' ? 'purple' : ($banding->gamoObjective->category === 'APO' ? 'blue' : ($banding->gamoObjective->category === 'BAI' ? 'green' : ($banding->gamoObjective->category === 'DSS' ? 'orange' : 'pink'))) }}-lt">
                                {{ $banding->gamoObjective->gamo_code }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-outline">{{ $banding->banding_round }}</span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $banding->banding_reason }}">
                                {{ $banding->banding_reason }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <span class="badge 
                                    @if($banding->old_maturity_level >= 4) bg-success
                                    @elseif($banding->old_maturity_level >= 3) bg-info
                                    @elseif($banding->old_maturity_level >= 2) bg-warning
                                    @elseif($banding->old_maturity_level >= 1) bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ number_format($banding->old_maturity_level, 1) }}
                                </span>
                                →
                                <span class="badge 
                                    @if($banding->new_maturity_level >= 4) bg-success
                                    @elseif($banding->new_maturity_level >= 3) bg-info
                                    @elseif($banding->new_maturity_level >= 2) bg-warning
                                    @elseif($banding->new_maturity_level >= 1) bg-danger
                                    @else bg-secondary
                                    @endif">
                                    {{ number_format($banding->new_maturity_level, 1) }}
                                </span>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $improvement = $banding->getMaturityImprovement();
                            @endphp
                            @if($improvement !== null)
                                <span class="badge {{ $improvement > 0 ? 'bg-cyan' : ($improvement < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ $improvement > 0 ? '+' : '' }}{{ number_format($improvement, 1) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $banding->initiatedBy->name }}</div>
                            <div class="text-muted small">{{ $banding->initiatedBy->email }}</div>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $banding->created_at->format('d M Y') }}</div>
                            <div class="text-muted small">{{ $banding->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('banding.show', [$banding->assessment, $banding]) }}" class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 12l2 2l4 -4"></path></svg>
                                Review
                            </a>
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
