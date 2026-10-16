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
                <p class="brand-subtitle">Suivez et gérez les commandes clients, l'état de livraison et les règlements en temps réel</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('export.orders') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            <a href="{{ route('orders.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
                <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouvelle commande</span><span class="d-sm-none">Créer</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Commandes -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('orders.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') && !request('payment_status') && !request('search') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Commandes</div>
                        <div class="stat-kpi-value text-dark" id="kpi-total-orders">{{ number_format($stats['total'] ?? $orders->total(), 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><span class="badge bg-light text-secondary border">Toutes périodes</span></div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- En Cours / Attente -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="stat-kpi-card text-decoration-none {{ in_array(request('status'), ['pending', 'processing']) ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">À Traiter / En Cours</div>
                        <div class="stat-kpi-value text-warning" id="kpi-pending-orders">{{ number_format($stats['pending'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-clock text-warning me-1"></i>En attente</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Livrées -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('orders.index', ['status' => 'delivered']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'delivered' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Commandes Livrées</div>
                        <div class="stat-kpi-value text-success" id="kpi-delivered-orders">{{ number_format($stats['delivered'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-check-circle text-success me-1"></i>Finalisées</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-truck-ramp-box"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Chiffre d'Affaires Encaissé -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('orders.index', ['payment_status' => 'paid']) }}" class="stat-kpi-card text-decoration-none {{ request('payment_status') === 'paid' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Revenu Encaissé</div>
                        <div class="stat-kpi-value" id="kpi-revenue-orders" style="color: #059669;">{{ currency($stats['revenue'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-credit-card text-success me-1"></i>Règlements payés</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-coins"></i>
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
                    $hasActiveFilters = request('status') || request('payment_status');
                    $activeFilterCount = collect(['status', 'payment_status'])->filter(fn($k) => request()->filled($k))->count();
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
                    <a href="{{ route('export.orders') }}" class="btn-brand-outline">
                        <i class="fas fa-file-csv me-1.5" style="color: var(--success-color)"></i>Exporter CSV
                    </a>
                </div>
            </div>

            <!-- Row 2 : Filtres Déroulants Uniques (Toujours visibles sur Desktop, repliables sur Mobile) -->
            <div class="collapse d-md-block {{ $hasActiveFilters ? 'show' : '' }} mt-3 pt-3 pt-md-2 border-top border-top-md-0" id="orderFiltersCollapse">
                <div class="row g-2 align-items-center">
                    <!-- Statut de livraison -->
                    <div class="col-6 col-md-auto">
                        <select name="status" class="form-select form-select-sm" style="min-width: 170px;">
                            <option value="">Tous les statuts de livraison</option>
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
                        <select name="payment_status" class="form-select form-select-sm" style="min-width: 165px;">
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
                        <a href="{{ route('export.orders') }}" class="btn btn-sm btn-outline-secondary w-100 justify-content-center">
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
            <table class="brand-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 15%; padding-left: 1.25rem;">N° Commande</th>
                        <th style="width: 20%;">Client</th>
                        <th class="text-center" style="width: 8%;">Articles</th>
                        <th class="text-end" style="width: 12%;">Total TTC</th>
                        <th class="text-center" style="width: 13%;">Livraison</th>
                        <th class="text-center" style="width: 13%;">Paiement</th>
                        <th style="width: 9%;">Date</th>
                        <th class="text-end" style="width: 10%; padding-right: 1.25rem;">Actions</th>
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
                            <div class="fw-bold text-dark fs-6">{{ $order->customer_name ?: 'Client invité' }}</div>
                            <div class="text-muted small">{{ $order->customer_email ?: ($order->customer_phone ?: 'Aucun contact') }}</div>
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
                        <td class="text-center">
                            @php
                                $payStatus = strtolower($order->payment_status);
                                $isPaid = in_array($payStatus, ['paid', 'payé']);
                                $pClass = match($payStatus) {
                                    'paid', 'payé' => 'success',
                                    'failed', 'échoué' => 'danger',
                                    'refunded', 'remboursé' => 'info',
                                    default => 'warning'
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
                            <div class="dropdown d-inline-block">
                                <button type="button" 
                                        class="brand-badge brand-badge-btn {{ $pClass }} dropdown-toggle" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-boundary="viewport"
                                        data-bs-popper-config='{"strategy":"fixed"}'
                                        aria-expanded="false"
                                        id="dt-pay-pill-{{ $order->id }}"
                                        title="Changer le statut de paiement">
                                    <i class="fas {{ $pIcon }}" id="dt-pay-icon-{{ $order->id }}"></i>
                                    <span id="dt-pay-label-{{ $order->id }}">{{ $pText }}</span>
                                    <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.6rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="dt-pay-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">État du paiement</li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'pending')">
                                            <span><i class="fas fa-clock text-warning me-1.5"></i> En attente</span>
                                            @if($payStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $isPaid ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'paid')">
                                            <span><i class="fas fa-check-circle text-success me-1.5"></i> Payé</span>
                                            @if($isPaid) <i class="fas fa-check text-success small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'failed' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'failed')">
                                            <span><i class="fas fa-exclamation-circle text-danger me-1.5"></i> Échoué</span>
                                            @if($payStatus === 'failed') <i class="fas fa-check text-danger small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'refunded' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'refunded')">
                                            <span><i class="fas fa-undo text-secondary me-1.5"></i> Remboursé</span>
                                            @if($payStatus === 'refunded') <i class="fas fa-check text-secondary small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $order->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $order->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-end" style="padding-right: 1.25rem;">
                            <div class="d-flex justify-content-end gap-1.5">
                                <a href="{{ route('orders.show', $order) }}" class="btn-action-icon" title="Détails de la commande">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn-action-icon" title="Modifier la commande">
                                    <i class="fas fa-pencil-alt text-info"></i>
                                </a>
                                @if($order->invoice)
                                    <a href="{{ route('invoices.show', $order->invoice) }}" class="btn-action-icon" title="Voir la facture">
                                        <i class="fas fa-file-invoice text-success"></i>
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
                                        <button type="submit" class="btn-action-icon danger" title="Supprimer la commande">
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
     * Quick In-Place Status & Payment Status Updater
     */
    function quickUpdateOrder(orderId, field, value) {
        if (!orderId || !field || !value) return;

        // Visual loading indicator on triggering pill
        const dtPill = document.getElementById(field === 'status' ? `dt-status-pill-${orderId}` : `dt-pay-pill-${orderId}`);
        const mobPill = document.getElementById(field === 'status' ? `mob-status-pill-${orderId}` : `mob-pay-pill-${orderId}`);
        
        if (dtPill) {
            dtPill.style.opacity = '0.6';
            dtPill.disabled = true;
        }
        if (mobPill) {
            mobPill.style.opacity = '0.6';
            mobPill.disabled = true;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/orders/${orderId}/quick-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                [field]: value
            })
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
                        timer: 2500,
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
                dtPill.disabled = false;
            }
            if (mobPill) {
                mobPill.style.opacity = '1';
                mobPill.disabled = false;
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
            'processing': { label: 'En cours', dot: 'blue', badgeClass: 'primary', textClass: 'text-primary', mobBg: '#eff6ff', mobColor: '#2563eb', mobBorder: '#dbeafe' },
            'shipped': { label: 'Expédiée', dot: 'teal', badgeClass: 'info', textClass: 'text-info', mobBg: '#f0fdfa', mobColor: '#0d9488', mobBorder: '#ccfbf1' },
            'delivered': { label: 'Livrée', dot: 'green', badgeClass: 'success', textClass: 'text-success', mobBg: '#ecfdf5', mobColor: '#047857', mobBorder: '#d1fae5' },
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
            'paid': { label: 'Payé', icon: 'fa-check-circle', badgeClass: 'success', textClass: 'text-success', mobClass: 'paid' },
            'pending': { label: 'En attente', icon: 'fa-clock', badgeClass: 'warning', textClass: 'text-warning', mobClass: 'pending' },
            'failed': { label: 'Échoué', icon: 'fa-exclamation-circle', badgeClass: 'danger', textClass: 'text-danger', mobClass: 'failed' },
            'refunded': { label: 'Remboursé', icon: 'fa-undo', badgeClass: 'info', textClass: 'text-secondary', mobClass: 'refunded' }
        };
        const pInfo = pstMap[pst] || { label: order.payment_status_label || pst, icon: 'fa-clock', badgeClass: 'warning', textClass: 'text-warning', mobClass: 'pending' };

        // Desktop Payment Badge
        const dtPayPill = document.getElementById(`dt-pay-pill-${orderId}`);
        if (dtPayPill) {
            dtPayPill.className = `brand-badge brand-badge-btn ${pInfo.badgeClass} dropdown-toggle`;
            const dtPayLabel = document.getElementById(`dt-pay-label-${orderId}`);
            if (dtPayLabel) dtPayLabel.textContent = pInfo.label;
            const dtPayIcon = document.getElementById(`dt-pay-icon-${orderId}`);
            if (dtPayIcon) dtPayIcon.className = `fas ${pInfo.icon}`;

            const dtMenu = dtPayPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (dtMenu) {
                dtMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${pst}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${pInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Mobile Payment Pill
        const mobPayPill = document.getElementById(`mob-pay-pill-${orderId}`);
        if (mobPayPill) {
            mobPayPill.className = `mobile-order-pay-pill ${pInfo.mobClass} border-0 dropdown-toggle`;
            const mobPayLabel = document.getElementById(`mob-pay-label-${orderId}`);
            if (mobPayLabel) mobPayLabel.textContent = pInfo.label;
            const mobPayIcon = document.getElementById(`mob-pay-icon-${orderId}`);
            if (mobPayIcon) mobPayIcon.className = `fas ${pInfo.icon}`;

            const mobMenu = mobPayPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (mobMenu) {
                mobMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${pst}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${pInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Re-initialize dropdowns to preserve fixed Popper strategy
        initStatusDropdowns();
    }

    /**
     * Update real-time KPI metrics
     */
    function updateKPIStats(stats) {
        if (!stats) return;
        if (stats.total !== undefined) {
            const el = document.getElementById('kpi-total-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.total);
        }
        if (stats.pending !== undefined) {
            const el = document.getElementById('kpi-pending-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.pending);
        }
        if (stats.delivered !== undefined) {
            const el = document.getElementById('kpi-delivered-orders');
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(stats.delivered);
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
            const current = bootstrap.Dropdown.getInstance(el);
            if (current) current.dispose();

            new bootstrap.Dropdown(el, {
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

    document.addEventListener('DOMContentLoaded', function() {
        initStatusDropdowns();
    });
</script>
@endpush
