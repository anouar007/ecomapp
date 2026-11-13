@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="brand-header">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div>
            <h1 class="brand-title">Gestion des Rôles & Permissions</h1>
            <p class="brand-subtitle">Configurez les profils d'accès, permissions système et privilèges d'équipe</p>
        </div>
    </div>
    <div class="brand-header-actions">
        <a href="{{ route('users.index') }}" class="btn-brand-outline">
            <i class="fas fa-users-gear me-1.5" style="color: var(--primary-color)"></i> Membres d'équipe
        </a>
        <a href="{{ route('roles.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-1.5"></i> Nouveau Rôle
        </a>
    </div>
</div>

<!-- Navigation Croisée Système & Audit -->
@include('partials.subnav.system')

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
            <i class="fas fa-list text-danger me-2"></i>Rôles définis ({{ $roles->count() }})
        </h5>
    </div>
    <div class="table-responsive">
        <table class="brand-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem; width: 25%;">Nom du rôle</th>
                    <th style="width: 45%;">Permissions accordées</th>
                    <th style="width: 15%;">Membres</th>
                    <th class="text-end" style="padding-right: 1.5rem; width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td style="padding-left: 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">{{ $role->name }}</span>
                            @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                                <span class="badge bg-secondary-subtle text-secondary border small px-2 py-0.5" style="font-size: 0.68rem;">Système</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($role->permissions->count() > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions->take(4) as $permission)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </span>
                                @endforeach
                                @if($role->permissions->count() > 4)
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.70rem;">
                                        +{{ $role->permissions->count() - 4 }} autres
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Aucune permission</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.76rem;">
                            <i class="fas fa-user me-1"></i>{{ $role->users->count() }} utilisateur(s)
                        </span>
                    </td>
                    <td style="padding-right: 1.5rem;" class="text-end">
                        <div class="action-buttons-group justify-content-end">
                            <a href="{{ route('roles.edit', $role) }}" class="action-btn action-btn-edit" title="Modifier le rôle">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!in_array($role->name, ['Admin', 'Manager', 'Staff']))
                                <form method="POST" 
                                      action="{{ route('roles.destroy', $role->id) }}" 
                                      class="d-inline"
                                      data-confirm-delete="true"
                                      data-item-type="role"
                                      data-item-name="{{ $role->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="opacity-50 mb-2">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h6 class="fw-bold text-muted">Aucun rôle trouvé</h6>
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
    @forelse($roles as $role)
    <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">{{ $role->name }}</h6>
                @if(in_array($role->name, ['Admin', 'Manager', 'Staff']))
                    <span class="badge bg-secondary-subtle text-secondary border small px-2 py-0.5" style="font-size: 0.68rem;">Système</span>
                @endif
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                <i class="fas fa-user me-1"></i>{{ $role->users->count() }}
            </span>
        </div>

        <div class="bg-light rounded-3 p-2.5 mb-2.5 border">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                Permissions ({{ $role->permissions->count() }}) :
            </div>
            @if($role->permissions->count() > 0)
                <div class="d-flex flex-wrap gap-1">
                    @foreach($role->permissions->take(5) as $permission)
                        <span class="badge bg-white text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.70rem;">
                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                        </span>
                    @endforeach
                    @if($role->permissions->count() > 5)
                        <span class="badge bg-white text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            +{{ $role->permissions->count() - 5 }}
                        </span>
                    @endif
                </div>
            @else
                <span class="text-muted small fst-italic">Aucune permission</span>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-end gap-1.5 pt-1 border-top">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            @if(!in_array($role->name, ['Admin', 'Manager', 'Staff']))
                <form method="POST" 
                      action="{{ route('roles.destroy', $role->id) }}" 
                      class="d-inline"
                      data-confirm-delete="true"
                      data-item-type="role"
                      data-item-name="{{ $role->name }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-3 border">
        <i class="fas fa-shield-alt fa-3x text-muted opacity-50 mb-3"></i>
        <h6 class="fw-bold text-muted">Aucun rôle trouvé</h6>
    </div>
    @endforelse
</div>
@endsection
