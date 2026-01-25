@extends('layouts.app')

@section('title', 'Schedule & Timeline - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.show', $assessment) }}">{{ $assessment->code }}</a>
                </div>
                <h2 class="page-title">Assessment Schedule & Timeline</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                    Back to Assessment
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <div class="d-flex"><div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div><div>{{ session('success') }}</div></div>
                <a class="btn-close" data-bs-dismiss="alert"></a>
            </div>
        @endif

        <!-- Timeline Settings -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Assessment Timeline</h3>
                    </div>
                    <form action="{{ route('assessments.schedule.update', $assessment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Start Date</label>
                                    <input type="date" name="assessment_period_start" 
                                           class="form-control @error('assessment_period_start') is-invalid @enderror" 
                                           value="{{ old('assessment_period_start', $assessment->assessment_period_start?->format('Y-m-d')) }}" 
                                           required>
                                    @error('assessment_period_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">End Date</label>
                                    <input type="date" name="assessment_period_end" 
                                           class="form-control @error('assessment_period_end') is-invalid @enderror" 
                                           value="{{ old('assessment_period_end', $assessment->assessment_period_end?->format('Y-m-d')) }}" 
                                           required>
                                    @error('assessment_period_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @php
                                $duration = $assessment->assessment_period_start && $assessment->assessment_period_end
                                    ? $assessment->assessment_period_start->diffInDays($assessment->assessment_period_end)
                                    : null;
                            @endphp

                            @if($duration)
                            <div class="alert alert-info">
                                <strong>Duration:</strong> {{ $duration }} days 
                                ({{ round($duration / 7, 1) }} weeks)
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="schedule_notes" rows="3" class="form-control" placeholder="Special notes about schedule, holidays, or constraints">{{ old('schedule_notes') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">Update Timeline</button>
                        </div>
                    </form>
                </div>

                <!-- Milestones -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Key Milestones</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        @if($assessment->assessment_period_start && $assessment->assessment_period_end)
                            @php
                                $start = $assessment->assessment_period_start;
                                $end = $assessment->assessment_period_end;
                                $duration = $start->diffInDays($end);
                                
                                $milestones = [
                                    ['name' => 'Assessment Start', 'date' => $start, 'percent' => 0],
                                    ['name' => 'Initial Interviews', 'date' => $start->copy()->addDays(ceil($duration * 0.2)), 'percent' => 20],
                                    ['name' => 'Evidence Collection', 'date' => $start->copy()->addDays(ceil($duration * 0.4)), 'percent' => 40],
                                    ['name' => 'Scoring & Analysis', 'date' => $start->copy()->addDays(ceil($duration * 0.7)), 'percent' => 70],
                                    ['name' => 'Review & Approval', 'date' => $start->copy()->addDays(ceil($duration * 0.9)), 'percent' => 90],
                                    ['name' => 'Final Report', 'date' => $end, 'percent' => 100],
                                ];
                            @endphp

                            @foreach($milestones as $milestone)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($milestone['name']) }}&background=206bc4&color=fff)"></span>
                                    </div>
                                    <div class="col">
                                        <div class="text-truncate"><strong>{{ $milestone['name'] }}</strong></div>
                                        <div class="text-muted">{{ $milestone['date']->format('F d, Y') }} ({{ $milestone['date']->diffForHumans() }})</div>
                                    </div>
                                    <div class="col-auto">
                                        @if($milestone['date']->isPast())
                                            <span class="badge bg-success">Passed</span>
                                        @elseif($milestone['date']->isToday())
                                            <span class="badge bg-warning">Today</span>
                                        @else
                                            <span class="badge bg-info">Upcoming</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="list-group-item text-center text-muted py-4">
                                Set start and end dates to see milestones
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Summary & Progress -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Timeline Summary</h3>
                    </div>
                    <div class="card-body">
                        @if($assessment->assessment_period_start && $assessment->assessment_period_end)
                            <div class="mb-3">
                                <div class="text-muted small">Start Date</div>
                                <div class="h3">{{ $assessment->assessment_period_start->format('M d, Y') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">End Date</div>
                                <div class="h3">{{ $assessment->assessment_period_end->format('M d, Y') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">Total Duration</div>
                                <div class="h3">{{ $duration }} days</div>
                            </div>

                            @php
                                $today = now();
                                $elapsed = $start->diffInDays($today);
                                $progress = min(100, ($elapsed / $duration) * 100);
                            @endphp

                            @if($today->between($start, $end))
                            <div class="mb-3">
                                <div class="text-muted small mb-1">Time Progress</div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $progress }}%" role="progressbar">
                                        {{ round($progress) }}%
                                    </div>
                                </div>
                                <small class="text-muted">{{ $end->diffInDays(now()) }} days remaining</small>
                            </div>
                            @elseif($today->lt($start))
                            <div class="alert alert-info">
                                Starts in {{ $start->diffInDays(now()) }} days
                            </div>
                            @else
                            <div class="alert alert-secondary">
                                Completed {{ $end->diffInDays(now()) }} days ago
                            </div>
                            @endif
                        @else
                            <p class="text-muted">No timeline set yet. Use the form to define start and end dates.</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('assessments.team.index', $assessment) }}" class="btn btn-outline-primary">
                                Manage Team
                            </a>
                            <a href="{{ route('assessments.take', $assessment) }}" class="btn btn-outline-primary">
                                Start Assessment
                            </a>
                            <a href="{{ route('assessments.progress', $assessment) }}" class="btn btn-outline-primary">
                                View Progress
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
