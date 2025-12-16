@extends('layouts.app')

@section('title', 'Design Factors')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">Design Factors</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.design-factors.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>Add Design Factor
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-check alert-icon"></i></div>
                    <div>{{ session('success') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <div class="d-flex">
                    <div><i class="ti ti-alert-circle alert-icon"></i></div>
                    <div>{{ session('error') }}</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="card">
            <div class="card-body border-bottom py-3">
                <div class="d-flex">
                    <div class="text-muted">
                        Show
                        <div class="mx-2 d-inline-block">
                            <select class="form-select form-select-sm" onchange="window.location.href='{{ route('master-data.design-factors.index') }}?perPage=' + this.value">
                                <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                        entries
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter datatable">
                    <thead>
                        <tr>
                            <th class="w-1">Order</th>
                            <th style="min-width: 100px;">Code</th>
                            <th style="min-width: 250px;">Name</th>
                            <th style="min-width: 300px;">Description</th>
                            <th style="min-width: 100px;">Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designFactors as $factor)
                            <tr>
                                <td><span class="badge badge-outline text-azure">{{ $factor->factor_order ?? '-' }}</span></td>
                                <td><code>{{ $factor->code }}</code></td>
                                <td class="fw-bold">
                                    <span title="{{ $factor->name }}">
                                        {{ Str::limit($factor->name, 50, '...') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted" title="{{ $factor->description }}">
                                        {{ Str::limit($factor->description, 100, '...') }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('master-data.design-factors.toggle-active', $factor) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge {{ $factor->is_active ? 'bg-green' : 'bg-secondary' }} border-0" 
                                                style="cursor: pointer;">
                                            {{ $factor->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('master-data.design-factors.edit', $factor) }}" class="btn btn-sm btn-icon btn-ghost-secondary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('master-data.design-factors.destroy', $factor) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this design factor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-puzzle icon"></i>
                                        </div>
                                        <p class="empty-title">No design factors found</p>
                                        <p class="empty-subtitle text-muted">
                                            Get started by creating your first design factor
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('master-data.design-factors.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-2"></i>Add Design Factor
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($designFactors->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        Showing {{ $designFactors->firstItem() }} to {{ $designFactors->lastItem() }} of {{ $designFactors->total() }} entries
                    </p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $designFactors->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
