@extends('layouts.app')

@section('title', 'Ajuster le stock - ' . $product->name)

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

                    <!-- Motif -->
                    <div class="mb-3.5">
                        <label class="form-label text-muted small fw-semibold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                            Motif de l'ajustement <span class="text-danger">*</span>
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
                                  required 
                                  placeholder="Détaillez la raison pour assurer l'audit trail..."
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
                        <span class="fw-bold text-dark">{{ currency($product->price) }}</span>
                    </div>

                    @if($product->cost_price)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Prix de revient / d'achat :</span>
                        <span class="fw-semibold text-secondary">{{ currency($product->cost_price) }}</span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Valeur actuelle en stock :</span>
                        <span class="fw-bold text-success">{{ currency($stk * $product->cost_price) }}</span>
                    </div>
                    @endif
                </div>

                <div class="mt-3 text-center">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill py-1.5" style="font-size: 0.8rem;">
                        <i class="fas fa-pen me-1"></i> Modifier la fiche produit
                    </a>
                </div>
            </div>

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
    const currentStock = {{ $product->stock ?? 0 }};

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

        updateLiveCalc();
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
    }

    function setMainReason(text) {
        const input = document.getElementById('mainReasonInput');
        input.value = text;
        input.focus();
    }
</script>
@endpush
