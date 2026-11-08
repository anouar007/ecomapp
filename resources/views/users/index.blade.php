@extends('layouts.app')

@section('title', 'Équipe & Gestion des Collaborateurs')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-4 p-3 d-flex align-items-center justify-content-center shadow-sm" 
                 style="width: 52px; height: 52px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
                <i class="fas fa-users-gear fa-xl"></i>
            </div>
            <div>
                <h1 class="h4 fw-bold mb-1 text-dark">Équipe & Collaborateurs</h1>
                <p class="text-muted small mb-0">Gérez les comptes, rôles et permissions granulaires en toute flexibilité et sécurité.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-xs">
                <i class="fas fa-shield-halved text-secondary"></i>
                <span class="fw-semibold">Rôles globaux</span>
            </a>
            <button type="button" class="btn btn-sm btn-brand-primary rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 shadow-sm text-white" 
                    style="background: #e11d48; border-color: #e11d48;"
                    onclick="openCreateModal()">
                <i class="fas fa-user-plus"></i>
                <span class="fw-bold">Nouveau collaborateur</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-circle-check fs-5 text-success"></i>
        <div class="fw-medium">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4 d-flex align-items-center gap-2" role="alert">
        <i class="fas fa-circle-exclamation fs-5 text-danger"></i>
        <div class="fw-medium">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-triangle-exclamation text-danger"></i>
            <span class="fw-bold">Veuillez corriger les erreurs suivantes :</span>
        </div>
        <ul class="mb-0 small ps-4">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Équipe Totale</span>
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 34px; height: 34px;">
                        <i class="fas fa-users" style="font-size: 0.85rem;"></i>
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-4 fw-bold text-dark">{{ $kpi['total_team'] }}</span>
                    <span class="text-muted small">membres actifs</span>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Administrateurs</span>
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 34px; height: 34px;">
                        <i class="fas fa-crown" style="font-size: 0.85rem;"></i>
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-4 fw-bold text-dark">{{ $kpi['admins'] }}</span>
                    <span class="text-muted small">super admins</span>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Managers & Staff</span>
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 34px; height: 34px;">
                        <i class="fas fa-briefcase" style="font-size: 0.85rem;"></i>
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-4 fw-bold text-dark">{{ $kpi['managers'] + $kpi['staff'] }}</span>
                    <span class="text-muted small">opérateurs</span>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 rounded-4 shadow-xs bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Inactifs / Suspendus</span>
                    <span class="rounded-circle d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" style="width: 34px; height: 34px;">
                        <i class="fas fa-user-lock" style="font-size: 0.85rem;"></i>
                    </span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="fs-4 fw-bold {{ $kpi['inactive'] > 0 ? 'text-warning' : 'text-dark' }}">{{ $kpi['inactive'] }}</span>
                    <span class="text-muted small">accès bloqués</span>
                </div>
                <div class="progress mt-2" style="height: 4px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $kpi['inactive'] > 0 ? '100' : '0' }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 rounded-4 shadow-xs bg-white mb-4">
        <div class="card-body p-3 p-md-3.5">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" 
                               class="form-control bg-light border-start-0 rounded-end-pill py-2" 
                               placeholder="Rechercher par nom, email, téléphone ou poste...">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="role" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()">
                        <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                        <option value="none" {{ $roleFilter === 'none' ? 'selected' : '' }}>Sans rôle (Clients)</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Tous statuts</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                        <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill py-2 w-100 fw-semibold">
                        Filtrer
                    </button>
                    @if($search !== '' || $roleFilter !== 'all' || $statusFilter !== 'all')
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-pill py-2 px-3" title="Réinitialiser">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden d-none d-lg-block mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 30%;">Collaborateur</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 22%;">Rôles & Permissions</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 14%;">Statut</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 18%;">Dernière Connexion</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 16%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                    @php
                        $isSelf = auth()->id() === $user->id;
                        $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
                        $avatarGradient = match(optional($user->roles->first())->name) {
                            'Admin' => 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)',
                            'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                            'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                            default => 'linear-gradient(135deg, #475569 0%, #1e293b 100%)',
                        };
                    @endphp
                    <tr class="hover-bg-light transition-all">
                        <td class="ps-4 py-3.5">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0"
                                     style="width: 42px; height: 42px; background: {{ $avatarGradient }}; font-size: 0.95rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-dark text-truncate">{{ $user->name }}</span>
                                        @if($isSelf)
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Vous</span>
                                        @endif
                                        @if($isSuperAdmin)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;" title="Super Admin Principal">
                                                <i class="fas fa-crown me-0.5"></i> Principal
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-muted small mt-0.5">
                                        <span><i class="fas fa-envelope me-1 opacity-50"></i>{{ $user->email }}</span>
                                        @if($user->phone)
                                            <span class="text-secondary opacity-50">•</span>
                                            <span><i class="fas fa-phone me-1 opacity-50"></i>{{ $user->phone }}</span>
                                        @endif
                                    </div>
                                    @if($user->job_title)
                                        <div class="text-muted small mt-0.5 fw-medium" style="font-size: 0.75rem; color: #64748b;">
                                            <i class="fas fa-id-badge me-1 text-secondary opacity-75"></i>{{ $user->job_title }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5">
                            <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                @forelse($user->roles as $role)
                                    @php
                                        $roleColor = match($role->name) {
                                            'Admin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                            'Manager' => 'bg-primary-subtle text-primary border-primary-subtle',
                                            'Staff' => 'bg-success-subtle text-success border-success-subtle',
                                            default => 'bg-light text-dark border-secondary-subtle',
                                        };
                                        $roleIcon = match($role->name) {
                                            'Admin' => 'fa-crown',
                                            'Manager' => 'fa-user-tie',
                                            'Staff' => 'fa-user-check',
                                            default => 'fa-shield-halved',
                                        };
                                    @endphp
                                    <span class="badge {{ $roleColor }} border rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                        <i class="fas {{ $roleIcon }}"></i>
                                        <span>{{ $role->name }}</span>
                                    </span>
                                @empty
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.72rem;">Client / Sans rôle</span>
                                @endforelse

                                @if($user->permissions->count() > 0)
                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle rounded-pill px-2 py-1" 
                                          style="font-size: 0.7rem; background: #f3e8ff; color: #7e22ce; border-color: #e9d5ff;"
                                          title="{{ $user->permissions->pluck('name')->implode(', ') }}">
                                        <i class="fas fa-key me-1"></i>+{{ $user->permissions->count() }} directes
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5" id="status-col-{{ $user->id }}">
                            {!! $user->status_badge !!}
                        </td>
                        <td class="py-3.5">
                            @if($user->last_login_at)
                                <div class="text-dark fw-medium small">
                                    {{ $user->last_login_at->diffForHumans() }}
                                </div>
                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @if($user->last_login_ip)
                                        <span class="text-secondary opacity-75">({{ $user->last_login_ip }})</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Jamais connecté</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3.5 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-primary shadow-xs border"
                                        onclick="openEditModal({{ $user->id }})" title="Modifier le collaborateur">
                                    <i class="fas fa-pencil"></i>
                                </button>

                                <!-- Reset Password Button -->
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-warning shadow-xs border"
                                        onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')" 
                                        title="Réinitialiser le mot de passe">
                                    <i class="fas fa-key"></i>
                                </button>

                                <!-- Toggle Status Button -->
                                @if(!$isSelf && !$isSuperAdmin)
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-secondary shadow-xs border"
                                            onclick="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            id="btn-toggle-{{ $user->id }}"
                                            title="Activer / Désactiver">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }} fa-lg"></i>
                                    </button>
                                @endif

                                <!-- Delete Button -->
                                @if(!$isSelf && !$isSuperAdmin)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                                          onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer le collaborateur {{ addslashes($user->name) }} ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-danger shadow-xs border" title="Supprimer">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-users-slash fa-3x text-muted opacity-50 mb-3"></i>
                                <h6 class="fw-bold text-dark">Aucun collaborateur trouvé</h6>
                                <p class="text-muted small mb-3">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    Réinitialiser les filtres
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Responsive Cards View -->
    <div class="d-block d-lg-none mb-4">
        @forelse($users as $user)
        @php
            $isSelf = auth()->id() === $user->id;
            $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
            $avatarGradient = match(optional($user->roles->first())->name) {
                'Admin' => 'linear-gradient(135deg, #e11d48 0%, #9f1239 100%)',
                'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                default => 'linear-gradient(135deg, #475569 0%, #1e293b 100%)',
            };
        @endphp
        <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5 mb-3">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0"
                         style="width: 44px; height: 44px; background: {{ $avatarGradient }}; font-size: 1rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.95rem;">{{ $user->name }}</h6>
                            @if($isSelf)
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">Vous</span>
                            @endif
                        </div>
                        <div class="text-muted small text-truncate" style="font-size: 0.78rem;">
                            {{ $user->email }}
                        </div>
                        @if($user->job_title)
                            <div class="text-secondary small fw-medium" style="font-size: 0.72rem;">
                                {{ $user->job_title }}
                            </div>
                        @endif
                    </div>
                </div>
                <div id="mob-status-{{ $user->id }}">
                    {!! $user->status_badge !!}
                </div>
            </div>

            <!-- Role & Info details -->
            <div class="bg-light rounded-3 p-2.5 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-semibold" style="font-size: 0.7rem; text-transform: uppercase;">Rôles :</span>
                    <span class="text-muted small" style="font-size: 0.7rem;">
                        @if($user->last_login_at)
                            Vu {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Jamais connecté
                        @endif
                    </span>
                </div>
                <div class="d-flex flex-wrap gap-1">
                    @forelse($user->roles as $role)
                        <span class="badge bg-white text-dark border rounded-pill px-2 py-1 fw-semibold" style="font-size: 0.72rem;">
                            <i class="fas fa-shield-alt text-primary me-1"></i>{{ $role->name }}
                        </span>
                    @empty
                        <span class="text-muted small fst-italic">Client / Sans rôle</span>
                    @endforelse

                    @if($user->permissions->count() > 0)
                        <span class="badge bg-purple-subtle text-purple border rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                            +{{ $user->permissions->count() }} directes
                        </span>
                    @endif
                </div>
            </div>

            <!-- Actions grid -->
            <div class="d-grid grid-cols-2 gap-2" style="display: grid; grid-template-columns: 1fr 1fr;">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold py-1.5 d-flex align-items-center justify-content-center gap-1.5"
                        onclick="openEditModal({{ $user->id }})">
                    <i class="fas fa-pencil"></i>
                    <span>Modifier</span>
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm rounded-pill fw-semibold py-1.5 d-flex align-items-center justify-content-center gap-1.5"
                        onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                    <i class="fas fa-key"></i>
                    <span>Mot de passe</span>
                </button>
            </div>

            @if(!$isSelf && !$isSuperAdmin)
                <div class="d-flex justify-content-between align-items-center mt-2.5 pt-2.5 border-top">
                    <button type="button" class="btn btn-link text-decoration-none p-0 text-secondary small fw-medium d-flex align-items-center gap-1.5"
                            onclick="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}')">
                        <i class="fas fa-power-off text-warning"></i>
                        <span>Activer / Désactiver</span>
                    </button>

                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer {{ addslashes($user->name) }} ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-decoration-none p-0 text-danger small fw-medium d-flex align-items-center gap-1">
                            <i class="fas fa-trash-can"></i>
                            <span>Supprimer</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-4 border">
            <i class="fas fa-users-slash fa-3x text-muted opacity-50 mb-3"></i>
            <h6 class="fw-bold text-dark">Aucun collaborateur trouvé</h6>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
    @endif

</div>

<!-- ========================================== -->
<!-- MODAL 1: CRÉER / MODIFIER COLLABORATEUR   -->
<!-- ========================================== -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-user-shield text-danger" id="userModalIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="userModalLabel">Nouveau Collaborateur</h5>
                        <p class="small text-white-50 mb-0" id="userModalSub">Définissez les accès, rôles et privilèges du membre</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form id="userForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <input type="hidden" name="_method" id="userFormMethod" value="POST">

                <div class="modal-body p-4 bg-light">
                    <div class="row g-4">

                        <!-- Left Column: User Profile & Security -->
                        <div class="col-12 col-lg-5">
                            <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5 mb-3">
                                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-id-card text-primary"></i> Informations Personnelles
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="user_name" class="form-control rounded-3" placeholder="Ex: Karim Benali" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Adresse Email (Identifiant de connexion) <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="user_email" class="form-control rounded-3" placeholder="Ex: karim@winashop.com" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-dark">Téléphone</label>
                                        <input type="text" name="phone" id="user_phone" class="form-control rounded-3" placeholder="06 12 34 56 78">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-dark">Poste / Titre</label>
                                        <input type="text" name="job_title" id="user_job_title" class="form-control rounded-3" placeholder="Ex: Responsable Ventes">
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-dark">Statut du compte <span class="text-danger">*</span></label>
                                    <select name="status" id="user_status" class="form-select rounded-3" required>
                                        <option value="active">Actif (Accès autorisé)</option>
                                        <option value="inactive">Inactif (Accès désactivé)</option>
                                        <option value="suspended">Suspendu (Sécurité)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5 mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                        <i class="fas fa-lock text-warning"></i> Mot de Passe
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1" 
                                            onclick="generatePassword('user_password', 'password_copy_msg')">
                                        <i class="fas fa-wand-magic-sparkles me-1"></i> Générer fort
                                    </button>
                                </div>
                                <p class="text-muted small mb-2" id="password_hint">Minimum 8 caractères sécurisés.</p>

                                <div class="position-relative mb-2">
                                    <input type="text" name="password" id="user_password" class="form-control rounded-3 font-monospace pe-5" 
                                           placeholder="••••••••" autocomplete="new-password">
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none me-2 p-0" 
                                            onclick="copyPasswordToClipboard('user_password', 'password_copy_msg')" title="Copier le mot de passe">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div id="password_copy_msg" class="text-success small fw-semibold" style="display: none;">
                                    <i class="fas fa-check me-1"></i> Mot de passe généré et copié dans le presse-papier !
                                </div>
                            </div>

                            <!-- Primary Roles Box -->
                            <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5">
                                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                                    <i class="fas fa-shield-alt text-danger"></i> Rôles Principaux
                                </h6>
                                <p class="text-muted small mb-3">Sélectionnez un ou plusieurs rôles de base pour ce membre :</p>

                                <div class="d-flex flex-column gap-2">
                                    @foreach($roles as $role)
                                    @php
                                        $roleBorder = match($role->name) {
                                            'Admin' => 'border-danger-subtle',
                                            'Manager' => 'border-primary-subtle',
                                            'Staff' => 'border-success-subtle',
                                            default => 'border',
                                        };
                                        $roleIcon = match($role->name) {
                                            'Admin' => 'fa-crown text-danger',
                                            'Manager' => 'fa-user-tie text-primary',
                                            'Staff' => 'fa-user-check text-success',
                                            default => 'fa-shield text-secondary',
                                        };
                                    @endphp
                                    <label class="d-flex align-items-start gap-2.5 p-2.5 rounded-3 border {{ $roleBorder }} bg-light cursor-pointer hover-bg-white transition-all mb-0">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                               id="role_check_{{ $role->id }}" 
                                               class="form-check-input mt-1 role-checkbox"
                                               data-role-name="{{ $role->name }}"
                                               data-permissions="{{ json_encode($role->permissions->pluck('name')) }}"
                                               onchange="handleRoleCheckChange()">
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas {{ $roleIcon }}"></i>
                                                <span class="fw-bold text-dark small">{{ $role->name }}</span>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">
                                                {{ $role->permissions->count() }} autorisations incluses par défaut
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Granular Permissions Overrides -->
                        <div class="col-12 col-lg-7">
                            <div class="card border-0 rounded-4 shadow-xs bg-white p-3.5 h-100">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <i class="fas fa-sliders text-success"></i> Permissions Granulaires Détaillées
                                        </h6>
                                        <p class="text-muted small mb-0">Personnalisez finement chaque module pour ce collaborateur</p>
                                    </div>
                                    <div class="d-flex gap-1.5">
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="toggleAllPermissions(true)">
                                            Tout cocher
                                        </button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="toggleAllPermissions(false)">
                                            Tout décocher
                                        </button>
                                    </div>
                                </div>

                                <div class="accordion d-flex flex-column gap-2.5" id="permissionsAccordion">
                                    @foreach($permissionModules as $modKey => $module)
                                    <div class="accordion-item border rounded-3 overflow-hidden">
                                        <h2 class="accordion-header" id="heading-{{ $modKey }}">
                                            <button class="accordion-button collapsed py-2.5 px-3 bg-light fw-bold text-dark d-flex align-items-center gap-2" 
                                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $modKey }}" 
                                                    aria-expanded="false" aria-controls="collapse-{{ $modKey }}" style="font-size: 0.85rem;">
                                                <i class="{{ $module['icon'] }}" style="color: {{ $module['color'] }}; width: 20px;"></i>
                                                <span>{{ $module['title'] }}</span>
                                                <span class="badge bg-white text-secondary border rounded-pill ms-auto me-2 px-2 py-0.5" style="font-size: 0.68rem;">
                                                    {{ count($module['permissions']) }} règles
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $modKey }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $modKey }}" data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body p-3 bg-white">
                                                <div class="d-flex justify-content-end mb-2">
                                                    <button type="button" class="btn btn-link text-decoration-none p-0 small fw-semibold text-primary" 
                                                            onclick="toggleModuleCheckboxes('{{ $modKey }}', true)">
                                                        Cocher le groupe
                                                    </button>
                                                    <span class="text-muted mx-2">•</span>
                                                    <button type="button" class="btn btn-link text-decoration-none p-0 small fw-semibold text-secondary" 
                                                            onclick="toggleModuleCheckboxes('{{ $modKey }}', false)">
                                                        Décocher
                                                    </button>
                                                </div>
                                                <div class="d-flex flex-column gap-2 module-group-{{ $modKey }}">
                                                    @foreach($module['permissions'] as $permName => $permDesc)
                                                    <label class="d-flex align-items-center justify-content-between p-2 rounded-2 border bg-light-subtle cursor-pointer hover-bg-light transition-all mb-0">
                                                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                                                            <input type="checkbox" name="permissions[]" value="{{ $permName }}" 
                                                                   id="perm_check_{{ $permName }}" 
                                                                   class="form-check-input perm-checkbox m-0" 
                                                                   style="width: 18px; height: 18px;">
                                                            <div>
                                                                <span class="fw-semibold text-dark d-block font-monospace" style="font-size: 0.78rem;">
                                                                    {{ $permName }}
                                                                </span>
                                                                <span class="text-muted small d-block" style="font-size: 0.72rem;">
                                                                    {{ $permDesc }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 inherited-badge d-none" 
                                                              id="inherited_{{ $permName }}" style="font-size: 0.65rem;">
                                                            Inclus dans rôle
                                                        </span>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-white py-3 px-4 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-brand-primary rounded-pill px-4.5 py-2 text-white fw-bold shadow-sm" 
                            style="background: #e11d48; border-color: #e11d48;" id="userSubmitBtn">
                        <i class="fas fa-check-circle me-1.5"></i> Enregistrer le Collaborateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: RÉINITIALISER MOT DE PASSE        -->
<!-- ========================================== -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Réinitialiser le Mot de Passe</h5>
                        <p class="small text-white-50 mb-0">Générez un nouveau code d'accès pour ce compte</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form id="passwordForm" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 rounded-3 p-3 bg-white shadow-xs mb-3">
                        <div class="text-muted small">Collaborateur concerné :</div>
                        <div class="fw-bold text-dark" id="pwd_modal_name"></div>
                        <div class="text-muted small font-monospace" id="pwd_modal_email"></div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <label class="form-label small fw-bold text-dark mb-0">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1"
                                    onclick="generatePassword('modal_new_password', 'pwd_modal_copy_msg')">
                                <i class="fas fa-wand-magic-sparkles me-1"></i> Générer fort
                            </button>
                        </div>
                        <div class="position-relative">
                            <input type="text" name="password" id="modal_new_password" class="form-control rounded-3 font-monospace pe-5" 
                                   placeholder="Minimum 8 caractères" required minlength="8">
                            <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none me-2 p-0" 
                                    onclick="copyPasswordToClipboard('modal_new_password', 'pwd_modal_copy_msg')" title="Copier le mot de passe">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <div id="pwd_modal_copy_msg" class="text-success small fw-semibold mt-1" style="display: none;">
                            <i class="fas fa-check me-1"></i> Mot de passe généré et copié !
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 small mb-0 py-2.5">
                        <i class="fas fa-info-circle me-1"></i> Le nouveau mot de passe prendra effet immédiatement. Pensez à le communiquer en toute sécurité au collaborateur.
                    </div>
                </div>

                <div class="modal-footer bg-white py-2.5 px-4 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">
                        <i class="fas fa-shield-alt me-1"></i> Appliquer le nouveau mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let bsUserModal = null;
let bsPwdModal = null;

document.addEventListener('DOMContentLoaded', function() {
    const userModalEl = document.getElementById('userModal');
    if (userModalEl) bsUserModal = new bootstrap.Modal(userModalEl);

    const pwdModalEl = document.getElementById('passwordModal');
    if (pwdModalEl) bsPwdModal = new bootstrap.Modal(pwdModalEl);
});

// Open creation modal
function openCreateModal() {
    document.getElementById('userModalLabel').textContent = 'Nouveau Collaborateur';
    document.getElementById('userModalSub').textContent = 'Créer un nouveau compte d\'accès avec rôles et autorisations';
    document.getElementById('userModalIcon').className = 'fas fa-user-plus text-danger';
    document.getElementById('userFormMethod').value = 'POST';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userSubmitBtn').innerHTML = '<i class="fas fa-check-circle me-1.5"></i> Créer le Collaborateur';

    // Reset fields
    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_phone').value = '';
    document.getElementById('user_job_title').value = '';
    document.getElementById('user_status').value = 'active';
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = true;
    document.getElementById('password_hint').textContent = 'Obligatoire à la création (min 8 caractères).';

    // Uncheck all roles and permissions
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.inherited-badge').forEach(badge => badge.classList.add('d-none'));

    // Auto-generate initial strong password
    generatePassword('user_password', 'password_copy_msg');

    if (bsUserModal) bsUserModal.show();
}

// Open edit modal via AJAX fetch
function openEditModal(userId) {
    document.getElementById('userModalLabel').textContent = 'Chargement en cours...';
    document.getElementById('userModalIcon').className = 'fas fa-spinner fa-spin text-primary';

    fetch(`/users/${userId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('userModalLabel').textContent = `Modifier : ${data.name}`;
        document.getElementById('userModalSub').textContent = `ID #${data.id} • ${data.email}`;
        document.getElementById('userModalIcon').className = 'fas fa-pencil text-primary';
        document.getElementById('userFormMethod').value = 'PUT';
        document.getElementById('userForm').action = `/users/${userId}`;
        document.getElementById('userSubmitBtn').innerHTML = '<i class="fas fa-save me-1.5"></i> Enregistrer les Modifications';

        document.getElementById('user_name').value = data.name || '';
        document.getElementById('user_email').value = data.email || '';
        document.getElementById('user_phone').value = data.phone || '';
        document.getElementById('user_job_title').value = data.job_title || '';
        document.getElementById('user_status').value = data.status || 'active';
        document.getElementById('user_password').value = '';
        document.getElementById('user_password').required = false;
        document.getElementById('password_hint').textContent = 'Laissez vide pour conserver le mot de passe actuel.';
        document.getElementById('password_copy_msg').style.display = 'none';

        // Check assigned roles
        document.querySelectorAll('.role-checkbox').forEach(cb => {
            cb.checked = data.roles.includes(cb.value);
        });

        // Check direct permissions
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = data.direct_permissions.includes(cb.value);
        });

        handleRoleCheckChange();

        if (bsUserModal) bsUserModal.show();
    })
    .catch(err => {
        alert('Erreur lors du chargement des données : ' + err.message);
    });
}

