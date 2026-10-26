<!DOCTYPE html>
<html lang="fr" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bon de Livraison #{{ $invoice->delivery_note_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 24px;
            font-size: 12px;
            line-height: 1.4;
            display: flex;
            flex-direction: column;
            align-items: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .dn-card {
            background: #ffffff;
            width: 100%;
            max-width: 900px;
            min-height: 1060px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dn-accent-bar {
            height: 4px;
            background: #0284c7;
            width: 100%;
            flex-shrink: 0;
        }

        .dn-body {
            padding: 28px 36px 16px 36px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .dn-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            border-bottom: 1.5px solid #e2e8f0;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-logo {
            max-height: 54px;
            max-width: 140px;
            object-fit: contain;
        }

        .brand-monogram {
            width: 48px;
            height: 48px;
            background: #0284c7;
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dn-brand-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .dn-brand-tagline {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }

        .dn-badge-box {
            text-align: right;
        }

        .dn-badge {
            background: #0284c7;
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.8px;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .dn-num {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        /* ── Client Banner (Full Width, Low Height) ── */
        .dn-client-banner {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3.5px solid #0284c7;
            border-radius: 4px;
            padding: 9px 14px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .dn-client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .dn-client-role {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0284c7;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .dn-client-ice {
            display: inline-block;
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 10.5px;
            font-weight: 700;
        }

        .dn-client-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .dn-client-name {
            font-size: 14.5px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.2px;
        }

        .dn-client-details {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 11px;
            color: #475569;
            flex-wrap: wrap;
        }

        /* Items Section */
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
            background: #0284c7;
            color: #ffffff;
        }

        .items-table thead th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #ffffff;
            border: 1px solid #0284c7;
            text-align: left;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .items-table tbody td {
            padding: 12px 14px;
            font-size: 12px;
            vertical-align: middle;
        }

        /* Summary & Totals */
        .summary-row {
            display: flex;
            gap: 16px;
            align-items: stretch;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .summary-left {
            flex: 1.3;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .legal-notice {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            padding: 9px 12px;
            border-radius: 0 4px 4px 0;
            font-size: 10.5px;
            color: #475569;
            line-height: 1.4;
        }

        .summary-right {
            flex: 1;
            max-width: 370px;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .tot-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11.5px;
        }

        .grand-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding-top: 5px;
            border-top: 1.5px solid #cbd5e1;
            margin-top: 5px;
            font-weight: 800;
            font-size: 17px;
            color: #0f172a;
        }

        /* Signatures */
        .signatures-grid {
            display: flex;
            gap: 16px;
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .sig-col {
            flex: 1;
            border: 1.5px dashed #cbd5e1;
            background: #fbfcfe;
            border-radius: 6px;
            padding: 10px 14px;
            min-height: 125px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sig-header {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }

        .sig-center {
            text-align: center;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sig-stamp-img {
            max-width: 140px;
            max-height: 75px;
            object-fit: contain;
        }

        .sig-foot {
            font-size: 9.5px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 4px;
        }

        /* Footer */
        .dn-footer {
            border-top: 1.5px solid #e2e8f0;
            padding: 12px 36px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            line-height: 1.5;
            margin-top: auto;
            flex-shrink: 0;
        }

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

            .dn-card {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                min-height: calc(297mm - 16mm) !important;
                border-radius: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                page-break-inside: avoid !important;
            }

            .dn-body {
                padding: 10px 14px 6px 14px !important;
                flex: 1 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }

            .items-section {
                flex: 1 !important;
            }

            .dn-footer {
                margin-top: auto !important;
                padding: 8px 14px !important;
                border-top: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</head>

@php
    $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'WINA SHOP'));
    $companyTagline = setting('company_tagline') ?: (setting('company_website') ?: 'Matériel Audiovisuel Professionnel — Maroc');
    $companyLogo = setting('company_logo') ?: setting('app_logo');
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
    <div class="dn-card">
        <div class="dn-accent-bar"></div>

        <div class="dn-body">
            <!-- Header -->
            <div class="dn-header">
                <div class="header-brand">
                    @if(app_logo_url())
                        <img src="{{ app_logo_url() }}" class="header-logo" alt="Logo">
                    @else
                        <div class="brand-monogram">
                            {{ strtoupper(substr($companyName, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="dn-brand-title">{{ $companyName }}</div>
                        <div class="dn-brand-tagline">{{ $companyTagline }}</div>
                    </div>
                </div>

                <div class="dn-badge-box">
                    <div class="dn-badge">BON DE LIVRAISON</div>
                    <div class="dn-num">N° {{ $invoice->delivery_note_number }}</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                        Date : {{ $invoice->issued_at->format('d/m/Y') }}<br>
                        @if($invoice->order && $invoice->order->order_number)
                            Réf. Commande : {{ $invoice->order->order_number }}<br>
                        @endif
                        Réf. Facture : {{ $invoice->display_number ?? $invoice->invoice_number }}
                    </div>
                </div>
            </div>

            <!-- Client Banner (Full Width, Low Height) -->
            <div class="dn-client-banner">
                <div class="dn-client-header">
                    <span class="dn-client-role">
                        <i class="fas fa-map-marker-alt me-1"></i>Destinataire / Lieu de livraison
                    </span>
                    @if($invoice->ice)
                        <span class="dn-client-ice">ICE Client : {{ $invoice->ice }}</span>
                    @endif
                </div>
                <div class="dn-client-body">
                    <div class="dn-client-name">{{ $invoice->customer_name }}</div>
                    <div class="dn-client-details">
                        @php $shippingAddress = $invoice->customer_address ?: ($invoice->order->shipping_address ?? 'Livraison au comptoir'); @endphp
                        <span><i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $shippingAddress }}</span>
                        @if($invoice->customer_city || ($invoice->order && $invoice->order->shipping_city))
                            &bull; <span><i class="fas fa-city text-muted me-1"></i>{{ $invoice->customer_city ?: $invoice->order->shipping_city }}</span>
                        @endif
                        @if($invoice->customer_phone)
                            &bull; <span><i class="fas fa-phone text-muted me-1"></i>{{ $invoice->customer_phone }}</span>
                        @endif
                        @if($invoice->customer_email)
                            &bull; <span><i class="fas fa-envelope text-muted me-1"></i>{{ $invoice->customer_email }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="items-section">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">N°</th>
                            <th style="width: 15%;">Réf / SKU</th>
                            <th style="width: 38%;">Désignation des Articles</th>
                            <th style="width: 14%; text-align: right;">P.U. HT</th>
                            <th style="width: 10%; text-align: center;">Qté</th>
                            <th style="width: 18%; text-align: right;">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td style="text-align: center; color: #94a3b8; font-weight: 600;">{{ $i + 1 }}</td>
                            <td style="font-family: monospace; font-size: 11px;">
                                {{ $item->product_sku ?: 'ART-' . str_pad($item->product_id ?? $item->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <strong>{{ $item->displayName }}</strong>
                            </td>
                            <td style="text-align: right; color: #334155; font-weight: 500;">{{ $item->formatted_unit_price_ht }}</td>
                            <td style="text-align: center; font-weight: 800; color: #0284c7;">{{ $item->quantity }}</td>
                            <td style="text-align: right; font-weight: 800; color: #0f172a;">{{ $item->formatted_total_price_ht }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary & Totals -->
            <div class="summary-row">
                <div class="summary-left">
                    <div class="legal-notice">
                        <strong><i class="fas fa-shield-alt me-1"></i>Conformité de livraison (Loi 16-99 & Code de Commerce Marocain) :</strong><br>
                        Le présent bon de livraison atteste de la remise effective et de la réception conforme des marchandises susmentionnées. 
                        Le client est tenu de vérifier l'état des colis et la conformité des produits dès la livraison. 
                        Toute réserve ou avarie doit être constatée sur ce bon et confirmée par écrit dans un délai de <strong>48 heures</strong> (Article 347 du Code de Commerce Marocain).
                    </div>
                </div>

                <div class="summary-right">
                    <div class="tot-row">
                        <span style="color: #64748b;">Sous-total HT :</span>
                        <span style="font-weight: 700;">{{ $invoice->formatted_subtotal }}</span>
                    </div>
                    <div class="tot-row">
                        <span style="color: #64748b;">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }}) :</span>
                        <span style="font-weight: 700;">{{ $invoice->formatted_tax_amount }}</span>
                    </div>
                    @if($invoice->shipping_cost > 0)
                    <div class="tot-row">
                        <span style="color: #64748b;">Frais de livraison :</span>
                        <span style="font-weight: 700;">{{ $invoice->formatted_shipping_cost }}</span>
                    </div>
                    @endif
                    <div class="grand-row">
                        <span>TOTAL TTC :</span>
                        <span>{{ $invoice->formatted_total_amount }}</span>
                    </div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures-grid">
                <div class="sig-col" style="min-height: 165px;">
                    <div class="sig-header">
                        <span>Pour l'Expéditeur / Transporteur</span>
                        <span style="color: #94a3b8; font-size: 9px;">Délivré par</span>
                    </div>
                    <div class="sig-center" style="min-height: 95px;">
                        @if(($withStamp ?? true) && setting('company_stamp'))
                            <img src="{{ asset('storage/' . setting('company_stamp')) }}" class="sig-stamp-img" alt="Cachet Société">
                        @else
                            <span style="font-size: 10px; color: #94a3b8; font-style: italic;">Cachet & Signature Société</span>
                        @endif
                    </div>
                    <div class="sig-foot">Date d'expédition : {{ $invoice->issued_at->format('d/m/Y') }}</div>
                </div>

                <div class="sig-col" style="min-height: 165px;">
                    <div class="sig-header">
                        <span>Pour le Réceptionnaire / Client</span>
                        <span style="color: #94a3b8; font-size: 9px;">Décharge</span>
                    </div>
                    <div style="font-size: 10px; color: #475569; padding-top: 3px;">
                        Mention manuscrite : <strong>« Bon pour réception conforme »</strong>
                    </div>
                    <div style="flex: 1; min-height: 70px; margin: 4px 0; border: 1px dashed #cbd5e1; border-radius: 4px; background: #fafafa; display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 9.5px; color: #94a3b8; font-style: italic;">Zone libre : Cachet, Signature, Réserves</span>
                    </div>
                    <div class="sig-foot" style="display: flex; justify-content: space-between;">
                        <span>Date & Heure : ..............................</span>
                        <span>Signature & Cachet</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="dn-footer">
            <div>
                <strong>{{ $companyName }}</strong> &bull; {{ $companyAddress }}
                @if($companyPhone) &bull; Tél : {{ $companyPhone }} @endif
                @if($companyEmail) &bull; {{ $companyEmail }} @endif
            </div>
            <div style="margin-top: 2px;">
                ICE : {{ $companyIce }} &bull; IF : {{ $companyIf }} &bull; RC : {{ $companyRc }} &bull; Patente : {{ $companyPatente }}
            </div>
        </div>
    </div>

    <script>
        @if(!request()->has('preview') && !request()->has('no_print'))
        window.onload = function () {
            setTimeout(function () {
                window.print();
            }, 500);
        };
        @endif
    </script>
</body>
</html>
