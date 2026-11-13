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

/* Domain Pill Filters inside Modal */
.domain-pill {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.35rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}
.domain-pill:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.domain-pill.active {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 2px 4px rgba(220, 38, 38, 0.25);
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

@keyframes pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.pulse-dot {
    animation: pulse-green 2s infinite;
}

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
    width: 42px;
    height: 24px;
    margin: 0;
    flex-shrink: 0;
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
    transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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
    transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.switch-ios input:checked + .switch-slider {
    background-color: #10b981;
}
.switch-ios input:checked + .switch-slider:before {
    transform: translateX(18px);
}

/* Role Selector Cards inside Modal */
.role-select-card {
    cursor: pointer;
    border: 2px solid #e2e8f0;
    border-radius: 12px !important;
    padding: 0.85rem;
    background: #ffffff;
    transition: all 0.2s ease;
    position: relative;
}
.role-select-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.role-select-card.selected-admin {
    border-color: #dc2626 !important;
    background: #fff1f2 !important;
    box-shadow: 0 0 0 1px #dc2626;
}
.role-select-card.selected-manager {
    border-color: #2563eb !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 1px #2563eb;
}
.role-select-card.selected-staff {
    border-color: #059669 !important;
    background: #ecfdf5 !important;
    box-shadow: 0 0 0 1px #059669;
}
.role-select-card.selected-custom {
    border-color: #4f46e5 !important;
    background: #eef2ff !important;
    box-shadow: 0 0 0 1px #4f46e5;
}
.role-select-card.is-restricted {
    opacity: 0.6;
    cursor: not-allowed;
    background: #f8fafc;
}
.role-check-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
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
.role-select-card.selected-admin .role-check-indicator {
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
.role-select-card.selected-custom .role-check-indicator {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
}

/* Sticky Selection Bulk Bar */
.users-bulk-bar {
    background: #0f172a;
    color: #ffffff;
    border-radius: 12px !important;
    padding: 0.65rem 1rem;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.2);
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

/* Permission Row Item */
.perm-row {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.7rem 0.85rem;
    transition: all 0.15s ease;
}
.perm-row:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.perm-row.is-active {
    border-color: #bbf7d0;
    background: #f0fdf4;
}

/* ==========================================================================
   MODAL SCROLLING & RESPONSIVE ARCHITECTURE (Guaranteed 100% Scrollable)
   ========================================================================== */

#userModal .modal-dialog {
    max-width: 1200px;
}

/* Desktop: 2-column split with dedicated scrollable panels */
@media (min-width: 992px) {
    #userModal .modal-content {
        height: 88vh;
        max-height: 88vh;
        display: flex;
        flex-direction: column;
        border-radius: 16px !important;
        overflow: hidden;
    }
    #userModal .modal-body {
        flex: 1 1 0;
        min-height: 0;
        overflow: hidden; /* inner columns handle their own scrolling */
        padding: 0 !important;
    }
    .modal-col-left {
        height: 100%;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        padding: 1.5rem;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
    }
    .modal-col-right {
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        overflow: hidden;
    }
    .perms-scroll-container {
        flex: 1 1 0;
        min-height: 0;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        padding: 1.25rem 1.5rem;
    }
}

