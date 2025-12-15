@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Assessment: {{ $assessment->code }}
                    </div>
                    <h2 class="page-title">
                        Capability Assessment
                    </h2>
                    <div class="text-muted mt-2">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-arrows">
                                <li class="breadcrumb-item"><a href="{{ route('assessments.index') }}">Assessments</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('assessments.show', $assessment) }}">{{ $assessment->code }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Capability Assessment</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                        Back to Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Select GAMO Objective</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">
                                Select a GAMO objective below to begin the capability assessment. 
                                Each objective is assessed across 6 capability levels (0-5).
                            </p>
                            
                            <div class="row row-cards">
                                @forelse($gamoObjectives as $gamo)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-link card-link-pop">
                                        <a href="{{ route('capability.assessment', [$assessment, $gamo]) }}" class="d-block">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="avatar bg-primary-lt">
                                                            {{ substr($gamo->code, 0, 3) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <div class="font-weight-medium">{{ $gamo->code }}</div>
                                                        <div class="text-muted small">{{ Str::limit($gamo->name_en, 50) }}</div>
                                                    </div>
                                                </div>
                                                @if($gamo->name_id)
                                                <div class="mt-3 text-muted small">
                                                    <strong>ID:</strong> {{ Str::limit($gamo->name_id, 60) }}
                                                </div>
                                                @endif
                                                
                                                <div class="mt-3">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="text-muted small">Questions</div>
                                                            <div class="fw-bold">{{ $gamo->questions->count() }}</div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="text-muted small">Domain</div>
                                                            <div class="fw-bold">{{ $gamo->domain_area ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" /><path d="M15 9l-6 6" /><path d="M9 9l6 6" /></svg>
                                        </div>
                                        <p class="empty-title">No GAMO objectives available</p>
                                        <p class="empty-subtitle text-muted">
                                            Please add GAMO objectives in master data first.
                                        </p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Capability Assessment Guide</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Capability Levels</h4>
                                    <ul>
                                        <li><strong>Level 0:</strong> Incomplete Process</li>
                                        <li><strong>Level 1:</strong> Performed Process</li>
                                        <li><strong>Level 2:</strong> Managed Process</li>
                                        <li><strong>Level 3:</strong> Established Process</li>
                                        <li><strong>Level 4:</strong> Predictable Process</li>
                                        <li><strong>Level 5:</strong> Optimizing Process</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h4>Achievement Rating</h4>
                                    <ul>
                                        <li><strong>Not Achieved:</strong> 0% - Process attribute is not implemented</li>
                                        <li><strong>Partially Achieved:</strong> 50% - Process attribute is partially implemented</li>
                                        <li><strong>Largely Achieved:</strong> 75% - Process attribute is largely implemented</li>
                                        <li><strong>Fully Achieved:</strong> 100% - Process attribute is fully implemented</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