// Open Password Modal
function openPasswordModal(userId, userName, userEmail) {
    document.getElementById('pwd_modal_name').textContent = userName;
    document.getElementById('pwd_modal_email').textContent = userEmail;
    document.getElementById('passwordForm').action = `/users/${userId}/reset-password`;
    document.getElementById('modal_new_password').value = '';
    document.getElementById('pwd_modal_copy_msg').style.display = 'none';

    generatePassword('modal_new_password', 'pwd_modal_copy_msg');

    if (bsPwdModal) bsPwdModal.show();
}

// 1-Click Strong Password Generator
function generatePassword(targetInputId, msgId) {
    const uppercase = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    const lowercase = "abcdefghijkmnopqrstuvwxyz";
    const numbers = "23456789";
    const special = "#@!%*?";
    
    // Prefix 'Wina-' + random combination
    let result = "Wina-";
    const allChars = uppercase + lowercase + numbers + special;
    
    for (let i = 0; i < 8; i++) {
        result += allChars.charAt(Math.floor(Math.random() * allChars.length));
    }
    
    const input = document.getElementById(targetInputId);
    if (input) {
        input.value = result;
        copyPasswordToClipboard(targetInputId, msgId);
    }
}

// Copy password to clipboard helper
function copyPasswordToClipboard(targetInputId, msgId) {
    const input = document.getElementById(targetInputId);
    if (!input || !input.value) return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value);
    } else {
        input.select();
        document.execCommand('copy');
    }

    const msg = document.getElementById(msgId);
    if (msg) {
        msg.style.display = 'block';
        setTimeout(() => { msg.style.display = 'none'; }, 4000);
    }
}