/* Mobile & Tablet (< 992px): Fullscreen app-style with tabs */
@media (max-width: 991.98px) {
    #userModal .modal-dialog,
    #passwordModal .modal-dialog {
        margin: 0;
        max-width: 100%;
        width: 100%;
        height: 100%;
        min-height: 100%;
    }
    #userModal .modal-content,
    #passwordModal .modal-content {
        height: 100vh !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        border: none;
        display: flex;
        flex-direction: column;
    }
    #userModal .modal-body {
        flex: 1 1 0;
        min-height: 0;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        padding: 1rem 1rem 90px 1rem !important; /* Large bottom clearance so footer never blocks content */
    }
    .perms-scroll-container {
        overflow: visible;
        padding: 0;
    }
    .modal-col-left, .modal-col-right {
        height: auto;
        border: none;
        padding: 0;
    }
    .modal-sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
        z-index: 1055;
        padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom, 0px)) 1rem;
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

    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-users-gear"></i>
            </div>
            <div>
                <h1 class="brand-title">Équipe, Collaborateurs & Rôles</h1>
                <p class="brand-subtitle">Comptes d'accès, permissions d'équipe et traçabilité des opérations</p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('users.export', request()->query()) }}" class="btn-brand-outline" data-no-pjax="true">
                <i class="fas fa-file-csv me-1.5 text-success"></i>
                <span>Exporter CSV</span>
            </a>
            <a href="{{ route('roles.index') }}" class="btn-brand-outline">
                <i class="fas fa-shield-halved me-1.5" style="color: var(--primary-color)"></i>
                <span>Rôles globaux</span>
            </a>
            <button type="button" class="btn-brand-primary" onclick="openCreateModal()">
                <i class="fas fa-plus me-1.5"></i>
                <span>Nouveau collaborateur</span>
            </button>
        </div>
    </div>

    <!-- Navigation Croisée Système & Audit -->
    @include('partials.subnav.system')

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

    <!-- KPI Metric Stat Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('users.index', ['scope' => 'team']) }}" class="stat-kpi-card text-decoration-none {{ $scopeFilter === 'team' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Équipe & Rôles</div>
                        <div class="stat-kpi-value text-dark">{{ $kpi['total_team'] ?? $kpi['total_users'] }}</div>
                        <div class="stat-kpi-sub text-muted">
                            @if(($kpi['online'] ?? 0) > 0)
                                <span class="text-success fw-semibold">{{ $kpi['online'] }} en ligne</span> • {{ $kpi['customers_only'] ?? 0 }} clients
                            @else
                                {{ $kpi['total_users'] }} comptes • {{ $kpi['customers_only'] ?? 0 }} clients
                            @endif
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-users-gear"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('users.index', ['role' => 'Admin']) }}" class="stat-kpi-card text-decoration-none {{ $roleFilter === 'Admin' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Administrateurs</div>
                        <div class="stat-kpi-value text-danger">{{ $kpi['admins'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">Accès complet système</div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('users.index', ['status' => 'online']) }}" class="stat-kpi-card text-decoration-none {{ $statusFilter === 'online' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Connectés (15 min)</div>
                        <div class="stat-kpi-value text-success">{{ $kpi['online'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">{{ $kpi['managers'] ?? 0 }} mgr • {{ $kpi['staff'] ?? 0 }} staff</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-signal"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('users.index', ['status' => 'blocked']) }}" class="stat-kpi-card text-decoration-none {{ in_array($statusFilter, ['inactive', 'suspended', 'blocked'], true) ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Comptes Bloqués</div>
                        <div class="stat-kpi-value {{ ($kpi['inactive'] ?? 0) > 0 ? 'text-warning' : 'text-muted' }}">{{ $kpi['inactive'] ?? 0 }}</div>
                        <div class="stat-kpi-sub text-muted">Inactifs ou suspendus</div>
                    </div>
                    <div class="stat-kpi-icon bg-secondary-subtle text-secondary">
                        <i class="fas fa-user-lock"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Dynamic Role & Status Filter Chips -->
    <div class="chips-scroll mb-2.5">
        <a href="{{ route('users.index', ['search' => $search ?: null]) }}"
           class="chip-item {{ $roleFilter === 'all' && $statusFilter === 'all' && $scopeFilter === 'all' ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Tous</span>
            <span class="badge bg-light text-dark rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['total_users'] }}</span>
        </a>
        <a href="{{ route('users.index', ['scope' => 'team', 'search' => $search ?: null]) }}"
           class="chip-item {{ $scopeFilter === 'team' && $roleFilter === 'all' ? 'active' : '' }}">
            <i class="fas fa-users-gear text-primary"></i>
            <span>Équipe</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['total_team'] }}</span>
        </a>
        @foreach($roles as $role)
            @php
                $chipIcon = match($role->name) {
                    'Admin' => 'fa-crown text-danger',
                    'Manager' => 'fa-user-tie text-primary',
                    'Staff' => 'fa-user-check text-success',
                    default => 'fa-shield-halved text-indigo',
                };
                $chipBadge = match($role->name) {
                    'Admin' => 'bg-danger-subtle text-danger',
                    'Manager' => 'bg-primary-subtle text-primary',
                    'Staff' => 'bg-success-subtle text-success',
                    default => 'bg-secondary-subtle text-secondary',
                };
            @endphp
            <a href="{{ route('users.index', ['role' => $role->name, 'search' => $search ?: null]) }}"
               class="chip-item {{ $roleFilter === $role->name ? 'active' : '' }}">
                <i class="fas {{ $chipIcon }}"></i>
                <span>{{ $role->name }}</span>
                <span class="badge {{ $chipBadge }} rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $role->users_count }}</span>
            </a>
        @endforeach
        <a href="{{ route('users.index', ['role' => 'none', 'search' => $search ?: null]) }}"
           class="chip-item {{ $roleFilter === 'none' || $scopeFilter === 'customers' ? 'active' : '' }}">
            <i class="fas fa-user text-muted"></i>
            <span>Sans rôle</span>
            <span class="badge bg-light text-secondary rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['customers_only'] }}</span>
        </a>
        <a href="{{ route('users.index', ['status' => 'blocked', 'search' => $search ?: null]) }}"
           class="chip-item {{ in_array($statusFilter, ['blocked', 'inactive', 'suspended'], true) ? 'active' : '' }}">
            <i class="fas fa-pause-circle text-secondary"></i>
            <span>Bloqués</span>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-1.5 py-0.5" style="font-size: 0.68rem;">{{ $kpi['inactive'] }}</span>
        </a>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('users.index') }}" class="w-100" id="usersFilterForm">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 220px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom, email, téléphone, poste..."
                           value="{{ $search }}">
                    @if($search !== '')
                        <a href="{{ route('users.index', array_filter(['role' => $roleFilter !== 'all' ? $roleFilter : null, 'status' => $statusFilter !== 'all' ? $statusFilter : null, 'scope' => $scopeFilter !== 'all' ? $scopeFilter : null])) }}"
                           class="text-muted pe-2 text-decoration-none" style="font-size: 0.85rem;" title="Effacer la recherche">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                </div>

                <select name="role" class="form-select" style="width: auto; min-width: 145px;" aria-label="Filtrer par rôle">
                    <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>
                            {{ $role->name }} ({{ $role->users_count }})
                        </option>
                    @endforeach
                    <option value="none" {{ $roleFilter === 'none' ? 'selected' : '' }}>Sans rôle ({{ $kpi['customers_only'] }})</option>
                </select>

                <select name="status" class="form-select" style="width: auto; min-width: 155px;" aria-label="Filtrer par statut">
                    <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Tous les statuts</option>
                    <option value="online" {{ $statusFilter === 'online' ? 'selected' : '' }}>En ligne ({{ $kpi['online'] }})</option>
                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Actifs seulement</option>
                    <option value="blocked" {{ $statusFilter === 'blocked' ? 'selected' : '' }}>Inactifs & Suspendus ({{ $kpi['inactive'] }})</option>
                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspendus</option>
                </select>

                <select name="per_page" class="form-select" style="width: auto; min-width: 96px;" aria-label="Lignes par page">
                    @foreach([15, 25, 50] as $pp)
                        <option value="{{ $pp }}" {{ (int)$perPage === $pp ? 'selected' : '' }}>{{ $pp }} / page</option>
                    @endforeach
                </select>

                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>

                @if($search !== '' || $roleFilter !== 'all' || $statusFilter !== 'all' || $scopeFilter !== 'all')
                    <a href="{{ route('users.index') }}" class="btn btn-light border rounded-pill px-3 py-2 small fw-semibold text-secondary">
                        <i class="fas fa-rotate-left me-1"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bulk Selection Bar -->
    <div id="usersBulkBar" class="users-bulk-bar d-none align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger rounded-pill px-2.5 py-1 fw-bold" id="usersBulkCount">0</span>
            <span class="small fw-semibold">compte(s) sélectionné(s)</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold" onclick="runBulkAction('activate')">
                <i class="fas fa-check me-1"></i> Activer
            </button>
            <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 fw-semibold" onclick="runBulkAction('deactivate')">
                <i class="fas fa-pause me-1"></i> Désactiver
            </button>
            <div class="input-group input-group-sm" style="width: auto;">
                <select id="bulkRoleSelect" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="">Changer de rôle...</option>
                    @foreach($roles as $role)
                        @if($role->name !== 'Admin' || $canManageAdminRole)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endif
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-primary fw-semibold" onclick="runBulkAction('assign_role')">
                    Appliquer
                </button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2.5" onclick="clearUserSelection()" title="Désélectionner tout">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 1. MOBILE USER CARDS (Mobile-First View)   -->
    <!-- ========================================== -->
    <div class="d-block d-lg-none">
        @forelse($users as $user)
        @php
            $isSelf = auth()->id() === $user->id;
            $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
            $isRestrictedTarget = !$canManageAdminRole && $user->hasRole('Admin');
            $canMutateTarget = !$isSelf && !$isSuperAdmin && !$isRestrictedTarget;
            $primaryRole = optional($user->roles->first())->name;
            $avatarGradient = match($primaryRole) {
                'Admin' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
                'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                default => $primaryRole
                    ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)'
                    : 'linear-gradient(135deg, #64748b 0%, #334155 100%)',
            };
            $statusClass = $user->status ?? 'active';
            $statusShortLabel = match($statusClass) {
                'active' => 'Actif',
                'suspended' => 'Suspendu',
                default => 'Inactif',
            };
        @endphp
        <div class="user-card p-3 mb-2.5 position-relative" id="mob-card-{{ $user->id }}">
            
            <!-- Top Section: Selection + Avatar + Name + Status Switch -->
            <div class="d-flex align-items-start justify-content-between gap-2.5 mb-2.5">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    @if($canMutateTarget)
                        <input type="checkbox" class="form-check-input user-select-cb flex-shrink-0 mt-0"
                               value="{{ $user->id }}" onchange="onUserSelectChange(this)"
                               aria-label="Sélectionner {{ $user->name }}">
                    @endif
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
                @if($canMutateTarget)
                <div class="d-flex flex-column align-items-end flex-shrink-0">
                    <label class="switch-ios" title="Activer / Désactiver">
                        <input type="checkbox" id="switch-mob-{{ $user->id }}" 
                               {{ $user->status === 'active' ? 'checked' : '' }}
                               onchange="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}', this)">
                        <span class="switch-slider"></span>
                    </label>
                    <span class="text-muted mt-1" style="font-size: 0.65rem;" id="mob-status-label-{{ $user->id }}">
                        {{ $statusShortLabel }}
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
                @if($user->isOnline())
                <div class="contact-pill bg-success-subtle text-success border-success-subtle fw-semibold">
                    <span class="rounded-circle bg-success pulse-dot" style="width: 7px; height: 7px; display: inline-block;"></span>
                    <span>En ligne</span>
                </div>
                @elseif($user->last_login_at)
                <div class="contact-pill text-dark" title="{{ $user->last_login_at->format('d/m/Y à H:i') }} @if($user->last_login_ip)({{ $user->last_login_ip }})@endif">
                    <i class="far fa-clock text-secondary" style="font-size: 0.7rem;"></i>
                    <span>{{ $user->last_login_at->diffForHumans() }}</span>
                </div>
                @else
                <div class="contact-pill text-muted">
                    <i class="far fa-clock opacity-50" style="font-size: 0.7rem;"></i>
                    <span class="fst-italic">Jamais connecté</span>
                </div>
                @endif
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
                                default => 'text-white',
                            };
                            $customStyle = in_array($role->name, ['Admin', 'Manager', 'Staff']) ? '' : 'background:#4f46e5;';
                        @endphp
                        <span class="badge {{ $badgeStyle }} rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.7rem; {{ $customStyle }}">
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
                @if(!$isRestrictedTarget)
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
                @else
                    <span class="btn btn-light btn-sm rounded-pill flex-grow-1 py-1.5 text-muted disabled">
                        <i class="fas fa-lock me-1"></i> Compte Admin protégé
                    </span>
                @endif
                @if(auth()->user()->hasRole('Admin') || auth()->user()->can('view_activity_logs'))
                    <a href="{{ route('activity-logs.index', ['user_id' => $user->id]) }}"
                       class="btn btn-outline-dark btn-sm rounded-pill px-2.5 py-1.5 d-flex align-items-center justify-content-center"
                       title="Historique d'activité">
                        <i class="fas fa-clock-rotate-left"></i>
                    </a>
                @endif
                @if($canMutateTarget)
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-1.5"
                            onclick="deleteUserConfirm({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ route('users.destroy', $user) }}')"
                            title="Supprimer">
                        <i class="fas fa-trash-can"></i>
                    </button>
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
                        <th class="ps-4 py-3" style="width: 42px;">
                            <input type="checkbox" class="form-check-input" id="selectAllUsers" onchange="toggleSelectAllUsers(this)" title="Tout sélectionner sur cette page">
                        </th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 30%;">Collaborateur</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 22%;">Rôles & Permissions</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 12%;">Statut</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 16%;">Dernière Connexion</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                    @php
                        $isSelf = auth()->id() === $user->id;
                        $isSuperAdmin = $user->id === 1 || $user->email === 'contact@wina.com';
                        $isRestrictedTarget = !$canManageAdminRole && $user->hasRole('Admin');
                        $canMutateTarget = !$isSelf && !$isSuperAdmin && !$isRestrictedTarget;
                        $primaryRole = optional($user->roles->first())->name;
                        $avatarGradient = match($primaryRole) {
                            'Admin' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
                            'Manager' => 'linear-gradient(135deg, #2563eb 0%, #1e40af 100%)',
                            'Staff' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                            default => $primaryRole
                                ? 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)'
                                : 'linear-gradient(135deg, #64748b 0%, #334155 100%)',
                        };
                    @endphp
                    <tr class="hover-bg-light transition-all" id="desk-row-{{ $user->id }}">
                        <td class="ps-4 py-3">
                            @if($canMutateTarget)
                                <input type="checkbox" class="form-check-input user-select-cb"
                                       value="{{ $user->id }}" onchange="onUserSelectChange(this)"
                                       aria-label="Sélectionner {{ $user->name }}">
                            @else
                                <span class="text-muted opacity-25" title="Compte protégé"><i class="fas fa-lock" style="font-size: 0.75rem;"></i></span>
                            @endif
                        </td>
                        <td class="py-3">
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
                                            default => '',
                                        };
                                        $roleInlineStyle = in_array($role->name, ['Admin', 'Manager', 'Staff'])
                                            ? ''
                                            : 'background: #eef2ff; color: #4338ca; border-color: #c7d2fe;';
                                        $roleIcon = match($role->name) {
                                            'Admin' => 'fa-crown',
                                            'Manager' => 'fa-user-tie',
                                            'Staff' => 'fa-user-check',
                                            default => 'fa-shield-halved',
                                        };
                                    @endphp
                                    <span class="badge {{ $roleColor }} border rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 0.74rem; {{ $roleInlineStyle }}">
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
                            @if($user->isOnline())
                                <div class="d-inline-flex align-items-center gap-1.5 px-2 py-0.5 rounded-pill bg-success-subtle text-success border border-success-subtle mb-1" style="font-size: 0.72rem; font-weight: 600;">
                                    <span class="rounded-circle bg-success pulse-dot" style="width: 7px; height: 7px; display: inline-block;"></span>
                                    <span>En ligne</span>
                                </div>
                                <div class="text-muted font-monospace" style="font-size: 0.7rem;" title="Dernière activité : {{ $user->last_login_at->format('d/m/Y à H:i') }} @if($user->last_login_ip)({{ $user->last_login_ip }})@endif">
                                    {{ $user->last_login_at->diffForHumans() }}
                                </div>
                            @elseif($user->last_login_at)
                                <div class="d-flex align-items-center gap-1.5 text-dark fw-semibold" style="font-size: 0.8rem;">
                                    <i class="far fa-clock text-secondary opacity-75" style="font-size: 0.72rem;"></i>
                                    <span>{{ $user->last_login_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-muted d-flex align-items-center gap-1.5 mt-0.5" style="font-size: 0.72rem;">
                                    <span class="font-monospace">{{ $user->last_login_at->format('d/m/Y à H:i') }}</span>
                                    @if($user->last_login_ip)
                                        <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0" style="font-size: 0.65rem;" title="Adresse IP de connexion">
                                            {{ $user->last_login_ip }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.72rem; font-weight: normal;">
                                    <i class="fas fa-minus-circle me-1 text-secondary opacity-50"></i> Jamais connecté
                                </span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if(!$isRestrictedTarget)
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-primary shadow-xs border"
                                            onclick="openEditModal({{ $user->id }})" title="Modifier">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-warning shadow-xs border"
                                            onclick="openPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')" 
                                            title="Mot de passe">
                                        <i class="fas fa-key"></i>
                                    </button>
                                @else
                                    <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 0.7rem;" title="Modification réservée aux administrateurs">
                                        <i class="fas fa-lock me-1"></i> Protégé
                                    </span>
                                @endif
                                @if(auth()->user()->hasRole('Admin') || auth()->user()->can('view_activity_logs'))
                                    <a href="{{ route('activity-logs.index', ['user_id' => $user->id]) }}"
                                       class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-dark shadow-xs border"
                                       title="Historique d'activité">
                                        <i class="fas fa-clock-rotate-left"></i>
                                    </a>
                                @endif
                                @if($canMutateTarget)
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-secondary shadow-xs border"
                                            onclick="toggleUserStatus({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            id="desk-btn-toggle-{{ $user->id }}"
                                            title="Activer / Désactiver">
                                        <i class="fas {{ $user->status === 'active' ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' }} fa-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-danger shadow-xs border"
                                            onclick="deleteUserConfirm({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ route('users.destroy', $user) }}')"
                                            title="Supprimer">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <form id="userForm" method="POST" action="{{ route('users.store') }}" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">

            <!-- Modal Header (Always Pinned Top) -->
            <div class="modal-header bg-dark text-white py-2.5 px-3 px-md-4 border-0 flex-shrink-0">
                <div class="d-flex align-items-center gap-2.5 min-w-0">
                    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="fas fa-user-shield text-danger" id="userModalIcon"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="modal-title fw-bold mb-0 text-truncate fs-6" id="userModalLabel">Nouveau Collaborateur</h5>
                        <p class="small text-white-50 mb-0 text-truncate" id="userModalSub" style="font-size: 0.76rem;">Définissez les accès et privilèges de l'équipe</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <!-- Mobile-Only Segmented Control Tabs (< 992px) -->
            <div class="bg-light border-bottom p-2 d-lg-none flex-shrink-0">
                <div class="nav nav-pills nav-fill bg-white p-1 rounded-pill border" id="mobileModalPills" role="tablist">
                    <button class="nav-link active rounded-pill py-1.5 small fw-bold" id="pills-profile-btn" data-bs-toggle="pill" data-bs-target="#pills-profile-content" type="button" role="tab">
                        <i class="fas fa-id-card me-1"></i> 1. Profil & Rôle
                    </button>
                    <button class="nav-link rounded-pill py-1.5 small fw-bold" id="pills-perms-btn" data-bs-toggle="pill" data-bs-target="#pills-perms-content" type="button" role="tab">
                        <i class="fas fa-sliders me-1"></i> 2. Permissions (<span id="mob-perms-counter">0</span>/{{ $totalPermissionsCount }})
                    </button>
                </div>
            </div>

            <!-- Modal Body (Always 100% Scrollable) -->
            <div class="modal-body">
                <div class="row g-0 h-100">

                    <!-- ============================================== -->
                    <!-- LEFT COLUMN / TAB 1: PROFIL, MOT DE PASSE, RÔLE -->
                    <!-- ============================================== -->
                    <div class="col-12 col-lg-5 modal-col-left tab-pane-responsive" id="pills-profile-content">
                        
                        <div id="userModalErrors" class="alert alert-danger border-0 rounded-3 small mb-3 d-none"></div>

                        <!-- Last Login Activity Banner (Visible only in Edit mode) -->
                        <div id="userLastLoginBanner" class="d-none alert alert-light border rounded-3 p-2.5 mb-3" style="background: #f8fafc;">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div id="userLastLoginDot" class="rounded-circle bg-secondary flex-shrink-0" style="width: 8px; height: 8px;"></div>
                                    <div>
                                        <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.04em;">Dernière connexion</div>
                                        <div class="fw-semibold text-dark" id="userLastLoginText" style="font-size: 0.8rem;">—</div>
                                    </div>
                                </div>
                                <div id="userLastLoginIpBadge" class="badge bg-white text-secondary border font-monospace px-2 py-1" style="font-size: 0.7rem;">
                                    —
                                </div>
                            </div>
                        </div>

                        <!-- 1. Identité Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark mb-2.5 d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                <i class="fas fa-user-circle text-primary"></i>
                                <span>Coordonnées & Compte</span>
                            </h6>

                            <div class="mb-2.5">
                                <label class="form-label small fw-bold text-dark mb-1">Nom complet <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="user_name" class="form-control rounded-3" 
                                       placeholder="Ex: Yassine El Amrani" required autocomplete="name">
                            </div>

                            <div class="mb-2.5">
                                <label class="form-label small fw-bold text-dark mb-1">Email professionnel <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="user_email" class="form-control rounded-3" 
                                       placeholder="yassine@winashop.com" required autocomplete="email">
                            </div>

                            <div class="row g-2 mb-2.5">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Téléphone</label>
                                    <input type="tel" name="phone" id="user_phone" class="form-control rounded-3" 
                                           placeholder="06 12 34 56 78" autocomplete="tel">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-dark mb-1">Poste / Fonction</label>
                                    <input type="text" name="job_title" id="user_job_title" class="form-control rounded-3" 
                                           placeholder="Ex: Responsable Ventes">
                                </div>
                            </div>

                            <div>
                                <label class="form-label small fw-bold text-dark mb-1">Statut d'accès</label>
                                <select name="status" id="user_status" class="form-select rounded-3">
                                    <option value="active">Actif (Accès immédiatement autorisé)</option>
                                    <option value="inactive">Inactif (Connexion bloquée)</option>
                                    <option value="suspended">Suspendu (Sécurité / Audit)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Mot de Passe Section -->
                        <div class="mb-4 p-3 rounded-4 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1.5" style="font-size: 0.85rem;">
                                    <i class="fas fa-lock text-warning"></i> Mot de passe
                                </h6>
                                <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-1"
                                        onclick="generateStrongPassword('user_password')">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i> Générer auto
                                </button>
                            </div>
                            <p class="text-muted small mb-2" id="password_hint" style="font-size: 0.74rem;">Min. 8 caractères sécurisés.</p>

                            <div class="position-relative mb-1.5">
                                <input type="text" name="password" id="user_password" 
                                       class="form-control bg-white rounded-3 font-monospace pe-5" 
                                       placeholder="••••••••" 
                                       oninput="checkPasswordStrength(this.value)"
                                       autocomplete="new-password">
                                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none me-2 p-0" 
                                        onclick="copyToClipboard('user_password', 'pwd-copied-toast')" title="Copier le mot de passe">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>

                            <!-- Strength Meter -->
                            <div class="pwd-strength-bar">
                                <div class="pwd-strength-fill" id="pwd-strength-fill"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="text-muted" style="font-size: 0.68rem;">Force :</span>
                                <span class="fw-bold text-muted" style="font-size: 0.68rem;" id="pwd-strength-text">En attente</span>
                            </div>

                            <div id="pwd-copied-toast" class="alert alert-success border-0 py-1 px-2 mt-1.5 mb-0 rounded-2 small fw-semibold" style="display: none; font-size: 0.72rem;">
                                <i class="fas fa-check-circle me-1"></i> Mot de passe généré et copié !
                            </div>
                        </div>

                        <!-- 3. Rôles Principaux Section -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                <i class="fas fa-shield-alt text-danger"></i>
                                <span>Rôle Principal</span>
                            </h6>
                            <p class="text-muted small mb-2.5" style="font-size: 0.76rem;">
                                Cliquez sur un rôle pour appliquer son profil de permissions :
                            </p>

                            <div class="d-flex flex-column gap-2">
                                @foreach($roles as $role)
                                @php
                                    $isRestricted = ($role->name === 'Admin' && !$canManageAdminRole);
                                    $roleColorClass = match($role->name) {
                                        'Admin' => 'selected-admin',
                                        'Manager' => 'selected-manager',
                                        'Staff' => 'selected-staff',
                                        default => 'selected-custom',
                                    };
                                    $icon = match($role->name) {
                                        'Admin' => 'fa-crown text-danger',
                                        'Manager' => 'fa-user-tie text-primary',
                                        'Staff' => 'fa-user-check text-success',
                                        default => 'fa-shield-halved text-indigo',
                                    };
                                    $desc = match($role->name) {
                                        'Admin' => 'Accès total à tous les modules, configurations et comptabilité.',
                                        'Manager' => 'Gestion des commandes, catalogue, stocks, clients et analyses.',
                                        'Staff' => 'Traitement des commandes, produits et état des stocks.',
                                        default => 'Rôle personnalisé avec son propre jeu de permissions.',
                                    };
                                @endphp
                                <div class="role-select-card {{ $isRestricted ? 'is-restricted' : '' }}"
                                     id="role-card-{{ $role->name }}"
                                     data-color-class="{{ $roleColorClass }}"
                                     data-restricted="{{ $isRestricted ? '1' : '0' }}"
                                     @if(!$isRestricted) onclick="selectRoleCard('{{ addslashes($role->name) }}')" @endif>
                                    <div class="role-check-indicator">
                                        <i class="fas {{ $isRestricted ? 'fa-lock' : 'fa-check' }}"></i>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fas {{ $icon }}" @if(!in_array($role->name, ['Admin', 'Manager', 'Staff'])) style="color: #4f46e5;" @endif></i>
                                        <span class="fw-bold text-dark small">{{ $role->name }}</span>
                                        @if($isRestricted)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill" style="font-size: 0.62rem;">
                                                <i class="fas fa-lock me-1"></i>Admin requis
                                            </span>
                                        @endif
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-auto me-4" style="font-size: 0.65rem;">
                                            {{ $role->permissions->count() }} règles
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0 pe-4" style="font-size: 0.74rem; line-height: 1.35;">
                                        {{ $desc }}
                                    </p>
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                           id="role-input-{{ $role->name }}" 
                                           class="d-none role-checkbox"
                                           data-role-name="{{ $role->name }}"
                                           data-restricted="{{ $isRestricted ? '1' : '0' }}"
                                           @if($isRestricted) disabled @endif
                                           data-permissions="{{ json_encode($role->permissions->pluck('name')) }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mobile CTA to Step 2 -->
                        <div class="d-lg-none mt-4 pt-2">
                            <button type="button" class="btn btn-outline-danger w-100 rounded-pill py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2"
                                    onclick="switchToPermissionsTab()">
                                <span>Voir & Personnaliser les Permissions ({{ $totalPermissionsCount }})</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>

                    <!-- ============================================== -->
                    <!-- RIGHT COLUMN / TAB 2: PERMISSIONS GRANULAIRES  -->
                    <!-- ============================================== -->
                    <div class="col-12 col-lg-7 modal-col-right tab-pane-responsive" id="pills-perms-content">
                        
                        <!-- Permissions Header & Controls Bar (Sticky on Desktop) -->
                        <div class="p-3 bg-white border-bottom flex-shrink-0">
                            
                            <!-- Counter & Presets Row -->
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2.5">
                                <div>
                                    <span class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                                        <i class="fas fa-sliders text-success"></i> Permissions Granulaires
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 0.72rem;" id="perms-active-badge">
                                            <span id="perms-count-number">0</span> / {{ $totalPermissionsCount }} actives
                                        </span>
                                    </span>
                                    <span class="text-muted small d-block mt-0.5" id="perms-summary-breakdown" style="font-size: 0.72rem;">
                                        0 via rôle • 0 directes
                                    </span>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="resetPermsToRole()" title="Réinitialiser selon rôle">
                                        <i class="fas fa-rotate-left me-1"></i> Rôle
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-dark rounded-pill px-2.5 py-1" onclick="toggleAllPerms(true)">
                                        Tout cocher
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1" onclick="toggleAllPerms(false)">
                                        Tout décocher
                                    </button>
                                </div>
                            </div>

                            <!-- Live Search Input -->
                            <div class="position-relative mb-2">
                                <input type="text" id="perm-search-box" class="form-control form-control-sm bg-light rounded-pill ps-4" 
                                       placeholder="Filtrer en direct (ex: stock, facture, commande, stats)..."
                                       oninput="filterPermissionsList(this.value)">
                                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2.5 text-muted small"></i>
                            </div>

                            <!-- Domain Filter Pills (Instant 1-Click Jump/Filter) -->
                            <div class="chips-scroll">
                                <span class="domain-pill active" onclick="filterByDomain('all', this)">
                                    Tous ({{ $totalPermissionsCount }})
                                </span>
                                @foreach($permissionModules as $modKey => $module)
                                <span class="domain-pill" onclick="filterByDomain('{{ $modKey }}', this)">
                                    <i class="{{ $module['icon'] }} me-1" style="font-size: 0.7rem;"></i>{{ $module['title'] }} ({{ count($module['permissions']) }})
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- 100% Scrollable Permissions List Container -->
                        <div class="perms-scroll-container" id="perms-scroll-area">
                            <div class="d-flex flex-column gap-3">
                                @foreach($permissionModules as $modKey => $module)
                                <div class="perm-module-section" id="domain-section-{{ $modKey }}" data-domain-key="{{ $modKey }}">
                                    
                                    <!-- Domain Title & Sub-action -->
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-flex align-items-center justify-content-center" 
                                                  style="width: 26px; height: 26px; background: {{ $module['color'] }}20; color: {{ $module['color'] }}; font-size: 0.75rem;">
                                                <i class="{{ $module['icon'] }}"></i>
                                            </span>
                                            <span class="fw-bold text-dark small">{{ $module['title'] }}</span>
                                            <span class="badge bg-light text-secondary border rounded-pill px-1.5" style="font-size: 0.65rem;">
                                                {{ count($module['permissions']) }}
                                            </span>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-link text-decoration-none p-0 text-primary small fw-semibold" 
                                                    onclick="toggleModulePerms('{{ $modKey }}', true)" style="font-size: 0.72rem;">
                                                Cocher tout
                                            </button>
                                            <span class="text-muted small">/</span>
                                            <button type="button" class="btn btn-link text-decoration-none p-0 text-secondary small fw-semibold" 
                                                    onclick="toggleModulePerms('{{ $modKey }}', false)" style="font-size: 0.72rem;">
                                                Décocher
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Permission Rows -->
                                    <div class="d-flex flex-column gap-1.5 module-items-{{ $modKey }}">
                                        @foreach($module['permissions'] as $permName => $permDesc)
                                        <div class="perm-row d-flex align-items-center justify-content-between gap-2 perm-item" 
                                             id="perm-row-{{ $permName }}"
                                             data-perm-name="{{ strtolower($permName) }}" 
                                             data-perm-desc="{{ strtolower($permDesc) }}">
                                            <div class="min-w-0">
                                                <span class="fw-bold text-dark d-block font-monospace" style="font-size: 0.76rem;">{{ $permName }}</span>
                                                <span class="text-muted d-block small" style="font-size: 0.72rem; line-height: 1.3;">{{ $permDesc }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-1.5 py-0.5 inherited-badge d-none" 
                                                      id="badge-inherited-{{ $permName }}" style="font-size: 0.65rem;">
                                                    Inclus dans rôle
                                                </span>
                                                <label class="switch-ios mb-0">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}" 
                                                           id="perm-switch-{{ $permName }}" 
                                                           class="perm-switch-input"
                                                           onchange="onPermSwitchChange()">
                                                    <span class="switch-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                </div>
                                @endforeach
                                <div id="perms-empty-search" class="text-center py-4 d-none">
                                    <i class="fas fa-search text-muted opacity-50 mb-2"></i>
                                    <div class="fw-semibold text-dark small">Aucune permission ne correspond à votre filtre</div>
                                    <button type="button" class="btn btn-xs btn-link text-decoration-none mt-1" onclick="document.getElementById('perm-search-box').value=''; filterPermissionsList('');">Réinitialiser la recherche</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Modal Footer (Always Pinned Bottom) -->
            <div class="modal-footer modal-sticky-footer bg-white border-top py-2.5 px-3 px-md-4 d-flex justify-content-between align-items-center flex-shrink-0">
                <button type="button" class="btn btn-light rounded-pill px-3 py-2 fw-semibold" data-bs-dismiss="modal">
                    Annuler
                </button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-lg-none" id="mobBackBtn" onclick="switchToProfileTab()" style="display: none;">
                        <i class="fas fa-arrow-left me-1"></i> Profil
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold text-white shadow-sm" id="userSubmitBtn">
                        <i class="fas fa-check-circle me-1.5"></i> Enregistrer
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: RÉINITIALISER MOT DE PASSE                       -->
<!-- ======================================================= -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="passwordForm" method="POST" class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            @csrf
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

            <div class="modal-body p-3.5 bg-light">
                <div id="pwdModalErrors" class="alert alert-danger border-0 rounded-3 small mb-3 d-none"></div>
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

@push('scripts')
<script>
let activePermDomain = 'all';

function getUserModal() {
    const el = document.getElementById('userModal');
    return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
}

function getPwdModal() {
    const el = document.getElementById('passwordModal');
    return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showToast(icon, title) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
}

function showErrorAlert(title, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title, text: message });
    } else {
        alert(message);
    }
}

