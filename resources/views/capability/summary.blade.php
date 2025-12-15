<!-- Summary Tab -->
<div class="row">
    <div class="col-12">
        <h3>Capability Assessment Summary</h3>
        <p class="text-muted">Overall progress and achievement summary for {{ $gamo->code }}</p>

        <!-- Overall Progress -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Overall Progress</div>
                        </div>
                        @php
                            $totalQuestions = collect($levelProgress)->sum('total');
                            $totalAnswered = collect($levelProgress)->sum('answered');
                            $overallPercentage = $totalQuestions > 0 ? round(($totalAnswered / $totalQuestions) * 100) : 0;
                        @endphp
                        <div class="h1 mb-3">{{ $overallPercentage }}%</div>
                        <div class="d-flex mb-2">
                            <div>Questions Answered</div>
                            <div class="ms-auto">
                                <span class="text-muted">{{ $totalAnswered }}/{{ $totalQuestions }}</span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary" style="width: {{ $overallPercentage }}%" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            @php
                $completedLevels = collect($levelProgress)->filter(function($progress) {
                    return $progress['percentage'] >= 100;
                })->count();
            @endphp
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Completed Levels</div>
                        <div class="h1 mb-3">{{ $completedLevels }}/6</div>
                        <div class="text-muted">Levels with 100% completion</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Current Level</div>
                        <div class="h1 mb-3">{{ $completedLevels }}</div>
                        <div class="text-muted">Highest completed level</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Evidence Provided</div>
                        @php
                            $evidenceCount = \App\Models\AssessmentAnswer::where('assessment_id', $assessment->id)
                                ->whereHas('question', function($q) use ($gamo) {
                                    $q->where('gamo_objective_id', $gamo->id);
                                })
                                ->where('evidence_provided', true)
                                ->count();
                        @endphp
                        <div class="h1 mb-3">{{ $evidenceCount }}</div>
                        <div class="text-muted">Documents uploaded</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level Breakdown -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Progress by Level</h3>
            </div>
            <div class="card-body">
                @for($level = 0; $level <= 5; $level++)
                    @php
                        $progress = $levelProgress[$level];
                        $percentage = $progress['percentage'];
                        $capabilityDef = \App\Models\GamoCapabilityDefinition::where('gamo_objective_id', $gamo->id)
                            ->where('level', $level)
                            ->first();
                    @endphp
                    <div class="row align-items-center mb-3">
                        <div class="col-3">
                            <div class="fw-bold">Level {{ $level }}</div>
                            <div class="text-muted small">{{ $capabilityDef->level_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-7">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar {{ $percentage >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ $percentage }}%
                                </div>
                            </div>
                        </div>
                        <div class="col-2 text-end">
                            <span class="badge {{ $percentage >= 100 ? 'bg-success' : ($percentage > 0 ? 'bg-primary' : 'bg-secondary') }}">
                                {{ $progress['answered'] }}/{{ $progress['total'] }}
                            </span>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Achievement Status Distribution -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Achievement Status Distribution</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $capabilityScores = \App\Models\AssessmentAnswerCapabilityScore::whereHas('assessmentAnswer', function($q) use ($assessment, $gamo) {
                                $q->where('assessment_id', $assessment->id)
                                  ->whereHas('question', function($query) use ($gamo) {
                                      $query->where('gamo_objective_id', $gamo->id);
                                  });
                            })->get();
                            
                            $fullyAchieved = $capabilityScores->where('achievement_status', 'FULLY_ACHIEVED')->count();
                            $largelyAchieved = $capabilityScores->where('achievement_status', 'LARGELY_ACHIEVED')->count();
                            $partiallyAchieved = $capabilityScores->where('achievement_status', 'PARTIALLY_ACHIEVED')->count();
                            $notAchieved = $capabilityScores->where('achievement_status', 'NOT_ACHIEVED')->count();
                            $totalScored = $capabilityScores->count();
                        @endphp
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h1 text-success">{{ $fullyAchieved }}</div>
                                    <div class="text-muted">Fully Achieved</div>
                                    @if($totalScored > 0)
                                        <div class="small text-muted">{{ round(($fullyAchieved / $totalScored) * 100) }}%</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h1 text-info">{{ $largelyAchieved }}</div>
                                    <div class="text-muted">Largely Achieved</div>
                                    @if($totalScored > 0)
                                        <div class="small text-muted">{{ round(($largelyAchieved / $totalScored) * 100) }}%</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h1 text-warning">{{ $partiallyAchieved }}</div>
                                    <div class="text-muted">Partially Achieved</div>
                                    @if($totalScored > 0)
                                        <div class="small text-muted">{{ round(($partiallyAchieved / $totalScored) * 100) }}%</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="h1 text-danger">{{ $notAchieved }}</div>
                                    <div class="text-muted">Not Achieved</div>
                                    @if($totalScored > 0)
                                        <div class="small text-muted">{{ round(($notAchieved / $totalScored) * 100) }}%</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Recommended Next Steps</h3>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @if($overallPercentage < 100)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-primary">1</span>
                                </div>
                                <div class="flex-fill">
                                    <strong>Complete all questions</strong>
                                    <div class="text-muted">{{ $totalQuestions - $totalAnswered }} questions remaining</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($evidenceCount < $totalAnswered)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-warning">2</span>
                                </div>
                                <div class="flex-fill">
                                    <strong>Upload supporting evidence</strong>
                                    <div class="text-muted">Add evidence documents for answered questions</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($completedLevels < 6)
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-info">3</span>
                                </div>
                                <div class="flex-fill">
                                    <strong>Complete Level {{ $completedLevels }} assessment</strong>
                                    <div class="text-muted">Review and rate all activities for the current level</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="list-group-item">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-success">✓</span>
                                </div>
                                <div class="flex-fill">
                                    <strong>Assessment Complete!</strong>
                                    <div class="text-muted">All levels have been assessed. You can now generate reports.</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
