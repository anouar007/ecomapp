<!-- Invoice Email Modal -->
<div class="modal fade" id="invoiceEmailModal" tabindex="-1" aria-labelledby="invoiceEmailModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);">
            <div class="modal-header py-3 px-4 bg-dark text-white border-0 position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paper-plane text-primary" style="font-size: 15px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="invoiceEmailModalLabel">Envoyer au client par email</h6>
                        <span class="text-white-50 small" id="emailModalDocSub" style="font-size: 0.75rem;">Document #...</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="invoiceEmailForm" onsubmit="sendInvoiceEmail(event)">
                @csrf
                <input type="hidden" name="invoice_id" id="emailModalInvoiceId">

                <div class="modal-body p-4">
                    <!-- Alert container -->
                    <div id="emailModalAlert" class="d-none alert py-2.5 px-3 rounded-3 mb-3 small"></div>

                    <!-- Document Type Selector (Facture vs Devis) -->
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.05em;">Type de document à joindre</label>
                        <div class="d-flex gap-2">
                            <label class="btn btn-outline-primary flex-fill py-2 rounded-3 text-start position-relative d-flex align-items-center gap-2" for="emailAsFacture" style="cursor: pointer;">
                                <input type="radio" class="form-check-input mt-0" name="as" id="emailAsFacture" value="facture" checked onchange="updateEmailModalSubject()">
                                <div>
                                    <div class="fw-bold" style="font-size: 0.85rem;">Facture</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Document officiel de vente</div>
                                </div>
                            </label>

                            <label class="btn btn-outline-warning flex-fill py-2 rounded-3 text-start position-relative d-flex align-items-center gap-2" for="emailAsDevis" style="cursor: pointer;">
                                <input type="radio" class="form-check-input mt-0" name="as" id="emailAsDevis" value="devis" onchange="updateEmailModalSubject()">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">Devis Proforma</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Proposition commerciale</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Official Stamp Toggle -->
                    <div class="form-check form-switch mb-3 p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between ps-3">
                        <label class="form-check-label small fw-semibold text-dark mb-0" for="emailWithStamp" style="cursor: pointer;">
                            <i class="fas fa-stamp text-danger me-1.5"></i> Inclure le Cachet & Signature WINA SHOP
                        </label>
                        <input class="form-check-input ms-0" type="checkbox" role="switch" name="with_stamp" id="emailWithStamp" value="1" checked>
                    </div>

                    <!-- Client Email -->
                    <div class="mb-3">
                        <label for="emailModalRecipient" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            Email du Client <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0" id="emailModalRecipient" name="recipient_email" required placeholder="client@exemple.com">
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Le PDF officiel sera généré et envoyé en pièce jointe à cette adresse.</div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-3">
                        <label for="emailModalSubject" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Objet de l'email</label>
                        <input type="text" class="form-control" id="emailModalSubject" name="subject" placeholder="Objet de l'email...">
                    </div>

                    <!-- Custom Message -->
                    <div class="mb-3">
                        <label for="emailModalMessage" class="form-label text-muted small fw-bold text-uppercase mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Message personnalisé (Optionnel)</label>
                        <textarea class="form-control" id="emailModalMessage" name="custom_message" rows="3" placeholder="Ajoutez un mot d'accompagnement ou des consignes pour le client..."></textarea>
                    </div>

                    <!-- Copy to Store Checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_copy" id="emailModalSendCopy" value="1" checked>
                        <label class="form-check-label small text-muted" for="emailModalSendCopy">
                            M'envoyer une copie conforme (CC) sur {{ setting('company_email', 'contact@winashop.com') }}
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 small fw-semibold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-1.5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" id="emailModalSubmitBtn" style="background: #0f172a; border-color: #0f172a;">
                        <span class="spinner-border spinner-border-sm d-none" id="emailModalSpinner" role="status"></span>
                        <i class="fas fa-paper-plane" id="emailModalIcon"></i>
                        <span id="emailModalBtnText">Envoyer au client</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentEmailInvoiceData = {
        id: null,
        number: '',
        customerName: '',
        customerEmail: '',
        type: 'facture',
        withStamp: true,
        total: ''
    };

    function openInvoiceEmailModal(id, number, customerName, customerEmail, type = 'facture', withStamp = true, total = '') {
        currentEmailInvoiceData = {
            id: id,
            number: number || '',
            customerName: customerName || '',
            customerEmail: customerEmail || '',
            type: (type || 'facture').toLowerCase(),
            withStamp: Boolean(withStamp),
            total: total || ''
        };

        document.getElementById('emailModalInvoiceId').value = id;
        document.getElementById('emailModalDocSub').textContent = (currentEmailInvoiceData.type === 'devis' ? 'Devis #' : 'Facture #') + number + (customerName ? ' — ' + customerName : '');
        
        // Pre-fill recipient email
        const recipientInput = document.getElementById('emailModalRecipient');
        if (recipientInput) {
            recipientInput.value = customerEmail || '';
        }
        
        // Set document type radio
        const devisRadio = document.getElementById('emailAsDevis');
        const factureRadio = document.getElementById('emailAsFacture');
        if (currentEmailInvoiceData.type === 'devis' && devisRadio) {
            devisRadio.checked = true;
        } else if (factureRadio) {
            factureRadio.checked = true;
        }
        
        // Set stamp switch
        const stampSwitch = document.getElementById('emailWithStamp');
        if (stampSwitch) {
            stampSwitch.checked = currentEmailInvoiceData.withStamp;
        }
        
        // Set default subject
        updateEmailModalSubject();
        
        // Reset message & alert
        const msgInput = document.getElementById('emailModalMessage');
        if (msgInput) msgInput.value = '';
        
        const alertBox = document.getElementById('emailModalAlert');
        if (alertBox) {
            alertBox.classList.add('d-none');
            alertBox.className = 'd-none alert py-2.5 px-3 rounded-3 mb-3 small';
        }
        
        const modalEl = document.getElementById('invoiceEmailModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function updateEmailModalSubject() {
        const isDevis = document.getElementById('emailAsDevis')?.checked;
        const docTitle = isDevis ? 'Devis' : 'Facture';
        const num = currentEmailInvoiceData.number || '';
        const appName = '{{ setting("app_name", "WINA SHOP") }}';
        const subjectInput = document.getElementById('emailModalSubject');
        if (subjectInput) {
            subjectInput.value = `[${appName}] Votre ${docTitle} #${num}`;
        }
    }

    function sendInvoiceEmail(e) {
        if (e) e.preventDefault();
        const id = document.getElementById('emailModalInvoiceId')?.value;
        if (!id) return;

        const form = document.getElementById('invoiceEmailForm');
        const submitBtn = document.getElementById('emailModalSubmitBtn');
        const spinner = document.getElementById('emailModalSpinner');
        const icon = document.getElementById('emailModalIcon');
        const btnText = document.getElementById('emailModalBtnText');
        const alertBox = document.getElementById('emailModalAlert');

        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (icon) icon.classList.add('d-none');
        if (btnText) btnText.textContent = 'Envoi en cours...';
        if (alertBox) alertBox.classList.add('d-none');

        const formData = new FormData(form);

        fetch(`/invoices/${id}/email`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(r => r.json().then(data => ({ status: r.status, data: data })))
        .then(res => {
            if (submitBtn) submitBtn.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (icon) icon.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Envoyer au client';

            if (res.status === 200 && res.data.success) {
                if (alertBox) {
                    alertBox.className = 'alert alert-success py-2.5 px-3 rounded-3 mb-3 small';
                    alertBox.innerHTML = '<i class="fas fa-check-circle me-1.5"></i> ' + (res.data.message || 'Email envoyé avec succès !');
                    alertBox.classList.remove('d-none');
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Envoyé !',
                        text: res.data.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }

                setTimeout(() => {
                    const modalEl = document.getElementById('invoiceEmailModal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                    }
                }, 1400);
            } else {
                if (alertBox) {
                    alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 mb-3 small';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-1.5"></i> ' + (res.data.message || 'Une erreur est survenue lors de l\'envoi.');
                    alertBox.classList.remove('d-none');
                }
            }
        })
        .catch(err => {
            if (submitBtn) submitBtn.disabled = false;
            if (spinner) spinner.classList.add('d-none');
            if (icon) icon.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Envoyer au client';

            if (alertBox) {
                alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 mb-3 small';
                alertBox.innerHTML = '<i class="fas fa-exclamation-triangle me-1.5"></i> ' + (err.message || 'Erreur réseau lors de l\'envoi.');
                alertBox.classList.remove('d-none');
            }
        });
    }
</script>
