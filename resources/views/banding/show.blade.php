@extends('layouts.app')

@section('title', 'Banding Detail')

@section('content')
<div class="container-xl">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-pretitle">Banding Request</div>
                <h2 class="page-title">{{ $banding->gamoObjective->gamo_code }} - Round {{ $banding->banding_round }}</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('banding.index', $assessment) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
                        Back to Bandings
                    </a>
                    @if($banding->status === 'draft' && ($banding->initiated_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin'])))
                    <form action="{{ route('banding.submit', [$assessment, $banding]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Submit this banding for approval?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l7 7l7 -7"></path></svg>
                            Submit for Approval
                        </button>
                    </form>
                    @endif
                    @if($banding->status === 'draft' && ($banding->initiated_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Admin'])))
                    <form action="{{ route('banding.destroy', [$assessment, $banding]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this draft banding?')">
                            Delete Draft
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Status Card -->
            <div class="card mb-3">
                <div class="card-header bg-{{ $banding->status === 'approved' ? 'success' : ($banding->status === 'rejected' ? 'danger' : ($banding->status === 'submitted' ? 'warning' : 'secondary')) }}-lt">
                    <h3 class="card-title">Status: 
                        <span class="badge 
                            @if($banding->status === 'approved') bg-success
                            @elseif($banding->status === 'rejected') bg-danger
                            @elseif($banding->status === 'submitted') bg-warning
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($banding->status) }}
                        </span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($banding->status === 'draft')
                    <div class="alert alert-info mb-0">
                        This banding is still in draft. Review the details and submit when ready.
                    </div>
                    @elseif($banding->status === 'submitted')
                    <div class="alert alert-warning mb-0">
                        This banding has been submitted and is waiting for admin review.
                    </div>
                    @elseif($banding->status === 'approved')
                    <div class="alert alert-success mb-0">
                        <strong>Approved by:</strong> {{ $banding->approvedBy->name }}<br>
                        <strong>Date:</strong> {{ $banding->updated_at->format('d M Y, H:i') }}<br>
                        @if($banding->approval_notes)
                        <strong>Notes:</strong> {{ $banding->approval_notes }}
                        @endif
                    </div>
                    @elseif($banding->status === 'rejected')
                    <div class="alert alert-danger mb-0">
                        <strong>Rejected by:</strong> {{ $banding->approvedBy->name }}<br>
                        <strong>Date:</strong> {{ $banding->updated_at->format('d M Y, H:i') }}<br>
                        @if($banding->approval_notes)
                        <strong>Notes:</strong> {{ $banding->approval_notes }}
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Banding Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Banding Request Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">GAMO Objective</label>
                        <div>
                            <span class="badge bg-{{ $banding->gamoObjective->category === 'EDM' ? 'purple' : ($banding->gamoObjective->category === 'APO' ? 'blue' : ($banding->gamoObjective->category === 'BAI' ? 'green' : ($banding->gamoObjective->category === 'DSS' ? 'orange' : 'pink'))) }}-lt me-2">
                                {{ $banding->gamoObjective->gamo_code }}
                            </span>
                            {{ $banding->gamoObjective->objective_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Old Maturity Level</label>
                            <div>
                                @if($banding->old_maturity_level)
                                    <span class="badge 
                                        @if($banding->old_maturity_level >= 4) bg-success
                                        @elseif($banding->old_maturity_level >= 3) bg-info
                                        @elseif($banding->old_maturity_level >= 2) bg-warning
                                        @elseif($banding->old_maturity_level >= 1) bg-danger
                                        @else bg-secondary
                                        @endif" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        {{ number_format($banding->old_maturity_level, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proposed New Maturity Level</label>
                            <div>
                                @if($banding->new_maturity_level)
                                    <span class="badge 
                                        @if($banding->new_maturity_level >= 4) bg-success
                                        @elseif($banding->new_maturity_level >= 3) bg-info
                                        @elseif($banding->new_maturity_level >= 2) bg-warning
                                        @elseif($banding->new_maturity_level >= 1) bg-danger
                                        @else bg-secondary
                                        @endif" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        {{ number_format($banding->new_maturity_level, 2) }}
                                    </span>
                                    @php
                                        $improvement = $banding->getMaturityImprovement();
                                    @endphp
                                    @if($improvement !== null && $improvement != 0)
                                        <span class="badge bg-cyan ms-2" style="font-size: 1rem; padding: 0.4rem 0.8rem;">
                                            {{ $improvement > 0 ? '+' : '' }}{{ number_format($improvement, 2) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Banding Reason</label>
                        <div class="fw-bold">{{ $banding->banding_reason }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description</label>
                        <div class="card card-sm bg-light">
                            <div class="card-body">
                                {{ $banding->banding_description }}
                            </div>
                        </div>
                    </div>

                    @if($banding->revised_answers)
                    <div class="mb-3">
                        <label class="form-label">Revised Answers</label>
                        <div class="card card-sm bg-light">
                            <div class="card-body">
                                {{ $banding->revised_answers }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Old Evidence Count</label>
                            <div class="h3 mb-0">{{ $banding->old_evidence_count ?? 0 }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Evidence Count</label>
                            <div class="h3 mb-0">
                                {{ $banding->new_evidence_count ?? 0 }}
                                @php
                                    $evidenceImprovement = $banding->getEvidenceImprovement();
                                @endphp
                                @if($evidenceImprovement > 0)
                                    <span class="badge bg-cyan">+{{ $evidenceImprovement }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($banding->additional_evidence_files)
                    <div class="mb-0">
                        <label class="form-label">Additional Evidence File</label>
                        <div>
                            <a href="{{ route('banding.download-evidence', [$assessment, $banding]) }}" class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path><path d="M7 11l5 5l5 -5"></path><path d="M12 4l0 12"></path></svg>
                                Download Evidence
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Form (Admin/Super Admin only) -->
            @if($banding->status === 'submitted' && auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
            <div class="card">
                <div class="card-header bg-warning-lt">
                    <h3 class="card-title">Review & Approval</h3>
                </div>
                <form action="{{ route('banding.process-approval', [$assessment, $banding]) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label required">Approval Notes</label>
                            <textarea name="approval_notes" class="form-control @error('approval_notes') is-invalid @enderror" 
                                      rows="4" placeholder="Provide notes about your decision..." required>{{ old('approval_notes') }}</textarea>
                            @error('approval_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">Minimum 10 characters. Explain your decision.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Decision</label>
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="approve" class="form-selectgroup-input" required>
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                        Approve Banding
                                    </span>
                                </label>
                                <label class="form-selectgroup-item">
                                    <input type="radio" name="action" value="reject" class="form-selectgroup-input">
                                    <span class="form-selectgroup-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                        Reject Banding
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            Submit Decision
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Request Information</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Assessment</label>
                        <div>
                            <a href="{{ route('assessments.show', $assessment) }}" class="text-decoration-none">
                                {{ $assessment->code }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banding Round</label>
                        <div><span class="badge badge-outline">Round {{ $banding->banding_round }}</span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Initiated By</label>
                        <div>{{ $banding->initiatedBy->name }}</div>
                        <small class="text-muted">{{ $banding->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Last Updated</label>
                        <div>{{ $banding->updated_at->format('d M Y, H:i') }}</div>
                        <small class="text-muted">{{ $banding->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
