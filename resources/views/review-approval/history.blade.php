@extends('layouts.app')

@section('title', 'Review & Approval History')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Review & Approval History</div>
                <h2 class="page-title">{{ $assessment->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Current Status Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Current Status</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <div class="fw-bold">{{ $assessment->code }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Company</label>
                        <div>{{ $assessment->company->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div>
                            <span class="badge 
                                @if($assessment->status === 'approved') bg-success
                                @elseif($assessment->status === 'reviewed') bg-purple
                                @elseif($assessment->status === 'completed') bg-cyan
                                @elseif($assessment->status === 'in_progress') bg-info
                                @else bg-secondary
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Created By</label>
                        <div>{{ $assessment->createdBy->name }}</div>
                        <small class="text-muted">{{ $assessment->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    @if($assessment->reviewedBy)
                    <div class="mb-3">
                        <label class="form-label">Reviewed By</label>
                        <div>{{ $assessment->reviewedBy->name }}</div>
                        <small class="text-muted">{{ $assessment->updated_at->format('d M Y, H:i') }}</small>
                    </div>
                    @endif
                    @if($assessment->approvedBy)
                    <div class="mb-0">
                        <label class="form-label">Approved By</label>
                        <div>{{ $assessment->approvedBy->name }}</div>
                        <small class="text-muted">{{ $assessment->updated_at->format('d M Y, H:i') }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Review & Approval Timeline</h3>
                </div>
                <div class="card-body">
                    @if($activities->isEmpty())
                        <div class="empty">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 8l0 4"></path><path d="M12 16l0 .01"></path></svg>
                            </div>
                            <p class="empty-title">No review/approval activity yet</p>
                            <p class="empty-subtitle text-muted">
                                This assessment hasn't been reviewed or approved yet.
                            </p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($activities as $activity)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar 
                                            @if(str_contains($activity->description, 'approved')) bg-success
                                            @elseif(str_contains($activity->description, 'rejected')) bg-danger
                                            @elseif(str_contains($activity->description, 'revision')) bg-warning
                                            @else bg-info
                                            @endif">
                                            @if(str_contains($activity->description, 'approved'))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                            @elseif(str_contains($activity->description, 'rejected'))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                            @elseif(str_contains($activity->description, 'revision'))
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 12l2 2l4 -4"></path></svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">{{ $activity->description }}</div>
                                        <div class="text-muted small">
                                            By {{ $activity->causer?->name ?? 'System' }} 
                                            • {{ $activity->created_at->format('d M Y, H:i') }}
                                            ({{ $activity->created_at->diffForHumans() }})
                                        </div>
                                        @if($activity->properties && isset($activity->properties['notes']))
                                        <div class="mt-2">
                                            <div class="card card-sm bg-light">
                                                <div class="card-body">
                                                    <strong>Notes:</strong><br>
                                                    {{ $activity->properties['notes'] }}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
