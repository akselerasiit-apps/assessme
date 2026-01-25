@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="text-center mb-4">
    <a href="." class="navbar-brand navbar-brand-autodark">
        <img src="https://via.placeholder.com/110x32/206bc4/ffffff?text=COBIT" height="32" alt="">
    </a>
</div>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Login to your account</h2>
        
        <!-- Test Credentials Info -->
        <div class="alert alert-info alert-important" role="alert">
            <div class="d-flex">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>
                </div>
                <div>
                    <h4 class="alert-title">Test Credentials</h4>
                    <div class="text-muted" style="font-size: 0.875rem;">
                        <strong>Super Admin:</strong> (Review & Final Approval)<br>
                        Email: <code>superadmin@assessme.com</code><br>
                        Password: <code>Password123!</code>
                        <hr class="my-2">
                        <strong>Assessor:</strong> (Conduct Assessments)<br>
                        Email: <code>assessor@assessme.com</code><br>
                        Password: <code>Password123!</code>
                        <hr class="my-2">
                        <strong>Viewer:</strong> (Read Only)<br>
                        Email: <code>viewer@assessme.com</code><br>
                        Password: <code>Password123!</code>
                    </div>
                </div>
            </div>
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Login failed!</h4>
                        <div class="text-muted">{{ $errors->first() }}</div>
                    </div>
                </div>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       placeholder="your@email.com" autocomplete="off" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-2">
                <label class="form-label">
                    Password
                    <span class="form-label-description">
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </span>
                </label>
                <div class="input-group input-group-flat">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Your password" autocomplete="off" required>
                    <span class="input-group-text">
                        <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                        </a>
                    </span>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-2">
                <label class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input"/>
                    <span class="form-check-label">Remember me on this device</span>
                </label>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-muted mt-3">
    Don't have account yet? <a href="{{ route('register') }}" tabindex="-1">Sign up</a>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelector('.input-group-text a').addEventListener('click', function(e) {
        e.preventDefault();
        const input = this.closest('.input-group').querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    });
</script>
@endpush