function clearModalErrors(boxId) {
    const box = document.getElementById(boxId);
    if (!box) return;
    box.classList.add('d-none');
    box.innerHTML = '';
}

function showModalErrors(boxId, message, fieldErrors = null) {
    const box = document.getElementById(boxId);
    if (!box) {
        showErrorAlert('Erreur', message);
        return;
    }

    let items = [];
    if (fieldErrors && typeof fieldErrors === 'object') {
        Object.values(fieldErrors).forEach(arr => {
            if (Array.isArray(arr)) {
                arr.forEach(msg => items.push(`<li>${escapeHtml(msg)}</li>`));
            }
        });
    }

    if (items.length > 0) {
        box.innerHTML = `<div class="fw-bold mb-1"><i class="fas fa-circle-exclamation me-1"></i> Veuillez corriger les champs suivants :</div><ul class="mb-0 ps-3">${items.join('')}</ul>`;
    } else {
        box.innerHTML = `<i class="fas fa-circle-exclamation me-1"></i> ${escapeHtml(message || 'Une erreur est survenue.')}`;
    }
    box.classList.remove('d-none');
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function refreshUsersView() {
    const userModalEl = document.getElementById('userModal');
    const pwdModalEl = document.getElementById('passwordModal');
    if (userModalEl) bootstrap.Modal.getInstance(userModalEl)?.hide();
    if (pwdModalEl) bootstrap.Modal.getInstance(pwdModalEl)?.hide();

    if (window.SpaNavigator && typeof window.SpaNavigator.navigate === 'function') {
        if (typeof window.SpaNavigator.clearCache === 'function') {
            window.SpaNavigator.clearCache();
        }
        setTimeout(() => window.SpaNavigator.navigate(window.location.href), 200);
    } else {
        setTimeout(() => window.location.reload(), 200);
    }
}

async function submitAjaxModalForm({ form, errorBoxId, submitBtn, defaultBtnHtml, loadingLabel }) {
    clearModalErrors(errorBoxId);

    const originalHtml = submitBtn ? submitBtn.innerHTML : defaultBtnHtml;
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-1.5"></i> ${escapeHtml(loadingLabel)}`;
    }

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: new FormData(form)
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            showModalErrors(errorBoxId, data.message || `Erreur (${res.status})`, data.errors || null);
            if (data.errors && document.getElementById('pills-profile-btn') && window.innerWidth < 992) {
                syncMobileTabDisplay('profile');
            }
            return;
        }

        showToast('success', data.message || 'Opération enregistrée.');
        refreshUsersView();
    } catch (err) {
        showModalErrors(errorBoxId, 'Erreur réseau : ' + err.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    }
}

