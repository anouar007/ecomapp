<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->getTypeLabel() }} {{ $invoice->display_number ?? $invoice->invoice_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        /* ====================================================
           EXECUTIVE MOROCCAN FACTURE & DEVIS — PRINT LAYOUT
           Engineered for perfect A4 page balance in all scenarios:
           1 item, 3 items, or 15+ items.
           ==================================================== */

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            line-height: 1.45;
            background: #e2e8f0;
        }

        body {
            padding: 24px 16px;
        }

        /* ── Master Document Container ── */
        .invoice-card {
            background: #ffffff;
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
            min-height: 275mm; /* Exact printable A4 height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            position: relative;
        }

        /* ── Top accent stripe ── */
        .accent-bar {
            height: 4px;
            background: #0f172a;
            width: 100%;
        }

        /* ── Document container padding ── */
        .doc-body {
            padding: 30px 38px 20px 38px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ── Header Row ── */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-logo {
            max-height: 54px;
            max-width: 140px;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.3px;
            margin: 0;
        }

        .company-tagline {
            font-size: 11px;
            font-weight: 500;
            color: #64748b;
            margin-top: 4px;
            letter-spacing: 0.2px;
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
            font-size: 25px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1;
            color: #0f172a;
        }

        .doc-number-badge {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 4px;
            letter-spacing: 0.3px;
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

        /* ── Parties Row (Émetteur & Client) ── */
        .parties-row {
            display: flex;
            gap: 18px;
            margin-bottom: 24px;
        }

        .party-box {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 2.5px solid #334155;
            padding: 16px 18px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
        }

        .party-role {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .party-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .party-detail {
            font-size: 12px;
            color: #475569;
            line-height: 1.5;
        }

        .fiscal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 8px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 10.5px;
            color: #334155;
        }

        .fiscal-grid .f-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 2px 7px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .fiscal-grid strong {
            color: #0f172a;
            font-weight: 700;
        }

        .client-ice-badge {
            display: inline-block;
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 6px;
            margin-right: 4px;
        }

        /* ── Items Table Section ── */
        .items-section {
            margin-bottom: 24px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
        }

        .items-table thead tr {
            background: #f8fafc;
        }

        .items-table thead th {
            padding: 11px 14px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1.5px solid #cbd5e1;
            text-align: left;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .items-table tbody td {
            padding: 14px 14px;
            vertical-align: middle;
            font-size: 12.5px;
            color: #1e293b;
        }

        .item-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .item-sku {
            font-size: 11px;
            color: #64748b;
            font-family: monospace;
            margin-top: 3px;
        }

        /* ── Flexible Middle Spacer ── */
        .content-flex-spacer {
            flex: 1;
            min-height: 18px;
        }

        /* ── Summary & Bottom Balanced Row ── */
        .summary-row {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            page-break-inside: avoid;
            margin-top: auto;
        }

        .summary-left {
            flex: 1.25;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-right {
            flex: 1;
            max-width: 370px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* Words / Arrêté Card */
        .words-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #334155;
            padding: 12px 16px;
            border-radius: 0 4px 4px 0;
        }

        .words-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .words-text {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.45;
        }

        .words-highlight {
            font-size: 12.5px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 3px;
            display: block;
        }

        /* Legal / Validity Banners */
        .notice-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 11px;
            color: #475569;
            line-height: 1.45;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        /* Observations Card */
        .notes-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #94a3b8;
            padding: 10px 14px;
            border-radius: 0 4px 4px 0;
            font-size: 11px;
            color: #475569;
        }

        /* Totals Card */
        .totals-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px 20px;
            color: #0f172a;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-size: 12.5px;
        }

        .total-row-label {
            color: #64748b;
            font-weight: 500;
        }

        .total-row-val {
            color: #0f172a;
            font-weight: 700;
            font-size: 13px;
        }

        .totals-divider {
            border: none;
            border-top: 1.5px solid #e2e8f0;
            margin: 10px 0;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding-top: 2px;
        }

        .grand-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
        }

        .grand-value {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.4px;
        }

        .stamp-box {
            text-align: center;
            padding: 4px 0 0 0;
        }

        .stamp-img {
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* ── Official Corporate Footer (Anchored) ── */
        .footer {
            background: #ffffff;
            border-top: 1.5px solid #e2e8f0;
            padding: 14px 36px;
            text-align: center;
            page-break-inside: avoid;
        }

        .footer-thanks {
            color: #0f172a;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .footer-legal {
            color: #64748b;
            font-size: 9.5px;
            line-height: 1.5;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px 12px;
        }

        /* ── Media Print Optimization ── */
        @media print {
            html, body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .invoice-card {
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                min-height: 272mm !important;
                height: auto !important;
                border-radius: 0 !important;
                border: none !important;
                margin: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                page-break-after: auto;
            }
            .doc-body {
                padding: 18px 26px 14px 26px !important;
            }
            .footer {
                padding: 10px 26px 14px 26px !important;
            }
            tr {
                page-break-inside: avoid;
            }
            thead {
                display: table-header-group;
            }
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td { text-align: right; }
        [dir="rtl"] .doc-badge-block { align-items: flex-start; text-align: left; }
        [dir="rtl"] .doc-meta-grid { justify-content: flex-start; }
    </style>
</head>
<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

    @php
        // Resolve company metadata with clean fallbacks
        $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'Winashop'));
        $companyAddress = setting('company_address');
        $companyPhone = setting('company_phone');
        $companyEmail = setting('company_email') ?: config('mail.from.address', 'contact@winashop.com');
        $companyWebsite = setting('company_website');
        $companyTaxId = setting('company_tax_id');
        $companyRegistryId = setting('company_registry_id');
        $companyFiscalId = setting('company_fiscal_id');
        $companyPatente = setting('company_patente');

        // Shipping cost
        $shippingCost = floatval($invoice->shipping_cost ?? ($invoice->order->shipping_cost ?? 0));

        // Stamp flag
        $showStamp = ($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp');
    @endphp

    <div class="invoice-card">
        <div class="accent-bar"></div>
        <div class="doc-body">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="header-row">
                {{-- Left: Brand & Logo --}}
                <div class="header-brand">
                    @if(setting('app_logo'))
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" class="header-logo" alt="Logo">
                    @endif
                    <div class="brand-text">
                        <div class="company-name">{{ $companyName }}</div>
                        @if($companyWebsite)
                            <div class="company-tagline">{{ $companyWebsite }}</div>
                        @endif
                    </div>
                </div>

                {{-- Right: Document Type, Number & Key Dates --}}
                <div class="doc-badge-block">
                    <div class="doc-title-row">
                        <span class="doc-type-label">
                            {{ $invoice->isDeliveryNote() ? 'BON DE LIVRAISON' : ($invoice->isQuote() ? 'DEVIS' : 'FACTURE') }}
                        </span>
                        <span class="doc-number-badge">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-grid">
                        <div class="meta-item"><span class="meta-k">Date :</span> <span class="meta-v">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
                        @if($invoice->isDeliveryNote())
                            @if($invoice->order && $invoice->order->order_number)
                                <div class="meta-item"><span class="meta-k">Réf. Commande :</span> <span class="meta-v">{{ $invoice->order->order_number }}</span></div>
                            @endif
                        @elseif($invoice->isQuote())
                            <div class="meta-item"><span class="meta-k">Validité :</span> <span class="meta-v">{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }} (30j)</span></div>
                        @elseif($invoice->due_date)
                            <div class="meta-item"><span class="meta-k">Échéance :</span> <span class="meta-v">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
                        @endif
                        @if(!$invoice->isDeliveryNote() && $invoice->order && $invoice->order->order_number)
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
                        <div class="party-name">{{ $companyName }}</div>
                        <div class="party-detail">
                            <div>{{ $companyAddress ?: 'Maroc' }}</div>
                            <div style="margin-top:3px;">
                                @if($companyPhone)<span><i class="fas fa-phone" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $companyPhone }}</span>@endif
                                @if($companyPhone && $companyEmail)&nbsp;•&nbsp;@endif
                                @if($companyEmail)<span><i class="fas fa-envelope" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $companyEmail }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @if($companyTaxId || $companyRegistryId || $companyFiscalId || $companyPatente)
                    <div class="fiscal-grid">
                        @if($companyTaxId)<div class="f-item"><strong>ICE:</strong> {{ $companyTaxId }}</div>@endif
                        @if($companyRegistryId)<div class="f-item"><strong>RC:</strong> {{ $companyRegistryId }}</div>@endif
                        @if($companyFiscalId)<div class="f-item"><strong>IF:</strong> {{ $companyFiscalId }}</div>@endif
                        @if($companyPatente)<div class="f-item"><strong>Patente:</strong> {{ $companyPatente }}</div>@endif
                    </div>
                    @endif
                </div>

                {{-- Client / Destinataire --}}
                <div class="party-box">
                    <div>
                        <div class="party-role"><i class="fas fa-user-check"></i>{{ $invoice->getBillToLabel() }}</div>
                        @if($invoice->customer_company)
                            <div class="party-name" style="font-size: 15px; font-weight: 800; color: #0f172a;">{{ $invoice->customer_company }}</div>
                            <div style="font-size: 12px; color: #475569; margin-bottom: 3px; font-weight: 600;">
                                <i class="fas fa-user" style="font-size:10px; color:#94a3b8; margin-right:4px;"></i>Attn: {{ $invoice->customer_name }}
                            </div>
                        @else
                            <div class="party-name">{{ $invoice->customer_name }}</div>
                        @endif
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
                    <div>
                        @if($invoice->ice || $invoice->customer_rc || $invoice->customer_if || $invoice->customer_patente)
                            <div style="margin-top: 6px;">
                                @if($invoice->ice)<span class="client-ice-badge">ICE : {{ $invoice->ice }}</span>@endif
                                @if($invoice->customer_rc)<span class="client-ice-badge">RC : {{ $invoice->customer_rc }}</span>@endif
                                @if($invoice->customer_if)<span class="client-ice-badge">IF : {{ $invoice->customer_if }}</span>@endif
                                @if($invoice->customer_patente)<span class="client-ice-badge">Patente : {{ $invoice->customer_patente }}</span>@endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ ITEMS TABLE ══════════ --}}
            <div class="items-section">
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
            </div>

            {{-- Spacer that absorbs excess space in few-item invoices --}}
            <div class="content-flex-spacer"></div>

            {{-- ══════════ SUMMARY ROW (BALANCED SPACE USAGE) ══════════ --}}
            <div class="summary-row">
                {{-- Left: Arrêté + Mentions légales + Notes --}}
                <div class="summary-left">
                    <div class="words-card">
                        <div class="words-label"><i class="fas fa-file-contract"></i>Montant en Toutes Lettres</div>
                        <div class="words-text">
                            {{ $invoice->isDeliveryNote() ? 'Arrêté le présent bon de livraison à la somme de :' : ($invoice->isQuote() ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :') }}
                            <span class="words-highlight">{{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC</span>
                        </div>
                    </div>

                    @if($invoice->isDeliveryNote())
                        <div class="notice-banner" style="border-left: 3px solid #334155; background: #f8fafc;">
                            <i class="fas fa-truck-loading" style="margin-top:2px; color:#0f172a;"></i>
                            <div>
                                <strong>Bon pour réception et décharge :</strong> « Marchandises reçues conformes en quantité et qualité, en parfait état. »
                                <div style="margin-top:6px; font-size:11px; color:#64748b;">
                                    Date de réception : _____ / _____ / 20___ &nbsp;&nbsp;•&nbsp;&nbsp; Nom & Signature / Cachet :
                                </div>
                            </div>
                        </div>
                    @elseif($invoice->isQuote())
                        <div class="notice-banner">
                            <i class="fas fa-calendar-check" style="margin-top:2px; color:#0f172a;"></i>
                            <div>
                                Validité de l'offre : <strong>30 jours</strong> à compter de la date d'émission. Ce document ne constitue pas une facture.
                            </div>
                        </div>
                    @else
                        <div class="notice-banner">
                            <i class="fas fa-shield-alt" style="margin-top:2px; color:#0f172a;"></i>
                            <div>
                                Facture payable à réception. En cas de retard de paiement, des pénalités seront appliquées conformément aux dispositions légales en vigueur (Code Général des Impôts - Maroc).
                            </div>
                        </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-card">
                        <strong style="color:#0f172a;"><i class="fas fa-sticky-note" style="margin-right:4px;"></i>Observations :</strong> {{ $invoice->notes }}
                    </div>
                    @endif
                </div>

                {{-- Right: Totals Card + Stamp --}}
                <div class="summary-right">
                    <div class="totals-card">
                        <div class="total-row">
                            <span class="total-row-label">Total HT</span>
                            <span class="total-row-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-row-label">Total {{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="total-row-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-row-label">Remise</span>
                            <span class="total-row-val" style="color:#059669;">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        @if($shippingCost > 0)
                        <div class="total-row">
                            <span class="total-row-label">Frais de livraison</span>
                            <span class="total-row-val" style="color:#0f172a; font-weight:700;">+ {{ currency($shippingCost) }}</span>
                        </div>
                        @endif
                        <hr class="totals-divider">
                        <div class="grand-total-row">
                            <span class="grand-label">Total TTC</span>
                            <span class="grand-value">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>

                    @if($showStamp)
                    <div class="stamp-box">
                        <img src="{{ asset('storage/' . setting('company_stamp')) }}"
                             class="stamp-img"
                             style="max-width:{{ round(140 * intval(setting('company_stamp_scale', 100)) / 100) }}px; max-height:85px;"
                             alt="Cachet">
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ══════════ OFFICIAL CORPORATE FOOTER (ANCHORED) ══════════ --}}
        <div class="footer">
            <div class="footer-thanks">
                <i class="fas fa-check-circle" style="color:#10b981;"></i>
                {{ $invoice->isDeliveryNote() ? 'Marchandise livrée sous réserve de vérification. Merci de votre confiance !' : ($invoice->isQuote() ? 'Nous vous remercions pour votre confiance. Ce devis reste à votre entière disposition.' : 'Nous vous remercions pour votre achat et votre confiance !') }}
            </div>
            <div class="footer-legal">
                <span><strong>{{ $companyName }}</strong></span>
                @if($companyAddress)<span>• {{ $companyAddress }}</span>@endif
                @if($companyPhone)<span>• Tél : {{ $companyPhone }}</span>@endif
                @if($companyEmail)<span>• Email : {{ $companyEmail }}</span>@endif
                @if($companyTaxId)<span>• ICE : {{ $companyTaxId }}</span>@endif
                @if($companyRegistryId)<span>• RC : {{ $companyRegistryId }}</span>@endif
                @if($companyFiscalId)<span>• IF : {{ $companyFiscalId }}</span>@endif
                @if($companyPatente)<span>• Patente : {{ $companyPatente }}</span>@endif
            </div>
        </div>
    </div>

    <script>
        @if(!request()->has('preview') && !request()->has('no_print'))
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 600);
        };
        @endif
    </script>
</body>
</html>
