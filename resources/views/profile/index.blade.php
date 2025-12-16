@extends('layouts.app')

@section('title', 'My Profile')

@section('page-header')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">My Profile</h2>
                <div class="text-secondary mt-1">Manage your profile information and settings</div>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-xl">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10" />
                </svg>
            </div>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row row-cards">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($user->avatar_path)
                        <img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="avatar avatar-xl rounded-circle">
                        @else
                        <span class="avatar avatar-xl rounded-circle">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                        @endif
                    </div>
                    <h3 class="mb-1">{{ $user->name }}</h3>
                    <div class="text-secondary mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                            <path d="M3 7l9 6l9 -6" />
                        </svg>
                        {{ $user->email }}
                    </div>
                    @if($user->phone)
                    <div class="text-secondary mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                        </svg>
                        {{ $user->phone }}
                    </div>
                    @endif
                    @if($user->bio)
                    <div class="text-secondary mb-3">
                        {{ $user->bio }}
                    </div>
                    @endif
                    <div class="mt-3">
                        @foreach($user->roles as $role)
                        <span class="badge bg-blue-lt">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('profile.edit') }}" class="btn w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                    <path d="M16 5l3 3" />
                                </svg>
                                Edit Profile
                            </a>
                        </div>
                        <div class="col">
                            <a href="{{ route('profile.change-password') }}" class="btn w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                    <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                    <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
                                </svg>
                                Change Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Card -->
            @if($user->company)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Company</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>{{ $user->company->name }}</strong>
                    </div>
                    @if($user->company->address)
                    <div class="text-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                        </svg>
                        {{ $user->company->address }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Created</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['assessments_created'] }}</div>
                            <div class="text-secondary">Assessments created</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Reviewed</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['assessments_reviewed'] }}</div>
                            <div class="text-secondary">Assessments reviewed</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Approved</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['assessments_approved'] }}</div>
                            <div class="text-secondary">Assessments approved</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Answered</div>
                            </div>
                            <div class="h1 mb-3">{{ $stats['answers_submitted'] }}</div>
                            <div class="text-secondary">Answers submitted</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Activities</h3>
                    <div class="card-actions">
                        <a href="{{ route('profile.activity') }}" class="btn btn-sm">
                            View All
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar avatar-sm rounded-circle bg-blue-lt">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col text-truncate">
                                <strong>{{ $activity->description }}</strong>
                                @if($activity->subject)
                                <div class="text-secondary">{{ class_basename($activity->subject_type) }}: {{ $activity->subject->name ?? $activity->subject->id }}</div>
                                @endif
                            </div>
                            <div class="col-auto">
                                <span class="text-secondary" title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="list-group-item">
                        <div class="text-secondary text-center py-3">
                            No activities yet
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row row-cards mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Settings</h3>
                            <p class="text-secondary">Manage your preferences and notification settings</p>
                            <a href="{{ route('profile.settings') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                </svg>
                                Go to Settings
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Activity History</h3>
                            <p class="text-secondary">View your complete activity history and logs</p>
                            <a href="{{ route('profile.activity') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                    <path d="M12 7v5l3 3" />
                                </svg>
                                View History
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
