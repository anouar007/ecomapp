@extends('layouts.app')

@section('title', 'Équipe & Collaborateurs')

@push('styles')
<style>
/* ==========================================================================
   MOBILE-FIRST TEAM MANAGEMENT STYLES
   ========================================================================== */

/* Chips Bar Horizontal Scroll */
.chips-scroll {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 2px;
}
.chips-scroll::-webkit-scrollbar {
    display: none;
}
.chip-item {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.45rem 0.9rem;
    font-size: 0.82rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
}
.chip-item:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chip-item.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 4px rgba(15, 23, 42, 0.15);
}
.chip-item.active .badge {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

/* Mobile User Card Styling */
.user-card {
    border-radius: 16px !important;
    border: 1px solid #edf2f7;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.user-card:active {
    transform: scale(0.995);
}

/* Avatar with Status Ring */
.avatar-wrap {
    position: relative;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
}
.avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    color: #ffffff;
    font-size: 1rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2.5px solid #ffffff;
}
.status-dot.active { background-color: #10b981; }
.status-dot.inactive { background-color: #94a3b8; }
.status-dot.suspended { background-color: #ef4444; }

/* Quick Contact Touch Pills */
.contact-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 9999px !important;
    font-size: 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    text-decoration: none;
    max-width: 100%;
}
.contact-pill:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* iOS-Style Switches */
.switch-ios {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin: 0;
}
.switch-ios input {
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 24px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.switch-ios input:checked + .switch-slider {
    background-color: #10b981;
}
.switch-ios input:checked + .switch-slider:before {
    transform: translateX(20px);
}

/* Role Selector Cards inside Modal */
.role-select-card {
    cursor: pointer;
    border: 2px solid #e2e8f0;
    border-radius: 14px !important;
    padding: 1rem;
    background: #ffffff;
    transition: all 0.2s ease;
    position: relative;
}
.role-select-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.role-select-card.selected {
    border-color: #dc2626;
    background: #fff1f2;
    box-shadow: 0 0 0 1px #dc2626;
}
.role-select-card.selected-manager {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 0 0 1px #2563eb;
}
.role-select-card.selected-staff {
    border-color: #059669;
    background: #ecfdf5;
    box-shadow: 0 0 0 1px #059669;
}
.role-check-indicator {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    font-size: 0.65rem;
}
.role-select-card.selected .role-check-indicator {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}
.role-select-card.selected-manager .role-check-indicator {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}
.role-select-card.selected-staff .role-check-indicator {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
}

/* Password Strength Bar */
.pwd-strength-bar {
    height: 5px;
    border-radius: 3px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 6px;
}
.pwd-strength-fill {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
}

/* Modal Fullscreen on Mobile */
@media (max-width: 767.98px) {
    #userModal .modal-dialog,
    #passwordModal .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100%;
    }
    #userModal .modal-content,
    #passwordModal .modal-content {
        height: 100%;
        border-radius: 0 !important;
        border: none;
    }
    #userModal .modal-body,
    #passwordModal .modal-body {
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 90px;
    }
    .modal-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
        z-index: 1055;
        padding: 0.85rem 1rem calc(0.85rem + env(safe-area-inset-bottom, 0px)) 1rem;
    }
}

