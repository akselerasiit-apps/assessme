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
                        <div class="alert alert-warning alert-important" id="password-requirements">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                        <path d="M12 9h.01" />
                                        <path d="M11 12h1v4h1" />
                                    </svg>
                                </div>
                                <div class="w-100">
                                    <h4 class="alert-title">Password Requirements</h4>
                                    <div class="text-white">
                                        <ul class="mb-0" id="requirement-list">
                                            <li id="req-length">
                                                <span class="text-white">○</span>
                                                Minimum 10 characters
                                            </li>
                                            <li id="req-uppercase">
                                                <span class="text-white">○</span>
                                                At least one uppercase letter (A-Z)
                                            </li>
                                            <li id="req-lowercase">
                                                <span class="text-white">○</span>
                                                At least one lowercase letter (a-z)
                                            </li>
                                            <li id="req-special">
                                                <span class="text-white">○</span>
                                                At least one special character (@$!%*?&#)
                                            </li>
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
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required autocomplete="new-password">
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
                            <button type="submit" class="btn btn-primary ms-auto" id="submit-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
                                    <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
                                    <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
                                </svg>
                                <span class="btn-text">Change Password</span>
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

function checkPasswordRequirements() {
    const password = document.getElementById('password').value;
    
    // Check each requirement
    const hasLength = password.length >= 10;
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasSpecial = /[@$!%*?&#]/.test(password);
    
    // Update UI for each requirement
    updateRequirement('req-length', hasLength);
    updateRequirement('req-uppercase', hasUppercase);
    updateRequirement('req-lowercase', hasLowercase);
    updateRequirement('req-special', hasSpecial);
    
    // Update strength bar
    updateStrengthBar(password, hasLength, hasUppercase, hasLowercase, hasSpecial);
    
    // Enable/disable submit button
    const allMet = hasLength && hasUppercase && hasLowercase && hasSpecial;
    return allMet;
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('span');
    
    if (isMet) {
        element.classList.remove('text-muted');
        element.classList.add('text-success');
        icon.innerHTML = '✓';
        icon.className = 'text-success fw-bold';
    } else {
        element.classList.remove('text-success');
        element.classList.add('text-white');
        icon.innerHTML = '○';
        icon.className = 'text-white';
    }
}

function updateStrengthBar(password, hasLength, hasUppercase, hasLowercase, hasSpecial) {
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
    
    // Calculate strength
    if (hasLength) strength += 40;
    if (hasLowercase) strength += 20;
    if (hasUppercase) strength += 20;
    if (hasSpecial) strength += 20;
    
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

// Add event listener for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const form = document.querySelector('form');
    
    // Real-time validation
    passwordField.addEventListener('input', checkPasswordRequirements);
    
    // AJAX form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const btnText = submitBtn.querySelector('.btn-text');
        const originalText = btnText.textContent;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        btnText.textContent = 'Changing...';
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { status: response.status, data: data };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10" />
                                </svg>
                            </div>
                            <div>${data.message}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                form.insertAdjacentHTML('beforebegin', alertHtml);
                
                // Reset form
                form.reset();
                checkPasswordRequirements();
                
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("profile.index") }}';
                }, 2000);
            }
        })
        .catch(error => {
            if (error.status && error.data) {
                // Show validation errors
                if (error.data.errors) {
                    Object.keys(error.data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback';
                            errorDiv.textContent = error.data.errors[field][0];
                            input.parentElement.appendChild(errorDiv);
                        }
                    });
                }
                
                // Show error message
                const message = error.data.message || 'An error occurred. Please try again.';
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 9v4" />
                                    <path d="M12 16v.01" />
                                </svg>
                            </div>
                            <div>${message}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                form.insertAdjacentHTML('beforebegin', alertHtml);
            } else {
                // Network or other error
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M12 9v4" />
                                    <path d="M12 16v.01" />
                                </svg>
                            </div>
                            <div>An error occurred. Please try again.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                form.insertAdjacentHTML('beforebegin', alertHtml);
            }
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            btnText.textContent = originalText;
        });
    });
});
</script>
@endpush
@endsection
