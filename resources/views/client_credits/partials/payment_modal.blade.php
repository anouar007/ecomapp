<!-- MODAL: ENREGISTRER UN RÈGLEMENT / OPÉRATION FINANCIÈRE -->
<div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('client-credits.transactions.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            @csrf
            <div class="modal-header py-3 px-4 border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; font-size: 1.1rem;">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 fs-6 text-white" id="newPaymentModalLabel">Enregistrer un Règlement ou Opération</h5>
                        <p class="small text-white-50 mb-0" style="font-size: 0.75rem;">Mise à jour instantanée du solde client avec calcul en direct</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <!-- 1. Select Customer -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1">
                        <i class="fas fa-user text-secondary me-1"></i> Client concerné <span class="text-danger">*</span>
                    </label>
                    <select name="customer_id" id="modal_customer_id" class="form-select rounded-3 py-2 fw-semibold" required onchange="onModalCustomerChange(this)">
                        <option value="">Sélectionnez un client dans la liste...</option>
                        @foreach($allCustomersList ?? [] as $cust)
                            <option value="{{ $cust->id }}" data-balance="{{ (float)$cust->current_balance }}" data-name="{{ $cust->name }}">
                                {{ $cust->name }} ({{ $cust->customer_code ?? 'ID #'.$cust->id }}) — Solde : {{ currency($cust->current_balance) }}
                            </option>
                        @endforeach
                    </select>
                    <div id="modalCustomerBalancePreview" class="d-none mt-2 p-2.5 rounded-3 bg-light border small">
                        <!-- Filled dynamically by JS -->
                    </div>
                </div>

                <!-- 2. Visual Operation Type Cards -->
                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark mb-1.5">
                        <i class="fas fa-sliders text-secondary me-1"></i> Nature de l'opération <span class="text-danger">*</span>
                    </label>
                    <div class="row g-2 mb-1">
                        <div class="col-6 col-sm-3">
                            <button type="button" class="w-100 p-2 rounded-3 border text-start op-type-card active" data-op-type="payment_received" onclick="selectModalOpType('payment_received')" style="background: #f0fdf4; border-color: #22c55e !important; transition: all 0.15s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-arrow-down me-1"></i>Entrée</span>
                                    <i class="fas fa-circle-check text-success op-check-icon"></i>
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.78rem;">Encaissement</div>
                                <div class="text-muted" style="font-size: 0.66rem; line-height: 1.2;">Le client paie sa dette</div>
                            </button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="w-100 p-2 rounded-3 border bg-white text-start op-type-card" data-op-type="credit_given" onclick="selectModalOpType('credit_given')" style="transition: all 0.15s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-plus me-1"></i>Créance</span>
                                    <i class="fas fa-circle-check text-warning d-none op-check-icon"></i>
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.78rem;">Vente à crédit</div>
                                <div class="text-muted" style="font-size: 0.66rem; line-height: 1.2;">Augmente la dette client</div>
                            </button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="w-100 p-2 rounded-3 border bg-white text-start op-type-card" data-op-type="credit_note" onclick="selectModalOpType('credit_note')" style="transition: all 0.15s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-info text-white rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-file-invoice me-1"></i>Avoir</span>
                                    <i class="fas fa-circle-check text-info d-none op-check-icon"></i>
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.78rem;">Avoir accordé</div>
                                <div class="text-muted" style="font-size: 0.66rem; line-height: 1.2;">Retour / geste commercial</div>
                            </button>
                        </div>
                        <div class="col-6 col-sm-3">
                            <button type="button" class="w-100 p-2 rounded-3 border bg-white text-start op-type-card" data-op-type="refund_to_client" onclick="selectModalOpType('refund_to_client')" style="transition: all 0.15s ease;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-arrow-up me-1"></i>Sortie</span>
                                    <i class="fas fa-circle-check text-danger d-none op-check-icon"></i>
                                </div>
                                <div class="fw-bold text-dark" style="font-size: 0.78rem;">Remboursement</div>
                                <div class="text-muted" style="font-size: 0.66rem; line-height: 1.2;">Rendu espèces au client</div>
                            </button>
                        </div>
                    </div>

                    <!-- Fallback / Synced Select for Form Submission -->
                    <select name="type" id="modal_type" class="form-select form-select-sm rounded-3 mt-1.5 text-secondary" style="font-size: 0.78rem;" required onchange="selectModalOpType(this.value)">
                        <option value="payment_received" selected>Règlement reçu du client (Diminue la dette du client)</option>
                        <option value="credit_given">Vente à crédit accordée (Augmente la dette du client)</option>
                        <option value="credit_note">Avoir accordé au client (Augmente l'avoir du client)</option>
                        <option value="refund_to_client">Remboursement versé au client (Diminue l'avoir du client)</option>
                        <option value="adjustment">Ajustement manuel de solde</option>
                    </select>
                </div>

                <!-- 3. Amount + Quick Fill Shortcuts + Live Balance Simulation -->
                <div class="row g-3 mb-3 align-items-start">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-coins text-warning me-1"></i> Montant de l'opération (DH) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" id="modal_amount" class="form-control form-control-lg rounded-start-3 fw-bold text-dark" placeholder="0.00" required oninput="updateModalLiveSimulation()">
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                        <!-- Quick Amount Chips -->
                        <div id="modalQuickAmounts" class="d-flex flex-wrap gap-1.5 mt-2">
                            <button type="button" id="btnFullBalanceChip" class="btn btn-xs btn-outline-success rounded-pill px-2.5 py-0.5 fw-semibold d-none" style="font-size: 0.7rem;" onclick="setModalQuickAmount('full')">
                                <i class="fas fa-check-double me-1"></i>Solder tout
                            </button>
                            <button type="button" id="btnHalfBalanceChip" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5 d-none" style="font-size: 0.7rem;" onclick="setModalQuickAmount('half')">
                                50%
                            </button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 text-secondary" style="font-size: 0.7rem;" onclick="setModalQuickAmount(500)">500 DH</button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 text-secondary" style="font-size: 0.7rem;" onclick="setModalQuickAmount(1000)">1 000 DH</button>
                            <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 text-secondary" style="font-size: 0.7rem;" onclick="setModalQuickAmount(2000)">2 000 DH</button>
                        </div>
                    </div>

                    <!-- Live Balance Simulation Box -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-calculator text-primary me-1"></i> Aperçu du solde après opération
                        </label>
                        <div id="modalLiveSimulation" class="p-2.5 rounded-3 border bg-light" style="min-height: 76px;">
                            <div class="d-flex align-items-center justify-content-between small text-muted mb-1">
                                <span>Solde avant :</span>
                                <span id="simBeforeVal" class="fw-semibold font-monospace">0,00 DH</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-1.5 border-top">
                                <span class="small fw-bold text-dark">Nouveau solde :</span>
                                <span id="simAfterVal" class="fw-bold font-monospace fs-6 text-secondary">0,00 DH</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Payment Method & Store & Reference -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" id="modal_payment_method" class="form-select rounded-3" required>
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
                                    <option value="{{ $st->id }}" {{ $st->is_default ? 'selected' : '' }}>{{ $st->name }} ({{ $st->city }})</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">N° Réf. / N° Chèque</label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="Ex: CHQ-981244, VIR-0021">
                    </div>
                </div>

                <!-- 5. Date & Proof Image Upload -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date & Heure de l'opération</label>
                        <input type="datetime-local" name="payment_date" class="form-control rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-camera text-primary me-1"></i> Preuve photo (Reçu / Chèque / Virement)
                        </label>
                        <input type="file" name="proof_image" accept="image/*" class="form-control rounded-3" id="proof_image_input" onchange="previewSelectedImage(this)">
                    </div>
                </div>

                <!-- Image preview container -->
                <div id="imagePreviewBox" class="d-none mb-3 p-2 rounded-3 bg-light border text-center">
                    <img id="imagePreviewTarget" src="" alt="Aperçu Preuve" style="max-height: 180px; max-width: 100%; border-radius: 8px;">
                </div>

                <!-- 6. Notes / Motifs -->
                <div class="mb-1">
                    <label class="form-label small fw-bold text-dark mb-1">Notes / Motif du règlement</label>
                    <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Précisez le détail (ex: Avance sur commande #1042, solde facture FAC-2026-004)..."></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0 justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-semibold shadow-sm">
                    <i class="fas fa-check-circle me-1.5"></i> Enregistrer & Mettre à jour le solde
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectModalOpType(typeVal) {
    const select = document.getElementById('modal_type');
    if (select && select.value !== typeVal) {
        select.value = typeVal;
    }
    const cards = document.querySelectorAll('.op-type-card');
    cards.forEach(card => {
        const isMatch = card.getAttribute('data-op-type') === typeVal;
        const icon = card.querySelector('.op-check-icon');
        if (isMatch) {
            card.classList.add('active');
            card.style.background = '#f8fafc';
            card.style.borderColor = '#0f172a';
            if (icon) icon.classList.remove('d-none');
        } else {
            card.classList.remove('active');
            card.style.background = '#ffffff';
            card.style.borderColor = '#dee2e6';
            if (icon) icon.classList.add('d-none');
        }
    });
    updateModalLiveSimulation();
}

function setModalQuickAmount(val) {
    const custSelect = document.getElementById('modal_customer_id');
    const amountInput = document.getElementById('modal_amount');
    if (!amountInput) return;

    let currentBal = 0;
    if (custSelect && custSelect.selectedIndex > 0) {
        currentBal = Math.abs(parseFloat(custSelect.options[custSelect.selectedIndex].getAttribute('data-balance') || 0));
    }

    if (val === 'full' && currentBal > 0) {
        amountInput.value = currentBal.toFixed(2);
    } else if (val === 'half' && currentBal > 0) {
        amountInput.value = (currentBal / 2).toFixed(2);
    } else if (typeof val === 'number') {
        amountInput.value = val.toFixed(2);
    }
    updateModalLiveSimulation();
}

function updateModalLiveSimulation() {
    const custSelect = document.getElementById('modal_customer_id');
    const typeSelect = document.getElementById('modal_type');
    const amountInput = document.getElementById('modal_amount');
    const beforeEl = document.getElementById('simBeforeVal');
    const afterEl = document.getElementById('simAfterVal');

    if (!beforeEl || !afterEl) return;

    let balanceBefore = 0;
    if (custSelect && custSelect.selectedIndex > 0) {
        balanceBefore = parseFloat(custSelect.options[custSelect.selectedIndex].getAttribute('data-balance') || 0);
    }

    const amount = parseFloat(amountInput?.value || 0);
    const type = typeSelect?.value || 'payment_received';

    let diff = 0;
    if (type === 'payment_received' || type === 'credit_note') {
        diff = -amount;
    } else if (type === 'credit_given' || type === 'refund_to_client') {
        diff = +amount;
    }

    const balanceAfter = balanceBefore + diff;
    const fmt = (n) => Math.abs(n).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' DH';

    if (balanceBefore > 0.01) {
        beforeEl.innerHTML = `<span class="text-danger">Doit ${fmt(balanceBefore)}</span>`;
    } else if (balanceBefore < -0.01) {
        beforeEl.innerHTML = `<span class="text-info">Avoir ${fmt(balanceBefore)}</span>`;
    } else {
        beforeEl.innerHTML = `<span class="text-success">Soldé (0,00 DH)</span>`;
    }

    if (Math.abs(balanceAfter) <= 0.01) {
        afterEl.innerHTML = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1"><i class="fas fa-check me-1"></i>Compte Soldé (0,00 DH)</span>`;
    } else if (balanceAfter > 0.01) {
        afterEl.innerHTML = `<span class="text-danger">Doit ${fmt(balanceAfter)}</span>`;
    } else {
        afterEl.innerHTML = `<span class="text-info">Avoir ${fmt(balanceAfter)}</span>`;
    }
}

if (typeof onModalCustomerChange !== 'function') {
    window.onModalCustomerChange = function(select) {
        const selected = select.options[select.selectedIndex];
        const preview = document.getElementById('modalCustomerBalancePreview');
        const fullChip = document.getElementById('btnFullBalanceChip');
        const halfChip = document.getElementById('btnHalfBalanceChip');

        if (!selected || !selected.value) {
            if (preview) preview.classList.add('d-none');
            if (fullChip) fullChip.classList.add('d-none');
            if (halfChip) halfChip.classList.add('d-none');
            updateModalLiveSimulation();
            return;
        }

        const balance = parseFloat(selected.getAttribute('data-balance') || 0);
        if (preview) preview.classList.remove('d-none');

        if (balance > 0.01) {
            const fmtBal = balance.toLocaleString('fr-FR', {minimumFractionDigits: 2});
            if (preview) preview.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i>Le client doit actuellement au magasin : ${fmtBal} DH</span>`;
            selectModalOpType('payment_received');
            document.getElementById('modal_amount').value = balance.toFixed(2);
            if (fullChip) {
                fullChip.classList.remove('d-none');
                fullChip.innerHTML = `<i class="fas fa-check-double me-1"></i>Solder tout (${fmtBal} DH)`;
            }
            if (halfChip) halfChip.classList.remove('d-none');
        } else if (balance < -0.01) {
            const absVal = Math.abs(balance);
            const fmtAbs = absVal.toLocaleString('fr-FR', {minimumFractionDigits: 2});
            if (preview) preview.innerHTML = `<span class="text-info fw-bold"><i class="fas fa-arrow-up me-1"></i>Le magasin doit actuellement à ce client (Avoir) : ${fmtAbs} DH</span>`;
            selectModalOpType('refund_to_client');
            document.getElementById('modal_amount').value = absVal.toFixed(2);
            if (fullChip) {
                fullChip.classList.remove('d-none');
                fullChip.innerHTML = `<i class="fas fa-check-double me-1"></i>Rembourser tout (${fmtAbs} DH)`;
            }
            if (halfChip) halfChip.classList.remove('d-none');
        } else {
            if (preview) preview.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check me-1"></i>Le compte du client est actuellement soldé (0 DH).</span>`;
            selectModalOpType('credit_given');
            document.getElementById('modal_amount').value = '';
            if (fullChip) fullChip.classList.add('d-none');
            if (halfChip) halfChip.classList.add('d-none');
        }

        updateModalLiveSimulation();
    };
}

if (typeof previewSelectedImage !== 'function') {
    window.previewSelectedImage = function(input) {
        const box = document.getElementById('imagePreviewBox');
        const target = document.getElementById('imagePreviewTarget');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                target.src = e.target.result;
                box.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else if (box) {
            box.classList.add('d-none');
        }
    };
}
</script>


