@extends('layouts.app')

@section('title', 'Nouveau document — Facture / Devis')

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
                    <div class="product-form-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-boxes text-primary"></i> 2. Articles & Produits
                            </h2>
                            <p class="product-form-card-subtitle">Ajoutez les articles, quantités et prix de la facture</p>
                        </div>
                        <button type="button" onclick="addItem()" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                            <i class="fas fa-plus"></i> <span>Ajouter une ligne</span>
                        </button>
                    </div>

                    <div class="table-responsive border rounded-3 overflow-hidden mb-3 invoice-items-wrap">
                        <table class="table table-hover align-middle mb-0" id="itemsTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th style="min-width: 220px;">Produit / Description</th>
                                    <th class="text-center" style="width: 110px;">Qté</th>
                                    <th class="text-end" style="width: 130px;">Prix Unitaire</th>
                                    <th class="text-end" style="width: 140px;">Total Ligne</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Lignes d'articles dynamiques -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" onclick="addItem()" class="btn btn-sm btn-brand-light w-100 py-2 d-flex align-items-center justify-content-center gap-1.5">
                        <i class="fas fa-plus-circle text-primary"></i>
                        <span>Ajouter un autre article</span>
                    </button>
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
            <td class="p-2.5 col-product">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Produit / Description</label>
                <select name="items[{index}][product_id]" class="form-select form-select-sm product-select" onchange="updateItem(this)" required>
                    <option value="">-- Sélectionner un produit --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ number_format($product->price, 2) }})</option>
                    @endforeach
                </select>
            </td>
            <td class="p-2.5 col-qty">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Qté</label>
                <input type="number" name="items[{index}][quantity]" class="form-control form-control-sm text-center quantity-input" value="1" min="1" onchange="updateTotals()" onkeyup="updateTotals()" required>
            </td>
            <td class="p-2.5 text-md-end col-price">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">P.U.</label>
                <div class="text-muted font-monospace unit-price">0.00</div>
            </td>
            <td class="p-2.5 text-md-end col-total">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1">Total</label>
                <div class="fw-bold text-dark font-monospace row-total">0.00</div>
            </td>
            <td class="p-2.5 text-center col-action">
                <label class="d-md-none form-label text-muted small fw-semibold mb-1 d-block opacity-0">&nbsp;</label>
                <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-light border-0 text-danger p-1" title="Supprimer la ligne">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    </template>
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

    function addItem() {
        const template = document.getElementById('itemRowTemplate').innerHTML;
        const tbody = document.querySelector('#itemsTable tbody');
        const rowHtml = template.replace(/{index}/g, itemIndex++);
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }

    function removeItem(btn) {
        btn.closest('tr').remove();
        updateTotals();
    }

    function updateItem(select) {
        const row = select.closest('tr');
        const price = parseFloat(select.selectedOptions[0]?.dataset.price || 0);
        row.querySelector('.unit-price').textContent = price.toFixed(2);
        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.unit-price').textContent || 0);
            const qty = parseInt(row.querySelector('.quantity-input').value || 0);
            const total = price * qty;
            row.querySelector('.row-total').textContent = total.toFixed(2);
            subtotal += total;
        });

        const taxPercent = {{ floatval(setting('tax_rate', 20)) }};
        const taxRate = taxPercent / 100;
        const totalAmount = subtotal;
        const taxAmount = taxRate > 0 ? (totalAmount - (totalAmount / (1 + taxRate))) : 0;
        const subtotalNet = totalAmount - taxAmount;

        document.getElementById('summarySubtotal').innerText = formatCurrency(subtotalNet);
        document.getElementById('summaryTax').innerText = formatCurrency(taxAmount);
        document.getElementById('summaryTotal').innerText = formatCurrency(totalAmount);
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
        addItem();
    });
</script>
@endpush
