@extends('layouts.app')

@section('title', 'GAMO Objectives')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Master Data</div>
                <h2 class="page-title">GAMO Objectives</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="{{ route('master-data.gamo-objectives.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>Add GAMO Objective
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
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index') }}" 
                           class="nav-link {{ !request('category') ? 'active' : '' }}" 
                           role="tab">
                            All
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index', ['category' => 'EDM']) }}" 
                           class="nav-link {{ request('category') == 'EDM' ? 'active' : '' }}" 
                           role="tab">
                            EDM
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index', ['category' => 'APO']) }}" 
                           class="nav-link {{ request('category') == 'APO' ? 'active' : '' }}" 
                           role="tab">
                            APO
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index', ['category' => 'BAI']) }}" 
                           class="nav-link {{ request('category') == 'BAI' ? 'active' : '' }}" 
                           role="tab">
                            BAI
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index', ['category' => 'DSS']) }}" 
                           class="nav-link {{ request('category') == 'DSS' ? 'active' : '' }}" 
                           role="tab">
                            DSS
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('master-data.gamo-objectives.index', ['category' => 'MEA']) }}" 
                           class="nav-link {{ request('category') == 'MEA' ? 'active' : '' }}" 
                           role="tab">
                            MEA
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="d-flex">
                    <div class="text-muted">
                        Show
                        <div class="mx-2 d-inline-block">
                            <select class="form-select form-select-sm" onchange="window.location.href='{{ route('master-data.gamo-objectives.index', ['category' => request('category')]) }}&perPage=' + this.value">
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
                            <th style="min-width: 90px;">Category</th>
                            <th style="min-width: 250px;">Name</th>
                            <th style="min-width: 300px;">Description</th>
                            <th style="min-width: 100px;">Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gamoObjectives as $objective)
                            <tr>
                                <td><span class="badge badge-outline text-azure">{{ $objective->objective_order ?? '-' }}</span></td>
                                <td><code>{{ $objective->code }}</code></td>
                                <td>
                                    <span class="badge badge-outline text-{{ 
                                        $objective->category == 'EDM' ? 'purple' : 
                                        ($objective->category == 'APO' ? 'blue' : 
                                        ($objective->category == 'BAI' ? 'green' : 
                                        ($objective->category == 'DSS' ? 'orange' : 'pink'))) 
                                    }}">
                                        {{ $objective->category }}
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    <span title="{{ $objective->name }}">
                                        {{ Str::limit($objective->name, 50, '...') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted" title="{{ $objective->description }}">
                                        {{ Str::limit($objective->description, 100, '...') }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('master-data.gamo-objectives.toggle-active', $objective) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge {{ $objective->is_active ? 'bg-green text-white' : 'bg-secondary' }} border-0" 
                                                style="cursor: pointer;">
                                            {{ $objective->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('master-data.gamo-objectives.edit', $objective) }}" class="btn btn-sm btn-icon btn-ghost-secondary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('master-data.gamo-objectives.destroy', $objective) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this GAMO objective?');">
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-target icon"></i>
                                        </div>
                                        <p class="empty-title">No GAMO objectives found</p>
                                        <p class="empty-subtitle text-muted">
                                            Get started by creating your first GAMO objective
                                        </p>
                                        <div class="empty-action">
                                            <a href="{{ route('master-data.gamo-objectives.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-2"></i>Add GAMO Objective
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($gamoObjectives->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        Showing {{ $gamoObjectives->firstItem() }} to {{ $gamoObjectives->lastItem() }} of {{ $gamoObjectives->total() }} entries
                    </p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $gamoObjectives->appends(['category' => request('category')])->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
