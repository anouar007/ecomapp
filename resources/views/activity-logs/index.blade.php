@extends('layouts.app')

@section('title', "Journal d'Activité & Traçabilité")

@push('styles')
<style>
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

/* Diff Table Styles */
.diff-table th {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    background: #f8fafc;
}
.diff-old {
    background-color: #fff1f2 !important;
    color: #be123c !important;
    font-family: monospace;
    font-size: 0.82rem;
    border-left: 3px solid #f43f5e;
}
.diff-new {
    background-color: #f0fdf4 !important;
    color: #15803d !important;
    font-family: monospace;
    font-size: 0.82rem;
    border-left: 3px solid #22c55e;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-2 py-md-3">

    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h1 class="brand-title">Journal d'Activité & Traçabilité</h1>
                <p class="brand-subtitle">Historique complet des modifications : anciennes valeurs, dates et opérateurs</p>
            </div>
        </div>

        <div class="brand-header-actions">
            <form action="{{ route('activity-logs.clear') }}" method="POST" onsubmit="return confirm('Purger définitivement les journaux anciens de plus de 90 jours ?');" class="m-0">
                @csrf
                <input type="hidden" name="older_than_days" value="90">
                <button type="submit" class="btn-brand-outline text-danger" style="border-color: #fee2e2;">
                    <i class="fas fa-trash-can me-1.5 text-danger"></i>
                    <span>Purger (>90j)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Navigation Croisée Système & Audit -->
    <div class="subnav-pills-bar">
        <a href="{{ route('activity-logs.index') }}" class="subnav-pill active">
            <i class="fas fa-shield-alt text-primary"></i>
            <span>Journal d'Activité & Traçabilité</span>
            <span class="subnav-pill-badge">{{ $logs->total() ?? 0 }}</span>
        </a>
        <a href="{{ route('users.index') }}" class="subnav-pill">
            <i class="fas fa-users-gear text-dark"></i>
            <span>Équipe, Collaborateurs & Rôles</span>
        </a>
        <a href="{{ route('settings.index') }}" class="subnav-pill">
            <i class="fas fa-sliders text-secondary"></i>
            <span>Paramètres Système</span>
        </a>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-xs mb-3 d-flex align-items-center gap-2 py-2.5 px-3" role="alert">
        <i class="fas fa-circle-check fs-5 text-success flex-shrink-0"></i>
        <div class="small fw-semibold">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- KPI Statistics Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Événements</div>
                        <div class="stat-kpi-value text-dark">{{ number_format($stats['total_activities']) }}</div>
                        <div class="stat-kpi-sub text-muted">Historique audité</div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-list-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Aujourd'hui</div>
                        <div class="stat-kpi-value text-success">{{ number_format($stats['today_activities']) }}</div>
                        <div class="stat-kpi-sub text-muted">Actions en direct</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-bolt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Opérateurs</div>
                        <div class="stat-kpi-value text-info">{{ number_format($stats['unique_users']) }}</div>
                        <div class="stat-kpi-sub text-muted">Membres actifs</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Event Filter Chips (Horizontal Scroll) -->
    <div class="chips-scroll mb-2.5">
        <a href="{{ route('activity-logs.index', request()->except('event')) }}" 
           class="chip-item {{ !request('event') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Tous les événements</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge(request()->all(), ['event' => 'updated'])) }}" 
           class="chip-item {{ request('event') === 'updated' ? 'active' : '' }}">
            <i class="fas fa-pen-to-square text-primary"></i>
            <span>Modifications</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge(request()->all(), ['event' => 'created'])) }}" 
           class="chip-item {{ request('event') === 'created' ? 'active' : '' }}">
            <i class="fas fa-plus-circle text-success"></i>
            <span>Créations</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge(request()->all(), ['event' => 'deleted'])) }}" 
           class="chip-item {{ request('event') === 'deleted' ? 'active' : '' }}">
            <i class="fas fa-trash-can text-danger"></i>
            <span>Suppressions</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge(request()->all(), ['event' => 'login'])) }}" 
           class="chip-item {{ request('event') === 'login' ? 'active' : '' }}">
            <i class="fas fa-right-to-bracket text-info"></i>
            <span>Connexions</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 rounded-4 shadow-xs bg-white mb-3">
        <div class="card-body p-2.5 p-md-3">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-2 align-items-center">
                @if(request('event'))
                    <input type="hidden" name="event" value="{{ request('event') }}">
                @endif
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-pill py-2" 
                               placeholder="Rechercher par mot-clé, produit, commande..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-6 col-md-3">
                    <select name="user_id" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()">
                        <option value="">Tous les utilisateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <input type="date" name="start_date" class="form-control bg-light rounded-pill py-2" 
                           value="{{ request('start_date') }}" title="Date début" onchange="this.form.submit()">
                </div>
                
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill py-2 w-100 fw-semibold">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'user_id', 'start_date', 'end_date', 'event']))
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary rounded-pill py-2 px-3" title="Réinitialiser">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 1. DESKTOP TABLE VIEW (>= 992px)           -->
    <!-- ========================================== -->
    <div class="card border-0 rounded-4 shadow-xs bg-white overflow-hidden d-none d-lg-block mb-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 22%;">Opérateur</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 14%;">Événement</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 34%;">Description & Détail</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 16%;">Date & Heure</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 14%;">Comparatif Diff</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($logs as $log)
                    @php
                        $userName = $log->user ? $log->user->name : ($log->properties['user_name'] ?? 'Système');
                        $userEmail = $log->user ? $log->user->email : ($log->properties['user_email'] ?? null);
                        $hasDiff = count($log->changes) > 0;
                    @endphp
                    <tr class="hover-bg-light transition-all">
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" 
                                     style="width: 36px; height: 36px; background: linear-gradient(135deg, #334155 0%, #0f172a 100%); font-size: 0.85rem;">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="fw-bold text-dark text-truncate d-block small">{{ $userName }}</span>
                                    @if($userEmail)
                                        <small class="text-muted text-truncate d-block" style="font-size: 0.72rem;">{{ $userEmail }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            {!! $log->event_badge !!}
                        </td>
                        <td class="py-3">
                            <div class="fw-medium text-dark small">{{ $log->description }}</div>
                            <div class="d-flex align-items-center gap-1.5 mt-1 flex-wrap">
                                @if($log->subject_type)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 font-monospace" style="font-size: 0.68rem;">
                                        {{ $log->subject_label }}
                                    </span>
                                @endif
                                @if($log->source_url)
                                    <a href="{{ $log->source_url }}" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-decoration-none px-2 py-0.5" style="font-size: 0.68rem;" title="Ouvrir la fiche source">
                                        <i class="fas fa-arrow-up-right-from-square me-1"></i> Fiche source
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="small fw-semibold text-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">
                                {{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})
                            </small>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            @if($hasDiff)
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1.5"
                                        onclick="openLogDiffModal({{ $log->id }})">
                                    <i class="fas fa-code-compare"></i>
                                    <span>Voir Diff</span>
                                </button>
                            @else
                                <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 text-secondary border">
                                    <i class="fas fa-eye me-1"></i> Détails
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4 opacity-75">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold text-dark">Aucune activité enregistrée</h6>
                                <p class="text-muted small mb-0">Les actions de création, mise à jour et suppression apparaîtront ici automatiquement.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. MOBILE USER CARDS (< 992px)             -->
    <!-- ========================================== -->
    <div class="d-block d-lg-none mb-4">
        @forelse($logs as $log)
        @php
            $userName = $log->user ? $log->user->name : ($log->properties['user_name'] ?? 'Système');
            $hasDiff = count($log->changes) > 0;
        @endphp
        <div class="card border rounded-4 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" 
                         style="width: 32px; height: 32px; background: linear-gradient(135deg, #334155 0%, #0f172a 100%); font-size: 0.8rem;">
                        {{ strtoupper(substr($userName, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <span class="fw-bold text-dark text-truncate d-block small">{{ $userName }}</span>
                    </div>
                </div>
                <span class="text-muted small font-monospace" style="font-size: 0.72rem;">
                    {{ $log->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-1.5 mb-2 flex-wrap">
                {!! $log->event_badge !!}
                @if($log->subject_type)
                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5 font-monospace" style="font-size: 0.68rem;">
                        {{ $log->subject_label }}
                    </span>
                @endif
                @if($log->source_url)
                    <a href="{{ $log->source_url }}" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill text-decoration-none px-2 py-0.5" style="font-size: 0.68rem;">
                        <i class="fas fa-arrow-up-right-from-square me-1"></i> Fiche
                    </a>
                @endif
            </div>

            <div class="text-dark small fw-medium mb-2.5" style="font-size: 0.82rem; line-height: 1.35;">
                {{ $log->description }}
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <small class="text-muted" style="font-size: 0.72rem;">
                    <i class="fas fa-clock me-1"></i>{{ $log->created_at->format('d/m/Y H:i:s') }}
                </small>
                @if($hasDiff)
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" 
                            style="font-size: 0.78rem;" onclick="openLogDiffModal({{ $log->id }})">
                        <i class="fas fa-code-compare me-1"></i> Voir Diff
                    </button>
                @else
                    <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-light rounded-pill px-3 py-1 text-secondary border" style="font-size: 0.78rem;">
                        <i class="fas fa-eye me-1"></i> Détails
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-4 border p-4">
            <i class="fas fa-history fa-3x text-muted opacity-50 mb-3"></i>
            <h6 class="fw-bold text-dark">Aucune activité enregistrée</h6>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links() }}
    </div>
    @endif

</div>

<!-- ======================================================= -->
<!-- MODAL: COMPARATIF DIFF EN DIRECT (OLD DATA VS NEW DATA) -->
<!-- ======================================================= -->
<div class="modal fade" id="logDiffModal" tabindex="-1" aria-labelledby="logDiffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            
            <!-- Modal Header -->
            <div class="modal-header bg-dark text-white py-3 px-3 px-md-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-code-compare text-primary" id="modalDiffIcon"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 fs-6" id="logDiffModalLabel">Détails des Modifications (Diff)</h6>
                        <span class="small text-white-50" id="logDiffModalSub">Comparaison Avant / Après</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-3 p-md-4 bg-light">
                
                <!-- Operator & Metadata Card -->
                <div class="card border-0 rounded-3 bg-white shadow-xs p-3 mb-3">
                    <div class="row g-2 small">
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Opérateur :</span>
                            <strong class="text-dark d-block" id="diff_modal_user"></strong>
                        </div>
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Date & Heure :</span>
                            <span class="text-dark font-monospace" id="diff_modal_date"></span>
                        </div>
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Adresse IP :</span>
                            <span class="text-dark font-monospace" id="diff_modal_ip"></span>
                        </div>
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Élément ciblé :</span>
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill" id="diff_modal_subject"></span>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted font-monospace" style="font-size: 0.72rem;" id="diff_modal_desc"></div>
                </div>

                <!-- Changes Table -->
                <div class="card border-0 rounded-3 bg-white shadow-xs overflow-hidden">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small">
                            <i class="fas fa-table-columns text-primary me-1.5"></i> Attributs Modifiés (Ancienne vs Nouvelle Valeur)
                        </span>
                        <span class="badge bg-light text-secondary border rounded-pill" id="diff_modal_count">0 changements</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table diff-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Champ / Attribut</th>
                                    <th style="width: 37.5%;">— Ancienne Valeur (Avant)</th>
                                    <th style="width: 37.5%;">+ Nouvelle Valeur (Après)</th>
                                </tr>
                            </thead>
                            <tbody id="diff_modal_tbody">
                                <!-- Populated dynamically via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white py-2.5 px-3 border-top d-flex justify-content-between">
                <div>
                    <a id="diff_modal_source_btn" href="#" class="btn btn-sm btn-primary rounded-pill px-3.5 d-none">
                        <i class="fas fa-arrow-up-right-from-square me-1.5"></i> Consulter la fiche source
                    </a>
                </div>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    Fermer
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
let bsDiffModal = null;

document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('logDiffModal');
    if (el) bsDiffModal = new bootstrap.Modal(el);
});

function openLogDiffModal(logId) {
    document.getElementById('logDiffModalLabel').textContent = 'Chargement du comparatif...';
    document.getElementById('diff_modal_tbody').innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin me-2"></i> Chargement des données...
            </td>
        </tr>
    `;

    if (bsDiffModal) bsDiffModal.show();

    fetch(`/activity-logs/${logId}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('logDiffModalLabel').textContent = `Activité #${data.id} • ${data.event.toUpperCase()}`;
        document.getElementById('diff_modal_user').textContent = data.user_name + (data.user_email ? ` (${data.user_email})` : '');
        document.getElementById('diff_modal_date').textContent = data.date;
        document.getElementById('diff_modal_ip').textContent = data.ip_address;
        document.getElementById('diff_modal_subject').textContent = data.subject_label;
        document.getElementById('diff_modal_desc').textContent = data.description;

        const sourceBtn = document.getElementById('diff_modal_source_btn');
        if (sourceBtn) {
            if (data.source_url) {
                sourceBtn.href = data.source_url;
                sourceBtn.classList.remove('d-none');
            } else {
                sourceBtn.classList.add('d-none');
            }
        }

        const tbody = document.getElementById('diff_modal_tbody');
        tbody.innerHTML = '';

        const changes = data.changes || {};
        const keys = Object.keys(changes);
        document.getElementById('diff_modal_count').textContent = `${keys.length} champ(s) modifié(s)`;

        if (keys.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted small">
                        Aucun détail de modification avant/après enregistré pour cet événement.
                    </td>
                </tr>
            `;
            return;
        }

        keys.forEach(key => {
            const item = changes[key];
            const oldVal = item.old !== null && item.old !== undefined ? (typeof item.old === 'object' ? JSON.stringify(item.old, null, 2) : String(item.old)) : '<span class="text-muted fst-italic">Vide / Initial</span>';
            const newVal = item.new !== null && item.new !== undefined ? (typeof item.new === 'object' ? JSON.stringify(item.new, null, 2) : String(item.new)) : '<span class="text-muted fst-italic">Supprimé / Nul</span>';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold text-dark font-monospace small ps-3">
                    ${escapeHtml(key)}
                </td>
                <td class="diff-old">
                    ${oldVal}
                </td>
                <td class="diff-new">
                    ${newVal}
                </td>
            `;
            tbody.appendChild(tr);
        });
    })
    .catch(err => {
        alert('Erreur lors du chargement : ' + err.message);
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
@endpush
@endsection
