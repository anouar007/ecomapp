@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="brand-header">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-key"></i>
        </div>
        <div>
            <h1 class="brand-title">Gestion des Permissions</h1>
            <p class="brand-subtitle">Définissez et configurez les droits d'accès granulaires du système</p>
        </div>
    </div>
    <div class="brand-header-actions">
        <a href="{{ route('roles.index') }}" class="btn-brand-outline">
            <i class="fas fa-shield-halved me-1.5" style="color: var(--primary-color)"></i> Rôles
        </a>
        <a href="{{ route('permissions.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-1.5"></i> Nouvelle Permission
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
            <i class="fas fa-list text-danger me-2"></i>Permissions système ({{ $permissions->count() }})
        </h5>
    </div>
    <div class="table-responsive">
        <table class="brand-table mb-0 align-middle">
            <thead>
                <tr>
                    <th style="padding-left: 1.5rem; width: 35%;">Nom de la permission</th>
                    <th style="width: 50%;">Attribuée aux rôles</th>
                    <th class="text-end" style="padding-right: 1.5rem; width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                <tr>
                    <td style="padding-left: 1.5rem;">
                        <span class="fw-bold text-dark d-block">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                        <code class="text-muted small font-monospace">{{ $permission->name }}</code>
                    </td>
                    <td>
                        @if($permission->roles->count() > 0)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($permission->roles as $role)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                        <i class="fas fa-shield-alt me-1"></i>{{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small fst-italic">Non attribuée</span>
                        @endif
                    </td>
                    <td style="padding-right: 1.5rem;" class="text-end">
                        <div class="action-buttons-group justify-content-end">
                            <a href="{{ route('permissions.edit', $permission) }}" class="action-btn action-btn-edit" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline" 
                                  data-confirm-delete="true"
                                  data-item-type="permission"
                                  data-item-name="{{ $permission->name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-btn-delete" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="opacity-50 mb-2">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <h6 class="fw-bold text-muted">Aucune permission trouvée</h6>
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
    @forelse($permissions as $permission)
    <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">
                {{ ucwords(str_replace('_', ' ', $permission->name)) }}
            </h6>
            <code class="text-muted small font-monospace" style="font-size: 0.70rem;">{{ $permission->name }}</code>
        </div>

        <div class="bg-light rounded-3 p-2.5 mb-2.5 border">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                Rôles associés :
            </div>
            @if($permission->roles->count() > 0)
                <div class="d-flex flex-wrap gap-1">
                    @foreach($permission->roles as $role)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-alt me-1"></i>{{ $role->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <span class="text-muted small fst-italic">Non attribuée</span>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-end gap-1.5 pt-1 border-top">
            <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline" 
                  data-confirm-delete="true"
                  data-item-type="permission"
                  data-item-name="{{ $permission->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Supprimer">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-3 border">
        <i class="fas fa-key fa-3x text-muted opacity-50 mb-3"></i>
        <h6 class="fw-bold text-muted">Aucune permission trouvée</h6>
    </div>
    @endforelse
</div>
@endsection
