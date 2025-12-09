@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')
<div class="text-center mb-4">
    <a href="." class="navbar-brand navbar-brand-autodark">
        <img src="https://via.placeholder.com/110x32/206bc4/ffffff?text=COBIT" height="32" alt="">
    </a>
</div>

<div class="card card-md">
    <div class="card-body">
        <h2 class="h2 text-center mb-4">Create new account</h2>
        
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="d-flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path><path d="M12 16h.01"></path></svg>
                    </div>
                    <div>
                        <h4 class="alert-title">Registration failed!</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
        
        <form action="{{ route('register') }}" method="POST" autocomplete="off" novalidate>
            @csrf
            
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       placeholder="Enter name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       placeholder="Enter email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" 
                       placeholder="Confirm password" required>
            </div>
            
            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" required/>
                    <span class="form-check-label">I agree to the <a href="#" tabindex="-1">terms and policy</a>.</span>
                </label>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Create new account</button>
            </div>
        </form>
    </div>
</div>

<div class="text-center text-muted mt-3">
    Already have account? <a href="{{ route('login') }}" tabindex="-1">Sign in</a>
</div>
@endsection
