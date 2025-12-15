@extends('layouts.app')

@section('title', 'Add Design Factor')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Add New Design Factor</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.design-factors.index') }}" class="btn btn-ghost-secondary">
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
                <form action="{{ route('master-data.design-factors.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Design Factor Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Code</label>
                                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code') }}" required autofocus placeholder="e.g., DF01">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Unique identifier for this design factor</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="factor_order" class="form-control @error('factor_order') is-invalid @enderror" 
                                           value="{{ old('factor_order') }}" min="1" placeholder="1">
                                    @error('factor_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-hint">Order in which this factor appears</small>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label required">Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <span class="form-check-label">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('master-data.design-factors.index') }}" class="btn btn-ghost-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>Save Design Factor
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
