@extends('layouts.app')

@section('title', 'Settings')

@section('page-header')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Profile</div>
                <h2 class="page-title">Settings</h2>
                <div class="text-secondary mt-1">Manage your preferences and notifications</div>
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

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ route('profile.update-settings') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Regional Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                <path d="M3.6 9h16.8" />
                                <path d="M3.6 15h16.8" />
                                <path d="M11.5 3a17 17 0 0 0 0 18" />
                                <path d="M12.5 3a17 17 0 0 1 0 18" />
                            </svg>
                            Regional Settings
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-select @error('timezone') is-invalid @enderror" name="timezone">
                                    <option value="">Select timezone</option>
                                    <option value="Asia/Jakarta" {{ old('timezone', $user->preferences['timezone'] ?? '') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                                    <option value="Asia/Makassar" {{ old('timezone', $user->preferences['timezone'] ?? '') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                                    <option value="Asia/Jayapura" {{ old('timezone', $user->preferences['timezone'] ?? '') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                                    <option value="UTC" {{ old('timezone', $user->preferences['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                </select>
                                @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Language</label>
                                <select class="form-select @error('language') is-invalid @enderror" name="language">
                                    <option value="id" {{ old('language', $user->preferences['language'] ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ old('language', $user->preferences['language'] ?? 'id') == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg>
                            Notification Preferences
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="email_notifications" value="1" {{ old('email_notifications', $user->preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <strong>Email Notifications</strong>
                                    <span class="d-block text-secondary">Receive email notifications for important updates</span>
                                </span>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="assessment_reminders" value="1" {{ old('assessment_reminders', $user->preferences['assessment_reminders'] ?? true) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <strong>Assessment Reminders</strong>
                                    <span class="d-block text-secondary">Get reminded about pending assessments and reviews</span>
                                </span>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="weekly_summary" value="1" {{ old('weekly_summary', $user->preferences['weekly_summary'] ?? false) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <strong>Weekly Summary</strong>
                                    <span class="d-block text-secondary">Receive a weekly summary of your activities and assessments</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-2">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z" />
                                <path d="M7 20h10" />
                                <path d="M9 16v4" />
                                <path d="M15 16v4" />
                            </svg>
                            Display Settings
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 9h.01" />
                                        <path d="M11 12h1v4h1" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Coming Soon</h4>
                                    <div class="text-secondary">Display settings such as theme, compact mode, and other visual preferences will be available in a future update.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('profile.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M14 4l0 4l-6 0l0 -4" />
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
