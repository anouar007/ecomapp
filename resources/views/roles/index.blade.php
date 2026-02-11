@extends('layouts.app')

@section('title', 'Roles Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-user-shield"></i> Roles Management</h1>
            <p class="page-subtitle">Manage system roles and their permissions</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Role
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> All Roles</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>
                        <strong>{{ $role->name }}</strong>
                        @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                            <span class="badge badge-primary">System</span>
                        @endif
                    </td>
                    <td>
                        @if($role->permissions->count() > 0)
                            <div class="permission-tags">
                                @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge badge-secondary">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                @endforeach
                                @if($role->permissions->count() > 3)
                                    <span class="badge badge-light">+{{ $role->permissions->count() - 3 }} more</span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">No permissions</span>
                        @endif
                    </td>
                    <td>{{ $role->users->count() }} users</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('roles.edit', $role) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($role->name, ['Admin', 'Manager', 'Staff']))
                                    <form method="POST" 
                                          action="{{ route('roles.destroy', $role->id) }}" 
                                          style="display: inline;"
                                          data-confirm-delete="true"
                                          data-item-type="role"
                                          data-item-name="{{ $role->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-user-shield"></i>
                        <p>No roles found. Create your first role to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