function initUsersPage() {
    getUserModal();
    getPwdModal();

    const pBtn = document.getElementById('pills-profile-btn');
    const permsBtn = document.getElementById('pills-perms-btn');
    if (pBtn && permsBtn && !pBtn.dataset.bound) {
        pBtn.dataset.bound = '1';
        pBtn.addEventListener('click', () => syncMobileTabDisplay('profile'));
        permsBtn.addEventListener('click', () => syncMobileTabDisplay('perms'));
    }

    const userForm = document.getElementById('userForm');
    if (userForm && !userForm.dataset.bound) {
        userForm.dataset.bound = '1';
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('userSubmitBtn');
            submitAjaxModalForm({
                form: userForm,
                errorBoxId: 'userModalErrors',
                submitBtn,
                defaultBtnHtml: '<i class="fas fa-check-circle me-1.5"></i> Enregistrer',
                loadingLabel: 'Enregistrement...'
            });
        });
    }

    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm && !passwordForm.dataset.bound) {
        passwordForm.dataset.bound = '1';
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = passwordForm.querySelector('button[type="submit"]');
            submitAjaxModalForm({
                form: passwordForm,
                errorBoxId: 'pwdModalErrors',
                submitBtn,
                defaultBtnHtml: '<i class="fas fa-check me-1"></i> Appliquer',
                loadingLabel: 'Application...'
            });
        });
    }

    updateBulkBar();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUsersPage);
} else {
    initUsersPage();
}

