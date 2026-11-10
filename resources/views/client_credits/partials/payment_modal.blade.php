<!-- MODAL: ENREGISTRER UN RÈGLEMENT / OPÉRATION FINANCIÈRE -->
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
                        @foreach($allCustomersList ?? [] as $cust)
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

                <!-- Payment Method & Reference & Store/Cashier -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Caisse / Boutique</label>
                        <select name="store_id" class="form-select rounded-3">
                            <option value="">Siège / Général</option>
                            @isset($stores)
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">N° Réf. / N° Chèque</label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="Ex: CHQ-981244, VIR-0021">
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

