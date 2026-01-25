@extends('layouts.app')

@section('title', 'Review Assessment')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Review</div>
                <h2 class="page-title">{{ $assessment->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <a href="{{ route('review-approval.pending-review') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Pending Reviews
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Assessment Information -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Assessment Information</h3>
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
                        <label class="form-label">Created By</label>
                        <div>{{ $assessment->createdBy->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Period</label>
                        <div>
                            {{ $assessment->assessment_period_start?->format('d M Y') }} - 
                            {{ $assessment->assessment_period_end?->format('d M Y') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div>
                            <span class="badge bg-cyan">{{ ucfirst($assessment->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">Total Questions</div>
                                <div class="h2 mb-0">{{ $statistics['total_questions'] }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">Answered</div>
                                <div class="h2 mb-0 text-success">{{ $statistics['answered_questions'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Completion Rate</div>
                        <div class="progress">
                            <div class="progress-bar bg-cyan" style="width: {{ $statistics['completion_rate'] }}%" role="progressbar">
                                {{ $statistics['completion_rate'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted">Evidence Files</div>
                        <div class="h3 mb-0">{{ $statistics['evidence_count'] }}</div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted">Average Maturity</div>
                        <div class="h3 mb-0">
                            <span class="badge 
                                @if($statistics['avg_maturity'] >= 4) bg-success
                                @elseif($statistics['avg_maturity'] >= 3) bg-info
                                @elseif($statistics['avg_maturity'] >= 2) bg-warning
                                @else bg-danger
                                @endif">
                                {{ number_format($statistics['avg_maturity'], 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="col-md-8">
            <!-- GAMO Scores Summary -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">GAMO Scores Summary</h3>
                </div>
                <div class="card-body">
                    @if($assessment->gamoScores->isEmpty())
                        <div class="text-muted">No scores calculated yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter">
                                <thead>
                                    <tr>
                                        <th>GAMO</th>
                                        <th>Objective</th>
                                        <th class="text-center">Maturity</th>
                                        <th class="text-center">Capability</th>
                                        <th class="text-center">Completion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessment->gamoScores->sortBy('gamoObjective.gamo_code') as $score)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ $score->gamoObjective->category === 'EDM' ? 'purple' : ($score->gamoObjective->category === 'APO' ? 'blue' : ($score->gamoObjective->category === 'BAI' ? 'green' : ($score->gamoObjective->category === 'DSS' ? 'orange' : 'pink'))) }}-lt">
                                                {{ $score->gamoObjective->gamo_code }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 300px;">
                                                {{ $score->gamoObjective->objective_name }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge 
                                                @if($score->current_maturity_level >= 4) bg-success
                                                @elseif($score->current_maturity_level >= 3) bg-info
                                                @elseif($score->current_maturity_level >= 2) bg-warning
                                                @elseif($score->current_maturity_level >= 1) bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ number_format($score->current_maturity_level, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($score->current_capability_score, 1) }}%
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-cyan">{{ number_format($score->completion_percentage, 0) }}%</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review Form Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Submit Review</h3>
                </div>
                <form action="{{ route('review-approval.submit-review', $assessment) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Review Notes</label>
                            <textarea name="review_notes" class="form-control @error('review_notes') is-invalid @enderror" rows="6" placeholder="Provide your review comments and recommendations..." required>{{ old('review_notes') }}</textarea>
                            @error('review_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Minimum 10 characters. Explain your decision and any recommendations.</small>
                        </div>

                        <div class="alert alert-info mb-3">
                            <h4 class="alert-title">Review Actions</h4>
                            <div class="text-muted">
                                <strong>Approve for Final Approval:</strong> Assessment will be marked as reviewed and ready for final approval by Super Admin.<br>
                                <strong>Send Back for Revision:</strong> Assessment will be returned to the creator for corrections.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Decision</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="approve" class="form-selectgroup-input" required>
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                        Approve for Final Approval
                                    </span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="revise" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-warning me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path></svg>
                                        Send Back for Revision
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('review-approval.pending-review') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path><path d="M9 12l2 2l4 -4"></path></svg>
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-primary w-100">
                                View Full Assessment
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('scoring.index', $assessment) }}" class="btn btn-outline-secondary w-100">
                                View Scoring Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
