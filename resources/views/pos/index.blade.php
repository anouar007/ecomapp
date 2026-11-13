@extends('layouts.app')

@section('title', 'Caisse POS')

@push('styles')
<style>
.main-content {
    padding: 0 !important;
    height: calc(100vh - 70px);
    overflow: hidden;
    background: #f8fafc;
}

/* === 3-Column Pro POS Layout === */
.pos-layout {
    display: grid;
    grid-template-columns: 1fr 315px 320px;
    height: 100%;
    min-height: 0;
    gap: 0;
}

@media (min-width: 1440px) {
    .pos-layout {
        grid-template-columns: 1fr 360px 360px;
    }
}

@media (max-width: 1200px) and (min-width: 1024px) {
    .pos-layout {
        grid-template-columns: 1fr 295px 300px;
    }
}

/* Mobile / Tablet Tab Bar */
.pos-mobile-tabs {
    display: none;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    gap: 8px;
}

.pos-mobile-tab {
    flex: 1;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
}

.pos-mobile-tab.active {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

@media (max-width: 1023px) {
    .main-content {
        height: auto;
        min-height: calc(100vh - 70px);
        overflow: visible;
    }
    .pos-mobile-tabs {
        display: flex;
        position: sticky;
        top: 70px;
        z-index: 20;
    }
    .pos-layout {
        display: block;
        height: auto;
    }
    .pos-pane {
        display: none;
        height: auto !important;
    }
    .pos-pane.mobile-active {
        display: flex !important;
        flex-direction: column;
    }
}

/* === Left: Products Catalog === */
.pos-products-area {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
    background: #f8fafc;
}

.pos-header {
    flex-shrink: 0;
    padding: 10px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-container {
    position: relative;
    flex: 1 1 220px;
    min-width: 180px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.search-input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13.5px;
    background: #f8fafc;
    color: #0f172a;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    background: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
}

.pos-store-picker {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 5px 10px;
}

.pos-store-picker i {
    color: #dc2626;
    font-size: 13px;
}

.pos-store-select {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
    outline: none;
    padding-right: 4px;
}

.pos-terminal-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    font-size: 12px;
    font-weight: 700;
}

.pos-kpi-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 11px;
    border-radius: 10px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 12px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.pos-icon-btn {
    height: 34px;
    padding: 0 11px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s;
}

.pos-icon-btn:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
}

.qty-input-inline {
    width: 34px;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    -moz-appearance: textfield;
}
.qty-input-inline::-webkit-outer-spin-button,
.qty-input-inline::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.cash-presets {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 6px;
}

.cash-preset-btn {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.cash-preset-btn:hover {
    border-color: #dc2626;
    color: #dc2626;
    background: #fef2f2;
}

.categories-wrapper {
    flex-shrink: 0;
    padding: 8px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
    white-space: nowrap;
}

.categories-wrapper::-webkit-scrollbar {
    height: 4px;
}
.categories-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.category-pills {
    display: inline-flex;
    gap: 8px;
}

.category-pill {
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}

.category-pill:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.category-pill.active {
    background: #dc2626;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
}

.products-grid {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 14px 16px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(158px, 1fr));
    grid-auto-rows: max-content;
    gap: 12px;
    align-content: start;
}

.pos-product-card {
    display: block;
    height: auto;
    min-height: 205px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.pos-product-card:hover {
    transform: translateY(-2px);
    border-color: #dc2626;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}

.pos-product-card.out-of-stock-card {
    opacity: 0.58;
    cursor: not-allowed;
}

.pos-product-card.out-of-stock-card:hover {
    transform: none;
    border-color: #e2e8f0;
    box-shadow: none;
}

.card-img-wrapper {
    position: relative;
    width: 100%;
    height: 120px;
    min-height: 120px;
    background: #f8fafc;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #f1f5f9;
}

.card-img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    padding: 6px;
    display: block;
}

.overlay-add {
    position: absolute;
    inset: 0;
    background: rgba(220, 38, 38, 0.78);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.15s;
}

.pos-product-card:hover .overlay-add {
    opacity: 1;
}

.overlay-add i {
    color: #ffffff;
    font-size: 24px;
}

.qty-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 3;
    background: #dc2626;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    min-width: 24px;
    height: 24px;
    padding: 0 7px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
}

.sale-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 3;
    background: #0f172a;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 6px;
    letter-spacing: 0.4px;
}

.stock-badge {
    position: absolute;
    top: 96px;
    right: 8px;
    z-index: 2;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-badge.in-stock { background: #dcfce7; color: #166534; }
.stock-badge.low-stock { background: #fef3c7; color: #92400e; }
.stock-badge.out-of-stock { background: #fee2e2; color: #991b1b; }

.card-stores-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 7px;
}

.store-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 9.5px;
    font-weight: 700;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.store-tag-pill.active-store {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}

.store-tag-pill.global-fallback {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}

.card-content {
    padding: 10px 12px 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 4px;
}

.card-sku {
    font-size: 10.5px;
    color: #94a3b8;
    font-family: ui-monospace, SFMono-Regular, monospace;
    margin-bottom: 8px;
}

.card-footer {
    margin-top: auto;
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.card-price {
    font-size: 14.5px;
    font-weight: 800;
    color: #0f172a;
}

.card-price.sale {
    color: #dc2626;
}

.card-original-price {
    font-size: 11px;
    color: #94a3b8;
    text-decoration: line-through;
}

/* === Middle & Right Panels === */
.cart-items-panel,
.order-form-panel {
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.order-form-panel {
    background: #f8fafc;
}

.panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    flex-shrink: 0;
}

.panel-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.panel-title i {
    color: #dc2626;
}

.panel-badge {
    background: #dc2626;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 9999px;
}

.items-list {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    min-height: 0;
}

.cart-item-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 11px 12px;
    margin-bottom: 10px;
    transition: border-color 0.15s;
}

.cart-item-card:hover {
    border-color: #cbd5e1;
}

.cart-item-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.cart-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
}

.cart-item-remove {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 6px;
    font-size: 12px;
}

.cart-item-remove:hover {
    color: #dc2626;
    background: #fee2e2;
}

.cart-item-mid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.item-controls {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 2px;
}

.control-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: none;
    background: #f8fafc;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
}

.control-btn:hover {
    background: #dc2626;
    color: #ffffff;
}

.qty-text {
    min-width: 22px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}

.cart-item-discount-row {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}

.item-disc-inputs {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.item-disc-input {
    width: 64px;
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12px;
    background: #ffffff;
    text-align: right;
}

.item-disc-input:focus {
    outline: none;
    border-color: #dc2626;
}

.item-disc-type {
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 11.5px;
    background: #ffffff;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
}

/* Form Panel */
.panel-content {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px 12px;
}

.pos-card-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 8px;
}

.pos-section-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.compact-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    color: #0f172a;
    transition: border-color 0.15s;
}

