@extends('layouts.app')

@section('title', 'Modifier la commande #' . $order->order_number)

@section('content')
    <!-- En-tête de page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ $order->isPosOrder() ? route('orders.pos') : route('orders.index') }}" class="text-decoration-none text-muted">{{ $order->isPosOrder() ? 'Commandes POS' : 'Commandes' }}</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted font-monospace">#{{ $order->order_number }}</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold">Modifier</span>
                </div>
                <h1 class="brand-title">Modifier : <span class="font-monospace">#{{ $order->order_number }}</span></h1>
                <p class="brand-subtitle d-flex align-items-center gap-2 flex-wrap">
                    <span>Client : <strong>{{ $order->customer_name ?: 'Client invité' }}</strong> &bull; Total : <strong>{{ $order->formatted_total }}</strong></span>
                    <span class="tax-mode-pill tax-mode-{{ $order->normalized_tax_mode }}">{{ $order->tax_mode_label }}</span>
                </p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                <i class="fas fa-arrow-left"></i> <span>Retour aux détails</span>
            </a>
        </div>
    </div>

    <!-- Alertes Erreurs -->
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

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

    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Colonne Principale (Gauche) -->
            <div class="col-12 col-lg-8">
                <!-- Statuts de la commande -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-truck-fast text-danger"></i> 1. Statuts & Traitement
                            </h2>
                            <p class="product-form-card-subtitle">Mettez à jour l'état logistique et la validation du paiement</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Statut de livraison -->
                        <div class="col-12 col-sm-6">
                            <label for="status" class="form-label fw-semibold text-dark">
                                Statut de livraison <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status" class="form-select fw-bold @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                <option value="en_demande" {{ old('status', $order->status) == 'en_demande' ? 'selected' : '' }}>📋 Sur Commande</option>
                                <option value="reserved" {{ old('status', $order->status) == 'reserved' ? 'selected' : '' }}>🔖 Réservé</option>
                                <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>⚙️ En préparation (En cours)</option>
                                <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>🚚 Expédiée</option>
                                <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>✅ Livrée (Déduire le stock)</option>
                                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>❌ Annulée</option>
                            </select>
                            <div class="form-text small text-muted mt-1.5">
                                <i class="fas fa-info-circle text-primary me-1"></i>Le passage à "Livrée" déduit automatiquement le stock des articles s'il ne l'est pas encore.
                            </div>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut de paiement -->
                        <div class="col-12 col-sm-6">
                            <label for="payment_status" class="form-label fw-semibold text-dark">
                                Statut de paiement <span class="text-danger">*</span>
                            </label>
                            <select id="payment_status" name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>⏳ En attente de règlement</option>
                                <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>🌗 Avance</option>
                                <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>💳 Payé / Encaissé</option>
                                <option value="failed" {{ old('payment_status', $order->payment_status) == 'failed' ? 'selected' : '' }}>⚠️ Échoué / Refusé</option>
                                <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>↩️ Remboursé</option>
                            </select>
                            <div class="form-text small text-muted mt-1.5">
                                Met à jour la facturation et les reçus associés.
                            </div>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Détails du paiement partiel (affiché si statut = Avance) -->
                        @php
                            $isPartialEdit = old('payment_status', $order->payment_status) === 'partial'
                                || $errors->has('paid_amount')
                                || $errors->has('payment_due_date');
                            $editOrderTotal = round((float) $order->total, 2);
                            $editPaidVal = old('paid_amount', number_format((float) ($order->paid_amount ?? 0), 2, '.', ''));
                            $editDueVal = old('payment_due_date', $order->payment_due_date?->format('Y-m-d') ?? '');
                        @endphp
                        <div class="col-12 {{ $isPartialEdit ? '' : 'd-none' }}" id="partialPaymentWrapper">
                            <div class="p-3 rounded-3 bg-primary-subtle border border-primary-subtle">
                                <div class="row g-2.5 align-items-end">
                                    <div class="col-12 col-md-7">
                                        <label for="paid_amount" class="form-label fw-bold text-primary-emphasis mb-1">
                                            <i class="fas fa-circle-half-stroke text-primary me-1"></i> Montant déjà payé (DH)
                                        </label>
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   max="{{ $editOrderTotal }}"
                                                   id="paid_amount"
                                                   name="paid_amount"
                                                   data-order-total="{{ $editOrderTotal }}"
                                                   value="{{ $editPaidVal }}"
                                                   class="form-control bg-white fw-bold @error('paid_amount') is-invalid @enderror">
                                            <span class="input-group-text fw-semibold">DH</span>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1.5 mt-1.5">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach([25, 50, 75] as $pct)
                                                    @php $pctAmt = round($editOrderTotal * ($pct / 100), 2); @endphp
                                                    <button type="button"
                                                            class="btn btn-xs btn-outline-primary bg-white rounded-pill px-2 py-0 fw-semibold edit-partial-pct-btn"
                                                            style="font-size: 0.7rem;"
                                                            data-amount="{{ number_format($pctAmt, 2, '.', '') }}">
                                                        {{ $pct }}% ({{ number_format($pctAmt, 2, '.', '') }} DH)
                                                    </button>
                                                @endforeach
                                            </div>
                                            <span class="small fw-bold text-danger" id="editPartialRemainingLabel">
                                                Reste : {{ number_format(max(0, $editOrderTotal - (float) $editPaidVal), 2, '.', '') }} DH
                                            </span>
                                        </div>
                                        @error('paid_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="payment_due_date" class="form-label fw-semibold text-primary-emphasis mb-1">
                                            Date d'échéance (optionnel)
                                        </label>
                                        <input type="date"
                                               id="payment_due_date"
                                               name="payment_due_date"
                                               value="{{ $editDueVal }}"
                                               class="form-control form-control-sm bg-white @error('payment_due_date') is-invalid @enderror">
                                        @error('payment_due_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Raison d'annulation (obligatoire si statut = Annulée) -->
                        @php
                            $isCancelledEdit = old('status', $order->status) === 'cancelled' || $errors->has('cancellation_reason');
                        @endphp
                        <div class="col-12 {{ $isCancelledEdit ? '' : 'd-none' }}" id="cancellationReasonWrapper">
                            <div class="p-3 rounded-3 bg-danger-subtle border border-danger-subtle">
                                <label for="cancellation_reason" class="form-label fw-bold text-danger-emphasis mb-1.5">
                                    <i class="fas fa-comment-slash text-danger me-1"></i> Raison d'annulation <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex flex-wrap gap-1.5 mb-2">
                                    @foreach(\App\Models\Order::CANCELLATION_REASONS as $presetReason)
                                        <button type="button"
                                                class="btn btn-xs btn-outline-danger rounded-pill px-2.5 py-0.5 fw-semibold bg-white edit-cancel-preset-btn"
                                                style="font-size: 0.72rem;"
                                                data-reason="{{ $presetReason }}">
                                            {{ $presetReason }}
                                        </button>
                                    @endforeach
                                </div>
                                <textarea id="cancellation_reason"
                                          name="cancellation_reason"
                                          rows="2"
                                          maxlength="1000"
                                          class="form-control bg-white @error('cancellation_reason') is-invalid @enderror"
                                          placeholder="Expliquez pourquoi cette commande est annulée..."
                                          {{ $isCancelledEdit ? 'required' : '' }}>{{ old('cancellation_reason', $order->cancellation_reason) }}</textarea>
                                @error('cancellation_reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Régime Fiscal (TTC / HT) -->
                        <div class="col-12 col-sm-6">
                            <label for="tax_mode" class="form-label fw-semibold text-dark">
                                Régime (TTC / HT)
                            </label>
                            <select id="tax_mode" name="tax_mode" class="form-select fw-bold @error('tax_mode') is-invalid @enderror">
                                @php $editTaxMode = old('tax_mode', $order->normalized_tax_mode); @endphp
                                <option value="ttc" {{ $editTaxMode === 'ttc' ? 'selected' : '' }}>TTC — Toutes Taxes Comprises</option>
                                <option value="ht" {{ $editTaxMode === 'ht' ? 'selected' : '' }}>HT — Hors Taxes</option>
                            </select>
                            <div class="form-text small text-muted mt-1.5">
                                Étiquette visible dans le tableau et les cartes des commandes.
                            </div>
                            @error('tax_mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type de livraison -->
                        <div class="col-12 col-sm-6">
                            <label for="delivery_type" class="form-label fw-semibold text-dark">
                                Type de livraison
                            </label>
                            @php
                                $editDeliveryType = old('delivery_type', $order->normalized_delivery_type);
                                $editSuggestedShipping = \App\Services\DeliveryService::getDeliveryCost($order->shipping_city, 40.0);
                            @endphp
                            <select id="delivery_type" name="delivery_type" data-suggested-shipping="{{ $editSuggestedShipping }}" class="form-select fw-semibold @error('delivery_type') is-invalid @enderror">
                                <option value="" {{ empty($editDeliveryType) ? 'selected' : '' }}>— Non spécifié —</option>
                                <option value="store" {{ $editDeliveryType === 'store' ? 'selected' : '' }}>Store (0 DH)</option>
                                <option value="our_team" {{ $editDeliveryType === 'our_team' ? 'selected' : '' }}>Livreur</option>
                                <option value="ozon" {{ $editDeliveryType === 'ozon' ? 'selected' : '' }}>Ozon</option>
                                <option value="autre" {{ $editDeliveryType === 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            <div class="form-text small text-muted mt-1.5">
                                Canal d'expédition ou de remise (POS / Boutique).
                            </div>
                            @error('delivery_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Frais de livraison -->
                        <div class="col-12 col-sm-6">
                            <label for="shipping_cost" class="form-label fw-semibold text-dark">
                                Frais de livraison (DH)
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       id="shipping_cost"
                                       name="shipping_cost"
                                       value="{{ old('shipping_cost', number_format((float) ($order->shipping_cost ?? 0), 2, '.', '')) }}"
                                       {{ $editDeliveryType === 'store' ? 'readonly' : '' }}
                                       class="form-control fw-bold @error('shipping_cost') is-invalid @enderror">
                                <span class="input-group-text fw-semibold">DH</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-1.5" id="edit-shipping-presets">
                                @foreach(collect([0, 20, 30, 40, $editSuggestedShipping])->unique()->sort()->values() as $presetCost)
                                    <button type="button"
                                            class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0 fw-semibold edit-ship-preset-btn"
                                            style="font-size: 0.7rem;"
                                            data-price="{{ $presetCost }}">
                                        {{ $presetCost }} DH
                                    </button>
                                @endforeach
                            </div>
                            <div class="form-text small text-muted mt-1" id="edit-shipping-hint">
                                {{ $editDeliveryType === 'store' ? 'Store sélectionné : frais automatiquement fixés à 0 DH.' : 'Le total de la commande est recalculé automatiquement.' }}
                            </div>
                            @error('shipping_cost')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mode et Détails du Règlement -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-credit-card text-success"></i> 2. Modalités de Règlement
                            </h2>
                            <p class="product-form-card-subtitle">Méthode de paiement et identifiant de transaction</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Mode de paiement -->
                        <div class="col-12 col-sm-6">
                            <label for="payment_method" class="form-label fw-semibold text-dark">
                                Mode de règlement
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-wallet"></i>
                                </span>
                                <input type="text" 
                                       id="payment_method" 
                                       name="payment_method" 
                                       class="form-control @error('payment_method') is-invalid @enderror" 
                                       value="{{ old('payment_method', $order->payment_method) }}" 
                                       placeholder="ex: Espèces, Carte bancaire, Virement">
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- ID de transaction -->
                        <div class="col-12 col-sm-6">
                            <label for="transaction_id" class="form-label fw-semibold text-dark">
                                Référence de transaction
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       id="transaction_id" 
                                       name="transaction_id" 
                                       class="form-control font-monospace @error('transaction_id') is-invalid @enderror" 
                                       value="{{ old('transaction_id', $order->transaction_id) }}" 
                                       placeholder="ex: TRX-9988221">
                            </div>
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Provenance Stock par Article (Magasins / Dépôts) -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h2 class="product-form-card-title mb-0">
                                <i class="fas fa-warehouse text-primary"></i> 3. Magasin / Dépôt par Produit
                            </h2>
                            <p class="product-form-card-subtitle mb-0">Choisissez d'où chaque produit de la commande est prélevé</p>
                        </div>
                        @if(isset($stores) && $stores->isNotEmpty() && $order->items->isNotEmpty())
                            <div class="d-flex align-items-center gap-1.5">
                                <select id="bulkApplyEditStore" class="form-select form-select-sm" style="min-width: 175px;">
                                    <option value="">— Non assigné —</option>
                                    @foreach($stores as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 fw-semibold text-nowrap" onclick="applyBulkStoreEdit()">
                                    <i class="fas fa-check-double me-1"></i>Appliquer à tous
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light small text-muted text-uppercase">
                                <tr>
                                    <th class="border-0 ps-2">Produit</th>
                                    <th class="border-0 text-center" style="width: 75px;">Qté</th>
                                    <th class="border-0" style="min-width: 240px;">Magasin / Dépôt source</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $itemStoreStocks = $item->product ? $item->product->storeStocks->keyBy('store_id') : collect();
                                        $hasItemStoreAllocations = $itemStoreStocks->isNotEmpty();
                                        $selectedItemStore = old('item_stores.' . $item->id, $item->store_id);
                                    @endphp
                                    <tr>
                                        <td class="ps-2 py-2.5">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div style="width: 40px; height: 40px; border-radius: 8px; overflow: hidden; background: #fff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 2px; display: flex; align-items: center; justify-content: center;">
                                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark small text-truncate" style="max-width: 240px;">{{ $item->product_name }}</div>
                                                    <div class="text-muted" style="font-size: 0.72rem;">
                                                        <span class="font-monospace">SKU: {{ $item->product_sku ?: 'N/A' }}</span>
                                                        @if($item->variant_name)
                                                            &bull; <span class="text-primary fw-semibold">{{ $item->variant_name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-2.5">
                                            <span class="badge bg-light text-dark border fw-bold">x{{ $item->quantity }}</span>
                                        </td>
                                        <td class="py-2.5">
                                            <select name="item_stores[{{ $item->id }}]" class="form-select form-select-sm edit-item-store-select">
                                                <option value="">— Non assigné —</option>
                                                @foreach($stores ?? [] as $st)
                                                    @php
                                                        $stStock = $itemStoreStocks->get($st->id);
                                                        $stQty = $stStock ? (int) $stStock->quantity : 0;
                                                        $stLoc = $stStock?->location_code ? ' [' . $stStock->location_code . ']' : '';
                                                        $stLabel = $st->name . ($hasItemStoreAllocations ? " — Stock: {$stQty}{$stLoc}" : '');
                                                    @endphp
                                                    <option value="{{ $st->id }}" {{ (string) $selectedItemStore === (string) $st->id ? 'selected' : '' }}>
                                                        {{ $stLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes de commande -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-comment-dots text-warning"></i> 4. Notes & Commentaires Internes
                            </h2>
                            <p class="product-form-card-subtitle">Instructions particulières pour l'équipe ou le livreur</p>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <textarea id="notes" 
                                  name="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="4" 
                                  placeholder="Notes d'expédition, instructions livreur, remarques client...">{{ old('notes', $order->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) : Résumé et Actions -->
            <div class="col-12 col-lg-4">
                <!-- Résumé Rapide -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-receipt text-primary"></i> Synthèse Commande
                        </h2>
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                        <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; font-weight: 800; font-size: 1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #fca5a5;">
                            {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <div class="fw-bold text-dark text-truncate">{{ $order->customer_name ?: 'Client invité' }}</div>
                            <div class="text-muted small text-truncate">{{ $order->customer_email ?: ($order->customer_phone ?: 'Aucun contact') }}</div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 small">
                        <div class="d-flex justify-content-between align-items-center text-secondary">
                            <span>Total articles :</span>
                            <span class="fw-bold text-dark">{{ $order->items->count() }} ({{ $order->items->sum('quantity') }} unités)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-secondary">
                            <span>Sous-total HT :</span>
                            <span class="fw-semibold text-dark">{{ currency($order->subtotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-secondary">
                            <span>TVA :</span>
                            <span class="fw-semibold text-dark">{{ currency($order->tax) }}</span>
                        </div>
                        <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark fs-6">Montant Total :</span>
                            <span class="fw-extrabold font-heading text-danger fs-5">{{ $order->formatted_total }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                            <span>Enregistrée le :</span>
                            <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action -->
                <div class="product-form-card">
                    <button type="submit" class="btn-brand-primary w-100 py-2.5 justify-content-center shadow-sm fw-bold">
                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary w-100 py-2 mt-2 rounded-pill text-center d-block text-decoration-none fw-semibold">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>

@push('scripts')
<script>
function applyBulkStoreEdit() {
    const bulkVal = document.getElementById('bulkApplyEditStore')?.value ?? '';
    document.querySelectorAll('.edit-item-store-select').forEach(sel => {
        sel.value = bulkVal;
    });
}

function syncOrderEditStatusColor(sel) {
    if (!sel) return;
    const themeMap = {
        'pending':    { bg: '#d97706', border: '#b45309', color: '#ffffff' },
        'en_demande': { bg: '#c61aff', border: '#a800e0', color: '#ffffff' },
        'reserved':   { bg: '#7c3aed', border: '#6d28d9', color: '#ffffff' },
        'processing': { bg: '#2563eb', border: '#1d4ed8', color: '#ffffff' },
        'shipped':    { bg: '#0093ac', border: '#0e7490', color: '#ffffff' },
        'delivered':  { bg: '#059669', border: '#047857', color: '#ffffff' },
        'cancelled':  { bg: '#dc2626', border: '#b91c1c', color: '#ffffff' }
    };
    const theme = themeMap[(sel.value || '').toLowerCase()] || themeMap.pending;
    sel.style.backgroundColor = theme.bg;
    sel.style.borderColor = theme.border;
    sel.style.color = theme.color;
}

(function () {
    function initOrderEditStatusSelect() {
        const statusSelect = document.getElementById('status');
        const paySelect = document.getElementById('payment_status');
        if (!statusSelect || statusSelect.dataset.statusBound === '1') return;
        statusSelect.dataset.statusBound = '1';

        const cancelWrapper = document.getElementById('cancellationReasonWrapper');
        const cancelTextarea = document.getElementById('cancellation_reason');

        function syncCancellationReasonField(shouldFocus = false) {
            if (!cancelWrapper || !cancelTextarea) return;
            const isCancelled = (statusSelect.value || '').toLowerCase() === 'cancelled';
            cancelWrapper.classList.toggle('d-none', !isCancelled);
            cancelTextarea.required = isCancelled;
            if (isCancelled && shouldFocus) {
                cancelTextarea.focus();
            }
        }

        document.querySelectorAll('.edit-cancel-preset-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (cancelTextarea) {
                    cancelTextarea.value = btn.dataset.reason || '';
                    cancelTextarea.focus();
                }
            });
        });

        const partialWrapper = document.getElementById('partialPaymentWrapper');
        const paidAmountInput = document.getElementById('paid_amount');
        const partialRemainingLabel = document.getElementById('editPartialRemainingLabel');

        function updateEditPartialRemaining() {
            if (!paidAmountInput || !partialRemainingLabel) return;
            const total = parseFloat(paidAmountInput.dataset.orderTotal || '0') || 0;
            const paid = parseFloat(paidAmountInput.value || '0') || 0;
            const rem = Math.max(0, total - paid);
            partialRemainingLabel.textContent = `Reste : ${rem.toFixed(2)} DH`;
        }

        function syncPartialPaymentField() {
            if (!partialWrapper || !paySelect) return;
            const isPartial = (paySelect.value || '').toLowerCase() === 'partial';
            partialWrapper.classList.toggle('d-none', !isPartial);
            if (isPartial) updateEditPartialRemaining();
        }

        if (paySelect) {
            syncPartialPaymentField();
            paySelect.addEventListener('change', syncPartialPaymentField);
        }
        if (paidAmountInput) {
            paidAmountInput.addEventListener('input', updateEditPartialRemaining);
        }
        document.querySelectorAll('.edit-partial-pct-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (paidAmountInput) {
                    paidAmountInput.value = btn.dataset.amount || '0';
                    updateEditPartialRemaining();
                    paidAmountInput.focus();
                }
            });
        });

        syncOrderEditStatusColor(statusSelect);
        syncCancellationReasonField(false);
        statusSelect.addEventListener('change', function() {
            syncOrderEditStatusColor(this);
            syncCancellationReasonField(true);
            if (this.value === 'delivered' && paySelect) {
                paySelect.value = 'paid';
                syncPartialPaymentField();
            }
        });

        const deliverySelect = document.getElementById('delivery_type');
        const shippingInput = document.getElementById('shipping_cost');
        const shippingHint = document.getElementById('edit-shipping-hint');
        if (deliverySelect && shippingInput) {
            deliverySelect.addEventListener('change', function () {
                const val = this.value;
                const suggested = parseFloat(this.dataset.suggestedShipping || '40') || 40;
                if (val === 'store') {
                    shippingInput.value = '0.00';
                    shippingInput.readOnly = true;
                    if (shippingHint) shippingHint.textContent = 'Store sélectionné : frais automatiquement fixés à 0 DH.';
                } else {
                    shippingInput.readOnly = false;
                    if (shippingHint) shippingHint.textContent = 'Saisissez ou confirmez le prix de livraison — le total sera recalculé automatiquement.';
                    const current = parseFloat(shippingInput.value || '0') || 0;
                    if (current === 0 && val !== '') {
                        shippingInput.value = suggested.toFixed(2);
                    }
                    shippingInput.focus();
                    shippingInput.select();
                }
            });
            document.querySelectorAll('.edit-ship-preset-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (deliverySelect.value === 'store') return;
                    const p = parseFloat(btn.dataset.price || '0') || 0;
                    shippingInput.value = p.toFixed(2);
                    shippingInput.focus();
                });
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initOrderEditStatusSelect);
    } else {
        initOrderEditStatusSelect();
    }
})();
</script>
@endpush
@endsection
