@extends('layouts.app')

@section('title', 'Statistiques Équipe — Confirmations & POS')

@push('styles')
<style>
    .staff-stats-page .staff-stat-strong {
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums;
    }

    .staff-stats-page .staff-preset-scroll {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;
    }

    .staff-stats-page .staff-preset-scroll::-webkit-scrollbar {
        display: none;
    }

    .staff-stats-page .staff-preset-pill {
        flex-shrink: 0;
        white-space: nowrap;
        border-radius: 9999px !important;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 14px;
        font-size: 0.78rem;
        font-weight: 600 !important;
        line-height: 1;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        text-decoration: none !important;
        transition: all 0.15s ease;
    }

    .staff-stats-page .brand-filter-bar .input-group-sm {
        height: 36px;
        align-items: stretch;
    }

    .staff-stats-page .brand-filter-bar .input-group-text {
        height: 36px !important;
        padding: 0 10px !important;
        display: inline-flex;
        align-items: center;
        font-size: 0.78rem;
        font-weight: 600 !important;
    }

    .staff-stats-page .brand-filter-bar input[type="date"].form-control {
        height: 36px !important;
        min-height: 36px !important;
        padding: 0 10px !important;
        font-size: 0.8rem !important;
        line-height: 1.2;
    }

    .staff-stats-page .brand-filter-bar .btn-brand-primary.btn-sm,
    .staff-stats-page .brand-filter-bar .btn-brand-light.btn-sm {
        height: 36px !important;
        min-height: 36px !important;
        padding: 0 14px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        border-radius: 8px !important;
    }

    .staff-stats-page .brand-filter-bar .btn-brand-light.btn-sm {
        width: 36px !important;
        padding: 0 !important;
        flex: 0 0 36px;
    }

    .staff-stats-page .staff-preset-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .staff-stats-page .staff-preset-pill.active {
        background: #0f172a;
        color: #ffffff !important;
        border-color: #0f172a;
    }

    .staff-stats-page .staff-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px !important;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        height: 100%;
    }

    .staff-stats-page .staff-mobile-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px !important;
        padding: 14px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }

    .staff-stats-page .staff-metric-tile {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px !important;
        padding: 8px 10px;
    }

    @media (max-width: 767.98px) {
        .staff-stats-page .staff-kpi-card {
            padding: 11px 12px;
        }
    }
</style>
@endpush

