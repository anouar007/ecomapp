@extends('layouts.app')

@section('title', 'Ajuster le stock - ' . $product->name)

@push('styles')
<style>
.inv-transfer-store-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
}
.inv-transfer-store-card {
    appearance: none;
    width: 100%;
    text-align: left;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 10px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.inv-transfer-store-card:hover {
    border-color: #94a3b8;
    background: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
}
.inv-transfer-store-card.is-adjust.active {
    background: #eff6ff;
    border-color: #dc2626;
    box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.18);
}
.inv-transfer-store-card.is-empty {
    opacity: 0.68;
}
.inv-transfer-store-card .inv-store-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    width: 100%;
}
.inv-transfer-store-card .inv-store-name {
    font-size: 0.8rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.inv-transfer-store-card .inv-store-check {
    display: none;
    font-size: 0.74rem;
    color: #dc2626;
    flex-shrink: 0;
}
.inv-transfer-store-card.active .inv-store-check {
    display: inline-block;
}
.inv-transfer-store-card .inv-store-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    font-size: 0.7rem;
    color: #64748b;
}
.inv-transfer-store-card .inv-store-qty {
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 9999px;
    background: #f1f5f9;
    color: #334155;
    font-variant-numeric: tabular-nums;
}
.inv-transfer-store-card.is-has-stock .inv-store-qty {
    background: #dcfce7;
    color: #166534;
}
.inv-transfer-store-card.is-empty .inv-store-qty {
    background: #fee2e2;
    color: #991b1b;
}
.inv-variant-card-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}
@media (max-width: 575.98px) {
    .inv-variant-card-grid {
        grid-template-columns: minmax(0, 1fr);
    }
}
.inv-variant-card {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px !important;
    padding: 8px 10px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
    user-select: none;
}
.inv-variant-card:hover {
    border-color: #93c5fd;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
}
.inv-variant-card.is-all-variants {
    grid-column: 1 / -1;
}
.inv-variant-card.active {
    background: #eff6ff;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.18);
}
.inv-variant-card.is-empty {
    background: #fafafa;
}
.inv-var-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.inv-var-name {
    font-size: 0.8rem;
    font-weight: 700 !important;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.inv-var-check {
    display: none;
    font-size: 0.74rem;
    color: #2563eb;
    flex-shrink: 0;
}
.inv-variant-card.active .inv-var-check {
    display: inline-block;
}
.inv-var-card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    font-size: 0.7rem;
    color: #475569;
    font-variant-numeric: tabular-nums;
}
.inv-var-qty {
    font-weight: 700 !important;
    padding: 1px 7px;
    border-radius: 999px !important;
    background: #f1f5f9;
    color: #334155;
    font-size: 0.68rem;
}
.inv-variant-card.is-has-stock .inv-var-qty {
    background: #dcfce7;
    color: #166534;
}
.inv-variant-card.is-empty .inv-var-qty {
    background: #fee2e2;
    color: #991b1b;
}
.inv-var-cost-box {
    margin-top: 3px;
    padding-top: 5px;
    border-top: 1px dashed #cbd5e1;
    cursor: default;
}
.inv-var-cost-row {
    display: flex;
    align-items: center;
    gap: 4px;
}
.inv-var-cost-input-group {
    flex: 1 1 auto;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 7px !important;
    padding: 2px 6px;
    min-height: 28px;
}
.inv-var-cost-input-group:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.12);
}
.inv-var-cost-input {
    width: 100%;
    min-width: 0;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    background: transparent !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    color: #0f172a;
    padding: 0;
    font-variant-numeric: tabular-nums;
    -moz-appearance: textfield;
}
.inv-var-cost-input::-webkit-inner-spin-button,
.inv-var-cost-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.inv-var-cost-save-btn {
    flex-shrink: 0;
    width: 26px;
    height: 26px;
    border-radius: 7px !important;
    border: 1px solid #bfdbfe;
    background: #eff6ff;
    color: #1d4ed8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 700 !important;
    cursor: pointer;
    transition: all 0.15s ease;
}
.inv-var-cost-save-btn:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}
.inv-var-cost-save-btn.is-dirty {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
}
.inv-var-benefit-line {
    margin-top: 3px;
    font-size: 0.66rem;
    font-weight: 600 !important;
    line-height: 1.25;
}
</style>
@endpush

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div>
                <h1 class="brand-title">Ajustement de Stock</h1>
                <p class="brand-subtitle">{{ $product->name }} (SKU: {{ $product->sku ?? 'SANS-SKU' }})</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-arrow-left"></i> <span>Retour inventaire</span>
            </a>
            <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-history text-primary"></i> <span>Historique de l'article</span>
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 shadow-sm mb-3">
            <i class="fas fa-exclamation-circle"></i> <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="row g-4">
        <!-- Colonne 1 : Formulaire d'Ajustement -->
        <div class="col-12 col-lg-7">
            <div class="bg-white border rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                    <h5 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                        <i class="fas fa-edit text-primary"></i>
                        <span>Nouvelle Opération de Stock</span>
                    </h5>
                    <span class="badge bg-light text-secondary border font-monospace">
                        ID #{{ $product->id }}
                    </span>
                </div>

                <!-- Carte d'impact dynamique (Live Simulation) -->
                <div class="p-3 mb-4 rounded-3 border bg-light">
                    <div class="row align-items-center text-center g-2">
                        <div class="col-4 border-end">
                            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Stock Actuel</div>
                            <div class="fw-bold text-dark fs-5 mt-0.5" id="liveCurrentStock">{{ $product->stock ?? 0 }}</div>
                            <div class="text-muted small" style="font-size: 0.68rem;">unité(s)</div>
                        </div>
                        <div class="col-4 border-end">
                            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Variation</div>
                            <div class="fw-bold fs-5 mt-0.5 text-success" id="liveDeltaStock">+1</div>
                            <div class="text-muted small" id="liveOpText" style="font-size: 0.68rem;">Entrée</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Nouveau Stock</div>
                            <div class="fw-extrabold fs-4 mt-0.5 text-success" id="liveProjectedStock">{{ ($product->stock ?? 0) + 1 }}</div>
                            <div class="text-muted small" style="font-size: 0.68rem;">estimé</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('inventory.process-adjustment', $product) }}" method="POST" id="mainAdjustmentForm">
                    @csrf

                    @php
                        $stores = $stores ?? collect();
                        $selectedStoreId = $selectedStoreId ?? null;
                        $storeStockMap = $product->storeStocks->keyBy('store_id');
                        $canManageCost = auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_profitability'));
                    @endphp

                    @if($stores->isNotEmpty())
                    <!-- Sélecteur de Magasin / Dépôt (1 clic) -->
                    <div class="mb-3.5 p-3 rounded-3 bg-light border">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            <i class="fas fa-warehouse text-danger me-1"></i>Magasin / Point de stockage ciblé (1 clic)
                        </label>
                        <select name="store_id" id="storeSelectAdjust" class="form-select d-none" onchange="onAdjustStoreChange(this)">
                            <option value="" data-stock="{{ $product->stock ?? 0 }}" data-location="" data-min="2" data-label="Stock global">
                                Stock global (Total produit : {{ $product->stock ?? 0 }} u.)
                            </option>
                            @foreach($stores as $st)
                                @php
                                    $rec = $storeStockMap->get($st->id);
                                    $stQty = (int) ($rec->quantity ?? 0);
                                    $stLoc = $rec->location_code ?? '';
                                    $stMin = (int) ($rec->min_quantity ?? 2);
                                @endphp
                                <option value="{{ $st->id }}"
                                        data-stock="{{ $stQty }}"
                                        data-location="{{ $stLoc }}"
                                        data-min="{{ $stMin }}"
                                        data-label="{{ $st->name }}"
                                        {{ (int)$selectedStoreId === (int)$st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->code }}) — {{ $stQty }} u.{{ $stLoc ? " · {$stLoc}" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @php $globalAdjQty = (int) ($product->stock ?? 0); @endphp
                        <div class="inv-transfer-store-grid" id="adjustPageStorePills">
                            <button type="button"
                                    class="inv-transfer-store-card is-adjust {{ !$selectedStoreId ? 'active' : '' }} {{ $globalAdjQty > 0 ? 'is-has-stock' : 'is-empty' }}"
                                    data-store-id=""
                                    onclick="selectStoreForAdjust('')">
                                <div class="inv-store-card-top">
                                    <span class="inv-store-name">Stock global</span>
                                    <i class="fas fa-check-circle inv-store-check"></i>
                                </div>
                                <div class="inv-store-card-meta">
                                    <span>Tous dépôts</span>
                                    <span class="inv-store-qty">{{ $globalAdjQty }} u.</span>
                                </div>
                            </button>
                            @foreach($stores as $st)
                                @php
                                    $rec = $storeStockMap->get($st->id);
                                    $stQty = (int) ($rec->quantity ?? 0);
                                @endphp
                                <button type="button"
                                        class="inv-transfer-store-card is-adjust {{ $st->has_custom_colors ? 'store-box-themed' : '' }} {{ (int) $selectedStoreId === (int) $st->id ? 'active' : '' }} {{ $stQty > 0 ? 'is-has-stock' : 'is-empty' }}"
                                        style="{{ $st->has_custom_colors ? $st->box_style : '' }}"
                                        data-store-id="{{ $st->id }}"
                                        onclick="selectStoreForAdjust({{ $st->id }})">
                                    <div class="inv-store-card-top">
                                        <span class="inv-store-name">
                                            @unless($st->has_custom_colors)
                                                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:{{ $st->resolved_badge_text_color }};margin-right:4px;"></span>
                                            @endunless
                                            {{ $st->name }}
                                        </span>
                                        <i class="fas fa-check-circle inv-store-check"></i>
                                    </div>
                                    <div class="inv-store-card-meta">
                                        <span>{{ $st->code }}</span>
                                        <span class="inv-store-qty">{{ $stQty }} u.</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                        <div class="row g-2 mt-2" id="adjustStoreMetaRow" style="{{ $selectedStoreId ? '' : 'display: none;' }}">
                            <div class="col-7">
                                <label class="form-label text-muted small mb-0.5" style="font-size: 0.72rem;">Emplacement (Rayon / Étagère)</label>
                                <input type="text" name="location_code" id="adjustLocationInput" class="form-control form-control-sm" placeholder="Ex: Rayon A - Étagère 2">
                            </div>
                            <div class="col-5">
                                <label class="form-label text-muted small mb-0.5" style="font-size: 0.72rem;">Seuil alerte site</label>
                                <input type="number" min="0" name="min_quantity" id="adjustMinQtyInput" class="form-control form-control-sm" value="2">
                            </div>
                        </div>
                        <div class="form-text small" style="font-size: 0.72rem;">
                            Ciblez un magasin ou dépôt précis pour gérer son stock et son emplacement, ou ajustez globalement.
                        </div>
                    </div>
                    @endif

                    @if($product->variants && $product->variants->isNotEmpty())
                    <!-- Sélecteur de déclinaison / variante (Petites cartes 1 clic + Coût par variante) -->
                    <div class="mb-3.5 p-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-0" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                <i class="fas fa-layer-group text-primary me-1"></i>Déclinaison / Variante ciblée (1 clic)
                            </label>
                            @if($canManageCost)
                                <button type="button"
                                        id="adjustSaveAllVariantCostsBtn"
                                        class="btn btn-xs btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold d-none"
                                        style="font-size: 0.7rem;"
                                        onclick="saveAllAdjustPageVariantCosts()">
                                    <i class="fas fa-save me-1"></i>Enregistrer coûts variantes
                                </button>
                            @endif
                        </div>

                        <select name="variant_id" id="variantSelectAdjust" class="form-select d-none" onchange="onAdjustVariantChange(this)">
                            <option value="" data-stock="{{ $product->stock ?? 0 }}" data-name="">Produit complet (Stock total : {{ $product->stock ?? 0 }})</option>
                            @foreach($product->variants as $var)
                                <option value="{{ $var->id }}"
                                        data-stock="{{ $var->stock ?? 0 }}"
                                        data-name="{{ $var->name }}"
                                        data-price="{{ (float) ($var->price ?? $product->active_price ?? 0) }}"
                                        @if($canManageCost) data-cost="{{ $var->cost_price !== null ? (float) $var->cost_price : '' }}" @endif>
                                    Variante : {{ $var->name }} (Stock : {{ $var->stock ?? 0 }} | {{ currency($var->price) }})
                                </option>
                            @endforeach
                        </select>

                        @php
                            $adjProdStock = (int) ($product->stock ?? 0);
                            $adjCurrSym = setting('currency_symbol', 'DH');
                        @endphp
                        <div id="adjustVariantScopeNote" class="text-muted mb-1.5" style="font-size: 0.7rem;">
                            <i class="fas fa-globe me-1"></i>Quantités : stock global (tous dépôts)
                        </div>
                        <div class="inv-variant-card-grid" id="adjustPageVariantCards">
                            <div role="button"
                                 tabindex="0"
                                 class="inv-variant-card is-all-variants active {{ $adjProdStock > 0 ? 'is-has-stock' : 'is-empty' }}"
                                 data-variant-id=""
                                 data-global-qty="{{ $adjProdStock }}"
                                 onclick="selectVariantForAdjust('')"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectVariantForAdjust('');}">
                                <div class="inv-var-card-top">
                                    <span class="inv-var-name">Produit complet</span>
                                    <i class="fas fa-check-circle inv-var-check"></i>
                                </div>
                                <div class="inv-var-card-meta">
                                    <span>Toutes variantes</span>
                                    <span class="inv-var-qty" data-adjust-page-var-qty>{{ $adjProdStock }} u.</span>
                                </div>
                            </div>

                            @foreach($product->variants as $var)
                                @php
                                    $varStk = (int) ($var->stock ?? 0);
                                    $varSellPrice = (float) ($var->price ?? $product->active_price ?? 0);
                                    $varCostVal = $var->cost_price !== null ? (float) $var->cost_price : null;
                                @endphp
                                <div role="button"
                                     tabindex="0"
                                     class="inv-variant-card {{ $varStk > 0 ? 'is-has-stock' : 'is-empty' }}"
                                     data-variant-id="{{ $var->id }}"
                                     data-sell-price="{{ $varSellPrice }}"
                                     data-global-qty="{{ $varStk }}"
                                     data-store-qtys='@json((object) $var->storeQuantityMap())'
                                     onclick="selectVariantForAdjust({{ $var->id }})"
                                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectVariantForAdjust({{ $var->id }});}">
                                    <div class="inv-var-card-top">
                                        <span class="inv-var-name" title="{{ $var->name }}">{{ $var->name }}</span>
                                        <i class="fas fa-check-circle inv-var-check"></i>
                                    </div>
                                    <div class="inv-var-card-meta">
                                        <span>{{ currency($varSellPrice) }}</span>
                                        <span class="inv-var-qty" data-adjust-page-var-qty>{{ $varStk }} u.</span>
                                    </div>

                                    @if($canManageCost)
                                        @php
                                            $varHasCost = $varCostVal !== null;
                                            $varBen = $varHasCost ? round($varSellPrice - $varCostVal, 2) : null;
                                            $varMrg = ($varHasCost && $varSellPrice > 0) ? round(($varBen / $varSellPrice) * 100, 1) : 0.0;
                                        @endphp
                                        <div class="inv-var-cost-box" onclick="event.stopPropagation()">
                                            <div class="inv-var-cost-row">
                                                <div class="inv-var-cost-input-group">
                                                    <input type="number"
                                                           step="0.01"
                                                           min="0"
                                                           inputmode="decimal"
                                                           name="variant_costs[{{ $var->id }}]"
                                                           class="inv-var-cost-input"
                                                           data-adjust-var-cost-input="{{ $var->id }}"
                                                           data-sell-price="{{ $varSellPrice }}"
                                                           value="{{ $varHasCost ? number_format($varCostVal, 2, '.', '') : '' }}"
                                                           placeholder="Coût/u."
                                                           onclick="event.stopPropagation()"
                                                           onfocus="this.select()"
                                                           oninput="onAdjustPageVariantCostInput({{ $var->id }}, this)">
                                                    <span class="text-muted fw-semibold" style="font-size: 0.64rem;">{{ $adjCurrSym }}</span>
                                                </div>
                                                <button type="button"
                                                        class="inv-var-cost-save-btn"
                                                        data-adjust-var-cost-save="{{ $var->id }}"
                                                        onclick="event.stopPropagation(); saveAdjustPageVariantCost({{ $var->id }}, this)"
                                                        title="Enregistrer le coût de cette variante">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                            <div class="inv-var-benefit-line" data-adjust-var-benefit-line="{{ $var->id }}">
                                                @if($varHasCost)
                                                    <span class="{{ $varBen >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Bén. {{ $varBen >= 0 ? '+' : '' }}{{ currency($varBen) }} ({{ $varMrg }}%)
                                                    </span>
                                                @else
                                                    <span class="text-muted">Coût non défini</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div id="adjustVariantWholeHint" class="mt-2 px-2 py-1.5 rounded-3 bg-warning-subtle border border-warning-subtle text-warning-emphasis" style="font-size: 0.7rem; font-weight: 600 !important;">
                            <i class="fas fa-info-circle me-1"></i>« Produit complet » n'est lié à aucune variante : choisissez une variante pour que le stock soit attribué à la bonne déclinaison.
                        </div>
                        <div class="form-text small mt-1.5" style="font-size: 0.72rem;">
                            Sélectionnez une variante en 1 clic pour ajuster son stock{{ $canManageCost ? ' et saisir son coût unitaire' : '' }}, ou choisissez « Produit complet ».
                        </div>
                    </div>
                    @endif

                    <!-- Sélecteur d'opération -->
                    <div class="mb-3.5">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            Type d'opération <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            <div class="col-4">
                                <div class="operation-pill-btn active" id="adj-pill-in" onclick="changeOp('in')">
                                    <div class="text-success fs-4 mb-1"><i class="fas fa-arrow-down"></i></div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">Entrée</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">+ Ajouter stock</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="operation-pill-btn" id="adj-pill-out" onclick="changeOp('out')">
                                    <div class="text-danger fs-4 mb-1"><i class="fas fa-arrow-up"></i></div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">Sortie</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">- Retirer stock</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="operation-pill-btn" id="adj-pill-adjustment" onclick="changeOp('adjustment')">
                                    <div class="text-primary fs-4 mb-1"><i class="fas fa-equals"></i></div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">Inventaire</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">= Valeur réelle</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="adjustment_type" id="mainAdjustmentType" value="in">
                    </div>

                    <!-- Quantité -->
                    <div class="mb-3.5">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label text-muted small fw-semibold text-uppercase mb-0" style="font-size: 0.72rem; letter-spacing: 0.04em;" id="mainQtyLabel">
                                Quantité à ajouter <span class="text-danger">*</span>
                            </label>
                            <!-- Quick Increment Chips -->
                            <div class="d-flex gap-1" id="quickChipsWrap">
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2" onclick="addChipQty(1)">+1</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2" onclick="addChipQty(5)">+5</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2" onclick="addChipQty(10)">+10</button>
                                <button type="button" class="badge bg-light text-secondary border py-1 px-2" onclick="addChipQty(50)">+50</button>
                            </div>
                        </div>
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary px-3.5" onclick="stepMainQty(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   name="quantity" 
                                   id="mainQuantityInput" 
                                   class="form-control text-center fw-bold fs-5" 
                                   value="1" 
                                   min="1" 
                                   required
                                   oninput="updateLiveCalc()">
                            <button type="button" class="btn btn-outline-secondary px-3.5" onclick="stepMainQty(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="form-text small" id="mainQtyHelp" style="font-size: 0.75rem;">
                            Unités ajoutées au stock existant.
                        </div>
                    </div>

                    @if($canManageCost)
                        @include('inventory.partials.cost-benefit-section', ['prefix' => 'adjust'])
                    @endif

                    <!-- Motif (Optionnel) -->
                    <div class="mb-3.5">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            Motif de l'ajustement (Optionnel)
                        </label>
                        <div class="d-flex flex-wrap gap-1.5 mb-2">
                            <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setMainReason('Arrivage commande fournisseur')">Arrivage fournisseur</button>
                            <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setMainReason('Retour client validé')">Retour client</button>
                            <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setMainReason('Audit et comptage inventaire physique')">Audit annuel</button>
                            <button type="button" class="badge bg-light text-secondary border py-1 px-2 text-decoration-none" onclick="setMainReason('Produit endommagé / perte constatée')">Casse / Perte</button>
                        </div>
                        <textarea name="reason" 
                                  id="mainReasonInput" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Motif optionnel (ex: Arrivage, Retour client, Audit...)"
                                  style="border-radius: 12px; font-size: 0.88rem;"></textarea>
                    </div>

                    <!-- Référence optionnelle -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            Référence du document (Optionnel)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-file-invoice"></i></span>
                            <input type="text" 
                                   name="reference" 
                                   class="form-control" 
                                   placeholder="ex: BL-2026-089, PO-4412...">
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 pt-2">
                        <button type="submit" class="btn btn-brand-primary flex-grow-1 py-2.5 fw-semibold d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-check"></i> <span>Valider l'ajustement de stock</span>
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-brand-light px-3 py-2.5 text-decoration-none">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Colonne 2 : Fiche Produit & Historique Récent -->
        <div class="col-12 col-lg-5">
            <!-- Fiche Produit -->
            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="mobile-inv-avatar flex-shrink-0" style="width: 58px; height: 58px;">
                        <img src="{{ $product->thumbnail }}" 
                             alt="{{ $product->name }}" 
                             onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">
                                {{ $product->sku ?? 'SANS-SKU' }}
                            </span>
                            <span class="text-muted small">•</span>
                            <span class="text-muted small text-truncate">
                                {{ $product->productCategory->name ?? ($product->category->name ?? 'Non classé') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="list-group list-group-flush border-top border-bottom small">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Statut du stock :</span>
                        @php
                            $stk = $product->stock ?? 0;
                            $thr = $product->low_stock_threshold ?? 10;
                            $stkClass = $stk <= 0 ? 'bg-danger' : ($stk <= $thr ? 'bg-warning text-dark' : 'bg-success');
                            $stkText = $stk <= 0 ? 'Rupture' : ($stk <= $thr ? 'Stock faible' : 'En stock');
                        @endphp
                        <span class="badge {{ $stkClass }} px-2 py-1">{{ $stkText }} ({{ $stk }} unités)</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Seuil d'alerte minimal :</span>
                        <span class="fw-semibold text-dark">{{ $thr }} unités</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Prix de vente :</span>
                        <span class="fw-bold text-dark">{{ currency($product->active_price) }}</span>
                    </div>

                    @if($canManageCost && $product->cost_price !== null && (float) $product->cost_price > 0)
                    @php
                        $adjSellPrice = (float) ($product->active_price ?? $product->price ?? 0);
                        $adjUnitBenefit = round($adjSellPrice - (float) $product->cost_price, 2);
                        $adjMarginPct = $adjSellPrice > 0 ? round(($adjUnitBenefit / $adjSellPrice) * 100, 1) : 0.0;
                    @endphp
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted"><i class="fas fa-lock text-danger me-1"></i> Prix de revient (PMP) :</span>
                        <span class="fw-semibold text-secondary">{{ currency($product->cost_price) }}</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted"><i class="fas fa-chart-line text-success me-1"></i> Bénéfice unitaire :</span>
                        <span class="fw-bold {{ $adjUnitBenefit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $adjUnitBenefit >= 0 ? '+' : '' }}{{ currency($adjUnitBenefit) }} ({{ $adjMarginPct }}%)
                        </span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted"><i class="fas fa-lock text-danger me-1"></i> Valeur actuelle en stock :</span>
                        <span class="fw-bold text-success">{{ currency($stk * $product->cost_price) }}</span>
                    </div>
                    @endif
                    @if($product->variants && $product->variants->isNotEmpty())
                    <div class="list-group-item px-0 py-2.5">
                        <div class="fw-semibold text-dark mb-1.5 d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-layer-group text-primary me-1"></i>Déclinaisons ({{ $product->variants->count() }}) :</span>
                            <span class="badge bg-light text-secondary border">Total: {{ $product->stock }} u.</span>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            @foreach($product->variants as $v)
                                <div class="d-flex align-items-center justify-content-between p-1.5 rounded bg-light border small"
                                     style="cursor: pointer;"
                                     onclick="selectVariantForAdjust({{ $v->id }})"
                                     title="Cliquer pour cibler la variante {{ $v->name }}">
                                    <div class="min-w-0 me-2">
                                        <span class="fw-medium text-dark">{{ $v->name }}</span>
                                        @if($canManageCost)
                                            <span class="text-muted {{ ($v->cost_price !== null && (float) $v->cost_price > 0) ? '' : 'd-none' }}"
                                                  id="sidebar-var-cost-{{ $v->id }}"
                                                  style="font-size: 0.68rem;">
                                                · Coût: {{ currency($v->cost_price ?? 0) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0">
                                        <span class="text-muted" style="font-size: 0.72rem;">{{ currency($v->price) }}</span>
                                        <span class="badge {{ $v->stock <= 0 ? 'bg-danger text-white' : ($v->stock <= ($v->min_stock ?: 5) ? 'bg-warning text-dark' : 'bg-primary text-white') }} px-1.5 py-0.5">
                                            {{ $v->stock }} u.
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill py-1.5" style="font-size: 0.8rem;">
                        <i class="fas fa-pen me-1"></i> Modifier la fiche produit
                    </a>
                </div>
            </div>

            @if(isset($stores) && $stores->isNotEmpty())
            <!-- Stock actuel par Magasin & Dépôt -->
            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">
                        <i class="fas fa-warehouse text-danger me-1.5"></i>Stock par Magasin &amp; Dépôt
                    </h6>
                    <a href="{{ route('stores.stock', ['search' => $product->sku ?? $product->name]) }}" class="small text-primary text-decoration-none fw-semibold">
                        Matrice <i class="fas fa-arrow-right ms-0.5"></i>
                    </a>
                </div>
                <div class="d-flex flex-column gap-2">
                    @foreach($stores as $st)
                        @php
                            $stRec = $product->storeStocks->firstWhere('store_id', $st->id);
                            $stQty = (int) ($stRec->quantity ?? 0);
                            $stLoc = $stRec->location_code ?? null;
                            $stMin = (int) ($stRec->min_quantity ?? 2);
                        @endphp
                        <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 border {{ $st->has_custom_colors ? 'store-box-themed' : 'bg-light' }}"
                             style="{{ $st->has_custom_colors ? $st->box_style : '' }}">
                            <div class="min-w-0">
                                <div class="fw-semibold text-dark store-theme-text small text-truncate">{{ $st->name }}</div>
                                <div class="text-muted store-theme-muted" style="font-size: 0.7rem;">
                                    <span class="font-monospace">{{ $st->code }}</span>
                                    @if($stLoc)
                                        · <span class="font-monospace"><i class="fas fa-map-pin me-0.5"></i>{{ $stLoc }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="badge {{ $stQty <= 0 ? 'bg-danger' : ($stQty <= $stMin ? 'bg-warning text-dark' : 'bg-success') }} rounded-pill px-2 py-1">
                                    {{ $stQty }} u.
                                </span>
                                <button type="button" class="btn btn-xs btn-light border text-dark rounded-pill px-2 py-0.5" style="font-size: 0.7rem;" onclick="selectStoreForAdjust({{ $st->id }})">
                                    Cibler
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($canManageCost)
            <!-- Lots & Arrivages FIFO de cet article (Admin / view_profitability uniquement) -->
            <div class="bg-white border rounded-4 p-4 shadow-sm mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">
                        <i class="fas fa-layer-group text-success me-1.5"></i>Lots &amp; Arrivages FIFO ({{ isset($fifoBatches) ? $fifoBatches->count() : 0 }})
                    </h6>
                    <a href="{{ route('inventory.arrivals', ['product_id' => $product->id]) }}" class="small text-primary text-decoration-none fw-semibold">
                        Vue complète <i class="fas fa-arrow-right ms-0.5"></i>
                    </a>
                </div>

                @if(isset($fifoBatches) && $fifoBatches->isNotEmpty())
                    <div class="d-flex flex-column gap-2">
                        @foreach($fifoBatches as $idx => $fBatch)
                            <div class="p-2.5 rounded-3 border bg-light bg-opacity-75">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                    <div class="d-flex align-items-center gap-1.5 min-w-0">
                                        <span class="badge bg-dark text-white rounded-pill" style="font-size: 0.64rem;">#{{ $idx + 1 }}</span>
                                        <span class="font-monospace fw-bold text-dark text-truncate" style="font-size: 0.76rem;">
                                            {{ $fBatch->reference ?? ('LOT #' . $fBatch->id) }}
                                        </span>
                                        @if($fBatch->variant)
                                            <span class="badge bg-primary-subtle text-primary" style="font-size: 0.62rem;">{{ $fBatch->variant->name }}</span>
                                        @endif
                                    </div>
                                    <span class="badge {{ $fBatch->status === 'sold_out' ? 'bg-secondary-subtle text-secondary' : ($fBatch->status === 'selling' ? 'bg-primary-subtle text-primary' : ($fBatch->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning')) }} rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                        {{ $fBatch->status_label }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.71rem;">
                                    <span>Arrivé : <strong class="text-dark">{{ $fBatch->arrived_at ? $fBatch->arrived_at->format('d/m/Y') : '—' }}</strong></span>
                                    <span>{{ $fBatch->sold_quantity }}/{{ $fBatch->initial_quantity }} vendu (Reste {{ $fBatch->remaining_quantity }})</span>
                                </div>
                                <div class="progress my-1.5" style="height: 5px;">
                                    <div class="progress-bar {{ $fBatch->status === 'sold_out' ? 'bg-secondary' : ($fBatch->status === 'selling' ? 'bg-primary' : 'bg-success') }}"
                                         style="width: {{ min(100, $fBatch->consumed_percent) }}%"></div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size: 0.71rem;">
                                    <span class="text-muted">Coût : <strong class="text-dark">{{ currency($fBatch->total_cost) }}</strong> ({{ currency($fBatch->unit_cost) }}/u)</span>
                                    <span class="{{ $fBatch->realized_profit >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                        Bén. {{ $fBatch->realized_profit >= 0 ? '+' : '' }}{{ currency($fBatch->realized_profit) }}
                                    </span>
                                </div>
                                <div class="text-muted mt-0.5" style="font-size: 0.67rem;">
                                    <i class="far fa-clock me-1"></i>{{ $fBatch->sell_duration_label }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3 text-muted small">
                        Aucun lot FIFO enregistré pour cet article.
                    </div>
                @endif
            </div>
            @endif

            <!-- Historique des 5 derniers mouvements pour cet article -->
            <div class="bg-white border rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">
                        <i class="fas fa-history text-secondary me-1.5"></i>Derniers mouvements
                    </h6>
                    <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" class="small text-primary text-decoration-none fw-semibold">
                        Voir tout <i class="fas fa-arrow-right ms-0.5"></i>
                    </a>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($product->inventoryMovements()->latest()->limit(5)->get() as $mov)
                    @php
                        $movColor = match($mov->type) {
                            'in' => 'success',
                            'out' => 'danger',
                            'adjustment' => 'warning',
                            'transfer' => 'info',
                            'return' => 'primary',
                            default => 'secondary'
                        };
                        $movSign = ($mov->type === 'in' || $mov->type === 'return') ? '+' : (($mov->type === 'out') ? '-' : '');
                    @endphp
                    <div class="list-group-item px-0 py-2.5 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="badge bg-{{ $movColor }}-subtle text-{{ $movColor }} px-2 py-0.5" style="font-size: 0.68rem;">
                                    {{ $mov->type_label }}
                                </span>
                                @if($mov->variant)
                                    <span class="badge bg-light text-primary border ms-1" style="font-size: 0.65rem;">
                                        {{ $mov->variant->name }}
                                    </span>
                                @endif
                                <div class="text-muted small mt-0.5" style="font-size: 0.72rem;">
                                    {{ $mov->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold fs-6 {{ $mov->type === 'in' ? 'text-success' : ($mov->type === 'out' ? 'text-danger' : 'text-primary') }}">
                                    {{ $movSign }}{{ $mov->quantity }} u.
                                </div>
                                <div class="text-muted small" style="font-size: 0.7rem;">
                                    {{ $mov->stock_before }} → {{ $mov->stock_after }}
                                </div>
                            </div>
                        </div>
                        @if($mov->reason)
                            <div class="text-muted small mt-1 text-truncate" style="font-size: 0.72rem;">
                                <i class="fas fa-comment-dots opacity-50 me-1"></i>{{ $mov->reason }}
                            </div>
                        @endif
                        @if($canManageCost && $mov->unit_cost !== null)
                            <div class="mt-1 d-flex flex-wrap align-items-center gap-1" style="font-size: 0.68rem;">
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    Coût : {{ currency($mov->unit_cost) }}/u (Lot : {{ currency($mov->total_cost ?? ($mov->unit_cost * $mov->quantity)) }})
                                </span>
                                @if($mov->cost_after !== null)
                                    <span class="badge bg-light text-dark border">
                                        PMP : {{ $mov->cost_before !== null ? currency($mov->cost_before) : '—' }} → {{ currency($mov->cost_after) }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-history mb-1 fs-5 d-block opacity-50"></i>
                        Aucun mouvement enregistré pour cet article.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var currentStock = {{ $product->stock ?? 0 }};
    var adjustBaseStock = {{ (int) ($product->stock ?? 0) }};
    var adjustBaseSellPrice = {{ (float) ($product->active_price ?? $product->price ?? 0) }};
    var adjustBaseCost = @json($canManageCost && $product->cost_price !== null ? (float) $product->cost_price : null);

    document.addEventListener('DOMContentLoaded', function() {
        const storeSelect = document.getElementById('storeSelectAdjust');
        if (storeSelect && storeSelect.value) {
            onAdjustStoreChange(storeSelect);
        }
        updateAdjustCostBenefit();
    });

    function selectStoreForAdjust(storeId) {
        const storeSelect = document.getElementById('storeSelectAdjust');
        if (storeSelect) {
            storeSelect.value = storeId ? String(storeId) : '';
            onAdjustStoreChange(storeSelect);
            const pillsWrap = document.getElementById('adjustPageStorePills') || storeSelect;
            pillsWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function syncAdjustPageStorePills(selectedId) {
        const activeVal = selectedId ? String(selectedId) : '';
        document.querySelectorAll('#adjustPageStorePills .inv-transfer-store-card').forEach(btn => {
            btn.classList.toggle('active', String(btn.dataset.storeId || '') === activeVal);
        });
    }

    function getAdjustSelectedStore() {
        const storeSelect = document.getElementById('storeSelectAdjust');
        if (!storeSelect || !storeSelect.value) return null;
        const opt = storeSelect.options[storeSelect.selectedIndex];
        return {
            id: String(storeSelect.value),
            qty: parseInt(opt?.dataset.stock || '0', 10) || 0,
            name: opt?.dataset.label || 'ce dépôt'
        };
    }

    // Quantity shown on a variant card: per-store when a Magasin / Point is selected, global otherwise
    function getAdjustCardQty(card, store) {
        if (!card) return 0;
        const isWhole = String(card.dataset.variantId || '') === '';
        if (!store) return parseInt(card.dataset.globalQty || '0', 10) || 0;
        if (isWhole) return store.qty;
        let map = {};
        try { map = JSON.parse(card.dataset.storeQtys || '{}') || {}; } catch (e) { map = {}; }
        return parseInt(map[store.id] ?? 0, 10) || 0;
    }

    function refreshAdjustPageVariantQtys() {
        const store = getAdjustSelectedStore();
        document.querySelectorAll('#adjustPageVariantCards .inv-variant-card').forEach(card => {
            const qty = getAdjustCardQty(card, store);
            const qtyEl = card.querySelector('[data-adjust-page-var-qty]');
            if (qtyEl) {
                qtyEl.textContent = `${qty} u.`;
                const globalQty = parseInt(card.dataset.globalQty || '0', 10) || 0;
                qtyEl.title = store ? `${qty} dans ce dépôt · ${globalQty} au total` : `${qty} au total (tous dépôts)`;
            }
            card.classList.toggle('is-empty', qty <= 0);
            card.classList.toggle('is-has-stock', qty > 0);
        });
        const note = document.getElementById('adjustVariantScopeNote');
        if (note) {
            note.innerHTML = store
                ? `<i class="fas fa-warehouse me-1"></i>Quantités dans : <strong>${String(store.name).replace(/[&<>"']/g, '')}</strong>`
                : '<i class="fas fa-globe me-1"></i>Quantités : stock global (tous dépôts)';
        }
    }

    // Current stock for the live simulator: variant-in-store, store, variant (global) or product (global)
    function computeAdjustCurrentStock() {
        const store = getAdjustSelectedStore();
        const variantSelect = document.getElementById('variantSelectAdjust');
        const variantId = variantSelect ? String(variantSelect.value || '') : '';
        if (variantId !== '') {
            const card = document.querySelector(`#adjustPageVariantCards .inv-variant-card[data-variant-id="${variantId}"]`);
            if (card) return getAdjustCardQty(card, store);
            const vOpt = variantSelect.options[variantSelect.selectedIndex];
            return parseInt(vOpt?.dataset.stock || '0', 10) || 0;
        }
        return store ? store.qty : {{ (int) ($product->stock ?? 0) }};
    }

    function applyAdjustCurrentStock() {
        currentStock = computeAdjustCurrentStock();
        const liveStockEl = document.getElementById('liveCurrentStock');
        if (liveStockEl) liveStockEl.textContent = currentStock;
        const op = document.getElementById('mainAdjustmentType').value;
        if (op === 'adjustment') {
            document.getElementById('mainQuantityInput').value = currentStock;
        }
        updateLiveCalc();
    }

    function onAdjustStoreChange(select) {
        const opt = select.options[select.selectedIndex];
        const metaRow = document.getElementById('adjustStoreMetaRow');
        const locInput = document.getElementById('adjustLocationInput');
        const minInput = document.getElementById('adjustMinQtyInput');

        syncAdjustPageStorePills(select.value);

        if (select.value && opt) {
            if (metaRow) metaRow.style.display = 'flex';
            if (locInput) locInput.value = opt.dataset.location || '';
            if (minInput) minInput.value = opt.dataset.min || 2;
        } else if (metaRow) {
            metaRow.style.display = 'none';
        }

        refreshAdjustPageVariantQtys();
        applyAdjustCurrentStock();
    }

    function selectVariantForAdjust(variantId, scrollIntoView = false) {
        const variantSelect = document.getElementById('variantSelectAdjust');
        if (!variantSelect) return;
        variantSelect.value = variantId ? String(variantId) : '';
        onAdjustVariantChange(variantSelect);
        if (scrollIntoView) {
            const cardsWrap = document.getElementById('adjustPageVariantCards') || variantSelect;
            cardsWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function syncAdjustPageVariantCards(selectedId) {
        const activeVal = selectedId ? String(selectedId) : '';
        document.querySelectorAll('#adjustPageVariantCards .inv-variant-card').forEach(card => {
            card.classList.toggle('active', String(card.dataset.variantId || '') === activeVal);
        });
        const wholeHint = document.getElementById('adjustVariantWholeHint');
        if (wholeHint) wholeHint.classList.toggle('d-none', activeVal !== '');
    }

    function onAdjustVariantChange(select) {
        syncAdjustPageVariantCards(select.value);
        applyAdjustCurrentStock();
    }

    function changeOp(op) {
        document.getElementById('mainAdjustmentType').value = op;

        // Toggle pills
        ['in', 'out', 'adjustment'].forEach(type => {
            const pill = document.getElementById('adj-pill-' + type);
            if (pill) {
                if (type === op) {
                    pill.classList.add('active');
                } else {
                    pill.classList.remove('active');
                }
            }
        });

        const qtyLabel = document.getElementById('mainQtyLabel');
        const qtyHelp = document.getElementById('mainQtyHelp');
        const qtyInput = document.getElementById('mainQuantityInput');
        const chipsWrap = document.getElementById('quickChipsWrap');

        if (op === 'in') {
            qtyLabel.innerHTML = 'Quantité à ajouter <span class="text-danger">*</span>';
            qtyHelp.textContent = "Unités ajoutées au stock existant.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
            chipsWrap.style.display = 'flex';
        } else if (op === 'out') {
            qtyLabel.innerHTML = 'Quantité à retirer <span class="text-danger">*</span>';
            qtyHelp.textContent = "Unités déduites du stock existant.";
            qtyInput.min = 1;
            if (parseInt(qtyInput.value, 10) < 1) qtyInput.value = 1;
            chipsWrap.style.display = 'flex';
        } else {
            qtyLabel.innerHTML = 'Nouveau stock réel (exact) <span class="text-danger">*</span>';
            qtyHelp.textContent = "La valeur exacte dénombrée lors du contrôle d'inventaire.";
            qtyInput.min = 0;
            qtyInput.value = currentStock;
            chipsWrap.style.display = 'none';
        }

        if (op !== 'in') {
            clearAdjustCostInput();
        }

        updateLiveCalc();
    }

    function setAdjustCostMode(mode) {
        if (window.__invCostEngine) {
            window.__invCostEngine.setMode('adjust', mode);
            updateAdjustCostBenefit();
        }
    }

    function prefillAdjustCurrentCost() {
        updateAdjustCostBenefit();
        if (window.__invCostEngine) {
            window.__invCostEngine.prefill('adjust');
        }
    }

    function clearAdjustCostInput() {
        const input = document.getElementById('adjustUnitCostInput');
        if (input) input.value = '';
        updateAdjustCostBenefit();
    }

    @if($canManageCost)
    function formatAdjustVariantBenefitHtml(sellPrice, rawCostVal) {
        const str = String(rawCostVal ?? '').trim();
        const cost = str !== '' ? parseFloat(str) : NaN;
        if (str === '' || !Number.isFinite(cost) || cost < 0) {
            return '<span class="text-muted">Coût non défini</span>';
        }
        const benefit = sellPrice - cost;
        const margin = sellPrice > 0 ? ((benefit / sellPrice) * 100).toFixed(1) : '0.0';
        const sign = benefit >= 0 ? '+' : '';
        const cls = benefit >= 0 ? 'text-success' : 'text-danger';
        const fmt = typeof window.formatInvCurrency === 'function'
            ? window.formatInvCurrency(benefit)
            : `${benefit.toFixed(2)} {{ setting('currency_symbol', 'DH') }}`;
        return `<span class="${cls}">Bén. ${sign}${fmt} (${margin}%)</span>`;
    }

    function onAdjustPageVariantCostInput(variantId, inputEl) {
        const card = inputEl ? inputEl.closest('.inv-variant-card') : null;
        const sellPrice = parseFloat(inputEl?.dataset.sellPrice || card?.dataset.sellPrice || '0') || 0;
        const benefitEl = document.querySelector(`[data-adjust-var-benefit-line="${variantId}"]`);
        if (benefitEl) {
            benefitEl.innerHTML = formatAdjustVariantBenefitHtml(sellPrice, inputEl ? inputEl.value : '');
        }
        const saveBtn = document.querySelector(`[data-adjust-var-cost-save="${variantId}"]`);
        if (saveBtn) saveBtn.classList.add('is-dirty');
        const saveAllBtn = document.getElementById('adjustSaveAllVariantCostsBtn');
        if (saveAllBtn) saveAllBtn.classList.remove('d-none');
    }

    async function persistAdjustPageVariantCosts(costsMap, triggerBtn = null) {
        if (!costsMap || Object.keys(costsMap).length === 0) return;
        const origHtml = triggerBtn ? triggerBtn.innerHTML : '';
        if (triggerBtn) {
            triggerBtn.disabled = true;
            triggerBtn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:11px;height:11px;"></span>';
        }

        try {
            const resp = await fetch(@json(route('inventory.update-variant-costs', $product)), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    variant_costs: costsMap
                })
            });
            const data = await resp.json();
            if (!resp.ok || !data.success) {
                let msg = data.message || 'Impossible d\'enregistrer les coûts des variantes.';
                if (data.errors) msg = Object.values(data.errors).flat().join(' ');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Erreur', text: msg });
                } else {
                    alert(msg);
                }
                return;
            }

            if ('cost_price' in data) {
                adjustBaseCost = data.cost_price !== null ? parseFloat(data.cost_price) : null;
            }

            const variantSelect = document.getElementById('variantSelectAdjust');
            (data.variants || []).forEach(v => {
                const costStr = (v.cost_price !== null && v.cost_price !== undefined)
                    ? Number(v.cost_price).toFixed(2)
                    : '';

                if (variantSelect) {
                    const opt = variantSelect.querySelector(`option[value="${v.id}"]`);
                    if (opt) opt.dataset.cost = costStr;
                }

                const inp = document.querySelector(`[data-adjust-var-cost-input="${v.id}"]`);
                if (inp) {
                    inp.value = costStr;
                }

                const saveBtn = document.querySelector(`[data-adjust-var-cost-save="${v.id}"]`);
                if (saveBtn) saveBtn.classList.remove('is-dirty');

                const sellPrice = parseFloat(v.price ?? inp?.dataset.sellPrice ?? adjustBaseSellPrice) || 0;
                const benefitEl = document.querySelector(`[data-adjust-var-benefit-line="${v.id}"]`);
                if (benefitEl) {
                    benefitEl.innerHTML = formatAdjustVariantBenefitHtml(sellPrice, costStr);
                }

                const sideCostEl = document.getElementById(`sidebar-var-cost-${v.id}`);
                if (sideCostEl) {
                    if (costStr !== '' && parseFloat(costStr) > 0) {
                        const fmtCost = typeof window.formatInvCurrency === 'function'
                            ? window.formatInvCurrency(parseFloat(costStr))
                            : `${costStr} {{ setting('currency_symbol', 'DH') }}`;
                        sideCostEl.textContent = `· Coût: ${fmtCost}`;
                        sideCostEl.classList.remove('d-none');
                    } else {
                        sideCostEl.classList.add('d-none');
                    }
                }
            });

            const saveAllBtn = document.getElementById('adjustSaveAllVariantCostsBtn');
            if (saveAllBtn) saveAllBtn.classList.add('d-none');

            updateAdjustCostBenefit();

            const successMsg = data.message || 'Coût des variantes enregistré avec succès.';
            if (window.Toast) {
                window.Toast.fire({ icon: 'success', title: successMsg });
            }
        } catch (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.' });
            } else {
                alert('Erreur réseau lors de l\'enregistrement des coûts.');
            }
        } finally {
            if (triggerBtn) {
                triggerBtn.disabled = false;
                triggerBtn.innerHTML = origHtml;
            }
        }
    }

    function saveAdjustPageVariantCost(variantId, btnEl) {
        const inp = document.querySelector(`[data-adjust-var-cost-input="${variantId}"]`);
        if (!inp) return;
        const costsMap = {};
        costsMap[String(variantId)] = inp.value !== '' ? inp.value : null;
        persistAdjustPageVariantCosts(costsMap, btnEl);
    }

    function saveAllAdjustPageVariantCosts() {
        const costsMap = {};
        document.querySelectorAll('#adjustPageVariantCards [data-adjust-var-cost-input]').forEach(inp => {
            const vid = inp.dataset.adjustVarCostInput;
            if (vid) costsMap[String(vid)] = inp.value !== '' ? inp.value : null;
        });
        const btn = document.getElementById('adjustSaveAllVariantCostsBtn');
        persistAdjustPageVariantCosts(costsMap, btn);
    }
    @endif

    function updateAdjustCostBenefit() {
        if (!window.__invCostEngine) return;
        const op = document.getElementById('mainAdjustmentType')?.value || 'in';
        const addedQty = Math.max(0, parseInt(document.getElementById('mainQuantityInput')?.value || '0', 10) || 0);
        const variantSelect = document.getElementById('variantSelectAdjust');
        const vOpt = (variantSelect && variantSelect.value) ? variantSelect.options[variantSelect.selectedIndex] : null;

        let sellingPrice = adjustBaseSellPrice;
        let existingCost = adjustBaseCost;
        let existingStock = adjustBaseStock;
        let variantName = '';

        if (vOpt) {
            variantName = vOpt.dataset.name || '';
            const vPrice = parseFloat(vOpt.dataset.price || '0');
            if (Number.isFinite(vPrice) && vPrice > 0) {
                sellingPrice = vPrice;
            }
            existingCost = (vOpt.dataset.cost !== undefined && vOpt.dataset.cost !== '')
                ? parseFloat(vOpt.dataset.cost)
                : null;
            existingStock = parseInt(vOpt.dataset.stock || '0', 10) || 0;
        }

        window.__invCostEngine.recalc('adjust', {
            op,
            addedQty,
            sellingPrice,
            existingCost,
            existingStock,
            variantName
        });
    }

    function stepMainQty(delta) {
        const input = document.getElementById('mainQuantityInput');
        let current = parseInt(input.value, 10) || 0;
        const minVal = parseInt(input.min, 10) || 0;
        let next = current + delta;
        if (next < minVal) next = minVal;
        input.value = next;
        updateLiveCalc();
    }

    function addChipQty(amount) {
        const input = document.getElementById('mainQuantityInput');
        let current = parseInt(input.value, 10) || 0;
        input.value = current + amount;
        updateLiveCalc();
    }

    function updateLiveCalc() {
        const op = document.getElementById('mainAdjustmentType').value;
        const qty = parseInt(document.getElementById('mainQuantityInput').value, 10) || 0;
        const deltaEl = document.getElementById('liveDeltaStock');
        const opTextEl = document.getElementById('liveOpText');
        const projEl = document.getElementById('liveProjectedStock');

        let projected = currentStock;
        if (op === 'in') {
            projected = currentStock + qty;
            deltaEl.className = 'fw-bold fs-5 mt-0.5 text-success';
            deltaEl.textContent = '+' + qty;
            opTextEl.textContent = 'Entrée';
            projEl.className = 'fw-extrabold fs-4 mt-0.5 text-success';
        } else if (op === 'out') {
            projected = currentStock - qty;
            deltaEl.className = 'fw-bold fs-5 mt-0.5 text-danger';
            deltaEl.textContent = '-' + qty;
            opTextEl.textContent = 'Sortie';
            projEl.className = projected < 0 ? 'fw-extrabold fs-4 mt-0.5 text-danger' : 'fw-extrabold fs-4 mt-0.5 text-warning';
        } else {
            projected = qty;
            const diff = qty - currentStock;
            deltaEl.className = diff >= 0 ? 'fw-bold fs-5 mt-0.5 text-primary' : 'fw-bold fs-5 mt-0.5 text-warning';
            deltaEl.textContent = (diff >= 0 ? '+' : '') + diff;
            opTextEl.textContent = 'Ajustement';
            projEl.className = 'fw-extrabold fs-4 mt-0.5 text-primary';
        }

        projEl.textContent = projected;
        updateAdjustCostBenefit();
    }

    function setMainReason(text) {
        const input = document.getElementById('mainReasonInput');
        input.value = text;
        input.focus();
    }
</script>
@endpush
