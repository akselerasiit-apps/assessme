@extends('layouts.app')

@section('title', 'Add Company')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Add New Company</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.companies.index') }}" class="btn btn-ghost-secondary">
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
                <form action="{{ route('master-data.companies.store') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Company Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label required">Company Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Industry</label>
                                    <input type="text" name="industry" class="form-control @error('industry') is-invalid @enderror" 
                                           value="{{ old('industry') }}" placeholder="e.g., Technology, Finance, Healthcare">
                                    @error('industry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label required">Company Size</label>
                                    <select name="size" class="form-select @error('size') is-invalid @enderror" required>
                                        <option value="">Select Size</option>
                                        <option value="startup" {{ old('size') == 'startup' ? 'selected' : '' }}>Startup</option>
                                        <option value="sme" {{ old('size') == 'sme' ? 'selected' : '' }}>SME</option>
                                        <option value="enterprise" {{ old('size') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                    </select>
                                    @error('size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Established Year</label>
                                    <input type="number" name="established_year" class="form-control @error('established_year') is-invalid @enderror" 
                                           value="{{ old('established_year') }}" min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                                    @error('established_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <a href="{{ route('master-data.companies.index') }}" class="btn btn-ghost-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>Save Company
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
