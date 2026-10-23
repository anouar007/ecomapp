<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->getTypeLabel() }} {{ $invoice->display_number ?? $invoice->invoice_number }}</title>
    <style>
        /* ====================================================
           EXECUTIVE MOROCCAN FACTURE / DEVIS — DOMPDF
           Optimized for balanced A4 presentation in all scenarios
           ==================================================== */
        @page {
            margin: 10mm 12mm 14mm 12mm;
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
            background: #0f172a;
            width: 100%;
            margin-bottom: 12px;
        }

        /* ── Page wrapper ── */
        .page {
            padding: 0 4px 50px 4px;
        }

        /* ── Header Table ── */
        .header-table {
            width: 100%;
            margin-bottom: 14px;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 12px;
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
            letter-spacing: 0.5px;
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

        .doc-number-tag {
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
            padding: 3px 8px;
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

        /* ── Parties Table ── */
        .parties-table {
            width: 100%;
            margin-bottom: 16px;
        }

        .party-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 2.5px solid #334155;
            padding: 10px 12px;
            border-radius: 0 0 4px 4px;
            min-height: 100px;
        }

        .party-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 4px;
        }

        .party-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .party-detail {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.4;
        }

        .fiscal-badge {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 2px;
            padding: 1px 5px;
            font-size: 7.5px;
            color: #334155;
            margin-right: 3px;
            margin-top: 3px;
        }

        .fiscal-badge strong {
            color: #0f172a;
        }

        .client-ice-badge {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            margin-top: 4px;
        }

        /* ── Items Table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1.5px solid #cbd5e1;
            color: #475569;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 8px 10px;
        }

        .items-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            color: #1e293b;
            vertical-align: middle;
        }

        .item-row-alt {
            background: #fafafa;
        }

        .item-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 9px;
        }

        .item-sku {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
            font-family: monospace;
        }

        /* ── Summary & Bottom Section ── */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .words-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #334155;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .words-label {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 2px;
        }

        .words-text {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.35;
        }

        .validity-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 6px 10px;
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .legal-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
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
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
        }

        /* Totals Card */
        .totals-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
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
            border-top: 1px solid #cbd5e1;
            margin: 6px 0;
        }

        .grand-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .grand-value {
            font-size: 19px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-align: right;
        }

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
            background: #ffffff;
            border-top: 1.5px solid #e2e8f0;
            color: #64748b;
            font-size: 7.5px;
            text-align: center;
            padding: 6px 34px 2px 34px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    @php
        $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'Winashop'));
        $companyAddress = setting('company_address');
        $companyPhone = setting('company_phone');
        $companyEmail = setting('company_email') ?: config('mail.from.address', 'contact@winashop.com');
        $companyWebsite = setting('company_website');
        $companyTaxId = setting('company_tax_id');
        $companyRegistryId = setting('company_registry_id');
        $companyFiscalId = setting('company_fiscal_id');
        $companyPatente = setting('company_patente');

        $shippingCost = floatval($invoice->shipping_cost ?? ($invoice->order->shipping_cost ?? 0));
        $showStamp = ($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp');
    @endphp

    <div class="top-stripe"></div>
    <div class="page">

        {{-- ══════════ HEADER ══════════ --}}
        <table class="header-table">
            <tr>
                {{-- Left: Logo & Company Name --}}
                <td style="width:58%; vertical-align:middle;">
                    <table style="width:100%;">
                        <tr>
                            @if(setting('app_logo'))
                                @php $logoPath = public_path('storage/' . setting('app_logo')); @endphp
                                @if(file_exists($logoPath))
                                <td style="width:75px; vertical-align:middle;">
                                    <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                                         style="max-width:70px; max-height:50px; display:block;">
                                </td>
                                @endif
                            @endif
                            <td style="vertical-align:middle;">
                                <div class="company-name">{{ $companyName }}</div>
                                @if($companyWebsite)
                                    <div class="company-tagline">{{ $companyWebsite }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

                {{-- Right: Doc Type & Number --}}
                <td style="width:42%; text-align:right; vertical-align:middle;">
                    <div class="doc-type-badge">{{ $invoice->isDeliveryNote() ? 'BON DE LIVRAISON' : ($invoice->isQuote() ? 'DEVIS' : 'FACTURE') }}</div>
                    <div class="doc-number-tag">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</div>
                    <div class="doc-meta-text">
                        Date : <strong>{{ $invoice->issued_at->format('d/m/Y') }}</strong>
                        @if($invoice->isDeliveryNote())
                            @if($invoice->order && $invoice->order->order_number)
                                &nbsp;•&nbsp; Réf. Commande : <strong>{{ $invoice->order->order_number }}</strong>
                            @endif
                        @elseif($invoice->isQuote())
                            &nbsp;•&nbsp; Validité : <strong>{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }}</strong>
                            @if($invoice->order && $invoice->order->order_number)
                                <br>Réf. Commande : <strong>{{ $invoice->order->order_number }}</strong>
                            @endif
                        @elseif($invoice->due_date)
                            &nbsp;•&nbsp; Échéance : <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>
                            @if($invoice->order && $invoice->order->order_number)
                                <br>Réf. Commande : <strong>{{ $invoice->order->order_number }}</strong>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ══════════ PARTIES ══════════ --}}
        <table class="parties-table">
            <tr>
                {{-- Fournisseur --}}
                <td style="width:48%; vertical-align:top;">
                    <div class="party-box">
                        <div class="party-label">ÉMETTEUR / FOURNISSEUR</div>
                        <div class="party-name">{{ $companyName }}</div>
                        <div class="party-detail">
                            <div>{{ $companyAddress ?: 'Maroc' }}</div>
                            <div style="margin-top:2px;">
                                @if($companyPhone)<span>Tél : {{ $companyPhone }}</span>@endif
                                @if($companyPhone && $companyEmail)&nbsp;•&nbsp;@endif
                                @if($companyEmail)<span>{{ $companyEmail }}</span>@endif
                            </div>
                        </div>
                        @if($companyTaxId || $companyRegistryId || $companyFiscalId || $companyPatente)
                        <div style="margin-top:6px; padding-top:6px; border-top:1px solid #e2e8f0;">
                            @if($companyTaxId)<span class="fiscal-badge"><strong>ICE:</strong> {{ $companyTaxId }}</span>@endif
                            @if($companyRegistryId)<span class="fiscal-badge"><strong>RC:</strong> {{ $companyRegistryId }}</span>@endif
                            @if($companyFiscalId)<span class="fiscal-badge"><strong>IF:</strong> {{ $companyFiscalId }}</span>@endif
                            @if($companyPatente)<span class="fiscal-badge"><strong>Patente:</strong> {{ $companyPatente }}</span>@endif
                        </div>
                        @endif
                    </div>
                </td>

                <td style="width:4%;"></td>

                {{-- Client / Destinataire --}}
                <td style="width:48%; vertical-align:top;">
                    <div class="party-box">
                        <div class="party-label">{{ $invoice->getBillToLabel() }}</div>
                        @if($invoice->customer_company)
                            <div class="party-name" style="font-size: 13px; font-weight: bold; color: #0f172a;">{{ $invoice->customer_company }}</div>
                            <div style="font-size: 9px; color: #475569; margin-bottom: 2px;">Attn : {{ $invoice->customer_name }}</div>
                        @else
                            <div class="party-name">{{ $invoice->customer_name }}</div>
                        @endif
                        <div class="party-detail">
                            @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                            @if($address)<div>{{ $address }}</div>@endif
                            <div style="margin-top:2px;">
                                @if($invoice->customer_phone)<span>Tél : {{ $invoice->customer_phone }}</span>@endif
                                @if($invoice->customer_phone && $invoice->customer_email)&nbsp;•&nbsp;@endif
                                @if($invoice->customer_email)<span>{{ $invoice->customer_email }}</span>@endif
                            </div>
                        </div>
                        @if($invoice->ice || $invoice->customer_rc || $invoice->customer_if || $invoice->customer_patente)
                        <div style="margin-top:6px; padding-top:6px; border-top:1px solid #e2e8f0;">
                            @if($invoice->ice)<span class="client-ice-badge" style="margin-right:4px;">ICE : {{ $invoice->ice }}</span>@endif
                            @if($invoice->customer_rc)<span class="client-ice-badge" style="margin-right:4px;">RC : {{ $invoice->customer_rc }}</span>@endif
                            @if($invoice->customer_if)<span class="client-ice-badge" style="margin-right:4px;">IF : {{ $invoice->customer_if }}</span>@endif
                            @if($invoice->customer_patente)<span class="client-ice-badge">Patente : {{ $invoice->customer_patente }}</span>@endif
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- ══════════ ITEMS TABLE ══════════ --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%; text-align:center;">N°</th>
                    <th style="width:52%; text-align:left;">Désignation / Description</th>
                    <th style="width:15%; text-align:right;">P.U. HT</th>
                    <th style="width:10%; text-align:center;">Qté</th>
                    <th style="width:18%; text-align:right;">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $i => $item)
                <tr class="{{ $i % 2 === 1 ? 'item-row-alt' : '' }}">
                    <td style="text-align:center; color:#94a3b8; font-size:8px;">{{ $i + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
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

        {{-- ══════════ SUMMARY & BOTTOM ROW ══════════ --}}
        <table class="bottom-table">
            <tr>
                {{-- Left: Arrêté + Notes / Mentions --}}
                <td style="width:55%; vertical-align:top; padding-right:16px;">
                    <div class="words-card">
                        <div class="words-label">Montant en Toutes Lettres</div>
                        <div class="words-text">
                            {{ $invoice->isDeliveryNote() ? 'Arrêté le présent bon de livraison à la somme de :' : ($invoice->isQuote() ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :') }}<br>
                            <strong style="color:#0f172a; font-size:10px;">
                                {{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC
                            </strong>
                        </div>
                    </div>

                    @if($invoice->isDeliveryNote())
                    <div class="validity-banner" style="background:#f8fafc; border-left:3px solid #334155;">
                        <strong>Bon pour réception et décharge client :</strong><br>
                        « Marchandises reçues conformes en quantité et en qualité, en parfait état. »<br>
                        <div style="margin-top:6px; font-size:8px; color:#475569;">
                            Date de réception : _____ / _____ / 20___ &nbsp;&nbsp;•&nbsp;&nbsp; Nom & Signature / Cachet :
                        </div>
                    </div>
                    @elseif($invoice->isQuote())
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
                            <span class="total-label">Total HT</span>
                            <span class="total-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-label">Total {{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="total-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-label">Remise</span>
                            <span class="total-val" style="color:#059669;">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        @if($shippingCost > 0)
                        <div class="total-row">
                            <span class="total-label">Frais de livraison</span>
                            <span class="total-val">+ {{ currency($shippingCost) }}</span>
                        </div>
                        @endif
                        <hr class="total-divider">
                        <div class="grand-label">Total TTC</div>
                        <div class="grand-value">{{ $invoice->formatted_total_amount }}</div>
                    </div>

                    @if($showStamp)
                        @php
                            $stampPath = public_path('storage/' . setting('company_stamp'));
                            $stampW = round(120 * intval(setting('company_stamp_scale', 100)) / 100);
                        @endphp
                        @if(file_exists($stampPath))
                        <div class="stamp-box">
                            <img src="data:image/{{ pathinfo($stampPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($stampPath)) }}"
                                 style="max-width:{{ $stampW }}px; max-height:80px; display:block; margin:0 auto;">
                        </div>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════ CORPORATE FOOTER ══════════ --}}
    <div class="footer">
        <div>{{ $invoice->isDeliveryNote() ? 'Marchandise livrée sous réserve de vérification. Merci de votre confiance !' : ($invoice->isQuote() ? 'Merci de votre confiance ! Pour toute question, nous restons à votre entière disposition.' : 'Merci pour votre achat et votre confiance !') }}</div>
        <div style="margin-top:2px;">
            <strong>{{ $companyName }}</strong>
            @if($companyAddress) • {{ $companyAddress }}@endif
            @if($companyPhone) • Tél : {{ $companyPhone }}@endif
            @if($companyEmail) • Email : {{ $companyEmail }}@endif
            @if($companyTaxId) • ICE : {{ $companyTaxId }}@endif
            @if($companyRegistryId) • RC : {{ $companyRegistryId }}@endif
            @if($companyFiscalId) • IF : {{ $companyFiscalId }}@endif
            @if($companyPatente) • Patente : {{ $companyPatente }}@endif
        </div>
    </div>
</body>
</html>
