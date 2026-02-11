@extends('layouts.app')

@section('title', 'Create Role')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create New Role</h1>
    <p class="page-subtitle">Define a new role and assign permissions</p>
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
        <h3 class="card-title"><i class="fas fa-file-alt"></i> Role Information</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">
                    Role Name <span class="required">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-control" 
                       value="{{ old('name') }}" 
                       placeholder="Enter role name (e.g., Manager, Customer)" 
                       required 
                       autofocus>
                <small class="form-help">Use a descriptive name that clearly identifies this role's purpose</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key"></i> Assign Permissions
                </label>
                <div class="permissions-grid">
                    @foreach($permissions as $permission)
                    <div class="permission-checkbox">
                        <input type="checkbox" 
                               id="permission-{{ $permission->id }}" 
                               name="permissions[]" 
                               value="{{ $permission->id }}"
                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        <label for="permission-{{ $permission->id }}">
                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Role
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
