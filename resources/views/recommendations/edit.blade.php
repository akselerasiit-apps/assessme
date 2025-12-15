@extends('layouts.app')

@section('title', 'Edit Recommendation - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.recommendations.index', $assessment) }}">Recommendations</a>
                </div>
                <h2 class="page-title">Edit Recommendation</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('assessments.recommendations.update', [$assessment, $recommendation]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recommendation Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">GAMO Objective</label>
                                <select name="gamo_objective_id" class="form-select @error('gamo_objective_id') is-invalid @enderror" required>
                                    @foreach($gamoObjectives as $gamo)
                                        <option value="{{ $gamo->id }}" {{ old('gamo_objective_id', $recommendation->gamo_objective_id) == $gamo->id ? 'selected' : '' }}>
                                            {{ $gamo->code }} - {{ $gamo->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gamo_objective_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Title</label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $recommendation->title) }}" required maxlength="255">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Description</label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                          required minlength="50">{{ old('description', $recommendation->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Priority</label>
                                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" {{ old('priority', $recommendation->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', $recommendation->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority', $recommendation->priority) == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="critical" {{ old('priority', $recommendation->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="open" {{ old('status', $recommendation->status) == 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="in_progress" {{ old('status', $recommendation->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ old('status', $recommendation->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="closed" {{ old('status', $recommendation->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Estimated Effort</label>
                                        <input type="text" name="estimated_effort" class="form-control @error('estimated_effort') is-invalid @enderror" 
                                               value="{{ old('estimated_effort', $recommendation->estimated_effort) }}">
                                        @error('estimated_effort')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Progress (%)</label>
                                        <input type="number" name="progress_percentage" class="form-control @error('progress_percentage') is-invalid @enderror" 
                                               value="{{ old('progress_percentage', $recommendation->progress_percentage) }}" min="0" max="100">
                                        @error('progress_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Responsible Person</label>
                                        <select name="responsible_person_id" class="form-select @error('responsible_person_id') is-invalid @enderror">
                                            <option value="">Unassigned</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('responsible_person_id', $recommendation->responsible_person_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('responsible_person_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Target Date</label>
                                        <input type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror" 
                                               value="{{ old('target_date', $recommendation->target_date ? $recommendation->target_date->format('Y-m-d') : '') }}">
                                        @error('target_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="{{ route('assessments.recommendations.show', [$assessment, $recommendation]) }}" class="btn btn-link">Cancel</a>
                                <button type="submit" class="btn btn-primary ms-auto">Update Recommendation</button>
                                <button type="button" class="btn btn-danger ms-2" onclick="if(confirm('Delete this recommendation?')) document.getElementById('delete-form').submit();">Delete</button>
                            </div>
                        </div>
                    </div>
                </form>

                <form id="delete-form" action="{{ route('assessments.recommendations.destroy', [$assessment, $recommendation]) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
