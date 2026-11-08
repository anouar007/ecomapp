@extends('layouts.app')

@section('title', 'Grand Livre des Règlements & Preuves de Paiement')

@push('styles')
<style>
/* ==========================================================================
   MOBILE-FIRST CLIENT TRANSACTIONS LEDGER STYLES
   ========================================================================== */
.chips-scroll-credit {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 3px;
}
.chips-scroll-credit::-webkit-scrollbar {
    display: none;
}
.chip-credit {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.45rem 0.95rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
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
.fixed-trx-table {
    table-layout: fixed !important;
    width: 100% !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Executive Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-danger d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; font-size: 1.35rem;">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <a href="{{ route('client-credits.index') }}" class="text-white-50 text-decoration-none small">
                                <i class="fas fa-arrow-left me-1"></i> Crédits & Créances
                            </a>
                            <span class="text-white-50 small">• Journal Complet des Opérations</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Grand Livre des Règlements & Preuves Photos</h1>
                        <p class="text-white-50 small mb-0 mt-1">Historique chronologique et vérifiable de chaque transaction financière avec justificatifs photographiques.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('client-credits.index') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold" style="font-size: 0.85rem;">
                        <i class="fas fa-users-viewfinder me-1.5"></i> Vue Comptes Clients
                    </a>
                    <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newPaymentModal" style="font-size: 0.85rem;">
                        <i class="fas fa-plus-circle me-1.5"></i> Nouveau Règlement
                    </button>
                </div>
            </div>
        </div>
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
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'type']), ['type' => 'refund_to_client'])) }}" 
               class="chip-credit {{ request('type') === 'refund_to_client' ? 'active' : '' }}">
                <i class="fas fa-arrow-up text-danger"></i>
                <span>Remboursements versés</span>
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
            <a href="{{ route('client-credits.transactions', array_merge(request()->except(['page', 'has_proof']), ['has_proof' => 'yes'])) }}" 
               class="chip-credit {{ request('has_proof') === 'yes' ? 'active' : '' }}">
                <i class="fas fa-camera text-primary"></i>
                <span>Avec preuve photo</span>
            </a>
        </div>
    </div>

    <!-- Clean Aligned Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3.5">
            <form method="GET" action="{{ route('client-credits.transactions') }}" class="row g-2.5 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche Réf / Client</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="N° Réf, client, note...">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Client</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">Tous les clients</option>
                        @foreach($allCustomersList as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Type d'opération</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>Règlement Reçu (Encaissé)</option>
                        <option value="refund_to_client" {{ request('type') == 'refund_to_client' ? 'selected' : '' }}>Remboursement Versé</option>
                        <option value="credit_given" {{ request('type') == 'credit_given' ? 'selected' : '' }}>Crédit Accordé (Vente)</option>
                        <option value="credit_note" {{ request('type') == 'credit_note' ? 'selected' : '' }}>Avoir Accordé</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Preuve photo</label>
                    <select name="has_proof" class="form-select form-select-sm">
                        <option value="">Toutes</option>
                        <option value="yes" {{ request('has_proof') == 'yes' ? 'selected' : '' }}>Avec photo uniquement</option>
                        <option value="no" {{ request('has_proof') == 'no' ? 'selected' : '' }}>Sans photo</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold flex-fill">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    @if(request()->anyFilled(['search', 'customer_id', 'type', 'has_proof', 'date_from', 'date_to']))
                        <a href="{{ route('client-credits.transactions') }}" class="btn btn-sm btn-light border rounded-pill px-2.5" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Desktop Table View (Hidden on mobile) -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0 fixed-trx-table">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 15%;">Date & N° Réf</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 20%;">Client</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 17%;">Type d'Opération</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 11%;">Preuve Photo</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 13%;">Montant</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 13%;">Nouveau Solde</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 11%;">Opérateur</th>
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
                            <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none hover-primary text-truncate d-block">
                                {{ $trx->customer->name ?? 'Client inconnu' }}
                            </a>
                            <div class="text-muted small" style="font-size: 0.72rem;">
                                {{ $trx->customer->customer_code ?? '' }} @if($trx->customer->phone)• {{ $trx->customer->phone }}@endif
                            </div>
                        </td>

                        <!-- Type & Payment Method -->
                        <td class="py-3">
                            <span class="badge {{ $trx->type_badge_class }} border rounded-pill px-2.5 py-1 fw-semibold mb-1 d-inline-block" style="font-size: 0.72rem;">
                                {{ $trx->type_label }}
                            </span>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <i class="fas fa-credit-card me-1 opacity-50"></i> {{ $trx->payment_method_label }}
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
                                <div class="text-muted text-truncate" style="max-width: 170px; font-size: 0.68rem; margin-left: auto;" title="{{ $trx->notes }}">
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
                        <td class="pe-3 py-3 text-end small text-muted">
                            <div class="text-truncate" style="max-width: 100px; margin-left: auto;" title="{{ $trx->createdBy->name ?? 'Système' }}">
                                <i class="fas fa-user-circle me-1 opacity-50"></i>{{ $trx->createdBy->name ?? 'Système' }}
                            </div>
                            @if(auth()->user()->hasRole('Admin'))
                            <div class="mt-1">
                                <form method="POST" action="{{ route('client-credits.transactions.destroy', $trx->id) }}" onsubmit="return confirm('Confirmez-vous l\'annulation de cette transaction ? Le solde du client sera réajusté.');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-light text-danger border rounded-pill px-2 py-0.5" title="Annuler cette transaction">
                                        <i class="fas fa-trash" style="font-size: 0.65rem;"></i> Annuler
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
@include('client_credits.partials.payment_modal', ['allCustomersList' => $allCustomersList])

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
