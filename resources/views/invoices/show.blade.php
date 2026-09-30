@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    /* ====================================================
       PROFESSIONAL MOROCCAN FACTURE / DEVIS — VIEW / PREVIEW
       Brand: Deep Slate #0f172a | Moroccan Ruby #dc2626
       Identical to Print & PDF layout (DGI Moroccan compliant)
       ==================================================== */

    .invoice-view-wrapper {
        padding: 32px 20px 60px 20px;
        max-width: 1040px;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .btn-action {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    /* ── The Document Card (Identical to Print) ── */
    .invoice-card {
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin: 0 auto;
        color: #0f172a;
    }

    .accent-bar {
        height: 6px;
        background: #dc2626;
    }

    .doc-body {
        padding: 28px 36px;
    }

    /* ── Header ── */
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 18px;
        margin-bottom: 18px;
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-logo {
        max-height: 56px;
        max-width: 140px;
        object-fit: contain;
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .company-name {
        font-size: 24px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.5px;
        margin: 0;
    }

    .company-tagline {
        font-size: 11px;
        font-weight: 700;
        color: #dc2626;
        margin-top: 4px;
        letter-spacing: 0.5px;
    }

    /* ── Document Type & Meta Block ── */
    .doc-badge-block {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .doc-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .doc-type-label {
        font-size: 28px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1;
    }

    .doc-type-label.facture { color: #0f172a; }
    .doc-type-label.devis   { color: #dc2626; }

    .doc-number-badge {
        font-size: 13px;
        font-weight: 800;
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 3px 10px;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }

    .doc-meta-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 12px;
        font-size: 11.5px;
        color: #64748b;
        margin-top: 4px;
    }

    .meta-item {
        white-space: nowrap;
    }

    .meta-k {
        color: #64748b;
    }

    .meta-v {
        font-weight: 700;
        color: #0f172a;
    }

    /* ── Parties row ── */
    .parties-row {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
    }

    .party-box {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-top: 3px solid #0f172a;
        padding: 14px 16px;
        border-radius: 0 0 6px 6px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .party-box.client {
        border-top-color: #dc2626;
    }

    .party-role {
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #0f172a;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .party-box.client .party-role {
        color: #dc2626;
    }

    .party-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .party-detail {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
    }

    .fiscal-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #334155;
    }

    .fiscal-grid .f-item {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        padding: 2px 7px;
        font-size: 10.5px;
        white-space: nowrap;
    }

    .fiscal-grid strong {
        color: #0f172a;
        font-weight: 700;
    }

    .client-ice-badge {
        display: inline-block;
        background: #fee2e2;
        color: #991b1b;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
    }

    /* ── Items table ── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        border-bottom: 2px solid #0f172a;
    }

    .items-table thead tr {
        background: #0f172a;
    }

    .items-table thead th {
        padding: 9px 12px;
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #ffffff;
        border: none;
        text-align: left;
    }

    .items-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .items-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    .items-table tbody td {
        padding: 9px 12px;
        vertical-align: middle;
        font-size: 12px;
    }

    .item-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .item-sku {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }

    /* ── Summary & Bottom Balanced Row ── */
    .summary-row {
        display: flex;
        gap: 18px;
        align-items: flex-start;
    }

    .summary-left {
        flex: 1.25;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .summary-right {
        flex: 1;
        max-width: 370px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .words-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #0f172a;
        padding: 10px 14px;
        border-radius: 0 6px 6px 0;
    }

    .words-label {
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #dc2626;
        margin-bottom: 3px;
    }

    .words-text {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.4;
    }

    .words-highlight {
        font-size: 12.5px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 2px;
        display: block;
    }

    .validity-banner {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 11px;
        color: #854d0e;
        line-height: 1.4;
    }

    .legal-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 11px;
        color: #475569;
        line-height: 1.4;
    }

    .notes-card {
        background: #fff1f2;
        border: 1px solid #fecaca;
        border-left: 3px solid #dc2626;
        padding: 8px 12px;
        border-radius: 0 6px 6px 0;
    }

    .notes-label {
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #dc2626;
        margin-bottom: 2px;
    }

    .notes-text {
        font-size: 11px;
        color: #475569;
        margin: 0;
        line-height: 1.4;
    }

    .totals-card {
        background: #0f172a;
        border-radius: 6px;
        padding: 14px 16px;
        color: white;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        font-size: 11.5px;
    }

    .total-row-label {
        color: rgba(255,255,255,0.65);
        font-weight: 600;
    }

    .total-row-val {
        color: #ffffff;
        font-weight: 700;
        font-size: 12.5px;
    }

    .totals-divider {
        border: none;
        border-top: 1px solid rgba(255,255,255,0.15);
        margin: 8px 0;
    }

    .grand-total-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    .grand-label {
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: rgba(255,255,255,0.5);
    }

    .grand-value {
        font-size: 24px;
        font-weight: 900;
        color: #dc2626;
        letter-spacing: -0.5px;
    }

    .stamp-box {
        background: transparent;
        border: none;
        padding: 6px 0 0 0;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stamp-img {
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    /* ── Footer ── */
    .doc-footer {
        background: #0f172a;
        padding: 12px 36px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: rgba(255,255,255,0.55);
        font-size: 10px;
    }

    .doc-footer-left {
        line-height: 1.5;
    }

    .doc-footer-left strong {
        color: rgba(255,255,255,0.9);
    }

    .doc-footer-right {
        font-size: 11px;
        font-weight: 700;
        color: #dc2626;
        white-space: nowrap;
    }
</style>

<div class="invoice-view-wrapper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <a href="{{ route('invoices.index') }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    {{ __('Retour aux Factures / Devis') }}
                </a>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h1 style="font-size: 24px; font-weight: 900; color: #0f172a; margin: 0; letter-spacing: -0.5px;">
                        {{ $invoice->isQuote() ? 'DEVIS' : 'FACTURE' }}
                        <span style="color: #dc2626; font-weight: 700; font-size: 20px;">#{{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </h1>
                </div>

                {{-- Interactive Mode Controls (Facture / Devis & Stamp) --}}
                <div style="display: flex; gap: 8px; align-items: center; margin-top: 10px; flex-wrap: wrap;">
                    <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Mode Aperçu') }}:</span>
                    
                    {{-- Toggle Facture --}}
                    <a href="{{ route('invoices.show', [$invoice, 'as' => 'facture', 'with_stamp' => $withStamp ? 1 : 0]) }}" 
                       class="btn btn-sm {{ !$invoice->isQuote() ? 'btn-dark' : 'btn-outline-secondary' }}"
                       style="font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 20px;">
                        <i class="fas fa-file-invoice me-1"></i> Facture
                    </a>

                    {{-- Toggle Devis --}}
                    <a href="{{ route('invoices.show', [$invoice, 'as' => 'devis', 'with_stamp' => $withStamp ? 1 : 0]) }}" 
                       class="btn btn-sm {{ $invoice->isQuote() ? 'btn-danger' : 'btn-outline-secondary' }}"
                       style="font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 20px;">
                        <i class="fas fa-file-signature me-1"></i> Devis
                    </a>

                    {{-- Toggle Stamp --}}
                    <a href="{{ route('invoices.show', [$invoice, 'as' => $invoice->isQuote() ? 'devis' : 'facture', 'with_stamp' => $withStamp ? 0 : 1]) }}"
                       class="btn btn-sm {{ $withStamp ? 'btn-success' : 'btn-outline-secondary' }}"
                       style="font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 20px;"
                       title="{{ $withStamp ? 'Désactiver le cachet' : 'Activer le cachet' }}">
                        <i class="fas fa-stamp me-1"></i> {{ $withStamp ? 'Avec Cachet' : 'Sans Cachet' }}
                    </a>
                </div>
            </div>

            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <!-- Download PDF Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #0f172a; border-color: #0f172a;">
                        <i class="fas fa-download"></i> {{ __('Télécharger PDF') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 12px; border-radius: 8px; min-width: 220px; padding: 6px;">
                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-file-invoice text-dark me-1"></i> Facture
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp text-success" style="width: 14px;"></i> Facture avec Cachet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> Facture sans Cachet
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-file-signature text-danger me-1"></i> Devis
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp text-success" style="width: 14px;"></i> Devis avec Cachet
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
                <div class="dropdown">
                    <button class="btn btn-action btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: white;">
                        <i class="fas fa-print"></i> {{ __('Imprimer') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 12px; border-radius: 8px; min-width: 220px; padding: 6px;">
                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-file-invoice text-dark me-1"></i> Facture
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp text-success" style="width: 14px;"></i> Imprimer Facture (Cachet)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                <i class="fas fa-print text-muted" style="width: 14px;"></i> Imprimer Facture (Standard)
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li class="dropdown-header text-uppercase text-muted fw-bold" style="font-size: 9.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-file-signature text-danger me-1"></i> Devis
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp text-success" style="width: 14px;"></i> Imprimer Devis (Cachet)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5 rounded" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                <i class="fas fa-print text-muted" style="width: 14px;"></i> Imprimer Devis (Standard)
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Guarantee Certificate Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #ecfdf5; color: #059669; border-color: #a7f3d0;">
                        <i class="fas fa-shield-alt"></i> {{ __('Garantie') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 12px; border-radius: 8px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5" href="{{ route('invoices.guarantee', $invoice) }}">
                                <i class="fas fa-eye text-success" style="width: 14px;"></i> Voir Certificat
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp text-success" style="width: 14px;"></i> PDF avec Cachet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="far fa-file-pdf text-muted" style="width: 14px;"></i> PDF sans Cachet
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-print text-success" style="width: 14px;"></i> Imprimer (Cachet)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-1.5" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="fas fa-print text-muted" style="width: 14px;"></i> Imprimer (Standard)
                            </a>
                        </li>
                    </ul>
                </div>

                @if($invoice->canEdit())
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-action btn-outline-secondary" style="background: white; color: #475569; border-color: #e2e8f0;">
                    <i class="fas fa-edit"></i> {{ __('Modifier') }}
                </a>
                @endif

                @if($invoice->isInvoice() && $invoice->remaining_balance > 0)
                <button type="button" class="btn btn-action btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                    <i class="fas fa-money-bill-wave"></i> {{ __('Paiement') }}
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ══════════ EXACT MOROCCAN DOCUMENT VIEW (IDENTICAL TO PRINT & PDF) ══════════ -->
    <div class="invoice-card">
        <div class="accent-bar"></div>
        <div class="doc-body">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="header-row">
                {{-- Left: Brand & Logo --}}
                <div class="header-brand">
                    @if(setting('app_logo'))
                        <img src="/storage/{{ setting('app_logo') }}" class="header-logo" alt="Logo" onerror="this.src='{{ asset('storage/' . setting('app_logo')) }}'">
                    @endif
                    <div class="brand-text">
                        <div class="company-name">{{ setting('company_name', setting('app_name')) }}</div>
                        @if(setting('company_website'))
                            <div class="company-tagline">{{ setting('company_website') }}</div>
                        @endif
                    </div>
                </div>

                {{-- Right: Document Type, Number & Key Dates --}}
                <div class="doc-badge-block">
                    <div class="doc-title-row">
                        <span class="doc-type-label {{ $invoice->isQuote() ? 'devis' : 'facture' }}">
                            {{ $invoice->isQuote() ? 'DEVIS' : 'FACTURE' }}
                        </span>
                        <span class="doc-number-badge">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-grid">
                        <div class="meta-item"><span class="meta-k">Date :</span> <span class="meta-v">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
                        @if($invoice->isQuote())
                            <div class="meta-item"><span class="meta-k">Validité :</span> <span class="meta-v">{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }} (30j)</span></div>
                        @elseif($invoice->due_date)
                            <div class="meta-item"><span class="meta-k">Échéance :</span> <span class="meta-v" style="color:#dc2626;">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
                        @endif
                        @if($invoice->order && $invoice->order->order_number)
                            <div class="meta-item"><span class="meta-k">Réf. Commande :</span> <span class="meta-v">{{ $invoice->order->order_number }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ PARTIES (ÉMETTEUR & CLIENT) ══════════ --}}
            <div class="parties-row">
                {{-- Vendor / Émetteur --}}
                <div class="party-box">
                    <div>
                        <div class="party-role"><i class="fas fa-building"></i>Émetteur / Fournisseur</div>
                        <div class="party-name">{{ setting('company_name', setting('app_name')) }}</div>
                        <div class="party-detail">
                            @if(setting('company_address'))<div>{{ setting('company_address') }}</div>@endif
                            <div style="margin-top:3px;">
                                @if(setting('company_phone'))<span><i class="fas fa-phone" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ setting('company_phone') }}</span>@endif
                                @if(setting('company_phone') && setting('company_email'))&nbsp;•&nbsp;@endif
                                @if(setting('company_email'))<span><i class="fas fa-envelope" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ setting('company_email') }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @if(setting('company_tax_id') || setting('company_registry_id') || setting('company_fiscal_id') || setting('company_patente'))
                    <div class="fiscal-grid">
                        @if(setting('company_tax_id'))<div class="f-item"><strong>ICE:</strong> {{ setting('company_tax_id') }}</div>@endif
                        @if(setting('company_registry_id'))<div class="f-item"><strong>RC:</strong> {{ setting('company_registry_id') }}</div>@endif
                        @if(setting('company_fiscal_id'))<div class="f-item"><strong>IF:</strong> {{ setting('company_fiscal_id') }}</div>@endif
                        @if(setting('company_patente'))<div class="f-item"><strong>Patente:</strong> {{ setting('company_patente') }}</div>@endif
                    </div>
                    @endif
                </div>

                {{-- Client / Destinataire --}}
                <div class="party-box client">
                    <div>
                        <div class="party-role"><i class="fas fa-user-check"></i>{{ $invoice->isQuote() ? 'Destinataire' : 'Client / Facturé à' }}</div>
                        <div class="party-name">{{ $invoice->customer_name }}</div>
                        <div class="party-detail">
                            @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                            @if($address)<div>{{ $address }}</div>@endif
                            <div style="margin-top:3px;">
                                @if($invoice->customer_phone)<span><i class="fas fa-phone" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $invoice->customer_phone }}</span>@endif
                                @if($invoice->customer_phone && $invoice->customer_email)&nbsp;•&nbsp;@endif
                                @if($invoice->customer_email)<span><i class="fas fa-envelope" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $invoice->customer_email }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @if($invoice->ice)
                    <div style="margin-top:10px; padding-top:8px; border-top:1px solid #e2e8f0;">
                        <span class="client-ice-badge"><strong>ICE Client :</strong> {{ $invoice->ice }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ══════════ ITEMS TABLE ══════════ --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center;">N°</th>
                        <th style="width:45%;">Désignation / Description</th>
                        <th style="width:8%; text-align:center;">Qté</th>
                        <th style="width:14%; text-align:right;">P.U. HT</th>
                        <th style="width:10%; text-align:center;">{{ $invoice->tax_label }} %</th>
                        <th style="width:18%; text-align:right;">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td style="text-align:center; color:#94a3b8; font-size:11px;">{{ $i + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                            <div class="item-sku">Réf : {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td style="text-align:center; font-weight:700; font-size:13px;">{{ $item->quantity }}</td>
                        <td style="text-align:right; font-size:12.5px;">{{ $item->formatted_unit_price_ht }}</td>
                        <td style="text-align:center; color:#64748b; font-size:12px;">{{ $invoice->formatted_tax_rate }}</td>
                        <td style="text-align:right; font-weight:800; font-size:13px; color:#0f172a;">{{ $item->formatted_total_price_ht }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- ══════════ SUMMARY ROW (BALANCED SPACE USAGE) ══════════ --}}
            <div class="summary-row">
                {{-- Left: Arrêté + Mentions légales + Notes --}}
                <div class="summary-left">
                    <div class="words-card">
                        <div class="words-label"><i class="fas fa-file-contract" style="margin-right:4px;"></i>Montant en Lettres</div>
                        <div class="words-text">
                            {{ $invoice->isQuote() ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :' }}
                            <span class="words-highlight">{{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC</span>
                        </div>
                    </div>

                    @if($invoice->isQuote())
                    <div class="validity-banner">
                        <i class="fas fa-calendar-check" style="margin-right:5px;"></i>
                        Validité de l'offre : <strong>30 jours</strong> à compter de la date d'émission. Ce document ne constitue pas une facture.
                    </div>
                    @else
                    <div class="legal-banner">
                        <i class="fas fa-shield-alt" style="margin-right:5px; color:#0f172a;"></i>
                        Facture payable à réception. En cas de retard de paiement, des pénalités seront appliquées conformément aux dispositions légales (CGI Maroc).
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-card">
                        <div class="notes-label"><i class="fas fa-sticky-note" style="margin-right:4px;"></i>Observations / Notes</div>
                        <p class="notes-text">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Right: Totals Card + Cachet Box --}}
                <div class="summary-right">
                    <div class="totals-card">
                        <div class="total-row">
                            <span class="total-row-label">Sous-total HT</span>
                            <span class="total-row-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-row-label">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="total-row-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-row-label" style="color:#fca5a5;">Remise</span>
                            <span class="total-row-val" style="color:#fca5a5;">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        <hr class="totals-divider">
                        <div class="grand-total-row">
                            <span class="grand-label">Total TTC</span>
                            <span class="grand-value">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>

                    {{-- Cachet Société placed right under Totals for perfect visual balance --}}
                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                    <div class="stamp-box">
                        <img src="/storage/{{ setting('company_stamp') }}"
                             class="stamp-img"
                             style="max-width:{{ round(140 * intval(setting('company_stamp_scale', 100)) / 100) }}px; max-height:85px;"
                             alt="Cachet"
                             onerror="this.src='{{ asset('storage/' . setting('company_stamp')) }}'">
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ══════════ FOOTER ══════════ --}}
        <div class="doc-footer">
            <div class="doc-footer-left">
                <strong>{{ setting('company_name') }}</strong>
                @if(setting('company_tax_id')) &nbsp;|&nbsp; ICE : {{ setting('company_tax_id') }} @endif
                @if(setting('company_registry_id')) &nbsp;|&nbsp; RC : {{ setting('company_registry_id') }} @endif
                @if(setting('company_fiscal_id')) &nbsp;|&nbsp; IF : {{ setting('company_fiscal_id') }} @endif
                @if(setting('company_patente')) &nbsp;|&nbsp; Patente : {{ setting('company_patente') }} @endif
                @if(setting('company_address')) <br>{{ setting('company_address') }} @endif
            </div>
            <div class="doc-footer-right">
                <i class="fas fa-heart" style="margin-right:5px; font-size:9px;"></i>
                {{ $invoice->isQuote() ? 'Merci de votre confiance !' : 'Merci pour votre achat !' }}
            </div>
        </div>
    </div>

    {{-- Payment History (Admin Section) --}}
    @if($invoice->payments->count() > 0)
    <div style="margin-top: 36px; background: white; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
            <i class="fas fa-receipt me-2" style="color: #dc2626;"></i>{{ __('Historique des Règlements') }}
        </h3>
        
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Date') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Mode') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Référence') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase; text-align: right;">{{ __('Montant') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase; text-align: center;">{{ __('Justificatif') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td style="font-size: 12px; color: #475569;">{{ $payment->payment_date->translatedFormat('d M, Y') }}</td>
                    <td style="font-size: 12px; font-weight: 600; color: #0f172a;">{{ __(ucfirst(str_replace('_', ' ', $payment->payment_method))) }}</td>
                    <td style="font-size: 12px; color: #64748b;">{{ $payment->transaction_reference ?? '-' }}</td>
                    <td style="font-size: 12px; font-weight: 700; color: #059669; text-align: right;">{{ currency($payment->amount) }}</td>
                    <td style="text-align: center;">
                        @if($payment->proof_file_path)
                        <a href="{{ asset('storage/' . $payment->proof_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px;">
                            <i class="fas fa-file-alt"></i> {{ __('Voir') }}
                        </a>
                        @else
                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Record Payment Modal -->
@if($invoice->isInvoice() && $invoice->remaining_balance > 0)
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Enregistrer un Paiement') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('payments.store', $invoice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Montant du règlement') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ setting('currency_symbol', 'DH') }}</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $invoice->remaining_balance }}" max="{{ $invoice->remaining_balance }}" required>
                        </div>
                        <small class="text-muted">{{ __('Solde restant') }}: {{ $invoice->formatted_remaining_balance }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Date de paiement') }}</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Mode de paiement') }}</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">{{ __('Espèces') }}</option>
                            <option value="bank_transfer">{{ __('Virement bancaire') }}</option>
                            <option value="check">{{ __('Chèque') }}</option>
                            <option value="card">{{ __('Carte bancaire') }}</option>
                            <option value="other">{{ __('Autre') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Numéro de référence / Chèque / Transaction') }}</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="ex: CHQ-928120">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Justificatif de paiement') }}</label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn btn-primary" style="background:#0f172a; border-color:#0f172a;">{{ __('Enregistrer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
