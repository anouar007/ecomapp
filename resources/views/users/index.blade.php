@extends('layouts.app')

@section('title', 'Users Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i> Users Management</h1>
    <p class="page-subtitle">Manage user roles and access permissions</p>
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
        <h3 class="card-title"><i class="fas fa-list"></i> All Users</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Current Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <strong>{{ $user->name }}</strong>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->roles->count() > 0)
                            <div class="role-tags">
                                @foreach($user->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">No roles assigned</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" 
                                class="btn-action btn-action-edit" 
                                onclick="openRoleModal({{ $user->id }}, '{{ $user->name }}', {{ json_encode($user->roles->pluck('id')) }})" 
                                title="Manage Roles">
                            <i class="fas fa-user-shield"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No users found in the system.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Role Assignment Modal -->
<div id="roleModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-user-shield"></i> Manage User Roles</h3>
            <button type="button" class="modal-close" onclick="closeRoleModal()">&times;</button>
        </div>
        <form id="roleForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <p class="modal-subtitle">Select roles for <strong id="userName"></strong></p>
                <div class="roles-grid">
                    @foreach($roles as $role)
                    <div class="role-checkbox">
                        <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}">
                        <label for="role-{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRoleModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Roles
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRoleModal(userId, userName, userRoles) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('roleForm').action = `/users/${userId}/roles`;
    
    // Uncheck all checkboxes first
    document.querySelectorAll('#roleModal input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check user's current roles
    userRoles.forEach(roleId => {
        const checkbox = document.getElementById(`role-${roleId}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    document.getElementById('roleModal').style.display = 'flex';
}

function closeRoleModal() {
    document.getElementById('roleModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('roleModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRoleModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('roleModal').style.display === 'flex') {
        closeRoleModal();
    }
});
</script>
@endpush
@endsection
