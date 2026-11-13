<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket POS #{{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            color: #0f172a;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .actions {
            margin-bottom: 16px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 9px 16px;
            border: none;
            border-radius: 8px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #dc2626; color: #fff; }
        .btn-back { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        .ticket {
            width: 80mm;
            max-width: 100%;
            background: #fff;
            padding: 16px 14px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.1);
            font-size: 12px;
            line-height: 1.45;
        }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .sep { border-top: 1px dashed #64748b; margin: 10px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .item-block { margin-bottom: 8px; }
        .item-sub { font-size: 11px; color: #475569; }
        .total-row { font-size: 15px; font-weight: 700; margin-top: 4px; }
        .footer { text-align: center; font-size: 11px; color: #475569; margin-top: 12px; }
        @media print {
            body { background: #fff; padding: 0; }
            .actions { display: none !important; }
            .ticket { box-shadow: none; width: 100%; padding: 4px; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" class="btn btn-print" onclick="window.print()">Imprimer Ticket</button>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-back">Voir Commande</a>
        <button type="button" class="btn btn-back" onclick="window.close()">Fermer</button>
    </div>

    <div class="ticket">
        <div class="center">
            <div class="bold" style="font-size: 15px;">{{ strtoupper(setting('app_name', 'WINA SHOP')) }}</div>
            @if(setting('company_address'))
                <div style="font-size: 11px;">{{ setting('company_address') }}</div>
            @endif
            @if(setting('company_phone'))
                <div style="font-size: 11px;">Tél : {{ setting('company_phone') }}</div>
            @endif
        </div>

        <div class="sep"></div>

        <div class="row">
            <span>Ticket :</span>
            <span class="bold">#{{ $order->order_number }}</span>
        </div>
        <div class="row">
            <span>Date :</span>
            <span>{{ $order->created_at?->format('d/m/Y H:i') }}</span>
        </div>
        @if($order->user)
        <div class="row">
            <span>Caissier :</span>
            <span>{{ $order->user->name }}</span>
        </div>
        @endif
        @php
            $storeNames = $order->items->pluck('store.name')->filter()->unique()->implode(', ');
        @endphp
        @if($storeNames)
        <div class="row">
            <span>Point de vente :</span>
            <span>{{ $storeNames }}</span>
        </div>
        @endif

        <div class="sep"></div>

        <div>
            <div><span class="bold">Client :</span> {{ $order->customer_name }}</div>
            @if($order->customer_phone)
                <div><span class="bold">Tél :</span> {{ $order->customer_phone }}</div>
            @endif
            @if($order->shipping_city && $order->shipping_city !== 'En boutique')
                <div><span class="bold">Ville :</span> {{ $order->shipping_city }}</div>
            @endif
        </div>

        <div class="sep"></div>

        @foreach($order->items as $item)
            <div class="item-block">
                <div class="bold">{{ $item->product_name }}</div>
                <div class="row item-sub">
                    <span>{{ $item->quantity }} × {{ currency($item->original_price ?? $item->price) }}</span>
                    <span class="bold" style="color: #0f172a;">{{ currency($item->subtotal) }}</span>
                </div>
                @if((float) ($item->discount_amount ?? 0) > 0)
                    <div class="row item-sub">
                        <span>Remise article</span>
                        <span>-{{ currency($item->discount_amount) }}</span>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="sep"></div>

        <div class="row">
            <span>Sous-total :</span>
            <span>{{ currency((float) $order->total + (float) $order->discount) }}</span>
        </div>
        @if((float) $order->discount > 0)
        <div class="row">
            <span>Remise totale :</span>
            <span>-{{ currency($order->discount) }}</span>
        </div>
        @endif

        <div class="sep"></div>

        <div class="row total-row">
            <span>TOTAL :</span>
            <span>{{ currency($order->total) }}</span>
        </div>
        <div class="row" style="margin-top: 4px;">
            <span>Paiement :</span>
            <span class="bold">{{ strtoupper($order->payment_method) }}</span>
        </div>

        <div class="sep"></div>

        <div class="footer">
            Merci pour votre visite !<br>
            À très bientôt chez {{ setting('app_name', 'Wina Shop') }}.
        </div>
    </div>
</body>
</html>