@section('content')
<div class="staff-stats-page">
    <!-- En-tête -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-user-check"></i>
            </div>
            <div>
                <h1 class="brand-title staff-stat-strong">Performance Équipe &amp; Confirmations</h1>
                <p class="brand-subtitle mb-0">
                    <span class="d-none d-md-inline">Suivez les commandes confirmées par chaque membre (premier passage hors « En attente ») ainsi que les ventes créées en caisse POS</span>
                    <span class="d-md-none">Confirmations Site Web &amp; créations POS par utilisateur</span>
                </p>
                <div class="mt-1.5">
                    @if($canViewAllStaffStats ?? false)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                            <i class="fas fa-users me-1"></i> Vue Globale — Tous les utilisateurs
                        </span>
                    @else
                        <span class="badge bg-light text-dark border px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                            <i class="fas fa-user-lock me-1 text-primary"></i> Mes Statistiques — {{ auth()->user()->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('orders.all') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-layer-group" style="color: #4f46e5;"></i> <span>Toutes les commandes</span>
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-globe text-primary"></i> <span>Commandes Site Web</span>
            </a>
            <a href="{{ route('orders.pos') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-cash-register text-danger"></i> <span>Commandes POS</span>
            </a>
        </div>
    </div>

    <!-- Filtre Période -->
    <div class="brand-filter-bar mb-3">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-2.5">
            <div class="staff-preset-scroll" role="navigation" aria-label="Période statistique">
                @php
                    $presets = [
                        'all' => 'Tout',
                        'today' => "Aujourd'hui",
                        'yesterday' => 'Hier',
                        'last_7_days' => '7 derniers jours',
                        'this_month' => 'Ce mois-ci',
                        'last_month' => 'Mois dernier',
                    ];
                @endphp
                @foreach($presets as $pKey => $pLabel)
                    <a href="{{ route('orders.staff-stats', ['date_preset' => $pKey]) }}"
                       class="staff-preset-pill {{ $datePreset === $pKey ? 'active' : '' }}">
                        {{ $pLabel }}
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('orders.staff-stats') }}" class="d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="date_preset" value="custom">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted">Du</span>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted">Au</span>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <button type="submit" class="btn-brand-primary btn-sm">
                    <i class="fas fa-filter me-1"></i> Appliquer
                </button>
                @if($datePreset !== 'all')
                    <a href="{{ route('orders.staff-stats') }}" class="btn-brand-light btn-sm" title="Réinitialiser">
                        <i class="fas fa-redo"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(($summary['pending_unconfirmed_count'] ?? 0) > 0)
        <div class="alert alert-warning border border-warning-subtle rounded-4 shadow-2xs mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2 py-2.5 px-3">
            <div class="d-flex align-items-center gap-2 text-dark small">
                <i class="fas fa-clock text-warning fs-5"></i>
                <span>
                    <strong>{{ $summary['pending_unconfirmed_count'] }}</strong>
                    commande{{ $summary['pending_unconfirmed_count'] > 1 ? 's' : '' }} Site Web {{ $summary['pending_unconfirmed_count'] > 1 ? 'sont' : 'est' }} toujours <strong>En attente</strong> sans utilisateur confirmateur attribué.
                </span>
            </div>
            <a href="{{ route('orders.index', ['status' => 'pending', 'staff_id' => 'unconfirmed']) }}" class="btn btn-sm btn-warning fw-semibold rounded-pill px-3 text-dark">
                Traiter maintenant <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    @endif

    <!-- 4 Cartes KPI Globales -->
    <div class="row row-cols-2 row-cols-lg-4 g-2.5 g-md-3 mb-4">
        <div class="col">
            <div class="staff-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">Confirmées Site Web</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-user-check"></i></span>
                </div>
                <div class="staff-stat-strong fs-3 text-success">{{ number_format($summary['total_web_confirmed'] ?? 0, 0, ',', ' ') }}</div>
                <div class="text-muted small">
                    + {{ number_format($summary['total_web_cancelled'] ?? 0, 0, ',', ' ') }} annulée(s) après prise en charge
                </div>
            </div>
        </div>
        <div class="col">
            <div class="staff-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">Créées Caisse POS</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="fas fa-cash-register"></i></span>
                </div>
                <div class="staff-stat-strong fs-3 text-primary">{{ number_format($summary['total_pos_created'] ?? 0, 0, ',', ' ') }}</div>
                <div class="text-muted small">Ventes enregistrées en boutique</div>
            </div>
        </div>
        <div class="col">
            <div class="staff-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">Total Commandes</span>
                    <span class="badge bg-light text-dark border"><i class="fas fa-layer-group"></i></span>
                </div>
                <div class="staff-stat-strong fs-3 text-dark">{{ number_format($summary['total_handled'] ?? 0, 0, ',', ' ') }}</div>
                <div class="text-muted small">
                    @if($canViewAllStaffStats ?? false)
                        {{ $summary['active_staff_count'] ?? 0 }} utilisateur(s) actif(s)
                    @else
                        Vos commandes traitées
                    @endif
                </div>
            </div>
        </div>
        <div class="col">
            <div class="staff-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">Chiffre d'affaires géré</span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-coins"></i></span>
                </div>
                <div class="staff-stat-strong fs-3 text-danger">{{ currency($summary['total_revenue'] ?? 0) }}</div>
                <div class="text-muted small">Hors commandes annulées</div>
            </div>
        </div>
    </div>

    <!-- Tableau Desktop (>= 768px) -->
    <div class="brand-table-card orders-table-card d-none d-md-block">
        <div class="table-responsive" style="min-height: 180px;">
            <table class="brand-table align-middle" style="width: 100%; min-width: 920px;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.25rem;">Utilisateur</th>
                        <th class="text-center">Site Web Confirmées</th>
                        <th class="text-center">Site Web Annulées</th>
                        <th class="text-center">Ventes POS Créées</th>
                        <th class="text-center">Total Gérées</th>
                        <th class="text-center">Taux Livraison</th>
                        <th class="text-end">CA Traité</th>
                        <th style="padding-right: 1.25rem;">Dernière Activité</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $webFilterParams = array_filter([
                                'staff_id' => $row->user_id,
                                'date_from' => $dateFrom,
                                'date_to' => $dateTo,
                            ]);
                            $posFilterParams = array_filter([
                                'staff_id' => $row->user_id,
                                'date_from' => $dateFrom,
                                'date_to' => $dateTo,
                            ]);
                        @endphp
                        <tr>
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="order-table-avatar">
                                        {{ strtoupper(substr($row->name ?: 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="staff-stat-strong text-dark">{{ $row->name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            <span class="badge bg-light text-secondary border px-1.5 py-0 me-1" style="font-size: 0.65rem;">{{ $row->role_label }}</span>
                                            <span>{{ $row->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.index', $webFilterParams) }}"
                                   class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 text-decoration-none staff-stat-strong"
                                   title="Voir les commandes Site Web confirmées par {{ $row->name }}">
                                    {{ number_format($row->web_confirmed, 0, ',', ' ') }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if($row->web_cancelled > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 staff-stat-strong">
                                        {{ number_format($row->web_cancelled, 0, ',', ' ') }}
                                    </span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('orders.pos', $posFilterParams) }}"
                                   class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-decoration-none staff-stat-strong"
                                   title="Voir les ventes POS créées par {{ $row->name }}">
                                    {{ number_format($row->pos_created, 0, ',', ' ') }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-2.5 py-1 staff-stat-strong" style="font-size: 0.82rem;">
                                    {{ number_format($row->total_handled, 0, ',', ' ') }}
                                </span>
                            </td>
                            <td class="text-center" style="min-width: 135px;">
                                <div class="d-flex align-items-center gap-2 justify-content-center">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 72px; background: #e2e8f0;">
                                        <div class="progress-bar bg-success" style="width: {{ $row->delivery_rate }}%;"></div>
                                    </div>
                                    <span class="small staff-stat-strong text-secondary">{{ $row->delivery_rate }}%</span>
                                </div>
                            </td>
                            <td class="text-end staff-stat-strong text-dark">
                                {{ currency($row->total_revenue) }}
                            </td>
                            <td style="padding-right: 1.25rem;">
                                @if($row->last_activity_at)
                                    <div class="small text-dark">{{ $row->last_activity_at->format('d/m/Y à H:i') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $row->last_activity_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-muted small">Aucune</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                Aucune donnée d'attribution enregistrée sur cette période.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cartes Mobile (< 768px) -->
    <div class="d-block d-md-none">
        @forelse($rows as $row)
            @php
                $webFilterParams = array_filter([
                    'staff_id' => $row->user_id,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]);
                $posFilterParams = array_filter([
                    'staff_id' => $row->user_id,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ]);
            @endphp
            <div class="staff-mobile-card mb-2.5">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2.5">
                    <div class="d-flex align-items-center gap-2.5 min-w-0">
                        <div class="order-table-avatar flex-shrink-0">
                            {{ strtoupper(substr($row->name ?: 'U', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="staff-stat-strong text-dark text-truncate">{{ $row->name }}</div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                {{ $row->role_label }}
                                @if($row->last_activity_at)
                                    &bull; {{ $row->last_activity_at->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="staff-stat-strong text-danger" style="font-size: 0.98rem;">{{ currency($row->total_revenue) }}</div>
                        <div class="text-muted" style="font-size: 0.68rem;">{{ $row->delivery_rate }}% livrées</div>
                    </div>
                </div>

                <div class="row g-2 mb-2.5">
                    <div class="col-6">
                        <div class="staff-metric-tile">
                            <div class="text-muted" style="font-size: 0.66rem; font-weight: 700;">SITE CONFIRMÉES</div>
                            <div class="staff-stat-strong text-success fs-5">{{ number_format($row->web_confirmed, 0, ',', ' ') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="staff-metric-tile">
                            <div class="text-muted" style="font-size: 0.66rem; font-weight: 700;">POS CRÉÉES</div>
                            <div class="staff-stat-strong text-primary fs-5">{{ number_format($row->pos_created, 0, ',', ' ') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="staff-metric-tile">
                            <div class="text-muted" style="font-size: 0.66rem; font-weight: 700;">ANNULÉES</div>
                            <div class="staff-stat-strong text-danger">{{ number_format($row->web_cancelled, 0, ',', ' ') }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="staff-metric-tile">
                            <div class="text-muted" style="font-size: 0.66rem; font-weight: 700;">TOTAL GÉRÉES</div>
                            <div class="staff-stat-strong text-dark">{{ number_format($row->total_handled, 0, ',', ' ') }}</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 pt-2 border-top">
                    <a href="{{ route('orders.index', $webFilterParams) }}" class="btn btn-sm btn-outline-success flex-fill fw-semibold">
                        <i class="fas fa-globe me-1"></i>Site Web ({{ $row->web_confirmed }})
                    </a>
                    <a href="{{ route('orders.pos', $posFilterParams) }}" class="btn btn-sm btn-outline-primary flex-fill fw-semibold">
                        <i class="fas fa-cash-register me-1"></i>POS ({{ $row->pos_created }})
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                Aucune donnée d'attribution enregistrée sur cette période.
            </div>
        @endforelse
    </div>
</div>
@endsection