/* Floating Action Button on Mobile */
.fab-create-user {
    position: fixed;
    bottom: 24px;
    right: 20px;
    z-index: 990;
    border-radius: 9999px !important;
    padding: 0.85rem 1.35rem;
    box-shadow: 0 8px 20px rgba(220, 38, 38, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
}
@media (min-width: 768px) {
    .fab-create-user {
        display: none !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3">

    <!-- Mobile Header -->
    <div class="d-flex align-items-center justify-content-between mb-3 pt-1">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 fw-bold mb-0 text-dark">Équipe & Rôles</h1>
                <span class="badge bg-dark text-white rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                    {{ $kpi['total_team'] }} membres
                </span>
            </div>
            <p class="text-muted small mb-0 d-none d-sm-block">Comptes, accès et permissions sécurisées</p>
        </div>

        <div class="d-flex align-items-center gap-1.5">
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center gap-1.5" title="Gérer les rôles globaux">
                <i class="fas fa-shield-halved text-secondary"></i>
                <span class="d-none d-md-inline small fw-semibold">Rôles globaux</span>
            </a>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 d-none d-md-inline-flex align-items-center gap-1.5 shadow-sm fw-bold"
                    onclick="openCreateModal()">
                <i class="fas fa-plus"></i>
                <span>Nouveau collaborateur</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-xs mb-3 d-flex align-items-center gap-2 py-2.5 px-3" role="alert">
        <i class="fas fa-circle-check fs-5 text-success flex-shrink-0"></i>
        <div class="small fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-xs mb-3 d-flex align-items-center gap-2 py-2.5 px-3" role="alert">
        <i class="fas fa-circle-exclamation fs-5 text-danger flex-shrink-0"></i>
        <div class="small fw-semibold">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-xs mb-3 py-2.5 px-3" role="alert">
        <div class="d-flex align-items-center gap-1.5 mb-1.5">
            <i class="fas fa-triangle-exclamation text-danger"></i>
            <span class="small fw-bold">Erreur de validation :</span>
        </div>
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Mobile Scrollable Filter Chips -->
    <div class="chips-scroll mb-2.5">
        <a href="{{ route('users.index', array_merge(request()->except('role'), ['role' => 'all'])) }}" 
           class="chip-item {{ $roleFilter === 'all' && $statusFilter === 'all' ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Tous</span>
            <span class="badge bg-light text-dark rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['total_users'] }}</span>
        </a>
        <a href="{{ route('users.index', ['role' => 'Admin']) }}" 
           class="chip-item {{ $roleFilter === 'Admin' ? 'active' : '' }}">
            <i class="fas fa-crown text-danger"></i>
            <span>Admins</span>
            <span class="badge bg-danger-subtle text-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['admins'] }}</span>
        </a>
        <a href="{{ route('users.index', ['role' => 'Manager']) }}" 
           class="chip-item {{ $roleFilter === 'Manager' ? 'active' : '' }}">
            <i class="fas fa-user-tie text-primary"></i>
            <span>Managers</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['managers'] }}</span>
        </a>
        <a href="{{ route('users.index', ['role' => 'Staff']) }}" 
           class="chip-item {{ $roleFilter === 'Staff' ? 'active' : '' }}">
            <i class="fas fa-user-check text-success"></i>
            <span>Staff</span>
            <span class="badge bg-success-subtle text-success rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['staff'] }}</span>
        </a>
        <a href="{{ route('users.index', ['status' => 'inactive']) }}" 
           class="chip-item {{ $statusFilter === 'inactive' ? 'active' : '' }}">
            <i class="fas fa-pause-circle text-secondary"></i>
            <span>Inactifs</span>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['inactive'] }}</span>
        </a>
    </div>

    <!-- Search Input (Mobile Optimized) -->
    <div class="mb-3">
        <form method="GET" action="{{ route('users.index') }}" class="position-relative">
            @if($roleFilter !== 'all')
                <input type="hidden" name="role" value="{{ $roleFilter }}">
            @endif
            @if($statusFilter !== 'all')
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <div class="input-group shadow-xs">
                <span class="input-group-text bg-white border-end-0 ps-3 text-muted" style="border-radius: 12px 0 0 12px !important;">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" 
                       class="form-control border-start-0 border-end-0 bg-white py-2" 
                       placeholder="Rechercher par nom, email, téléphone..."
                       style="font-size: 0.9rem;">
                @if($search !== '')
                    <a href="{{ route('users.index') }}" class="input-group-text bg-white border-start-0 text-muted pe-3 text-decoration-none">
                        <i class="fas fa-circle-xmark"></i>
                    </a>
                @endif
                <button type="submit" class="btn btn-dark fw-semibold px-3" style="border-radius: 0 12px 12px 0 !important;">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================== -->
    <!-- 1. MOBILE USER CARDS (Mobile-First View)   -->
    <!-- ========================================== -->
    <div class="d-block d-lg-none">
        @forelse($users as $user)
        @php
            $isSelf = auth()->id() === $user->id;
            $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
            $primaryRole = optional($user->roles->first())->name;
            $avatarGradient = match($primaryRole) {
                'Admin' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
                'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                default => 'linear-gradient(135deg, #64748b 0%, #334155 100%)',
            };
            $statusClass = $user->status ?? 'active';
        @endphp
        <div class="user-card p-3 mb-2.5 position-relative">
            
            <!-- Top Section: Avatar + Name + Status Switch -->
            <div class="d-flex align-items-start justify-content-between gap-2.5 mb-2.5">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <div class="avatar-wrap">
                        <div class="avatar-circle" style="background: {{ $avatarGradient }};">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="status-dot {{ $statusClass }}" id="mob-dot-{{ $user->id }}"></div>
                    </div>
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $user->name }}</span>
                            @if($isSelf)
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-1.5 py-0.2" style="font-size: 0.65rem;">Vous</span>
                            @endif
                            @if($isSuperAdmin)
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-1.5 py-0.2" style="font-size: 0.65rem;">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small text-truncate" style="font-size: 0.78rem;">
                            {{ $user->email }}
                        </div>
                        @if($user->job_title)
                            <div class="text-secondary small fw-medium mt-0.5" style="font-size: 0.74rem;">
                                <i class="fas fa-id-badge text-muted me-1"></i>{{ $user->job_title }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Status Toggle Switch (iOS Style) -->
                @if(!$isSelf && !$isSuperAdmin)
                <div class="d-flex flex-column align-items-end flex-shrink-0">
                    <label class="switch-ios" title="Activer / Désactiver">
                        <input type="checkbox" id="switch-mob-{{ $user->id }}" 
                               {{ $user->status === 'active' ? 'checked' : '' }}
                               onchange="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', this)">
                        <span class="switch-slider"></span>
                    </label>
                    <span class="text-muted mt-1" style="font-size: 0.65rem;" id="mob-status-label-{{ $user->id }}">
                        {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                @else
                <div class="flex-shrink-0" id="mob-status-{{ $user->id }}">
                    {!! $user->status_badge !!}
                </div>
                @endif
            </div>

            <!-- Contact & Last Seen Chips -->
            <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                @if($user->phone)
                <a href="tel:{{ $user->phone }}" class="contact-pill">
                    <i class="fas fa-phone text-success" style="font-size: 0.7rem;"></i>
                    <span>{{ $user->phone }}</span>
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="contact-pill text-success" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                @endif
                <a href="mailto:{{ $user->email }}" class="contact-pill">
                    <i class="fas fa-envelope text-primary" style="font-size: 0.7rem;"></i>
                    <span>Email</span>
                </a>
                <div class="contact-pill text-muted">
                    <i class="fas fa-clock" style="font-size: 0.7rem;"></i>
                    <span>
                        @if($user->last_login_at)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            Jamais connecté
                        @endif
                    </span>
                </div>
            </div>

            <!-- Assigned Roles & Permissions Summary -->
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-2.5">
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    @forelse($user->roles as $role)
                        @php
                            $badgeStyle = match($role->name) {
                                'Admin' => 'bg-danger text-white',
                                'Manager' => 'bg-primary text-white',
                                'Staff' => 'bg-success text-white',
                                default => 'bg-secondary text-white',
                            };
                        @endphp
                        <span class="badge {{ $badgeStyle }} rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.7rem;">
                            {{ $role->name }}
                        </span>
                    @empty
                        <span class="badge bg-white text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Client / Sans rôle</span>
                    @endforelse

                    @if($user->permissions->count() > 0)
                        <span class="badge bg-purple-subtle text-purple border rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem; background: #f3e8ff; color: #7e22ce;">
                            +{{ $user->permissions->count() }} directes
                        </span>
                    @endif
                </div>
                <span class="text-muted small" style="font-size: 0.7rem;">ID #{{ $user->id }}</span>
            </div>

            <!-- Touch Actions Row -->
            <div class="d-flex gap-2 pt-1 border-top">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5"
                        onclick="openEditModal({{ $user->id }})">
                    <i class="fas fa-pencil"></i>
                    <span>Modifier</span>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill flex-grow-1 py-1.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5"
                        onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                    <i class="fas fa-key text-warning"></i>
                    <span>Mot de passe</span>
                </button>
                @if(!$isSelf && !$isSuperAdmin)
                <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                      onsubmit="return confirm('Supprimer définitivement le collaborateur {{ addslashes($user->name) }} ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1.5" title="Supprimer">
                        <i class="fas fa-trash-can"></i>
                    </button>
                </form>
                @endif
            </div>

        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-4 border p-4">
            <i class="fas fa-users-slash fa-3x text-muted opacity-50 mb-3"></i>
            <h6 class="fw-bold text-dark">Aucun collaborateur trouvé</h6>
            <p class="text-muted small mb-3">Modifiez vos filtres de recherche ou ajoutez un nouveau membre.</p>
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1.5" onclick="openCreateModal()">
                + Nouveau collaborateur
            </button>
        </div>
        @endforelse
    </div>

    <!-- ========================================== -->
    <!-- 2. DESKTOP TABLE VIEW (>= 992px)           -->
    <!-- ========================================== -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden d-none d-lg-block mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 32%;">Collaborateur</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 22%;">Rôles & Permissions</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 14%;">Statut</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 16%;">Dernière Connexion</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 16%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                    @php
                        $isSelf = auth()->id() === $user->id;
                        $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
                        $primaryRole = optional($user->roles->first())->name;
                        $avatarGradient = match($primaryRole) {
                            'Admin' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
                            'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                            'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                            default => 'linear-gradient(135deg, #64748b 0%, #334155 100%)',
                        };
                    @endphp
                    <tr class="hover-bg-light transition-all">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-wrap">
                                    <div class="avatar-circle" style="background: {{ $avatarGradient }};">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="status-dot {{ $user->status ?? 'active' }}" id="desk-dot-{{ $user->id }}"></div>
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
                                        <div class="text-muted small mt-0.5 fw-medium" style="font-size: 0.74rem;">
                                            <i class="fas fa-id-badge me-1 text-secondary opacity-75"></i>{{ $user->job_title }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
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
                        <td class="py-3" id="desk-status-col-{{ $user->id }}">
                            {!! $user->status_badge !!}
                        </td>
                        <td class="py-3">
                            @if($user->last_login_at)
                                <div class="text-dark fw-medium small">
                                    {{ $user->last_login_at->diffForHumans() }}
                                </div>
                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">
                                    {{ $user->last_login_at->format('d/m/Y H:i') }}
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Jamais connecté</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-primary shadow-xs border"
                                        onclick="openEditModal({{ $user->id }})" title="Modifier">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-warning shadow-xs border"
                                        onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')" 
                                        title="Mot de passe">
                                    <i class="fas fa-key"></i>
                                </button>
                                @if(!$isSelf && !$isSuperAdmin)
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-secondary shadow-xs border"
                                            onclick="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            id="desk-btn-toggle-{{ $user->id }}"
                                            title="Activer / Désactiver">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }} fa-lg"></i>
                                    </button>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline"
                                          onsubmit="return confirm('Supprimer définitivement le collaborateur {{ addslashes($user->name) }} ?');">
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
                                <p class="text-muted small mb-3">Aucun utilisateur ne correspond à vos filtres.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
    @endif

</div>

<!-- Mobile Floating Action Button (FAB) -->
<button type="button" class="btn btn-danger fab-create-user" onclick="openCreateModal()">
    <i class="fas fa-user-plus"></i>
    <span>+ Collaborateur</span>
</button>

<!-- ======================================================= -->
<!-- MODAL PRO: NOUVEAU / MODIFIER COLLABORATEUR             -->
<!-- ======================================================= -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            
            <!-- Modal Header -->
            <div class="modal-header bg-dark text-white py-3 px-3 px-md-4 border-0">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-shield text-danger" id="userModalIcon"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="modal-title fw-bold mb-0 text-truncate" id="userModalLabel" style="font-size: 1.05rem;">Nouveau Collaborateur</h5>
                        <p class="small text-white-50 mb-0 text-truncate" id="userModalSub">Définissez les accès et privilèges de l'équipe</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <!-- Tab Navigation Header (Step-by-step Pro Feel) -->
            <div class="bg-light border-bottom px-3 px-md-4 pt-2">
                <ul class="nav nav-pills gap-1 flex-nowrap overflow-auto" id="userModalTabs" role="tablist" style="scrollbar-width: none;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3 py-1.5 small fw-semibold text-nowrap" id="tab-identity-btn" 
                                data-bs-toggle="pill" data-bs-target="#tab-identity" type="button" role="tab">
                            <i class="fas fa-id-card me-1.5"></i>1. Identité & Compte
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold text-nowrap" id="tab-roles-btn" 
                                data-bs-toggle="pill" data-bs-target="#tab-roles" type="button" role="tab">
                            <i class="fas fa-shield-halved me-1.5"></i>2. Rôle & Accès
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1.5 small fw-semibold text-nowrap" id="tab-permissions-btn" 
                                data-bs-toggle="pill" data-bs-target="#tab-permissions" type="button" role="tab">
                            <i class="fas fa-sliders me-1.5"></i>3. Permissions Granulaires
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" id="perms-tab-counter">0</span>
                        </button>
                    </li>
                </ul>
            </div>

            <form id="userForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <input type="hidden" name="_method" id="userFormMethod" value="POST">

                <div class="modal-body p-3 p-md-4">
                    <div class="tab-content" id="userModalTabContent">

                        <!-- TAB 1: IDENTITÉ & MOT DE PASSE -->
                        <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
                            <div class="row g-3">
                                
                                <!-- Personal Info Card -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3.5 rounded-4 bg-light border h-100">
                                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                            <i class="fas fa-user-circle text-primary"></i> Informations Personnelles
                                        </h6>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Nom complet <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="user_name" class="form-control form-control-lg bg-white rounded-3 fs-6" 
                                                   placeholder="Ex: Yassine El Amrani" required autocomplete="name">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Adresse Email (Identifiant) <span class="text-danger">*</span></label>
                                            <input type="email" name="email" id="user_email" class="form-control form-control-lg bg-white rounded-3 fs-6" 
                                                   placeholder="yassine@winashop.com" required autocomplete="email">
                                        </div>

                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-dark">Téléphone</label>
                                                <input type="tel" name="phone" id="user_phone" class="form-control bg-white rounded-3" 
                                                       placeholder="06 12 34 56 78" autocomplete="tel">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-dark">Poste / Fonction</label>
                                                <input type="text" name="job_title" id="user_job_title" class="form-control bg-white rounded-3" 
                                                       placeholder="Ex: Responsable Ventes">
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <label class="form-label small fw-bold text-dark">Statut du compte</label>
                                            <select name="status" id="user_status" class="form-select bg-white rounded-3">
                                                <option value="active">Actif (Accès immédiatement autorisé)</option>
                                                <option value="inactive">Inactif (Connexion bloquée)</option>
                                                <option value="suspended">Suspendu (Sécurité / Audit)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Card -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3.5 rounded-4 bg-light border h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                    <i class="fas fa-lock text-warning"></i> Sécurité & Mot de Passe
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1"
                                                        onclick="generateStrongPassword('user_password')">
                                                    <i class="fas fa-wand-magic-sparkles me-1"></i> Générer auto
                                                </button>
                                            </div>
                                            <p class="text-muted small mb-3" id="password_hint">Minimum 8 caractères recommandés.</p>

                                            <div class="position-relative mb-2">
                                                <input type="text" name="password" id="user_password" 
                                                       class="form-control form-control-lg bg-white rounded-3 font-monospace pe-5 fs-6" 
                                                       placeholder="Tapez ou générez un code fort" 
                                                       oninput="checkPasswordStrength(this.value)"
                                                       autocomplete="new-password">
                                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none me-2 p-0" 
                                                        onclick="copyToClipboard('user_password', 'pwd-copied-toast')" title="Copier le mot de passe">
                                                    <i class="fas fa-copy fa-lg"></i>
                                                </button>
                                            </div>

                                            <!-- Strength Indicator -->
                                            <div class="pwd-strength-bar">
                                                <div class="pwd-strength-fill" id="pwd-strength-fill"></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="text-muted small" style="font-size: 0.72rem;">Niveau de sécurité :</span>
                                                <span class="fw-bold small" style="font-size: 0.72rem;" id="pwd-strength-text">En attente</span>
                                            </div>

                                            <div id="pwd-copied-toast" class="alert alert-success border-0 py-1.5 px-2.5 mt-2 mb-0 rounded-3 small fw-semibold" style="display: none;">
                                                <i class="fas fa-check-circle me-1"></i> Mot de passe copié dans le presse-papier !
                                            </div>
                                        </div>

                                        <div class="mt-4 p-2.5 rounded-3 bg-white border small text-muted">
                                            <i class="fas fa-shield-alt text-success me-1"></i> Les mots de passe sont chiffrés avec Bcrypt (256-bit). Le collaborateur pourra le modifier à tout moment.
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- TAB 2: RÔLES & ACCÈS DE BASE -->
                        <div class="tab-pane fade" id="tab-roles" role="tabpanel">
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-1">Sélectionnez le rôle principal du collaborateur</h6>
                                <p class="text-muted small mb-0">Chaque rôle débloque automatiquement un ensemble d'autorisations prédéfinies.</p>
                            </div>

                            <div class="row g-3">
                                @foreach($roles as $role)
                                @php
                                    $roleKey = strtolower($role->name);
                                    $cardColor = match($role->name) {
                                        'Admin' => 'selected',
                                        'Manager' => 'selected-manager',
                                        'Staff' => 'selected-staff',
                                        default => 'selected',
                                    };
                                    $icon = match($role->name) {
                                        'Admin' => 'fa-crown text-danger',
                                        'Manager' => 'fa-user-tie text-primary',
                                        'Staff' => 'fa-user-check text-success',
                                        default => 'fa-shield-halved text-secondary',
                                    };
                                    $desc = match($role->name) {
                                        'Admin' => 'Accès total et absolu à tous les modules, configurations et données de la boutique.',
                                        'Manager' => 'Gestion complète des opérations : commandes, catalogue, stocks, clients et rapports analytiques.',
                                        'Staff' => 'Accès opérationnel quotidien : traitement des commandes, gestion des produits et consultation des stocks.',
                                        default => 'Rôle standard avec autorisations spécifiques.',
                                    };
                                @endphp
                                <div class="col-12 col-md-4">
                                    <div class="role-select-card h-100" id="role-card-{{ $role->name }}" 
                                         onclick="selectRoleCard('{{ $role->name }}')">
                                        <div class="role-check-indicator">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="d-flex align-items-center gap-2.5 mb-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 40px; height: 40px;">
                                                <i class="fas {{ $icon }} fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">{{ $role->name }}</h6>
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.68rem;">
                                                    {{ $role->permissions->count() }} autorisations
                                                </span>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.8rem; line-height: 1.4;">
                                            {{ $desc }}
                                        </p>
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                               id="role-input-{{ $role->name }}" 
                                               class="d-none role-checkbox"
                                               data-role-name="{{ $role->name }}"
                                               data-permissions="{{ json_encode($role->permissions->pluck('name')) }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="alert alert-info border-0 rounded-4 p-3 mt-4 d-flex align-items-start gap-2.5">
                                <i class="fas fa-info-circle text-primary mt-0.5"></i>
                                <div class="small">
                                    <strong class="d-block mb-0.5 text-dark">Besoin de personnaliser les autorisations ?</strong>
                                    Vous pouvez ajuster finement les accès du collaborateur dans l'onglet suivant <strong>3. Permissions Granulaires</strong>.
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: PERMISSIONS GRANULAIRES SUR-MESURE -->
                        <div class="tab-pane fade" id="tab-permissions" role="tabpanel">
                            
                            <!-- Search & Quick Filters Bar -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 mb-3 bg-light p-2.5 rounded-3 border">
                                <div class="position-relative flex-grow-1">
                                    <input type="text" id="perm-search-box" class="form-control form-control-sm bg-white rounded-pill ps-4" 
                                           placeholder="Filtrer les permissions (ex: stock, facture, commande)..."
                                           oninput="filterPermissionsList(this.value)">
                                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted small"></i>
                                </div>
                                <div class="d-flex gap-1.5 justify-content-end">
                                    <button type="button" class="btn btn-xs btn-outline-dark rounded-pill px-2.5 py-1" onclick="toggleAllPerms(true)">
                                        Tout cocher
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="toggleAllPerms(false)">
                                        Tout décocher
                                    </button>
                                </div>
                            </div>

                            <!-- Modules Grid -->
                            <div class="row g-3" id="perms-container">
                                @foreach($permissionModules as $modKey => $module)
                                <div class="col-12 col-md-6 perm-module-card" data-module-key="{{ $modKey }}">
                                    <div class="card border rounded-4 shadow-xs h-100 overflow-hidden">
                                        <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="{{ $module['icon'] }}" style="color: {{ $module['color'] }}; font-size: 0.95rem;"></i>
                                                <span class="fw-bold text-dark small">{{ $module['title'] }}</span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-primary small fw-semibold" 
                                                        onclick="toggleModulePerms('{{ $modKey }}', true)" style="font-size: 0.72rem;">
                                                    Cocher
                                                </button>
                                                <span class="text-muted small">/</span>
                                                <button type="button" class="btn btn-link text-decoration-none p-0 text-secondary small fw-semibold" 
                                                        onclick="toggleModulePerms('{{ $modKey }}', false)" style="font-size: 0.72rem;">
                                                    Décocher
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body p-2.5 d-flex flex-column gap-1.5 bg-light-subtle module-items-{{ $modKey }}">
                                            @foreach($module['permissions'] as $permName => $permDesc)
                                            <label class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-white border cursor-pointer hover-bg-light transition-all mb-0 perm-item"
                                                   data-perm-name="{{ strtolower($permName) }}" 
                                                   data-perm-desc="{{ strtolower($permDesc) }}">
                                                <div class="min-w-0 pe-2">
                                                    <span class="fw-bold text-dark d-block font-monospace" style="font-size: 0.78rem;">{{ $permName }}</span>
                                                    <span class="text-muted d-block small" style="font-size: 0.72rem; line-height: 1.3;">{{ $permDesc }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                    <span class="badge bg-primary-subtle text-primary border rounded-pill px-1.5 py-0.5 inherited-badge d-none" 
                                                          id="badge-inherited-{{ $permName }}" style="font-size: 0.65rem;">
                                                        Inclus
                                                    </span>
                                                    <label class="switch-ios mb-0">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permName }}" 
                                                               id="perm-switch-{{ $permName }}" 
                                                               class="perm-switch-input"
                                                               onchange="onPermSwitchChange()">
                                                        <span class="switch-slider"></span>
                                                    </label>
                                                </div>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>

                    </div>
                </div>

                <!-- Footer with Safe Area Support -->
                <div class="modal-footer modal-sticky-footer border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-md-none" id="modalPrevTabBtn" onclick="navigateModalTab(-1)">
                            Précédent
                        </button>
                        <button type="button" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold d-md-none" id="modalNextTabBtn" onclick="navigateModalTab(1)">
                            Suivant
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold text-white shadow-sm" id="userSubmitBtn">
                            <i class="fas fa-check-circle me-1.5"></i> Enregistrer
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: RÉINITIALISER MOT DE PASSE                       -->
<!-- ======================================================= -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-3 px-md-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Réinitialiser le Mot de Passe</h6>
                        <span class="small text-white-50" id="pwd_modal_name"></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form id="passwordForm" method="POST">
                @csrf
                <div class="modal-body p-3.5 bg-light">
                    <div class="card border-0 rounded-3 p-3 bg-white shadow-xs mb-3">
                        <div class="text-muted small">Identifiant :</div>
                        <div class="fw-bold text-dark font-monospace" id="pwd_modal_email"></div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <label class="form-label small fw-bold text-dark mb-0">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1"
                                    onclick="generateStrongPassword('modal_new_password')">
                                <i class="fas fa-wand-magic-sparkles me-1"></i> Générer fort
                            </button>
                        </div>
                        <div class="position-relative">
                            <input type="text" name="password" id="modal_new_password" class="form-control form-control-lg bg-white rounded-3 font-monospace pe-5 fs-6" 
                                   placeholder="Minimum 8 caractères" required minlength="8">
                            <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none me-2 p-0" 
                                    onclick="copyToClipboard('modal_new_password', 'pwd-reset-copied-toast')" title="Copier">
                                <i class="fas fa-copy fa-lg"></i>
                            </button>
                        </div>
                        <div id="pwd-reset-copied-toast" class="alert alert-success border-0 py-1 px-2 mt-2 mb-0 rounded-3 small fw-semibold" style="display: none;">
                            <i class="fas fa-check-circle me-1"></i> Mot de passe copié !
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 small mb-0 py-2">
                        <i class="fas fa-info-circle me-1"></i> Le nouveau mot de passe prend effet immédiatement dès validation.
                    </div>
                </div>

                <div class="modal-footer bg-white py-2.5 px-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-3.5 fw-bold">
                        <i class="fas fa-check me-1"></i> Appliquer
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
const tabIds = ['tab-identity', 'tab-roles', 'tab-permissions'];
let currentTabIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    const userModalEl = document.getElementById('userModal');
    if (userModalEl) bsUserModal = new bootstrap.Modal(userModalEl);

    const pwdModalEl = document.getElementById('passwordModal');
    if (pwdModalEl) bsPwdModal = new bootstrap.Modal(pwdModalEl);

    // Track active tab changes
    const tabBtns = document.querySelectorAll('#userModalTabs button[data-bs-toggle="pill"]');
    tabBtns.forEach((btn, index) => {
        btn.addEventListener('shown.bs.tab', function(e) {
            currentTabIndex = index;
            updateTabNavButtons();
        });
    });
});

