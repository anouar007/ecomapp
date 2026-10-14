@extends('layouts.app')

@section('title', 'Modifier le document — #' . ($invoice->display_number ?? $invoice->invoice_number))

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-pencil-alt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('invoices.index') }}" class="text-decoration-none text-muted">Facturation</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none text-muted">#{{ $invoice->display_number ?? $invoice->invoice_number }}</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold">Modifier</span>
                </div>
                <h1 class="brand-title">Modifier {{ $invoice->isQuote() ? 'le devis' : 'la facture' }}</h1>
                <p class="brand-subtitle">
                    Modifiez les coordonnées client, le statut de paiement, les conditions et les options d'affichage
                </p>
            </div>
        </div>

        <div class="brand-header-actions">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                <i class="fas fa-arrow-left"></i> <span>Retour au document</span>
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

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="editInvoiceForm">
        @csrf
        @method('PUT')

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
                            <p class="product-form-card-subtitle">Informations du destinataire de la facture</p>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label for="customer_name" class="form-label fw-semibold text-dark">
                                Nom du client / Raison sociale <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-user"></i></span>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $invoice->customer_name) }}" required
                                       class="form-control border-start-0" placeholder="Nom ou entreprise">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="customer_email" class="form-label fw-semibold text-dark">
                                Adresse Email
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $invoice->customer_email) }}"
                                       class="form-control border-start-0" placeholder="client@exemple.com">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="customer_phone" class="form-label fw-semibold text-dark">
                                Numéro de téléphone
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-phone"></i></span>
                                <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $invoice->customer_phone) }}"
                                       class="form-control border-start-0" placeholder="+212 600 000000">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <label for="ice" class="form-label fw-semibold text-dark">
                                ICE (Identifiant Fiscal)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="ice" id="ice" value="{{ old('ice', $invoice->ice) }}"
                                       class="form-control border-start-0" placeholder="Ex: 002345678000045">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="customer_address" class="form-label fw-semibold text-dark">
                                Adresse de facturation
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="customer_address" id="customer_address" rows="2" class="form-control border-start-0" placeholder="Adresse complète...">{{ old('customer_address', $invoice->customer_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Notes & Mentions Légales -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-comment-alt text-primary"></i> 2. Notes & Mentions
                            </h2>
                            <p class="product-form-card-subtitle">Instructions de paiement ou remarques visibles sur le document</p>
                        </div>
                    </div>
                    <div>
                        <textarea name="notes" rows="4" class="form-control" placeholder="Conditions de règlement...">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Colonne Latérale (Droite) : Paramètres & Enregistrement -->
            <div class="col-12 col-lg-4">
                <!-- Paramètres -->
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title" style="font-size: 1rem;">
                                <i class="fas fa-cog text-primary"></i> Paramètres du document
                            </h2>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <!-- Type -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Type de document <span class="text-danger">*</span></label>
                            <select name="type" required class="form-select">
                                <option value="invoice" {{ old('type', $invoice->type) == 'invoice' ? 'selected' : '' }}>📄 Facture commerciale</option>
                                <option value="quote" {{ old('type', $invoice->type) == 'quote' ? 'selected' : '' }}>📑 Devis estimatif</option>
                            </select>
                        </div>

                        <!-- Statut de paiement -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Statut du paiement <span class="text-danger">*</span></label>
                            <select name="payment_status" required class="form-select">
                                <option value="paid" {{ old('payment_status', $invoice->payment_status) == 'paid' ? 'selected' : '' }}>✅ Payé</option>
                                <option value="unpaid" {{ old('payment_status', $invoice->payment_status) == 'unpaid' ? 'selected' : '' }}>⏳ Impayé</option>
                                <option value="partial" {{ old('payment_status', $invoice->payment_status) == 'partial' ? 'selected' : '' }}>🔄 Partiel</option>
                                <option value="cancelled" {{ old('payment_status', $invoice->payment_status) == 'cancelled' ? 'selected' : '' }}>❌ Annulé</option>
                            </select>
                        </div>

                        <!-- Mode de paiement -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Mode de règlement <span class="text-danger">*</span></label>
                            <select name="payment_method" required class="form-select">
                                <option value="cash" {{ old('payment_method', $invoice->payment_method) == 'cash' ? 'selected' : '' }}>💵 Espèces</option>
                                <option value="card" {{ old('payment_method', $invoice->payment_method) == 'card' ? 'selected' : '' }}>💳 Carte bancaire</option>
                                <option value="bank_transfer" {{ old('payment_method', $invoice->payment_method) == 'bank_transfer' ? 'selected' : '' }}>🏦 Virement bancaire</option>
                                <option value="other" {{ old('payment_method', $invoice->payment_method) == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>

                        <!-- Date d'échéance -->
                        <div>
                            <label class="form-label fw-semibold text-dark small mb-1">Date d'échéance</label>
                            <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}" class="form-control">
                        </div>

                        <!-- Cachet -->
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between p-0">
                                <label class="form-check-label fw-semibold text-dark small d-flex align-items-center gap-2 cursor-pointer" for="withStampSwitchEdit">
                                    <i class="fas fa-stamp text-primary"></i> Cachet & Signature officiel
                                </label>
                                <input class="form-check-input ms-0 cursor-pointer" type="checkbox" name="with_stamp" value="1" id="withStampSwitchEdit" {{ old('with_stamp', $invoice->with_stamp) ? 'checked' : '' }} style="width: 2.2rem; height: 1.2rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif -->
                <div class="product-form-card mb-4" style="background: #fafbfc;">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title" style="font-size: 1rem;">
                                <i class="fas fa-info-circle text-primary"></i> Informations Document
                            </h2>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3 small">
                        <div class="d-flex justify-content-between text-muted">
                            <span>Numéro officiel :</span>
                            <span class="fw-bold font-monospace text-dark">#{{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Articles :</span>
                            <span class="fw-semibold text-dark">{{ $invoice->items->count() }} ligne(s)</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted">
                            <span>Émis le :</span>
                            <span class="fw-semibold text-dark">{{ $invoice->issued_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <hr class="my-1 border-secondary-subtle">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-bold text-dark fs-6">Montant Total :</span>
                            <span class="fw-bold fs-4 text-primary font-heading">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>

                    <!-- Boutons d'Action -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-brand-primary justify-content-center py-2.5 fw-bold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Enregistrer les modifications
                        </button>
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light border justify-content-center py-2 text-muted fw-semibold">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