// Mobile tab switcher helpers
function syncMobileTabDisplay(tab) {
    if (window.innerWidth >= 992) return;

    const leftCol = document.getElementById('pills-profile-content');
    const rightCol = document.getElementById('pills-perms-content');
    const backBtn = document.getElementById('mobBackBtn');

    if (tab === 'profile') {
        if (leftCol) leftCol.style.display = 'block';
        if (rightCol) rightCol.style.display = 'none';
        if (backBtn) backBtn.style.display = 'none';
        document.getElementById('pills-profile-btn')?.classList.add('active');
        document.getElementById('pills-perms-btn')?.classList.remove('active');
    } else {
        if (leftCol) leftCol.style.display = 'none';
        if (rightCol) rightCol.style.display = 'block';
        if (backBtn) backBtn.style.display = 'inline-block';
        document.getElementById('pills-perms-btn')?.classList.add('active');
        document.getElementById('pills-profile-btn')?.classList.remove('active');
    }
}

function switchToPermissionsTab() {
    syncMobileTabDisplay('perms');
}

function switchToProfileTab() {
    syncMobileTabDisplay('profile');
}

function clearRoleSelections() {
    document.querySelectorAll('.role-select-card').forEach(c => {
        c.classList.remove('selected-admin', 'selected-manager', 'selected-staff', 'selected-custom');
    });
    document.querySelectorAll('.role-checkbox').forEach(cb => {
        cb.checked = false;
    });
}

