@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number)

@push('styles')
<style>
.order-history-filter-pill {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 9999px;
    padding: 0.25rem 0.72rem;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.order-history-filter-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.order-history-filter-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
.order-history-timeline {
    position: relative;
    padding-left: 2rem;
}
.order-history-timeline::before {
    content: '';
    position: absolute;
    left: 13px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e2e8f0;
}
.order-history-item {
    position: relative;
    padding-bottom: 1.15rem;
}
.order-history-item:last-child {
    padding-bottom: 0;
}
.order-history-node {
    position: absolute;
    left: -2rem;
    top: 2px;
    width: 28px;
    height: 28px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1);
    z-index: 2;
}
.order-history-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    transition: border-color 0.15s ease, background-color 0.15s ease;
}
.order-history-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
}
.order-diff-chip {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.35rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.22rem 0.55rem;
    font-size: 0.76rem;
}
</style>
@endpush

@section('content')
    <!-- En-tête de la page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Commandes</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-dark fw-semibold font-monospace">#{{ $order->order_number }}</span>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-muted">Détails</span>
                </div>
                <h1 class="brand-title">Commande <span class="font-monospace">#{{ $order->order_number }}</span></h1>
                <p class="brand-subtitle">
                    Passée le <strong>{{ $order->created_at->format('d/m/Y') }}</strong> à {{ $order->created_at->format('H:i') }} &bull; 
                    Client : <strong>{{ $order->customer_name ?: 'Client invité' }}</strong>
                </p>
            </div>
        </div>

        <!-- Boutons d'action responsive -->
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <!-- Retour -->
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary d-inline-flex align-items-center gap-1.5 shadow-2xs" title="Retour aux commandes">
                <i class="fas fa-arrow-left"></i> <span>Retour</span>
            </a>

            <!-- Facture -->
            @if(!$order->invoice)
                <form action="{{ route('orders.generate-invoice', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs">
                        <i class="fas fa-file-invoice"></i> <span>Créer Facture</span>
                    </button>
                </form>
            @else
                <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white">
                    <i class="fas fa-file-invoice"></i> <span>Facture #{{ $order->invoice->invoice_number }}</span>
                </a>
                <a href="{{ route('invoices.delivery-note', $order->invoice) }}" class="btn btn-sm btn-info rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white" style="background: #0284c7; border-color: #0284c7;" title="Consulter le Bon de Livraison conforme Maroc">
                    <i class="fas fa-truck-loading"></i> <span>BL #{{ $order->invoice->delivery_note_number }}</span>
                </a>
            @endif

            <!-- Devis -->
            @if(!$order->quote)
                <form action="{{ route('orders.generate-quote', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs" style="color: #b45309; border-color: #fde68a; background: #fffdf5;">
                        <i class="fas fa-file-alt"></i> <span>Créer Devis</span>
                    </button>
                </form>
            @else
                <a href="{{ route('invoices.show', $order->quote) }}" class="btn btn-sm btn-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="color: #78350f;">
                    <i class="fas fa-file-alt"></i> <span>Devis #{{ $order->quote->invoice_number }}</span>
                </a>
            @endif

            <!-- Modifier -->
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5 text-white" style="background: #dc2626; border-color: #dc2626;">
                <i class="fas fa-pencil-alt"></i> <span>Modifier</span>
            </a>
        </div>
    </div>

    <!-- Alerte Blacklist si client sur liste noire -->
    @if($order->is_blacklisted)
        <div class="alert alert-danger border-danger border-2 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3 p-3.5 bg-danger-subtle text-danger" role="alert">
            <div style="font-size: 1.6rem;" class="text-danger flex-shrink-0 mt-0.5">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="alert-heading fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                        <span>ATTENTION : Client sur Liste Noire</span>
                        <span class="badge bg-danger text-white">Alerte sécurité</span>
                    </h5>
                    <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}" onsubmit="return confirm('Confirmer le retrait de ce client de la liste noire ?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger bg-white shadow-2xs rounded-pill px-3 py-1 fw-semibold">
                            <i class="fas fa-check-circle me-1 text-success"></i> Retirer de la liste noire
                        </button>
                    </form>
                </div>
                <div class="mt-1 text-danger-emphasis small">
                    <strong>Motif :</strong> {{ $order->blacklist_reason ?: 'Client blacklisté (motif non spécifié).' }}
                    @if($order->customer_record && $order->customer_record->blacklisted_at)
                        <span class="opacity-75 ms-2">&bull; Date d'inscription : {{ $order->customer_record->blacklisted_at->format('d/m/Y à H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Stepper de progression de la commande -->
    @php
        $steps = ['pending', 'processing', 'shipped', 'delivered'];
        $currentStatusIndex = array_search(strtolower($order->status), $steps);
        if (strtolower($order->status) === 'cancelled') $currentStatusIndex = -1;
    @endphp

    @if(strtolower($order->status) === 'cancelled')
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3 p-3">
            <i class="fas fa-times-circle fs-3 text-danger"></i>
            <div>
                <h6 class="fw-bold mb-0">Commande Annulée</h6>
                <div class="small opacity-75">Cette commande a été annulée le {{ $order->updated_at->format('d/m/Y à H:i') }}. Aucun envoi n'est prévu.</div>
            </div>
        </div>
    @else
        <div class="order-stepper-wrap mb-4">
            <div class="order-stepper">
                @php
                    $stepPercent = match($currentStatusIndex) {
                        0 => 0,
                        1 => 33,
                        2 => 66,
                        3 => 100,
                        default => 0
                    };
                @endphp
                <div class="order-stepper-track">
                    <div class="order-stepper-progress" style="width: {{ $stepPercent }}%;"></div>
                </div>

                @php
                    $stepLabels = [
                        'pending' => ['name' => 'En attente', 'icon' => 'fa-clock'],
                        'processing' => ['name' => 'En préparation', 'icon' => 'fa-cog'],
                        'shipped' => ['name' => 'Expédiée', 'icon' => 'fa-truck-fast'],
                        'delivered' => ['name' => 'Livrée', 'icon' => 'fa-circle-check'],
                    ];
                @endphp

                @foreach($steps as $idx => $stKey)
                    @php
                        $isDone = $idx < $currentStatusIndex;
                        $isCur = $idx === $currentStatusIndex;
                        $stClass = $isDone ? 'completed' : ($isCur ? 'active' : '');
                    @endphp
                    <div class="order-step-item {{ $stClass }}">
                        <div class="order-step-circle">
                            @if($isDone)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $stepLabels[$stKey]['icon'] }}"></i>
                            @endif
                        </div>
                        <div class="order-step-text">
                            {{ $stepLabels[$stKey]['name'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Colonne Principale (Gauche) : Articles et Notes -->
        <div class="col-12 col-lg-8">
            <!-- Articles commandés -->
            <div class="product-form-card mb-4">
                <div class="product-form-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="product-form-card-title m-0">
                            <i class="fas fa-box-open text-danger"></i> Articles commandés
                            <span class="badge rounded-pill bg-light text-secondary border px-2.5 ms-1" style="font-size: 0.72rem;">
                                {{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }}
                            </span>
                        </h2>
                        @if($order->stock_deducted)
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                <i class="fas fa-check-circle me-1"></i>Stock déduit
                            </span>
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.72rem;">
                                <i class="fas fa-clock me-1"></i>Prélèvement en attente
                            </span>
                        @endif
                    </div>

                    @if(isset($stores) && $stores->isNotEmpty() && $order->items->isNotEmpty())
                        <form action="{{ route('orders.update-stores', $order) }}" method="POST" class="d-flex align-items-center gap-1.5 m-0">
                            @csrf
                            <input type="hidden" name="apply_all" value="1">
                            <select name="bulk_store_id" class="form-select form-select-sm" style="min-width: 175px; max-width: 220px;" title="Affecter tous les articles à un magasin ou dépôt">
                                <option value="">— Tout désaffecter —</option>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 fw-semibold text-nowrap" title="Appliquer ce dépôt à tous les articles">
                                <i class="fas fa-warehouse me-1"></i>Tout affecter
                            </button>
                        </form>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-3 py-2 px-3 small mb-3">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-3">Produit</th>
                                <th class="border-0" style="min-width: 225px;">Magasin / Dépôt source</th>
                                <th class="border-0 text-center">P.U. TTC</th>
                                <th class="border-0 text-center">Quantité</th>
                                <th class="border-0 text-end pe-3">Total TTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $itemStoreStocks = $item->product ? $item->product->storeStocks->keyBy('store_id') : collect();
                                    $hasItemStoreAllocations = $itemStoreStocks->isNotEmpty();
                                    $assignedStockRow = $item->store_id ? $itemStoreStocks->get($item->store_id) : null;
                                @endphp
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 52px; height: 52px; border-radius: 10px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                                <img src="{{ $item->product_image }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     style="width: 100%; height: 100%; object-fit: contain;"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                                     loading="lazy">
                                            </div>
                                            <div>
                                                @if($item->product)
                                                    <a href="{{ route('products.edit', $item->product) }}" class="fw-bold text-dark text-decoration-none d-block hover-danger">
                                                        {{ $item->product_name }}
                                                    </a>
                                                @else
                                                    <span class="fw-bold text-dark d-block">{{ $item->product_name }}</span>
                                                @endif
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                    <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}</span>
                                                    @if($item->variant_name)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.72rem;">
                                                            <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <form action="{{ route('orders.items.update-store', [$order, $item]) }}"
                                              method="POST"
                                              class="item-store-form m-0"
                                              data-item-id="{{ $item->id }}">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <select name="store_id"
                                                        class="form-select form-select-sm item-store-select"
                                                        aria-label="Magasin ou dépôt pour {{ $item->product_name }}">
                                                    <option value="">— Non assigné —</option>
                                                    @foreach($stores ?? [] as $st)
                                                        @php
                                                            $stStock = $itemStoreStocks->get($st->id);
                                                            $stQty = $stStock ? (int) $stStock->quantity : 0;
                                                            $stLoc = $stStock?->location_code ? ' [' . $stStock->location_code . ']' : '';
                                                            $stLabel = $st->name . ($hasItemStoreAllocations ? " — Stock: {$stQty}{$stLoc}" : '');
                                                        @endphp
                                                        <option value="{{ $st->id }}" {{ (int) $item->store_id === (int) $st->id ? 'selected' : '' }}>
                                                            {{ $stLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-outline-primary px-2" title="Enregistrer ce dépôt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap item-store-meta" id="item-store-meta-{{ $item->id }}">
                                                @if($item->store)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">
                                                        <i class="fas fa-warehouse me-1"></i>{{ $item->store->name }}
                                                    </span>
                                                    @if($assignedStockRow?->location_code)
                                                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                                            <i class="fas fa-map-pin text-danger me-1"></i>{{ $assignedStockRow->location_code }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Dépôt à attribuer
                                                    </span>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center py-3 fw-semibold text-secondary">
                                        {{ currency($item->price) }}
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold" style="border-radius: 8px;">
                                            x{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 py-3 fw-bold text-dark font-heading">
                                        {{ currency($item->price * $item->quantity) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Items Cards (Touch-friendly) -->
                <div class="d-block d-md-none">
                    @foreach($order->items as $item)
                        @php
                            $itemStoreStocks = $item->product ? $item->product->storeStocks->keyBy('store_id') : collect();
                            $hasItemStoreAllocations = $itemStoreStocks->isNotEmpty();
                        @endphp
                        <div class="p-3 border rounded-3 mb-2 bg-light bg-opacity-25">
                            <div class="d-flex align-items-start gap-2.5">
                                <div style="width: 52px; height: 52px; border-radius: 10px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $item->product_image }}" 
                                         alt="{{ $item->product_name }}" 
                                         style="width: 100%; height: 100%; object-fit: contain;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                         loading="lazy">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    @if($item->product)
                                        <a href="{{ route('products.edit', $item->product) }}" class="fw-bold text-dark text-decoration-none text-truncate d-block" style="font-size: 0.9rem;">
                                            {{ $item->product_name }}
                                        </a>
                                    @else
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                            {{ $item->product_name }}
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                        <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}</span>
                                        @if($item->variant_name)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.7rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                            </span>
                                        @endif
                                    </div>
                                    <form action="{{ route('orders.items.update-store', [$order, $item]) }}"
                                          method="POST"
                                          class="item-store-form mt-2 m-0"
                                          data-item-id="{{ $item->id }}">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-primary px-2"><i class="fas fa-warehouse"></i></span>
                                            <select name="store_id" class="form-select form-select-sm item-store-select">
                                                <option value="">— Non assigné —</option>
                                                @foreach($stores ?? [] as $st)
                                                    @php
                                                        $stStock = $itemStoreStocks->get($st->id);
                                                        $stQty = $stStock ? (int) $stStock->quantity : 0;
                                                        $stLoc = $stStock?->location_code ? ' [' . $stStock->location_code . ']' : '';
                                                        $stLabel = $st->name . ($hasItemStoreAllocations ? " — {$stQty} dispo{$stLoc}" : '');
                                                    @endphp
                                                    <option value="{{ $st->id }}" {{ (int) $item->store_id === (int) $st->id ? 'selected' : '' }}>
                                                        {{ $stLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-outline-primary px-2" title="Enregistrer">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            {{ currency($item->price) }} &times; {{ $item->quantity }}
                                        </span>
                                        <span class="fw-bold text-dark font-heading">
                                            {{ currency($item->price * $item->quantity) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes de la commande -->
            @if($order->notes)
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-comment-dots text-warning"></i> Notes & Remarques
                        </h2>
                    </div>
                    <div class="p-3 rounded-3 bg-light border text-secondary" style="font-style: italic; line-height: 1.6; border-left: 3px solid #dc2626 !important;">
                        &ldquo;{{ $order->notes }}&rdquo;
                    </div>
                </div>
            @endif

            <!-- Historique complet & Traçabilité -->
            @php
                $historyCollection = $orderHistory ?? collect();
                $histStatusCount = $historyCollection->where('category', 'status')->count();
                $histStockCount = $historyCollection->where('category', 'stock')->count();
                $histPaymentCount = $historyCollection->where('category', 'payment')->count();
            @endphp
            <div class="product-form-card" id="orderHistorySection">
                <div class="product-form-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h2 class="product-form-card-title m-0">
                        <i class="fas fa-clock-rotate-left text-danger"></i> Historique complet &amp; Traçabilité
                        <span class="badge bg-light text-dark border rounded-pill ms-1.5" id="orderHistoryVisibleCount">{{ $historyCollection->count() }}</span>
                    </h2>
                    <div class="d-flex flex-wrap gap-1.5" id="orderHistoryFilters">
                        <button type="button" class="order-history-filter-pill active" data-history-cat="all">
                            Tout ({{ $historyCollection->count() }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="status">
                            Statuts &amp; Modifs ({{ $histStatusCount }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="stock">
                            Stock ({{ $histStockCount }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="payment">
                            Paiements ({{ $histPaymentCount }})
                        </button>
                    </div>
                </div>

                @if($historyCollection->isNotEmpty())
                    <div class="order-history-timeline mt-2" id="orderHistoryTimeline">
                        @foreach($historyCollection as $event)
                            <div class="order-history-item" data-history-cat="{{ $event->category }}">
                                <div class="order-history-node {{ $event->badge_class }} border">
                                    <i class="{{ $event->icon }}"></i>
                                </div>
                                <div class="order-history-card">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                            {{ $event->title }}
                                        </div>
                                        <div class="small text-muted d-flex align-items-center gap-2">
                                            @if($event->timestamp)
                                                <span title="{{ $event->timestamp->format('d/m/Y H:i:s') }}">
                                                    <i class="far fa-clock me-1"></i>{{ $event->timestamp->format('d/m/Y à H:i') }}
                                                </span>
                                                <span class="badge bg-white text-secondary border rounded-pill" style="font-size: 0.68rem;">
                                                    {{ $event->timestamp->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($event->description)
                                        <div class="small text-secondary mb-2">{{ $event->description }}</div>
                                    @endif

                                    @if(!empty($event->changes))
                                        <div class="d-flex flex-wrap gap-1.5 mb-2">
                                            @foreach($event->changes as $chg)
                                                <div class="order-diff-chip">
                                                    <span class="fw-semibold text-secondary">{{ $chg['field'] }} :</span>
                                                    @if($chg['old'] !== null)
                                                        <span class="text-danger text-decoration-line-through">{{ $chg['old'] }}</span>
                                                        <i class="fas fa-arrow-right text-muted" style="font-size: 0.65rem;"></i>
                                                    @endif
                                                    <span class="fw-bold text-dark">{{ $chg['new'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($event->meta))
                                        <div class="d-flex flex-wrap gap-1.5 mb-2">
                                            @foreach($event->meta as $mKey => $mVal)
                                                <div class="order-diff-chip">
                                                    <span class="fw-semibold text-secondary">{{ $mKey }} :</span>
                                                    <span class="fw-bold text-dark">{{ $mVal }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1.5 border-top border-light-subtle text-muted" style="font-size: 0.74rem;">
                                        <span>
                                            <i class="fas fa-user-shield me-1 text-secondary"></i>
                                            Par : <strong class="text-dark">{{ $event->actor }}</strong>
                                        </span>
                                        @if(!empty($event->ip))
                                            <span class="font-monospace">IP: {{ $event->ip }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="orderHistoryEmptyCat" class="text-center py-4 text-muted small d-none">
                        <i class="fas fa-filter fa-lg mb-2 opacity-25 d-block"></i>
                        Aucun événement dans cette catégorie pour cette commande.
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-history fa-2x mb-2 opacity-25 d-block"></i>
                        Aucun historique détaillé enregistré pour cette commande.
                    </div>
                @endif
            </div>
        </div>

        <!-- Colonne Latérale (Droite) : Récapitulatif, Client & Paiement -->
        <div class="col-12 col-lg-4">
            <!-- Récapitulatif Financier -->
            <div class="product-form-card mb-4">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-calculator text-success"></i> Récapitulatif Financier
                    </h2>
                </div>

                <div class="d-flex flex-column gap-2.5">
                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>Sous-total HT</span>
                        <span class="fw-semibold text-dark">{{ currency($order->subtotal) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%)</span>
                        <span class="fw-semibold text-dark">{{ currency($order->tax) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>Frais de livraison</span>
                        <span class="fw-semibold text-dark">{{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : 'Gratuit' }}</span>
                    </div>

                    @if($order->discount > 0)
                        <div class="d-flex justify-content-between align-items-center text-success small">
                            <span>Remise</span>
                            <span class="fw-semibold">-{{ currency($order->discount) }}</span>
                        </div>
                    @endif

                    <div class="pt-2.5 mt-1 border-top d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-6">Total TTC</span>
                        <span class="fw-extrabold font-heading text-danger fs-4">{{ currency($order->total) }}</span>
                    </div>

                    <!-- Statut du Paiement -->
                    @php
                        $payLower = strtolower($order->payment_status);
                        $payBadgeClass = match($payLower) {
                            'paid', 'payé' => 'pay-badge-paid',
                            'failed', 'échoué' => 'pay-badge-failed',
                            'refunded', 'remboursé' => 'pay-badge-refunded',
                            default => 'pay-badge-pending'
                        };
                        $payText = match($payLower) {
                            'paid' => 'Règlement Confirmé (Payé)',
                            'failed' => 'Paiement Échoué',
                            'refunded' => 'Commande Remboursée',
                            default => 'En attente de paiement'
                        };
                    @endphp
                    <div class="mt-2 text-center p-2.5 rounded-pill {{ $payBadgeClass }} fw-semibold small">
                        <i class="fas fa-credit-card me-1.5"></i> {{ $payText }}
                    </div>
                </div>
            </div>

            <!-- Fiche Client & Livraison -->
            <div class="product-form-card mb-4 {{ $order->is_blacklisted ? 'border-danger' : '' }}">
                <div class="product-form-card-header d-flex align-items-center justify-content-between">
                    <h2 class="product-form-card-title m-0">
                        <i class="fas fa-user-circle text-primary"></i> Client & Livraison
                    </h2>
                    @if($order->is_blacklisted)
                        <span class="badge bg-danger text-white fw-bold px-2 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-ban me-1"></i>Liste Noire
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: {{ $order->is_blacklisted ? 'linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%)' : 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)' }}; color: {{ $order->is_blacklisted ? '#dc2626' : '#2563eb' }}; font-weight: 800; font-size: 1.15rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid {{ $order->is_blacklisted ? '#f87171' : '#bfdbfe' }}; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);">
                        @if($order->is_blacklisted)
                            <i class="fas fa-ban"></i>
                        @else
                            {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="fw-bold text-dark text-truncate">{{ $order->customer_name ?: 'Client invité' }}</span>
                            @php $tier = $order->customer_tier; @endphp
                            <span class="badge {{ $tier['badge_class'] }}" style="font-size: 0.7rem; padding: 2px 6px;" title="{{ $tier['description'] }}">
                                <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                            </span>
                        </div>
                        <div class="text-muted small mt-0.5">
                            @if($order->customer_record)
                                <span>{{ $order->customer_record->total_orders }} commande(s) &bull; {{ currency($order->customer_record->total_spent) }}</span>
                            @else
                                <span>Nouvelle commande enregistrée</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 text-secondary small">
                    @if($order->customer_email)
                        <div class="d-flex align-items-center gap-2">
                            <i class="far fa-envelope text-muted" style="width: 16px;"></i>
                            <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none text-dark hover-danger text-truncate">{{ $order->customer_email }}</a>
                        </div>
                    @endif

                    @if($order->customer_phone)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-phone-alt text-muted" style="width: 16px;"></i>
                            <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-dark hover-danger fw-semibold">{{ $order->customer_phone }}</a>
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '212' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none ms-auto px-2 py-1" title="Contacter sur WhatsApp">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                    @endif

                    @if($order->shipping_address)
                        <div class="d-flex align-items-start gap-2 pt-2 border-top mt-1">
                            <i class="fas fa-map-marker-alt text-danger mt-1" style="width: 16px;"></i>
                            <div>
                                <span class="fw-semibold text-dark">Adresse de livraison :</span><br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }} {{ $order->shipping_zip ? '(' . $order->shipping_zip . ')' : '' }}<br>
                                <span class="text-muted">{{ $order->shipping_country ?: 'Maroc' }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions Blacklist et Historique Client -->
                <div class="pt-3 mt-3 border-top d-flex flex-column gap-2">
                    @if($order->customer_record)
                        <a href="{{ route('customers.show', $order->customer_record) }}" class="btn btn-sm btn-light border rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold shadow-2xs">
                            <i class="fas fa-user-clock text-primary"></i> <span>Voir l'historique complet du client</span>
                        </a>
                    @endif

                    @if($order->is_blacklisted)
                        <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}" onsubmit="return confirm('Confirmer le retrait de ce client de la liste noire ?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold">
                                <i class="fas fa-check-circle"></i> <span>Retirer de la Liste Noire</span>
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#blacklistOrderModal">
                            <i class="fas fa-ban"></i> <span>Ajouter à la Liste Noire</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Détails du Règlement -->
            <div class="product-form-card">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-money-check-dollar text-secondary"></i> Modalités de Paiement
                    </h2>
                </div>

                <div class="d-flex flex-column gap-2.5 small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Mode :</span>
                        <span class="fw-bold text-dark text-capitalize">{{ str_replace('_', ' ', $order->payment_method ?: 'Non spécifié') }}</span>
                    </div>

                    @if($order->transaction_id)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Réf. Transaction :</span>
                            <span class="font-monospace fw-semibold text-dark">{{ $order->transaction_id }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Date d'enregistrement :</span>
                        <span class="text-dark">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    @if($order->payment_status !== 'paid' && $order->customer_id)
                        <div class="pt-2.5 mt-1 border-top">
                            <a href="{{ route('customers.show', $order->customer_id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold w-100">
                                <i class="fas fa-wallet me-1.5"></i> Encaisser / Solde Client ({{ currency($order->customer->current_balance ?? $order->total) }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout à la Liste Noire -->
    <div class="modal fade" id="blacklistOrderModal" tabindex="-1" aria-labelledby="blacklistOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3" style="background: #fef2f2; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #fecaca;">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-danger mb-0" id="blacklistOrderModalLabel">Placer sur Liste Noire</h5>
                            <div class="text-muted small">Ce client sera signalé sur toutes ses commandes futures</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="small text-muted mb-1">Client concerné :</div>
                            <div class="fw-bold text-dark fs-6">{{ $order->customer_name ?: 'Client sans nom' }}</div>
                            <div class="small text-secondary">{{ $order->customer_phone ?: $order->customer_email }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Motif du blacklistage <span class="text-danger">*</span></label>
                            <select name="reason_preset" class="form-select form-select-sm mb-2" onchange="if(this.value){ document.getElementById('customReason').value = this.value; }">
                                <option value="">-- Choisir un motif fréquent --</option>
                                <option value="Colis refusé à la livraison sans motif">Colis refusé à la livraison sans motif</option>
                                <option value="Fausse commande / Numéro injoignable">Fausse commande / Numéro injoignable</option>
                                <option value="Impayé / Chèque rejeté">Impayé / Chèque rejeté</option>
                                <option value="Comportement abusif / Harcèlement">Comportement abusif / Harcèlement</option>
                                <option value="Retours répétés injustifiés">Retours répétés injustifiés</option>
                            </select>
                            <textarea name="reason" id="customReason" class="form-control" rows="3" placeholder="Précisez la raison détaillée du blacklistage..." required>Colis refusé à la livraison sans motif</textarea>
                            <div class="form-text small">Cette note sera visible par tous les administrateurs lors de la réception de nouvelles commandes.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 py-1.5 fw-semibold shadow-xs" style="background: #dc2626; border-color: #dc2626;">
                            <i class="fas fa-ban me-1.5"></i> Confirmer le Blacklistage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script>
(function() {
function initOrderShow() {
    document.querySelectorAll('.item-store-form').forEach(form => {
        const select = form.querySelector('.item-store-select');
        let previousValue = select ? select.value : '';

        if (select) {
            select.addEventListener('change', function() {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            });
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const itemId = form.getAttribute('data-item-id');
            const submitBtn = form.querySelector('button[type="submit"]');
            const storeId = select ? select.value : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            try {
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ store_id: storeId || null })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Impossible de mettre à jour le dépôt.');
                }

                previousValue = storeId;

                // Keep desktop and mobile selects for the same item synchronized
                document.querySelectorAll(`.item-store-form[data-item-id="${itemId}"] .item-store-select`).forEach(sel => {
                    sel.value = storeId;
                });

                const metaEl = document.getElementById(`item-store-meta-${itemId}`);
                if (metaEl) {
                    if (data.item && data.item.store_name) {
                        const locBadge = data.item.location_code
                            ? `<span class="badge bg-light text-secondary border" style="font-size: 0.68rem;"><i class="fas fa-map-pin text-danger me-1"></i>${data.item.location_code}</span>`
                            : '';
                        metaEl.innerHTML = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;"><i class="fas fa-warehouse me-1"></i>${data.item.store_name}</span>${locBadge}`;
                    } else {
                        metaEl.innerHTML = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;"><i class="fas fa-exclamation-circle me-1"></i>Dépôt à attribuer</span>`;
                    }
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2600,
                        timerProgressBar: true,
                    });
                }
            } catch (err) {
                if (select) {
                    select.value = previousValue;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock insuffisant / Erreur',
                        text: err.message,
                        confirmButtonColor: '#dc2626',
                    });
                } else {
                    alert(err.message);
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check"></i>';
                }
            }
        });
    });

    // Live zero-refresh filter for Order History timeline
    const historyFilterBtns = document.querySelectorAll('#orderHistoryFilters .order-history-filter-pill');
    const historyItems = document.querySelectorAll('#orderHistoryTimeline .order-history-item');
    const historyVisibleCount = document.getElementById('orderHistoryVisibleCount');
    const historyEmptyCat = document.getElementById('orderHistoryEmptyCat');

    historyFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cat = this.dataset.historyCat || 'all';
            historyFilterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            let visible = 0;
            historyItems.forEach(item => {
                const match = cat === 'all' || item.dataset.historyCat === cat;
                item.classList.toggle('d-none', !match);
                if (match) visible++;
            });

            if (historyVisibleCount) historyVisibleCount.textContent = visible;
            if (historyEmptyCat) historyEmptyCat.classList.toggle('d-none', visible > 0);
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOrderShow);
} else {
    initOrderShow();
}
})();
</script>
@endpush
@endsection
