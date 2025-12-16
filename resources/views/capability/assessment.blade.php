@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        {{ $gamo->code }}
                    </div>
                    <h2 class="page-title">
                        {{ $gamo->name_en }}
                    </h2>
                    @if($gamo->name_id)
                    <div class="text-muted mt-1">{{ $gamo->name_id }}</div>
                    @endif
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('capability.index', $assessment) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to GAMO Selection
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Level Cards -->
            <div class="row mb-4">
                @for($level = 0; $level <= 5; $level++)
                    @php
                        $progress = $levelProgress[$level] ?? ['total' => 0, 'answered' => 0, 'percentage' => 0];
                        $isComplete = $progress['percentage'] >= 100;
                        $isPrevComplete = $level == 0 || ($levelProgress[$level - 1]['percentage'] ?? 0) >= 100;
                        $isLocked = !$isPrevComplete && $level > 0;
                        $levelDef = $capabilityLevels->where('level', $level)->first();
                    @endphp
                    <div class="col-md-4 col-lg-2">
                        <div class="card {{ $isLocked ? 'opacity-50' : '' }}" data-level="{{ $level }}">
                            <div class="card-body p-3 text-center">
                                <div class="mb-2">
                                    @if($isLocked)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /></svg>
                                    @elseif($isComplete)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                    @endif
                                </div>
                                <div class="fw-bold">Level {{ $level }}</div>
                                <div class="text-muted small">{{ $levelDef->level_name ?? 'N/A' }}</div>
                                <div class="mt-2">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar {{ $isComplete ? 'bg-success' : 'bg-primary' }}" 
                                             style="width: {{ $progress['percentage'] }}%"
                                             role="progressbar"
                                             aria-valuenow="{{ $progress['percentage'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        {{ $progress['answered'] }}/{{ $progress['total'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Tabs Navigation -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                        <li class="nav-item">
                            <a href="#tab-levels" class="nav-link active" data-bs-toggle="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12l.01 0" /><path d="M13 12l2 0" /><path d="M9 16l.01 0" /><path d="M13 16l2 0" /></svg>
                                Activities by Level
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-pbc" class="nav-link" data-bs-toggle="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                PBC List
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-repository" class="nav-link" data-bs-toggle="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" /><path d="M10 12l4 0" /></svg>
                                Evidence Repository
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#tab-summary" class="nav-link" data-bs-toggle="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3v18h18" /><path d="M20 18v3" /><path d="M16 16v5" /><path d="M12 13v8" /><path d="M8 16v5" /><path d="M3 11l6 -6l4 4l7 -7" /></svg>
                                Summary
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Activities Tab -->
                        <div class="tab-pane active show" id="tab-levels">
                            @include('capability.activities', ['questions' => $questions, 'assessment' => $assessment, 'gamo' => $gamo])
                        </div>

                        <!-- PBC Tab -->
                        <div class="tab-pane" id="tab-pbc">
                            @include('capability.pbc', ['assessment' => $assessment, 'gamo' => $gamo])
                        </div>

                        <!-- Repository Tab -->
                        <div class="tab-pane" id="tab-repository">
                            @include('capability.repository', ['assessment' => $assessment, 'gamo' => $gamo])
                        </div>

                        <!-- Summary Tab -->
                        <div class="tab-pane" id="tab-summary">
                            @include('capability.summary', ['assessment' => $assessment, 'gamo' => $gamo, 'levelProgress' => $levelProgress])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Evidence Detail Modal -->
