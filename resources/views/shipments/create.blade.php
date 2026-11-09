@extends('layouts.app')

@section('title', 'Enregistrer un Nouvel Arrivage & Coûts (Admin)')

@push('styles')
<style>
.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    max-height: 280px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
}
.search-result-item {
    padding: 0.65rem 0.85rem;
    cursor: pointer;
    transition: background-color 0.15s ease;
    border-bottom: 1px solid #f1f5f9;
}
.search-result-item:hover {
    background-color: #f8fafc;
}
.prod-thumb-row {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Header Navigation Bar -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i> Retour aux arrivages
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">Nouvel Arrivage & Coûts de Marchandises</h1>
            <p class="text-muted small mb-0">Enregistrez les quantités reçues, les coûts unitaires d'achat et ventilez les frais de douane et transport.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="submit" form="shipmentForm" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="fas fa-check-circle me-1.5"></i> Valider & Enregistrer l'Arrivage
            </button>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-xs mb-4">
        <ul class="mb-0 small fw-semibold">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('shipments.store') }}" id="shipmentForm">
        @csrf

        <div class="row g-4">
            
            <!-- Left Column: Form Details & Items -->
            <div class="col-12 col-xl-8">
                
                <!-- 1. General Shipment Information Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-3.5">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Informations Générales de l'Arrivage</h6>
                        </div>
                    </div>
                    <div class="card-body p-3.5">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">N° Référence <span class="text-danger">*</span></label>
                                <input type="text" name="reference" value="{{ old('reference', $nextReference) }}" class="form-control rounded-3 font-monospace fw-bold" required>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Fournisseur <span class="text-danger">*</span></label>
                                <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" class="form-control rounded-3 fw-semibold" placeholder="ex: Sony Maroc, DJI, Godox" required>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Date de Réception <span class="text-danger">*</span></label>
                                <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" class="form-control rounded-3" required>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Facture Fournisseur N°</label>
                                <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" class="form-control rounded-3" placeholder="ex: FAC-2026-981">
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">N° Suivi / Tracking</label>
                                <input type="text" name="tracking_number" value="{{ old('tracking_number') }}" class="form-control rounded-3" placeholder="ex: DHL-87129381">
                            </div>

                            <div class="col-12 col-sm-6 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Dépôt / Magasin Cible</label>
                                <select name="store_id" class="form-select rounded-3">
                                    <option value="">Dépôt Central Principal</option>
                                    @foreach($stores as $st)
                                        <option value="{{ $st->id }}" {{ old('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Extra Landing Fees (Shipping, Customs, Transit) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-plane-departure"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Frais d'Approche (Transport, Douane & Transit)</h6>
                                <span class="text-muted small">Ces frais seront répartis automatiquement sur le coût de revient unitaire de chaque article.</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3.5">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Frais de Transport / Fret (DH)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="shipping_cost" id="extraShippingCost" value="{{ old('shipping_cost', 0) }}" class="form-control rounded-start-3 fw-bold" oninput="recomputeShipmentTotals()">
                                    <span class="input-group-text bg-light text-muted fw-semibold">DH</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Frais de Douane / Transit (DH)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="customs_cost" id="extraCustomsCost" value="{{ old('customs_cost', 0) }}" class="form-control rounded-start-3 fw-bold" oninput="recomputeShipmentTotals()">
                                    <span class="input-group-text bg-light text-muted fw-semibold">DH</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-secondary mb-1">Autres Frais Annexes (DH)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="other_fees" id="extraOtherFees" value="{{ old('other_fees', 0) }}" class="form-control rounded-start-3 fw-bold" oninput="recomputeShipmentTotals()">
                                    <span class="input-group-text bg-light text-muted fw-semibold">DH</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Shipment Items Table (Dynamic) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-3.5">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="fas fa-boxes-stacked"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-dark">Articles de l'Arrivage (<span id="itemsCountLabel">0</span>)</h6>
                            </div>

                            <!-- Product Live Search Input -->
                            <div class="position-relative" style="width: 320px; max-width: 100%;">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="productSearchInput" class="form-control bg-light border-start-0" placeholder="Chercher un article à ajouter..." autocomplete="off">
                                </div>
                                <div class="search-results-dropdown d-none" id="searchResultsDropdown"></div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0" id="shipmentItemsTable">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 32%;">Article</th>
                                    <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 12%;">Quantité</th>
                                    <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 16%;">Prix Achat Fournisseur</th>
                                    <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 16%;">Coût Rendu Unitaire</th>
                                    <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 16%;">Marge Estimée</th>
                                    <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 8%;">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" id="itemsTableBody">
                                <tr id="emptyRow">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                        <p class="mb-0 fw-semibold">Aucun article ajouté. Utilisez la barre de recherche ci-dessus pour ajouter des produits à cet arrivage.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Column: Summary & Options -->
            <div class="col-12 col-xl-4">
                
                <!-- Financial Summary Box -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="card-header bg-dark text-white p-3.5 rounded-top-4">
                        <h6 class="fw-bold mb-0">Récapitulatif Financier de l'Arrivage</h6>
                    </div>
                    <div class="card-body p-3.5 space-y-3">
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span>Articles différents :</span>
                            <strong class="text-dark" id="summaryDistinctCount">0</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span>Total unités physiques :</span>
                            <strong class="text-dark" id="summaryTotalUnits">0</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span>Total Achat Fournisseur :</span>
                            <strong class="text-dark fs-6" id="summaryItemsCost">0,00 DH</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span>Frais d'approche (fret/douane) :</span>
                            <span class="text-info fw-semibold" id="summaryExtraFees">0,00 DH</span>
                        </div>
                        
                        <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-dark fs-6">Coût Total Rendu :</span>
                            <span class="fw-bold text-danger fs-5" id="summaryTotalLanded">0,00 DH</span>
                        </div>

                        <div class="p-2.5 rounded-3 bg-light border mt-2">
                            <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                <span>Marge Brute Moyenne Estimée :</span>
                                <strong class="text-success fs-6" id="summaryAvgMargin">0%</strong>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 99px;">
                                <div class="progress-bar bg-success" id="summaryMarginBar" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>

                        <!-- Options Checkboxes -->
                        <div class="pt-3 border-top space-y-2">
                            <label class="form-label small fw-bold text-secondary mb-1">Statut de l'arrivage :</label>
                            <select name="status" class="form-select form-select-sm rounded-3 fw-bold mb-3">
                                <option value="received" selected>Reçu en stock (Disponible)</option>
                                <option value="in_transit">En cours d'acheminement (Transit)</option>
                                <option value="draft">Brouillon (En préparation)</option>
                            </select>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="update_catalog_costs" value="1" id="updateCostsSwitch" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="updateCostsSwitch">
                                    Mettre à jour le coût d'achat catalogue du produit avec ce lot
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="stock_applied" value="1" id="stockAppliedSwitch" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="stockAppliedSwitch">
                                    Incrémenter le stock physique immédiatement
                                </label>
                            </div>
                        </div>

                        <div class="pt-3 border-top">
                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                                <i class="fas fa-check me-1.5"></i> Enregistrer l'Arrivage
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>
let addedItems = [];

const searchInput = document.getElementById('productSearchInput');
const dropdown = document.getElementById('searchResultsDropdown');
let searchDebounce = null;

searchInput.addEventListener('input', function() {
    clearTimeout(searchDebounce);
    const q = this.value.trim();
    if (q.length < 1) {
        dropdown.classList.add('d-none');
        return;
    }

    searchDebounce = setTimeout(() => {
        fetch(`/shipments/search-products?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(products => {
                if (products.length === 0) {
                    dropdown.innerHTML = '<div class="p-3 text-muted text-center small">Aucun produit trouvé.</div>';
                    dropdown.classList.remove('d-none');
                    return;
                }

                let html = '';
                products.forEach(p => {
                    html += `
                        <div class="search-result-item d-flex align-items-center gap-2" onclick="addProductToShipment(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                            <img src="${p.image_url}" class="prod-thumb-row" alt="">
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-bold text-dark text-truncate small">${p.name}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    SKU: <span class="font-monospace">${p.sku}</span> • Vente: <strong>${p.selling_price_formatted}</strong> • Stock: ${p.stock}
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-0 text-primary small">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    `;
                });
                dropdown.innerHTML = html;
                dropdown.classList.remove('d-none');
            });
    }, 200);
});

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target) && e.target !== searchInput) {
        dropdown.classList.add('d-none');
    }
});

