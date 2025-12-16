@extends('layouts.app')

@section('title', 'Edit Profile')

@section('page-header')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Profile</div>
                <h2 class="page-title">Edit Profile</h2>
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
        <div class="col-lg-8">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Profile Information</h3>
                    </div>
                    <div class="card-body">
                        <!-- Avatar Upload -->
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if($user->avatar_path)
                                    <img src="{{ Storage::url($user->avatar_path) }}" alt="{{ $user->name }}" class="avatar avatar-xl rounded-circle" id="avatar-preview">
                                    @else
                                    <span class="avatar avatar-xl rounded-circle" id="avatar-preview">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/jpeg,image/png,image/jpg">
                                    @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">JPG, PNG or JPEG. Max 2MB.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label required">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+62 812 3456 7890">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" name="bio" rows="4" placeholder="Tell us about yourself..." maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 500 characters</div>
                        </div>

                        <!-- Company (Read-only) -->
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" value="{{ $user->company->name ?? 'No company assigned' }}" readonly disabled>
                            <div class="form-text">Contact administrator to change company</div>
                        </div>
                    </div>
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
                                Save Changes
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
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatar-preview');
            preview.innerHTML = '';
            preview.style.backgroundImage = 'url(' + e.target.result + ')';
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
