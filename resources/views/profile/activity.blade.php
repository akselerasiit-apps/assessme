@extends('layouts.app')

@section('title', 'Activity History')

@section('page-header')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Profile</div>
                <h2 class="page-title">Activity History</h2>
                <div class="text-secondary mt-1">Your complete activity log and audit trail</div>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('profile.index') }}" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                    Back to Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-xl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Log</h3>
                    <div class="card-actions">
                        <span class="text-secondary">
                            Total: {{ $activities->total() }} activities
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>Properties</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                            <tr>
                                <td class="text-secondary">
                                    <div title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                        {{ $activity->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-secondary">
                                        {{ $activity->created_at->format('H:i:s') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $iconColor = 'blue';
                                            $icon = '<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" />';
                                            
                                            if (str_contains(strtolower($activity->description), 'created')) {
                                                $iconColor = 'green';
                                                $icon = '<path d="M12 5l0 14" /><path d="M5 12l14 0" />';
                                            } elseif (str_contains(strtolower($activity->description), 'updated')) {
                                                $iconColor = 'yellow';
                                                $icon = '<path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />';
                                            } elseif (str_contains(strtolower($activity->description), 'deleted')) {
                                                $iconColor = 'red';
                                                $icon = '<path d="M18 6l-12 12" /><path d="M6 6l12 12" />';
                                            } elseif (str_contains(strtolower($activity->description), 'approved')) {
                                                $iconColor = 'green';
                                                $icon = '<path d="M5 12l5 5l10 -10" />';
                                            }
                                        @endphp
                                        <span class="avatar avatar-sm rounded me-2 bg-{{ $iconColor }}-lt">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                {!! $icon !!}
                                            </svg>
                                        </span>
                                        <strong>{{ $activity->description }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($activity->subject)
                                        <div>
                                            <span class="badge bg-blue-lt">{{ class_basename($activity->subject_type) }}</span>
                                        </div>
                                        <div class="text-secondary mt-1">
                                            @if(method_exists($activity->subject, 'getRouteKey'))
                                                ID: {{ $activity->subject->getRouteKey() }}
                                            @endif
                                            @if(isset($activity->subject->name))
                                                - {{ Str::limit($activity->subject->name, 30) }}
                                            @elseif(isset($activity->subject->title))
                                                - {{ Str::limit($activity->subject->title, 30) }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->properties && count($activity->properties) > 0)
                                        <button type="button" class="btn btn-sm btn-ghost-secondary" data-bs-toggle="modal" data-bs-target="#activityModal{{ $activity->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M12 8l.01 0" />
                                                <path d="M11 12l1 0l0 4l1 0" />
                                            </svg>
                                            View Details
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal modal-blur fade" id="activityModal{{ $activity->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Activity Properties</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <strong>Action:</strong> {{ $activity->description }}<br>
                                                            <strong>Time:</strong> {{ $activity->created_at->format('d M Y H:i:s') }}
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Property</th>
                                                                        <th>Value</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($activity->properties as $key => $value)
                                                                    <tr>
                                                                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                                        <td>
                                                                            @if(is_array($value))
                                                                                <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                                            @else
                                                                                {{ $value }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-5">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-3 text-secondary">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                    <div>No activities found</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($activities->hasPages())
                <div class="card-footer">
                    {{ $activities->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