.compact-input:focus {
    outline: none;
    border-color: #dc2626;
}

.payment-pills {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}

.payment-pill {
    padding: 7px 6px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    color: #334155;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all 0.15s;
}

.payment-pill:hover:not(:disabled) {
    border-color: #94a3b8;
    background: #ffffff;
}

.payment-pill.active {
    background: #fef2f2;
    border-color: #dc2626;
    color: #dc2626;
}

.payment-pill:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.order-totals {
    background: #ffffff;
    padding: 10px 14px 12px;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.bill-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 12.5px;
    color: #64748b;
}

.bill-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px dashed #e2e8f0;
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}

.pay-btn {
    width: 100%;
    margin-top: 12px;
    padding: 13px 16px;
    background: #dc2626;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.pay-btn:hover:not(:disabled) {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
}

.pay-btn:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}
</style>
@endpush

@section('content')
@php
    $currencySymbol = setting('currency_symbol', 'DH');
@endphp

<!-- Mobile Tab Switcher -->
<div class="pos-mobile-tabs">
    <button type="button" class="pos-mobile-tab active" data-pane="paneCatalog" onclick="switchMobilePane('paneCatalog', this)">
        <i class="fas fa-th-large"></i> Catalogue
    </button>
    <button type="button" class="pos-mobile-tab" data-pane="paneCart" onclick="switchMobilePane('paneCart', this)">
        <i class="fas fa-shopping-cart"></i> Panier (<span id="mobileCartBadge">0</span>)
    </button>
    <button type="button" class="pos-mobile-tab" data-pane="paneCheckout" onclick="switchMobilePane('paneCheckout', this)">
        <i class="fas fa-cash-register"></i> Caisse
    </button>
</div>

