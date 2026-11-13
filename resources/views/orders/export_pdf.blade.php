<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Commandes</title>
    <style>
        @page {
            margin: 14mm 12mm 16mm 12mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-banner {
            background: #0f172a;
            color: #ffffff;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 3px 0;
            color: #ffffff;
        }
        .header-sub {
            font-size: 9px;
            color: #cbd5e1;
            margin: 0;
        }
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin: 0 -6px 14px -6px;
        }
        .kpi-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 3px solid #dc2626;
            border-radius: 6px;
            padding: 8px 10px;
            width: 25%;
            vertical-align: top;
        }
        .kpi-box.green { border-top-color: #10b981; }
        .kpi-box.blue { border-top-color: #3b82f6; }
        .kpi-box.amber { border-top-color: #f59e0b; }
        .kpi-label {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .kpi-val {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-note {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .filter-bar {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5px;
            color: #334155;
        }
        .order-card {
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            margin-bottom: 12px;
            page-break-inside: avoid;
            overflow: hidden;
        }
        .order-head {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 7px 10px;
        }
        .order-head-table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-num {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-gray { background: #e2e8f0; color: #334155; }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
        }
        .info-cell {
            width: 50%;
            padding: 7px 10px;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-cell.left {
            border-right: 1px solid #f1f5f9;
        }
        .section-lbl {
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 7.5px;
            text-transform: uppercase;
            text-align: left;
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .order-foot {
            background: #f8fafc;
            padding: 6px 10px;
            font-size: 8.5px;
            text-align: right;
            color: #334155;
        }
        .order-foot strong {
            color: #0f172a;
        }
        .empty-box {
            text-align: center;
            padding: 30px;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header-banner">
        <table class="header-table">
            <tr>
                <td>
                    <div class="header-title">Rapport Détaillé des Commandes</div>
                    <p class="header-sub">Commandes, coordonnées clients, dépôts et détail des articles</p>
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <div style="font-size: 8.5px; color: #e2e8f0;">Généré le {{ $generatedAt->format('d/m/Y à H:i') }}</div>
                    <div style="font-size: 8px; color: #94a3b8;">{{ config('app.name') }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(!empty($filters))
        <div class="filter-bar">
            <strong>Filtres appliqués :</strong>
            @foreach($filters as $fKey => $fVal)
                <span style="margin-right: 10px;">{{ $fKey }}: <strong>{{ $fVal }}</strong></span>
            @endforeach
        </div>
    @endif

    <table class="kpi-table">
        <tr>
            <td class="kpi-box blue">
                <div class="kpi-label">Commandes</div>
                <div class="kpi-val">{{ number_format($summary['total_orders'], 0, ',', ' ') }}</div>
                <div class="kpi-note">{{ $summary['delivered_count'] }} livrée(s)</div>
            </td>
            <td class="kpi-box">
                <div class="kpi-label">Chiffre d'Affaires</div>
                <div class="kpi-val">{{ number_format($summary['valid_revenue'], 2, ',', ' ') }} DH</div>
                <div class="kpi-note">Hors commandes annulées</div>
            </td>
            <td class="kpi-box green">
                <div class="kpi-label">Montant Payé</div>
                <div class="kpi-val">{{ number_format($summary['paid_revenue'], 2, ',', ' ') }} DH</div>
                <div class="kpi-note">En attente : {{ number_format($summary['pending_revenue'], 2, ',', ' ') }} DH</div>
            </td>
            <td class="kpi-box amber">
                <div class="kpi-label">Articles Vendus</div>
                <div class="kpi-val">{{ number_format($summary['total_units'], 0, ',', ' ') }} pcs</div>
                <div class="kpi-note">Total unités commandées</div>
            </td>
        </tr>
    </table>

    @forelse($orders as $order)
        @php
            $statusBadge = match($order->status) {
                'delivered', 'completed' => 'badge-success',
                'processing', 'shipped' => 'badge-info',
                'cancelled', 'refunded' => 'badge-danger',
                default => 'badge-warning',
            };
            $payBadge = $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning';
            $clientName = $order->customer_name ?? ($order->customer->name ?? ($order->user->name ?? 'Client'));
            $clientEmail = $order->customer_email ?? ($order->customer->email ?? ($order->user->email ?? '—'));
            $clientPhone = $order->customer_phone ?? ($order->customer->phone ?? '—');
            $storeNames = $order->items->pluck('store.name')->filter()->unique()->implode(', ');
        @endphp

        <div class="order-card">
            <div class="order-head">
                <table class="order-head-table">
                    <tr>
                        <td>
                            <span class="order-num">#{{ $order->order_number }}</span>
                            <span style="color: #64748b; font-size: 8px; margin-left: 6px;">
                                {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="badge {{ $statusBadge }}">{{ ucfirst($order->status) }}</span>
                            <span class="badge {{ $payBadge }}" style="margin-left: 3px;">
                                {{ $order->payment_status === 'paid' ? 'Payé' : 'Paiement en attente' }}
                            </span>
                            @if($order->payment_method)
                                <span class="badge badge-gray" style="margin-left: 3px;">{{ strtoupper($order->payment_method) }}</span>
                            @endif
                            <span style="font-size: 11px; font-weight: bold; color: #dc2626; margin-left: 8px;">
                                {{ number_format((float) $order->total, 2, ',', ' ') }} DH
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <table class="info-grid">
                <tr>
                    <td class="info-cell left">
                        <div class="section-lbl">Client</div>
                        <div><strong>{{ $clientName }}</strong></div>
                        <div>Tél : {{ $clientPhone }} &bull; Email : {{ $clientEmail }}</div>
                        @if($order->ice)
                            <div>ICE : {{ $order->ice }}</div>
                        @endif
                    </td>
                    <td class="info-cell">
                        <div class="section-lbl">Livraison &amp; Dépôt</div>
                        <div>{{ $order->shipping_address ?: '—' }}</div>
                        <div>
                            {{ $order->shipping_city ?: '—' }}
                            @if($order->shipping_zip) ({{ $order->shipping_zip }}) @endif
                            @if($order->shipping_country) - {{ $order->shipping_country }} @endif
                        </div>
                        <div>
                            Dépôt(s) : <strong>{{ $storeNames !== '' ? $storeNames : 'Non assigné' }}</strong>
                            @if($order->coupon_code) &bull; Promo : <strong>{{ $order->coupon_code }}</strong> @endif
                        </div>
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Article</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 17%;">Dépôt</th>
                        <th class="text-center" style="width: 8%;">Qté</th>
                        <th class="text-right" style="width: 10%;">Prix U.</th>
                        <th class="text-right" style="width: 10%;">Sous-total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        @php
                            $lineSub = $item->subtotal !== null ? (float) $item->subtotal : ((float) $item->price * (int) $item->quantity);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $item->product_name ?? ($item->product->name ?? 'Produit') }}</strong>
                                @if($item->variant_name)
                                    <span style="color: #64748b;">({{ $item->variant_name }})</span>
                                @endif
                            </td>
                            <td style="color: #64748b;">{{ $item->product_sku ?? ($item->product->sku ?? '—') }}</td>
                            <td>{{ $item->store->name ?? 'Non assigné' }}</td>
                            <td class="text-center"><strong>{{ (int) $item->quantity }}</strong></td>
                            <td class="text-right">{{ number_format((float) $item->price, 2, ',', ' ') }}</td>
                            <td class="text-right"><strong>{{ number_format($lineSub, 2, ',', ' ') }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="color: #94a3b8;">Aucun article enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="order-foot">
                Sous-total : <strong>{{ number_format((float) $order->subtotal, 2, ',', ' ') }} DH</strong>
                &nbsp;|&nbsp; Livraison : <strong>{{ number_format((float) $order->shipping_cost, 2, ',', ' ') }} DH</strong>
                @if((float) $order->discount > 0)
                    &nbsp;|&nbsp; Remise : <strong style="color: #dc2626;">-{{ number_format((float) $order->discount, 2, ',', ' ') }} DH</strong>
                @endif
                @if((float) $order->tax > 0)
                    &nbsp;|&nbsp; TVA : <strong>{{ number_format((float) $order->tax, 2, ',', ' ') }} DH</strong>
                @endif
                &nbsp;|&nbsp; Total : <strong style="font-size: 9.5px; color: #0f172a;">{{ number_format((float) $order->total, 2, ',', ' ') }} DH</strong>
                @if($order->notes)
                    <div style="text-align: left; color: #64748b; margin-top: 3px; font-size: 8px;">
                        Note : {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-box">Aucune commande ne correspond aux filtres sélectionnés.</div>
    @endforelse
</body>
</html>

