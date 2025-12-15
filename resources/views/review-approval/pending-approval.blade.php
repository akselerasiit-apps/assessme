@extends('layouts.app')

@section('title', 'Assessments Pending Approval')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Super Admin</div>
                <h2 class="page-title">Assessments Pending Final Approval</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Assessments
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('review-approval.pending-approval') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by code or title..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Final Approval ({{ $pendingAssessments->total() }})</h3>
        </div>

        @if($pendingAssessments->isEmpty())
        <div class="empty">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 12l2 2l4 -4"></path></svg>
            </div>
            <p class="empty-title">No assessments pending approval</p>
            <p class="empty-subtitle text-muted">
                All reviewed assessments have been approved.
            </p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Title</th>
                        <th>Company</th>
                        <th>Reviewed By</th>
                        <th class="text-center">Maturity</th>
                        <th class="text-center">Progress</th>
                        <th>Reviewed At</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAssessments as $assessment)
                    <tr>
                        <td>
                            <span class="badge bg-purple-lt">{{ $assessment->code }}</span>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;">
                                {{ $assessment->title }}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2">{{ substr($assessment->company->name, 0, 2) }}</span>
                                <div>{{ $assessment->company->name }}</div>
                            </div>
                        </td>
                        <td>
                            @if($assessment->reviewedBy)
                                <div class="text-muted">
                                    {{ $assessment->reviewedBy->name }}
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($assessment->overall_maturity_level)
                                <span class="badge 
                                    @if($assessment->overall_maturity_level >= 4) bg-success
                                    @elseif($assessment->overall_maturity_level >= 3) bg-info
                                    @elseif($assessment->overall_maturity_level >= 2) bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ number_format($assessment->overall_maturity_level, 2) }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-cyan">{{ $assessment->progress_percentage }}%</span>
                        </td>
                        <td>
                            <div class="text-muted small">
                                {{ $assessment->updated_at->format('d M Y, H:i') }}
                            </div>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <a href="{{ route('review-approval.approve', $assessment) }}" class="btn btn-sm btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                    Approve
                                </a>
                                <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($pendingAssessments->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                Showing <span>{{ $pendingAssessments->firstItem() }}</span> to <span>{{ $pendingAssessments->lastItem() }}</span> of <span>{{ $pendingAssessments->total() }}</span> entries
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $pendingAssessments->links() }}
            </ul>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
