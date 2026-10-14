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
                    <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Commandes</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none text-muted font-monospace">#{{ $order->order_number }}</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold">Modifier</span>
                </div>
                <h1 class="brand-title">Modifier : <span class="font-monospace">#{{ $order->order_number }}</span></h1>
                <p class="brand-subtitle">
                    Client : <strong>{{ $order->customer_name ?: 'Client invité' }}</strong> &bull; Total : <strong>{{ $order->formatted_total }}</strong>
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
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
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

                <!-- Notes de commande -->
                <div class="product-form-card">
                    <div class="product-form-card-header">
                        <div>
                            <h2 class="product-form-card-title">
                                <i class="fas fa-comment-dots text-warning"></i> 3. Notes & Commentaires Internes
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
@endsection