// 1. OPEN CREATE MODAL
function openCreateModal() {
    clearModalErrors('userModalErrors');
    document.getElementById('userModalLabel').textContent = 'Nouveau Collaborateur';
    document.getElementById('userModalSub').textContent = 'Créer un compte d\'accès sécurisé avec rôles et permissions';
    document.getElementById('userModalIcon').className = 'fas fa-user-plus text-danger';
    document.getElementById('userFormMethod').value = 'POST';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userSubmitBtn').innerHTML = '<i class="fas fa-check-circle me-1.5"></i> Enregistrer';

    const lastLoginBanner = document.getElementById('userLastLoginBanner');
    if (lastLoginBanner) lastLoginBanner.classList.add('d-none');

    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_phone').value = '';
    document.getElementById('user_job_title').value = '';
    document.getElementById('user_status').value = 'active';
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = true;
    document.getElementById('password_hint').textContent = 'Min. 8 caractères sécurisés.';
    const copiedToast = document.getElementById('pwd-copied-toast');
    if (copiedToast) copiedToast.style.display = 'none';

    clearRoleSelections();

    const staffCard = document.getElementById('role-card-Staff');
    if (staffCard && staffCard.dataset.restricted !== '1') {
        selectRoleCard('Staff');
    } else {
        const firstAvailable = document.querySelector('.role-checkbox:not([disabled])');
        if (firstAvailable?.dataset.roleName) {
            selectRoleCard(firstAvailable.dataset.roleName);
        }
    }

    document.querySelectorAll('.perm-switch-input').forEach(sw => sw.checked = false);
    onPermSwitchChange();

    // Auto-generate strong initial password without mutating clipboard on open
    generateStrongPassword('user_password', false);

    document.getElementById('perm-search-box').value = '';
    filterByDomain('all', document.querySelector('.domain-pill'));
    syncMobileTabDisplay('profile');

    getUserModal()?.show();
}

