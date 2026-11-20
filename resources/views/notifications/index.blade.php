@extends('layouts.app')

@section('title', 'Centre de Notifications & Rappels')

@push('styles')
<style>
    .notif-center-shell {
        max-width: 1400px;
        margin: 0 auto;
    }

    .notif-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    @media (max-width: 1199.98px) {
        .notif-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .notif-kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem;
        }
    }

    .notif-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .notif-kpi-card:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .notif-kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .notif-filter-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 1.25rem;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .notif-state-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .notif-state-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.16s ease;
    }

    .notif-state-tab:hover {
        border-color: #94a3b8;
        color: #0f172a;
        background: #ffffff;
    }

    .notif-state-tab.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .notif-cat-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.16s ease;
    }

    .notif-cat-chip:hover {
        border-color: #dc2626;
        color: #dc2626;
    }

    .notif-cat-chip.active {
        background: #fef2f2;
        border-color: #dc2626;
        color: #dc2626;
    }

    .notif-alert-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #0284c7;
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 10px;
        transition: all 0.18s ease;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
    }

    .notif-alert-card.is-unread {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-color: #cbd5e1;
    }

    .notif-alert-card.prio-critical {
        border-left-color: #dc2626;
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    }

    .notif-alert-card.prio-high {
        border-left-color: #d97706;
    }

    .notif-alert-card.prio-medium {
        border-left-color: #0284c7;
    }

    .notif-alert-card.prio-low {
        border-left-color: #64748b;
    }

    .notif-alert-card:hover {
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }

    .notif-card-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .notif-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .notif-action-btn:hover {
        border-color: #0f172a;
        background: #f8fafc;
        color: #0f172a;
    }

    .notif-action-btn.is-cta {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .notif-action-btn.is-cta:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
    }

    .notif-action-btn.is-success {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #15803d;
    }

    .notif-action-btn.is-success:hover {
        background: #16a34a;
        border-color: #16a34a;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
@php
    $counts = $feed['counts'];
    $kpis = $feed['kpis'];
    $items = $feed['items'];
    $categoryLabels = \App\Services\AdminNotificationService::CATEGORY_LABELS;
@endphp

<div class="notif-center-shell">
    <!-- Brand Header -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-bell"></i>
            </div>
            <div>
                <h1 class="brand-title mb-0">Centre de Notifications & Rappels</h1>
                <p class="brand-subtitle mb-0 mt-1">
                    Échéances crédits clients, commandes en attente, charges fixes, alertes stock et rappels personnels
                </p>
            </div>
        </div>
        <div class="brand-header-actions">
            <button type="button" class="btn-brand-primary" onclick="window.WinaNotifications?.openReminderModal()">
                <i class="fas fa-plus"></i> <span>Nouveau Rappel</span>
            </button>
            <button type="button" class="btn-brand-outline" onclick="notifCenterMarkAllRead()">
                <i class="fas fa-check-double text-success"></i> <span>Tout marquer lu</span>
            </button>
            <a href="{{ route('notifications.index', ['view' => $view, 'category' => $category]) }}" class="btn-brand-outline" title="Actualiser">
                <i class="fas fa-rotate-right"></i> <span>Actualiser</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="notif-kpi-grid">
        <a href="{{ route('notifications.index', ['view' => 'active']) }}" class="notif-kpi-card">
            <div class="notif-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem;">Urgences & Échéances</div>
                <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ $kpis['critical_count'] }}</div>
                <div class="text-danger small mt-1" style="font-size: 0.72rem;">Action prioritaire requise</div>
            </div>
        </a>

        <a href="{{ route('notifications.index', ['view' => 'active', 'category' => 'credit']) }}" class="notif-kpi-card">
            <div class="notif-kpi-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem;">Crédits & Impayés</div>
                <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ $kpis['credit_deadlines_count'] }}</div>
                <div class="text-muted small mt-1 text-truncate" style="font-size: 0.72rem;">
                    Encours : <strong>{{ currency($kpis['credit_total_amount']) }}</strong>
                </div>
            </div>
        </a>

        <a href="{{ route('notifications.index', ['view' => 'active', 'category' => 'orders']) }}" class="notif-kpi-card">
            <div class="notif-kpi-icon" style="background: #eff6ff; color: #0284c7;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem;">Commandes en Attente</div>
                <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ $kpis['pending_orders_count'] }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.72rem;">À confirmer / expédier</div>
            </div>
        </a>

        <a href="{{ route('notifications.index', ['view' => 'reminders']) }}" class="notif-kpi-card">
            <div class="notif-kpi-icon" style="background: #f0fdf4; color: #16a34a;">
                <i class="fas fa-list-check"></i>
            </div>
            <div class="min-w-0">
                <div class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem;">Stock, Charges & Rappels</div>
                <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ $kpis['other_alerts_count'] }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.72rem;">Dont {{ $counts['reminders'] }} rappel(s) perso.</div>
            </div>
        </a>
    </div>

    <!-- Filter Panel -->
    <div class="notif-filter-panel">
        <div class="notif-state-tabs">
            <a href="{{ route('notifications.index', ['view' => 'active', 'category' => $category]) }}" class="notif-state-tab {{ $view === 'active' ? 'active' : '' }}">
                <i class="fas fa-bolt"></i>
                <span>Actives</span>
                <span class="badge rounded-pill {{ $view === 'active' ? 'bg-danger text-white' : 'bg-light text-dark border' }}">{{ $counts['total_active'] }}</span>
            </a>
            <a href="{{ route('notifications.index', ['view' => 'snoozed']) }}" class="notif-state-tab {{ $view === 'snoozed' ? 'active' : '' }}">
                <i class="far fa-clock"></i>
                <span>Reportées (Snooze)</span>
                <span class="badge rounded-pill {{ $view === 'snoozed' ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">{{ $counts['snoozed'] }}</span>
            </a>
            <a href="{{ route('notifications.index', ['view' => 'dismissed']) }}" class="notif-state-tab {{ $view === 'dismissed' ? 'active' : '' }}">
                <i class="far fa-eye-slash"></i>
                <span>Ignorées</span>
                <span class="badge rounded-pill bg-light text-dark border">{{ $counts['dismissed'] }}</span>
            </a>
            <a href="{{ route('notifications.index', ['view' => 'reminders']) }}" class="notif-state-tab {{ $view === 'reminders' ? 'active' : '' }}">
                <i class="fas fa-thumbtack"></i>
                <span>Mes Rappels</span>
                <span class="badge rounded-pill {{ $view === 'reminders' ? 'bg-danger text-white' : 'bg-light text-dark border' }}">{{ $counts['reminders'] }}</span>
            </a>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            @if($view === 'active')
                <div class="d-flex flex-wrap align-items-center gap-1.5">
                    <a href="{{ route('notifications.index', ['view' => 'active', 'category' => 'all']) }}" class="notif-cat-chip {{ $category === 'all' ? 'active' : '' }}">
                        Toutes ({{ $counts['total_active'] }})
                    </a>
                    @foreach($categoryLabels as $catKey => $catLabel)
                        @php $catCount = $counts['by_category'][$catKey] ?? 0; @endphp
                        <a href="{{ route('notifications.index', ['view' => 'active', 'category' => $catKey]) }}" class="notif-cat-chip {{ $category === $catKey ? 'active' : '' }}">
                            <span>{{ $catLabel }}</span>
                            @if($catCount > 0)
                                <span class="badge rounded-pill bg-light text-dark border" style="font-size: 0.65rem;">{{ $catCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-muted small fw-semibold">
                    @if($view === 'snoozed')
                        Rappels temporairement reportés (réapparaissent automatiquement à l'heure prévue)
                    @elseif($view === 'dismissed')
                        Alertes ignorées (vous pouvez les restaurer à tout moment)
                    @else
                        Vos rappels personnels planifiés
                    @endif
                </div>
            @endif

            <div class="input-group input-group-sm" style="max-width: 280px;">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-search"></i></span>
                <input type="text"
                       id="notifCenterSearch"
                       class="form-control border-start-0"
                       placeholder="Filtrer (client, n° commande, titre...)"
                       oninput="filterNotifCards(this.value)">
            </div>
        </div>
    </div>

    <!-- Notification List -->
    <div id="notifCenterList">
        @forelse($items as $item)
            <div class="notif-alert-card prio-{{ $item['priority'] }} {{ $item['is_read'] ? '' : 'is-unread' }}"
                 data-alert-key="{{ $item['key'] }}"
                 data-search-text="{{ strtolower($item['title'] . ' ' . $item['message'] . ' ' . ($item['meta'] ?? '') . ' ' . ($item['due_label'] ?? '')) }}">
                <div class="d-flex align-items-start gap-3">
                    <div class="notif-card-icon" style="background: {{ $item['color'] }}18; color: {{ $item['color'] }};">
                        <i class="{{ $item['icon'] }}"></i>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 min-w-0">
                                @if(!$item['is_read'] && $view === 'active')
                                    <span class="rounded-circle bg-danger flex-shrink-0" style="width: 8px; height: 8px;" title="Non lu"></span>
                                @endif
                                <span class="fw-bold text-dark" style="font-size: 0.94rem;">{{ $item['title'] }}</span>
                                <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.68rem;">{{ $item['category_label'] }}</span>
                                @if($item['priority'] === 'critical')
                                    <span class="badge bg-danger text-white rounded-pill" style="font-size: 0.66rem;">URGENT</span>
                                @elseif($item['priority'] === 'high')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill" style="font-size: 0.66rem;">Prioritaire</span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                @if(!empty($item['due_label']))
                                    <span class="badge {{ $item['priority'] === 'critical' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-dark border' }} rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="far fa-calendar-clock me-1"></i>{{ $item['due_label'] }}
                                    </span>
                                @endif
                                @if(!empty($item['meta']))
                                    <span class="badge bg-dark-subtle text-dark rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                        {{ $item['meta'] }}
                                    </span>
                                @endif
                                @if(!empty($item['snoozed_until_label']))
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        Jusqu'au {{ $item['snoozed_until_label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p class="text-secondary mb-2" style="font-size: 0.84rem; line-height: 1.45;">
                            {{ $item['message'] }}
                            @if(!empty($item['created_at_human']))
                                <span class="text-muted ms-1" style="font-size: 0.75rem;">&bull; {{ $item['created_at_human'] }}</span>
                            @endif
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            @if(!empty($item['url']))
                                <a href="{{ $item['url'] }}"
                                   class="notif-action-btn is-cta"
                                   onclick="window.WinaNotifications?.openAlert('{{ $item['key'] }}')">
                                    <span>{{ $item['action_label'] ?? 'Consulter' }}</span>
                                    <i class="fas fa-arrow-right" style="font-size: 0.68rem;"></i>
                                </a>
                            @endif

                            @if($item['quick_action'] === 'complete_reminder' && $item['reminder_id'])
                                <button type="button" class="notif-action-btn is-success" onclick="notifCenterAction(this, () => window.WinaNotifications.completeReminder({{ $item['reminder_id'] }}, true), true)">
                                    <i class="fas fa-check"></i><span>Terminer</span>
                                </button>
                                <button type="button" class="notif-action-btn text-danger" onclick="notifCenterAction(this, () => window.WinaNotifications.destroyReminder({{ $item['reminder_id'] }}), true)">
                                    <i class="fas fa-trash"></i><span>Supprimer</span>
                                </button>
                            @elseif($item['quick_action'] === 'mark_contact_read' && $item['target_id'])
                                <button type="button" class="notif-action-btn is-success" onclick="notifCenterAction(this, () => window.WinaNotifications.markContactRead({{ $item['target_id'] }}, '{{ $item['key'] }}'), true)">
                                    <i class="fas fa-envelope-open"></i><span>Marquer message lu</span>
                                </button>
                            @elseif($item['quick_action'] === 'ack_stock_alert' && $item['target_id'])
                                <button type="button" class="notif-action-btn is-success" onclick="notifCenterAction(this, () => window.WinaNotifications.ackStockAlert({{ $item['target_id'] }}, '{{ $item['key'] }}'), true)">
                                    <i class="fas fa-check"></i><span>Acquitter alerte</span>
                                </button>
                            @endif

                            @if($view === 'active')
                                @if($item['is_read'])
                                    <button type="button" class="notif-action-btn" onclick="notifCenterToggleRead(this, '{{ $item['key'] }}', false)">
                                        <i class="far fa-envelope"></i><span>Marquer non lu</span>
                                    </button>
                                @else
                                    <button type="button" class="notif-action-btn" onclick="notifCenterToggleRead(this, '{{ $item['key'] }}', true)">
                                        <i class="fas fa-check"></i><span>Marquer lu</span>
                                    </button>
                                @endif

                                <div class="dropdown d-inline-block">
                                    <button type="button" class="notif-action-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="far fa-clock"></i><span>Reporter</span>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm border rounded-3" style="font-size: 0.8rem;">
                                        <li><button type="button" class="dropdown-item" onclick="notifCenterAction(this, () => window.WinaNotifications.snooze('{{ $item['key'] }}', '2h'), true)">Dans 2 heures</button></li>
                                        <li><button type="button" class="dropdown-item" onclick="notifCenterAction(this, () => window.WinaNotifications.snooze('{{ $item['key'] }}', 'tomorrow'), true)">Demain à 09h00</button></li>
                                        <li><button type="button" class="dropdown-item" onclick="notifCenterAction(this, () => window.WinaNotifications.snooze('{{ $item['key'] }}', '3d'), true)">Dans 3 jours</button></li>
                                        <li><button type="button" class="dropdown-item" onclick="notifCenterAction(this, () => window.WinaNotifications.snooze('{{ $item['key'] }}', '7d'), true)">Dans 7 jours</button></li>
                                    </ul>
                                </div>

                                <button type="button" class="notif-action-btn text-muted" onclick="notifCenterAction(this, () => window.WinaNotifications.dismiss('{{ $item['key'] }}'), true)">
                                    <i class="far fa-eye-slash"></i><span>Ignorer</span>
                                </button>
                            @elseif(in_array($view, ['snoozed', 'dismissed'], true))
                                <button type="button" class="notif-action-btn is-success" onclick="notifCenterAction(this, () => window.WinaNotifications.restore('{{ $item['key'] }}'), true)">
                                    <i class="fas fa-rotate-left"></i><span>Remettre en actif</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="brand-table-card text-center py-5 px-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                    <i class="fas fa-check-double"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Aucune notification ici</h5>
                <p class="text-muted small mb-3">
                    @if($view === 'active')
                        Toutes vos alertes et échéances dans cette catégorie sont traitées.
                    @elseif($view === 'reminders')
                        Créez un rappel personnel pour suivre une relance client, une commande ou une facture.
                    @else
                        Aucun élément dans cet onglet.
                    @endif
                </p>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-4 fw-semibold" onclick="window.WinaNotifications?.openReminderModal()">
                    <i class="fas fa-plus me-1"></i> Créer un rappel
                </button>
            </div>
        @endforelse
    </div>

    @if($view === 'reminders' && $completedReminders->isNotEmpty())
        <div class="brand-table-card mt-4 p-3 p-md-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-check-circle text-success"></i>
                <span>Rappels terminés récemment</span>
            </h6>
            <div class="d-flex flex-column gap-2">
                @foreach($completedReminders as $done)
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-2.5 rounded-3 bg-light border">
                        <div class="min-w-0">
                            <div class="fw-semibold text-muted text-decoration-line-through small">{{ $done->title }}</div>
                            @if($done->completed_at)
                                <div class="text-muted" style="font-size: 0.7rem;">Terminé {{ $done->completed_at->diffForHumans() }}</div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <button type="button" class="notif-action-btn" onclick="notifCenterAction(this, () => window.WinaNotifications.completeReminder({{ $done->id }}, false), false, () => window.location.reload())">
                                <i class="fas fa-rotate-left"></i><span>Réouvrir</span>
                            </button>
                            <button type="button" class="notif-action-btn text-danger" onclick="notifCenterAction(this, () => window.WinaNotifications.destroyReminder({{ $done->id }}), false, () => window.location.reload())">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function filterNotifCards(raw) {
        const q = String(raw || '').trim().toLowerCase();
        document.querySelectorAll('#notifCenterList .notif-alert-card').forEach(card => {
            const hay = card.getAttribute('data-search-text') || '';
            card.style.display = (!q || hay.includes(q)) ? '' : 'none';
        });
    }

    async function notifCenterAction(btn, asyncFn, removeCard = false, afterFn = null) {
        if (!window.WinaNotifications || typeof asyncFn !== 'function') return;
        const card = btn ? btn.closest('.notif-alert-card') : null;
        if (btn) btn.disabled = true;
        try {
            await asyncFn();
            if (removeCard && card) {
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 180);
            }
            if (typeof afterFn === 'function') afterFn();
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    async function notifCenterToggleRead(btn, key, markRead) {
        if (!window.WinaNotifications) return;
        const card = btn ? btn.closest('.notif-alert-card') : null;
        if (btn) btn.disabled = true;
        try {
            await window.WinaNotifications.markRead(key, markRead);
            if (card) {
                card.classList.toggle('is-unread', !markRead);
            }
            window.location.reload();
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    async function notifCenterMarkAllRead() {
        if (!window.WinaNotifications) return;
        await window.WinaNotifications.markAllRead();
        window.location.reload();
    }
</script>
@endpush
