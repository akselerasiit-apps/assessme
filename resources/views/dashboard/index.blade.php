@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <div class="page-pretitle">
        Overview
    </div>
    <h2 class="page-title">
        Dashboard
    </h2>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row row-deck row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Assessments</div>
                </div>
                <div class="h1 mb-3">{{ $stats['total_assessments'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>All assessments in the system</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">In Progress</div>
                </div>
                <div class="h1 mb-3">{{ $stats['in_progress'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>Currently being assessed</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Completed</div>
                </div>
                <div class="h1 mb-3">{{ $stats['completed'] ?? 0 }}</div>
                <div class="d-flex mb-2">
                    <div>Successfully completed</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Average Maturity</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['average_maturity'] ?? 0, 1) }}</div>
                <div class="d-flex mb-2">
                    <div>Out of 5.0</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row row-deck row-cards mt-3">
    <!-- Assessments by Status -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Assessments by Status</h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Maturity Distribution -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Maturity Level Distribution</h3>
            </div>
            <div class="card-body">
                <canvas id="maturityChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Assessments Table -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Assessments</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Maturity</th>
                                <th class="w-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAssessments ?? [] as $assessment)
                            <tr>
                                <td><span class="text-muted">{{ $assessment->id }}</span></td>
                                <td>{{ $assessment->title }}</td>
                                <td>{{ $assessment->company->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($assessment->status) {
                                            'draft' => 'bg-secondary',
                                            'in_progress' => 'bg-blue',
                                            'under_review' => 'bg-yellow',
                                            'completed' => 'bg-green',
                                            'approved' => 'bg-teal',
                                            'archived' => 'bg-gray',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</span>
                                </td>
                                <td>{{ $assessment->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($assessment->maturity_level)
                                        <span class="badge bg-primary">{{ number_format($assessment->maturity_level, 1) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No assessments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'In Progress', 'Under Review', 'Completed', 'Approved'],
            datasets: [{
                data: [
                    {{ $stats['draft'] ?? 0 }},
                    {{ $stats['in_progress'] ?? 0 }},
                    {{ $stats['under_review'] ?? 0 }},
                    {{ $stats['completed'] ?? 0 }},
                    {{ $stats['approved'] ?? 0 }}
                ],
                backgroundColor: [
                    '#6c757d',
                    '#0054a6',
                    '#f59f00',
                    '#2fb344',
                    '#20c997'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Maturity Distribution Chart
    const maturityCtx = document.getElementById('maturityChart').getContext('2d');
    new Chart(maturityCtx, {
        type: 'bar',
        data: {
            labels: ['Level 0', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5'],
            datasets: [{
                label: 'Number of Assessments',
                data: @json(array_values($maturityDistribution ?? [0, 0, 0, 0, 0, 0])),
                backgroundColor: '#0054a6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
