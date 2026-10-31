@extends('layouts.app')

@section('title', 'Nouveau document — Facture / Devis')

@push('styles')
<style>
    /* Modern Invoice Items Table & Inputs Styling */
    .invoice-table-head {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .invoice-table-head th {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #64748b;
        padding: 12px 14px;
        border-bottom: none;
    }

    .item-row {
        transition: background-color 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }

    .item-row:hover {
        background-color: #fafbfc;
    }

    .item-row td {
        padding: 12px 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Product Cell & Modern Inputs */
    .product-cell-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .invoice-custom-select,
    .invoice-custom-input {
        height: 38px;
        font-size: 0.85rem;
        color: #0f172a;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
    }

    .invoice-custom-select:hover,
    .invoice-custom-input:hover {
        border-color: #94a3b8;
        background-color: #fbfcfe;
    }

    .invoice-custom-select:focus,
    .invoice-custom-input:focus {
        border-color: var(--primary-color, #dc2626);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        background-color: #ffffff;
        outline: none;
    }

    /* Badges & Mode Switchers */
    .badge-mode-catalog {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .badge-mode-manual {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #c2410c;
        background: #fff7ed;
        border: 1px solid #ffedd5;
        padding: 2px 7px;
        border-radius: 6px;
    }

    .btn-mode-switch {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.74rem;
        font-weight: 500;
        color: #64748b;
        background: transparent;
        border: 1px solid transparent;
        padding: 2px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-mode-switch:hover {
        color: var(--primary-color, #dc2626);
        background: #fee2e2;
        border-color: #fecaca;
    }

    /* Manual Mode Grid (Side-by-side on desktop, compact on mobile) */
    .manual-fields-grid {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .manual-name-wrapper {
        flex: 1;
        min-width: 0;
        position: relative;
    }

    .manual-sku-wrapper {
        width: 140px;
        flex-shrink: 0;
    }

    .modern-input-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .modern-input-icon-wrap .input-icon {
        position: absolute;
        left: 11px;
        color: #94a3b8;
        font-size: 0.8rem;
        pointer-events: none;
    }

    .modern-input-icon-wrap .item-manual-name {
        padding-left: 32px;
    }

    /* P.U. HT Input with integrated badge */
    .modern-price-field {
        position: relative;
        display: flex;
        align-items: center;
    }

    .modern-price-field .unit-price-input {
        padding-right: 48px;
        font-weight: 600;
        font-size: 0.88rem;
        text-align: right;
        color: #0f172a;
    }

    .modern-price-field .price-suffix {
        position: absolute;
        right: 8px;
        pointer-events: none;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        padding: 2px 5px;
        border-radius: 4px;
    }

    /* Quantity Field */
    .modern-qty-field .quantity-input {
        text-align: center;
        font-weight: 600;
        font-size: 0.88rem;
        color: #0f172a;
    }

    /* Row Total Display */
    .row-total-container {
        padding-right: 4px;
    }

    .row-total-value {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .row-total-sub {
        font-size: 0.65rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Action delete button */
    .btn-remove-row {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: transparent;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .btn-remove-row:hover {
        background: #fee2e2;
        color: #dc2626;
        transform: scale(1.05);
    }

    /* Bottom Add Line Interactive Cards */
    .btn-add-line {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 11px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        color: #334155;
        text-align: left;
    }

    .btn-add-line:hover {
        border-color: var(--primary-color, #dc2626);
        background: #fef2f2;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.06);
        transform: translateY(-1px);
    }

    .btn-add-line-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .btn-add-catalog .btn-add-line-icon {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-add-catalog:hover .btn-add-line-icon {
        background: #e2e8f0;
        color: #0f172a;
    }

    .btn-add-manual .btn-add-line-icon {
        background: #fee2e2;
        color: var(--primary-color, #dc2626);
    }

    .btn-add-manual:hover .btn-add-line-icon {
        background: var(--primary-color, #dc2626);
        color: #ffffff;
    }

    .btn-add-line-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #0f172a;
    }

    .btn-add-line-sub {
        font-size: 0.72rem;
        color: #64748b;
        margin-top: 2px;
    }

    @media (max-width: 767.98px) {
        .manual-fields-grid {
            flex-direction: column;
            align-items: stretch;
        }
        .manual-sku-wrapper {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('invoices.index') }}" class="text-decoration-none text-muted">Facturation</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold">Créer un document</span>
                </div>
                <h1 class="brand-title">Nouveau document</h1>
                <p class="brand-subtitle">
                    Générez une facture ou un devis conforme avec calcul automatique des taxes et cachet officiel
                </p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                <i class="fas fa-arrow-left"></i> <span>Retour à la liste</span>
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

    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf

        <div class="row g-4">
            <!-- Colonne Principale (Gauche) -->
            <div class="col-12 col-lg-8">
                <!-- Section 1 : Informations Client -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-user-circle text-primary"></i> 1. Coordonnées du Client
                            </h2>
                            <p class="product-form-card-subtitle">Sélectionnez un client existant ou saisissez ses informations</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Sélection client existant -->
                        <div class="col-12">
                            <label for="customerSelect" class="form-label fw-semibold text-dark">
                                Client enregistré (Optionnel)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-users"></i></span>
                                <select id="customerSelect" onchange="fillCustomerInfo()" class="form-select border-start-0">
                                    <option value="">-- Saisie manuelle d'un nouveau client --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                            data-name="{{ $customer->name }}" 
                                            data-email="{{ $customer->email }}" 
                                            data-phone="{{ $customer->phone }}" 
                                            data-address="{{ $customer->address }}"
                                            data-ice="{{ $customer->ice ?? '' }}">
                                            {{ $customer->name }} {{ $customer->email ? '(' . $customer->email . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Nom complet -->
                        <div class="col-12 col-sm-6">
                            <label for="customer_name" class="form-label fw-semibold text-dark">
                                Nom du client / Société <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                <input type="text" name="customer_name" id="customer_name" required value="{{ old('customer_name') }}"
                                       class="form-control border-start-0" placeholder="Ex: Mohammed Alami ou SARL Atlas">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-sm-6">
                            <label for="customer_email" class="form-label fw-semibold text-dark">
                                Adresse Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email') }}"
                                       class="form-control border-start-0" placeholder="client@exemple.com">
                            </div>
                        </div>

                        <!-- Téléphone -->
                        <div class="col-12 col-sm-6">
                            <label for="customer_phone" class="form-label fw-semibold text-dark">
                                Numéro de téléphone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-phone"></i></span>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                                       class="form-control border-start-0" placeholder="+212 600 000000">
                            </div>
                        </div>

                        <!-- ICE (Identifiant Commun de l'Entreprise) -->
                        <div class="col-12 col-sm-6">
                            <label for="ice" class="form-label fw-semibold text-dark">
                                ICE (Identifiant Fiscal)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="ice" id="ice" value="{{ old('ice') }}"
                                       class="form-control border-start-0" placeholder="Ex: 002345678000045">
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="col-12">
                            <label for="customer_address" class="form-label fw-semibold text-dark">
                                Adresse de facturation
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="customer_address" id="customer_address" rows="2" class="form-control border-start-0" placeholder="Adresse complète, Ville, Code Postal...">{{ old('customer_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Articles & Lignes de Facture -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-boxes-stacked text-primary"></i> 2. Articles & Lignes de Facture
                            </h2>
                            <p class="product-form-card-subtitle">Ajoutez des produits du catalogue ou saisissez des articles et prestations avec prix HT libre</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" onclick="openSmartTextModal()" class="btn btn-sm btn-dark rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 shadow-2xs" style="background: #0f172a; border-color: #0f172a;">
                                <i class="fas fa-wand-magic-sparkles text-warning"></i> <span>Ajout rapide par texte (Smart)</span>
                            </button>
                            <button type="button" onclick="addItem('catalog')" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                <i class="fas fa-boxes-stacked text-muted"></i> <span>Du catalogue</span>
                            </button>
                            <button type="button" onclick="addItem('manual')" class="btn btn-sm btn-brand-primary rounded-pill px-3 py-1.5 fw-medium d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                <i class="fas fa-feather-pointed"></i> <span>Saisie manuelle HT</span>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded-3 overflow-hidden mb-3 invoice-items-wrap">
                        <table class="table table-hover align-middle mb-0" id="itemsTable">
                            <thead class="invoice-table-head">
                                <tr>
                                    <th class="ps-3" style="min-width: 320px;">Produit / Description</th>
                                    <th class="text-end" style="width: 140px;">P.U. HT</th>
                                    <th class="text-center" style="width: 85px;">Qté</th>
                                    <th class="text-end" style="width: 130px;">Total HT</th>
                                    <th class="pe-3 text-center" style="width: 44px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Lignes d'articles dynamiques -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-2 w-100">
                        <div class="col-12 col-md-4">
                            <button type="button" onclick="openSmartTextModal()" class="btn-add-line w-100 border-2" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; border: 1px solid #334155;">
                                <div class="btn-add-line-icon" style="background: rgba(251, 191, 36, 0.2); color: #fbbf24;">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </div>
                                <div class="text-start">
                                    <div class="btn-add-line-title text-white">Ajout intelligent par texte</div>
                                    <div class="btn-add-line-sub text-white-50">Coller une liste (ex: fx3, 24-70mm...)</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="button" onclick="addItem('catalog')" class="btn-add-line btn-add-catalog w-100">
                                <div class="btn-add-line-icon">
                                    <i class="fas fa-boxes-stacked"></i>
                                </div>
                                <div class="text-start">
                                    <div class="btn-add-line-title">+ Produit du catalogue</div>
                                    <div class="btn-add-line-sub">Sélectionner parmi vos produits</div>
                                </div>
                            </button>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="button" onclick="addItem('manual')" class="btn-add-line btn-add-manual w-100">
                                <div class="btn-add-line-icon">
                                    <i class="fas fa-feather-pointed"></i>
                                </div>
                                <div class="text-start">
                                    <div class="btn-add-line-title">+ Article ou Prestation libre</div>
                                    <div class="btn-add-line-sub">Désignation libre & Prix HT</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Notes et Mentions Légales -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-comment-alt text-primary"></i> 3. Notes & Mentions
                            </h2>
                            <p class="product-form-card-subtitle">Conditions de paiement, coordonnées bancaires ou remerciements</p>
                        </div>
                    </div>
                    <div>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Ex: Règlement attendu sous 30 jours. Merci pour votre confiance !">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) : Paramètres & Récapitulatif -->
            <div class="col-12 col-lg-4">
                <!-- Paramètres du document -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title" style="font-size: 1rem;">
                                <i class="fas fa-cog text-primary"></i> Paramètres du document
                            </h2>
                            <p class="product-form-card-subtitle">Type et conditions de règlement</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <!-- Type de document -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Type de document <span class="text-danger">*</span></label>
                            <select name="type" required class="form-select">
                                <option value="invoice" {{ old('type') == 'invoice' ? 'selected' : '' }}>📄 Facture commerciale</option>
                                <option value="quote" {{ old('type') == 'quote' ? 'selected' : '' }}>📑 Devis estimatif</option>
                            </select>
                        </div>

                        <!-- Statut de paiement -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Statut du paiement <span class="text-danger">*</span></label>
                            <select name="payment_status" required class="form-select">
                                <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>⏳ Impayé (En attente)</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>✅ Payé (Encaissé)</option>
                                <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>🔄 Partiellement payé</option>
                            </select>
                        </div>

                        <!-- Mode de paiement -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Mode de règlement <span class="text-danger">*</span></label>
                            <select name="payment_method" required class="form-select">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 Espèces</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>💳 Carte bancaire</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>🏦 Virement bancaire</option>
                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Autre moyen</option>
                            </select>
                        </div>

                        <!-- Frais de livraison -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Frais de livraison (DH)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-truck"></i></span>
                                <input type="number" step="0.01" min="0" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', '0.00') }}" class="form-control border-start-0" onchange="updateTotals()" onkeyup="updateTotals()" placeholder="0.00">
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Non soumis à la TVA</small>
                        </div>

                        <!-- Date d'échéance -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Date d'échéance</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control">
                        </div>

                        <!-- Cachet officiel -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between p-0">
                                <label class="form-check-label fw-semibold text-dark small d-flex align-items-center gap-2 cursor-pointer" for="withStampSwitch">
                                    <i class="fas fa-stamp text-primary"></i> Cachet & Signature officiel
                                </label>
                                <input class="form-check-input ms-0 cursor-pointer" type="checkbox" name="with_stamp" value="1" id="withStampSwitch" {{ old('with_stamp', 1) ? 'checked' : '' }} style="width: 2.2rem; height: 1.2rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif Financier -->
                <div class="product-form-card mb-4" style="background: #fafbfc;">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title" style="font-size: 1rem;">
                                <i class="fas fa-receipt text-success"></i> Totaux du document
                            </h2>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2.5 mb-3">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Sous-total HT :</span>
                            <span class="fw-semibold text-dark" id="summarySubtotal">0.00 DH</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span id="summaryTaxLabel">{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%) :</span>
                            <span class="fw-semibold text-dark" id="summaryTax">0.00 DH</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small" id="summaryShippingRow">
                            <span>Frais de livraison :</span>
                            <span class="fw-semibold text-dark" id="summaryShipping">0.00 DH</span>
                        </div>
                        <hr class="my-1 border-secondary-subtle">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-bold text-dark fs-6">Montant Total TTC :</span>
                            <span class="fw-bold fs-4 text-primary font-heading" id="summaryTotal">0.00 DH</span>
                        </div>
                    </div>

                    <!-- Actions de Soumission -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-brand-primary justify-content-center py-2.5 fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-1.5"></i> Générer le document
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-light border justify-content-center py-2 text-muted fw-semibold">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Template de ligne d'article -->
    <template id="itemRowTemplate">
        <tr class="item-row">
            <td class="ps-3 pe-2 col-product">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Produit / Description</label>
                
                <div class="product-cell-container">
                    <!-- Mode Catalogue -->
                    <div class="catalog-mode-box">
                        <select name="items[{index}][product_id]" class="form-select invoice-custom-select product-select" onchange="updateItem(this)">
                            <option value="">-- Sélectionner un produit du catalogue --</option>
                            <option value="custom" class="fw-bold text-primary">➕ Saisie libre / Article personnalisé...</option>
                            @foreach($products as $product)
                                @php
                                    $taxRateVal = floatval(setting('tax_rate', 20)) / 100;
                                    $pSellingPrice = $product->active_price;
                                    $pPriceHt = $taxRateVal > 0 ? ($pSellingPrice / (1 + $taxRateVal)) : $pSellingPrice;
                                @endphp
                                <option value="{{ $product->id }}" data-price="{{ $pSellingPrice }}" data-price-ht="{{ round($pPriceHt, 2) }}">
                                    {{ $product->name }} • {{ number_format(round($pPriceHt, 2), 2) }} DH HT
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode Saisie Manuelle -->
                    <div class="manual-mode-box d-none position-relative">
                        <div class="modern-input-icon-wrap w-100">
                            <i class="fas fa-pen-nib input-icon"></i>
                            <input type="text" name="items[{index}][product_name]" class="form-control invoice-custom-input item-manual-name w-100 pe-5" placeholder="Désignation complète du produit, article ou prestation...">
                            <input type="hidden" name="items[{index}][product_sku]" class="item-manual-sku" value="">
                        </div>
                        <button type="button" onclick="switchRowToCatalog(this)" class="btn btn-sm btn-link text-muted position-absolute end-0 top-50 translate-middle-y me-1 p-1 text-decoration-none" title="Choisir du catalogue" style="font-size: 0.78rem;">
                            <i class="fas fa-list text-secondary"></i>
                        </button>
                    </div>
                </div>
            </td>
            <td class="p-2 text-md-end col-price">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">P.U. HT</label>
                <div class="modern-price-field">
                    <input type="number" step="0.01" min="0" name="items[{index}][unit_price_ht]" class="form-control invoice-custom-input unit-price-input font-monospace text-end" value="0.00" onchange="updateTotals()" onkeyup="updateTotals()" placeholder="0.00" required>
                    <span class="price-suffix">HT</span>
                </div>
            </td>
            <td class="p-2 col-qty">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Qté</label>
                <div class="modern-qty-field">
                    <input type="number" name="items[{index}][quantity]" class="form-control invoice-custom-input quantity-input font-monospace text-center" value="1" min="1" onchange="updateTotals()" onkeyup="updateTotals()" required>
                </div>
            </td>
            <td class="p-2 text-md-end col-total">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Total HT</label>
                <div class="row-total-container text-end">
                    <div class="row-total-value">
                        <span class="row-total font-monospace">0.00</span>
                        <span class="text-xs text-muted ms-0.5">DH</span>
                    </div>
                    <div class="row-total-sub">Total HT</div>
                </div>
            </td>
            <td class="pe-3 ps-1 text-center col-action">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1 d-block opacity-0">&nbsp;</label>
                <button type="button" onclick="removeItem(this)" class="btn-remove-row" title="Supprimer cette ligne">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Modal Smart Text Import -->
    <div class="modal fade" id="smartTextModal" tabindex="-1" aria-labelledby="smartTextModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg" style="overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4" style="background: #0f172a; color: #fff;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3" style="width: 36px; height: 36px; background: rgba(251, 191, 36, 0.2); color: #fbbf24;">
                            <i class="fas fa-wand-magic-sparkles"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold fs-6 text-white mb-0" id="smartTextModalLabel">Ajout intelligent d'articles par texte</h5>
                            <p class="text-white-50 small mb-0" style="font-size: 0.76rem;">Collez une liste de produits pour reconnaissance et ajout instantané</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Step 1: Input text block -->
                    <div id="smartStepInput">
                        <label class="form-label fw-bold text-dark small d-flex justify-content-between align-items-center mb-1.5">
                            <span>Saisissez ou collez vos produits (1 par ligne)</span>
                            <span class="text-muted fw-normal" style="font-size: 0.75rem;">Supporte les quantités (ex: <i>2x fx3</i>, <i>sony 24-70mm x 3</i>)</span>
                        </label>

                        <textarea id="smartTextInput" class="form-control rounded-3 font-monospace p-3" rows="6" placeholder="fx3&#10;sony 24-70mm&#10;dji mic3&#10;2x sandisk 512gb" style="font-size: 0.88rem; line-height: 1.6; resize: vertical;"></textarea>

                        <!-- Example quick chips -->
                        <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2.5">
                            <span class="text-muted small" style="font-size: 0.74rem;">Exemples rapides :</span>
                            <button type="button" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-decoration-none" onclick="appendSmartExample('fx3')">fx3</button>
                            <button type="button" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-decoration-none" onclick="appendSmartExample('sony 24-70mm')">sony 24-70mm</button>
                            <button type="button" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-decoration-none" onclick="appendSmartExample('dji mic3')">dji mic3</button>
                            <button type="button" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-decoration-none" onclick="appendSmartExample('2x sandisk 512gb')">2x sandisk 512gb</button>
                            <button type="button" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-decoration-none" onclick="appendSmartExample('tamron 28-200')">tamron 28-200</button>
                            <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-auto" style="font-size: 0.75rem;" onclick="clearSmartInput()">Effacer</button>
                        </div>

                        <div class="bg-light rounded-3 p-3 mt-3 border">
                            <div class="d-flex align-items-start gap-2 text-muted small" style="font-size: 0.78rem;">
                                <i class="fas fa-lightbulb text-warning mt-0.5"></i>
                                <div>
                                    <strong>Reconnaissance intelligente :</strong> L'algorithme détecte les modèles officiels, marques et références du catalogue (avec exclusion des accessoires non demandés). Si un article n'est pas dans le catalogue, il sera automatiquement inséré en tant qu'article libre personnalisé.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Analysis Preview Block (hidden by default) -->
                    <div id="smartStepPreview" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span>Articles analysés</span>
                                    <span class="badge bg-dark rounded-pill px-2" id="smartDetectedCountBadge">0</span>
                                </h6>
                                <p class="text-muted small mb-0" style="font-size: 0.76rem;">Vérifiez et affinez les correspondances avant l'insertion dans la facture</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-medium" onclick="backToSmartInput()">
                                <i class="fas fa-edit me-1"></i> Modifier le texte
                            </button>
                        </div>

                        <div class="table-responsive border rounded-3 mb-2" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light text-muted small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">
                                    <tr>
                                        <th style="width: 38px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="smartSelectAllCheck" checked onchange="toggleSelectAllSmart(this)" title="Tout sélectionner / désélectionner">
                                        </th>
                                        <th style="width: 22%;">Texte saisi</th>
                                        <th>Correspondance catalogue</th>
                                        <th style="width: 85px;" class="text-center">Qté</th>
                                        <th style="width: 120px;" class="text-end">P.U. HT</th>
                                    </tr>
                                </thead>
                                <tbody id="smartPreviewTableBody">
                                    <!-- Dynamic rows generated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border rounded-pill px-3 py-1.5 text-secondary" data-bs-dismiss="modal">Fermer</button>
                    <div id="smartModalFooterAction">
                        <button type="button" id="btnAnalyzeSmart" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" onclick="analyzeSmartText()">
                            <i class="fas fa-wand-magic-sparkles text-warning"></i>
                            <span>Analyser & Trouver les correspondances</span>
                        </button>
                        <button type="button" id="btnInsertSmart" class="btn btn-brand-primary rounded-pill px-4 py-2 fw-semibold d-none align-items-center gap-2 shadow-sm" onclick="insertSmartItemsIntoInvoice()">
                            <i class="fas fa-check"></i>
                            <span id="btnInsertSmartText">Insérer dans la facture</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let itemIndex = 0;
    
    // Currency configuration
    const currencySymbol = "{{ setting('currency_symbol', 'DH') }}";
    const currencyPosition = "{{ setting('currency_position', 'after') }}";

    function formatCurrency(amount) {
        let value = parseFloat(amount || 0).toFixed(2);
        return currencyPosition === 'before' 
            ? `${currencySymbol} ${value}` 
            : `${value} ${currencySymbol}`;
    }

    function addItem(mode = 'catalog') {
        const template = document.getElementById('itemRowTemplate').innerHTML;
        const tbody = document.querySelector('#itemsTable tbody');
        const rowHtml = template.replace(/{index}/g, itemIndex++);
        tbody.insertAdjacentHTML('beforeend', rowHtml);

        const newRow = tbody.lastElementChild;
        if (mode === 'manual') {
            switchRowToManual(newRow.querySelector('.manual-mode-box'));
        }
        updateTotals();
        return newRow;
    }

    function removeItem(btn) {
        btn.closest('tr').remove();
        if (document.querySelectorAll('#itemsTable tbody .item-row').length === 0) {
            addItem('catalog');
        } else {
            updateTotals();
        }
    }

    function switchRowToManual(el) {
        const row = el.closest('tr');
        if (!row) return;
        const catalogBox = row.querySelector('.catalog-mode-box');
        const manualBox = row.querySelector('.manual-mode-box');
        const productSelect = row.querySelector('.product-select');
        const nameInput = row.querySelector('.item-manual-name');

        catalogBox.classList.add('d-none');
        manualBox.classList.remove('d-none');
        
        productSelect.value = '';
        productSelect.removeAttribute('required');

        nameInput.setAttribute('required', 'required');
        nameInput.focus();
    }

    function switchRowToCatalog(el) {
        const row = el.closest('tr');
        if (!row) return;
        const catalogBox = row.querySelector('.catalog-mode-box');
        const manualBox = row.querySelector('.manual-mode-box');
        const productSelect = row.querySelector('.product-select');
        const nameInput = row.querySelector('.item-manual-name');
        const skuInput = row.querySelector('.item-manual-sku');

        manualBox.classList.add('d-none');
        catalogBox.classList.remove('d-none');

        nameInput.removeAttribute('required');
        nameInput.value = '';
        if (skuInput) skuInput.value = '';

        productSelect.setAttribute('required', 'required');
        productSelect.focus();
    }

    function updateItem(select) {
        const row = select.closest('tr');
        if (select.value === 'custom') {
            switchRowToManual(select);
            return;
        }

        const opt = select.selectedOptions[0];
        const taxPercent = {{ floatval(setting('tax_rate', 20)) }};
        const taxRate = taxPercent / 100;
        let priceHt = 0;
        if (opt && opt.dataset.priceHt !== undefined && opt.dataset.priceHt !== '') {
            priceHt = parseFloat(opt.dataset.priceHt);
        } else if (opt && opt.dataset.price !== undefined && opt.dataset.price !== '') {
            const priceTtc = parseFloat(opt.dataset.price);
            priceHt = taxRate > 0 ? (priceTtc / (1 + taxRate)) : priceTtc;
        }

        const priceInput = row.querySelector('.unit-price-input');
        if (priceInput) {
            priceInput.value = priceHt > 0 ? priceHt.toFixed(2) : '0.00';
        }
        updateTotals();
    }

    function updateTotals() {
        let subtotalHt = 0;
        const taxPercent = {{ floatval(setting('tax_rate', 20)) }};
        const taxRate = taxPercent / 100;
        const shippingCost = parseFloat(document.getElementById('shipping_cost')?.value || 0) || 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const priceInput = row.querySelector('.unit-price-input');
            const priceHt = parseFloat(priceInput ? priceInput.value : 0) || 0;
            const qty = parseInt(row.querySelector('.quantity-input')?.value || 0) || 0;
            const totalHt = Math.round(priceHt * qty * 100) / 100;
            
            const rowTotalEl = row.querySelector('.row-total');
            if (rowTotalEl) {
                rowTotalEl.textContent = totalHt.toFixed(2);
            }
            subtotalHt += totalHt;
        });

        const taxAmount = Math.round(subtotalHt * taxRate * 100) / 100;
        const totalAmount = Math.round((subtotalHt + taxAmount + shippingCost) * 100) / 100;

        const subtotalEl = document.getElementById('summarySubtotal');
        if (subtotalEl) subtotalEl.innerText = formatCurrency(subtotalHt);

        const taxEl = document.getElementById('summaryTax');
        if (taxEl) taxEl.innerText = formatCurrency(taxAmount);

        const shippingEl = document.getElementById('summaryShipping');
        if (shippingEl) shippingEl.innerText = formatCurrency(shippingCost);

        const totalEl = document.getElementById('summaryTotal');
        if (totalEl) totalEl.innerText = formatCurrency(totalAmount);
    }

    function fillCustomerInfo() {
        const select = document.getElementById('customerSelect');
        const option = select.selectedOptions[0];
        if (!option || !option.value) return;

        if (option.dataset.name) document.getElementById('customer_name').value = option.dataset.name;
        if (option.dataset.email) document.getElementById('customer_email').value = option.dataset.email;
        if (option.dataset.phone) document.getElementById('customer_phone').value = option.dataset.phone;
        if (option.dataset.address) document.getElementById('customer_address').value = option.dataset.address;
        if (option.dataset.ice) document.getElementById('ice').value = option.dataset.ice;
    }

    // Add initial row on load
    document.addEventListener('DOMContentLoaded', function() {
        addItem('catalog');
    });

    // ===== SMART TEXT IMPORT SYSTEM =====
    let smartModalInstance = null;
    window.smartAnalyzedItems = [];

    function openSmartTextModal() {
        const modalEl = document.getElementById('smartTextModal');
        if (!modalEl) return;
        if (!smartModalInstance) {
            smartModalInstance = new bootstrap.Modal(modalEl);
        }
        smartModalInstance.show();
        setTimeout(() => {
            const textarea = document.getElementById('smartTextInput');
            if (textarea) textarea.focus();
        }, 200);
    }

    function appendSmartExample(sampleText) {
        const input = document.getElementById('smartTextInput');
        if (!input) return;
        const current = input.value.trim();
        input.value = current ? (current + '\n' + sampleText) : sampleText;
        input.focus();
    }

    function clearSmartInput() {
        const input = document.getElementById('smartTextInput');
        if (input) {
            input.value = '';
            input.focus();
        }
    }

    function backToSmartInput() {
        document.getElementById('smartStepInput').classList.remove('d-none');
        document.getElementById('smartStepPreview').classList.add('d-none');
        document.getElementById('btnAnalyzeSmart').classList.remove('d-none');
        const insertBtn = document.getElementById('btnInsertSmart');
        insertBtn.classList.add('d-none');
        insertBtn.classList.remove('d-inline-flex');
    }

    function toggleSelectAllSmart(master) {
        document.querySelectorAll('.smart-item-checkbox').forEach(cb => {
            cb.checked = master.checked;
        });
        updateSmartInsertButtonCount();
    }

    function updateSmartInsertButtonCount() {
        const checkedCount = document.querySelectorAll('.smart-item-checkbox:checked').length;
        const btnText = document.getElementById('btnInsertSmartText');
        const docType = document.querySelector('select[name="type"]')?.value === 'quote' ? 'le devis' : 'la facture';
        if (btnText) {
            btnText.textContent = `Insérer ${checkedCount} article(s) dans ${docType}`;
        }
    }

    async function analyzeSmartText() {
        const text = document.getElementById('smartTextInput')?.value.trim() || '';
        if (!text) {
            alert('Veuillez saisir ou coller au moins un produit.');
            return;
        }

        const btn = document.getElementById('btnAnalyzeSmart');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Analyse intelligente en cours...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || document.querySelector('input[name="_token"]')?.value || '';

            const res = await fetch("{{ route('invoices.smart-match-text') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ text })
            });

            const data = await res.json();
            if (!data.success || !data.items || data.items.length === 0) {
                alert(data.message || 'Aucun produit détecté dans le texte.');
                btn.disabled = false;
                btn.innerHTML = origHtml;
                return;
            }

            window.smartAnalyzedItems = data.items;
            renderSmartPreview(data.items);

            // Switch to Step 2 Preview
            document.getElementById('smartStepInput').classList.add('d-none');
            document.getElementById('smartStepPreview').classList.remove('d-none');
            btn.classList.add('d-none');
            const insertBtn = document.getElementById('btnInsertSmart');
            insertBtn.classList.remove('d-none');
            insertBtn.classList.add('d-inline-flex');

            document.getElementById('smartDetectedCountBadge').textContent = data.items.length;
            updateSmartInsertButtonCount();

        } catch (err) {
            console.error('Smart text error:', err);
            alert('Une erreur est survenue lors de l\'analyse du texte.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    }

    function renderSmartPreview(items) {
        const tbody = document.getElementById('smartPreviewTableBody');
        if (!tbody) return;

        let html = '';
        items.forEach((item, idx) => {
            const best = item.best;
            const hasMatch = item.matched && best;

            let productHtml = '';
            if (hasMatch) {
                const imgHtml = best.thumbnail ? `
                    <img src="${best.thumbnail}" alt="" width="36" height="36" class="rounded-2 border flex-shrink-0" style="object-fit: cover;" onerror="this.style.display='none'">
                ` : `
                    <div class="rounded-2 border flex-shrink-0 bg-light d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-camera text-muted" style="font-size: 13px;"></i>
                    </div>
                `;

                let altsSelectHtml = '';
                if (item.alternatives && item.alternatives.length > 0) {
                    altsSelectHtml = `
                        <div class="mt-1.5">
                            <select class="form-select form-select-sm smart-product-choice" style="font-size: 0.76rem;" onchange="smartRowChoiceChanged(${idx}, this)">
                                <option value="${best.id}" selected>✓ ${escapeHtml(best.name)} (${best.price_ht} DH HT)</option>
                                ${item.alternatives.map(alt => `
                                    <option value="${alt.id}" data-name="${escapeHtml(alt.name)}" data-price-ht="${alt.price_ht}" data-sku="${escapeHtml(alt.sku || '')}" data-stock="${alt.stock}" data-thumb="${alt.thumbnail || ''}">
                                        ↔ ${escapeHtml(alt.name)} (${alt.price_ht} DH HT)
                                    </option>
                                `).join('')}
                                <option value="manual">➕ Conserver comme article libre / personnalisé</option>
                            </select>
                        </div>
                    `;
                }

                productHtml = `
                    <div class="smart-row-product-box" data-idx="${idx}">
                        <div class="d-flex align-items-center gap-2">
                            ${imgHtml}
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-bold text-dark text-truncate smart-product-name" style="font-size: 0.84rem;" title="${escapeHtml(best.name)}">${escapeHtml(best.name)}</div>
                                <div class="text-muted d-flex align-items-center gap-2" style="font-size: 0.72rem;">
                                    ${best.sku ? `<span>SKU: <strong class="font-monospace">${escapeHtml(best.sku)}</strong></span>` : ''}
                                    <span class="badge ${best.stock > 0 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'} border rounded-pill px-1.5" style="font-size: 0.68rem;">
                                        ${best.stock > 0 ? `Stock: ${best.stock}` : 'Rupture'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        ${altsSelectHtml}
                        <input type="hidden" class="smart-selected-product-id" value="${best.id}">
                        <input type="hidden" class="smart-is-manual" value="0">
                    </div>
                `;
            } else {
                productHtml = `
                    <div class="smart-row-product-box" data-idx="${idx}">
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 mb-1" style="font-size: 0.7rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i> Non trouvé au catalogue (création libre)
                        </span>
                        <input type="text" class="form-control form-control-sm smart-custom-title" value="${escapeHtml(item.parsed_query)}" placeholder="Désignation de l'article...">
                        <input type="hidden" class="smart-selected-product-id" value="">
                        <input type="hidden" class="smart-is-manual" value="1">
                    </div>
                `;
            }

            const priceVal = hasMatch ? parseFloat(best.price_ht).toFixed(2) : '0.00';

            html += `
                <tr class="smart-preview-row" data-idx="${idx}">
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input smart-item-checkbox" checked onchange="updateSmartInsertButtonCount()">
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border px-2 py-1 font-monospace fw-normal" style="font-size: 0.78rem;">
                            ${escapeHtml(item.original)}
                        </span>
                    </td>
                    <td>
                        ${productHtml}
                    </td>
                    <td class="text-center">
                        <input type="number" min="1" value="${item.qty}" class="form-control form-control-sm font-monospace text-center smart-row-qty" style="width: 65px; margin: 0 auto;">
                    </td>
                    <td class="text-end">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <input type="number" step="0.01" min="0" value="${priceVal}" class="form-control form-control-sm font-monospace text-end smart-row-price-ht" style="width: 105px;">
                            <span class="text-muted small" style="font-size: 0.72rem;">HT</span>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function smartRowChoiceChanged(idx, selectEl) {
        const row = selectEl.closest('tr');
        if (!row) return;

        const val = selectEl.value;
        const priceInput = row.querySelector('.smart-row-price-ht');
        const isManualInput = row.querySelector('.smart-is-manual');
        const prodIdInput = row.querySelector('.smart-selected-product-id');
        const nameEl = row.querySelector('.smart-product-name');

        if (val === 'manual') {
            isManualInput.value = '1';
            prodIdInput.value = '';
            if (priceInput) priceInput.value = '0.00';
        } else {
            isManualInput.value = '0';
            prodIdInput.value = val;
            const opt = selectEl.selectedOptions[0];
            if (opt && opt.dataset.priceHt && priceInput) {
                priceInput.value = parseFloat(opt.dataset.priceHt).toFixed(2);
            }
            if (opt && opt.dataset.name && nameEl) {
                nameEl.textContent = opt.dataset.name;
            }
        }
    }

    function insertSmartItemsIntoInvoice() {
        const rows = document.querySelectorAll('#smartPreviewTableBody tr.smart-preview-row');
        if (rows.length === 0) return;

        // Check if existing items table only has 1 blank untouched row
        const existingRows = document.querySelectorAll('#itemsTable tbody .item-row');
        if (existingRows.length === 1) {
            const firstSelect = existingRows[0].querySelector('.product-select');
            const firstName = existingRows[0].querySelector('.item-manual-name');
            const firstPrice = parseFloat(existingRows[0].querySelector('.unit-price-input')?.value || 0);
            if ((!firstSelect || !firstSelect.value) && (!firstName || !firstName.value.trim()) && firstPrice === 0) {
                existingRows[0].remove();
            }
        }

        let insertedCount = 0;

        rows.forEach(row => {
            const checkbox = row.querySelector('.smart-item-checkbox');
            if (!checkbox || !checkbox.checked) return;

            const isManual = row.querySelector('.smart-is-manual')?.value === '1';
            const productId = row.querySelector('.smart-selected-product-id')?.value;
            const qty = parseInt(row.querySelector('.smart-row-qty')?.value || 1, 10);
            const priceHt = parseFloat(row.querySelector('.smart-row-price-ht')?.value || 0);
            const customTitle = row.querySelector('.smart-custom-title')?.value || row.querySelector('.smart-product-name')?.textContent || '';

            if (!isManual && productId) {
                const newRow = addItem('catalog');
                const productSelect = newRow.querySelector('.product-select');
                if (productSelect) {
                    productSelect.value = productId;
                    // If product option is not in pre-rendered select, create it dynamically
                    if (productSelect.value !== productId) {
                        const opt = document.createElement('option');
                        opt.value = productId;
                        opt.textContent = customTitle;
                        opt.setAttribute('data-price-ht', priceHt);
                        productSelect.appendChild(opt);
                        productSelect.value = productId;
                    }
                    updateItem(productSelect);
                }
                const qtyInput = newRow.querySelector('.quantity-input');
                if (qtyInput) qtyInput.value = qty;
                const priceInput = newRow.querySelector('.unit-price-input');
                if (priceInput) priceInput.value = priceHt.toFixed(2);

                insertedCount++;
            } else {
                const newRow = addItem('manual');
                const nameInput = newRow.querySelector('.item-manual-name');
                if (nameInput) nameInput.value = customTitle;
                const qtyInput = newRow.querySelector('.quantity-input');
                if (qtyInput) qtyInput.value = qty;
                const priceInput = newRow.querySelector('.unit-price-input');
                if (priceInput) priceInput.value = priceHt.toFixed(2);

                insertedCount++;
            }
        });

        updateTotals();

        // Close modal
        if (smartModalInstance) {
            smartModalInstance.hide();
        }

        // Reset step
        backToSmartInput();
        clearSmartInput();

        // Show friendly visual feedback toast
        showSmartToast(`${insertedCount} article(s) inséré(s) avec succès dans le document !`);
    }

    function showSmartToast(message) {
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '99999';
        toast.innerHTML = `
            <div class="toast show align-items-center text-white bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                        <i class="fas fa-check-circle text-success fs-5"></i>
                        <span class="fw-semibold small">${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" onclick="this.closest('.position-fixed').remove()"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
    }
</script>
@endpush
