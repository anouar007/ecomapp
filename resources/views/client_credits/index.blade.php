@extends('layouts.app')

@section('title', 'Gestion des Crédits, Créances & Règlements Clients')

@push('styles')
<style>
.stat-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.25rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.stat-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.06);
}
.kpi-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
}
.proof-thumb {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.proof-thumb:hover {
    transform: scale(1.08);
}
.nav-pills-custom .nav-link {
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    padding: 0.45rem 1rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    transition: all 0.15s ease;
}
.nav-pills-custom .nav-link.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                    <i class="fas fa-wallet me-1"></i> Trésorerie Clients
                </span>
                <span class="text-muted small">• Créances, Avoirs & Historique</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Gestion des Crédits & Règlements Clients</h1>
            <p class="text-muted small mb-0">Suivez les soldes débiteurs (ce que les clients vous doivent) et créditeurs (vos avoirs envers les clients) avec preuves de paiement.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('client-credits.transactions') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-xs">
                <i class="fas fa-list-check me-1.5"></i> Grand Livre des Règlements
            </a>
            <button type="button" class="btn btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                <i class="fas fa-plus-circle me-1.5"></i> Enregistrer un Règlement / Avoir
            </button>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- 4 Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Créances Clients (Clients owe store) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Créances Clients (Dû au magasin)</span>
                    <div class="kpi-icon-wrap bg-danger-subtle text-danger">
                        <i class="fas fa-arrow-down-long"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-danger mb-1">{{ currency($totalDebtors) }}</div>
                <div class="small text-muted d-flex align-items-center gap-1">
                    <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $activeDebtorCount }} clients</span>
                    <span>ont un solde débiteur</span>
                </div>
            </div>
        </div>

        <!-- 2. Dettes envers Clients (Store owes client / Avoirs) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Avoirs Clients (Dû aux clients)</span>
                    <div class="kpi-icon-wrap bg-info-subtle text-info">
                        <i class="fas fa-arrow-up-long"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-info mb-1">{{ currency($totalCreditors) }}</div>
                <div class="small text-muted d-flex align-items-center gap-1">
                    <span class="badge bg-info-subtle text-info rounded-pill">{{ $activeCreditorCount }} clients</span>
                    <span>ont un solde créditeur</span>
                </div>
            </div>
        </div>

        <!-- 3. Solde Net Trésorerie -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Solde Net Trésorerie</span>
                    <div class="kpi-icon-wrap {{ $netBalance >= 0 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning' }}">
                        <i class="fas fa-scale-balanced"></i>
                    </div>
                </div>
                <div class="h4 fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-warning' }} mb-1">
                    {{ currency($netBalance) }}
                </div>
                <div class="small text-muted">
                    {{ $netBalance >= 0 ? 'Balance nette en votre faveur' : 'Avoirs supérieurs aux créances' }}
                </div>
            </div>
        </div>

        <!-- 4. Règlements Encaissés ce mois -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Encaissé ce mois-ci</span>
                    <div class="kpi-icon-wrap bg-success-subtle text-success">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                </div>
                <div class="h4 fw-bold text-success mb-1">{{ currency($collectedThisMonth) }}</div>
                <div class="small text-muted">Paiements clients reçus en {{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Left: Customer Balances Table -->
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                
                <!-- Table Filters Bar -->
                <div class="p-3 border-bottom bg-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2.5">
                        <!-- Navigation Tabs / Filters -->
                        <div class="nav nav-pills nav-pills-custom gap-1.5" role="tablist">
                            <a href="{{ route('client-credits.index', ['filter' => 'all']) }}" class="nav-link {{ $filter === 'all' ? 'active' : '' }}">
                                Tous les comptes
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'debtors']) }}" class="nav-link {{ $filter === 'debtors' ? 'active' : '' }}">
                                <i class="fas fa-arrow-down text-danger me-1"></i> Clients Débiteurs ({{ $activeDebtorCount }})
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'creditors']) }}" class="nav-link {{ $filter === 'creditors' ? 'active' : '' }}">
                                <i class="fas fa-arrow-up text-info me-1"></i> Avoirs Clients ({{ $activeCreditorCount }})
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'settled']) }}" class="nav-link {{ $filter === 'settled' ? 'active' : '' }}">
                                Comptes Soldés (0 DH)
                            </a>
                        </div>

                        <!-- Search Form -->
                        <form method="GET" action="{{ route('client-credits.index') }}" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="Nom, code, tél...">
                            </div>
                            @if(request('search'))
                                <a href="{{ route('client-credits.index', ['filter' => $filter]) }}" class="btn btn-sm btn-light border" title="Réinitialiser">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Customers Table -->
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Client</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Statut du Solde</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Plafond de Crédit</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Dernière Opération</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($customers as $c)
                            <tr class="hover-bg-light">
                                <!-- Client Info -->
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <a href="{{ route('customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none hover-primary text-truncate">
                                                    {{ $c->name }}
                                                </a>
                                                @if($c->customer_code)
                                                    <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0" style="font-size: 0.65rem;">
                                                        {{ $c->customer_code }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 text-muted small mt-0.5" style="font-size: 0.75rem;">
                                                @if($c->phone)
                                                    <span><i class="fas fa-phone me-1 opacity-50"></i>{{ $c->phone }}</span>
                                                @endif
                                                @if($c->city)
                                                    <span>• {{ $c->city }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Balance Status -->
                                <td class="py-3">
                                    @if($c->current_balance > 0.01)
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; width: fit-content;">
                                                <i class="fas fa-arrow-down"></i> Doit : {{ currency($c->current_balance) }}
                                            </span>
                                            <span class="text-muted mt-0.5" style="font-size: 0.7rem;">Créance client à encaisser</span>
                                        </div>
                                    @elseif($c->current_balance < -0.01)
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.78rem; width: fit-content;">
                                                <i class="fas fa-arrow-up"></i> Avoir : {{ currency(abs($c->current_balance)) }}
                                            </span>
                                            <span class="text-muted mt-0.5" style="font-size: 0.7rem;">Dû au client par le magasin</span>
                                        </div>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="fas fa-check me-0.5"></i> Compte Soldé (0 DH)
                                        </span>
                                    @endif
                                </td>

                                <!-- Credit Limit -->
                                <td class="py-3">
                                    @if($c->credit_limit > 0)
                                        <div class="fw-semibold text-dark small">{{ currency($c->credit_limit) }}</div>
                                        @php
                                            $usage = min(100, max(0, ($c->current_balance / $c->credit_limit) * 100));
                                            $barColor = $usage >= 90 ? 'bg-danger' : ($usage >= 70 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress mt-1" style="height: 5px; width: 100px;">
                                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $usage }}%;"></div>
                                        </div>
                                        <div class="text-muted mt-0.5" style="font-size: 0.68rem;">{{ round($usage) }}% utilisé</div>
                                    @else
                                        <span class="text-muted small">Aucun plafond</span>
                                    @endif
                                </td>

                                <!-- Last Operation -->
                                <td class="py-3">
                                    @php
                                        $lastTrx = $c->transactions()->first();
                                    @endphp
                                    @if($lastTrx)
                                        <div class="small fw-medium text-dark">{{ currency($lastTrx->amount) }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            {{ $lastTrx->payment_date->diffForHumans() }}
                                            @if($lastTrx->proof_image)
                                                <i class="fas fa-paperclip text-primary ms-1" title="Preuve de paiement attachée"></i>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="pe-3 py-3 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-2.5 py-1 shadow-xs fw-semibold"
                                                onclick="openNewPaymentForCustomer({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->current_balance }})"
                                                title="Enregistrer un règlement / avoir">
                                            <i class="fas fa-coins me-1"></i> Règlement
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-secondary"
                                                onclick="openLimitModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->credit_limit }})"
                                                title="Modifier le plafond de crédit">
                                            <i class="fas fa-sliders"></i>
                                        </button>
                                        <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-primary" title="Fiche client">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-wallet fa-3x mb-3 text-secondary opacity-25"></i>
                                        <p class="mb-0 fw-semibold">Aucun client trouvé pour ce filtre.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $customers->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Recent Payment Ledger Feed with Proofs -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-3.5 border-bottom bg-white d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Derniers Règlements</h6>
                        <span class="text-muted small">Avec justificatifs & preuves photos</span>
                    </div>
                    <a href="{{ route('client-credits.transactions') }}" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-primary border" style="font-size: 0.75rem;">
                        Tout voir <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($recentTransactions as $trx)
                    <div class="list-group-item p-3 hover-bg-light">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                            <div class="min-w-0">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none small text-truncate">
                                        {{ $trx->customer->name ?? 'Client' }}
                                    </a>
                                    <span class="badge {{ $trx->type_badge_class }} border rounded-pill" style="font-size: 0.65rem;">
                                        {{ $trx->type_label }}
                                    </span>
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem;">
                                    {{ $trx->payment_date->format('d/m/Y à H:i') }} • {{ $trx->payment_method_label }}
                                    @if($trx->reference_number)
                                        (Réf: {{ $trx->reference_number }})
                                    @endif
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }} small">
                                    {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                                </div>
                                <div class="text-muted" style="font-size: 0.68rem;">
                                    Solde: {{ currency($trx->balance_after) }}
                                </div>
                            </div>
                        </div>

                        <!-- Proof of payment thumbnail if attached -->
                        @if($trx->proof_image_url)
                        <div class="d-flex align-items-center gap-2 mt-2 p-1.5 rounded-3 bg-light border">
                            <img src="{{ $trx->proof_image_url }}" alt="Preuve" class="proof-thumb" 
                                 onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')">
                            <div class="small min-w-0">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.74rem;">Preuve de paiement attachée</div>
                                <a href="javascript:void(0)" class="text-primary text-decoration-none" style="font-size: 0.7rem;"
                                   onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')">
                                    <i class="fas fa-expand me-1"></i> Agrandir l'image
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($trx->notes)
                        <div class="text-muted small fst-italic mt-1.5 bg-light p-1.5 rounded" style="font-size: 0.72rem;">
                            <i class="fas fa-comment-dots me-1 opacity-50"></i> {{ $trx->notes }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">
                        Aucun règlement enregistré récemment.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: ENREGISTRER UN RÈGLEMENT / OPÉRATION FINANCIÈRE  -->
<!-- ======================================================= -->
<div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('client-credits.transactions.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 fs-6" id="newPaymentModalLabel">Enregistrer une Opération / Règlement</h5>
                        <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Mise à jour du compte client et justificatif de paiement</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Select Customer -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">Client concerné <span class="text-danger">*</span></label>
                    <select name="customer_id" id="modal_customer_id" class="form-select rounded-3" required onchange="onModalCustomerChange(this)">
                        <option value="">Sélectionnez un client...</option>
                        @foreach($allCustomersList as $cust)
                            <option value="{{ $cust->id }}" data-balance="{{ (float)$cust->current_balance }}">
                                {{ $cust->name }} ({{ $cust->customer_code ?? 'ID #'.$cust->id }}) — Solde : {{ currency($cust->current_balance) }}
                            </option>
                        @endforeach
                    </select>
                    <div id="modalCustomerBalancePreview" class="d-none mt-2 p-2 rounded-3 bg-light border small">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Operation Type & Amount -->
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark mb-1">Type d'opération <span class="text-danger">*</span></label>
                        <select name="type" id="modal_type" class="form-select rounded-3" required>
                            <option value="payment_received" selected>Règlement reçu du client (Diminue la dette du client)</option>
                            <option value="refund_to_client">Remboursement versé au client (Diminue l'avoir du client)</option>
                            <option value="credit_given">Vente à crédit accordée (Augmente la dette du client)</option>
                            <option value="credit_note">Avoir accordé au client (Augmente la dette du magasin)</option>
                            <option value="adjustment">Ajustement manuel de solde</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Montant (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" id="modal_amount" class="form-control rounded-start-3 fw-bold" placeholder="0.00" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method & Reference -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select rounded-3" required>
                            <option value="especes" selected>Espèces / Cash</option>
                            <option value="virement">Virement bancaire</option>
                            <option value="cheque">Chèque bancaire</option>
                            <option value="versement">Versement bancaire</option>
                            <option value="tpe">Carte bancaire (TPE)</option>
                            <option value="avoir">Déduction sur Avoir</option>
                            <option value="autre">Autre mode</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">N° Réf. Transaction / N° Chèque</label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="Ex: CHQ-981244 ou VIR-BAM-0021">
                    </div>
                </div>

                <!-- Date & Proof Image Upload -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de l'opération</label>
                        <input type="datetime-local" name="payment_date" class="form-control rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-camera text-primary me-1"></i> Preuve de paiement (Image reçue / photo reçu)
                        </label>
                        <input type="file" name="proof_image" accept="image/*" class="form-control rounded-3" id="proof_image_input" onchange="previewSelectedImage(this)">
                        <div class="form-text" style="font-size: 0.72rem;">Formats acceptés : JPG, PNG, WEBP (Max 5MB).</div>
                    </div>
                </div>

                <!-- Image preview container -->
                <div id="imagePreviewBox" class="d-none mb-3 p-2 rounded-3 bg-light border text-center">
                    <img id="imagePreviewTarget" src="" alt="Aperçu Preuve" style="max-height: 180px; max-width: 100%; border-radius: 8px;">
                </div>

                <!-- Notes / Motifs -->
                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Notes / Motif du règlement</label>
                    <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Précisez le détail (ex: Avance sur commande #1042, solde facture FAC-2026-004)..."></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-xs">
                    <i class="fas fa-check-circle me-1.5"></i> Valider l'Opération
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: MODIFIER LE PLAFOND DE CRÉDIT CLIENT            -->
<!-- ======================================================= -->
<div class="modal fade" id="creditLimitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="creditLimitForm" method="POST" action="" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <h6 class="modal-title fw-bold mb-0 fs-6">Plafond de Crédit</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-muted small mb-2" id="limitCustomerName">—</div>
                <label class="form-label small fw-bold text-dark mb-1">Montant Max Autorisé (DH)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" name="credit_limit" id="limitInput" class="form-control rounded-start-3 fw-bold" required>
                    <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                </div>
                <div class="form-text" style="font-size: 0.72rem;">0 = Aucun plafond (crédit non accordé par défaut).</div>
            </div>
            <div class="modal-footer bg-light px-3 py-2 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: ZOOM PREUVE DE PAIEMENT (IMAGE FULLSCREEN)      -->
<!-- ======================================================= -->
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

@push('scripts')
<script>
function openNewPaymentForCustomer(customerId, customerName, currentBalance) {
    const select = document.getElementById('modal_customer_id');
    if (select) {
        select.value = customerId;
        onModalCustomerChange(select);
    }
    const modal = new bootstrap.Modal(document.getElementById('newPaymentModal'));
    modal.show();
}

function onModalCustomerChange(select) {
    const selected = select.options[select.selectedIndex];
    const preview = document.getElementById('modalCustomerBalancePreview');
    if (!selected || !selected.value) {
        preview.classList.add('d-none');
        return;
    }
    const balance = parseFloat(selected.getAttribute('data-balance') || 0);
    preview.classList.remove('d-none');

    if (balance > 0.01) {
        preview.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i>Le client doit actuellement au magasin : ${balance.toLocaleString('fr-FR', {minimumFractionDigits: 2})} DH</span>`;
        document.getElementById('modal_type').value = 'payment_received';
        document.getElementById('modal_amount').value = balance.toFixed(2);
    } else if (balance < -0.01) {
        const absVal = Math.abs(balance);
        preview.innerHTML = `<span class="text-info fw-bold"><i class="fas fa-arrow-up me-1"></i>Le magasin doit actuellement à ce client (Avoir) : ${absVal.toLocaleString('fr-FR', {minimumFractionDigits: 2})} DH</span>`;
        document.getElementById('modal_type').value = 'refund_to_client';
        document.getElementById('modal_amount').value = absVal.toFixed(2);
    } else {
        preview.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check me-1"></i>Le compte du client est actuellement soldé (0 DH).</span>`;
    }
}

function previewSelectedImage(input) {
    const box = document.getElementById('imagePreviewBox');
    const target = document.getElementById('imagePreviewTarget');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            target.src = e.target.result;
            box.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        box.classList.add('d-none');
    }
}

function openLimitModal(customerId, customerName, currentLimit) {
    document.getElementById('creditLimitForm').action = `/customers/${customerId}/credit-limit`;
    document.getElementById('limitCustomerName').textContent = `Client : ${customerName}`;
    document.getElementById('limitInput').value = currentLimit;
    const modal = new bootstrap.Modal(document.getElementById('creditLimitModal'));
    modal.show();
}

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

