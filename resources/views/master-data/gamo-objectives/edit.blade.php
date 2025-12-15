@extends('layouts.app')

@section('title', 'Edit GAMO Objective')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Edit GAMO Objective</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.gamo-objectives.index') }}" class="btn btn-ghost-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form action="{{ route('master-data.gamo-objectives.update', $gamoObjective) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">GAMO Objective Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Code</label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code', $gamoObjective->code) }}" required autofocus>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Unique identifier for this objective</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Category</label>
                                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        <option value="EDM" {{ old('category', $gamoObjective->category) == 'EDM' ? 'selected' : '' }}>EDM - Evaluate, Direct and Monitor</option>
                                        <option value="APO" {{ old('category', $gamoObjective->category) == 'APO' ? 'selected' : '' }}>APO - Align, Plan and Organize</option>
                                        <option value="BAI" {{ old('category', $gamoObjective->category) == 'BAI' ? 'selected' : '' }}>BAI - Build, Acquire and Implement</option>
                                        <option value="DSS" {{ old('category', $gamoObjective->category) == 'DSS' ? 'selected' : '' }}>DSS - Deliver, Service and Support</option>
                                        <option value="MEA" {{ old('category', $gamoObjective->category) == 'MEA' ? 'selected' : '' }}>MEA - Monitor, Evaluate and Assess</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label required">Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $gamoObjective->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $gamoObjective->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="objective_order" class="form-control @error('objective_order') is-invalid @enderror" 
                                           value="{{ old('objective_order', $gamoObjective->objective_order) }}" min="1">
                                    @error('objective_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Order within category</small>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $gamoObjective->is_active) ? 'checked' : '' }}>
                                        <span class="form-check-label">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('master-data.gamo-objectives.index') }}" class="btn btn-ghost-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>Update GAMO Objective
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
