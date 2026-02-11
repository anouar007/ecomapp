@extends('layouts.app')

@section('title', 'Edit Permission')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-edit"></i> Edit Permission: {{ $permission->name }}</h1>
    <p class="page-subtitle">Update permission information</p>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Permission Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('permissions.update', $permission) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="form-label">
                    Permission Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="{{ old('name', $permission->name) }}" 
                       placeholder="e.g., manage_products, view_reports" 
                       required>
                <small class="form-help">
                    Use snake_case format (e.g., manage_products, view_reports, create_orders)
                </small>
            </div>

            @if($permission->roles->count() > 0)
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Currently assigned to:</strong>
                    <div class="role-tags" style="margin-top: 8px;">
                        @foreach($permission->roles as $role)
                            <span class="badge badge-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('permissions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
