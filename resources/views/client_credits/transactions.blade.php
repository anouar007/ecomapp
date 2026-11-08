@extends('layouts.app')

@section('title', 'Grand Livre des Règlements & Preuves de Paiement')

@push('styles')
<style>
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
    transform: scale(1.1);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('client-credits.index') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Crédits & Créances
                </a>
                <span class="text-muted small">• Journal des Opérations</span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-0">Grand Livre des Règlements & Preuves de Paiement</h1>
            <p class="text-muted small mb-0">Historique complet et auditable de toutes les transactions financières clients avec justificatifs photographiques.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('client-credits.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-users-viewfinder me-1.5"></i> Vue Comptes Clients
            </a>
            <button type="button" class="btn btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                <i class="fas fa-plus-circle me-1.5"></i> Nouveau Règlement
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

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3.5">
            <form method="GET" action="{{ route('client-credits.transactions') }}" class="row g-2.5 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Recherche</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="N° Réf, client, note...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
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
                        <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>Règlement Reçu</option>
                        <option value="refund_to_client" {{ request('type') == 'refund_to_client' ? 'selected' : '' }}>Remboursement Versé</option>
                        <option value="credit_given" {{ request('type') == 'credit_given' ? 'selected' : '' }}>Crédit Accordé</option>
                        <option value="credit_note" {{ request('type') == 'credit_note' ? 'selected' : '' }}>Avoir Accordé</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Preuve photo</label>
                    <select name="has_proof" class="form-select form-select-sm">
                        <option value="">Tous</option>
                        <option value="yes" {{ request('has_proof') == 'yes' ? 'selected' : '' }}>Avec preuve uniquement</option>
                        <option value="no" {{ request('has_proof') == 'no' ? 'selected' : '' }}>Sans preuve</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
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
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Date & Réf</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Client</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Type & Mode</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem;">Preuve Photo</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Montant</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Nouveau Solde</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Opérateur</th>
                        @if(auth()->user()->hasRole('Admin'))
                            <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">Action</th>
                        @endif
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
                            <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
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
                                <span class="text-muted small fst-italic" style="font-size: 0.72rem;">Aucune</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="py-3 text-end">
                            <div class="fw-bold fs-6 {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                            </div>
                            @if($trx->notes)
                                <div class="text-muted text-truncate" style="max-width: 180px; font-size: 0.68rem;" title="{{ $trx->notes }}">
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

                        <!-- Operator -->
                        <td class="py-3 small text-muted">
                            <div><i class="fas fa-user-circle me-1 opacity-50"></i>{{ $trx->createdBy->name ?? 'Système' }}</div>
                        </td>

                        <!-- Actions (Delete for Admin) -->
                        @if(auth()->user()->hasRole('Admin'))
                        <td class="pe-3 py-3 text-end">
                            <form method="POST" action="{{ route('client-credits.transactions.destroy', $trx->id) }}" onsubmit="return confirm('Confirmez-vous l\'annulation de cette transaction ? Le solde du client sera réajusté.');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border rounded-pill px-2 py-1" title="Annuler cette transaction">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
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

        @if($transactions->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Include same proof view modal -->
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

