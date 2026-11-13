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
                <p class="brand-subtitle">Audit complet des actions : anciennes valeurs, adresses IP, modules et opérateurs</p>
            </div>
        </div>

        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('activity-logs.export', request()->except('page')) }}" class="btn-brand-outline">
                <i class="fas fa-file-csv me-1.5 text-success"></i>
                <span>Exporter CSV</span>
            </a>

            <form id="purgeLogsForm" action="{{ route('activity-logs.clear') }}" method="POST" class="d-flex align-items-center gap-1.5 m-0">
                @csrf
                <select name="older_than_days" id="purgeDaysSelect" class="form-select form-select-sm rounded-pill bg-white border" style="width: auto; font-size: 0.78rem;">
                    <option value="30">&gt; 30 jours</option>
                    <option value="90" selected>&gt; 90 jours</option>
                    <option value="180">&gt; 180 jours</option>
                    <option value="365">&gt; 1 an</option>
                </select>
                <button type="button" onclick="confirmPurgeLogs()" class="btn-brand-outline text-danger" style="border-color: #fee2e2;">
                    <i class="fas fa-trash-can me-1.5 text-danger"></i>
                    <span>Purger</span>
                </button>
            </form>
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

    <!-- KPI Statistics Cards -->
    <div class="row g-2.5 g-md-3 mb-3">
        <div class="col-6 col-lg-3">
            <a href="{{ route('activity-logs.index') }}" class="text-decoration-none d-block h-100">
                <div class="stat-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-kpi-label">Résultats / Total</div>
                            <div class="stat-kpi-value text-dark">{{ number_format($stats['filtered_count']) }}</div>
                            <div class="stat-kpi-sub text-muted">sur {{ number_format($stats['total_activities']) }} au total</div>
                        </div>
                        <div class="stat-kpi-icon bg-primary-subtle text-primary">
                            <i class="fas fa-list-check"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('activity-logs.index', ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]) }}" class="text-decoration-none d-block h-100">
                <div class="stat-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-kpi-label">Aujourd'hui</div>
                            <div class="stat-kpi-value text-success">{{ number_format($stats['today_activities']) }}</div>
                            <div class="stat-kpi-sub text-muted">Actions du jour</div>
                        </div>
                        <div class="stat-kpi-icon bg-success-subtle text-success">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <a href="{{ route('activity-logs.index', ['event' => 'security']) }}" class="text-decoration-none d-block h-100">
                <div class="stat-kpi-card h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-kpi-label">Alertes &amp; Suppr.</div>
                            <div class="stat-kpi-value text-danger">{{ number_format($stats['security_events']) }}</div>
                            <div class="stat-kpi-sub text-muted">Suppressions &amp; sécurité</div>
                        </div>
                        <div class="stat-kpi-icon bg-danger-subtle text-danger">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Opérateurs</div>
                        <div class="stat-kpi-value text-info">{{ number_format($stats['unique_users']) }}</div>
                        <div class="stat-kpi-sub text-muted">Comptes tracés</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Event Filter Chips (Horizontal Scroll) -->
    @php
        $baseChipParams = request()->except(['event', 'page']);
    @endphp
    <div class="chips-scroll mb-2.5">
        <a href="{{ route('activity-logs.index', $baseChipParams) }}"
           class="chip-item {{ !request('event') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Tous</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'updated'])) }}"
           class="chip-item {{ request('event') === 'updated' ? 'active' : '' }}">
            <i class="fas fa-pen-to-square text-primary"></i>
            <span>Modifications</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'created'])) }}"
           class="chip-item {{ request('event') === 'created' ? 'active' : '' }}">
            <i class="fas fa-plus-circle text-success"></i>
            <span>Créations</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'deleted'])) }}"
           class="chip-item {{ request('event') === 'deleted' ? 'active' : '' }}">
            <i class="fas fa-trash-can text-danger"></i>
            <span>Suppressions</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'login'])) }}"
           class="chip-item {{ request('event') === 'login' ? 'active' : '' }}">
            <i class="fas fa-right-to-bracket text-info"></i>
            <span>Connexions</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'exported'])) }}"
           class="chip-item {{ request('event') === 'exported' ? 'active' : '' }}">
            <i class="fas fa-file-export text-secondary"></i>
            <span>Exports</span>
        </a>
        <a href="{{ route('activity-logs.index', array_merge($baseChipParams, ['event' => 'security'])) }}"
           class="chip-item {{ request('event') === 'security' ? 'active' : '' }}">
            <i class="fas fa-shield-exclamation text-danger"></i>
            <span>Sécurité</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card border-0 rounded-4 shadow-xs bg-white mb-3">
        <div class="card-body p-2.5 p-md-3">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="row g-2 align-items-center" id="activityFilterForm">
                <div class="col-12 col-lg-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-light border-start-0 rounded-end-pill py-2"
                               placeholder="Mot-clé, IP, utilisateur, #ID..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="user_id" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()" aria-label="Filtrer par opérateur">
                        <option value="">Tous opérateurs</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="subject_type" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()" aria-label="Filtrer par module">
                        <option value="">Tous les modules</option>
                        @foreach($subjectTypes as $typeClass => $typeLabel)
                            <option value="{{ $typeClass }}" {{ request('subject_type') === $typeClass ? 'selected' : '' }}>
                                {{ $typeLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <select name="event" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()" aria-label="Filtrer par événement">
                        <option value="">Tous événements</option>
                        <option value="security" {{ request('event') === 'security' ? 'selected' : '' }}>Sécurité &amp; Suppressions</option>
                        @foreach($eventOptions as $evtKey => $evtLabel)
                            <option value="{{ $evtKey }}" {{ request('event') === $evtKey ? 'selected' : '' }}>
                                {{ $evtLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 col-lg-1">
                    <select name="per_page" class="form-select bg-light rounded-pill py-2" onchange="this.form.submit()" aria-label="Lignes par page">
                        @foreach([25, 50, 100] as $pp)
                            <option value="{{ $pp }}" {{ (int) ($perPage ?? 50) === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">Du</span>
                        <input type="date" name="start_date" class="form-control bg-light border-start-0"
                               value="{{ request('start_date') }}" title="Date début">
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">Au</span>
                        <input type="date" name="end_date" class="form-control bg-light border-start-0"
                               value="{{ request('end_date') }}" title="Date fin">
                    </div>
                </div>

                <div class="col-12 col-md-4 col-lg-8 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="text-muted small me-1" style="font-size: 0.74rem;">Période :</span>
                        <a href="{{ route('activity-logs.index', array_merge(request()->except(['start_date', 'end_date', 'page']), ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()])) }}"
                           class="btn btn-xs btn-light border rounded-pill px-2.5 py-1 {{ request('start_date') === now()->toDateString() && request('end_date') === now()->toDateString() ? 'fw-bold border-dark' : '' }}" style="font-size: 0.74rem;">
                            Aujourd'hui
                        </a>
                        <a href="{{ route('activity-logs.index', array_merge(request()->except(['start_date', 'end_date', 'page']), ['start_date' => now()->subDays(6)->toDateString(), 'end_date' => now()->toDateString()])) }}"
                           class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                            7 jours
                        </a>
                        <a href="{{ route('activity-logs.index', array_merge(request()->except(['start_date', 'end_date', 'page']), ['start_date' => now()->subDays(29)->toDateString(), 'end_date' => now()->toDateString()])) }}"
                           class="btn btn-xs btn-light border rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                            30 jours
                        </a>
                    </div>
                    <div class="d-flex gap-2 ms-auto">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3.5 py-1.5 fw-semibold">
                            <i class="fas fa-filter me-1"></i> Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'user_id', 'subject_type', 'start_date', 'end_date', 'event']))
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5" title="Réinitialiser">
                                <i class="fas fa-rotate-left me-1"></i> Réinitialiser
                            </a>
                        @endif
                    </div>
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
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 13%;">Événement</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 35%;">Description &amp; Détail</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 17%;">Date, IP &amp; Appareil</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.74rem; letter-spacing: 0.05em; width: 13%;">Inspection</th>
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
                                    @if($log->user_id)
                                        <a href="{{ route('activity-logs.index', array_merge(request()->except('page'), ['user_id' => $log->user_id])) }}"
                                           class="fw-bold text-dark text-truncate d-block small text-decoration-none" title="Filtrer par {{ $userName }}">
                                            {{ $userName }}
                                        </a>
                                    @else
                                        <span class="fw-bold text-dark text-truncate d-block small">{{ $userName }}</span>
                                    @endif
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
                                @if($hasDiff)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        {{ count($log->changes) }} champ(s)
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
                            <div class="small fw-semibold text-dark">
                                {{ $log->created_at->format('d/m/Y') }}
                                <span class="text-muted fw-normal font-monospace" style="font-size: 0.74rem;">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                @if($log->ip_address)
                                    <a href="{{ route('activity-logs.index', ['search' => $log->ip_address]) }}"
                                       class="badge bg-light text-secondary border font-monospace text-decoration-none px-1.5 py-0" style="font-size: 0.65rem;" title="Filtrer par IP">
                                        {{ $log->ip_address }}
                                    </a>
                                @endif
                                <small class="text-muted" style="font-size: 0.68rem;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent_summary }}
                                </small>
                            </div>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <button type="button"
                                    class="btn btn-sm {{ $hasDiff ? 'btn-outline-primary fw-semibold' : 'btn-light text-secondary border' }} rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1.5"
                                    onclick="openLogDiffModal({{ $log->id }})">
                                <i class="fas {{ $hasDiff ? 'fa-code-compare' : 'fa-eye' }}"></i>
                                <span>{{ $hasDiff ? 'Voir Diff' : 'Détails' }}</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4 opacity-75">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h6 class="fw-bold text-dark">Aucune activité enregistrée</h6>
                                <p class="text-muted small mb-0">Aucune trace ne correspond aux filtres sélectionnés.</p>
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
                <button type="button"
                        class="btn btn-sm {{ $hasDiff ? 'btn-outline-primary fw-semibold' : 'btn-light text-secondary border' }} rounded-pill px-3 py-1"
                        style="font-size: 0.78rem;" onclick="openLogDiffModal({{ $log->id }})">
                    <i class="fas {{ $hasDiff ? 'fa-code-compare' : 'fa-eye' }} me-1"></i> {{ $hasDiff ? 'Voir Diff' : 'Détails' }}
                </button>
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
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Date &amp; Heure :</span>
                            <span class="text-dark font-monospace" id="diff_modal_date"></span>
                        </div>
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">IP &amp; Appareil :</span>
                            <span class="text-dark font-monospace d-block" id="diff_modal_ip"></span>
                            <span class="text-muted d-block" style="font-size: 0.68rem;" id="diff_modal_ua"></span>
                        </div>
                        <div class="col-6 col-sm-3">
                            <span class="text-muted d-block" style="font-size: 0.72rem;">Élément ciblé :</span>
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill" id="diff_modal_subject"></span>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top small text-muted font-monospace" style="font-size: 0.74rem;" id="diff_modal_desc"></div>
                </div>

                <!-- Changes Table -->
                <div class="card border-0 rounded-3 bg-white shadow-xs overflow-hidden mb-3" id="diff_modal_changes_card">
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

                <!-- Extra Properties Table -->
                <div class="card border-0 rounded-3 bg-white shadow-xs overflow-hidden d-none" id="diff_modal_props_card">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small">
                            <i class="fas fa-circle-info text-secondary me-1.5"></i> Propriétés &amp; Métadonnées
                        </span>
                        <span class="badge bg-light text-secondary border rounded-pill" id="diff_modal_props_count">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody id="diff_modal_props_tbody" class="divide-y small"></tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white py-2.5 px-3 border-top d-flex justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a id="diff_modal_source_btn" href="#" class="btn btn-sm btn-primary rounded-pill px-3.5 d-none">
                        <i class="fas fa-arrow-up-right-from-square me-1.5"></i> Fiche source
                    </a>
                    <a id="diff_modal_full_btn" href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-expand me-1"></i> Page complète
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
function getLogDiffModal() {
    const el = document.getElementById('logDiffModal');
    return el ? bootstrap.Modal.getOrCreateInstance(el) : null;
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatDiffValue(val, emptyLabel) {
    if (val === null || val === undefined || val === '') {
        return `<span class="text-muted fst-italic">${escapeHtml(emptyLabel)}</span>`;
    }
    const raw = typeof val === 'object' ? JSON.stringify(val, null, 2) : String(val);
    return `<span style="white-space: pre-wrap; word-break: break-word;">${escapeHtml(raw)}</span>`;
}

async function confirmPurgeLogs() {
    const days = document.getElementById('purgeDaysSelect')?.value || '90';
    const form = document.getElementById('purgeLogsForm');
    if (!form) return;

    let confirmed = false;
    if (typeof Swal !== 'undefined') {
        const res = await Swal.fire({
            icon: 'warning',
            title: 'Purger les anciens journaux ?',
            text: `Les entrées antérieures à ${days} jours seront supprimées définitivement.`,
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Purger maintenant',
            cancelButtonText: 'Annuler'
        });
        confirmed = res.isConfirmed;
    } else {
        confirmed = confirm(`Purger définitivement les journaux de plus de ${days} jours ?`);
    }

    if (confirmed) {
        form.submit();
    }
}

async function openLogDiffModal(logId) {
    document.getElementById('logDiffModalLabel').textContent = 'Chargement du comparatif...';
    document.getElementById('diff_modal_tbody').innerHTML = `
        <tr>
            <td colspan="3" class="text-center py-4 text-muted">
                <i class="fas fa-spinner fa-spin me-2"></i> Chargement des données...
            </td>
        </tr>
    `;
    document.getElementById('diff_modal_props_card')?.classList.add('d-none');

    const fullBtn = document.getElementById('diff_modal_full_btn');
    if (fullBtn) fullBtn.href = `/activity-logs/${logId}`;

    getLogDiffModal()?.show();

    try {
        const res = await fetch(`/activity-logs/${logId}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) {
            throw new Error(`Erreur serveur (${res.status})`);
        }
        const data = await res.json();

        document.getElementById('logDiffModalLabel').textContent = `Activité #${data.id} • ${(data.event || '').toUpperCase()}`;
        document.getElementById('diff_modal_user').textContent = data.user_name + (data.user_email ? ` (${data.user_email})` : '');
        document.getElementById('diff_modal_date').textContent = data.date || '';
        document.getElementById('diff_modal_ip').textContent = data.ip_address || '—';
        document.getElementById('diff_modal_ua').textContent = data.user_agent_summary || '';
        document.getElementById('diff_modal_subject').textContent = data.subject_label || 'Système';
        document.getElementById('diff_modal_desc').textContent = data.description || '';

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
        } else {
            keys.forEach(key => {
                const item = changes[key] || {};
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold text-dark font-monospace small ps-3">${escapeHtml(key)}</td>
                    <td class="diff-old">${formatDiffValue(item.old, 'Vide / Initial')}</td>
                    <td class="diff-new">${formatDiffValue(item.new, 'Supprimé / Nul')}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Render extra properties (excluding old/new already shown in diff table)
        const propsCard = document.getElementById('diff_modal_props_card');
        const propsTbody = document.getElementById('diff_modal_props_tbody');
        const propsCount = document.getElementById('diff_modal_props_count');
        if (propsCard && propsTbody && propsCount) {
            const rawProps = data.properties || {};
            const extraKeys = Object.keys(rawProps).filter(k => k !== 'old' && k !== 'new');
            propsTbody.innerHTML = '';
            if (extraKeys.length > 0) {
                propsCount.textContent = `${extraKeys.length} clé(s)`;
                extraKeys.forEach(k => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="ps-3 py-2 fw-bold text-dark font-monospace" style="width: 30%;">${escapeHtml(k)}</td>
                        <td class="pe-3 py-2 font-monospace text-secondary">${formatDiffValue(rawProps[k], '—')}</td>
                    `;
                    propsTbody.appendChild(tr);
                });
                propsCard.classList.remove('d-none');
            } else {
                propsCard.classList.add('d-none');
            }
        }
    } catch (err) {
        document.getElementById('diff_modal_tbody').innerHTML = `
            <tr>
                <td colspan="3" class="text-center py-4 text-danger small">
                    <i class="fas fa-circle-exclamation me-1"></i> Erreur lors du chargement : ${escapeHtml(err.message)}
                </td>
            </tr>
        `;
    }
}
</script>
@endpush
@endsection
