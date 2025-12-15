@extends('layouts.app')

@section('title', 'Create Recommendation - ' . $assessment->title)

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <a href="{{ route('assessments.recommendations.index', $assessment) }}">Recommendations</a>
                </div>
                <h2 class="page-title">Create New Recommendation</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('assessments.recommendations.store', $assessment) }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recommendation Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">GAMO Objective</label>
                                <select name="gamo_objective_id" class="form-select @error('gamo_objective_id') is-invalid @enderror" required>
                                    <option value="">Select GAMO...</option>
                                    @foreach($gamoObjectives as $gamo)
                                        <option value="{{ $gamo->id }}" {{ old('gamo_objective_id') == $gamo->id ? 'selected' : '' }}>
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
                                       value="{{ old('title') }}" required maxlength="255">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Description</label>
                                <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                          required minlength="50">{{ old('description') }}</textarea>
                                <small class="form-hint">Minimum 50 characters. Provide detailed explanation of the recommendation.</small>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Priority</label>
                                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Estimated Effort</label>
                                        <input type="text" name="estimated_effort" class="form-control @error('estimated_effort') is-invalid @enderror" 
                                               value="{{ old('estimated_effort') }}" placeholder="e.g., 1-3 months">
                                        @error('estimated_effort')
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
                                                <option value="{{ $user->id }}" {{ old('responsible_person_id') == $user->id ? 'selected' : '' }}>
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
                                               value="{{ old('target_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        @error('target_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('assessments.recommendations.index', $assessment) }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Recommendation</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Guidelines</h3>
                    </div>
                    <div class="card-body">
                        <h4>Creating Effective Recommendations</h4>
                        <ul class="list-unstyled space-y-1">
                            <li><strong>Be Specific:</strong> Clearly state what needs to be done</li>
                            <li><strong>Actionable:</strong> Use action verbs (implement, establish, develop)</li>
                            <li><strong>Measurable:</strong> Include success criteria when possible</li>
                            <li><strong>Realistic:</strong> Consider organizational capacity</li>
                            <li><strong>Time-bound:</strong> Set appropriate target dates</li>
                        </ul>

                        <hr class="my-3">

                        <h4>Priority Guidelines</h4>
                        <ul class="list-unstyled space-y-1">
                            <li><span class="badge bg-danger">Critical</span> - Immediate risk, regulatory requirement</li>
                            <li><span class="badge bg-warning">High</span> - Significant impact, large gap</li>
                            <li><span class="badge bg-info">Medium</span> - Moderate impact</li>
                            <li><span class="badge bg-secondary">Low</span> - Nice to have, minor improvement</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
