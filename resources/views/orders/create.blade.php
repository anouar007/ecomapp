@extends('layouts.app')

@section('title', 'Créer une nouvelle commande')

@section('content')
    <!-- En-tête de la page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Commandes</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold">Créer une commande</span>
                </div>
                <h1 class="brand-title">Nouvelle commande</h1>
                <p class="brand-subtitle">
                    Créez une commande manuelle avec sélection de produits et calcul automatique des totaux
                </p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                <i class="fas fa-arrow-left"></i> <span>Retour aux commandes</span>
            </a>
        </div>
    </div>

    <!-- Alertes Erreurs -->
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-exclamation-circle fs-5 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-1">Veuillez corriger les erreurs suivantes :</h6>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" id="createOrderForm">
        @csrf

        <div class="row g-4">
            <!-- Colonne Principale (Gauche) -->
            <div class="col-12 col-lg-8">
                <!-- Section 1 : Informations Client & Expédition -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-user-circle text-primary"></i> 1. Coordonnées Client & Livraison
                            </h2>
                            <p class="product-form-card-subtitle">Informations du destinataire et adresse de livraison</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="customer_name" class="form-label fw-semibold text-dark">
                                Nom complet du client <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="far fa-user"></i>
                                </span>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       class="form-control @error('customer_name') is-invalid @enderror" 
                                       value="{{ old('customer_name') }}" 
                                       placeholder="ex: Mohammed Alami" 
                                       required>
                            </div>
                            @error('customer_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="customer_email" class="form-label fw-semibold text-dark">
                                Adresse E-mail <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="far fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       id="customer_email" 
                                       name="customer_email" 
                                       class="form-control @error('customer_email') is-invalid @enderror" 
                                       value="{{ old('customer_email') }}" 
                                       placeholder="ex: client@exemple.com" 
                                       required>
                            </div>
                            @error('customer_email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="customer_phone" class="form-label fw-semibold text-dark">
                                Téléphone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       id="customer_phone" 
                                       name="customer_phone" 
                                       class="form-control @error('customer_phone') is-invalid @enderror" 
                                       value="{{ old('customer_phone') }}" 
                                       placeholder="ex: +212 600 000 000">
                            </div>
                            @error('customer_phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="shipping_city" class="form-label fw-semibold text-dark">
                                Ville <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-city"></i>
                                </span>
                                <input type="text" 
                                       id="shipping_city" 
                                       name="shipping_city" 
                                       class="form-control @error('shipping_city') is-invalid @enderror" 
                                       value="{{ old('shipping_city', 'Casablanca') }}" 
                                       placeholder="ex: Casablanca, Rabat, Marrakech" 
                                       required>
                            </div>
                            @error('shipping_city')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="shipping_address" class="form-label fw-semibold text-dark">
                                Adresse complète de livraison <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <input type="text" 
                                       id="shipping_address" 
                                       name="shipping_address" 
                                       class="form-control @error('shipping_address') is-invalid @enderror" 
                                       value="{{ old('shipping_address') }}" 
                                       placeholder="ex: 123 Boulevard d'Anfa, Quartier Racine" 
                                       required>
                            </div>
                            @error('shipping_address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="shipping_zip" class="form-label fw-semibold text-dark">
                                Code Postal
                            </label>
                            <input type="text" 
                                   id="shipping_zip" 
                                   name="shipping_zip" 
                                   class="form-control" 
                                   value="{{ old('shipping_zip', '20000') }}" 
                                   placeholder="20000">
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="shipping_country" class="form-label fw-semibold text-dark">
                                Pays
                            </label>
                            <input type="text" 
                                   id="shipping_country" 
                                   name="shipping_country" 
                                   class="form-control" 
                                   value="{{ old('shipping_country', 'Maroc') }}" 
                                   placeholder="Maroc">
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Sélection des Articles -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h2 class="product-form-card-title mb-0">
                                <i class="fas fa-boxes-stacked text-danger"></i> 2. Articles commandés <span class="text-danger">*</span>
                            </h2>
                            <p class="product-form-card-subtitle mb-0">Sélectionnez chaque produit ainsi que son magasin / dépôt de prélèvement</p>
                        </div>
                        @if(isset($stores) && $stores->isNotEmpty())
                            <div class="d-flex align-items-center gap-2">
                                <label for="default_store_id" class="small fw-semibold text-secondary mb-0 text-nowrap">
                                    <i class="fas fa-warehouse text-primary me-1"></i> Dépôt par défaut :
                                </label>
                                <select id="default_store_id" name="default_store_id" class="form-select form-select-sm" style="min-width: 190px;" onchange="applyDefaultStoreToPicker()">
                                    <option value="">— Auto / À affecter —</option>
                                    @foreach($stores as $st)
                                        <option value="{{ $st->id }}" {{ old('default_store_id', $defaultStore?->id) == $st->id ? 'selected' : '' }}>
                                            {{ $st->name }} ({{ $st->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Sélecteur rapide de produit + dépôt -->
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label for="product_picker" class="form-label small fw-semibold text-dark mb-1">
                                    Choisir un produit du catalogue
                                </label>
                                <select id="product_picker" class="form-select" onchange="onProductPickerChange()">
                                    <option value="">-- Sélectionnez un produit --</option>
                                    @foreach($products as $prod)
                                        @if($prod->variants && $prod->variants->isNotEmpty())
                                            @foreach($prod->variants as $variant)
                                                <option value="{{ $prod->id }}" 
                                                        data-variant-id="{{ $variant->id }}"
                                                        data-variant-name="{{ $variant->name }}"
                                                        data-name="{{ $prod->name }} ({{ $variant->name }})" 
                                                        data-price="{{ $variant->price }}" 
                                                        data-sku="{{ $variant->sku ?: $prod->sku }}" 
                                                        data-stock="{{ $variant->stock }}"
                                                        data-thumb="{{ $variant->image_url ?: $prod->thumbnail }}">
                                                    {{ $prod->name }} — {{ $variant->name }} &bull; {{ currency($variant->price) }} (Stock: {{ $variant->stock }})
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="{{ $prod->id }}" 
                                                    data-variant-id=""
                                                    data-variant-name=""
                                                    data-name="{{ $prod->name }}" 
                                                    data-price="{{ $prod->price }}" 
                                                    data-sku="{{ $prod->sku }}" 
                                                    data-stock="{{ $prod->stock }}"
                                                    data-thumb="{{ $prod->thumbnail }}">
                                                {{ $prod->name }} &bull; {{ currency($prod->price) }} (Stock: {{ $prod->stock }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="store_picker" class="form-label small fw-semibold text-dark mb-1">
                                    <i class="fas fa-store text-primary me-1"></i>Magasin / Dépôt
                                </label>
                                <select id="store_picker" class="form-select">
                                    <option value="">— Non assigné —</option>
                                    @foreach($stores ?? [] as $st)
                                        <option value="{{ $st->id }}" {{ ($defaultStore?->id == $st->id) ? 'selected' : '' }}>
                                            {{ $st->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-5 col-sm-3 col-md-2">
                                <label for="quantity_picker" class="form-label small fw-semibold text-dark mb-1">Quantité</label>
                                <input type="number" id="quantity_picker" class="form-control" value="1" min="1" max="999">
                            </div>

                            <div class="col-7 col-sm-3 col-md-2">
                                <button type="button" class="btn btn-brand-primary w-100 justify-content-center" onclick="addSelectedProduct()" title="Ajouter à la commande">
                                    <i class="fas fa-plus"></i> <span class="ms-1">Ajouter</span>
                                </button>
                            </div>
                        </div>
                        <div id="pickerStoreHint" class="small text-muted mt-2 d-none"></div>
                    </div>

                    <!-- Liste des articles ajoutés -->
                    <div id="itemsContainer">
                        <div class="table-responsive d-none d-md-block">
                            <table class="table align-middle mb-0" id="orderItemsTable">
                                <thead class="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th class="border-0 ps-3">Produit</th>
                                        <th class="border-0" style="min-width: 210px;">Magasin / Dépôt source</th>
                                        <th class="border-0 text-center" style="width: 115px;">P.U. TTC</th>
                                        <th class="border-0 text-center" style="width: 130px;">Quantité</th>
                                        <th class="border-0 text-end pe-3" style="width: 120px;">Total TTC</th>
                                        <th class="border-0 text-center" style="width: 44px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsTbody">
                                    <!-- Dynamic rows inserted by JavaScript -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Items List -->
                        <div id="mobileItemsList" class="d-block d-md-none">
                            <!-- Dynamic mobile cards inserted by JavaScript -->
                        </div>

                        <!-- Message si aucun article -->
                        <div id="noItemsAlert" class="text-center py-4 border rounded-3 bg-white text-muted">
                            <i class="fas fa-cart-arrow-down fs-2 mb-2 text-muted opacity-50"></i>
                            <div class="fw-semibold">Aucun article ajouté pour l'instant</div>
                            <div class="small">Sélectionnez un produit, son dépôt et cliquez sur <strong>Ajouter</strong>.</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Modalités & Notes -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-credit-card text-success"></i> 3. Modalités & Notes
                            </h2>
                            <p class="product-form-card-subtitle">Mode de règlement et instructions éventuelles</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="payment_method" class="form-label fw-semibold text-dark">
                                Mode de règlement
                            </label>
                            <select id="payment_method" name="payment_method" class="form-select">
                                <option value="cash_on_delivery" selected>💵 Espèces à la livraison</option>
                                <option value="credit_card">💳 Carte bancaire</option>
                                <option value="bank_transfer">🏦 Virement bancaire</option>
                                <option value="cmi">🇲🇦 CMI Maroc</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold text-dark">
                                Notes ou instructions spéciales
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Instructions de livraison, créneau horaire, remarque client...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) : Récapitulatif Financier et Validation -->
            <div class="col-12 col-lg-4">
                <!-- Récapitulatif en direct -->
                <div class="product-form-card mb-4 position-sticky" style="top: 20px;">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-calculator text-danger"></i> Récapitulatif Commande
                        </h2>
                    </div>

                    <div class="d-flex flex-column gap-2.5">
                        <div class="d-flex justify-content-between align-items-center text-secondary small">
                            <span>Articles sélectionnés :</span>
                            <span class="fw-bold text-dark" id="calcItemCount">0</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center text-secondary small">
                            <span>Sous-total HT :</span>
                            <span class="fw-semibold text-dark" id="calcSubtotal">0,00 MAD</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center text-secondary small">
                            <span>{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%) :</span>
                            <span class="fw-semibold text-dark" id="calcTax">0,00 MAD</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center text-secondary small">
                            <span>Livraison :</span>
                            <span class="fw-semibold text-success">Gratuite</span>
                        </div>

                        <div class="pt-2.5 mt-1 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">Total TTC</span>
                            <span class="fw-extrabold font-heading text-danger fs-4" id="calcTotal">0,00 MAD</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center shadow-sm fw-bold">
                            <i class="fas fa-check-circle me-2"></i> Créer la commande
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 py-2 mt-2 rounded-pill text-center d-block text-decoration-none fw-semibold">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
@php
    $orderStoresPayload = [];
    foreach ($stores ?? [] as $st) {
        $orderStoresPayload[] = [
            'id' => (int) $st->id,
            'name' => $st->name,
            'code' => $st->code,
            'type' => $st->type,
        ];
    }

    $productStoreStockPayload = [];
    foreach ($products as $prod) {
        $rows = [];
        foreach ($prod->storeStocks as $ss) {
            $rows[(int) $ss->store_id] = [
                'qty' => (int) $ss->quantity,
                'location' => $ss->location_code,
            ];
        }
        $productStoreStockPayload[(int) $prod->id] = $rows;
    }
@endphp
<script>
    const currencySymbol = @json(setting('currency_symbol', 'MAD'));
    const taxRate = {{ floatval(setting('tax_rate', 20)) }} / 100;
    const orderStores = @json($orderStoresPayload);
    const productStoreStocks = @json($productStoreStockPayload);
    let orderItems = [];

    function getProductStoreInfo(productId, storeId) {
        if (!productId || !storeId) return null;
        const map = productStoreStocks[productId] || {};
        return map[storeId] || null;
    }

    function productHasStoreAllocations(productId) {
        const map = productStoreStocks[productId] || {};
        return Object.keys(map).length > 0;
    }

    function escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
        });
    }

    function buildStoreOptionsHtml(productId, selectedStoreId) {
        const hasAllocations = productHasStoreAllocations(productId);
        let html = `<option value="">— Non assigné —</option>`;
        orderStores.forEach(st => {
            const info = getProductStoreInfo(productId, st.id);
            const qty = info ? info.qty : 0;
            const loc = info && info.location ? ` [${info.location}]` : '';
            const stockText = hasAllocations ? ` — Stock: ${qty}${loc}` : '';
            const selected = String(selectedStoreId || '') === String(st.id) ? 'selected' : '';
            html += `<option value="${st.id}" ${selected}>${escapeHtml(st.name)}${escapeHtml(stockText)}</option>`;
        });
        return html;
    }

    function applyDefaultStoreToPicker() {
        const def = document.getElementById('default_store_id')?.value || '';
        const pickerStore = document.getElementById('store_picker');
        if (pickerStore) {
            pickerStore.value = def;
            onProductPickerChange();
        }
    }

    function onProductPickerChange() {
        const picker = document.getElementById('product_picker');
        const storePicker = document.getElementById('store_picker');
        const hint = document.getElementById('pickerStoreHint');
        const prodId = parseInt(picker?.value || 0);

        if (!storePicker) return;

        const prevVal = storePicker.value || (document.getElementById('default_store_id')?.value || '');
        if (!prodId) {
            storePicker.innerHTML = buildStoreOptionsHtml(0, prevVal);
            if (hint) { hint.classList.add('d-none'); hint.innerHTML = ''; }
            return;
        }

        // Rebuild store picker with live stock counts for this product
        let bestStoreId = prevVal;
        const map = productStoreStocks[prodId] || {};
        const prevQty = map[prevVal]?.qty ?? 0;
        if (productHasStoreAllocations(prodId) && prevQty <= 0) {
            // Prefer a store that actually has stock if current default is empty
            for (const st of orderStores) {
                if ((map[st.id]?.qty ?? 0) > 0) {
                    bestStoreId = String(st.id);
                    break;
                }
            }
        }
        storePicker.innerHTML = buildStoreOptionsHtml(prodId, bestStoreId);

        if (hint) {
            if (productHasStoreAllocations(prodId)) {
                const pills = orderStores.map(st => {
                    const info = map[st.id];
                    const qty = info ? info.qty : 0;
                    const cls = qty > 0 ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-muted border';
                    const loc = info?.location ? ` (${escapeHtml(info.location)})` : '';
                    return `<span class="badge ${cls} border me-1 mb-1">${escapeHtml(st.name)}: <strong>${qty}</strong>${loc}</span>`;
                }).join('');
                hint.innerHTML = `<i class="fas fa-warehouse text-primary me-1"></i>Disponibilité par dépôt : ${pills}`;
                hint.classList.remove('d-none');
            } else {
                hint.innerHTML = `<i class="fas fa-info-circle text-muted me-1"></i>Stock global (aucune répartition par dépôt enregistrée pour ce produit).`;
                hint.classList.remove('d-none');
            }
        }
    }

    function addSelectedProduct() {
        const picker = document.getElementById('product_picker');
        const storePicker = document.getElementById('store_picker');
        const qtyPicker = document.getElementById('quantity_picker');
        const prodId = parseInt(picker.value);
        const qty = parseInt(qtyPicker.value) || 1;
        const storeId = storePicker && storePicker.value ? parseInt(storePicker.value) : null;

        if (!prodId) {
            Swal.fire({
                icon: 'warning',
                title: 'Veuillez choisir un produit',
                text: 'Sélectionnez un produit dans la liste avant d\'ajouter.',
                confirmButtonColor: '#dc2626'
            });
            return;
        }

        const selectedOption = picker.options[picker.selectedIndex];
        const name = selectedOption.getAttribute('data-name');
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const sku = selectedOption.getAttribute('data-sku') || '';
        const thumb = selectedOption.getAttribute('data-thumb') || '';
        const maxStock = parseInt(selectedOption.getAttribute('data-stock')) || 999;
        const variantId = selectedOption.getAttribute('data-variant-id') || null;
        const variantName = selectedOption.getAttribute('data-variant-name') || null;

        // Combine only when product, variant AND store match (allows splitting same product across two depots!)
        const existing = orderItems.find(it =>
            it.product_id === prodId &&
            String(it.variant_id || '') === String(variantId || '') &&
            String(it.store_id || '') === String(storeId || '')
        );
        if (existing) {
            existing.quantity += qty;
        } else {
            orderItems.push({
                product_id: prodId,
                variant_id: variantId,
                variant_name: variantName,
                store_id: storeId,
                name: name,
                price: price,
                sku: sku,
                thumb: thumb,
                quantity: qty,
                max_stock: maxStock
            });
        }

        // Reset pickers
        picker.value = '';
        qtyPicker.value = 1;
        onProductPickerChange();

        renderOrderItems();
    }

    function removeItem(index) {
        orderItems.splice(index, 1);
        renderOrderItems();
    }

    function updateItemQty(index, newQty) {
        const qty = parseInt(newQty);
        if (qty > 0) {
            orderItems[index].quantity = qty;
        } else {
            orderItems.splice(index, 1);
        }
        renderOrderItems();
    }

    function updateItemStore(index, newStoreId) {
        orderItems[index].store_id = newStoreId ? parseInt(newStoreId) : null;
        renderOrderItems();
    }

    function formatCurrency(val) {
        return (val || 0).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + currencySymbol;
    }

    function renderOrderItems() {
        const tbody = document.getElementById('orderItemsTbody');
        const mobileContainer = document.getElementById('mobileItemsList');
        const noItemsAlert = document.getElementById('noItemsAlert');

        tbody.innerHTML = '';
        mobileContainer.innerHTML = '';

        if (orderItems.length === 0) {
            noItemsAlert.style.display = 'block';
            updateTotals(0);
            return;
        }

        noItemsAlert.style.display = 'none';

        let subtotalGross = 0;
        let totalCount = 0;

        orderItems.forEach((item, idx) => {
            const lineTotal = item.price * item.quantity;
            subtotalGross += lineTotal;
            totalCount += item.quantity;

            const storeOpts = buildStoreOptionsHtml(item.product_id, item.store_id);
            let storeWarning = '';
            if (item.store_id && productHasStoreAllocations(item.product_id)) {
                const info = getProductStoreInfo(item.product_id, item.store_id);
                const avail = info ? info.qty : 0;
                if (avail < item.quantity) {
                    storeWarning = `<div class="text-danger mt-1" style="font-size: 0.72rem;"><i class="fas fa-exclamation-triangle me-1"></i>Stock dépôt insuffisant (${avail} dispo)</div>`;
                } else if (info && info.location) {
                    storeWarning = `<div class="text-muted mt-1" style="font-size: 0.72rem;"><i class="fas fa-map-pin text-primary me-1"></i>${escapeHtml(info.location)} &bull; ${avail} dispo</div>`;
                }
            }

            // Desktop Row (holds authoritative form inputs)
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3 py-2.5">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 42px; height: 42px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                            <img src="${item.thumb || '/images/camera/cat_cameras.jpg'}" alt="${escapeHtml(item.name)}" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.src='/images/camera/cat_cameras.jpg'">
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${escapeHtml(item.name)}</div>
                            <div class="text-muted small font-monospace">SKU: ${escapeHtml(item.sku || 'N/A')}</div>
                        </div>
                    </div>
                    <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${idx}][variant_id]" value="${item.variant_id || ''}">
                    <input type="hidden" name="items[${idx}][variant_name]" value="${escapeHtml(item.variant_name || '')}">
                </td>
                <td class="py-2.5">
                    <select name="items[${idx}][store_id]" class="form-select form-select-sm" onchange="updateItemStore(${idx}, this.value)">
                        ${storeOpts}
                    </select>
                    ${storeWarning}
                </td>
                <td class="text-center py-2.5 text-secondary">
                    ${formatCurrency(item.price)}
                </td>
                <td class="text-center py-2.5">
                    <div class="input-group input-group-sm mx-auto" style="width: 105px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateItemQty(${idx}, ${item.quantity - 1})">-</button>
                        <input type="number" name="items[${idx}][quantity]" class="form-control text-center px-1" value="${item.quantity}" min="1" onchange="updateItemQty(${idx}, this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateItemQty(${idx}, ${item.quantity + 1})">+</button>
                    </div>
                </td>
                <td class="text-end pe-3 py-2.5 fw-bold text-dark font-heading">
                    ${formatCurrency(lineTotal)}
                </td>
                <td class="text-center py-2.5">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${idx})" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            // Mobile Card
            const card = document.createElement('div');
            card.className = 'p-3 border rounded-3 mb-2 bg-light bg-opacity-25';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                        <div style="width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                            <img src="${item.thumb || '/images/camera/cat_cameras.jpg'}" alt="${escapeHtml(item.name)}" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.src='/images/camera/cat_cameras.jpg'">
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="fw-bold text-dark text-truncate">${escapeHtml(item.name)}</div>
                            <div class="text-muted small font-monospace">SKU: ${escapeHtml(item.sku || 'N/A')}</div>
                            <div class="small text-secondary">${formatCurrency(item.price)} / unité</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${idx})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <label class="form-label small fw-semibold text-muted mb-1"><i class="fas fa-store text-primary me-1"></i>Magasin / Dépôt source</label>
                    <select class="form-select form-select-sm" onchange="updateItemStore(${idx}, this.value)">
                        ${storeOpts}
                    </select>
                    ${storeWarning}
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                    <div class="input-group input-group-sm" style="width: 105px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateItemQty(${idx}, ${item.quantity - 1})">-</button>
                        <input type="number" class="form-control text-center px-1" value="${item.quantity}" min="1" onchange="updateItemQty(${idx}, this.value)">
                        <button type="button" class="btn btn-outline-secondary" onclick="updateItemQty(${idx}, ${item.quantity + 1})">+</button>
                    </div>
                    <div class="fw-bold text-dark font-heading">
                        ${formatCurrency(lineTotal)}
                    </div>
                </div>
            `;
            mobileContainer.appendChild(card);
        });

        updateTotals(subtotalGross, totalCount);
    }

    function updateTotals(totalTtc, count = 0) {
        document.getElementById('calcItemCount').textContent = count;

        if (totalTtc <= 0) {
            document.getElementById('calcSubtotal').textContent = formatCurrency(0);
            document.getElementById('calcTax').textContent = formatCurrency(0);
            document.getElementById('calcTotal').textContent = formatCurrency(0);
            return;
        }

        const taxAmount = taxRate > 0 ? (totalTtc - (totalTtc / (1 + taxRate))) : 0;
        const subtotalHt = totalTtc - taxAmount;

        document.getElementById('calcSubtotal').textContent = formatCurrency(subtotalHt);
        document.getElementById('calcTax').textContent = formatCurrency(taxAmount);
        document.getElementById('calcTotal').textContent = formatCurrency(totalTtc);
    }

    // Form submit check
    document.getElementById('createOrderForm').addEventListener('submit', function(e) {
        if (orderItems.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Articles requis',
                text: 'Veuillez ajouter au moins un produit à la commande avant d\'enregistrer.',
                confirmButtonColor: '#dc2626'
            });
        }
    });
</script>
@endpush
