@extends('layouts.app')

@section('title', 'Invoices Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                Invoices Management
            </h1>
            <p class="brand-subtitle">Track billings, manage customer payments, and monitor revenue</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Create Invoice
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="brand-stat-label">Total Invoices</div>
            <div class="brand-stat-value">{{ $stats['total_invoices'] }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-history"></i> Lifetime generated
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="brand-stat-label">Paid Amount</div>
            <div class="brand-stat-value">{{ currency($stats['paid_amount']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-arrow-up text-success"></i> Successfully collected
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-hourglass-start"></i>
            </div>
            <div class="brand-stat-label">Unpaid Amount</div>
            <div class="brand-stat-value">{{ currency($stats['unpaid_amount']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-exclamation-circle text-warning"></i> Pending collections
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="brand-stat-label">Total Revenue</div>
            <div class="brand-stat-value">{{ currency($stats['total_revenue']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> Combined gross value
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('invoices.index') }}" class="d-flex align-items-end gap-3 flex-wrap">
            <div class="brand-search-wrapper flex-grow-1">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Invoice #, customer name...">
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="invoice" {{ request('type') === 'invoice' ? 'selected' : '' }}>Invoice</option>
                    <option value="quote" {{ request('type') === 'quote' ? 'selected' : '' }}>Quote</option>
                </select>
            </div>
            
            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">Stamp</label>
                <select name="with_stamp" class="form-select">
                    <option value="">All Stamps</option>
                    <option value="1" {{ request('with_stamp') === '1' ? 'selected' : '' }}>With Stamp</option>
                    <option value="0" {{ request('with_stamp') === '0' ? 'selected' : '' }}>Without Stamp</option>
                </select>
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">From Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>

            <div style="min-width: 140px;">
                <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">To Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-brand-primary">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('invoices.index') }}" class="btn-brand-light" title="Reset">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="brand-table-card" style="overflow: visible;">
        <div class="table-responsive" style="overflow: visible; min-height: 260px;">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Invoice #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Stamp</th>
                        <th>Method</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-2">
                                {{ $invoice->invoice_number }}
                                @if($invoice->isQuote())
                                    <span style="background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 800; text-transform: uppercase;">{{ __('Quote') }}</span>
                                @endif
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $invoice->customer_name }}</div>
                            @if($invoice->customer_email)
                                <div class="text-muted small">{{ $invoice->customer_email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-dark">{{ $invoice->issued_at->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $invoice->issued_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            {{ $invoice->formatted_total_amount }}
                        </td>
                        <td class="text-center">
                            @php
                                $statusClasses = [
                                    'paid' => 'success',
                                    'unpaid' => 'warning',
                                    'partial' => 'info',
                                    'cancelled' => 'danger',
                                ];
                                $badgeType = $statusClasses[$invoice->payment_status] ?? 'primary';
                            @endphp
                            <span class="brand-badge {{ $badgeType }}">
                                {{ $invoice->status_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($invoice->with_stamp)
                                <span style="background: #e0e7ff; color: #4338ca; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas fa-stamp" style="font-size: 9px;"></i> Yes
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <span class="text-uppercase" style="letter-spacing: 0.02em;">{{ str_replace('_', ' ', $invoice->payment_method) }}</span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-1">
                                <button type="button" class="btn-action-icon text-primary" title="Aperçu Rapide" 
                                        onclick="openInvoicePreview('{{ $invoice->id }}', '{{ $invoice->display_number ?? $invoice->invoice_number }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn-action-icon" title="Détails de la facture">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>

                                <!-- PDF Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn-action-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Télécharger PDF (Facture / Devis)">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="font-size: 12px; border-radius: 12px; min-width: 210px; padding: 6px; z-index: 99999;">
                                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                            <i class="fas fa-file-invoice text-primary me-1"></i> Facture
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #6366f1; width: 14px;"></i> Facture avec Cachet
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> Facture sans Cachet
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                            <i class="fas fa-file-signature text-warning me-1"></i> Devis
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #f59e0b; width: 14px;"></i> Devis avec Cachet
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> Devis sans Cachet
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Print Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn-action-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Imprimer (Facture / Devis)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="font-size: 12px; border-radius: 12px; min-width: 210px; padding: 6px; z-index: 99999;">
                                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                            <i class="fas fa-file-invoice text-primary me-1"></i> Facture
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #6366f1; width: 14px;"></i> Imprimer Facture (Cachet)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                                <i class="fas fa-print text-muted" style="width: 14px;"></i> Imprimer Facture (Standard)
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                            <i class="fas fa-file-signature text-warning me-1"></i> Devis
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #f59e0b; width: 14px;"></i> Imprimer Devis (Cachet)
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                                <i class="fas fa-print text-muted" style="width: 14px;"></i> Imprimer Devis (Standard)
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Guarantee Dropdown -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn-action-icon" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="{{ __('Guarantee Options') }}" style="color: #059669;">
                                        <i class="fas fa-shield-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="font-size: 13px; border-radius: 10px; z-index: 99999;">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.guarantee', $invoice) }}">
                                                <i class="fas fa-eye" style="color: #10b981; width: 14px;"></i> {{ __('View Guarantee') }}
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp" style="color: #10b981; width: 14px;"></i> {{ __('PDF With Stamp') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> {{ __('PDF No Stamp') }}
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 1]) }}">
                                                <i class="fas fa-print" style="color: #10b981; width: 14px;"></i> {{ __('Print With Stamp') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 0]) }}">
                                                <i class="fas fa-print text-muted" style="width: 14px;"></i> {{ __('Print No Stamp') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h5 class="fw-bold text-dark">No invoices found</h5>
                                <p class="text-muted">You haven't generated any invoices matching your search.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>

    <!-- Quick Preview Modal -->
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);">
                <div class="modal-header py-2 px-3 bg-dark text-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold" id="previewModalTitle" style="font-size: 14px;">Aperçu du document</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-sm btn-outline-light" id="previewBtnFacture" onclick="setPreviewDocType('facture')">Facture</button>
                            <button type="button" class="btn btn-sm btn-outline-light" id="previewBtnDevis" onclick="setPreviewDocType('devis')">Devis</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-light" id="previewBtnStamp" onclick="togglePreviewStamp()">
                            <i class="fas fa-stamp me-1"></i> <span id="previewStampLabel">Avec Cachet</span>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light" onclick="printFromPreview()"><i class="fas fa-print me-1"></i> Imprimer</button>
                        <a href="#" id="previewDownloadLink" class="btn btn-sm btn-danger"><i class="fas fa-download me-1"></i> PDF</a>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0" style="background: #cbd5e1; height: 75vh;">
                    <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPreviewId = null;
        let currentPreviewType = 'facture';
        let currentPreviewStamp = true;

        function openInvoicePreview(id, number, type, withStamp) {
            currentPreviewId = id;
            currentPreviewType = type || 'facture';
            currentPreviewStamp = withStamp !== false;
            document.getElementById('previewModalTitle').innerText = (currentPreviewType === 'devis' ? 'Devis #' : 'Facture #') + number;
            updatePreviewUI();
            const modalEl = document.getElementById('invoicePreviewModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }

        function setPreviewDocType(type) {
            currentPreviewType = type;
            updatePreviewUI();
        }

        function togglePreviewStamp() {
            currentPreviewStamp = !currentPreviewStamp;
            updatePreviewUI();
        }

        function updatePreviewUI() {
            if (!currentPreviewId) return;
            const btnF = document.getElementById('previewBtnFacture');
            const btnD = document.getElementById('previewBtnDevis');
            const btnS = document.getElementById('previewBtnStamp');
            const stampLabel = document.getElementById('previewStampLabel');

            if (currentPreviewType === 'facture') {
                btnF.className = 'btn btn-sm btn-light text-dark fw-bold';
                btnD.className = 'btn btn-sm btn-outline-light';
            } else {
                btnF.className = 'btn btn-sm btn-outline-light';
                btnD.className = 'btn btn-sm btn-danger fw-bold';
            }

            if (currentPreviewStamp) {
                btnS.className = 'btn btn-sm btn-success fw-bold';
                stampLabel.innerText = 'Avec Cachet';
            } else {
                btnS.className = 'btn btn-sm btn-outline-light';
                stampLabel.innerText = 'Sans Cachet';
            }

            const url = `/invoices/${currentPreviewId}/print?preview=1&as=${currentPreviewType}&with_stamp=${currentPreviewStamp ? 1 : 0}`;
            document.getElementById('previewIframe').src = url;

            const downloadUrl = `/invoices/${currentPreviewId}/download?as=${currentPreviewType}&with_stamp=${currentPreviewStamp ? 1 : 0}`;
            document.getElementById('previewDownloadLink').href = downloadUrl;
        }

        function printFromPreview() {
            const iframe = document.getElementById('previewIframe');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        }
    </script>
@endsection
