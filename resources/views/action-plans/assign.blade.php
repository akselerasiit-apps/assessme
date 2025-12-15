@extends('layouts.app')

@section('title', 'Assign Recommendations - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><a href="{{ route('assessments.action-plans.index', $assessment) }}">Action Plans</a></div>
                <h2 class="page-title">Bulk Assign Recommendations</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible"><div class="d-flex"><div><svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg></div><div>{{ session('success') }}</div></div><a class="btn-close" data-bs-dismiss="alert"></a></div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('assessments.action-plans.assign', $assessment) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Select Recommendations to Assign</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Assign To</label>
                                <select name="responsible_person_id" class="form-select @error('responsible_person_id') is-invalid @enderror" required>
                                    <option value="">Select user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('responsible_person_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Target Date (Optional)</label>
                                <input type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('target_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label required">Select Recommendations</label>
                                @forelse($recommendations as $rec)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="recommendations[]" value="{{ $rec->id }}" id="rec{{ $rec->id }}">
                                    <label class="form-check-label" for="rec{{ $rec->id }}">
                                        <span class="badge bg-{{ $rec->priority_badge }}">{{ ucfirst($rec->priority) }}</span>
                                        <span class="badge bg-blue-lt">{{ $rec->gamoObjective->code }}</span>
                                        {{ $rec->title }}
                                    </label>
                                </div>
                                @empty
                                <p class="text-muted">All recommendations have been assigned.</p>
                                @endforelse
                                @error('recommendations')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('assessments.action-plans.index', $assessment) }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary" {{ $recommendations->isEmpty() ? 'disabled' : '' }}>Assign Selected</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-sm w-100 mb-2" onclick="selectAll()">Select All</button>
                        <button type="button" class="btn btn-sm w-100 mb-2" onclick="selectNone()">Deselect All</button>
                        <button type="button" class="btn btn-sm w-100 mb-2" onclick="selectPriority('critical')">Select Critical Only</button>
                        <button type="button" class="btn btn-sm w-100" onclick="selectPriority('high')">Select High Priority</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('input[name="recommendations[]"]').forEach(cb => cb.checked = true);
}
function selectNone() {
    document.querySelectorAll('input[name="recommendations[]"]').forEach(cb => cb.checked = false);
}
function selectPriority(priority) {
    selectNone();
    document.querySelectorAll(`label:has(.badge.bg-${priority === 'critical' ? 'danger' : 'warning'})`).forEach(label => {
        const checkbox = label.closest('.form-check').querySelector('input[type="checkbox"]');
        if (checkbox) checkbox.checked = true;
    });
}
</script>
@endsection
