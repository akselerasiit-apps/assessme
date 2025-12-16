@extends('layouts.app')

@section('title', 'Change Password')

@section('page-header')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Security</div>
                <h2 class="page-title">Change Password</h2>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('profile.index') }}" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Update Your Password</h3>
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
                                    <h4 class="alert-title">Password Requirements</h4>
                                    <div class="text-secondary">
                                        <ul class="mb-0">
                                            <li>Minimum 8 characters</li>
                                            <li>At least one uppercase letter</li>
                                            <li>At least one lowercase letter</li>
                                            <li>At least one number</li>
                                            <li>At least one special character</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label required">Current Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" id="current_password" required autocomplete="current-password">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePassword('current_password', this); return false;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label required">New Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required autocomplete="new-password" onkeyup="checkPasswordStrength()">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePassword('password', this); return false;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="progress mt-2" style="height: 5px; display: none;" id="password-strength-bar">
                                <div class="progress-bar" id="password-strength-progress" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div class="form-text" id="password-strength-text"></div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label required">Confirm New Password</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" required autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePassword('password_confirmation', this); return false;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                        </svg>
                                    </a>
                                </span>
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <a href="{{ route('profile.index') }}" class="btn btn-link">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                    <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                    <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
                                </svg>
                                Change Password
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
    
    // Toggle icon
    const svg = icon.querySelector('svg');
    if (type === 'text') {
        svg.innerHTML = '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3l18 18" /><path d="M10.584 10.587a2 2 0 0 0 2.828 2.83" /><path d="M9.363 5.365a9.466 9.466 0 0 1 2.637 -.365c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87m-3.197 .93a9.88 9.88 0 0 1 -3.665 .7c-3.6 0 -6.6 -2 -9 -6c1.369 -2.282 2.91 -3.81 4.625 -4.654" />';
    } else {
        svg.innerHTML = '<path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />';
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const progressBar = document.getElementById('password-strength-progress');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    if (password.length === 0) {
        strengthBar.style.display = 'none';
        strengthText.textContent = '';
        return;
    }
    
    strengthBar.style.display = 'block';
    
    let strength = 0;
    let text = '';
    let color = '';
    
    // Length check
    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 10;
    
    // Character variety checks
    if (/[a-z]/.test(password)) strength += 20;
    if (/[A-Z]/.test(password)) strength += 20;
    if (/[0-9]/.test(password)) strength += 20;
    if (/[^a-zA-Z0-9]/.test(password)) strength += 10;
    
    // Determine strength level
    if (strength < 40) {
        text = 'Weak password';
        color = 'bg-danger';
    } else if (strength < 60) {
        text = 'Fair password';
        color = 'bg-warning';
    } else if (strength < 80) {
        text = 'Good password';
        color = 'bg-info';
    } else {
        text = 'Strong password';
        color = 'bg-success';
    }
    
    progressBar.style.width = strength + '%';
    progressBar.className = 'progress-bar ' + color;
    strengthText.textContent = text;
}
</script>
@endpush
@endsection