// 2. OPEN EDIT MODAL VIA AJAX
async function openEditModal(userId) {
    clearModalErrors('userModalErrors');

    try {
        const res = await fetch(`/users/${userId}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            showErrorAlert('Accès refusé', data.message || `Impossible de charger ce compte (${res.status}).`);
            return;
        }

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

        // Populate Last Login Activity Banner
        const loginBanner = document.getElementById('userLastLoginBanner');
        const loginDot = document.getElementById('userLastLoginDot');
        const loginText = document.getElementById('userLastLoginText');
        const loginIp = document.getElementById('userLastLoginIpBadge');
        if (loginBanner && loginDot && loginText && loginIp) {
            loginBanner.classList.remove('d-none');
            if (data.last_login_at) {
                if (data.is_online) {
                    loginDot.className = 'rounded-circle bg-success flex-shrink-0 pulse-dot';
                    loginText.innerHTML = `<span class="text-success fw-bold">En ligne actuellement</span> <span class="text-muted fw-normal" style="font-size: 0.74rem;">(${escapeHtml(data.last_login_diff)})</span>`;
                } else {
                    loginDot.className = 'rounded-circle bg-secondary flex-shrink-0';
                    loginText.innerHTML = `${escapeHtml(data.last_login_diff)} <span class="text-muted fw-normal" style="font-size: 0.74rem;">(${escapeHtml(data.last_login_at)})</span>`;
                }
                if (data.last_login_ip) {
                    loginIp.textContent = 'IP: ' + data.last_login_ip;
                    loginIp.classList.remove('d-none');
                } else {
                    loginIp.classList.add('d-none');
                }
            } else {
                loginDot.className = 'rounded-circle bg-warning flex-shrink-0';
                loginText.innerHTML = '<span class="text-muted fst-italic">Jamais connecté à la plateforme</span>';
                loginIp.classList.add('d-none');
            }
        }

        clearRoleSelections();
        (data.roles || []).forEach(roleName => {
            selectRoleCard(roleName, true);
        });

        const directPerms = Array.isArray(data.direct_permissions) ? data.direct_permissions : [];
        document.querySelectorAll('.perm-switch-input').forEach(sw => {
            sw.checked = directPerms.includes(sw.value);
        });

        onPermSwitchChange();

        document.getElementById('perm-search-box').value = '';
        filterByDomain('all', document.querySelector('.domain-pill'));
        syncMobileTabDisplay('profile');

        getUserModal()?.show();
    } catch (err) {
        showErrorAlert('Erreur de chargement', err.message);
    }
}

// 3. ROLE SELECTOR CARD CLICK
function selectRoleCard(roleName, multi = false) {
    const card = document.getElementById(`role-card-${roleName}`);
    const input = document.getElementById(`role-input-${roleName}`);
    if (!card || !input || card.dataset.restricted === '1') return;

    const roleColorClass = card.dataset.colorClass || (
        roleName === 'Admin' ? 'selected-admin' :
        (roleName === 'Manager' ? 'selected-manager' :
        (roleName === 'Staff' ? 'selected-staff' : 'selected-custom'))
    );

    if (!multi) {
        clearRoleSelections();
        card.classList.add(roleColorClass);
        input.checked = true;
    } else {
        const isSelected = input.checked;
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

// 4. RESET DIRECT OVERRIDES TO ROLE BASELINE
function resetPermsToRole() {
    document.querySelectorAll('.perm-switch-input').forEach(sw => {
        sw.checked = false;
    });
    onPermSwitchChange();
}

// 5. PERMISSIONS REACTION & REAL-TIME COUNTER
function onPermSwitchChange() {
    const inheritedPerms = new Set();

    document.querySelectorAll('.role-checkbox:checked').forEach(cb => {
        try {
            const perms = JSON.parse(cb.getAttribute('data-permissions') || '[]');
            perms.forEach(p => inheritedPerms.add(p));
        } catch (e) {}
    });

    let totalActive = 0;
    let viaRoleCount = 0;
    let directExtraCount = 0;

    document.querySelectorAll('.perm-switch-input').forEach(sw => {
        const permName = sw.value;
        const isInherited = inheritedPerms.has(permName);
        const badge = document.getElementById(`badge-inherited-${permName}`);
        const row = document.getElementById(`perm-row-${permName}`);

        if (badge) {
            badge.classList.toggle('d-none', !isInherited);
        }

        if (isInherited) viaRoleCount++;
        if (sw.checked && !isInherited) directExtraCount++;

        const isActive = sw.checked || isInherited;
        if (isActive) {
            totalActive++;
            row?.classList.add('is-active');
        } else {
            row?.classList.remove('is-active');
        }
    });

    const countNumber = document.getElementById('perms-count-number');
    if (countNumber) countNumber.textContent = totalActive;

    const mobCounter = document.getElementById('mob-perms-counter');
    if (mobCounter) mobCounter.textContent = totalActive;

    const breakdown = document.getElementById('perms-summary-breakdown');
    if (breakdown) {
        breakdown.textContent = `${viaRoleCount} via rôle • ${directExtraCount} directe${directExtraCount > 1 ? 's' : ''}`;
    }
}

// 6. INSTANT LIVE SEARCH & DOMAIN FILTER FOR PERMISSIONS
function applyPermissionFilters() {
    const q = (document.getElementById('perm-search-box')?.value || '').toLowerCase().trim();
    let totalVisible = 0;

    document.querySelectorAll('.perm-module-section').forEach(sec => {
        const domainKey = sec.getAttribute('data-domain-key');
        const matchesDomain = (activePermDomain === 'all' || domainKey === activePermDomain);
        let sectionVisible = 0;

        sec.querySelectorAll('.perm-item').forEach(item => {
            const name = item.getAttribute('data-perm-name') || '';
            const desc = item.getAttribute('data-perm-desc') || '';
            const show = matchesDomain && (q === '' || name.includes(q) || desc.includes(q));
            item.classList.toggle('d-none', !show);
            if (show) {
                sectionVisible++;
                totalVisible++;
            }
        });

        sec.classList.toggle('d-none', !matchesDomain || sectionVisible === 0);
    });

    const emptyBox = document.getElementById('perms-empty-search');
    if (emptyBox) {
        emptyBox.classList.toggle('d-none', totalVisible > 0);
    }
}

function filterPermissionsList() {
    applyPermissionFilters();
}

function filterByDomain(domainKey, clickedPill) {
    activePermDomain = domainKey || 'all';
    document.querySelectorAll('.domain-pill').forEach(p => p.classList.remove('active'));
    if (clickedPill) clickedPill.classList.add('active');

    applyPermissionFilters();

    const scrollArea = document.getElementById('perms-scroll-area');
    if (scrollArea) scrollArea.scrollTop = 0;
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

// 8. PASSWORD GENERATOR & STRENGTH EVALUATOR
function generateStrongPassword(targetInputId, copyToClip = true) {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789#@!%*?";
    let pwd = "Wina-";
    for (let i = 0; i < 9; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const input = document.getElementById(targetInputId);
    if (input) {
        input.value = pwd;
        if (targetInputId === 'user_password') checkPasswordStrength(pwd);
        if (copyToClip) {
            const toastId = targetInputId === 'modal_new_password' ? 'pwd-reset-copied-toast' : 'pwd-copied-toast';
            copyToClipboard(targetInputId, toastId);
        }
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
        navigator.clipboard.writeText(input.value).catch(() => {});
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

// 9. TOGGLE STATUS (AJAX)
async function toggleUserStatus(userId, userName, triggerSwitch = null) {
    try {
        const res = await fetch(`/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.success) {
            if (triggerSwitch) triggerSwitch.checked = !triggerSwitch.checked;
            showErrorAlert('Action impossible', data.message || `Erreur (${res.status})`);
            return;
        }

        const isActive = data.status === 'active';
        const mobDot = document.getElementById(`mob-dot-${userId}`);
        if (mobDot) mobDot.className = `status-dot ${data.status}`;

        const deskDot = document.getElementById(`desk-dot-${userId}`);
        if (deskDot) deskDot.className = `status-dot ${data.status}`;

        const mobSwitch = document.getElementById(`switch-mob-${userId}`);
        if (mobSwitch && mobSwitch !== triggerSwitch) mobSwitch.checked = isActive;

        const mobLabel = document.getElementById(`mob-status-label-${userId}`);
        if (mobLabel) mobLabel.textContent = isActive ? 'Actif' : 'Inactif';

        const deskCol = document.getElementById(`desk-status-col-${userId}`);
        if (deskCol) deskCol.innerHTML = data.badge;

        const deskBtn = document.getElementById(`desk-btn-toggle-${userId}`);
        if (deskBtn) {
            deskBtn.innerHTML = isActive
                ? '<i class="fas fa-toggle-on text-success fa-lg"></i>'
                : '<i class="fas fa-toggle-off text-muted fa-lg"></i>';
        }

        showToast('success', `${userName} est maintenant ${isActive ? 'actif' : 'inactif'}.`);
    } catch (err) {
        if (triggerSwitch) triggerSwitch.checked = !triggerSwitch.checked;
        showErrorAlert('Erreur réseau', err.message);
    }
}

