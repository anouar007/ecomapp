<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->getTypeLabel() }} {{ $invoice->display_number ?? $invoice->invoice_number }}</title>
    <style>
        /* ====================================================
           PROFESSIONAL MOROCCAN FACTURE / DEVIS — PDF TEMPLATE
           Brand: Deep Slate #0f172a | Moroccan Ruby #dc2626
           Space-optimized, executive layout (DGI compliant)
           ==================================================== */
        @page {
            margin: 0;
            padding: 0;
            size: A4 portrait;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 9.5px;
        }

        /* ── Top accent stripe ── */
        .top-stripe {
            height: 3px;
            background: #334155;
            width: 100%;
        }

        /* ── Page wrapper ── */
        .page {
            padding: 24px 34px 60px 34px;
        }

        /* ── Header Table ── */
        .header-table {
            width: 100%;
            margin-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 14px;
        }

        .company-name {
            font-size: 19px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .company-tagline {
            font-size: 8.5px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-top: 3px;
        }

        .doc-type-badge {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1;
            color: #0f172a;
        }

        .doc-type-badge.facture { color: #0f172a; }
        .doc-type-badge.devis   { color: #0f172a; }

        .doc-number-tag {
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #334155;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            margin-top: 3px;
        }

        .doc-meta-text {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.4;
        }

        .doc-meta-text strong {
            color: #0f172a;
        }

        /* ── Client Banner (Full Width, Low Height) ── */
        .client-banner {
            width: 100%;
            margin-bottom: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0f172a;
            border-radius: 4px;
            padding: 8px 12px;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
        }

        .client-role {
            font-size: 7.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .client-ice-badge {
            display: inline-block;
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }

        .client-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.2px;
            margin-top: 2px;
        }

        .client-detail {
            font-size: 8.5px;
            color: #475569;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table thead tr {
            background: #f8fafc;
        }

        .items-table thead th {
            padding: 8px 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #475569;
            border: none;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #cbd5e1;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 9.5px;
        }

        .item-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
        }

        .item-sku {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ── Totals + Bottom Layout ── */
        .totals-outer {
            width: 100%;
        }

        .words-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #cbd5e1;
            padding: 10px 12px;
            border-radius: 0 4px 4px 0;
            margin-bottom: 10px;
        }

        .words-label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 3px;
        }

        .words-text {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.4;
        }

        .validity-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .legal-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .notes-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #cbd5e1;
            padding: 6px 10px;
            border-radius: 0 4px 4px 0;
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
        }

        /* Totals Card */
        .totals-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 12px 14px;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .total-label {
            display: table-cell;
            font-size: 8.5px;
            color: #64748b;
            font-weight: bold;
        }

        .total-val {
            display: table-cell;
            text-align: right;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .total-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 6px 0;
        }

        .grand-label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .grand-value {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-align: right;
        }

        /* Stamp Box */
        .stamp-box {
            padding: 4px 0 0 0;
            text-align: center;
        }


        /* ── Fixed Footer ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 32px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 7.5px;
            font-weight: 500;
            line-height: 14px;
            text-align: center;
            padding: 4px 34px 0 34px;
        }
    </style>
</head>
@php
    $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'WINA SHOP'));
    $companyTagline = setting('company_tagline') ?: (setting('company_website') ?: 'Commerce & Distribution');
    $companyAddress = setting('company_address') ?: 'Casablanca, Maroc';
    $companyPhone = setting('company_phone');
    $companyEmail = setting('company_email') ?: config('mail.from.address', 'contact@winashop.com');
    $companyIce = setting('company_tax_id') ?: setting('company_ice', '002938475000089');
    $companyRc = setting('company_registry_id') ?: setting('company_rc', '49821');
    $companyIf = setting('company_fiscal_id') ?: setting('company_if', '39482710');
    $companyPatente = setting('company_patente') ?: '37920194';
    $companyCnss = setting('company_cnss');
@endphp

<body>
    <div class="top-stripe"></div>
    <div class="page">

        {{-- ══════════ HEADER ══════════ --}}
        <table class="header-table">
            <tr>
                {{-- Left: Logo & Company Name --}}
                <td style="width:58%; vertical-align:middle;">
                    <table style="width:100%;">
                        <tr>
                            @php
                                $logoPath = app_logo_path();
                            @endphp
                            @if($logoPath && file_exists($logoPath))
                                <td style="width:75px; vertical-align:middle;">
                                    <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                                         style="max-width:70px; max-height:50px; display:block;">
                                </td>
                            @endif
                            <td style="vertical-align:middle;">
                                <div class="company-name">{{ $companyName }}</div>
                                <div class="company-tagline">{{ $companyTagline }}</div>
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- Right: Document Type & Dates --}}
                <td style="width:42%; vertical-align:middle; text-align:right;">
                    <div class="doc-type-badge {{ $invoice->isQuote() ? 'devis' : 'facture' }}">
                        {{ $invoice->isQuote() ? 'DEVIS' : 'FACTURE' }}
                    </div>
                    <div>
                        <span class="doc-number-tag">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-text">
                        Date : <strong>{{ $invoice->issued_at->format('d/m/Y') }}</strong>
                        @if($invoice->isQuote())
                            &nbsp;|&nbsp; Validité : <strong>{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }} (30j)</strong>
                        @elseif($invoice->due_date)
                            &nbsp;|&nbsp; Échéance : <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>
                        @endif
                        @if($invoice->order && $invoice->order->order_number)
                            <br>Réf. Commande : <strong>{{ $invoice->order->order_number }}</strong>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ══════════ CLIENT (FULL WIDTH, LOW HEIGHT) ══════════ --}}
        <div class="client-banner">
            <table class="client-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <span class="client-role">{{ $invoice->isQuote() ? 'DESTINATAIRE / DEVIS POUR' : 'CLIENT / FACTURÉ À' }}</span>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        @if($invoice->ice)
                            <span class="client-ice-badge">ICE : {{ $invoice->ice }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="client-name">{{ $invoice->customer_name }}</div>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        <div class="client-detail">
                            @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                            @if($address)<span>{{ $address }}{{ $invoice->customer_city ? ', ' . $invoice->customer_city : '' }}</span>@endif
                            @if($address && $invoice->customer_phone)&nbsp;&bull;&nbsp;@endif
                            @if($invoice->customer_phone)<span>Tél : {{ $invoice->customer_phone }}</span>@endif
                            @if(($address || $invoice->customer_phone) && $invoice->customer_email)&nbsp;&bull;&nbsp;@endif
                            @if($invoice->customer_email)<span>{{ $invoice->customer_email }}</span>@endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ══════════ ITEMS TABLE ══════════ --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%; text-align:center;">N°</th>
                    <th style="width:47%; text-align:left;">Désignation / Description</th>
                    <th style="width:18%; text-align:right;">P.U. HT</th>
                    <th style="width:10%; text-align:center;">Qté</th>
                    <th style="width:20%; text-align:right;">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $i => $item)
                <tr>
                    <td style="text-align:center; color:#94a3b8; font-size:8.5px;">{{ $i + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->displayName }}</div>
                        @if($item->product_sku)
                        <div class="item-sku">Réf : {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td style="text-align:right;">{{ $item->formatted_unit_price_ht }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $item->quantity }}</td>
                    <td style="text-align:right; font-weight:bold; color:#0f172a;">{{ $item->formatted_total_price_ht }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- ══════════ TOTALS + WORDS (BALANCED BOTTOM) ══════════ --}}
        <table class="totals-outer">
            <tr>
                {{-- Left: Arrêté + Legal/Validity + Notes --}}
                <td style="width:55%; vertical-align:top; padding-right:16px;">
                    <div class="words-card">
                        <div class="words-label">Montant en Lettres</div>
                        <div class="words-text">
                            {{ $invoice->isQuote() ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :' }}<br>
                            <strong style="color:#0f172a; font-size:10px;">
                                {{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC
                            </strong>
                        </div>
                    </div>

                    @if($invoice->isQuote())
                    <div class="validity-banner">
                        Validité de l'offre : <strong>30 jours</strong> à compter de la date d'émission. Ce document ne constitue pas une facture.
                    </div>
                    @else
                    <div class="legal-banner">
                        Facture payable à réception. En cas de retard de paiement, des pénalités seront appliquées conformément au Code Général des Impôts (CGI Maroc).
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-box">
                        <strong>Observations / Notes :</strong> {{ $invoice->notes }}
                    </div>
                    @endif
                </td>

                {{-- Right: Totals Card + Stamp --}}
                <td style="width:45%; vertical-align:top;">
                    <div class="totals-card">
                        <div class="total-row">
                            <span class="total-label">Sous-total HT</span>
                            <span class="total-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-label">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="total-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->shipping_cost > 0)
                        <div class="total-row">
                            <span class="total-label">Frais de livraison</span>
                            <span class="total-val">{{ $invoice->formatted_shipping_cost }}</span>
                        </div>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-label">Remise</span>
                            <span class="total-val">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        <hr class="total-divider">
                        <div class="grand-label">Total TTC</div>
                        <div class="grand-value">{{ $invoice->formatted_total_amount }}</div>
                    </div>

                    {{-- Dedicated space for the auto-added Cachet --}}
                    <div class="stamp-box">
                        @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                            @php
                                $stampPath = public_path('storage/' . setting('company_stamp'));
                                $stampW = round(120 * intval(setting('company_stamp_scale', 100)) / 100);
                            @endphp
                            @if(file_exists($stampPath))
                                <img src="data:image/{{ pathinfo($stampPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($stampPath)) }}"
                                     style="max-width:{{ $stampW }}px; max-height:55px; display:block; margin:0 auto;">
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════ EXECUTIVE MOROCCAN LEGAL FOOTER ══════════ --}}
    <div class="footer">
        <div>
            <strong>{{ $companyName }}</strong>
            @if(setting('company_legal_form', 'SARL')) &bull; Société {{ setting('company_legal_form', 'SARL') }} @endif
            @if(setting('company_capital')) &bull; Capital : {{ setting('company_capital') }} DH @endif
            @if($companyAddress) &bull; {{ $companyAddress }} @endif
            @if($companyPhone) &bull; Tél : {{ $companyPhone }} @endif
            @if($companyEmail) &bull; {{ $companyEmail }} @endif
        </div>
        <div style="margin-top: 2px;">
            ICE : {{ $companyIce }} &bull; IF : {{ $companyIf }} &bull; RC : {{ $companyRc }} &bull; Patente : {{ $companyPatente }}
            @if($companyCnss) &bull; CNSS : {{ $companyCnss }} @endif
            &bull; {{ $invoice->isQuote() ? 'Merci pour votre confiance !' : 'Merci pour votre achat !' }}
        </div>
    </div>
</body>
</html>
