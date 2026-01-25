<!-- Evidence Repository -->
<div class="row">
    <div class="col-12">
        <h3>Evidence Repository</h3>
        <p class="text-muted">All uploaded evidence for {{ $gamo->code }} capability assessment.</p>

        @php
            $evidences = \App\Models\AssessmentAnswer::where('assessment_id', $assessment->id)
                ->whereHas('question', function($q) use ($gamo) {
                    $q->where('gamo_objective_id', $gamo->id);
                })
                ->where('evidence_provided', true)
                ->with(['question', 'answeredBy'])
                ->latest()
                ->get();
        @endphp

        @if($evidences->isEmpty())
            <div class="empty">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" /><path d="M10 12l4 0" /></svg>
                </div>
                <p class="empty-title">No evidence uploaded yet</p>
                <p class="empty-subtitle text-muted">
                    Upload evidence documents to support your capability assessment.
                </p>
                <div class="empty-action">
                    <a href="{{ route('assessments.evidence.create', $assessment) }}?gamo={{ $gamo->id }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Upload Evidence
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Uploaded Evidence ({{ $evidences->count() }})</h3>
                    <div class="card-actions">
                        <a href="{{ route('assessments.evidence.create', $assessment) }}?gamo={{ $gamo->id }}" class="btn btn-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            Upload More
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Question Code</th>
                                <th>Level</th>
                                <th>Description</th>
                                <th>Evidence Type</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evidences as $evidence)
                            <tr>
                                <td>
                                    <span class="badge bg-primary-lt">{{ $evidence->question->code ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">Level {{ $evidence->question->maturity_level }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;">
                                        {{ Str::limit($evidence->answer_text ?? 'N/A', 100) }}
                                    </div>
                                </td>
                                <td>
                                    @if($evidence->evidence_file_path)
                                        <span class="badge bg-success">File Uploaded</span>
                                    @else
                                        <span class="badge bg-info">Text Evidence</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($evidence->answeredBy->name ?? 'Unknown') }}&background=random)"></span>
                                        {{ $evidence->answeredBy->name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td>
                                    <span title="{{ $evidence->answered_at }}">
                                        {{ $evidence->answered_at ? $evidence->answered_at->diffForHumans() : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @if($evidence->evidence_file_path)
                                        <a href="{{ route('assessments.evidence.download', [$assessment, $evidence]) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Download Evidence">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('assessments.take', $assessment) }}?question={{ $evidence->question_id }}" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