// 10. OPEN PASSWORD RESET MODAL
function openPasswordModal(userId, userName, userEmail) {
    clearModalErrors('pwdModalErrors');
    document.getElementById('pwd_modal_name').textContent = userName;
    document.getElementById('pwd_modal_email').textContent = userEmail;
    document.getElementById('passwordForm').action = `/users/${userId}/reset-password`;
    document.getElementById('modal_new_password').value = '';
    document.getElementById('pwd-reset-copied-toast').style.display = 'none';

    generateStrongPassword('modal_new_password', false);

    getPwdModal()?.show();
}

// 11. BULK SELECTION & ACTIONS
function getSelectedUserIds() {
    const ids = new Set();
    document.querySelectorAll('.user-select-cb:checked').forEach(cb => {
        const val = Number(cb.value);
        if (val > 0) ids.add(val);
    });
    return Array.from(ids);
}

function updateBulkBar() {
    const ids = getSelectedUserIds();
    const bar = document.getElementById('usersBulkBar');
    const countEl = document.getElementById('usersBulkCount');
    if (!bar || !countEl) return;

    countEl.textContent = ids.length;
    bar.classList.toggle('d-none', ids.length === 0);
    bar.classList.toggle('d-flex', ids.length > 0);

    const allMaster = document.getElementById('selectAllUsers');
    if (allMaster) {
        const totalUnique = new Set(Array.from(document.querySelectorAll('.user-select-cb')).map(cb => cb.value)).size;
        allMaster.checked = totalUnique > 0 && ids.length === totalUnique;
        allMaster.indeterminate = ids.length > 0 && ids.length < totalUnique;
    }
}

function onUserSelectChange(cb) {
    document.querySelectorAll(`.user-select-cb[value="${cb.value}"]`).forEach(other => {
        other.checked = cb.checked;
    });
    updateBulkBar();
}

function toggleSelectAllUsers(masterCb) {
    document.querySelectorAll('.user-select-cb').forEach(cb => {
        cb.checked = masterCb.checked;
    });
    updateBulkBar();
}

function clearUserSelection() {
    document.querySelectorAll('.user-select-cb').forEach(cb => {
        cb.checked = false;
    });
    const allMaster = document.getElementById('selectAllUsers');
    if (allMaster) {
        allMaster.checked = false;
        allMaster.indeterminate = false;
    }
    updateBulkBar();
}

async function runBulkAction(action) {
    const userIds = getSelectedUserIds();
    if (userIds.length === 0) return;

    const payload = { action, user_ids: userIds };
    if (action === 'assign_role') {
        const role = document.getElementById('bulkRoleSelect')?.value || '';
        if (!role) {
            showErrorAlert('Rôle requis', 'Veuillez sélectionner un rôle à appliquer.');
            return;
        }
        payload.role = role;
    }

    try {
        const res = await fetch("{{ route('users.bulk-action') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            showErrorAlert('Action groupée refusée', data.message || `Erreur (${res.status})`);
            return;
        }

        clearUserSelection();
        showToast('success', data.message || 'Action groupée effectuée.');
        refreshUsersView();
    } catch (err) {
        showErrorAlert('Erreur réseau', err.message);
    }
}

// 12. DELETE USER CONFIRMATION (AJAX)
async function deleteUserConfirm(userId, userName, destroyUrl) {
    let confirmed = false;
    if (typeof Swal !== 'undefined') {
        const result = await Swal.fire({
            icon: 'warning',
            title: 'Supprimer ce compte ?',
            text: `Le compte "${userName}" sera définitivement supprimé.`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Supprimer',
            cancelButtonText: 'Annuler'
        });
        confirmed = result.isConfirmed;
    } else {
        confirmed = confirm(`Supprimer définitivement le collaborateur ${userName} ?`);
    }

    if (!confirmed) return;

    try {
        const res = await fetch(destroyUrl, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            showErrorAlert('Suppression impossible', data.message || `Erreur (${res.status})`);
            return;
        }

        showToast('success', data.message || `Compte "${userName}" supprimé.`);
        refreshUsersView();
    } catch (err) {
        showErrorAlert('Erreur réseau', err.message);
    }
}
</script>
@endpush
@endsection
