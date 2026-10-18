<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->getTypeLabel() }} {{ $invoice->display_number ?? $invoice->invoice_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;800&display=swap');

        /* ====================================================
           PROFESSIONAL MOROCCAN FACTURE / DEVIS — PRINT
           Full A4 balanced vertical distribution
           ==================================================== */

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
            color: #0f172a;
            line-height: 1.4;
            padding: 24px;
            background: #e2e8f0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        body, table, th, td, p, span:not([class*="fa"]):not(.fas):not(.far):not(.fab), div:not([class*="fa"]), a, h1, h2, h3, h4, h5, h6 {
            font-family: "Roboto", sans-serif !important;
        }

        .fas, .fa-solid {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
        }

        /* ── Full A4 Page card ── */
        .invoice-card {
            background: #ffffff;
            width: 100%;
            max-width: 900px;
            min-height: 1060px;
            border-radius: 6px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* ── Top accent stripe ── */
        .accent-bar {
            height: 4px;
            background: #0f172a;
            width: 100%;
            flex-shrink: 0;
        }

        /* ── Document container ── */
        .doc-body {
            padding: 28px 36px 16px 36px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 16px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-logo {
            max-height: 56px;
            max-width: 150px;
            object-fit: contain;
        }

        .brand-monogram {
            width: 48px;
            height: 48px;
            background: #0f172a;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            letter-spacing: -1px;
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
            margin-top: 3px;
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
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1;
            color: #0f172a;
        }

        .doc-number-badge {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 3px 10px;
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

        /* ── Wide, Low-Height Client Banner (Full Width, Low Height) ── */
        .client-banner {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #0f172a;
            border-radius: 4px;
            padding: 10px 16px;
            margin-bottom: 18px;
            flex-shrink: 0;
        }

        .client-banner-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .client-role-badge {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .client-ice-badge {
            display: inline-block;
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 1px 8px;
            border-radius: 3px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .client-banner-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .client-main-name {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.2px;
        }

        .client-details-row {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 11.5px;
            color: #475569;
            flex-wrap: wrap;
        }

        .c-info-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .c-info-item i {
            font-size: 10px;
            color: #94a3b8;
        }

        /* ── Items table container ── */
        .items-section {
            margin-bottom: 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }

        .items-table thead tr {
            background: #0f172a;
            color: #ffffff;
        }

        .items-table thead th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #ffffff;
            text-align: left;
            border: 1px solid #0f172a;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            font-size: 12px;
            color: #1e293b;
        }

        .item-name {
            font-size: 12.5px;
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
            gap: 16px;
            align-items: stretch;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .summary-left {
            flex: 1.25;
            display: flex;
            flex-direction: column;
            gap: 8px;
            justify-content: space-between;
        }

        .summary-right {
            flex: 1;
            max-width: 370px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Words / Arrêté */
        .words-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0f172a;
            padding: 9px 12px;
            border-radius: 0 4px 4px 0;
        }

        .words-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 2px;
        }

        .words-text {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.35;
        }

        .words-highlight {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
            display: block;
        }

        /* Banners */
        .legal-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 10.5px;
            color: #475569;
            line-height: 1.35;
        }

        .notes-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #94a3b8;
            padding: 7px 12px;
            border-radius: 0 4px 4px 0;
        }

        .notes-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .notes-text {
            font-size: 10.5px;
            color: #475569;
            margin: 0;
            line-height: 1.35;
        }

        /* Totals card */
        .totals-card {
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            color: #0f172a;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .total-row-label {
            color: #64748b;
            font-weight: 500;
        }

        .total-row-val {
            color: #0f172a;
            font-weight: 700;
            font-size: 12.5px;
        }

        .totals-divider {
            border: none;
            border-top: 1.5px solid #cbd5e1;
            margin: 6px 0;
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
            font-size: 21px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        /* ── Cachet Area (Stamp auto-added) ── */
        .stamp-box {
            margin-top: 10px;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .stamp-img {
            max-width: 140px;
            max-height: 80px;
            object-fit: contain;
            display: inline-block;
        }

        /* ── Footer Pinned at Bottom ── */
        .footer {
            background: #ffffff;
            border-top: 1.5px solid #e2e8f0;
            padding: 12px 36px;
            text-align: center;
            color: #64748b;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.2px;
            line-height: 1.5;
            margin-top: auto;
            flex-shrink: 0;
        }

        .footer-legal {
            color: #334155;
            font-weight: 600;
        }

        .footer-fiscal {
            color: #64748b;
            margin-top: 2px;
        }

        .footer-closing {
            margin-top: 4px;
            font-size: 10.5px;
            color: #0f172a;
            font-weight: 700;
        }

        /* ── Print Media Optimization ── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm 8mm 10mm;
            }

            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                min-height: calc(297mm - 16mm) !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                page-break-inside: avoid !important;
            }

            .doc-body {
                padding: 10px 14px 6px 14px !important;
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }

            .items-section {
                flex: 1 !important;
            }

            .footer {
                margin-top: auto !important;
                padding: 8px 14px !important;
                border-top: 1px solid #cbd5e1 !important;
            }
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td { text-align: right; }
        [dir="rtl"] .doc-badge-block { align-items: flex-start; text-align: left; }
        [dir="rtl"] .doc-meta-grid { justify-content: flex-start; }
    </style>
</head>

@php
    $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'WINA SHOP'));
    $companyTagline = setting('company_tagline') ?: (setting('company_website') ?: 'Vente de Matériel & Accessoires');
    $companyLogo = setting('company_logo') ?: setting('app_logo');
    $companyAddress = setting('company_address') ?: 'Casablanca, Maroc';
    $companyPhone = setting('company_phone');
    $companyEmail = setting('company_email') ?: config('mail.from.address', 'contact@winashop.com');
    $companyIce = setting('company_tax_id') ?: setting('company_ice', '002938475000089');
    $companyRc = setting('company_registry_id') ?: setting('company_rc', '49821');
    $companyIf = setting('company_fiscal_id') ?: setting('company_if', '39482710');
    $companyPatente = setting('company_patente') ?: '37920194';
    $companyCnss = setting('company_cnss');
    $isQuote = $invoice->isQuote();
@endphp

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="invoice-card">
        <div class="accent-bar"></div>
        <div class="doc-body">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="header-row">
                {{-- Left: Brand & Logo --}}
                <div class="header-brand">
                    @if($companyLogo)
                        <img src="{{ asset('storage/' . $companyLogo) }}" class="header-logo" alt="Logo">
                    @else
                        <div class="brand-monogram">
                            {{ strtoupper(substr($companyName, 0, 1)) }}
                        </div>
                    @endif
                    <div class="brand-text">
                        <div class="company-name">{{ $companyName }}</div>
                        <div class="company-tagline">{{ $companyTagline }}</div>
                    </div>
                </div>

                {{-- Right: Document Type, Number & Key Dates --}}
                <div class="doc-badge-block">
                    <div class="doc-title-row">
                        <span class="doc-type-label {{ $isQuote ? 'devis' : 'facture' }}">
                            {{ $isQuote ? 'DEVIS' : 'FACTURE' }}
                        </span>
                        <span class="doc-number-badge">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-grid">
                        <div class="meta-item"><span class="meta-k">Date :</span> <span class="meta-v">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
                        @if($isQuote)
                            <div class="meta-item"><span class="meta-k">Validité :</span> <span class="meta-v">{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }} (30j)</span></div>
                        @elseif($invoice->due_date)
                            <div class="meta-item"><span class="meta-k">Échéance :</span> <span class="meta-v">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
                        @endif
                        @if($invoice->order && $invoice->order->order_number)
                            <div class="meta-item"><span class="meta-k">Réf. Commande :</span> <span class="meta-v">{{ $invoice->order->order_number }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ CLIENT (FULL WIDTH, LOW HEIGHT) ══════════ --}}
            <div class="client-banner">
                <div class="client-banner-header">
                    <span class="client-role-badge">
                        <i class="fas fa-user-check"></i>
                        {{ $isQuote ? 'Destinataire / Devis pour' : 'Client / Facturé à' }}
                    </span>
                    @if($invoice->ice)
                        <span class="client-ice-badge">ICE : {{ $invoice->ice }}</span>
                    @endif
                </div>
                <div class="client-banner-body">
                    <div class="client-main-name">{{ $invoice->customer_name }}</div>
                    <div class="client-details-row">
                        @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                        @if($address)
                            <span class="c-info-item"><i class="fas fa-map-marker-alt"></i> {{ $address }}{{ $invoice->customer_city ? ', ' . $invoice->customer_city : '' }}</span>
                        @elseif($invoice->customer_city)
                            <span class="c-info-item"><i class="fas fa-map-marker-alt"></i> {{ $invoice->customer_city }}</span>
                        @endif
                        @if($invoice->customer_phone)
                            <span class="c-info-item"><i class="fas fa-phone"></i> {{ $invoice->customer_phone }}</span>
                        @endif
                        @if($invoice->customer_email)
                            <span class="c-info-item"><i class="fas fa-envelope"></i> {{ $invoice->customer_email }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ ITEMS TABLE ══════════ --}}
            <div class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">N°</th>
                            <th style="width: 50%;">Désignation / Description</th>
                            <th style="width: 15%; text-align: right;">P.U. HT</th>
                            <th style="width: 10%; text-align: center;">Qté</th>
                            <th style="width: 20%; text-align: right;">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $idx => $item)
                        <tr>
                            <td style="text-align: center; color: #64748b; font-weight: 600;">{{ $idx + 1 }}</td>
                            <td>
                                <div class="item-name">{{ $item->product_name }}</div>
                                @if($item->product_sku)
                                    <div class="item-sku">Réf : {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="text-align: right; color: #334155; font-weight: 500;">
                                {{ $item->formatted_unit_price_ht }}
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #0f172a;">
                                {{ $item->quantity }}
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #0f172a;">
                                {{ $item->formatted_total_price_ht }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ══════════ SUMMARY & FINANCIAL TOTALS ══════════ --}}
            <div class="summary-row">
                {{-- Left: Arrêté + Mentions légales + Notes --}}
                <div class="summary-left">
                    <div class="words-card">
                        <div class="words-label"><i class="fas fa-file-contract" style="margin-right:4px;"></i>Montant en Lettres</div>
                        <div class="words-text">
                            {{ $isQuote ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :' }}
                            <span class="words-highlight">{{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC</span>
                        </div>
                    </div>

                    @if($isQuote)
                    <div class="legal-banner">
                        <i class="fas fa-calendar-check" style="margin-right:5px; color:#0f172a;"></i>
                        Validité de l'offre : <strong>30 jours</strong>. Ce devis est une proposition commerciale. Pour accord, veuillez nous le retourner signé et revêtu de votre mention <em>« Bon pour accord »</em>.
                    </div>
                    @else
                    <div class="legal-banner">
                        <i class="fas fa-shield-alt" style="margin-right:5px; color:#0f172a;"></i>
                        Facture payable selon les conditions convenues. Mode de règlement : <strong>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method ?: 'Espèces')) }}</strong>. En cas de retard, des pénalités seront appliquées (Art. 78-3 du Code de Commerce / CGI Maroc).
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-card">
                        <div class="notes-label"><i class="fas fa-sticky-note" style="margin-right:4px;"></i>Observations / Instructions</div>
                        <p class="notes-text">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Right: Totals Card + Cachet Auto-Added --}}
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
                        @if($invoice->shipping_cost > 0)
                        <div class="total-row">
                            <span class="total-row-label">Frais de livraison</span>
                            <span class="total-row-val">{{ $invoice->formatted_shipping_cost }}</span>
                        </div>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-row-label">Remise</span>
                            <span class="total-row-val">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        <hr class="totals-divider">
                        <div class="grand-total-row">
                            <span class="grand-label">Total TTC</span>
                            <span class="grand-value">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>

                    {{-- Dedicated space for the auto-added Cachet --}}
                    <div class="stamp-box">
                        @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                            <img src="{{ asset('storage/' . setting('company_stamp')) }}"
                                 class="stamp-img"
                                 style="max-width:{{ round(140 * intval(setting('company_stamp_scale', 100)) / 100) }}px; max-height:80px;"
                                 alt="Cachet">
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════ EXECUTIVE MOROCCAN LEGAL FOOTER (PINNED TO BOTTOM) ══════════ --}}
        <div class="footer">
            <div class="footer-legal">
                <strong>{{ $companyName }}</strong>
                @if(setting('company_legal_form', 'SARL')) &bull; Société {{ setting('company_legal_form', 'SARL') }} @endif
                @if(setting('company_capital')) &bull; Capital : {{ setting('company_capital') }} DH @endif
                @if($companyAddress) &bull; {{ $companyAddress }} @endif
                @if($companyPhone) &bull; Tél : {{ $companyPhone }} @endif
                @if($companyEmail) &bull; {{ $companyEmail }} @endif
            </div>
            <div class="footer-fiscal">
                @if($companyIce) <span><strong>ICE :</strong> {{ $companyIce }}</span> @endif
                @if($companyRc) <span>&bull; <strong>RC :</strong> {{ $companyRc }}</span> @endif
                @if($companyIf) <span>&bull; <strong>IF :</strong> {{ $companyIf }}</span> @endif
                @if($companyPatente) <span>&bull; <strong>Patente :</strong> {{ $companyPatente }}</span> @endif
                @if($companyCnss) <span>&bull; <strong>CNSS :</strong> {{ $companyCnss }}</span> @endif
            </div>
            <div class="footer-closing">
                <i class="fas fa-handshake" style="margin-right:4px; font-size:10.5px; color:#0f172a;"></i>
                {{ $isQuote ? 'Merci pour votre confiance ! Proposition soumise à nos conditions générales.' : 'Merci pour votre achat et votre confiance !' }}
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
