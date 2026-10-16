@php
    $storeEmail = setting('company_email') ?: (config('mail.from.address') ?: 'contact@winashop.com');
@endphp

<!-- Invoice Email Modal (Moroccan Pro SaaS Design) -->
<div class="modal fade" id="invoiceEmailModal" tabindex="-1" aria-labelledby="invoiceEmailModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content invoice-email-modal-card">
            
            {{-- Executive Modal Header --}}
            <div class="modal-header email-modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="email-modal-icon-badge">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h5 class="modal-title email-modal-title" id="invoiceEmailModalLabel">Envoyer au client par email</h5>
                        <div class="email-modal-subtitle-box">
                            <span class="email-modal-doc-pill" id="emailModalDocSub">Document #...</span>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-email-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="invoiceEmailForm" onsubmit="sendInvoiceEmail(event)">
                @csrf
                <input type="hidden" name="invoice_id" id="emailModalInvoiceId">

                <div class="modal-body email-modal-body">
                    <!-- Alert container -->
                    <div id="emailModalAlert" class="d-none alert py-2.5 px-3 rounded-3 mb-3 small"></div>

                    <!-- ── 1. Document Type Selector (Interactive Segmented Cards) ── -->
                    <div class="mb-3">
                        <label class="email-modal-field-label">
                            <i class="fas fa-file-lines me-1 opacity-75"></i> Type de document à joindre
                        </label>
                        <div class="row g-2">
                            <!-- Facture Option -->
                            <div class="col-6">
                                <label class="doc-type-choice-card" for="emailAsFacture" id="cardAsFacture">
                                    <input type="radio" name="as" id="emailAsFacture" value="facture" checked onchange="onEmailDocTypeChange()">
                                    <div class="doc-type-choice-indicator">
                                        <div class="indicator-dot"></div>
                                    </div>
                                    <div class="doc-type-choice-content">
                                        <div class="doc-type-choice-title">
                                            <i class="fas fa-file-invoice me-1 text-slate-800"></i> Facture
                                        </div>
                                        <div class="doc-type-choice-desc">Vente officielle (comptable)</div>
                                    </div>
                                </label>
                            </div>

                            <!-- Devis Proforma Option -->
                            <div class="col-6">
                                <label class="doc-type-choice-card" for="emailAsDevis" id="cardAsDevis">
                                    <input type="radio" name="as" id="emailAsDevis" value="devis" onchange="onEmailDocTypeChange()">
                                    <div class="doc-type-choice-indicator">
                                        <div class="indicator-dot"></div>
                                    </div>
                                    <div class="doc-type-choice-content">
                                        <div class="doc-type-choice-title">
                                            <i class="fas fa-file-signature me-1 text-danger"></i> Devis Proforma
                                        </div>
                                        <div class="doc-type-choice-desc">Proposition commerciale</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- ── 2. Official Moroccan Stamp Switch ── -->
                    <div class="stamp-toggle-card mb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="stamp-toggle-icon">
                                <i class="fas fa-stamp"></i>
                            </div>
                            <div>
                                <label class="stamp-toggle-label mb-0" for="emailWithStamp" style="cursor: pointer;">
                                    Apposer le Cachet & Signature WINA SHOP
                                </label>
                                <div class="stamp-toggle-sub">Document certifié prêt pour signature ou archivage</div>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                            <input class="form-check-input pro-switch ms-0" type="checkbox" role="switch" name="with_stamp" id="emailWithStamp" value="1" checked>
                        </div>
                    </div>

                    <!-- ── 3. Client Recipient Email ── -->
                    <div class="mb-3">
                        <label for="emailModalRecipient" class="email-modal-field-label">
                            Email du Client <span class="text-danger">*</span>
                        </label>
                        <div class="input-group email-input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="emailModalRecipient" name="recipient_email" required placeholder="nom.client@exemple.com">
                        </div>
                        <div class="email-modal-help-text">
                            <i class="fas fa-info-circle me-1"></i> Le PDF officiel certifié A4 sera généré et joint directement à cet email.
                        </div>
                    </div>

                    <!-- ── 4. Subject ── -->
                    <div class="mb-3">
                        <label for="emailModalSubject" class="email-modal-field-label">Objet de l'email</label>
                        <input type="text" class="form-control email-form-input" id="emailModalSubject" name="subject" placeholder="Objet de l'email...">
                    </div>

                    <!-- ── 5. Custom Note / Message ── -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="emailModalMessage" class="email-modal-field-label mb-0">Message d'accompagnement</label>
                            <span class="text-muted" style="font-size: 11px;">Optionnel</span>
                        </div>
                        <textarea class="form-control email-form-input" id="emailModalMessage" name="custom_message" rows="3" placeholder="Bonjour, veuillez trouver ci-joint votre document..."></textarea>
                    </div>

                    <!-- ── 6. CC Copy Option ── -->
                    <div class="form-check email-cc-check d-flex align-items-center gap-2">
                        <input class="form-check-input pro-checkbox mt-0" type="checkbox" name="send_copy" id="emailModalSendCopy" value="1" checked>
                        <label class="form-check-label email-cc-label" for="emailModalSendCopy" style="cursor: pointer;">
                            <span>M'envoyer une copie conforme (CC)</span>
                            @if(!empty($storeEmail))
                                <span class="email-cc-pill">{{ $storeEmail }}</span>
                            @endif
                        </label>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer email-modal-footer">
                    <button type="button" class="btn-email-modal-cancel" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn-email-modal-submit" id="emailModalSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="emailModalSpinner" role="status"></span>
                        <i class="fas fa-paper-plane" id="emailModalIcon"></i>
                        <span id="emailModalBtnText">Envoyer au client</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ═══════════════════════════════════════════════════════
       WINA SHOP — HIGH-END MOROCCAN EMAIL DISPATCH MODAL
       Palette: Slate #0f172a, Ruby Red #dc2626, Emerald #059669
       ═══════════════════════════════════════════════════════ */

    .invoice-email-modal-card {
        border-radius: 14px !important;
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.35), 0 0 1px rgba(15, 23, 42, 0.1) !important;
        background: #ffffff;
    }

    /* Header */
    .email-modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        padding: 18px 24px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        position: relative;
    }

    .email-modal-icon-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.18) 0%, rgba(220, 38, 38, 0.3) 100%);
        border: 1px solid rgba(220, 38, 38, 0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        font-size: 15px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
    }

    .email-modal-title {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        margin-bottom: 3px !important;
        letter-spacing: -0.2px;
    }

    .email-modal-subtitle-box {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .email-modal-doc-pill {
        font-size: 11.5px;
        font-weight: 500;
        color: #cbd5e1;
        background: rgba(255, 255, 255, 0.1);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        max-width: 320px;
        display: inline-block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .btn-email-modal-close {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.15s ease;
        padding: 0;
    }

    .btn-email-modal-close:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        transform: scale(1.04);
    }

    /* Modal Body */
    .email-modal-body {
        padding: 22px 24px !important;
        background: #ffffff;
    }

    .email-modal-field-label {
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        color: #475569 !important;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 6px;
    }

    /* ── Document Type Choice Cards ── */
    .doc-type-choice-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        height: 100%;
        user-select: none;
    }

    .doc-type-choice-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .doc-type-choice-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-1px);
    }

    .doc-type-choice-indicator {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
        background: #ffffff;
    }

    .indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: transparent;
        transition: all 0.15s ease;
    }

    .doc-type-choice-content {
        line-height: 1.25;
        overflow: hidden;
    }

    .doc-type-choice-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .doc-type-choice-desc {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Active states for choice cards */
    .doc-type-choice-card.is-active-facture {
        border-color: #0f172a !important;
        background: #f8fafc !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }

    .doc-type-choice-card.is-active-facture .doc-type-choice-indicator {
        border-color: #0f172a;
    }

    .doc-type-choice-card.is-active-facture .indicator-dot {
        background: #0f172a;
    }

    .doc-type-choice-card.is-active-devis {
        border-color: #dc2626 !important;
        background: #fff8f8 !important;
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1);
    }

    .doc-type-choice-card.is-active-devis .doc-type-choice-indicator {
        border-color: #dc2626;
    }

    .doc-type-choice-card.is-active-devis .indicator-dot {
        background: #dc2626;
    }

    /* ── Stamp Switch Card ── */
    .stamp-toggle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        transition: background 0.15s ease;
    }

    .stamp-toggle-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fef2f2;
        border: 1px solid #fee2e2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .stamp-toggle-label {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
    }

    .stamp-toggle-sub {
        font-size: 11px;
        color: #64748b;
        margin-top: 1px;
    }

    /* Custom Switch & Inputs */
    .pro-switch {
        width: 38px !important;
        height: 21px !important;
        cursor: pointer;
        border-color: #cbd5e1 !important;
        transition: all 0.2s ease !important;
    }

    .pro-switch:checked {
        background-color: #059669 !important;
        border-color: #059669 !important;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25) !important;
    }

    .email-input-group .input-group-text {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        border-right: none !important;
        color: #64748b !important;
        border-radius: 8px 0 0 8px !important;
        padding: 9px 13px !important;
        font-size: 13px;
    }

    .email-input-group .form-control {
        border: 1px solid #cbd5e1 !important;
        border-left: none !important;
        border-radius: 0 8px 8px 0 !important;
        padding: 9px 12px !important;
        font-size: 13.5px !important;
        color: #0f172a !important;
        background: #ffffff !important;
    }

    .email-input-group .form-control:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
    }

    .email-modal-help-text {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 5px;
    }

    .email-form-input {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 9px 12px !important;
        font-size: 13.5px !important;
        color: #0f172a !important;
        background: #ffffff !important;
        transition: all 0.15s ease;
    }

    .email-form-input:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        background: #ffffff !important;
    }

    /* CC Checkbox */
    .pro-checkbox {
        width: 17px !important;
        height: 17px !important;
        border-radius: 5px !important;
        border: 1.5px solid #cbd5e1 !important;
        cursor: pointer;
    }

    .pro-checkbox:checked {
        background-color: #0f172a !important;
        border-color: #0f172a !important;
    }

    .email-cc-label {
        font-size: 12.5px;
        color: #334155;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .email-cc-pill {
        font-size: 11px;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 1px 7px;
        border-radius: 6px;
    }

    /* Footer */
    .email-modal-footer {
        padding: 14px 24px !important;
        background: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-email-modal-cancel {
        height: 38px;
        padding: 0 18px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-email-modal-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #94a3b8;
    }

    .btn-email-modal-submit {
        height: 38px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        border: 1px solid #0f172a;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
        cursor: pointer;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        line-height: 1;
    }

    .btn-email-modal-submit:hover:not(:disabled) {
        background: linear-gradient(180deg, #334155 0%, #1e293b 100%);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.3);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-email-modal-submit:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2);
    }

    .btn-email-modal-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

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

    function onEmailDocTypeChange() {
        const isDevis = document.getElementById('emailAsDevis')?.checked;
        const cardFacture = document.getElementById('cardAsFacture');
        const cardDevis = document.getElementById('cardAsDevis');

        if (isDevis) {
            cardFacture?.classList.remove('is-active-facture');
            cardDevis?.classList.add('is-active-devis');
        } else {
            cardDevis?.classList.remove('is-active-devis');
            cardFacture?.classList.add('is-active-facture');
        }

        updateEmailModalSubject();
    }

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

        const invoiceIdInput = document.getElementById('emailModalInvoiceId');
        if (invoiceIdInput) invoiceIdInput.value = id;

        const subPill = document.getElementById('emailModalDocSub');
        if (subPill) {
            const prefix = currentEmailInvoiceData.type === 'devis' ? 'Devis #' : 'Facture #';
            subPill.textContent = prefix + number + (customerName ? ' — ' + customerName : '');
        }
        
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

        // Update active card styling
        onEmailDocTypeChange();
        
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
                    alertBox.className = 'alert alert-success py-2.5 px-3 rounded-3 mb-3 small d-flex align-items-center gap-2';
                    alertBox.innerHTML = '<i class="fas fa-check-circle text-success" style="font-size: 15px;"></i> <span>' + (res.data.message || 'Email envoyé avec succès !') + '</span>';
                    alertBox.classList.remove('d-none');
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Envoyé avec succès !',
                        text: res.data.message,
                        timer: 2600,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-4 shadow-lg'
                        }
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
                    alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 mb-3 small d-flex align-items-center gap-2';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-triangle text-danger" style="font-size: 15px;"></i> <span>' + (res.data.message || 'Une erreur est survenue lors de l\'envoi.') + '</span>';
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
                alertBox.className = 'alert alert-danger py-2.5 px-3 rounded-3 mb-3 small d-flex align-items-center gap-2';
                alertBox.innerHTML = '<i class="fas fa-exclamation-triangle text-danger" style="font-size: 15px;"></i> <span>' + (err.message || 'Erreur réseau lors de l\'envoi.') + '</span>';
                alertBox.classList.remove('d-none');
            }
        });
    }
</script>