<div class="pos-layout">
    <!-- Left: Catalog -->
    <div class="pos-products-area pos-pane mobile-active" id="paneCatalog">
        <div class="pos-header">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom ou code SKU... (F2)" style="padding-right: 32px;" autofocus>
                <button type="button" id="clearSearchBtn" onclick="clearSearchInput()" title="Effacer" style="display:none;position:absolute;right:8px;top:50%;transform:translateY(-50%);border:none;background:transparent;color:#94a3b8;cursor:pointer;padding:4px;">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            @if(isset($stores) && $stores->count() > 0)
            <div class="pos-store-picker" title="Point de vente / Dépôt actif">
                <i class="fas fa-warehouse"></i>
                <select id="posStoreSelect" class="pos-store-select">
                    <option value="" selected>Tous les magasins / dépôts</option>
                    @foreach($stores as $posStore)
                        <option value="{{ $posStore->id }}">
                            {{ $posStore->name }} ({{ $posStore->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(isset($brands) && $brands->count() > 0)
            <div class="pos-store-picker" title="Filtrer par marque">
                <i class="fas fa-tag"></i>
                <select id="brandFilter" class="pos-store-select">
                    <option value="">Toutes marques</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="pos-store-picker" title="Filtrer par disponibilité">
                <i class="fas fa-boxes-stacked"></i>
                <select id="stockFilter" class="pos-store-select">
                    <option value="all">Tous stocks</option>
                    <option value="in_stock">En stock</option>
                    <option value="low_stock">Stock faible (≤5)</option>
                </select>
            </div>

            <button type="button" id="resetFiltersBtn" class="pos-icon-btn" onclick="resetPosFilters()" style="display: none;" title="Réinitialiser les filtres">
                <i class="fas fa-rotate-left text-danger"></i>
            </button>

            <div class="pos-kpi-pill d-none d-md-inline-flex" title="Ventes POS d'aujourd'hui">
                <i class="fas fa-chart-line"></i>
                <span>Aujourd'hui : <strong id="todaySalesCount">{{ $todayPosCount ?? 0 }}</strong> • <strong id="todaySalesTotal">{{ currency($todayPosTotal ?? 0) }}</strong></span>
            </div>

            <button type="button" class="pos-icon-btn" onclick="openHeldOrdersModal()" title="Tickets mis en attente">
                <i class="fas fa-pause-circle text-warning"></i>
                <span class="d-none d-sm-inline">Attente</span>
                <span class="badge bg-warning text-dark rounded-pill" id="heldCountBadge" style="font-size: 10px;">0</span>
            </button>

            <button type="button" class="pos-icon-btn d-none d-lg-inline-flex" onclick="togglePosFullscreen()" title="Plein écran">
                <i class="fas fa-expand" id="fullscreenIcon"></i>
            </button>

            <div class="pos-terminal-badge d-none d-xl-flex">
                <i class="fas fa-cash-register"></i>
                <span>POS-{{ str_pad(auth()->id(), 3, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <div class="categories-wrapper">
            <div class="category-pills">
                <div class="category-pill category-btn active" data-category="">Tous les articles</div>
                @foreach($categories as $category)
                    <div class="category-pill category-btn" data-category="{{ $category->id }}">{{ $category->name }}</div>
                @endforeach
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            <div style="grid-column: 1/-1; text-align: center; padding-top: 90px; color: #94a3b8;">
                <i class="fas fa-circle-notch fa-spin fa-2x"></i>
                <p style="margin-top: 12px; font-weight: 500; font-size: 13px;">Chargement du catalogue...</p>
            </div>
        </div>
    </div>

    <!-- Middle: Cart Items + Per-Item Discount -->
    <div class="cart-items-panel pos-pane" id="paneCart">
        <div class="panel-header">
            <div class="panel-title">
                <i class="fas fa-shopping-cart"></i>
                Panier
                <span class="panel-badge" id="cartCount">0</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="holdCurrentCart()" title="Mettre ce panier en attente"
                        style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; height: 32px; padding: 0 10px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700;">
                    <i class="fas fa-pause"></i>
                    <span>Attente</span>
                </button>
                <button type="button" onclick="clearCartConfirm()" title="Vider le panier (F4)"
                        style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>

        <div class="items-list" id="cartItems"></div>
    </div>

    <!-- Right: Client Info + Global Discount + Payment -->
    <div class="order-form-panel pos-pane" id="paneCheckout">
        <div class="panel-header">
            <div class="panel-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Client & Encaissement
            </div>
        </div>

        <div class="panel-content">
            <!-- Customer Selection + Editable Info (Name, Phone, City) + Save Option -->
            <div class="pos-card-box">
                <div class="pos-section-label">
                    <span><i class="fas fa-user me-1 text-danger"></i> Client</span>
                    <button type="button" onclick="resetCustomerFields()" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px; color: #64748b;">
                        Nouveau / Passage
                    </button>
                </div>

                <input type="text" id="customerFilterInput" class="compact-input mb-2" placeholder="🔍 Filtrer client (nom, tél, ville)..." style="font-size: 12px; padding: 6px 10px; background: #f8fafc;">

                <select id="customerSelect" class="compact-input mb-2" style="cursor: pointer;">
                    <option value="">🚶 Client de passage (Walk-in)</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-email="{{ $customer->email }}"
                                data-phone="{{ $customer->phone }}"
                                data-city="{{ $customer->city }}"
                                data-credit-limit="{{ $customer->credit_limit }}"
                                data-balance="{{ $customer->current_balance }}">
                            {{ $customer->name }}{{ $customer->phone ? ' • ' . $customer->phone : '' }}{{ $customer->city ? ' (' . $customer->city . ')' : '' }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden" id="customerId" value="">
                <input type="hidden" id="customerEmail" value="">

                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" id="customerName" class="compact-input" value="Client de passage" placeholder="Nom du client *">
                    </div>
                    <div class="col-6">
                        <input type="text" id="customerPhone" class="compact-input" placeholder="Téléphone">
                    </div>
                    <div class="col-6">
                        <input type="text" id="customerCity" list="posCityList" class="compact-input" placeholder="Ville">
                        <datalist id="posCityList">
                            <option value="Casablanca">
                            <option value="Rabat">
                            <option value="Marrakech">
                            <option value="Tanger">
                            <option value="Fès">
                            <option value="Agadir">
                            <option value="Meknès">
                            <option value="Oujda">
                            <option value="Kénitra">
                            <option value="Tétouan">
                            <option value="Safi">
                            <option value="Mohammedia">
                            <option value="El Jadida">
                            <option value="Béni Mellal">
                            <option value="Nador">
                        </datalist>
                    </div>
                </div>

                <div class="form-check form-switch mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <input class="form-check-input" type="checkbox" id="saveCustomerCheckbox" style="cursor: pointer;">
                    <label class="form-check-label" for="saveCustomerCheckbox" style="font-size: 12px; font-weight: 600; color: #334155; cursor: pointer;">
                        Enregistrer / mettre à jour ce client
                    </label>
                </div>

                <!-- Credit Bar for Selected Registered Customer -->
                <div id="creditInfo" style="display: none; margin-top: 10px; padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px;">
                        <span style="color: #64748b; font-weight: 600;">Crédit client :</span>
                        <span id="creditUsed" style="font-weight: 700; color: #0f172a;"></span>
                    </div>
                    <div style="height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden;">
                        <div id="creditBar" style="height: 100%; background: #10b981; width: 0%; transition: width 0.3s;"></div>
                    </div>
                </div>
            </div>

            <!-- Global Discount -->
            <div class="pos-card-box">
                <div class="pos-section-label">
                    <span><i class="fas fa-tag me-1 text-danger"></i> Remise globale</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <input type="number" id="discountAmount" class="compact-input" placeholder="0" min="0" step="0.01" style="flex: 1;">
                    <select id="discountType" class="compact-input" style="width: 78px; font-weight: 600;">
                        <option value="percent">%</option>
                        <option value="fixed">{{ $currencySymbol }}</option>
                    </select>
                </div>
            </div>

            <!-- Payment Method & Cash Tendered -->
            <div class="pos-card-box">
                <div class="pos-section-label">
                    <span><i class="fas fa-credit-card me-1 text-danger"></i> Mode de paiement</span>
                </div>
                <input type="hidden" id="paymentMethod" value="cash">
                <div class="payment-pills mb-2">
                    <button type="button" class="payment-pill active" data-method="cash" onclick="selectPaymentMethod('cash', this)">💵 Espèces</button>
                    <button type="button" class="payment-pill" data-method="card" onclick="selectPaymentMethod('card', this)">💳 Carte</button>
                    <button type="button" class="payment-pill" data-method="mobile" onclick="selectPaymentMethod('mobile', this)">📱 Mobile</button>
                    <button type="button" class="payment-pill" id="creditOption" data-method="credit" onclick="selectPaymentMethod('credit', this)" disabled>🏦 Crédit</button>
                    <button type="button" class="payment-pill" data-method="other" onclick="selectPaymentMethod('other', this)">⚪ Autre</button>
                </div>

                <div id="cashTenderedBox" class="mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <label for="amountReceived" style="font-size: 11.5px; color: #64748b; font-weight: 600; white-space: nowrap;">Montant reçu :</label>
                        <input type="number" id="amountReceived" class="compact-input" placeholder="0.00" min="0" step="0.01" style="width: 120px; text-align: right; font-weight: 700;">
                    </div>
                    <div class="cash-presets">
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset('exact')">Exact</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(50)">50</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(100)">100</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(200)">200</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(500)">500</button>
                    </div>
                    <div id="changeRow" class="d-flex justify-content-between align-items-center mt-1" style="font-size: 12px; display: none !important;">
                        <span style="color: #64748b;">Monnaie à rendre :</span>
                        <strong id="changeAmount" style="color: #16a34a;">{{ currency(0) }}</strong>
                    </div>
                </div>

                <input type="text" id="orderNotes" class="compact-input mt-2" placeholder="Note interne (optionnel)">
            </div>
        </div>

        <!-- Totals & Checkout Footer -->
        <div class="order-totals">
            <div class="bill-row">
                <span>Sous-total articles</span>
                <span id="subtotal" style="font-weight: 600; color: #0f172a;">{{ currency(0) }}</span>
            </div>
            <div class="bill-row" id="totalDiscountRow">
                <span>Remise totale</span>
                <span id="totalDiscountDisplay" style="font-weight: 600; color: #64748b;">{{ currency(0) }}</span>
            </div>
            <div class="bill-total">
                <span>Total</span>
                <span id="total" style="color: #dc2626;">{{ currency(0) }}</span>
            </div>

            <div style="margin-top: 8px; font-size: 10.5px; color: #64748b; text-align: center;">
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F2</kbd> Rechercher
                <span class="mx-1">•</span>
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F4</kbd> Vider
                <span class="mx-1">•</span>
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F9</kbd> Encaisser
            </div>

            <button type="button" class="pay-btn" id="checkoutBtn" onclick="checkout()" disabled>
                <i class="fas fa-check-circle"></i>
                <span id="checkoutBtnLabel">Encaisser {{ currency(0) }}</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.currencyConfig = {
    symbol: '{{ setting("currency_symbol", "DH") }}',
    position: '{{ setting("currency_position", "after") }}',
    decimals: {{ (int) setting("currency_decimals", 2) }},
    decimal_separator: '{{ setting("decimal_separator", ".") }}',
    thousands_separator: '{{ setting("thousands_separator", ",") }}',
    tax_rate: {{ floatval(setting("tax_rate", 20)) }} / 100
};

window.posState = {
    products: [],
    cart: [],
    currentTotal: 0,
    todayCount: {{ (int) ($todayPosCount ?? 0) }},
    todayTotal: {{ (float) ($todayPosTotal ?? 0) }}
};

window.POS_HELD_STORAGE_KEY = 'wina_pos_held_orders_v1';

function getHeldOrders() {
    try {
        return JSON.parse(localStorage.getItem(window.POS_HELD_STORAGE_KEY) || '[]');
    } catch {
        return [];
    }
}

function saveHeldOrders(list) {
    localStorage.setItem(window.POS_HELD_STORAGE_KEY, JSON.stringify(list));
    refreshHeldBadge();
}

function refreshHeldBadge() {
    const badge = document.getElementById('heldCountBadge');
    if (badge) badge.textContent = getHeldOrders().length;
}

window.togglePosFullscreen = function() {
    const icon = document.getElementById('fullscreenIcon');
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen?.();
        if (icon) icon.className = 'fas fa-compress';
    } else {
        document.exitFullscreen?.();
        if (icon) icon.className = 'fas fa-expand';
    }
};

window.setCashPreset = function(val) {
    const input = document.getElementById('amountReceived');
    if (!input) return;
    input.value = val === 'exact'
        ? (window.posState.currentTotal > 0 ? window.posState.currentTotal.toFixed(2) : '')
        : Number(val).toFixed(2);
    updateCashChange();
};

window.holdCurrentCart = function() {
    if (!window.posState.cart.length) {
        Swal.fire({ icon: 'info', title: 'Panier vide', text: 'Ajoutez au moins un produit avant de mettre en attente.', timer: 1600, showConfirmButton: false });
        return;
    }
    const list = getHeldOrders();
    list.unshift({
        id: Date.now(),
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        customerName: document.getElementById('customerName')?.value || 'Client de passage',
        customerPhone: document.getElementById('customerPhone')?.value || '',
        customerCity: document.getElementById('customerCity')?.value || '',
        customerId: document.getElementById('customerId')?.value || '',
        discountAmount: document.getElementById('discountAmount')?.value || '',
        discountType: document.getElementById('discountType')?.value || 'percent',
        notes: document.getElementById('orderNotes')?.value || '',
        total: window.posState.currentTotal,
        cart: window.posState.cart
    });
    saveHeldOrders(list);

    window.posState.cart = [];
    document.getElementById('discountAmount').value = '';
    document.getElementById('orderNotes').value = '';
    resetCustomerFields();
    renderCart();
    renderProducts();

    Swal.fire({ icon: 'success', title: 'Ticket mis en attente', timer: 1200, showConfirmButton: false });
};

window.openHeldOrdersModal = function() {
    const list = getHeldOrders();
    if (!list.length) {
        Swal.fire({ icon: 'info', title: 'Aucun ticket en attente', text: 'Les paniers mis en pause apparaîtront ici.' });
        return;
    }
    const rows = list.map((h, idx) => `
        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-light text-start">
            <div>
                <div class="fw-bold text-dark" style="font-size:13px;">${escapeHtml(h.customerName)} <span class="text-muted fw-normal">(${h.time})</span></div>
                <div class="text-muted" style="font-size:11.5px;">${h.cart.length} article(s) • <strong class="text-danger">${formatCurrency(h.total)}</strong></div>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="resumeHeldOrder(${idx})">Reprendre</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deleteHeldOrder(${idx})"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `).join('');

    Swal.fire({
        title: 'Tickets en attente',
        html: `<div style="max-height:320px;overflow-y:auto;">${rows}</div>`,
        showConfirmButton: false,
        showCloseButton: true
    });
};

window.resumeHeldOrder = function(idx) {
    const list = getHeldOrders();
    const held = list[idx];
    if (!held) return;
    list.splice(idx, 1);
    saveHeldOrders(list);

    window.posState.cart = held.cart || [];
    if (held.customerId) {
        document.getElementById('customerSelect').value = held.customerId;
        document.getElementById('customerSelect').dispatchEvent(new Event('change'));
    } else {
        resetCustomerFields();
        document.getElementById('customerName').value = held.customerName || 'Client de passage';
        document.getElementById('customerPhone').value = held.customerPhone || '';
        document.getElementById('customerCity').value = held.customerCity || '';
    }
    document.getElementById('discountAmount').value = held.discountAmount || '';
    document.getElementById('discountType').value = held.discountType || 'percent';
    document.getElementById('orderNotes').value = held.notes || '';
    renderCart();
    renderProducts();
    Swal.close();
};

window.deleteHeldOrder = function(idx) {
    const list = getHeldOrders();
    list.splice(idx, 1);
    saveHeldOrders(list);
    openHeldOrdersModal();
};

window.formatCurrency = function(amount) {
    const cfg = window.currencyConfig;
    const num = Number(amount || 0);
    const formatted = num.toFixed(cfg.decimals)
        .replace(/\d(?=(\d{3})+\.)/g, '$&' + cfg.thousands_separator)
        .replace('.', cfg.decimal_separator);
    return cfg.position === 'before' ? cfg.symbol + formatted : formatted + ' ' + cfg.symbol;
};

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, s => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[s]));
}

function switchMobilePane(paneId, btn) {
    document.querySelectorAll('.pos-pane').forEach(el => el.classList.remove('mobile-active'));
    document.querySelectorAll('.pos-mobile-tab').forEach(el => el.classList.remove('active'));
    document.getElementById(paneId)?.classList.add('mobile-active');
    btn?.classList.add('active');
}

function computeItemLine(item) {
    const unitPrice = parseFloat(item.price) || 0;
    const qty = parseInt(item.quantity, 10) || 1;
    const gross = +(unitPrice * qty).toFixed(2);
    const discVal = parseFloat(item.discount_amount) || 0;
    const discType = item.discount_type || 'percent';

    let lineDiscount = 0;
    if (discVal > 0 && gross > 0) {
        if (discType === 'percent') {
            lineDiscount = +((gross * Math.min(discVal, 100)) / 100).toFixed(2);
        } else {
            lineDiscount = +Math.min(discVal, gross).toFixed(2);
        }
    }
    const net = +Math.max(0, gross - lineDiscount).toFixed(2);
    return { gross, lineDiscount, net };
}

window.clearSearchInput = function() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        input.focus();
    }
    loadProducts();
};

window.resetPosFilters = function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    const brandFilter = document.getElementById('brandFilter');
    if (brandFilter) brandFilter.value = '';
    const stockFilter = document.getElementById('stockFilter');
    if (stockFilter) stockFilter.value = 'all';
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.category-btn[data-category=""]')?.classList.add('active');
    loadProducts();
};

