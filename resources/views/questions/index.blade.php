@extends('layouts.app')

@section('title', 'Question Management')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Question Bank</div>
                <h2 class="page-title">Question Management</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                @can('create questions')
                <a href="{{ route('questions.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i>Add Question
                </a>
                @endcan
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

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body border-bottom py-3">
                <form method="GET" action="{{ route('questions.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search questions..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="gamo_id" class="form-select">
                            <option value="">All GAMO Objectives</option>
                            @foreach($gamoObjectives as $gamo)
                                <option value="{{ $gamo->id }}" {{ request('gamo_id') == $gamo->id ? 'selected' : '' }}>
                                    {{ $gamo->code }} - {{ Str::limit($gamo->name, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="maturity_level" class="form-select">
                            <option value="">All Levels</option>
                            @foreach($maturityLevels as $level)
                                <option value="{{ $level }}" {{ request('maturity_level') == $level ? 'selected' : '' }}>
                                    Level {{ $level }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-search me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="w-1">Code</th>
                            <th>GAMO</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th class="w-1">Level</th>
                            <th class="w-1">Order</th>
                            <th>Status</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td><code>{{ $question->code }}</code></td>
                                <td>
                                    <div>
                                        <span class="badge badge-outline text-{{ 
                                            $question->gamoObjective->category == 'EDM' ? 'purple' : 
                                            ($question->gamoObjective->category == 'APO' ? 'blue' : 
                                            ($question->gamoObjective->category == 'BAI' ? 'green' : 
                                            ($question->gamoObjective->category == 'DSS' ? 'orange' : 'pink'))) 
                                        }}">
                                            {{ $question->gamoObjective->code }}
                                        </span>
                                    </div>
                                    <div class="text-muted small">{{ Str::limit($question->gamoObjective->name, 30) }}</div>
                                </td>
                                <td style="max-width: 400px;">
                                    <div class="fw-bold">{{ Str::limit($question->question_text, 80) }}</div>
                                    @if($question->required)
                                        <span class="badge bg-red-lt">Required</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-cyan">L{{ $question->maturity_level }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $question->question_order ?? '-' }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('questions.toggle-active', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="badge {{ $question->is_active ? 'bg-green' : 'bg-secondary' }} border-0" 
                                                style="cursor: pointer;">
                                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('questions.show', $question) }}" class="btn btn-sm btn-icon btn-ghost-info" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @can('update questions')
                                        <a href="{{ route('questions.edit', $question) }}" class="btn btn-sm btn-icon btn-ghost-primary" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @endcan
                                        @can('delete questions')
                                        <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this question?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-ghost-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="ti ti-question-mark icon"></i>
                                        </div>
                                        <p class="empty-title">No questions found</p>
                                        <p class="empty-subtitle text-muted">
                                            Get started by creating your first question
                                        </p>
                                        <div class="empty-action">
                                            @can('create questions')
                                            <a href="{{ route('questions.create') }}" class="btn btn-primary">
                                                <i class="ti ti-plus me-2"></i>Add Question
                                            </a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($questions->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <p class="m-0 text-muted">
                        Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} entries
                    </p>
                    <ul class="pagination m-0 ms-auto">
                        {{ $questions->appends(request()->query())->links() }}
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
