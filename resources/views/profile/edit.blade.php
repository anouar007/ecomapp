@extends('layouts.app')

@section('title', 'Edit Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid #e2e8f0;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #667eea;
    font-weight: 700;
    overflow: hidden;
    margin-bottom: 16px;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-upload {
    text-align: center;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-edit"></i> Edit Profile</h1>
    <p class="page-subtitle">Update your account information and password</p>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <div>
        <strong>Oops! Something went wrong:</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user"></i> Profile Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" id="avatarImg">
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                
                <div class="form-group">
                    <label for="avatar" class="form-label">Profile Picture</label>
                    <input type="file" 
                           id="avatar" 
                           name="avatar" 
                           class="form-control" 
                           accept="image/*"
                           onchange="previewAvatar(event)">
                    <small class="text-muted">Max size: 2MB. Accepted: JPG, PNG, GIF</small>
                </div>
            </div>
            
            <hr style="margin: 32px 0;">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="name" class="form-label">
                        Full Name <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           value="{{ old('name', $user->name) }}" 
                           required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="{{ old('email', $user->email) }}" 
                           required>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-lock"></i> Change Password</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="form-group">
                    <label for="current_password" class="form-label">
                        Current Password <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="form-control" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        New Password <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        Confirm New Password <span class="required">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control" 
                           required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('avatarPreview');
            const initial = document.getElementById('avatarInitial');
            
            if (initial) {
                initial.remove();
            }
            
            let img = document.getElementById('avatarImg');
            if (!img) {
                img = document.createElement('img');
                img.id = 'avatarImg';
                preview.appendChild(img);
            }
            img.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection
