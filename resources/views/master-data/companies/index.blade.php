@extends('layouts.app')

@section('title', 'Companies Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Companies Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.companies.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>Add Company
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Companies</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <form method="GET" action="{{ route('master-data.companies.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search companies..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="industry" class="form-select">
                            <option value="">All Industries</option>
                            @foreach($industries as $industry)
                                <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>
                                    {{ $industry }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="size" class="form-select">
                            <option value="">All Sizes</option>
                            <option value="startup" {{ request('size') == 'startup' ? 'selected' : '' }}>Startup</option>
                            <option value="sme" {{ request('size') == 'sme' ? 'selected' : '' }}>SME</option>
                            <option value="enterprise" {{ request('size') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Industry</th>
                            <th>Size</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Established</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $company->name }}</div>
                                @if($company->address)
                                <div class="text-muted small">{{ Str::limit($company->address, 50) }}</div>
                                @endif
                            </td>
                            <td>
                                @if($company->industry)
                                <span class="badge bg-azure-lt">{{ $company->industry }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($company->size == 'startup')
                                <span class="badge bg-green-lt">Startup</span>
                                @elseif($company->size == 'sme')
                                <span class="badge bg-blue-lt">SME</span>
                                @else
                                <span class="badge bg-purple-lt">Enterprise</span>
                                @endif
                            </td>
                            <td>{{ $company->email ?? '-' }}</td>
                            <td>{{ $company->phone ?? '-' }}</td>
                            <td>{{ $company->established_year ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('master-data.companies.edit', $company) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form action="{{ route('master-data.companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this company?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-building"></i>
                                    </div>
                                    <p class="empty-title">No companies found</p>
                                    <p class="empty-subtitle text-muted">
                                        Get started by creating your first company.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('master-data.companies.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-2"></i>Add Company
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($companies->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Showing <span>{{ $companies->firstItem() }}</span> to <span>{{ $companies->lastItem() }}</span> of <span>{{ $companies->total() }}</span> entries
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $companies->links() }}
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