function updateTabNavButtons() {
    const prevBtn = document.getElementById('modalPrevTabBtn');
    const nextBtn = document.getElementById('modalNextTabBtn');
    if (!prevBtn || !nextBtn) return;

    prevBtn.style.display = currentTabIndex === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = currentTabIndex === tabIds.length - 1 ? 'none' : 'inline-block';
}

function navigateModalTab(direction) {
    let nextIndex = currentTabIndex + direction;
    if (nextIndex >= 0 && nextIndex < tabIds.length) {
        const targetBtn = document.getElementById(tabIds[nextIndex] + '-btn');
        if (targetBtn) {
            const tabInstance = bootstrap.Tab.getOrCreateInstance(targetBtn);
            tabInstance.show();
        }
    }
}

// 1. OPEN CREATE MODAL
function openCreateModal() {
    document.getElementById('userModalLabel').textContent = 'Nouveau Collaborateur';
    document.getElementById('userModalSub').textContent = 'Créer un compte d\'accès sécurisé avec rôles et permissions';
    document.getElementById('userModalIcon').className = 'fas fa-user-plus text-danger';
    document.getElementById('userFormMethod').value = 'POST';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userSubmitBtn').innerHTML = '<i class="fas fa-check-circle me-1.5"></i> Créer le Collaborateur';

    // Reset inputs
    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_phone').value = '';
    document.getElementById('user_job_title').value = '';
    document.getElementById('user_status').value = 'active';
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = true;
    document.getElementById('password_hint').textContent = 'Obligatoire à la création (min 8 caractères).';

    // Reset roles selection cards
    document.querySelectorAll('.role-select-card').forEach(c => {
        c.classList.remove('selected', 'selected-manager', 'selected-staff');
    });
    document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);

    // Default to Staff role
    selectRoleCard('Staff');

    // Reset switches
    document.querySelectorAll('.perm-switch-input').forEach(sw => sw.checked = false);
    onPermSwitchChange();

    // Auto-generate strong initial password
    generateStrongPassword('user_password');

    // Switch to first tab
    const firstTabBtn = document.getElementById('tab-identity-btn');
    if (firstTabBtn) bootstrap.Tab.getOrCreateInstance(firstTabBtn).show();

    if (bsUserModal) bsUserModal.show();
}

