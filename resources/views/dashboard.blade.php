@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="dashboard-container">
    <!-- En-tête de page & Salutation (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <h1 class="brand-title">Vue d'ensemble</h1>
                <p class="brand-subtitle">Bienvenue, <strong>{{ auth()->user()->name }}</strong> &bull; Administration Wina Shop</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('products.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus"></i> <span>Nouveau produit</span>
            </a>
            <a href="{{ route('invoices.create') }}" class="btn-brand-outline">
                <i class="fas fa-file-invoice text-danger"></i> <span>+ Devis / Facture</span>
            </a>
        </div>
    </div>

    <!-- Quick Actions Pills (Barre d'accès rapide fluide) -->
    <div class="dashboard-quick-actions">
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-warning"><i class="fas fa-clock"></i></span>
            <span class="quick-pill-text">En attente ({{ $quickStats['pending_orders'] ?? 0 }})</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-danger"><i class="fas fa-exclamation-triangle"></i></span>
            <span class="quick-pill-text">Stock faible ({{ $quickStats['low_stock_total'] ?? 0 }})</span>
        </a>
        <a href="{{ route('invoices.index') }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-info"><i class="fas fa-receipt"></i></span>
            <span class="quick-pill-text">Factures & Devis</span>
        </a>
        <a href="{{ route('products.index') }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-dark"><i class="fas fa-box-open"></i></span>
            <span class="quick-pill-text">Catalogue produits</span>
        </a>
        <a href="{{ route('categories.index') }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-amber"><i class="fas fa-folder-tree"></i></span>
            <span class="quick-pill-text">Catégories</span>
        </a>
        <a href="{{ route('banners.index') }}" class="quick-action-pill">
            <span class="quick-pill-icon icon-red"><i class="fas fa-images"></i></span>
            <span class="quick-pill-text">Bannières & Sliders</span>
        </a>
    </div>

    <!-- Cartes KPI Pro Unifiées (2x2 sur Mobile, 1x4 sur Desktop) -->
    <div class="stats-grid-pro dash-section">
        <!-- KPI 1 : Chiffre d'affaires -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-red">
                    <i class="fas fa-wallet"></i>
                </div>
                @php $rGrowth = $stats['revenue_growth']; @endphp
                <span class="stat-trend-badge {{ $rGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $rGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($rGrowth) }}%
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Chiffre d'affaires</div>
                <div class="stat-value-pro">{{ currency($stats['total_revenue']) }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    <i class="fas fa-calendar-day text-danger me-1"></i> Aujourd'hui: <strong>{{ currency($quickStats['today_revenue'] ?? 0) }}</strong>
                </span>
            </div>
        </div>

        <!-- KPI 2 : Commandes -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-green">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                @php $oGrowth = $stats['orders_growth']; @endphp
                <span class="stat-trend-badge {{ $oGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $oGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($oGrowth) }}%
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Commandes totales</div>
                <div class="stat-value-pro">{{ number_format($stats['total_orders']) }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    @if(($quickStats['pending_orders'] ?? 0) > 0)
                        <span class="badge-dot dot-warning"></span> <strong class="text-warning">{{ $quickStats['pending_orders'] }}</strong> à traiter
                    @else
                        <span class="badge-dot dot-success"></span> Aucune en attente
                    @endif
                </span>
            </div>
        </div>

        <!-- KPI 3 : Produits en catalogue -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-amber">
                    <i class="fas fa-box"></i>
                </div>
                @php $pGrowth = $stats['products_growth']; @endphp
                <span class="stat-trend-badge {{ $pGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $pGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($pGrowth) }}%
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Catalogue articles</div>
                <div class="stat-value-pro">{{ number_format($stats['total_products']) }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    @if(($quickStats['low_stock_total'] ?? 0) > 0)
                        <span class="badge-dot dot-danger"></span> <strong class="text-danger">{{ $quickStats['low_stock_total'] }}</strong> stock faible
                    @else
                        <span class="badge-dot dot-success"></span> Stock optimal
                    @endif
                </span>
            </div>
        </div>

        <!-- KPI 4 : Clients -->
        <div class="stat-card-pro">
            <div class="stat-card-header">
                <div class="stat-icon-pro icon-indigo">
                    <i class="fas fa-users"></i>
                </div>
                @php $uGrowth = $stats['users_growth']; @endphp
                <span class="stat-trend-badge {{ $uGrowth >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $uGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($uGrowth) }}%
                </span>
            </div>
            <div class="stat-card-body">
                <div class="stat-label-pro">Clients inscrits</div>
                <div class="stat-value-pro">{{ number_format($stats['total_users']) }}</div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-sub-text">
                    <span class="badge-dot dot-info"></span> {{ $quickStats['today_orders'] ?? 0 }} commandes du jour
                </span>
            </div>
        </div>
    </div>

    <!-- Section Graphiques (Pro Dashboards Analytics) -->
    <div class="row g-3 g-md-4 dash-section">
        <!-- Aperçu des revenus (Line Chart) -->
        <div class="col-lg-8">
            <div class="brand-table-card h-100 d-flex flex-column justify-content-between mb-0">
                <div class="card-header-pro">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="card-header-icon flex-shrink-0 icon-red">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark m-0 fs-6">Aperçu des revenus</h5>
                            <p class="text-muted small mb-0">7 derniers jours &bull; Total: <strong class="text-dark">{{ currency($chartData['weekly_total'] ?? 0) }}</strong></p>
                        </div>
                    </div>
                    <span class="live-status-pill">
                        <span class="live-dot"></span> En direct
                    </span>
                </div>
                <div class="card-body-pro flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="chart-container-revenue">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition des commandes (Donut Chart + Pro Progress Bars) -->
        <div class="col-lg-4">
            <div class="brand-table-card h-100 d-flex flex-column justify-content-between mb-0">
                <div class="card-header-pro">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="card-header-icon flex-shrink-0 icon-green">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark m-0 fs-6">Répartition commandes</h5>
                            <p class="text-muted small mb-0">Total: <strong>{{ $chartData['status_total'] ?? 0 }} commande(s)</strong></p>
                        </div>
                    </div>
                    <a href="{{ route('orders.index') }}" class="btn-action-link" title="Voir les commandes">
                        <i class="fas fa-arrow-right fs-xs"></i>
                    </a>
                </div>

                <div class="card-body-pro flex-grow-1 d-flex flex-column justify-content-between">
                    <div class="chart-container-donut my-1">
                        <canvas id="orderStatusChart"></canvas>
                    </div>

                    @php
                        $sTotal = max($chartData['status_total'] ?? 1, 1);
                        $compCount = $chartData['status_counts']['completed'] ?? 0;
                        $pendCount = $chartData['status_counts']['pending'] ?? 0;
                        $cancCount = $chartData['status_counts']['cancelled'] ?? 0;
                        $compPct = round(($compCount / $sTotal) * 100);
                        $pendPct = round(($pendCount / $sTotal) * 100);
                        $cancPct = round(($cancCount / $sTotal) * 100);
                    @endphp

                    <!-- Breakdown Bars Pro -->
                    <div class="donut-breakdown-list pt-2 border-top">
                        <div class="breakdown-item">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="d-inline-flex align-items-center gap-1.5 fw-semibold text-dark">
                                    <span class="badge-dot dot-success"></span> Livrée / Complétée
                                </span>
                                <span class="font-monospace fw-bold text-dark">{{ $compCount }} ({{ $compPct }}%)</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-success" style="width: {{ $compPct }}%"></div>
                            </div>
                        </div>

                        <div class="breakdown-item mt-2">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="d-inline-flex align-items-center gap-1.5 fw-semibold text-dark">
                                    <span class="badge-dot dot-warning"></span> En attente
                                </span>
                                <span class="font-monospace fw-bold text-dark">{{ $pendCount }} ({{ $pendPct }}%)</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-warning" style="width: {{ $pendPct }}%"></div>
                            </div>
                        </div>

                        <div class="breakdown-item mt-2">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="d-inline-flex align-items-center gap-1.5 fw-semibold text-dark">
                                    <span class="badge-dot dot-danger"></span> Annulée
                                </span>
                                <span class="font-monospace fw-bold text-dark">{{ $cancCount }} ({{ $cancPct }}%)</span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-danger" style="width: {{ $cancPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commandes récentes & Alertes stock (Alignement 100% harmonisé) -->
    <div class="row g-3 g-md-4">
        <!-- Commandes récentes -->
        <div class="col-lg-8">
            <div class="brand-table-card h-100 mb-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="card-header-pro">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="card-header-icon flex-shrink-0 icon-blue">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark m-0 fs-6">Commandes récentes</h5>
                                <p class="text-muted small mb-0">Dernières transactions enregistrées</p>
                            </div>
                        </div>
                        <a href="{{ route('orders.index') }}" class="btn-brand-light btn-sm d-inline-flex align-items-center gap-1.5">
                            <span>Voir tout</span>
                            <i class="fas fa-arrow-right fs-xs"></i>
                        </a>
                    </div>

                    {{-- Vue Desktop : Table Complète --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="brand-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 22%;">Commande</th>
                                    <th style="width: 32%;">Client</th>
                                    <th style="width: 14%;" class="text-center">Articles</th>
                                    <th style="width: 16%;" class="text-center">Statut</th>
                                    <th style="width: 16%;" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('orders.show', $order) }}'">
                                    <td>
                                        <span class="order-ref-badge">
                                            #{{ $order->order_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="brand-avatar flex-shrink-0" style="width: 34px; height: 34px; background: var(--gradient-dark); color: white; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(220, 38, 38, 0.25);">
                                                {{ strtoupper(substr($order->customer_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="fw-bold text-dark small text-truncate">{{ $order->customer_name }}</div>
                                                <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ Str::limit($order->customer_email, 22) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="brand-badge info px-2 py-0.5" style="font-size: 0.65rem;">
                                            {{ $order->items_count }} {{ $order->items_count > 1 ? 'Articles' : 'Article' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $st = strtolower($order->status);
                                            $statusClass = match($st) {
                                                'completed', 'livrée', 'payée', 'delivered' => 'success',
                                                'pending', 'en attente' => 'warning',
                                                'processing', 'en cours', 'shipped' => 'info',
                                                'cancelled', 'annulée' => 'danger',
                                                default => 'secondary'
                                            };
                                            $statusLabel = match($st) {
                                                'completed' => 'Complétée',
                                                'pending' => 'En attente',
                                                'processing' => 'En cours',
                                                'shipped' => 'Expédiée',
                                                'delivered', 'livrée' => 'Livrée',
                                                'cancelled', 'annulée' => 'Annulée',
                                                default => ucfirst($order->status)
                                            };
                                        @endphp
                                        <span class="brand-badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark font-monospace">
                                        {{ currency($order->total) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center py-5">
                                            <i class="fas fa-receipt text-muted opacity-25 fs-1 mb-3"></i>
                                            <p class="text-muted mb-0">Aucune commande enregistrée pour l'instant</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Vue Mobile : Cartes Tactiles (Sans scroll horizontal) --}}
                    <div class="d-block d-md-none card-body-pro">
                        @forelse($recentOrders as $order)
                            @php
                                $st = strtolower($order->status);
                                $statusClass = match($st) {
                                    'completed', 'livrée', 'payée', 'delivered' => 'success',
                                    'pending', 'en attente' => 'warning',
                                    'processing', 'en cours', 'shipped' => 'info',
                                    'cancelled', 'annulée' => 'danger',
                                    default => 'secondary'
                                };
                                $statusLabel = match($st) {
                                    'completed' => 'Complétée',
                                    'pending' => 'En attente',
                                    'processing' => 'En cours',
                                    'shipped' => 'Expédiée',
                                    'delivered', 'livrée' => 'Livrée',
                                    'cancelled', 'annulée' => 'Annulée',
                                    default => ucfirst($order->status)
                                };
                            @endphp
                            <a href="{{ route('orders.show', $order) }}" class="mobile-order-card d-block text-decoration-none mb-2.5">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="order-ref-badge">
                                        #{{ $order->order_number }}
                                    </span>
                                    <span class="brand-badge {{ $statusClass }} py-0.5 px-2" style="font-size: 11px;">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $order->customer_name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span> &bull; 
                                            <span>{{ $order->items_count }} art.</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-dark font-monospace">{{ currency($order->total) }}</div>
                                        <div class="text-danger small" style="font-size: 11px;">Détails <i class="fas fa-chevron-right ms-0.5"></i></div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-receipt opacity-25 fs-2 mb-2"></i>
                                <p class="small mb-0">Aucune commande récente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Widget Alertes Stock (Design Pro Wina Shop) -->
        <div class="col-lg-4">
            <div class="brand-table-card h-100 mb-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="card-header-pro">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="card-header-icon icon-amber">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <div class="min-w-0">
                                <h5 class="fw-bold text-dark m-0 fs-6">Alertes de stock</h5>
                                <p class="text-muted small mb-0 text-truncate">
                                    @if(($quickStats['low_stock_total'] ?? 0) > count($lowStockProducts))
                                        {{ count($lowStockProducts) }} plus urgents affichés
                                    @else
                                        Réapprovisionnement requis
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if(($quickStats['low_stock_total'] ?? 0) > 0)
                            <span class="stock-header-badge">
                                <span class="badge-dot dot-danger"></span>
                                {{ $quickStats['low_stock_total'] }} {{ $quickStats['low_stock_total'] > 1 ? 'articles' : 'article' }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body-pro">
                        @if(count($lowStockProducts) > 0)
                            <div class="stock-alert-list">
                                @foreach($lowStockProducts as $product)
                                    @php
                                        $stockVal = max(0, (int)$product->stock);
                                        $isOutOfStock = $stockVal === 0;
                                        $threshold = max(2, (int)($product->min_stock ?? 5));
                                        $pct = min(100, max(8, ($stockVal / $threshold) * 100));
                                    @endphp
                                    <a href="{{ route('products.edit', $product) }}" class="stock-alert-item" title="Modifier le produit {{ $product->name }}">
                                        <!-- Vignette Produit avec badge overlay -->
                                        <div class="stock-img-box flex-shrink-0">
                                            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                            @if($isOutOfStock)
                                                <span class="stock-img-pill" title="Rupture"><i class="fas fa-times"></i></span>
                                            @endif
                                        </div>

                                        <!-- Titre & Détails Produit -->
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="stock-item-title text-truncate">{{ $product->name }}</div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <span class="stock-sku-badge">{{ $product->sku ?? ('#'.$product->id) }}</span>
                                                @if((float)$product->price > 0)
                                                    <span class="stock-price-text">&bull; {{ currency($product->price) }}</span>
                                                @endif
                                            </div>
                                            <!-- Mini-jauge de niveau de stock -->
                                            <div class="stock-micro-progress mt-1.5">
                                                <div class="progress-bar {{ $isOutOfStock ? 'bg-danger' : 'bg-warning' }}" style="width: {{ $isOutOfStock ? '100%' : $pct.'%' }}; opacity: {{ $isOutOfStock ? '0.35' : '1' }};"></div>
                                            </div>
                                        </div>

                                        <!-- Badge Statut & Stock Restant -->
                                        <div class="text-end flex-shrink-0 ms-1 d-flex align-items-center gap-1.5">
                                            <div>
                                                @if($isOutOfStock)
                                                    <span class="stock-status-badge out-of-stock">
                                                        <i class="fas fa-ban"></i>
                                                        <span>Épuisé</span>
                                                    </span>
                                                    <div class="stock-count-sub text-danger font-monospace">0 en stock</div>
                                                @else
                                                    <span class="stock-status-badge low-stock">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        <span>Critique</span>
                                                    </span>
                                                    <div class="stock-count-sub text-warning font-monospace">{{ $stockVal }} restant(s)</div>
                                                @endif
                                            </div>
                                            <i class="fas fa-chevron-right stock-chevron d-none d-sm-inline-block"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="brand-avatar mx-auto mb-3" style="background: #f0fdf4; color: #16a34a; width: 56px; height: 56px; font-size: 22px; border-radius: 14px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Niveaux de stock parfaits</h6>
                                <p class="text-muted small mb-0">Tous vos produits sont au-dessus du seuil de réapprovisionnement.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Pro -->
                <div class="card-footer-pro">
                    <a href="{{ route('inventory.index') }}" class="btn-brand-primary stock-footer-cta">
                        <span>Gérer l'inventaire</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Integration with Real Controller Data -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    titleFont: { size: 12, family: "'Roboto', sans-serif" },
                    bodyFont: { size: 12, weight: 'normal', family: "'Roboto', sans-serif" },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.parsed.y.toLocaleString() + ' DH';
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { borderDash: [4, 4], color: '#f1f5f9' },
                    ticks: { 
                        color: '#94a3b8', 
                        font: { size: 10, family: "'Roboto', sans-serif" },
                        maxTicksLimit: 5,
                        callback: function(val) {
                            return val >= 1000 ? (val/1000) + 'k DH' : val + ' DH';
                        }
                    }
                },
                x: { 
                    grid: { display: false },
                    ticks: { 
                        color: '#94a3b8', 
                        font: { size: 10, family: "'Roboto', sans-serif" },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: window.innerWidth < 576 ? 4 : 7
                    }
                }
            }
        };

        // Dynamic Weekly Revenue Chart from Controller
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revGradient = revenueCtx.createLinearGradient(0, 0, 0, 240);
        revGradient.addColorStop(0, 'rgba(220, 38, 38, 0.22)');
        revGradient.addColorStop(1, 'rgba(220, 38, 38, 0.0)');

        const weeklyLabels = {!! json_encode($chartData['weekly_labels']) !!};
        const weeklyValues = {!! json_encode($chartData['weekly_values']) !!};

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [{
                    label: 'Revenus',
                    data: weeklyValues,
                    borderColor: '#dc2626',
                    backgroundColor: revGradient,
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#dc2626',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 6
                }]
            },
            options: chartOptions
        });

        // Dynamic Order Distribution Donut Chart
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const statusValues = {!! json_encode($chartData['status_values']) !!};

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Complétée', 'En attente', 'Annulée'],
                datasets: [{
                    data: statusValues,
                    backgroundColor: ['#10b981', '#f59e0b', '#dc2626'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + ' commande(s)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
