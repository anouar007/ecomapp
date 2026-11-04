@extends('layouts.app')

@section('title', 'Gestion des commandes')

@section('content')
    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des commandes</h1>
                <p class="brand-subtitle d-flex align-items-center gap-2 flex-wrap mb-0">
                    <span>Suivez et gérez vos commandes selon leur statut de livraison</span>
                    <span class="badge bg-light text-dark border px-2 py-1"><i class="fas fa-receipt text-primary me-1"></i> <strong id="kpi-total-orders">{{ number_format($stats['total'] ?? $orders->total(), 0, ',', ' ') }}</strong> total</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-coins me-1"></i> <strong id="kpi-revenue-orders">{{ currency($stats['revenue'] ?? 0) }}</strong> encaissés</span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions">
            @if(request('status') || request('payment_status') || request('search'))
                <a href="{{ route('orders.index') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-rotate-left"></i> <span>Toutes ({{ number_format($stats['total'] ?? 0, 0, ',', ' ') }})</span>
                </a>
            @endif
            <a href="{{ route('export.orders') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            <a href="{{ route('orders.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
                <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouvelle commande</span><span class="d-sm-none">Créer</span>
            </a>
        </div>
    </div>

    <!-- 6 Delivery Status Cards (Interactive Filters with All Orders) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-6 g-2.5 g-md-3 mb-4">
        <!-- 0. Toutes les commandes (All orders) -->
        <div class="col mb-2">
            <a href="{{ route('orders.index', request()->except(['status', 'page'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ !request('status') ? 'active-filter' : '' }}"
               title="Voir toutes les commandes">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label text-white">Total</div>
                        <div class="stat-kpi-value text-white" id="kpi-all-orders-card">{{ number_format($stats['total'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-light"><i class="fas fa-boxes-stacked text-white me-1"></i></div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #f1f5f9; color: #1e293b;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 1. En attente (pending) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'pending' ? route('orders.index', request()->except(['status', 'page'])) : route('orders.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ request('status') === 'pending' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes en attente">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label" style="color: #d97706;">En attente</div>
                        <div class="stat-kpi-value" id="kpi-pending-orders" style="color: #d97706;">{{ number_format($stats['pending'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-clock me-1" style="color: #d97706;"></i>À traiter</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. En cours (processing) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'processing' ? route('orders.index', request()->except(['status', 'page'])) : route('orders.index', array_merge(request()->except('page'), ['status' => 'processing'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ request('status') === 'processing' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes en cours de préparation">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label" style="color: #0284c7;">En cours</div>
                        <div class="stat-kpi-value" id="kpi-processing-orders" style="color: #0284c7;">{{ number_format($stats['processing'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-box-archive me-1" style="color: #0284c7;"></i>En préparation</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Expédiée (shipped) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'shipped' ? route('orders.index', request()->except(['status', 'page'])) : route('orders.index', array_merge(request()->except('page'), ['status' => 'shipped'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ request('status') === 'shipped' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes expédiées en acheminement">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label" style="color: #6366f1;">Expédiée</div>
                        <div class="stat-kpi-value" id="kpi-shipped-orders" style="color: #6366f1;">{{ number_format($stats['shipped'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-truck-fast me-1" style="color: #6366f1;"></i>En livraison</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #ede9fe; color: #6366f1;">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Livrée (delivered) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'delivered' ? route('orders.index', request()->except(['status', 'page'])) : route('orders.index', array_merge(request()->except('page'), ['status' => 'delivered'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ request('status') === 'delivered' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes livrées avec succès">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label text-success">Livrée</div>
                        <div class="stat-kpi-value text-success" id="kpi-delivered-orders">{{ number_format($stats['delivered'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-circle-check text-success me-1"></i>Finalisée</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #d1fae5; color: #059669;">
                        <i class="fas fa-truck-ramp-box"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 5. Annulée (cancelled) -->
        <div class="col mb-2">
            <a href="{{ request('status') === 'cancelled' ? route('orders.index', request()->except(['status', 'page'])) : route('orders.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" 
               class="stat-kpi-card text-decoration-none h-100 {{ request('status') === 'cancelled' ? 'active-filter' : '' }}"
               title="Filtrer : Commandes annulées ou rejetées">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label text-danger">Annulée</div>
                        <div class="stat-kpi-value text-danger" id="kpi-cancelled-orders">{{ number_format($stats['cancelled'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-ban text-danger me-1"></i>Non aboutie</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-circle-xmark"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('orders.index') }}" id="orderFilterForm">
            <!-- Row 1 : Champ Recherche + Bouton Toggle Mobile + Actions Desktop -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 170px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="N° commande, client..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $hasDateFilter = request('date_preset') || request('date_from') || request('date_to') || request('date');
                    $hasActiveFilters = request('status') || request('payment_status') || $hasDateFilter;
                    $activeFilterCount = collect(['status', 'payment_status', 'date_preset', 'date_from', 'date_to', 'date'])->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#orderFiltersCollapse" 
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Actions Desktop (Compteur de résultats) (d-none d-md-flex) -->
                <div class="ms-md-auto d-none d-md-flex align-items-center gap-2">
                    @if(request('search') || $hasActiveFilters)
                        <span class="badge bg-light text-secondary border px-2.5 py-2">
                            {{ $orders->total() }} résultat(s)
                        </span>
                    @endif
                    <a href="{{ route('export.orders', request()->all()) }}" class="btn-brand-outline">
                        <i class="fas fa-file-csv me-1.5" style="color: var(--success-color)"></i>Exporter CSV
                    </a>
                </div>
            </div>

            <!-- Row 2 : Filtres Déroulants Uniques (Toujours visibles sur Desktop, repliables sur Mobile) -->
            <div class="collapse d-md-block {{ $hasActiveFilters ? 'show' : '' }} mt-3 pt-3 pt-md-2 border-top border-top-md-0" id="orderFiltersCollapse">
                <div class="row g-2 align-items-center">
                    <!-- Statut de livraison -->
                    <div class="col-6 col-md-auto">
                        <select name="status" class="form-select form-select-sm" style="min-width: 160px;">
                            <option value="">Tous les statuts</option>
                            @php
                                $statusNames = [
                                    'pending' => '⏳ En attente',
                                    'processing' => '⚙️ En cours',
                                    'shipped' => '🚚 Expédiée',
                                    'delivered' => '✅ Livrée',
                                    'cancelled' => '❌ Annulée'
                                ];
                            @endphp
                            @foreach($statusNames as $stKey => $stLabel)
                                <option value="{{ $stKey }}" {{ request('status') == $stKey ? 'selected' : '' }}>{{ $stLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Statut de paiement -->
                    <div class="col-6 col-md-auto">
                        <select name="payment_status" class="form-select form-select-sm" style="min-width: 155px;">
                            <option value="">Tous les paiements</option>
                            @php
                                $paymentStatusNames = [
                                    'pending' => '⏳ En attente',
                                    'paid' => '💳 Payé',
                                    'failed' => '⚠️ Échoué',
                                    'refunded' => '↩️ Remboursé'
                                ];
                            @endphp
                            @foreach($paymentStatusNames as $pstKey => $pstLabel)
                                <option value="{{ $pstKey }}" {{ request('payment_status') == $pstKey ? 'selected' : '' }}>{{ $pstLabel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre de Date (Preset) -->
                    <div class="col-12 col-md-auto">
                        <select name="date_preset" id="orderDatePreset" class="form-select form-select-sm" style="min-width: 165px;">
                            <option value="">📅 Toutes les dates</option>
                            <option value="today" {{ request('date_preset') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="yesterday" {{ request('date_preset') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                            <option value="this_week" {{ request('date_preset') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="this_month" {{ request('date_preset') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                            <option value="last_month" {{ request('date_preset') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                            <option value="custom" {{ request('date_preset') == 'custom' || (!request('date_preset') && (request('date_from') || request('date_to'))) ? 'selected' : '' }}>Période personnalisée...</option>
                        </select>
                    </div>

                    <!-- Date de début (Du) -->
                    <div class="col-6 col-md-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2" style="font-size: 0.72rem;">Du</span>
                            <input type="date" name="date_from" id="orderDateFrom" class="form-control form-control-sm" 
                                   value="{{ request('date_from', request('date_preset') ? ($stats['date_from'] ?? '') : '') }}" 
                                   title="Date de début">
                        </div>
                    </div>

                    <!-- Date de fin (Au) -->
                    <div class="col-6 col-md-auto">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted px-2" style="font-size: 0.72rem;">Au</span>
                            <input type="date" name="date_to" id="orderDateTo" class="form-control form-control-sm" 
                                   value="{{ request('date_to', request('date_preset') ? ($stats['date_to'] ?? '') : '') }}" 
                                   title="Date de fin">
                        </div>
                    </div>

                    <!-- Boutons Filtrer / Réinitialiser -->
                    <div class="col-12 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn-brand-primary btn-sm flex-grow-1 flex-md-grow-0 justify-content-center" title="Appliquer les filtres">
                            <i class="fas fa-filter me-1"></i> <span class="d-none d-md-inline">Filtrer</span><span class="d-md-none">Appliquer les filtres</span>
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn-brand-light btn-sm flex-shrink-0" title="Réinitialiser les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>

                    <!-- Bouton Exporter sur Mobile (d-md-none) -->
                    <div class="col-12 d-md-none mt-2 pt-2 border-top">
                        <a href="{{ route('export.orders', request()->all()) }}" class="btn btn-sm btn-outline-secondary w-100 justify-content-center">
                            <i class="fas fa-file-csv me-1 text-success"></i> Exporter les commandes (CSV)
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges with Quick Removal Pills -->
    @if(request('search') || $hasActiveFilters)
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3 px-1">
            <span class="text-muted small fw-semibold">
                <i class="fas fa-filter text-primary me-1"></i> Filtres appliqués :
            </span>

            @if(request('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Recherche : "{{ request('search') }}" <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('status'))
                @php
                    $statusBadgeLabels = [
                        'pending' => 'En attente',
                        'processing' => 'En cours',
                        'shipped' => 'Expédiée',
                        'delivered' => 'Livrée',
                        'cancelled' => 'Annulée'
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Livraison : {{ $statusBadgeLabels[request('status')] ?? request('status') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('payment_status'))
                @php
                    $payBadgeLabels = [
                        'pending' => 'En attente',
                        'paid' => 'Payé',
                        'failed' => 'Échoué',
                        'refunded' => 'Remboursé'
                    ];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['payment_status' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Paiement : {{ $payBadgeLabels[request('payment_status')] ?? request('payment_status') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if($hasDateFilter)
                @php
                    $presetLabels = [
                        'today' => "Aujourd'hui",
                        'yesterday' => 'Hier',
                        'this_week' => 'Cette semaine',
                        'this_month' => 'Ce mois-ci',
                        'last_month' => 'Mois dernier',
                        'custom' => 'Personnalisé'
                    ];
                    $presetName = $presetLabels[request('date_preset')] ?? '';
                    $dateRangeFormatted = '';
                    if (!empty($stats['date_from']) && !empty($stats['date_to'])) {
                        if ($stats['date_from'] === $stats['date_to']) {
                            $dateRangeFormatted = \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y');
                        } else {
                            $dateRangeFormatted = \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y') . ' → ' . \Carbon\Carbon::parse($stats['date_to'])->format('d/m/Y');
                        }
                    } elseif (!empty($stats['date_from'])) {
                        $dateRangeFormatted = 'Depuis le ' . \Carbon\Carbon::parse($stats['date_from'])->format('d/m/Y');
                    } elseif (!empty($stats['date_to'])) {
                        $dateRangeFormatted = "Jusqu'au " . \Carbon\Carbon::parse($stats['date_to'])->format('d/m/Y');
                    }
                    $dateBadgeTitle = $presetName ? ($presetName . ($dateRangeFormatted ? " ({$dateRangeFormatted})" : '')) : $dateRangeFormatted;
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['date_preset' => null, 'date_from' => null, 'date_to' => null, 'date' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer le filtre de date">
                    <i class="far fa-calendar-alt text-primary me-0.5"></i> Date : {{ $dateBadgeTitle }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            <a href="{{ route('orders.index') }}" class="small text-danger fw-semibold text-decoration-none ms-1">
                Effacer tout
            </a>
        </div>
    @endif

    <!-- Mobile Orders Container (Touch First, d-block d-md-none) -->
    <div class="mobile-orders-container d-block d-md-none">
        <div class="d-flex align-items-center justify-content-between px-1 mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark" style="font-size: 0.92rem; letter-spacing: -0.01em;">
                    <i class="fas fa-receipt text-danger me-1.5 opacity-75"></i>Commandes récentes
                </span>
                <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                    {{ $orders->total() }}
                </span>
            </div>
            @if(request('status') || request('payment_status') || request('search'))
                <a href="{{ route('orders.index') }}" class="small text-danger fw-semibold text-decoration-none" style="font-size: 0.75rem;">
                    <i class="fas fa-times me-0.5"></i>Effacer filtres
                </a>
            @endif
        </div>

        @forelse($orders as $order)
            @include('orders.partials.mobile-order-card', ['order' => $order])
        @empty
            <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px;">
                    <i class="fas fa-shopping-bag text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucune commande trouvée</h6>
                <p class="text-muted small mb-0">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                @if(request('search') || $hasActiveFilters)
                    <a href="{{ route('orders.index') }}" class="btn-brand-primary btn-sm mt-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endforelse

        <!-- Mobile Pagination Footer -->
        @if($orders->hasPages())
            <div class="bg-white p-3 rounded-4 border shadow-sm mt-3 mb-4">
                <div class="text-muted small text-center mb-2">
                    Affichage de <span class="fw-semibold text-dark">{{ $orders->firstItem() ?? 0 }}</span> à <span class="fw-semibold text-dark">{{ $orders->lastItem() ?? 0 }}</span> sur <span class="fw-semibold text-dark">{{ $orders->total() }}</span>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Tableau des commandes Desktop (d-none d-md-block) -->
    <div class="brand-table-card orders-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="brand-table align-middle" style="width: 100%; min-width: 1020px;">
                <thead>
                    <tr>
                        <th style="min-width: 130px; padding-left: 1.25rem;">N° Commande</th>
                        <th style="min-width: 220px;">Client</th>
                        <th class="text-center" style="min-width: 80px;">Articles</th>
                        <th class="text-end" style="min-width: 120px;">Total TTC</th>
                        <th class="text-center" style="min-width: 130px;">Livraison</th>
                        <th class="text-center d-none" style="min-width: 130px;">Paiement</th>
                        <th style="min-width: 100px;">Date</th>
                        <th class="text-end" style="min-width: 110px; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="padding-left: 1.25rem;">
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="fw-bold fs-6 {{ $order->is_blacklisted ? 'text-danger' : 'text-dark' }}" style="{{ $order->is_blacklisted ? 'color: #dc2626 !important;' : '' }}">{{ $order->customer_name ?: 'Client invité' }}</span>
                                @if($order->is_blacklisted)
                                    <span class="badge bg-danger text-white border border-danger-subtle fw-bold d-none" style="font-size: 0.68rem; padding: 2px 7px;" title="Client sur liste noire : {{ $order->blacklist_reason ?: 'Sans motif spécifié' }}">
                                        <i class="fas fa-ban me-1"></i>Liste Noire
                                    </span>
                                @endif
                                @php $tier = $order->customer_tier; @endphp
                                <span class="badge {{ $tier['badge_class'] }} d-none" style="font-size: 0.68rem; padding: 2px 6px;" title="{{ $tier['description'] }}">
                                    <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                                </span>
                            </div>
                            <div class="text-muted small d-flex align-items-center gap-2">
                                <span>{{ $order->customer_email ?: ($order->customer_phone ?: 'Aucun contact') }}</span>
                                @if($order->customer_record)
                                    <a href="{{ route('customers.show', $order->customer_record) }}" class="text-primary text-decoration-none" title="Consulter la fiche client et ses statistiques">
                                        <i class="fas fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-secondary px-3 py-1 border" style="border-radius: 6px;">
                                {{ $order->items->count() }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6 font-heading">
                            {{ $order->formatted_total }}
                        </td>
                        <td class="text-center">
                            @php
                                $stLower = strtolower($order->status);
                                $statusDot = match($stLower) {
                                    'delivered', 'livrée' => 'green',
                                    'shipped', 'expédiée' => 'teal',
                                    'processing', 'en cours' => 'blue',
                                    'cancelled', 'annulée' => 'red',
                                    default => 'amber'
                                };
                                $statusClass = match($stLower) {
                                    'delivered', 'livrée' => 'success',
                                    'cancelled', 'annulée' => 'danger',
                                    'shipped', 'expédiée' => 'info',
                                    'processing', 'en cours' => 'primary',
                                    default => 'warning'
                                };
                                $statusText = match($stLower) {
                                    'delivered', 'livrée' => 'Livrée',
                                    'shipped', 'expédiée' => 'Expédiée',
                                    'processing', 'en cours' => 'En cours',
                                    'pending' => 'En attente',
                                    'cancelled', 'annulée' => 'Annulée',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <div class="dropdown d-inline-block">
                                <button type="button" 
                                        class="brand-badge brand-badge-btn {{ $statusClass }} dropdown-toggle" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-auto-close="true"
                                        data-bs-boundary="viewport"
                                        data-bs-popper-config='{"strategy":"fixed"}'
                                        aria-expanded="false"
                                        id="dt-status-pill-{{ $order->id }}"
                                        title="Changer le statut de livraison">
                                    <span class="status-dot dot-{{ $statusDot }}" id="dt-status-dot-{{ $order->id }}"></span>
                                    <span id="dt-status-label-{{ $order->id }}">{{ $statusText }}</span>
                                    <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.6rem;"></i>
                                </button>
                                <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="dt-status-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $stLower === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'pending')">
                                            <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                                            @if($stLower === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ in_array($stLower, ['processing', 'en cours']) ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'processing')">
                                            <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                                            @if(in_array($stLower, ['processing', 'en cours'])) <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ in_array($stLower, ['shipped', 'expédiée']) ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'shipped')">
                                            <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                                            @if(in_array($stLower, ['shipped', 'expédiée'])) <i class="fas fa-check text-info small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ in_array($stLower, ['delivered', 'livrée']) ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'delivered')">
                                            <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                                            @if(in_array($stLower, ['delivered', 'livrée'])) <i class="fas fa-check text-success small"></i> @endif
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ in_array($stLower, ['cancelled', 'annulée']) ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'cancelled')">
                                            <span><span class="status-dot dot-red me-1.5"></span> Annulée</span>
                                            @if(in_array($stLower, ['cancelled', 'annulée'])) <i class="fas fa-check text-danger small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td class="text-center d-none">
                            @php
                                $payStatus = strtolower($order->payment_status);
                                $isPaid = in_array($payStatus, ['paid', 'payé']);
                                $pClass = match($payStatus) {
                                    'paid', 'payé' => 'paid',
                                    'failed', 'échoué' => 'failed',
                                    'refunded', 'remboursé' => 'refunded',
                                    default => 'pending'
                                };
                                $pText = match($payStatus) {
                                    'paid', 'payé' => 'Payé',
                                    'failed', 'échoué' => 'Échoué',
                                    'refunded', 'remboursé' => 'Remboursé',
                                    default => 'En attente'
                                };
                                $pIcon = match($payStatus) {
                                    'paid', 'payé' => 'fa-check-circle',
                                    'failed', 'échoué' => 'fa-exclamation-circle',
                                    'refunded', 'remboursé' => 'fa-undo',
                                    default => 'fa-clock'
                                };
                            @endphp
                            <div class="d-inline-block">
                                <span class="brand-badge pay-badge-{{ $pClass }} py-1 px-2.5" 
                                      id="dt-pay-pill-{{ $order->id }}"
                                      title="Statut de paiement : {{ $pText }}">
                                    <i class="fas {{ $pIcon }}" id="dt-pay-icon-{{ $order->id }}"></i>
                                    <span id="dt-pay-label-{{ $order->id }}">{{ $pText }}</span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end" style="padding-right: 1.25rem;">
                            <div class="action-buttons-group justify-content-end">
                                <a href="{{ route('orders.show', $order) }}" class="action-btn action-btn-view" title="Détails de la commande">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="action-btn action-btn-edit" title="Modifier la commande">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @if($order->invoice)
                                    <a href="{{ route('invoices.show', $order->invoice) }}" class="action-btn action-btn-success" title="Voir la facture">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                @endif
                                @if(in_array($order->status, ['pending', 'cancelled']))
                                    <form method="POST" 
                                          action="{{ route('orders.destroy', $order->id) }}" 
                                          class="m-0 p-0 d-inline"
                                          data-confirm-delete="true"
                                          data-item-type="order"
                                          data-item-name="Commande #{{ $order->order_number }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-delete" title="Supprimer la commande">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-shopping-bag text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucune commande trouvée</h5>
                                <p class="text-muted">Aucun enregistrement ne correspond à vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Affichage de {{ $orders->firstItem() ?? 0 }} à {{ $orders->lastItem() ?? 0 }} sur {{ $orders->total() }} commandes
            </span>
            <div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function copyOrderNumber(orderNum, btn) {
        if (!orderNum) return;
        const textToCopy = orderNum.toString().trim();
        
        const showSuccess = function() {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 1500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(showSuccess).catch(function() {
                fallbackCopy(textToCopy, showSuccess);
            });
        } else {
            fallbackCopy(textToCopy, showSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Copy failed:', e);
        }
        document.body.removeChild(temp);
    }

    /**
     * Helper to immediately close any dropdown for an order
     */
    function closeOrderDropdowns(orderId) {
        const toggles = [
            document.getElementById(`dt-status-pill-${orderId}`),
            document.getElementById(`dt-pay-pill-${orderId}`),
            document.getElementById(`mob-status-pill-${orderId}`),
            document.getElementById(`mob-pay-pill-${orderId}`)
        ].filter(Boolean);

        toggles.forEach(btn => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                const inst = bootstrap.Dropdown.getInstance(btn);
                if (inst) inst.hide();
            }
            btn.classList.remove('show');
            btn.setAttribute('aria-expanded', 'false');
            const parent = btn.closest('.dropdown');
            if (parent) {
                parent.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
            }
        });

        // Close any other open status dropdown menu immediately
        document.querySelectorAll('.status-dropdown-menu.show').forEach(m => {
            m.classList.remove('show');
            const toggle = m.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
            if (toggle) {
                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
                if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    const inst = bootstrap.Dropdown.getInstance(toggle);
                    if (inst) inst.hide();
                }
            }
        });
    }

    /**
     * Quick In-Place Status & Payment Status Updater
     */
    function quickUpdateOrder(orderId, field, value) {
        if (!orderId || !field || !value) return;

        // Auto close dropdown immediately on click
        closeOrderDropdowns(orderId);

        // Visual loading indicator on triggering pill (using pointerEvents instead of disabled)
        const dtPill = document.getElementById(field === 'status' ? `dt-status-pill-${orderId}` : `dt-pay-pill-${orderId}`);
        const mobPill = document.getElementById(field === 'status' ? `mob-status-pill-${orderId}` : `mob-pay-pill-${orderId}`);
        
        if (dtPill) {
            dtPill.style.opacity = '0.6';
            dtPill.style.pointerEvents = 'none';
        }
        if (mobPill) {
            mobPill.style.opacity = '0.6';
            mobPill.style.pointerEvents = 'none';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const urlParams = new URLSearchParams(window.location.search);
            const bodyPayload = {
                [field]: value,
                date_from: urlParams.get('date_from') || '{{ $stats['date_from'] ?? '' }}',
                date_to: urlParams.get('date_to') || '{{ $stats['date_to'] ?? '' }}',
                date_preset: urlParams.get('date_preset') || ''
            };

            fetch(`/orders/${orderId}/quick-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyPayload)
            })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.order) {
                // Update Order row and mobile card UI
                updateOrderUI(orderId, data.order);

                // Update KPI metrics counters live
                if (data.stats) {
                    updateKPIStats(data.stats);
                }

                // Modern Toast Notification
                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Mise à jour effectuée avec succès'
                    });
                }
            } else {
                throw new Error(data.message || 'Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Quick update error:', error);
            const msg = error.message || (error.errors ? Object.values(error.errors).flat().join('<br>') : 'Impossible de modifier le statut');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: msg,
                    confirmButtonColor: '#dc2626'
                });
            } else {
                alert(msg);
            }
        })
        .finally(() => {
            if (dtPill) {
                dtPill.style.opacity = '1';
                dtPill.style.pointerEvents = '';
            }
            if (mobPill) {
                mobPill.style.opacity = '1';
                mobPill.style.pointerEvents = '';
            }
        });
    }

    /**
     * Dynamically update badges, icons, labels, and dropdown states for both desktop and mobile
     */
    function updateOrderUI(orderId, order) {
        // --- Delivery Status ---
        const st = (order.status || '').toLowerCase();
        const stMap = {
            'pending': { label: 'En attente', dot: 'amber', badgeClass: 'warning', textClass: 'text-warning', mobBg: '#fffbeb', mobColor: '#b45309', mobBorder: '#fde68a' },
            'processing': { label: 'En cours', dot: 'blue', badgeClass: 'primary', textClass: 'text-primary', mobBg: '#eff6ff', mobColor: '#1d4ed8', mobBorder: '#bfdbfe' },
            'shipped': { label: 'Expédiée', dot: 'teal', badgeClass: 'info', textClass: 'text-info', mobBg: 'rgb(192 246 255 / 53%)', mobColor: '#0093ac', mobBorder: '#0dcaf0' },
            'delivered': { label: 'Livrée', dot: 'green', badgeClass: 'success', textClass: 'text-success', mobBg: '#ecfdf5', mobColor: '#047857', mobBorder: '#a7f3d0' },
            'cancelled': { label: 'Annulée', dot: 'red', badgeClass: 'danger', textClass: 'text-danger', mobBg: '#fef2f2', mobColor: '#b91c1c', mobBorder: '#fecaca' }
        };
        const sInfo = stMap[st] || { label: order.status_label || st, dot: 'amber', badgeClass: 'warning', textClass: 'text-warning', mobBg: '#fffbeb', mobColor: '#b45309', mobBorder: '#fde68a' };

        // Desktop Delivery Badge
        const dtStatusPill = document.getElementById(`dt-status-pill-${orderId}`);
        if (dtStatusPill) {
            dtStatusPill.className = `brand-badge brand-badge-btn ${sInfo.badgeClass} dropdown-toggle`;
            const dtLabel = document.getElementById(`dt-status-label-${orderId}`);
            if (dtLabel) dtLabel.textContent = sInfo.label;
            const dtDot = document.getElementById(`dt-status-dot-${orderId}`);
            if (dtDot) dtDot.className = `status-dot dot-${sInfo.dot}`;

            const dtMenu = dtStatusPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (dtMenu) {
                dtMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Mobile Delivery Badge
        const mobStatusPill = document.getElementById(`mob-status-pill-${orderId}`);
        if (mobStatusPill) {
            mobStatusPill.style.background = sInfo.mobBg;
            mobStatusPill.style.color = sInfo.mobColor;
            mobStatusPill.style.border = `1px solid ${sInfo.mobBorder}`;
            const mobLabel = document.getElementById(`mob-status-label-${orderId}`);
            if (mobLabel) mobLabel.textContent = sInfo.label;
            const mobDot = document.getElementById(`mob-status-dot-${orderId}`);
            if (mobDot) mobDot.className = `status-dot dot-${sInfo.dot}`;

            const mobMenu = mobStatusPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (mobMenu) {
                mobMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // --- Payment Status ---
        const pst = (order.payment_status || '').toLowerCase();
        const pstMap = {
            'paid': { label: 'Payé', icon: 'fa-check-circle', badgeClass: 'paid', textClass: 'text-success', mobClass: 'paid' },
            'pending': { label: 'En attente', icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' },
            'failed': { label: 'Échoué', icon: 'fa-exclamation-circle', badgeClass: 'failed', textClass: 'text-danger', mobClass: 'failed' },
            'refunded': { label: 'Remboursé', icon: 'fa-undo', badgeClass: 'refunded', textClass: 'text-secondary', mobClass: 'refunded' }
        };
        const pInfo = pstMap[pst] || { label: order.payment_status_label || pst, icon: 'fa-clock', badgeClass: 'pending', textClass: 'text-warning', mobClass: 'pending' };

        // Desktop Payment Badge (Static read-only pill)
        const dtPayPill = document.getElementById(`dt-pay-pill-${orderId}`);
        if (dtPayPill) {
            dtPayPill.className = `brand-badge pay-badge-${pInfo.badgeClass} py-1 px-2.5`;
            const dtPayLabel = document.getElementById(`dt-pay-label-${orderId}`);
            if (dtPayLabel) dtPayLabel.textContent = pInfo.label;
            const dtPayIcon = document.getElementById(`dt-pay-icon-${orderId}`);
            if (dtPayIcon) dtPayIcon.className = `fas ${pInfo.icon}`;
        }

        // Mobile Payment Pill (Static read-only pill)
        const mobPayPill = document.getElementById(`mob-pay-pill-${orderId}`);
        if (mobPayPill) {
            mobPayPill.className = `mobile-order-pay-pill ${pInfo.mobClass} border-0`;
            const mobPayLabel = document.getElementById(`mob-pay-label-${orderId}`);
            if (mobPayLabel) mobPayLabel.textContent = pInfo.label;
            const mobPayIcon = document.getElementById(`mob-pay-icon-${orderId}`);
            if (mobPayIcon) mobPayIcon.className = `fas ${pInfo.icon}`;
        }
    }

    /**
     * Update real-time KPI metrics for all 5 delivery statuses
     */
    function updateKPIStats(stats) {
        if (!stats) return;
        if (stats.total !== undefined) {
            const el = document.getElementById('kpi-total-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.total);
            const elCard = document.getElementById('kpi-all-orders-card');
            if (elCard) elCard.textContent = new Intl.NumberFormat('fr-FR').format(stats.total);
        }
        if (stats.pending !== undefined) {
            const el = document.getElementById('kpi-pending-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.pending);
        }
        if (stats.processing !== undefined) {
            const el = document.getElementById('kpi-processing-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.processing);
        }
        if (stats.shipped !== undefined) {
            const el = document.getElementById('kpi-shipped-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.shipped);
        }
        if (stats.delivered !== undefined) {
            const el = document.getElementById('kpi-delivered-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.delivered);
        }
        if (stats.cancelled !== undefined) {
            const el = document.getElementById('kpi-cancelled-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.cancelled);
        }
        if (stats.revenue_formatted !== undefined) {
            const el = document.getElementById('kpi-revenue-orders');
            if (el) el.textContent = stats.revenue_formatted;
        }
    }

    /**
     * Initialize dropdowns with fixed Popper strategy so they never get clipped by table bounds
     */
    function initStatusDropdowns() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
        const triggers = document.querySelectorAll('.brand-badge-btn[data-bs-toggle="dropdown"], .mobile-order-badge[data-bs-toggle="dropdown"], .mobile-order-pay-pill[data-bs-toggle="dropdown"]');
        triggers.forEach(el => {
            bootstrap.Dropdown.getOrCreateInstance(el, {
                autoClose: true,
                popperConfig: function(defaultBsPopperConfig) {
                    return {
                        ...defaultBsPopperConfig,
                        strategy: 'fixed',
                        modifiers: [
                            ...(defaultBsPopperConfig?.modifiers || []),
                            {
                                name: 'preventOverflow',
                                options: {
                                    boundary: 'viewport',
                                    padding: 8
                                }
                            },
                            {
                                name: 'flip',
                                options: {
                                    fallbackPlacements: ['top', 'bottom']
                                }
                            }
                        ]
                    };
                }
            });
        });
    }

    // Auto-close any status dropdown as soon as an item inside is clicked
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.status-dropdown-menu .dropdown-item');
        if (item) {
            const menu = item.closest('.dropdown-menu');
            if (menu) {
                menu.classList.remove('show');
                const toggle = menu.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    toggle.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                        const inst = bootstrap.Dropdown.getInstance(toggle);
                        if (inst) inst.hide();
                    }
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        initStatusDropdowns();

        // Auto-handle Date Preset Selection
        const datePresetSelect = document.getElementById('orderDatePreset');
        const fromInput = document.getElementById('orderDateFrom');
        const toInput = document.getElementById('orderDateTo');
        const form = document.getElementById('orderFilterForm');

        if (datePresetSelect && form) {
            datePresetSelect.addEventListener('change', function() {
                const val = this.value;
                const today = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const formatDate = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

                if (val === 'today') {
                    const todayStr = formatDate(today);
                    if (fromInput) fromInput.value = todayStr;
                    if (toInput) toInput.value = todayStr;
                    form.submit();
                } else if (val === 'yesterday') {
                    const y = new Date(today);
                    y.setDate(y.getDate() - 1);
                    const yStr = formatDate(y);
                    if (fromInput) fromInput.value = yStr;
                    if (toInput) toInput.value = yStr;
                    form.submit();
                } else if (val === 'this_week') {
                    const curr = new Date(today);
                    const day = curr.getDay();
                    const diff = curr.getDate() - (day === 0 ? 6 : day - 1); // Monday
                    const firstDay = new Date(curr.setDate(diff));
                    const lastDay = new Date(firstDay);
                    lastDay.setDate(lastDay.getDate() + 6);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === 'this_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === 'last_month') {
                    const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                    if (fromInput) fromInput.value = formatDate(firstDay);
                    if (toInput) toInput.value = formatDate(lastDay);
                    form.submit();
                } else if (val === '') {
                    if (fromInput) fromInput.value = '';
                    if (toInput) toInput.value = '';
                    form.submit();
                }
            });
        }
    });
</script>
@endpush
