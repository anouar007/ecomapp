@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="brand-header mb-3 mb-md-4">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-users-gear"></i>
        </div>
        <div>
            <h1 class="brand-title">Gestion des Utilisateurs</h1>
            <p class="brand-subtitle">Gérez les comptes, les autorisations et les rôles d'accès de l'équipe</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3 mb-md-4" role="alert">
    <i class="fas fa-check-circle me-1.5"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3 mb-md-4" role="alert">
    <i class="fas fa-exclamation-circle me-1.5"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Desktop Table (d-none d-md-block, Zero horizontal scroll) -->
<div class="brand-table-card d-none d-md-block shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white p-3 px-md-4 border-bottom">
        <h5 class="mb-0 fw-bold fs-6 fs-md-5">
            <i class="fas fa-user-shield text-danger me-2"></i>Utilisateurs du système ({{ $users->count() }})
        </h5>
    </div>
    <div class="table-responsive">
        <table class="brand-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem; width: 30%;">Utilisateur</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 25%;">Rôles assignés</th>
                    <th class="text-end" style="padding-right: 1.5rem; width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="padding-left: 1.5rem;">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" 
                                 style="width: 38px; height: 38px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-size: 0.85rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <span class="fw-bold text-dark text-truncate d-block">{{ $user->name }}</span>
                                <small class="text-muted font-monospace" style="font-size: 0.72rem;">ID: #{{ $user->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="mailto:{{ $user->email }}" class="text-decoration-none text-secondary">
                            {{ $user->email }}
                        </a>
                    </td>
                    <td>
                        @if($user->roles->count() > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-shield-alt me-1"></i>{{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Aucun rôle</span>
                        @endif
                    </td>
                    <td style="padding-right: 1.5rem;">
                        <div class="d-flex justify-content-end">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5" 
                                    onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ json_encode($user->roles->pluck('id')) }})" 
                                    title="Gérer les rôles">
                                <i class="fas fa-user-shield"></i>
                                <span>Gérer rôles</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="opacity-50 mb-2">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6 class="fw-bold text-muted">Aucun utilisateur trouvé</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Touch Cards (d-block d-md-none, Zero horizontal scroll) -->
<div class="d-block d-md-none mb-4">
    @forelse($users as $user)
    <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
        <div class="d-flex align-items-center justify-content-between mb-2.5">
            <div class="d-flex align-items-center gap-2.5 min-w-0">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs flex-shrink-0" 
                     style="width: 38px; height: 38px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; font-size: 0.85rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: 0.94rem;">{{ $user->name }}</h6>
                    <a href="mailto:{{ $user->email }}" class="text-muted small text-decoration-none text-truncate d-block" style="font-size: 0.76rem;">
                        {{ $user->email }}
                    </a>
                </div>
            </div>
            <span class="badge bg-light text-muted border px-1.5 py-0.5 font-monospace" style="font-size: 0.68rem;">
                #{{ $user->id }}
            </span>
        </div>

        <!-- Role Badges Container -->
        <div class="bg-light rounded-3 p-2.5 mb-2.5 border">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                Rôles attribués :
            </div>
            @if($user->roles->count() > 0)
                <div class="d-flex flex-wrap gap-1">
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-alt me-1"></i>{{ $role->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-muted small fst-italic">Aucun rôle attribué</span>
            @endif
        </div>

        <!-- Touch Action -->
        <button type="button" 
                class="btn btn-outline-primary btn-sm w-100 rounded-pill py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ json_encode($user->roles->pluck('id')) }})">
            <i class="fas fa-user-shield"></i>
            <span>Modifier les rôles & autorisations</span>
        </button>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-3 border">
        <i class="fas fa-users fa-3x text-muted opacity-50 mb-3"></i>
        <h6 class="fw-bold text-muted">Aucun utilisateur trouvé</h6>
    </div>
    @endforelse
</div>

<!-- Modal Gérer les Rôles (Bootstrap 5 Responsive) -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold font-heading fs-6" id="roleModalLabel">
                    <i class="fas fa-user-shield text-danger me-2"></i>Gérer les Rôles de l'Utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="roleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Cochez les rôles à attribuer à : <strong class="text-dark" id="userName"></strong>
                    </p>
                    <div class="row g-2">
                        @foreach($roles as $role)
                        <div class="col-12 col-sm-6">
                            <label class="d-flex align-items-center gap-2.5 p-2.5 border rounded-3 bg-light cursor-pointer mb-0">
                                <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" class="form-check-input m-0" style="width: 20px; height: 20px;">
                                <span class="fw-semibold text-dark small">{{ $role->name }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-sm btn-brand-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let bsRoleModal = null;

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

    const modalEl = document.getElementById('roleModal');
    if (!bsRoleModal && typeof bootstrap !== 'undefined') {
        bsRoleModal = new bootstrap.Modal(modalEl);
    }
    if (bsRoleModal) {
        bsRoleModal.show();
    }
}
</script>
@endpush
@endsection
