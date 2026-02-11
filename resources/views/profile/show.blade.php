@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
    border-radius: 16px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid white;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #667eea;
    font-weight: 700;
    overflow: hidden;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info h2 {
    margin: 0 0 8px 0;
    font-size: 32px;
}

.profile-info p {
    margin: 0;
    opacity: 0.9;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 24px;
}

.info-item {
    padding: 16px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid #667eea;
}

.info-label {
    font-size: 12px;
    text-transform: uppercase;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 6px;
}

.info-value {
    font-size: 16px;
    color: #0f172a;
    font-weight: 500;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-user"></i> My Profile</h1>
            <p class="page-subtitle">View and manage your account information</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="profile-header">
    <div class="profile-avatar">
        @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
        @else
            {{ strtoupper(substr($user->name, 0, 1)) }}
        @endif
    </div>
    <div class="profile-info">
        <h2>{{ $user->name }}</h2>
        <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
        <p style="margin-top: 8px;">
            @foreach($user->roles as $role)
                <span class="badge badge-success" style="margin-right: 6px;">
                    <i class="fas fa-shield-alt"></i> {{ $role->name }}
                </span>
            @endforeach
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Account Information</h3>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label"><i class="fas fa-user"></i> Full Name</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-envelope"></i> Email Address</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-calendar"></i> Member Since</div>
                <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-clock"></i> Last Updated</div>
                <div class="info-value">{{ $user->updated_at->diffForHumans() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-shield-alt"></i> Roles & Permissions</h3>
    </div>
    <div class="card-body">
        <h4 style="margin-bottom: 12px;">Your Roles:</h4>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px;">
            @forelse($user->roles as $role)
                <span class="badge badge-primary" style="padding: 8px 16px; font-size: 14px;">
                    <i class="fas fa-user-shield"></i> {{ $role->name }}
                </span>
            @empty
                <p class="text-muted">No roles assigned</p>
            @endforelse
        </div>
        
        @if($user->roles->isNotEmpty())
        <h4 style="margin-bottom: 12px;">Your Permissions:</h4>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @php
                $permissions = $user->roles->flatMap->permissions->unique('id');
            @endphp
            @foreach($permissions as $permission)
                <span class="badge badge-secondary" style="padding: 6px 12px;">
                    <i class="fas fa-key"></i> {{ $permission->name }}
                </span>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
