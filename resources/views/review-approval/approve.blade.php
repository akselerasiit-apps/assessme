@extends('layouts.app')

@section('title', 'Final Approval')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Super Admin - Final Approval</div>
                <h2 class="page-title">{{ $assessment->title }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <a href="{{ route('review-approval.pending-approval') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Pending Approvals
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
                        <label class="form-label">Reviewed By</label>
                        <div>
                            @if($assessment->reviewedBy)
                                {{ $assessment->reviewedBy->name }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
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
                            <span class="badge bg-purple">{{ ucfirst($assessment->status) }}</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Overall Maturity</label>
                        <div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Form -->
        <div class="col-md-8">
            <!-- GAMO Scores Summary -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">GAMO Scores Summary</h3>
                </div>
                <div class="card-body">
                    @if($assessment->gamoScores->isEmpty())
                        <div class="text-muted">No scores available.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-vcenter">
                                <thead>
                                    <tr>
                                        <th>GAMO</th>
                                        <th>Objective</th>
                                        <th class="text-center">Current</th>
                                        <th class="text-center">Target</th>
                                        <th class="text-center">Gap</th>
                                        <th class="text-center">Status</th>
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
                                            <div class="text-truncate" style="max-width: 250px;">
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
                                            <span class="badge bg-azure">{{ number_format($score->target_maturity_level, 1) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $gap = $score->target_maturity_level - $score->current_maturity_level;
                                            @endphp
                                            <span class="badge {{ $gap > 0 ? 'bg-warning' : 'bg-success' }}">
                                                {{ $gap > 0 ? '+' : '' }}{{ number_format($gap, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $score->isTargetMet() ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $score->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Form Card -->
            <div class="card">
                <div class="card-header bg-purple-lt">
                    <h3 class="card-title">Final Approval Decision</h3>
                </div>
                <form action="{{ route('review-approval.submit-approval', $assessment) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-warning mb-3">
                            <h4 class="alert-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
                                Super Admin Action Required
                            </h4>
                            <div class="text-muted">
                                This assessment has been reviewed by {{ $assessment->reviewedBy?->name ?? 'a reviewer' }}. 
                                As Super Admin, you have the final authority to approve or reject this assessment.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Approval Notes</label>
                            <textarea name="approval_notes" class="form-control @error('approval_notes') is-invalid @enderror" rows="6" placeholder="Provide your approval/rejection notes and final comments..." required>{{ old('approval_notes') }}</textarea>
                            @error('approval_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Minimum 10 characters. Explain your final decision.</small>
                        </div>

                        <div class="alert alert-info mb-3">
                            <h4 class="alert-title">Approval Actions</h4>
                            <div class="text-muted">
                                <strong>Approve:</strong> Assessment will be marked as approved and finalized.<br>
                                <strong>Reject:</strong> Assessment will be returned to the creator for corrections.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Final Decision</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="approve" class="form-selectgroup-input" required>
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                        Approve Assessment
                                    </span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="reject" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                        Reject & Send for Revision
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('review-approval.pending-approval') }}" class="btn btn-link">Cancel</a>
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                            Submit Final Decision
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-primary w-100">
                                View Full Assessment
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('scoring.index', $assessment) }}" class="btn btn-outline-secondary w-100">
                                View Scoring
                            </a>
                        </div>
                        <div class="col-4">
                            <a href="{{ route('review-approval.history', $assessment) }}" class="btn btn-outline-info w-100">
                                View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
