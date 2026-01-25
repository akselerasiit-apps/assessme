<!-- Activities by Level -->
<div class="accordion" id="accordionLevels">
    @for($level = 0; $level <= 5; $level++)
        @php
            $levelQuestions = $questions->get($level, collect());
            $levelDef = $capabilityLevels->where('level', $level)->first() ?? null;
        @endphp
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading-level-{{ $level }}">
                <button class="accordion-button {{ $level == 0 ? '' : 'collapsed' }}" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse-level-{{ $level }}"
                        aria-expanded="{{ $level == 0 ? 'true' : 'false' }}"
                        aria-controls="collapse-level-{{ $level }}">
                    <div class="d-flex w-100 align-items-center">
                        <div class="me-auto">
                            <strong>Level {{ $level }}: {{ $levelDef->level_name ?? 'N/A' }}</strong>
                            <span class="text-muted ms-2">({{ $levelQuestions->count() }} activities)</span>
                        </div>
                        @if($levelProgress[$level]['percentage'] >= 100)
                            <span class="badge bg-success me-3">Complete</span>
                        @elseif($levelProgress[$level]['answered'] > 0)
                            <span class="badge bg-primary me-3">In Progress ({{ $levelProgress[$level]['percentage'] }}%)</span>
                        @else
                            <span class="badge bg-secondary me-3">Not Started</span>
                        @endif
                    </div>
                </button>
            </h2>
            <div id="collapse-level-{{ $level }}" 
                 class="accordion-collapse collapse {{ $level == 0 ? 'show' : '' }}" 
                 aria-labelledby="heading-level-{{ $level }}"
                 data-bs-parent="#accordionLevels">
                <div class="accordion-body">
                    @if($levelDef && $levelDef->guidance_text)
                    <div class="alert alert-info mb-3">
                        <h4>Level Guidance</h4>
                        <p class="mb-0">{!! nl2br(e($levelDef->guidance_text)) !!}</p>
                    </div>
                    @endif

                    @if($levelQuestions->isEmpty())
                        <div class="text-muted text-center py-4">
                            No activities defined for this level
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-vcenter table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 10%">Code</th>
                                        <th style="width: 35%">Management Practice</th>
                                        <th style="width: 5%">Weight</th>
                                        <th style="width: 15%">Achievement Status</th>
                                        <th style="width: 10%">Evidence</th>
                                        <th style="width: 10%">Compliance</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($levelQuestions as $index => $question)
                                        @php
                                            $answer = $question->answers->first();
                                            $capabilityScore = $answer ? $answer->capabilityScores->where('level', $level)->first() : null;
                                            $achievementStatus = $capabilityScore->achievement_status ?? null;
                                            $compliancePercentage = $capabilityScore->compliance_percentage ?? 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge bg-primary-lt">{{ $question->code ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <div class="mb-1">
                                                    <strong>EN:</strong> {{ $question->question_text_en }}
                                                </div>
                                                @if($question->question_text_id)
                                                <div class="text-muted small">
                                                    <strong>ID:</strong> {{ $question->question_text_id }}
                                                </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-outline">{{ $question->weight ?? 1 }}</span>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm achievement-select" 
                                                        data-answer-id="{{ $answer->id ?? '' }}"
                                                        data-level="{{ $level }}"
                                                        {{ !$answer ? 'disabled' : '' }}>
                                                    <option value="">Select...</option>
                                                    <option value="NOT_ACHIEVED" 
                                                        {{ $achievementStatus == 'NOT_ACHIEVED' ? 'selected' : '' }}>
                                                        Not Achieved (0%)
                                                    </option>
                                                    <option value="PARTIALLY_ACHIEVED" 
                                                        {{ $achievementStatus == 'PARTIALLY_ACHIEVED' ? 'selected' : '' }}>
                                                        Partially (50%)
                                                    </option>
                                                    <option value="LARGELY_ACHIEVED" 
                                                        {{ $achievementStatus == 'LARGELY_ACHIEVED' ? 'selected' : '' }}>
                                                        Largely (75%)
                                                    </option>
                                                    <option value="FULLY_ACHIEVED" 
                                                        {{ $achievementStatus == 'FULLY_ACHIEVED' ? 'selected' : '' }}>
                                                        Fully (100%)
                                                    </option>
                                                </select>
                                                @if(!$answer)
                                                <small class="text-muted">Answer question first</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($answer)
                                                    @if($answer->evidence_provided)
                                                        <a href="#" class="badge bg-success text-decoration-none view-evidence-btn" 
                                                           data-answer-id="{{ $answer->id }}"
                                                           data-question-code="{{ $question->code }}"
                                                           title="Click to view evidence details">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                            Provided ({{ $answer->evidence_count ?? 1 }})
                                                        </a>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M12 16v.01" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                                            Pending
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($capabilityScore)
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar {{ $compliancePercentage >= 75 ? 'bg-success' : ($compliancePercentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $compliancePercentage }}%"
                                                             aria-valuenow="{{ $compliancePercentage }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ $compliancePercentage }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($answer)
                                                    <div class="btn-group" role="group">
                                                        @if($answer->evidence_provided)
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-info view-evidence-btn" 
                                                                    data-answer-id="{{ $answer->id }}"
                                                                    data-question-code="{{ $question->code }}"
                                                                    title="View Evidence Details">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('assessments.evidence.index', $assessment) }}?question={{ $question->id }}" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="Manage Evidence">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" /></svg>
                                                        </a>
                                                    </div>
                                                @else
                                                    <a href="{{ route('assessments.take', $assessment) }}?question={{ $question->id }}" 
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="Answer Question">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endfor
</div>
