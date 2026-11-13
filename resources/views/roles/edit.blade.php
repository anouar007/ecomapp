@extends('layouts.app')

@section('title', 'Edit Role')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h1 class="brand-title">Modifier le Rôle : {{ $role->name }}</h1>
                <p class="brand-subtitle">Mettez à jour le nom et la matrice des permissions</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('roles.index') }}" class="btn-brand-outline">
                <i class="fas fa-arrow-left me-1.5"></i> Retour aux Rôles
            </a>
        </div>
    </div>

    @include('partials.subnav.system')

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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Role Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="form-label">
                    Role Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="{{ old('name', $role->name) }}" 
                       placeholder="Enter role name" 
                       required>
                @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                    <small class="form-help text-warning">
                        <i class="fas fa-info-circle"></i> This is a system role. Changes may affect system functionality.
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label mb-2">
                    <i class="fas fa-key me-1 text-primary"></i> Matrice des Permissions (Tableau de bord, Sections &amp; Actions)
                </label>
                @include('roles._permission_matrix', [
                    'permissionModules' => $permissionModules,
                    'permissionsByName' => $permissionsByName,
                    'permissions' => $permissions,
                    'selectedPermissions' => old('permissions', $rolePermissions),
                ])
            </div>

            <div class="form-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