// 2. OPEN EDIT MODAL VIA AJAX
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
        checkPasswordStrength('');

        // Reset roles cards
        document.querySelectorAll('.role-select-card').forEach(c => {
            c.classList.remove('selected', 'selected-manager', 'selected-staff');
        });
        document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);

        // Check assigned roles
        data.roles.forEach(roleName => {
            selectRoleCard(roleName, true);
        });

        // Check permissions switches
        document.querySelectorAll('.perm-switch-input').forEach(sw => {
            sw.checked = data.direct_permissions.includes(sw.value);
        });

        onPermSwitchChange();

        // Switch to first tab
        const firstTabBtn = document.getElementById('tab-identity-btn');
        if (firstTabBtn) bootstrap.Tab.getOrCreateInstance(firstTabBtn).show();

        if (bsUserModal) bsUserModal.show();
    })
    .catch(err => {
        alert('Erreur lors du chargement : ' + err.message);
    });
}

// 3. ROLE SELECTOR CARD CLICK
function selectRoleCard(roleName, multi = false) {
    const card = document.getElementById(`role-card-${roleName}`);
    const input = document.getElementById(`role-input-${roleName}`);
    if (!card || !input) return;

    const roleColorClass = roleName === 'Admin' ? 'selected' : (roleName === 'Manager' ? 'selected-manager' : 'selected-staff');

    if (!multi) {
        // Single select behavior
        document.querySelectorAll('.role-select-card').forEach(c => {
            c.classList.remove('selected', 'selected-manager', 'selected-staff');
        });
        document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = false);

        card.classList.add(roleColorClass);
        input.checked = true;
    } else {
        // Multi select toggle
        const isSelected = card.classList.contains(roleColorClass);
        if (isSelected) {
            card.classList.remove(roleColorClass);
            input.checked = false;
        } else {
            card.classList.add(roleColorClass);
            input.checked = true;
        }
    }

    onPermSwitchChange();
}

