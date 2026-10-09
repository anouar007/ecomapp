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
           PROFESSIONAL MOROCCAN FACTURE / DEVIS — PRINT
           Brand: Deep Slate #0f172a | Moroccan Ruby #dc2626
           Space-optimized, executive layout (DGI compliant)
           ==================================================== */

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
            line-height: 1.4;
            margin: 0;
            padding: 24px;
            background: #e2e8f0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Page card ── */
        .invoice-card {
            background: #ffffff;
            max-width: 920px;
            margin: 0 auto;
            border-radius: 4px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            color: #1e293b;
        }

        /* ── Top accent stripe ── */
        .accent-bar {
            height: 3px;
            background: #334155;
        }

        /* ── Document container padding ── */
        .doc-body {
            padding: 28px 36px;
        }

        /* ── Header ── */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 18px;
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
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1;
            color: #0f172a;
        }

        .doc-type-label.facture { color: #0f172a; }
        .doc-type-label.devis   { color: #0f172a; }

        .doc-number-badge {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
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
            border-top: 2px solid #475569;
            padding: 14px 16px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .party-box.client {
            border-top-color: #475569;
        }

        .party-role {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #64748b;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .party-box.client .party-role {
            color: #64748b;
        }

        .party-name {
            font-size: 14.5px;
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 8px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #334155;
        }

        .fiscal-grid .f-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 2px 7px;
            font-size: 10.5px;
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
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
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
            border-bottom: 1.5px solid #cbd5e1;
        }

        .items-table thead tr {
            background: #f8fafc;
        }

        .items-table thead th {
            padding: 10px 12px;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .items-table tbody td {
            padding: 9px 12px;
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

        /* Words / Arrêté */
        .words-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #64748b;
            padding: 10px 14px;
            border-radius: 0 4px 4px 0;
        }

        .words-label {
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .words-text {
            font-size: 11.5px;
            color: #475569;
            line-height: 1.4;
        }

        .words-highlight {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
            display: block;
        }

        /* Banners */
        .validity-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }

        .legal-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 11px;
            color: #64748b;
            line-height: 1.4;
        }

        .notes-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #94a3b8;
            padding: 8px 12px;
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
            font-size: 11px;
            color: #475569;
            margin: 0;
            line-height: 1.4;
        }

        /* Totals card */
        .totals-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
            color: #0f172a;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
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
            border-top: 1.5px solid #e2e8f0;
            margin: 8px 0;
        }

        .grand-total-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .grand-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
        }

        .grand-value {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
        }

        /* Stamp card under totals */
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
        .footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 14px 36px;
            text-align: center;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* ── Print Media Optimization ── */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
                border: none;
            }
            .doc-body {
                padding: 20px 28px;
            }
            .footer {
                padding: 10px 28px;
            }
        }

        [dir="rtl"] .items-table th,
        [dir="rtl"] .items-table td { text-align: right; }
        [dir="rtl"] .doc-badge-block { align-items: flex-start; text-align: left; }
        [dir="rtl"] .doc-meta-grid { justify-content: flex-start; }
        [dir="rtl"] .party-box { border-top: 2px solid #475569; }
        [dir="rtl"] .party-box.client { border-top-color: #475569; }
    </style>
</head>
<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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
                            <div class="meta-item"><span class="meta-k">Échéance :</span> <span class="meta-v">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
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

                    {{-- Cachet Société placed right under Totals for perfect visual balance --}}
                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
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

        {{-- ══════════ FOOTER ══════════ --}}
        <div class="footer">
            <i class="fas fa-handshake" style="margin-right:6px; font-size:11px; color:#94a3b8;"></i>
            {{ $invoice->isQuote() ? 'Merci de votre confiance !' : 'Merci pour votre achat !' }}
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
