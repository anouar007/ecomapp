<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bon de Livraison #{{ $invoice->delivery_note_number }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 12mm 14mm 12mm 14mm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        /* Top Color Bar */
        .top-accent {
            height: 4px;
            background-color: #0284c7;
            margin-bottom: 12px;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #cbd5e1;
        }

        .brand-name {
            font-size: 17px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .brand-sub {
            font-size: 9px;
            color: #64748b;
        }

        .doc-title-badge {
            background-color: #0284c7;
            color: #ffffff;
            font-weight: bold;
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .doc-number {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Client Banner (Full Width, Low Height) */
        .client-banner {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .client-table {
            width: 100%;
            border-collapse: collapse;
        }

        .client-role {
            font-size: 7.5px;
            font-weight: bold;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .client-ice-badge {
            display: inline-block;
            background: #ffffff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
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

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border-bottom: 1.5px solid #cbd5e1;
            text-align: left;
        }

        .items-table td {
            padding: 5px 8px;
            font-size: 8.5px;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .item-name {
            font-weight: bold;
            color: #0f172a;
        }

        .item-sku {
            font-size: 7.5px;
            color: #64748b;
        }

        /* Summary & Totals Table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .legal-notice-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 2.5px solid #0284c7;
            padding: 7px 10px;
            font-size: 8px;
            color: #475569;
            line-height: 1.35;
        }

        .totals-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
        }

        .totals-row-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .totals-row-table td {
            padding: 2px 0;
        }

        .grand-total {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px !important;
            margin-top: 4px;
        }

        /* Signatures Table */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .sig-box {
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            height: 110px;
            vertical-align: top;
            background: #ffffff;
        }

        .sig-header {
            font-size: 8.5px;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 3px;
            margin-bottom: 4px;
        }

        .sig-footer {
            font-size: 7.5px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = app_logo_path();
        $logoBase64 = null;
        if ($logoPath && file_exists($logoPath)) {
            $logoExt = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $logoExt . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $stampPath = setting('company_stamp') ? public_path('storage/' . setting('company_stamp')) : null;
        $stampBase64 = null;
        if ($stampPath && file_exists($stampPath)) {
            $stampExt = pathinfo($stampPath, PATHINFO_EXTENSION);
            $stampBase64 = 'data:image/' . $stampExt . ';base64,' . base64_encode(file_get_contents($stampPath));
        }
    @endphp

    <div class="top-accent"></div>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <!-- Brand Left -->
            <td style="width: 55%; vertical-align: middle;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 48px; max-width: 140px; margin-bottom: 4px;">
                @endif
                <div class="brand-name">{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}</div>
                <div class="brand-sub">{{ setting('company_tagline', 'Matériel Audiovisuel Professionnel — Maroc') }}</div>
            </td>

            <!-- Doc Title Right -->
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div class="doc-title-badge">BON DE LIVRAISON</div>
                <div class="doc-number">N° {{ $invoice->delivery_note_number }}</div>
                <div style="font-size: 8.5px; color: #475569; margin-top: 3px;">
                    <strong>Date :</strong> {{ $invoice->issued_at->format('d/m/Y') }}<br>
                    @if($invoice->order && $invoice->order->order_number)
                        <strong>Réf. Cde :</strong> {{ $invoice->order->order_number }}<br>
                    @endif
                    <strong>Réf. Facture :</strong> {{ $invoice->display_number ?? $invoice->invoice_number }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Client Banner (Full Width, Low Height) -->
    <div class="client-banner">
        <table class="client-table">
            <tr>
                <td style="vertical-align: middle;">
                    <span class="client-role">DESTINATAIRE / LIEU DE LIVRAISON</span>
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    @if($invoice->ice)
                        <span class="client-ice-badge">ICE Client : {{ $invoice->ice }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle;">
                    <div class="client-name">{{ $invoice->customer_name }}</div>
                </td>
                <td style="vertical-align: middle; text-align: right;">
                    <div class="client-detail">
                        @php $shippingAddress = $invoice->customer_address ?: ($invoice->order->shipping_address ?? 'Livraison au comptoir'); @endphp
                        <span>{{ $shippingAddress }}</span>
                        @if($invoice->order && $invoice->order->shipping_city)
                            &nbsp;&bull;&nbsp;<span>{{ $invoice->order->shipping_city }}</span>
                        @endif
                        @if($invoice->customer_phone)
                            &nbsp;&bull;&nbsp;<span>Tél : {{ $invoice->customer_phone }}</span>
                        @endif
                        @if($invoice->customer_email)
                            &nbsp;&bull;&nbsp;<span>{{ $invoice->customer_email }}</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">N°</th>
                <th style="width: 16%;">Réf / SKU</th>
                <th style="width: 40%;">Désignation des Articles</th>
                <th style="width: 14%; text-align: right;">P.U. HT</th>
                <th style="width: 8%; text-align: center;">Qté</th>
                <th style="width: 18%; text-align: right;">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td style="text-align: center; color: #94a3b8;">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-size: 8px; color: #475569;">
                    {{ $item->product_sku ?: 'ART-' . str_pad($item->product_id ?? $item->id, 4, '0', STR_PAD_LEFT) }}
                </td>
                <td>
                    <div class="item-name">{{ $item->displayName }}</div>
                </td>
                <td style="text-align: right;">{{ $item->formatted_unit_price_ht }}</td>
                <td style="text-align: center; font-weight: bold; color: #0284c7;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-weight: bold;">{{ $item->formatted_total_price_ht }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary & Totals -->
    <table class="summary-table">
        <tr>
            <!-- Left: Moroccan Legal Notice -->
            <td style="width: 58%; vertical-align: top; padding-right: 12px;">
                <div class="legal-notice-box">
                    <strong>Conformité & Décharge (Loi 16-99 & Code de Commerce Marocain) :</strong><br>
                    Le présent bon atteste de la remise effective et conforme des marchandises désignées ci-dessus. 
                    Le client est tenu de vérifier l'état des colis dès réception. Toute anomalie ou avarie doit être constatée sur ce bon et confirmée par écrit dans un délai de <strong>48 heures</strong> (Art. 347 du Code de Commerce Marocain).
                </div>
                @if($invoice->notes)
                <div style="margin-top: 4px; font-size: 7.5px; color: #475569;">
                    <strong>Observations :</strong> {{ $invoice->notes }}
                </div>
                @endif
            </td>

            <!-- Right: Totals Card -->
            <td style="width: 42%; vertical-align: top;">
                <div class="totals-card">
                    <table class="totals-row-table">
                        <tr>
                            <td style="color: #64748b;">Sous-total HT :</td>
                            <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_subtotal }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b;">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }}) :</td>
                            <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_tax_amount }}</td>
                        </tr>
                        @if($invoice->shipping_cost > 0)
                        <tr>
                            <td style="color: #64748b;">Frais de livraison :</td>
                            <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_shipping_cost }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="grand-total">Total TTC :</td>
                            <td class="grand-total" style="text-align: right; color: #0284c7;">{{ $invoice->formatted_total_amount }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Dual Signatures (Cadre d'émargement obligatoire) -->
    <table class="signatures-table">
        <tr>
            <!-- Expéditeur -->
            <td style="width: 49%; vertical-align: top;" class="sig-box">
                <div class="sig-header">Pour l'Expéditeur / Délivré par :</div>
                <div style="text-align: center; height: 50px;">
                    @if(($withStamp ?? true) && $stampBase64)
                        <img src="{{ $stampBase64 }}" style="max-height: 48px; max-width: 120px;">
                    @else
                        <div style="font-size: 7.5px; color: #94a3b8; padding-top: 15px;">Cachet & Signature Société</div>
                    @endif
                </div>
                <div class="sig-footer">Date : {{ $invoice->issued_at->format('d/m/Y') }}</div>
            </td>

            <td style="width: 2%;"></td>

            <!-- Destinataire -->
            <td style="width: 49%; vertical-align: top;" class="sig-box">
                <div class="sig-header">Pour le Réceptionnaire / Décharge :</div>
                <div style="font-size: 7.5px; color: #475569; padding-top: 2px;">
                    Mention : <strong>« Reçu conforme et en parfait état »</strong>
                </div>
                <div style="height: 52px; border: 1px dashed #cbd5e1; border-radius: 3px; background: #fafafa; margin: 5px 0; text-align: center; line-height: 52px; font-size: 7.5px; color: #94a3b8; font-style: italic;">
                    Zone libre pour Cachet, Signature, Date & Réserves
                </div>
                <div class="sig-footer">
                    Date & Heure : ......................... | Signature :
                </div>
            </td>
        </tr>
    </table>

    <!-- Official Moroccan Legal Footer -->
    <div class="page-footer">
        <div>
            <strong>{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}</strong> &bull; 
            {{ setting('company_address', 'Casablanca, Maroc') }}
            @if(setting('company_phone')) &bull; Tél : {{ setting('company_phone') }} @endif
            @if(setting('company_email')) &bull; {{ setting('company_email') }} @endif
        </div>
        <div style="margin-top: 1px;">
            ICE : {{ setting('company_tax_id', setting('company_ice', '002938475000089')) }} &bull; 
            IF : {{ setting('company_fiscal_id', setting('company_if', '39482710')) }} &bull; 
            RC : {{ setting('company_registry_id', setting('company_rc', '49821')) }} &bull; 
            Patente : {{ setting('company_patente', '37920194') }}
        </div>
    </div>
</body>
</html>
