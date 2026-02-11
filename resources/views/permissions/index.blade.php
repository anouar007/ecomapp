@extends('layouts.app')

@section('title', 'Permissions Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-key"></i> Permissions Management</h1>
            <p class="page-subtitle">Manage system permissions and capabilities</p>
        </div>
        <a href="{{ route('permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Permission
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
        <h3 class="card-title"><i class="fas fa-list"></i> All Permissions</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Permission Name</th>
                    <th>Assigned To Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td>
                        <strong>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</strong>
                        <br>
                        <small class="text-muted">{{ $permission->name }}</small>
                    </td>
                    <td>
                        @if($permission->roles->count() > 0)
                            <div class="role-tags">
                                @foreach($permission->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">Not assigned to any role</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('permissions.edit', $permission) }}" class="btn-action btn-action-edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display:inline;" 
                                  data-confirm-delete="true"
                                  data-item-type="permission"
                                  data-item-name="{{ $permission->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-action-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        <i class="fas fa-key"></i>
                        <p>No permissions found. Create your first permission to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