// 4. PASSWORD GENERATOR & STRENGTH EVALUATOR
function generateStrongPassword(targetInputId) {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789#@!%*?";
    let pwd = "Wina-";
    for (let i = 0; i < 9; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const input = document.getElementById(targetInputId);
    if (input) {
        input.value = pwd;
        checkPasswordStrength(pwd);
        copyToClipboard(targetInputId, 'pwd-copied-toast');
    }
}

function checkPasswordStrength(val) {
    const fill = document.getElementById('pwd-strength-fill');
    const text = document.getElementById('pwd-strength-text');
    if (!fill || !text) return;

    if (!val || val.length === 0) {
        fill.style.width = '0%';
        fill.style.backgroundColor = '#e2e8f0';
        text.textContent = 'En attente';
        text.className = 'fw-bold small text-muted';
        return;
    }

    let score = 0;
    if (val.length >= 8) score += 25;
    if (val.length >= 12) score += 25;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 25;
    if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score += 25;

    fill.style.width = score + '%';

    if (score <= 25) {
        fill.style.backgroundColor = '#ef4444';
        text.textContent = 'Faible';
        text.className = 'fw-bold small text-danger';
    } else if (score <= 50) {
        fill.style.backgroundColor = '#f59e0b';
        text.textContent = 'Moyen';
        text.className = 'fw-bold small text-warning';
    } else if (score <= 75) {
        fill.style.backgroundColor = '#3b82f6';
        text.textContent = 'Bon';
        text.className = 'fw-bold small text-primary';
    } else {
        fill.style.backgroundColor = '#10b981';
        text.textContent = 'Excellent';
        text.className = 'fw-bold small text-success';
    }
}

function copyToClipboard(inputId, toastId) {
    const input = document.getElementById(inputId);
    if (!input || !input.value) return;

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value);
    } else {
        input.select();
        document.execCommand('copy');
    }

    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3500);
    }
}

