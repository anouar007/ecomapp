@extends('layouts.app')

@section('title', 'Grand Livre des Règlements & Preuves de Paiement')

@push('styles')
<style>
/* ==========================================================================
   MOBILE-FIRST CLIENT TRANSACTIONS LEDGER STYLES
   ========================================================================== */
.chips-scroll-credit {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: center;
    overflow-x: visible;
}
.chip-credit {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.35rem 0.8rem;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.chip-credit:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chip-credit.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}
.chip-credit.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

.trx-mobile-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.trx-mobile-card:active {
    transform: scale(0.995);
}

.proof-thumb-table {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.proof-thumb-table:hover {
    transform: scale(1.08);
}
.proof-thumb-mobile {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    flex-shrink: 0;
}
.credit-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.credit-trx-table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0 !important;
}
.credit-trx-table th {
    letter-spacing: 0.03em;
    white-space: nowrap;
}
.credit-trx-table td {
    vertical-align: middle;
}
.trx-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.1rem 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- En-tête de page (Standard Products Style) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h1 class="brand-title">Grand Livre des Règlements & Preuves Photos</h1>
                <p class="brand-subtitle">Historique chronologique et vérifiable de chaque transaction financière avec justificatifs photographiques</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('client-credits.index') }}" class="btn-brand-outline">
                <i class="fas fa-wallet me-1.5" style="color: var(--primary-color)"></i> <span>Vue Comptes Clients</span>
            </a>
            <button type="button" class="btn-brand-primary" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                <i class="fas fa-plus me-1"></i> <span>Nouveau Règlement</span>
            </button>
        </div>
    </div>

    <!-- Navigation Croisée ERP (Clients <-> Crédits <-> Grand Livre) -->
    <div class="subnav-pills-bar">
        <a href="{{ route('customers.index') }}" class="subnav-pill">
            <i class="fas fa-users"></i>
            <span>Fiches & Profils Clients</span>
        </a>
        <a href="{{ route('client-credits.index') }}" class="subnav-pill">
            <i class="fas fa-wallet text-danger"></i>
            <span>Crédits, Créances & Dettes</span>
        </a>
        <a href="{{ route('client-credits.transactions') }}" class="subnav-pill active">
            <i class="fas fa-list-check text-primary"></i>
            <span>Grand Livre des Règlements</span>
        </a>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- 4 Financial Summary KPI Cards (Standard Products Style) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('client-credits.transactions', ['type' => 'payment_received']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'payment_received' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Règlements Encaissés</div>
                        <div class="stat-kpi-value text-success">+{{ currency($totalPaymentsReceived ?? $totalIn ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-arrow-down text-success me-1"></i>Entrées de trésorerie</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('client-credits.transactions', ['type' => 'credit_given']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'credit_given' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Crédits Accordés</div>
                        <div class="stat-kpi-value text-danger">{{ currency($totalCreditsGiven ?? $totalOut ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-arrow-up text-danger me-1"></i>Ventes à crédit</div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('client-credits.transactions', ['type' => 'credit_note']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'credit_note' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Avoirs & Remboursements</div>
                        <div class="stat-kpi-value text-info">{{ currency(($totalCreditNotes ?? 0) + ($totalRefunds ?? 0)) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-file-invoice text-info me-1"></i>Avoirs et retours</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('client-credits.transactions', ['has_proof' => 'yes']) }}" class="stat-kpi-card text-decoration-none {{ request('has_proof') === 'yes' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Justificatifs Photos</div>
                        <div class="stat-kpi-value text-dark">{{ $withProofCount ?? 0 }} <span class="fs-6 fw-normal text-muted">/ {{ $transactions->total() }}</span></div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-camera text-primary me-1"></i>Preuves vérifiées</div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Swipeable Chips Filter Bar (Mobile & Desktop) -->
    <div class="mb-3">
        <div class="chips-scroll-credit">
            <a href="{{ route('client-credits.transactions') }}" 
               class="chip-credit {{ !request()->filled('type') && !request()->filled('has_proof') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i>
                <span>Toutes les opérations</span>
                <span class="badge bg-secondary rounded-pill px-1.5" style="font-size: 0.65rem;">{{ $transactions->total() }}</span>
            </a>
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'type']), ['type' => 'payment_received'])) }}" 
               class="chip-credit {{ request('type') === 'payment_received' ? 'active' : '' }}">
                <i class="fas fa-arrow-down text-success"></i>
                <span>Règlements reçus</span>
            </a>
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'type']), ['type' => 'credit_given'])) }}" 
               class="chip-credit {{ request('type') === 'credit_given' ? 'active' : '' }}">
                <i class="fas fa-hand-holding-dollar text-warning"></i>
                <span>Crédits accordés</span>
            </a>
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'type']), ['type' => 'credit_note'])) }}" 
               class="chip-credit {{ request('type') === 'credit_note' ? 'active' : '' }}">
                <i class="fas fa-file-invoice text-info"></i>
                <span>Avoirs clients</span>
            </a>
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'type']), ['type' => 'refund_to_client'])) }}" 
               class="chip-credit {{ request('type') === 'refund_to_client' ? 'active' : '' }}">
                <i class="fas fa-arrow-up text-danger"></i>
                <span>Remboursements versés</span>
            </a>
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'has_proof']), ['has_proof' => 'yes'])) }}" 
               class="chip-credit {{ request('has_proof') === 'yes' ? 'active' : '' }}">
                <i class="fas fa-camera text-primary"></i>
                <span>Avec preuve photo</span>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Standard Products Style) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('client-credits.transactions') }}" class="row g-2.5 align-items-center">
            <div class="col-12 col-md-3">
                <div class="brand-search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Réf, client, note...">
                </div>
            </div>

            <div class="col-6 col-md-2">
                <select name="customer_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les clients</option>
                    @foreach($allCustomersList as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>Règlement Reçu</option>
                    <option value="credit_given" {{ request('type') == 'credit_given' ? 'selected' : '' }}>Crédit Accordé</option>
                    <option value="credit_note" {{ request('type') == 'credit_note' ? 'selected' : '' }}>Avoir Accordé</option>
                    <option value="refund_to_client" {{ request('type') == 'refund_to_client' ? 'selected' : '' }}>Remboursement</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="store_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Toutes les caisses</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>
                            {{ $st->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-1">
                <select name="has_proof" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Preuve: Tout</option>
                    <option value="yes" {{ request('has_proof') == 'yes' ? 'selected' : '' }}>Avec</option>
                    <option value="no" {{ request('has_proof') == 'no' ? 'selected' : '' }}>Sans</option>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1.5">
                <button type="submit" class="btn-brand-primary btn-sm flex-fill" title="Appliquer les filtres">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                @if(request()->anyFilled(['search', 'customer_id', 'store_id', 'type', 'has_proof', 'date_from', 'date_to']))
                    <a href="{{ route('client-credits.transactions') }}" class="btn-brand-light btn-sm flex-shrink-0" title="Réinitialiser tous les filtres">
                        <i class="fas fa-redo"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Ledger Table Card Standard (Standard Products Style) -->
    <div class="brand-table-card mb-4">
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="credit-table-wrap d-none d-lg-block">
            <table class="table align-middle mb-0 credit-trx-table">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Date & N° Réf</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Client</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Opération & Mode</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem;">Preuve Photo</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Montant</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Solde Après</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Opérateur & Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($transactions as $trx)
                    <tr class="hover-bg-light">
                        <!-- Date & Ref -->
                        <td class="ps-3 py-3">
                            <div class="fw-bold font-monospace text-dark" style="font-size: 0.8rem;">{{ $trx->transaction_number }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $trx->payment_date->format('d/m/Y à H:i') }}</div>
                            @if($trx->reference_number)
                                <div class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 0.65rem;">
                                    Réf: {{ $trx->reference_number }}
                                </div>
                            @endif
                        </td>

                        <!-- Client -->
                        <td class="py-3">
                            <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.88rem;">
                                {{ $trx->customer->name ?? 'Client inconnu' }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                {{ $trx->customer->customer_code ?? '' }} @if($trx->customer?->phone)• {{ $trx->customer->phone }}@endif
                            </div>
                        </td>

                        <!-- Type & Payment Method -->
                        <td class="py-3">
                            <span class="badge {{ $trx->type_badge_class }} border rounded-pill px-2.5 py-1 fw-semibold mb-1 d-inline-block" style="font-size: 0.72rem;">
                                {{ $trx->type_label }}
                            </span>
                            <div class="text-muted d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.72rem;">
                                <span><i class="fas fa-credit-card me-1 opacity-50"></i>{{ $trx->payment_method_label }}</span>
                                @if($trx->store)
                                    <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                        <i class="fas fa-store me-1 text-primary"></i>{{ $trx->store->name }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Proof of payment photo -->
                        <td class="py-3 text-center">
                            @if($trx->proof_image_url)
                                <img src="{{ $trx->proof_image_url }}" alt="Preuve" class="proof-thumb-table shadow-xs" 
                                     onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')"
                                     title="Cliquer pour agrandir le justificatif">
                            @else
                                <span class="text-muted small fst-italic" style="font-size: 0.72rem;">—</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="py-3 text-end">
                            <div class="fw-bold fs-6 {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                            </div>
                            @if($trx->notes)
                                <div class="text-muted" style="max-width: 240px; font-size: 0.7rem; margin-left: auto;">
                                    {{ $trx->notes }}
                                </div>
                            @endif
                        </td>

                        <!-- Balance After -->
                        <td class="py-3 text-end font-monospace small">
                            <div class="fw-semibold {{ $trx->balance_after > 0 ? 'text-danger' : ($trx->balance_after < 0 ? 'text-info' : 'text-success') }}">
                                {{ currency($trx->balance_after) }}
                            </div>
                            <div class="text-muted" style="font-size: 0.68rem;">
                                {{ $trx->balance_after > 0 ? 'Client doit' : ($trx->balance_after < 0 ? 'Avoir client' : 'Soldé') }}
                            </div>
                        </td>

                        <!-- Operator & Action -->
                        <td class="pe-3 py-3 text-end small text-muted text-nowrap">
                            <div>
                                <i class="fas fa-user-circle me-1 opacity-50"></i>{{ $trx->createdBy->name ?? 'Système' }}
                            </div>
                            @if(auth()->user()->hasRole('Admin'))
                            <div class="mt-1">
                                <form method="POST" action="{{ route('client-credits.transactions.destroy', $trx->id) }}" onsubmit="return confirm('Confirmez-vous l\'annulation de cette transaction ? Le solde du client sera réajusté.');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-light text-danger border rounded-pill px-2.5 py-0.5" title="Annuler cette transaction">
                                        <i class="fas fa-trash me-1" style="font-size: 0.65rem;"></i>Annuler
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-25"></i>
                                <p class="mb-0 fw-semibold">Aucune transaction trouvée.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View (d-block d-lg-none) -->
        <div class="d-block d-lg-none p-3 space-y-3">
            @forelse($transactions as $trx)
            <div class="trx-mobile-card p-3">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                            <span class="badge bg-light text-dark font-monospace border px-2 py-0.5" style="font-size: 0.72rem;">
                                {{ $trx->transaction_number }}
                            </span>
                            <span class="badge {{ $trx->type_badge_class }} border rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                {{ $trx->type_label }}
                            </span>
                        </div>
                        <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.95rem;">
                            {{ $trx->customer->name ?? 'Client' }}
                        </a>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            {{ $trx->payment_date->format('d/m/Y à H:i') }} • {{ $trx->payment_method_label }}
                        </div>
                    </div>

                    <!-- Amount & Solde Pill -->
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold fs-6 {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                        </div>
                        <div class="badge {{ $trx->balance_after > 0 ? 'bg-danger-subtle text-danger' : ($trx->balance_after < 0 ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success') }} rounded-pill font-monospace px-2 py-0.5 mt-0.5" style="font-size: 0.68rem;">
                            Solde: {{ currency($trx->balance_after) }}
                        </div>
                    </div>
                </div>

                @if($trx->notes)
                <div class="bg-light rounded-3 p-2 mb-2 small text-muted fst-italic" style="font-size: 0.74rem;">
                    <i class="fas fa-comment-dots text-secondary opacity-50 me-1"></i>{{ $trx->notes }}
                </div>
                @endif

                <!-- Proof Image Box on Mobile -->
                @if($trx->proof_image_url)
                <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-2 border mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $trx->proof_image_url }}" alt="Preuve" class="proof-thumb-mobile shadow-xs"
                             onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')">
                        <div class="small">
                            <div class="fw-semibold text-dark" style="font-size: 0.74rem;">Preuve de paiement</div>
                            <span class="text-muted" style="font-size: 0.68rem;">Photo / Reçu bancaire</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-white bg-white border rounded-pill px-2.5 py-1 text-primary shadow-xs"
                            onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')"
                            style="font-size: 0.74rem;">
                        <i class="fas fa-expand me-1"></i> Agrandir
                    </button>
                </div>
                @endif

                <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted" style="font-size: 0.72rem;">
                    <div>
                        <i class="fas fa-user-circle me-1 opacity-50"></i>{{ $trx->createdBy->name ?? 'Système' }}
                    </div>
                    @if(auth()->user()->hasRole('Admin'))
                    <form method="POST" action="{{ route('client-credits.transactions.destroy', $trx->id) }}" onsubmit="return confirm('Confirmez-vous l\'annulation de cette transaction ? Le solde du client sera réajusté.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-light text-danger border rounded-pill px-2.5 py-1" style="font-size: 0.7rem;">
                            <i class="fas fa-trash me-1"></i> Annuler l'opération
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted small">
                <i class="fas fa-receipt fa-2x mb-2 text-secondary opacity-25"></i>
                <p class="mb-0">Aucune transaction trouvée.</p>
            </div>
            @endforelse
        </div>

        @if($transactions->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Zoom Preuve Photo Fullscreen -->
<div class="modal fade" id="proofViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice text-warning"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 fs-6" id="proofModalTitle">Justificatif de Paiement</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;" id="proofModalSub">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-2 text-center bg-black bg-opacity-10">
                <img id="proofModalImage" src="" alt="Preuve" style="max-width: 100%; max-height: 75vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            </div>
            <div class="modal-footer bg-white px-3 py-2 border-0 justify-content-between">
                <a id="proofDownloadBtn" href="" download class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Télécharger le document
                </a>
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Re-use New Payment Modal -->
@include('client_credits.partials.payment_modal', ['allCustomersList' => $allCustomersList, 'stores' => $stores])

@push('scripts')
<script>
function previewProofModal(imageUrl, trxNumber, customerName) {
    document.getElementById('proofModalTitle').textContent = `Preuve de Paiement • ${trxNumber}`;
    document.getElementById('proofModalSub').textContent = `Client : ${customerName}`;
    document.getElementById('proofModalImage').src = imageUrl;
    document.getElementById('proofDownloadBtn').href = imageUrl;
    const modal = new bootstrap.Modal(document.getElementById('proofViewModal'));
    modal.show();
}
</script>
@endpush
@endsection