function addProductToShipment(product) {
    dropdown.classList.add('d-none');
    searchInput.value = '';

    // Check if already in table
    const existing = addedItems.find(i => i.id === product.id);
    if (existing) {
        existing.quantity += 1;
        document.getElementById(`qty-input-${product.id}`).value = existing.quantity;
        recomputeShipmentTotals();
        return;
    }

    addedItems.push({
        id: product.id,
        name: product.name,
        sku: product.sku,
        image_url: product.image_url,
        selling_price: product.selling_price,
        unit_cost: product.current_cost > 0 ? product.current_cost : 0,
        quantity: 1,
    });

    renderItemsTable();
}

function removeItem(productId) {
    addedItems = addedItems.filter(i => i.id !== productId);
    renderItemsTable();
}

function handleItemQtyChange(productId, val) {
    const item = addedItems.find(i => i.id === productId);
    if (item) {
        item.quantity = Math.max(1, parseInt(val) || 1);
        recomputeShipmentTotals();
    }
}

function handleItemCostChange(productId, val) {
    const item = addedItems.find(i => i.id === productId);
    if (item) {
        item.unit_cost = Math.max(0, parseFloat(val) || 0);
        recomputeShipmentTotals();
    }
}

function renderItemsTable() {
    const tbody = document.getElementById('itemsTableBody');
    const emptyRow = document.getElementById('emptyRow');

    if (addedItems.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0 fw-semibold">Aucun article ajouté. Utilisez la barre de recherche ci-dessus pour ajouter des produits à cet arrivage.</p>
                </td>
            </tr>
        `;
        recomputeShipmentTotals();
        return;
    }

    let html = '';
    addedItems.forEach((item, index) => {
        html += `
            <tr id="item-row-${item.id}">
                <td class="ps-3 py-3">
                    <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                    <div class="d-flex align-items-center gap-2.5">
                        <img src="${item.image_url}" class="prod-thumb-row shadow-xs" alt="">
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-truncate small" title="${item.name}">${item.name}</div>
                            <div class="text-muted font-monospace" style="font-size: 0.7rem;">SKU: ${item.sku}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 text-center">
                    <input type="number" min="1" step="1" name="items[${index}][quantity]" id="qty-input-${item.id}"
                           value="${item.quantity}" class="form-control form-control-sm text-center fw-bold mx-auto" style="width: 75px;"
                           oninput="handleItemQtyChange(${item.id}, this.value)">
                </td>
                <td class="py-3 text-end">
                    <div class="input-group input-group-sm ms-auto" style="width: 130px;">
                        <input type="number" step="0.01" min="0" name="items[${index}][unit_cost]" id="cost-input-${item.id}"
                               value="${item.unit_cost > 0 ? item.unit_cost : ''}" placeholder="0.00"
                               class="form-control text-end fw-bold"
                               oninput="handleItemCostChange(${item.id}, this.value)">
                        <span class="input-group-text bg-light text-muted fw-semibold">DH</span>
                    </div>
                </td>
                <td class="py-3 text-end">
                    <div class="fw-bold text-dark small" id="landed-cost-${item.id}">0,00 DH</div>
                    <div class="text-muted" style="font-size: 0.68rem;" id="allocated-fee-${item.id}">+0,00 DH frais</div>
                </td>
                <td class="py-3 text-end">
                    <div class="fw-bold text-success small" id="margin-dh-${item.id}">0,00 DH</div>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" id="margin-pct-${item.id}" style="font-size: 0.68rem;">0%</span>
                </td>
                <td class="pe-3 py-3 text-center">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger" style="width: 28px; height: 28px; padding: 0;" onclick="removeItem(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    recomputeShipmentTotals();
}

function recomputeShipmentTotals() {
    const shipping = parseFloat(document.getElementById('extraShippingCost').value) || 0;
    const customs = parseFloat(document.getElementById('extraCustomsCost').value) || 0;
    const other = parseFloat(document.getElementById('extraOtherFees').value) || 0;
    const totalExtraFees = shipping + customs + other;

    let totalRawItemsCost = 0;
    let totalUnits = 0;

    addedItems.forEach(item => {
        totalRawItemsCost += item.quantity * item.unit_cost;
        totalUnits += item.quantity;
    });

    const totalLandedCost = totalRawItemsCost + totalExtraFees;

    let totalProjectedSelling = 0;

    // Recalculate each item landed cost
    addedItems.forEach(item => {
        const itemRawSubtotal = item.quantity * item.unit_cost;
        const itemFeeTotal = (totalRawItemsCost > 0) ? (itemRawSubtotal / totalRawItemsCost) * totalExtraFees : 0;
        const itemFeePerUnit = item.quantity > 0 ? (itemFeeTotal / item.quantity) : 0;
        const landedUnitCost = item.unit_cost + itemFeePerUnit;

        const profitDh = item.selling_price - landedUnitCost;
        const marginPct = item.selling_price > 0 ? ((profitDh / item.selling_price) * 100).toFixed(1) : 0;

        totalProjectedSelling += item.quantity * item.selling_price;

        const landedCostEl = document.getElementById(`landed-cost-${item.id}`);
        const allocatedFeeEl = document.getElementById(`allocated-fee-${item.id}`);
        const marginDhEl = document.getElementById(`margin-dh-${item.id}`);
        const marginPctEl = document.getElementById(`margin-pct-${item.id}`);

        if (landedCostEl) {
            landedCostEl.textContent = formatDh(landedUnitCost);
            allocatedFeeEl.textContent = `+${formatDh(itemFeePerUnit)} frais`;
            marginDhEl.textContent = formatDh(profitDh);
            marginDhEl.className = profitDh >= 0 ? 'fw-bold text-success small' : 'fw-bold text-danger small';
            marginPctEl.textContent = `${marginPct}%`;
            marginPctEl.className = `badge ${marginPct >= 30 ? 'bg-success-subtle text-success' : (marginPct >= 15 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning')} rounded-pill px-2 py-0.5`;
        }
    });

    // Update Summary Box
    document.getElementById('itemsCountLabel').textContent = addedItems.length;
    document.getElementById('summaryDistinctCount').textContent = addedItems.length;
    document.getElementById('summaryTotalUnits').textContent = totalUnits;
    document.getElementById('summaryItemsCost').textContent = formatDh(totalRawItemsCost);
    document.getElementById('summaryExtraFees').textContent = formatDh(totalExtraFees);
    document.getElementById('summaryTotalLanded').textContent = formatDh(totalLandedCost);

    const overallMarginPct = totalProjectedSelling > 0 ? (((totalProjectedSelling - totalLandedCost) / totalProjectedSelling) * 100).toFixed(1) : 0;
    document.getElementById('summaryAvgMargin').textContent = `${overallMarginPct}%`;
    document.getElementById('summaryMarginBar').style.width = `${Math.min(100, Math.max(0, overallMarginPct))}%`;
}

function formatDh(num) {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num) + ' DH';
}
</script>
@endpush
@endsection
