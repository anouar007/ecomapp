<div class="mobile-order-card mb-3" id="mobile-invoice-card-{{ $invoice->id }}">
    <!-- Top Row: Number, Type, Date and Amount -->
    <div class="mobile-order-header">
        <div class="min-w-0 flex-grow-1">
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                @if($invoice->isQuote())
                    <span class="badge rounded-pill bg-warning-subtle text-warning fw-bold px-2 py-0.5" style="font-size: 0.68rem; letter-spacing: 0.02em;">
                        DEVIS
                    </span>
                @else
                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-bold px-2 py-0.5" style="font-size: 0.68rem; letter-spacing: 0.02em;">
                        FACTURE
                    </span>
                @endif
                <a href="{{ route('invoices.show', $invoice) }}" class="mobile-order-num text-decoration-none" title="Consulter le document">
                    #{{ $invoice->display_number ?? $invoice->invoice_number }}
                </a>
                <button type="button" 
                        class="mobile-copy-btn" 
                        onclick="copyInvoiceNumber('{{ $invoice->display_number ?? $invoice->invoice_number }}', this)" 
                        title="Copier le N°">
                    <i class="far fa-copy"></i>
                </button>
            </div>
            <div class="mobile-order-date">
                <i class="far fa-clock me-1 opacity-75"></i>{{ $invoice->issued_at->format('d/m/Y') }} à {{ $invoice->issued_at->format('H:i') }}
            </div>
        </div>

        <div class="mobile-order-price-wrap text-end flex-shrink-0">
            <div class="mobile-order-price">
                {{ $invoice->formatted_total_amount }}
            </div>

            @php
                $pStatus = strtolower($invoice->payment_status);
                $isPaid = $pStatus === 'paid';
                $payPillClass = match($pStatus) {
                    'paid' => 'paid',
                    'partial' => 'pending',
                    'cancelled' => 'failed',
                    default => 'pending'
                };
                $payPillLabel = match($pStatus) {
                    'paid' => 'Payé',
                    'partial' => 'Partiel',
                    'cancelled' => 'Annulé',
                    default => 'Impayé'
                };
                $payPillIcon = match($pStatus) {
                    'paid' => 'fa-check-circle',
                    'partial' => 'fa-adjust',
                    'cancelled' => 'fa-times-circle',
                    default => 'fa-clock'
                };
            @endphp

            <!-- Interactive Quick Payment Switcher -->
            <div class="dropdown">
                <button type="button" 
                        class="mobile-order-pay-pill {{ $payPillClass }} border-0 dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport"
                        aria-expanded="false"
                        id="mob-pay-pill-{{ $invoice->id }}"
                        title="Changer le statut de paiement">
                    <i class="fas {{ $payPillIcon }}" id="mob-pay-icon-{{ $invoice->id }}"></i>
                    <span id="mob-pay-label-{{ $invoice->id }}">{{ $payPillLabel }}</span>
                    <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.55rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" style="background: #ffffff !important;" aria-labelledby="mob-pay-pill-{{ $invoice->id }}">
                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de paiement</li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'paid' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'paid')">
                            <span><i class="fas fa-check-circle text-success me-1.5"></i> Payé</span>
                            @if($pStatus === 'paid') <i class="fas fa-check text-success small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'unpaid' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'unpaid')">
                            <span><i class="fas fa-clock text-warning me-1.5"></i> Impayé</span>
                            @if($pStatus === 'unpaid') <i class="fas fa-check text-warning small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'partial' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'partial')">
                            <span><i class="fas fa-adjust text-info me-1.5"></i> Partiel</span>
                            @if($pStatus === 'partial') <i class="fas fa-check text-info small"></i> @endif
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $pStatus === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'cancelled')">
                            <span><i class="fas fa-times-circle text-danger me-1.5"></i> Annulé</span>
                            @if($pStatus === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Customer Information Row -->
    <div class="mobile-order-customer">
        <div class="mobile-order-cust-avatar" style="background: {{ $invoice->isQuote() ? '#fef3c7; color: #b45309' : '#e0e7ff; color: #3730a3' }};">
            {{ strtoupper(substr($invoice->customer_name ?: 'C', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-grow-1">
            <div class="mobile-order-cust-name text-truncate">
                {{ $invoice->customer_name ?: 'Client sans nom' }}
            </div>
            <div class="mobile-order-cust-sub text-truncate d-flex align-items-center gap-2">
                @if($invoice->customer_email)
                    <span><i class="far fa-envelope me-1 opacity-75"></i>{{ $invoice->customer_email }}</span>
                @elseif($invoice->customer_phone)
                    <span><i class="fas fa-phone me-1 opacity-75"></i>{{ $invoice->customer_phone }}</span>
                @else
                    <span class="text-muted opacity-75">Aucun contact</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tags Row: Stamp, Payment Method, Items count -->
    <div class="mobile-order-tags">
        <!-- Stamp Badge -->
        @if($invoice->with_stamp)
            <span class="badge rounded-pill bg-indigo-subtle border border-indigo-subtle d-inline-flex align-items-center gap-1 px-2.5 py-1" style="background: #eef2ff; color: #4338ca; border-color: #c7d2fe !important; font-size: 0.72rem; font-weight: 600;">
                <i class="fas fa-stamp" style="font-size: 0.68rem;"></i> Avec Cachet
            </span>
        @else
            <span class="badge rounded-pill bg-light text-secondary border px-2.5 py-1" style="font-size: 0.72rem; font-weight: 500;">
                Sans Cachet
            </span>
        @endif

        <!-- Payment Method -->
        @if($invoice->payment_method)
            <span class="mobile-order-badge" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                <i class="fas fa-wallet text-muted me-1"></i>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}
            </span>
        @endif

        <!-- Items Count -->
        <span class="mobile-order-items-pill">
            <i class="fas fa-box text-muted me-1"></i>{{ $invoice->items->count() }} article{{ $invoice->items->count() > 1 ? 's' : '' }}
        </span>

        @if($invoice->due_date)
            <span class="text-muted small ms-auto d-inline-flex align-items-center" style="font-size: 0.72rem;">
                <i class="far fa-calendar-alt text-muted me-1"></i>Échéance : {{ $invoice->due_date->format('d/m/Y') }}
            </span>
        @endif
    </div>

    <!-- Mobile Actions Toolbar -->
    <div class="mobile-order-toolbar">
        <div>
            <!-- Aperçu Rapide Button -->
            <button type="button" 
                    class="mobile-order-doc-btn border-0" 
                    onclick="openInvoicePreview('{{ $invoice->id }}', '{{ $invoice->display_number ?? $invoice->invoice_number }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }})"
                    title="Aperçu rapide">
                <i class="fas fa-eye text-primary"></i>
                <span>Aperçu</span>
            </button>
        </div>

        <div class="mobile-action-btns">
            <!-- Voir -->
            <a href="{{ route('invoices.show', $invoice) }}" class="mobile-act-btn mobile-act-view" title="Consulter la fiche">
                <i class="fas fa-external-link-alt"></i>
            </a>

            <!-- Dropdown PDF / Imprimer (Dropup for clean bottom sheet behavior) -->
            <div class="dropdown dropup d-inline-block">
                <button type="button" 
                        class="mobile-act-btn mobile-act-edit dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport"
                        aria-expanded="false" 
                        title="Options PDF & Impression">
                    <i class="fas fa-file-pdf"></i>
                    <span>PDF</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" style="min-width: 210px; background: #ffffff !important;">
                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem;">
                        <i class="fas fa-file-invoice text-primary me-1"></i> Facture
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2 py-1.5 px-2" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                            <i class="fas fa-stamp text-indigo me-1.5" style="color: #6366f1;"></i> Facture avec Cachet
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2 py-1.5 px-2" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                            <i class="far fa-file-pdf text-muted me-1.5"></i> Facture standard
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem;">
                        <i class="fas fa-file-signature text-warning me-1"></i> Devis
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2 py-1.5 px-2" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                            <i class="fas fa-stamp text-warning me-1.5"></i> Devis avec Cachet
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-2 py-1.5 px-2" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                            <i class="far fa-file-pdf text-muted me-1.5"></i> Devis standard
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Modifier -->
            <a href="{{ route('invoices.edit', $invoice) }}" class="mobile-act-btn mobile-act-edit" title="Modifier la facture">
                <i class="fas fa-pencil-alt"></i>
            </a>

            <!-- Supprimer -->
            <form method="POST" 
                  action="{{ route('invoices.destroy', $invoice) }}" 
                  class="m-0 p-0 d-inline"
                  data-confirm-delete="true"
                  data-item-type="document"
                  data-item-name="{{ ($invoice->isQuote() ? 'Devis #' : 'Facture #') . ($invoice->display_number ?? $invoice->invoice_number) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="mobile-act-btn mobile-act-delete" title="Supprimer">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