// Toggle permission checkboxes per module
function toggleModuleCheckboxes(modKey, state) {
    const container = document.querySelector(`.module-group-${modKey}`);
    if (container) {
        container.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = state;
        });
    }
}

// Toggle all permissions across all modules
function toggleAllPermissions(state) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = state;
    });
}

// React when roles are checked/unchecked to visually indicate inherited permissions
function handleRoleCheckChange() {
    const inheritedPerms = new Set();

    document.querySelectorAll('.role-checkbox:checked').forEach(cb => {
        try {
            const perms = JSON.parse(cb.getAttribute('data-permissions') || '[]');
            perms.forEach(p => inheritedPerms.add(p));
        } catch(e) {}
    });

    document.querySelectorAll('.inherited-badge').forEach(badge => {
        const permName = badge.id.replace('inherited_', '');
        if (inheritedPerms.has(permName)) {
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    });
}

// Quick AJAX toggle for status
function toggleUserStatus(userId, userName) {
    if (!confirm(`Changer le statut d'accès de ${userName} ?`)) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const dCol = document.getElementById(`status-col-${userId}`);
            if (dCol) dCol.innerHTML = data.badge;

            const mCol = document.getElementById(`mob-status-${userId}`);
            if (mCol) mCol.innerHTML = data.badge;

            const btn = document.getElementById(`btn-toggle-${userId}`);
            if (btn) {
                btn.innerHTML = data.status === 'active' 
                    ? '<i class="fas fa-toggle-on text-success fa-lg"></i>' 
                    : '<i class="fas fa-toggle-off text-muted fa-lg"></i>';
            }
        } else {
            alert(data.message || 'Action impossible');
        }
    })
    .catch(err => {
        alert('Erreur : ' + err.message);
    });
}
</script>
@endpush
@endsection
