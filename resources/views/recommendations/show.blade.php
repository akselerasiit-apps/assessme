@extends('layouts.app')

@section('title', $recommendation->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.recommendations.index', $assessment) }}">Recommendations</a>
                </div>
                <h2 class="page-title">{{ $recommendation->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.recommendations.edit', [$assessment, $recommendation]) }}" class="btn btn-primary">
                        Edit
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
                    <div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recommendation Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">GAMO Objective</label>
                                <div><span class="badge bg-blue-lt">{{ $recommendation->gamoObjective->code }}</span> {{ $recommendation->gamoObjective->name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <div><span class="badge bg-{{ $recommendation->priority_badge }}">{{ ucfirst($recommendation->priority) }}</span></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p class="text-muted">{{ $recommendation->description }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div><span class="badge bg-{{ $recommendation->status_badge }}">{{ ucfirst(str_replace('_', ' ', $recommendation->status)) }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Progress</label>
                                <div class="progress progress-sm mb-2">
                                    <div class="progress-bar bg-{{ $recommendation->progress_bar_color }}" style="width: {{ $recommendation->progress_percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $recommendation->progress_percentage }}% Complete</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Responsible Person</label>
                                <div>
                                    @if($recommendation->responsiblePerson)
                                        {{ $recommendation->responsiblePerson->name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Target Date</label>
                                <div>
                                    @if($recommendation->target_date)
                                        {{ $recommendation->target_date->format('F d, Y') }}
                                        @if($recommendation->isOverdue())
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        @elseif($recommendation->isUpcoming())
                                            <span class="badge bg-warning ms-2">Due Soon</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Estimated Effort</label>
                                <div>{{ $recommendation->estimated_effort ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($gamoScore)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">GAMO Context</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h3 mb-1">{{ number_format($gamoScore->current_maturity_level, 2) }}</div>
                                    <div class="text-muted small">Current Maturity</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h3 mb-1">{{ number_format($gamoScore->target_maturity_level, 2) }}</div>
                                    <div class="text-muted small">Target Maturity</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="h3 mb-1 text-danger">{{ number_format($gamoScore->target_maturity_level - $gamoScore->current_maturity_level, 2) }}</div>
                                    <div class="text-muted small">Gap</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Activity History</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($activities as $activity)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar">{{ substr($activity->causer->name ?? 'S', 0, 2) }}</span>
                                </div>
                                <div class="col">
                                    <div class="text-truncate"><strong>{{ $activity->causer->name ?? 'System' }}</strong> {{ $activity->description }}</div>
                                    <div class="text-muted">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">No activity recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