function updateFilterUiState() {
    const query = (document.getElementById('searchInput')?.value || '').trim();
    const categoryId = document.querySelector('.category-btn.active')?.dataset.category || '';
    const brandId = document.getElementById('brandFilter')?.value || '';
    const stockFilter = document.getElementById('stockFilter')?.value || 'all';

    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) clearBtn.style.display = query !== '' ? 'block' : 'none';

    const resetBtn = document.getElementById('resetFiltersBtn');
    if (resetBtn) {
        const hasFilters = query !== '' || categoryId !== '' || brandId !== '' || stockFilter !== 'all';
        resetBtn.style.display = hasFilters ? 'inline-flex' : 'none';
    }
}

function loadProducts() {
    const query = (document.getElementById('searchInput')?.value || '').trim();
    const categoryId = document.querySelector('.category-btn.active')?.dataset.category || '';
    const brandId = document.getElementById('brandFilter')?.value || '';
    const stockFilter = document.getElementById('stockFilter')?.value || 'all';
    const storeId = document.getElementById('posStoreSelect')?.value || '';

    updateFilterUiState();

    let url = `{{ route('pos.search') }}?query=${encodeURIComponent(query)}&stock_filter=${encodeURIComponent(stockFilter)}`;
    if (categoryId) url += `&category_id=${encodeURIComponent(categoryId)}`;
    if (brandId) url += `&brand_id=${encodeURIComponent(brandId)}`;
    if (storeId) url += `&store_id=${encodeURIComponent(storeId)}`;

    fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            window.posState.products = data;
            renderProducts();
        })
        .catch(err => {
            const grid = document.getElementById('productsGrid');
            if (grid) {
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding-top:60px;color:#dc2626;">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Erreur de chargement : ${escapeHtml(err.message)}</p>
                </div>`;
            }
        });
}

function renderProducts() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    const products = window.posState.products;
    if (!products.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding-top:70px;color:#94a3b8;">
            <i class="fas fa-search fa-3x mb-3" style="opacity:0.35;"></i>
            <p class="fw-semibold">Aucun produit correspondant</p>
        </div>`;
        return;
    }

    const fallbackSrc = '{{ asset("images/camera/cat_cameras.jpg") }}';
    grid.innerHTML = products.map(product => {
        const cartItem = window.posState.cart.find(i => i.product_id === product.id);
        const qtyBadge = cartItem ? `<div class="qty-badge">${cartItem.quantity}</div>` : '';

        let imgSrc = fallbackSrc;
        if (product.image) {
            if (/^(https?:)?\//.test(product.image)) imgSrc = product.image;
            else if (product.image.startsWith('images/')) imgSrc = '/' + product.image;
            else imgSrc = '/storage/' + product.image;
        }

        const isOut = product.stock <= 0;
        let stockBadge = '';
        if (isOut) {
            stockBadge = `<div class="stock-badge out-of-stock"><i class="fas fa-times-circle"></i> Épuisé</div>`;
        } else if (product.stock <= 5) {
            stockBadge = `<div class="stock-badge low-stock"><i class="fas fa-exclamation-triangle"></i> ${product.stock}</div>`;
        } else {
            stockBadge = `<div class="stock-badge in-stock"><i class="fas fa-check-circle"></i> ${product.stock}</div>`;
        }

        const hasSale = product.sale_price && parseFloat(product.sale_price) < parseFloat(product.regular_price);
        const saleBadge = hasSale ? `<div class="sale-badge">PROMO</div>` : '';
        const priceHtml = hasSale
            ? `<span class="card-price sale">${formatCurrency(product.sale_price)}</span>
               <span class="card-original-price">${formatCurrency(product.regular_price)}</span>`
            : `<span class="card-price">${formatCurrency(product.price)}</span>`;

        const selectedStoreId = document.getElementById('posStoreSelect')?.value || '';
        const storesList = Array.isArray(product.stores) ? product.stores : [];
        let storeTagsHtml = '';
        if (storesList.length > 0) {
            storeTagsHtml = `<div class="card-stores-tags">` + storesList.map(st => {
                const isSelected = selectedStoreId && String(st.id) === String(selectedStoreId);
                return `<span class="store-tag-pill ${isSelected ? 'active-store' : ''}" title="${escapeHtml(st.name)} : ${st.quantity} en stock">
                    <i class="fas fa-store" style="font-size:8px;"></i> ${escapeHtml(st.name)} (${st.quantity})
                </span>`;
            }).join('') + `</div>`;
        } else if (product.global_stock > 0) {
            storeTagsHtml = `<div class="card-stores-tags"><span class="store-tag-pill global-fallback" title="Stock global non réparti par dépôt"><i class="fas fa-cubes" style="font-size:8px;"></i> Stock global (${product.global_stock})</span></div>`;
        }

        return `
            <div class="pos-product-card ${isOut ? 'out-of-stock-card' : ''}"
                 onclick="${isOut ? '' : `addToCart(${product.id})`}"
                 title="${escapeHtml(product.name)}">
                ${qtyBadge}
                ${saleBadge}
                <div class="card-img-wrapper">
                    <img src="${imgSrc}" class="card-img" alt="${escapeHtml(product.name)}" loading="lazy" onerror="this.onerror=null;this.src='${fallbackSrc}';">
                    ${!isOut ? `<div class="overlay-add"><i class="fas fa-plus"></i></div>` : ''}
                </div>
                ${stockBadge}
                <div class="card-content">
                    <div class="card-title">${escapeHtml(product.name)}</div>
                    ${product.sku ? `<div class="card-sku">${escapeHtml(product.sku)}</div>` : ''}
                    ${storeTagsHtml}
                    <div class="card-footer">${priceHtml}</div>
                </div>
            </div>
        `;
    }).join('');
}

window.addToCart = function(productId) {
    const product = window.posState.products.find(p => p.id === productId);
    if (!product || product.stock <= 0) return;

    const existing = window.posState.cart.find(i => i.product_id === productId);
    if (existing) {
        if (existing.quantity >= product.stock) {
            Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible : ${product.stock}`, timer: 1600, showConfirmButton: false });
            return;
        }
        existing.quantity++;
    } else {
        window.posState.cart.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            stock: product.stock,
            discount_amount: 0,
            discount_type: 'percent'
        });
    }

    renderCart();
    renderProducts();
};

window.updateQuantity = function(productId, delta) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    const next = item.quantity + delta;
    if (next <= 0) {
        removeFromCart(productId);
        return;
    }
    if (next > item.stock) {
        Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible : ${item.stock}`, timer: 1600, showConfirmButton: false });
        return;
    }
    item.quantity = next;
    renderCart();
    renderProducts();
};

window.setItemQuantity = function(productId, rawVal) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;
    let qty = parseInt(rawVal, 10);
    if (isNaN(qty) || qty <= 0) {
        removeFromCart(productId);
        return;
    }
    if (qty > item.stock) {
        qty = item.stock;
        Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible : ${item.stock}`, timer: 1400, showConfirmButton: false });
    }
    item.quantity = qty;
    renderCart();
    renderProducts();
};

window.removeFromCart = function(productId) {
    window.posState.cart = window.posState.cart.filter(i => i.product_id !== productId);
    renderCart();
    renderProducts();
};

window.updateItemDiscount = function(productId, value, type) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    if (value !== undefined) item.discount_amount = Math.max(0, parseFloat(value) || 0);
    if (type !== undefined) item.discount_type = type;

    const { lineDiscount, net } = computeItemLine(item);
    const lineNetEl = document.getElementById(`cart-line-net-${productId}`);
    const lineDiscEl = document.getElementById(`cart-line-disc-${productId}`);
    if (lineNetEl) lineNetEl.textContent = formatCurrency(net);
    if (lineDiscEl) {
        lineDiscEl.textContent = lineDiscount > 0 ? `-${formatCurrency(lineDiscount)}` : '';
        lineDiscEl.style.display = lineDiscount > 0 ? 'inline-block' : 'none';
    }

    updateTotals();
};

function renderCart() {
    const container = document.getElementById('cartItems');
    const countBadge = document.getElementById('cartCount');
    const mobileBadge = document.getElementById('mobileCartBadge');
    const cart = window.posState.cart;

    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    if (countBadge) countBadge.textContent = totalItems;
    if (mobileBadge) mobileBadge.textContent = totalItems;

    if (!cart.length) {
        container.innerHTML = `<div style="text-align:center;margin-top:65px;color:#94a3b8;">
            <div style="width:60px;height:60px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="fas fa-shopping-basket" style="font-size:22px;color:#cbd5e1;"></i>
            </div>
            <h4 style="font-size:14px;font-weight:600;color:#64748b;">Panier vide</h4>
            <p style="font-size:12px;margin-top:4px;">Cliquez sur un produit pour l'ajouter</p>
        </div>`;
    } else {
        container.innerHTML = cart.map(item => {
            const { lineDiscount, net } = computeItemLine(item);
            return `
                <div class="cart-item-card">
                    <div class="cart-item-top">
                        <div>
                            <div class="cart-item-name">${escapeHtml(item.name)}</div>
                            <div style="font-size:11.5px;color:#64748b;">${formatCurrency(item.price)} / u</div>
                        </div>
                        <button type="button" class="cart-item-remove" onclick="removeFromCart(${item.product_id})" title="Retirer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="cart-item-mid">
                        <div class="item-controls">
                            <button type="button" class="control-btn" onclick="updateQuantity(${item.product_id}, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input-inline" min="1" max="${item.stock}" value="${item.quantity}" onchange="setItemQuantity(${item.product_id}, this.value)">
                            <button type="button" class="control-btn" onclick="updateQuantity(${item.product_id}, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="text-end">
                            <span id="cart-line-disc-${item.product_id}" class="badge bg-success-subtle text-success me-1" style="font-size:10.5px;display:${lineDiscount > 0 ? 'inline-block' : 'none'};">
                                ${lineDiscount > 0 ? `-${formatCurrency(lineDiscount)}` : ''}
                            </span>
                            <strong id="cart-line-net-${item.product_id}" style="font-size:13.5px;color:#0f172a;">${formatCurrency(net)}</strong>
                        </div>
                    </div>
                    <div class="cart-item-discount-row">
                        <span style="font-size:11px;color:#64748b;font-weight:600;"><i class="fas fa-percent me-1 text-danger"></i>Remise</span>
                        <div class="item-disc-inputs">
                            <input type="number" class="item-disc-input" min="0" step="0.01"
                                   value="${item.discount_amount > 0 ? item.discount_amount : ''}"
                                   placeholder="0"
                                   oninput="updateItemDiscount(${item.product_id}, this.value, undefined)">
                            <select class="item-disc-type" onchange="updateItemDiscount(${item.product_id}, undefined, this.value)">
                                <option value="percent" ${item.discount_type === 'percent' ? 'selected' : ''}>%</option>
                                <option value="fixed" ${item.discount_type === 'fixed' ? 'selected' : ''}>${window.currencyConfig.symbol}</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    updateTotals();
}

function updateTotals() {
    const cart = window.posState.cart;
    let grossSubtotal = 0;
    let itemDiscountsTotal = 0;
    let itemsNetSubtotal = 0;

    cart.forEach(item => {
        const { gross, lineDiscount, net } = computeItemLine(item);
        grossSubtotal += gross;
        itemDiscountsTotal += lineDiscount;
        itemsNetSubtotal += net;
    });

    const discountVal = parseFloat(document.getElementById('discountAmount')?.value) || 0;
    const discountType = document.getElementById('discountType')?.value || 'percent';
    let globalDiscount = 0;
    if (discountVal > 0 && itemsNetSubtotal > 0) {
        globalDiscount = discountType === 'percent'
            ? +((itemsNetSubtotal * Math.min(discountVal, 100)) / 100).toFixed(2)
            : +Math.min(discountVal, itemsNetSubtotal).toFixed(2);
    }

    const total = +Math.max(0, itemsNetSubtotal - globalDiscount).toFixed(2);
    const totalDiscount = +(itemDiscountsTotal + globalDiscount).toFixed(2);
    window.posState.currentTotal = total;

    document.getElementById('subtotal').textContent = formatCurrency(grossSubtotal);

    const totalDiscountEl = document.getElementById('totalDiscountDisplay');
    if (totalDiscountEl) {
        totalDiscountEl.textContent = totalDiscount > 0 ? '-' + formatCurrency(totalDiscount) : formatCurrency(0);
        totalDiscountEl.style.color = totalDiscount > 0 ? '#16a34a' : '#64748b';
    }
    document.getElementById('total').textContent = formatCurrency(total);

    const checkoutBtn = document.getElementById('checkoutBtn');
    const checkoutLabel = document.getElementById('checkoutBtnLabel');
    if (checkoutBtn) checkoutBtn.disabled = cart.length === 0;
    if (checkoutLabel) checkoutLabel.textContent = `Encaisser ${formatCurrency(total)}`;

    updateCashChange();
}

function updateCashChange() {
    const method = document.getElementById('paymentMethod')?.value;
    const received = parseFloat(document.getElementById('amountReceived')?.value);
    const changeRow = document.getElementById('changeRow');
    const changeEl = document.getElementById('changeAmount');
    if (!changeRow || !changeEl) return;

    if (method === 'cash' && !isNaN(received) && received >= window.posState.currentTotal && window.posState.currentTotal > 0) {
        changeRow.style.setProperty('display', 'flex', 'important');
        changeEl.textContent = formatCurrency(received - window.posState.currentTotal);
    } else {
        changeRow.style.setProperty('display', 'none', 'important');
    }
}

window.selectPaymentMethod = function(method, btn) {
    document.getElementById('paymentMethod').value = method;
    document.querySelectorAll('.payment-pill').forEach(p => p.classList.remove('active'));
    btn?.classList.add('active');

    const cashBox = document.getElementById('cashTenderedBox');
    if (cashBox) cashBox.style.display = method === 'cash' ? 'block' : 'none';
    updateCashChange();
};

window.resetCustomerFields = function() {
    document.getElementById('customerSelect').value = '';
    document.getElementById('customerId').value = '';
    document.getElementById('customerEmail').value = '';
    document.getElementById('customerName').value = 'Client de passage';
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerCity').value = '';
    document.getElementById('saveCustomerCheckbox').checked = false;
    document.getElementById('creditInfo').style.display = 'none';

    const creditOpt = document.getElementById('creditOption');
    if (creditOpt) creditOpt.disabled = true;
    if (document.getElementById('paymentMethod').value === 'credit') {
        selectPaymentMethod('cash', document.querySelector('.payment-pill[data-method="cash"]'));
    }
};

window.clearCartConfirm = function() {
    if (!window.posState.cart.length) return;
    Swal.fire({
        icon: 'question',
        title: 'Vider le panier ?',
        text: 'Tous les articles sélectionnés seront retirés.',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Oui, vider',
        cancelButtonText: 'Annuler'
    }).then(r => {
        if (r.isConfirmed) {
            window.posState.cart = [];
            renderCart();
            renderProducts();
        }
    });
};

window.checkout = function() {
    const cart = window.posState.cart;
    if (!cart.length) {
        Swal.fire({ icon: 'warning', title: 'Panier vide', text: 'Ajoutez au moins un produit.' });
        return;
    }

    const customerName = (document.getElementById('customerName')?.value || '').trim();
    const customerPhone = (document.getElementById('customerPhone')?.value || '').trim();
    const customerCity = (document.getElementById('customerCity')?.value || '').trim();
    const customerId = document.getElementById('customerId')?.value || null;
    const customerEmail = (document.getElementById('customerEmail')?.value || '').trim() || null;
    const saveCustomer = !!document.getElementById('saveCustomerCheckbox')?.checked;

    if (!customerName) {
        Swal.fire({ icon: 'warning', title: 'Nom requis', text: 'Veuillez saisir le nom du client.' });
        document.getElementById('customerName').focus();
        return;
    }

    const paymentMethod = document.getElementById('paymentMethod').value;
    const total = window.posState.currentTotal;

    if (paymentMethod === 'credit') {
        if (!customerId) {
            Swal.fire({ icon: 'warning', title: 'Client requis', text: 'Sélectionnez un client enregistré avec plafond de crédit.' });
            return;
        }
        const opt = document.getElementById('customerSelect').selectedOptions[0];
        const creditLimit = parseFloat(opt?.dataset.creditLimit) || 0;
        const balance = parseFloat(opt?.dataset.balance) || 0;
        if (creditLimit <= 0 || (creditLimit - balance) < total) {
            Swal.fire({ icon: 'error', title: 'Crédit insuffisant', text: `Disponible : ${formatCurrency(Math.max(0, creditLimit - balance))}` });
            return;
        }
    }

    const btn = document.getElementById('checkoutBtn');
    const label = document.getElementById('checkoutBtnLabel');
    btn.disabled = true;
    label.textContent = 'Traitement...';

    const payload = {
        customer_id: customerId || null,
        customer_name: customerName,
        customer_email: customerEmail,
        customer_phone: customerPhone || null,
        customer_city: customerCity || null,
        save_customer: saveCustomer,
        store_id: document.getElementById('posStoreSelect')?.value || null,
        payment_method: paymentMethod,
        amount_received: document.getElementById('amountReceived')?.value || null,
        notes: document.getElementById('orderNotes')?.value || null,
        discount_amount: parseFloat(document.getElementById('discountAmount')?.value) || 0,
        discount_type: document.getElementById('discountType')?.value || 'percent',
        items: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price,
            discount_amount: item.discount_amount || 0,
            discount_type: item.discount_type || 'percent'
        }))
    };

    fetch(`{{ route('pos.order') }}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
            let msg = data.message || `Erreur serveur (${res.status})`;
            if (data.errors && typeof data.errors === 'object') {
                const firstErr = Object.values(data.errors).flat()[0];
                if (firstErr) msg = firstErr;
            }
            Swal.fire({ icon: 'error', title: 'Erreur', text: msg });
            return;
        }

        // Update/append customer option in dropdown if saved
        if (data.customer) {
            const select = document.getElementById('customerSelect');
            let opt = select.querySelector(`option[value="${data.customer.id}"]`);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = data.customer.id;
                select.appendChild(opt);
            }
            opt.dataset.name = data.customer.name || '';
            opt.dataset.email = data.customer.email || '';
            opt.dataset.phone = data.customer.phone || '';
            opt.dataset.city = data.customer.city || '';
            opt.dataset.creditLimit = data.customer.credit_limit || 0;
            opt.dataset.balance = data.customer.current_balance || 0;
            opt.textContent = `${data.customer.name}${data.customer.phone ? ' • ' + data.customer.phone : ''}${data.customer.city ? ' (' + data.customer.city + ')' : ''}`;
        }

        // Update today's live POS KPI pill
        window.posState.todayCount += 1;
        window.posState.todayTotal += parseFloat(data.order.total || 0);
        const cntEl = document.getElementById('todaySalesCount');
        const totEl = document.getElementById('todaySalesTotal');
        if (cntEl) cntEl.textContent = window.posState.todayCount;
        if (totEl) totEl.textContent = formatCurrency(window.posState.todayTotal);

        const received = parseFloat(document.getElementById('amountReceived')?.value);
        const changeHtml = (paymentMethod === 'cash' && !isNaN(received) && received > data.order.total)
            ? `<p class="mb-1"><strong>Monnaie à rendre :</strong> <span class="text-success fw-bold">${formatCurrency(received - data.order.total)}</span></p>`
            : '';
        const savedClientHtml = (saveCustomer && data.customer)
            ? `<p class="mb-0 text-success small"><i class="fas fa-user-check me-1"></i>Client enregistré : <strong>${escapeHtml(data.customer.name)}</strong></p>`
            : '';

        Swal.fire({
            icon: 'success',
            title: 'Vente enregistrée !',
            html: `
                <div class="text-start p-2 rounded bg-light border mb-2">
                    <p class="mb-1"><strong>Commande :</strong> #${escapeHtml(data.order.order_number)}</p>
                    <p class="mb-1"><strong>Total payé :</strong> <strong class="text-danger">${formatCurrency(data.order.total)}</strong></p>
                    ${changeHtml}
                    ${savedClientHtml}
                </div>
                ${data.order.receipt_url ? `<a href="${data.order.receipt_url}" target="_blank" class="btn btn-sm btn-outline-dark w-100"><i class="fas fa-print me-1"></i> Imprimer Ticket 80mm</a>` : ''}
            `,
            showCancelButton: true,
            confirmButtonText: 'Nouvelle vente',
            cancelButtonText: 'Voir commande',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#0f172a'
        }).then(result => {
            if (result.dismiss === Swal.DismissReason.cancel && data.order.show_url) {
                window.location.href = data.order.show_url;
                return;
            }
            window.posState.cart = [];
            document.getElementById('discountAmount').value = '';
            document.getElementById('amountReceived').value = '';
            document.getElementById('orderNotes').value = '';
            resetCustomerFields();
            renderCart();
            loadProducts();
        });
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.' });
    })
    .finally(() => {
        btn.disabled = window.posState.cart.length === 0;
        label.textContent = `Encaisser ${formatCurrency(window.posState.currentTotal)}`;
    });
};

function initPosTerminal() {
    // Auto-collapse sidebar on desktop so POS catalog has maximum workspace
    if (window.innerWidth >= 992) {
        document.querySelector('.sidebar')?.classList.add('collapsed');
        document.querySelector('.main-content')?.classList.add('expanded');
    }

    const searchInput = document.getElementById('searchInput');
    let searchTimer;
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadProducts, 250);
    });
    searchInput?.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = (searchInput.value || '').trim().toLowerCase();
            const exactSkuMatch = q ? window.posState.products.find(p => (p.sku || '').toLowerCase() === q) : null;
            const targetProduct = exactSkuMatch || (window.posState.products.length === 1 ? window.posState.products[0] : null);
            if (targetProduct && targetProduct.stock > 0) {
                addToCart(targetProduct.id);
                searchInput.value = '';
                loadProducts();
            } else {
                loadProducts();
            }
        }
    });

    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadProducts();
        });
    });

    document.getElementById('posStoreSelect')?.addEventListener('change', loadProducts);
    document.getElementById('brandFilter')?.addEventListener('change', loadProducts);
    document.getElementById('stockFilter')?.addEventListener('change', loadProducts);

    document.getElementById('discountAmount')?.addEventListener('input', updateTotals);
    document.getElementById('discountType')?.addEventListener('change', updateTotals);
    document.getElementById('amountReceived')?.addEventListener('input', updateCashChange);

    document.getElementById('customerSelect')?.addEventListener('change', function() {
        if (!this.value) {
            resetCustomerFields();
            return;
        }
        const opt = this.options[this.selectedIndex];
        document.getElementById('customerId').value = this.value;
        document.getElementById('customerName').value = opt.dataset.name || '';
        document.getElementById('customerEmail').value = opt.dataset.email || '';
        document.getElementById('customerPhone').value = opt.dataset.phone || '';
        document.getElementById('customerCity').value = opt.dataset.city || '';

        const creditLimit = parseFloat(opt.dataset.creditLimit) || 0;
        const balance = parseFloat(opt.dataset.balance) || 0;
        const creditInfo = document.getElementById('creditInfo');
        const creditOpt = document.getElementById('creditOption');

        if (creditLimit > 0) {
            const avail = Math.max(0, creditLimit - balance);
            const pct = Math.min(100, (balance / creditLimit) * 100);
            creditInfo.style.display = 'block';
            document.getElementById('creditUsed').textContent = `${formatCurrency(balance)} / ${formatCurrency(creditLimit)} (Dispo: ${formatCurrency(avail)})`;
            const bar = document.getElementById('creditBar');
            bar.style.width = pct + '%';
            bar.style.background = pct > 90 ? '#dc2626' : (pct > 75 ? '#f59e0b' : '#10b981');
            if (creditOpt) creditOpt.disabled = false;
        } else {
            creditInfo.style.display = 'none';
            if (creditOpt) creditOpt.disabled = true;
            if (document.getElementById('paymentMethod').value === 'credit') {
                selectPaymentMethod('cash', document.querySelector('.payment-pill[data-method="cash"]'));
            }
        }
    });

    if (window.__posKeydownHandler) {
        document.removeEventListener('keydown', window.__posKeydownHandler);
    }
    window.__posKeydownHandler = function(e) {
        if (!document.getElementById('productsGrid')) return;
        const activeEl = document.activeElement;
        const isInput = activeEl && ['INPUT', 'SELECT', 'TEXTAREA'].includes(activeEl.tagName);
        const si = document.getElementById('searchInput');
        if (e.key === 'F2') {
            e.preventDefault();
            si?.focus();
            si?.select();
        } else if (e.key === 'F4' && !isInput) {
            e.preventDefault();
            clearCartConfirm();
        } else if (e.key === 'F9' && !isInput && window.posState.cart.length > 0) {
            e.preventDefault();
            checkout();
        }
    };
    document.addEventListener('keydown', window.__posKeydownHandler);

    document.getElementById('customerFilterInput')?.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        const select = document.getElementById('customerSelect');
        if (!select) return;
        Array.from(select.options).forEach((opt, idx) => {
            if (idx === 0) {
                opt.hidden = false;
                return;
            }
            const text = (opt.textContent + ' ' + (opt.dataset.phone || '') + ' ' + (opt.dataset.city || '')).toLowerCase();
            opt.hidden = q !== '' && !text.includes(q);
        });
    });

    refreshHeldBadge();
    renderCart();
    loadProducts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPosTerminal);
} else {
    initPosTerminal();
}
</script>
@endpush
@endsection