<div class="modal modal-blur fade" id="evidenceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evidence Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="evidenceModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">Loading evidence details...</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle achievement status change
    document.querySelectorAll('.achievement-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const answerId = this.dataset.answerId;
            const level = this.dataset.level;
            const status = this.value;
            
            if (!status) return;
            
            // Update capability score via AJAX
            fetch('{{ route("capability.update-score", $assessment) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    answer_id: answerId,
                    level: level,
                    achievement_status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('Success', data.message, 'success');
                    
                    // Reload page to update progress
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', 'Failed to update capability score', 'danger');
            });
        });
    });
    
    // Handle view evidence button click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.view-evidence-btn')) {
            e.preventDefault();
            const button = e.target.closest('.view-evidence-btn');
            const answerId = button.dataset.answerId;
            const questionCode = button.dataset.questionCode;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('evidenceModal'));
            modal.show();
            
            // Fetch evidence details
            fetch(`/api/assessments/{{ $assessment->id }}/answers/${answerId}/evidence`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    
                    if (data.answer) {
                        html += `
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h4 class="card-title">Question: ${questionCode}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Question Text (EN):</label>
                                        <p>${data.question.question_text_en || 'N/A'}</p>
                                    </div>
                                    ${data.question.question_text_id ? `
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Question Text (ID):</label>
                                            <p>${data.question.question_text_id}</p>
                                        </div>
                                    ` : ''}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Answer:</label>
                                        <p>${data.answer.answer_text || 'No answer text provided'}</p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Answered By:</label>
                                            <p>${data.answered_by ? data.answered_by.name : 'Unknown'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Answered At:</label>
                                            <p>${data.answer.answered_at ? new Date(data.answer.answered_at).toLocaleString() : 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        if (data.answer.evidence_provided && data.answer.evidence_file_path) {
                            html += `
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Evidence File</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col">
                                                <div class="d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-primary me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /></svg>
                                                    <div>
                                                        <div class="fw-bold">${data.answer.evidence_file_path.split('/').pop()}</div>
                                                        <div class="text-muted small">Evidence document</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="/assessments/{{ $assessment->id }}/evidence/${answerId}/download" 
                                                   class="btn btn-primary" 
                                                   target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                        ${data.answer.evidence_description ? `
                                            <div class="mt-3">
                                                <label class="form-label fw-bold">Evidence Description:</label>
                                                <p>${data.answer.evidence_description}</p>
                                            </div>
                                        ` : ''}
                                        ${data.answer.evidence_url ? `
                                            <div class="mt-3">
                                                <label class="form-label fw-bold">Evidence URL:</label>
                                                <p><a href="${data.answer.evidence_url}" target="_blank" class="text-primary">${data.answer.evidence_url}</a></p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        } else if (data.answer.evidence_url) {
                            html += `
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title">Evidence Link</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">URL:</label>
                                            <p><a href="${data.answer.evidence_url}" target="_blank" class="text-primary">${data.answer.evidence_url}</a></p>
                                        </div>
                                        ${data.answer.evidence_description ? `
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description:</label>
                                                <p>${data.answer.evidence_description}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        }
                        
                        if (data.capability_scores && data.capability_scores.length > 0) {
                            html += `
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Capability Scores by Level</h4>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>Level</th>
                                                    <th>Achievement Status</th>
                                                    <th>Compliance</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;
                            
                            data.capability_scores.forEach(score => {
                                const statusBadge = {
                                    'FULLY_ACHIEVED': '<span class="badge bg-success">Fully Achieved</span>',
                                    'LARGELY_ACHIEVED': '<span class="badge bg-info">Largely Achieved</span>',
                                    'PARTIALLY_ACHIEVED': '<span class="badge bg-warning">Partially Achieved</span>',
                                    'NOT_ACHIEVED': '<span class="badge bg-danger">Not Achieved</span>'
                                };
                                
                                html += `
                                    <tr>
                                        <td><span class="badge bg-primary">Level ${score.level}</span></td>
                                        <td>${statusBadge[score.achievement_status] || 'N/A'}</td>
                                        <td>
                                            <div class="progress" style="height: 20px; min-width: 100px;">
                                                <div class="progress-bar ${score.compliance_percentage >= 75 ? 'bg-success' : (score.compliance_percentage >= 50 ? 'bg-warning' : 'bg-danger')}" 
                                                     style="width: ${score.compliance_percentage}%">
                                                    ${score.compliance_percentage}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>${score.assessment_notes || '-'}</td>
                                    </tr>
                                `;
                            });
                            
                            html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                        }
                    } else {
                        html = `
                            <div class="empty">
                                <p class="empty-title">No evidence found</p>
                                <p class="empty-subtitle text-muted">Evidence details are not available for this question.</p>
                            </div>
                        `;
                    }
                    
                    document.getElementById('evidenceModalBody').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('evidenceModalBody').innerHTML = `
                        <div class="alert alert-danger">
                            <h4 class="alert-title">Error</h4>
                            <div class="text-muted">Failed to load evidence details. Please try again.</div>
                        </div>
                    `;
                });
        }
    });
    
    function showToast(title, message, type) {
        // Simple toast notification (you can replace with your preferred toast library)
        alert(title + ': ' + message);
    }
});
</script>
@endpush
@endsection