// 5. PERMISSIONS REACTION & REAL-TIME COUNTER
function onPermSwitchChange() {
    const inheritedPerms = new Set();

    document.querySelectorAll('.role-checkbox:checked').forEach(cb => {
        try {
            const perms = JSON.parse(cb.getAttribute('data-permissions') || '[]');
            perms.forEach(p => inheritedPerms.add(p));
        } catch(e) {}
    });

    let totalActive = 0;
    document.querySelectorAll('.perm-switch-input').forEach(sw => {
        const permName = sw.value;
        const isInherited = inheritedPerms.has(permName);
        const badge = document.getElementById(`badge-inherited-${permName}`);

        if (badge) {
            if (isInherited) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        if (sw.checked || isInherited) {
            totalActive++;
        }
    });

    const counter = document.getElementById('perms-tab-counter');
    if (counter) counter.textContent = totalActive;
}

// Filter permissions list in real time
function filterPermissionsList(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.perm-item').forEach(item => {
        const name = item.getAttribute('data-perm-name') || '';
        const desc = item.getAttribute('data-perm-desc') || '';
        if (q === '' || name.includes(q) || desc.includes(q)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });

    // Hide modules where all items are hidden
    document.querySelectorAll('.perm-module-card').forEach(mod => {
        const visibleItems = mod.querySelectorAll('.perm-item[style*="display: flex"]');
        if (q !== '' && visibleItems.length === 0) {
            mod.style.display = 'none';
        } else {
            mod.style.display = 'block';
        }
    });
}

function toggleModulePerms(modKey, state) {
    const container = document.querySelector(`.module-items-${modKey}`);
    if (container) {
        container.querySelectorAll('.perm-switch-input').forEach(sw => {
            sw.checked = state;
        });
        onPermSwitchChange();
    }
}

function toggleAllPerms(state) {
    document.querySelectorAll('.perm-switch-input').forEach(sw => {
        sw.checked = state;
    });
    onPermSwitchChange();
}

// 6. TOGGLE STATUS (AJAX)
function toggleUserStatus(userId, userName, triggerSwitch = null) {
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
            // Update mobile dot & label
            const mobDot = document.getElementById(`mob-dot-${userId}`);
            if (mobDot) {
                mobDot.className = `status-dot ${data.status}`;
            }
            const deskDot = document.getElementById(`desk-dot-${userId}`);
            if (deskDot) {
                deskDot.className = `status-dot ${data.status}`;
            }
            const mobLabel = document.getElementById(`mob-status-label-${userId}`);
            if (mobLabel) {
                mobLabel.textContent = data.status === 'active' ? 'Actif' : 'Inactif';
            }
            const deskCol = document.getElementById(`desk-status-col-${userId}`);
            if (deskCol) {
                deskCol.innerHTML = data.badge;
            }
            const deskBtn = document.getElementById(`desk-btn-toggle-${userId}`);
            if (deskBtn) {
                deskBtn.innerHTML = data.status === 'active' 
                    ? '<i class="fas fa-toggle-on text-success fa-lg"></i>' 
                    : '<i class="fas fa-toggle-off text-muted fa-lg"></i>';
            }
        } else {
            alert(data.message || 'Action impossible');
            if (triggerSwitch) triggerSwitch.checked = !triggerSwitch.checked;
        }
    })
    .catch(err => {
        alert('Erreur réseau : ' + err.message);
        if (triggerSwitch) triggerSwitch.checked = !triggerSwitch.checked;
    });
}

// 7. OPEN PASSWORD RESET MODAL
function openPasswordModal(userId, userName, userEmail) {
    document.getElementById('pwd_modal_name').textContent = userName;
    document.getElementById('pwd_modal_email').textContent = userEmail;
    document.getElementById('passwordForm').action = `/users/${userId}/reset-password`;
    document.getElementById('modal_new_password').value = '';
    document.getElementById('pwd-reset-copied-toast').style.display = 'none';

    generateStrongPassword('modal_new_password');

    if (bsPwdModal) bsPwdModal.show();
}
</script>
@endpush
@endsection
