<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Commande #{{ $order->order_number }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.5; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 30px 10px; }
        .email-container { max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08); border: 1px solid #e2e8f0; }
        .email-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 32px 30px; text-align: center; border-bottom: 3px solid #dc2626; color: #ffffff; }
        .brand-title { font-size: 20px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin: 0 0 10px; color: #ffffff; }
        .alert-pill { display: inline-block; background-color: rgba(220, 38, 38, 0.2); border: 1px solid #dc2626; color: #fecaca; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; padding: 6px 14px; border-radius: 9999px; }
        .email-body { padding: 30px; }
        .order-headline { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
        .order-date { font-size: 13px; color: #64748b; margin: 0 0 24px; }
        
        /* Info Cards */
        .info-grid { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 24px; }
        .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; vertical-align: top; }
        .info-card-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; color: #dc2626; margin: 0 0 10px; }
        .info-card-text { font-size: 13.5px; color: #334155; margin: 0 0 6px; line-height: 1.45; }
        .info-card-text strong { color: #0f172a; }
        
        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table th { background-color: #f8fafc; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 14px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        .items-table td { padding: 14px; border-bottom: 1px solid #e2e8f0; font-size: 13.5px; vertical-align: middle; }
        .item-name { font-weight: 700; color: #0f172a; margin-bottom: 3px; }
        .item-sku { font-size: 11px; color: #64748b; }
        
        /* Financial Summary */
        .summary-box { background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 18px 22px; margin-bottom: 26px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13.5px; color: #64748b; margin-bottom: 8px; }
        .summary-row.total-row { font-size: 18px; font-weight: 800; color: #0f172a; border-top: 2px dashed #cbd5e1; padding-top: 12px; margin-top: 8px; margin-bottom: 0; }
        .total-price { color: #dc2626; }
        
        /* CTA Button */
        .btn-cta { display: block; text-align: center; background-color: #dc2626; color: #ffffff !important; text-decoration: none; font-size: 15px; font-weight: 700; padding: 14px 28px; border-radius: 10px; margin: 24px 0 16px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); }
        .btn-cta:hover { background-color: #b91c1c; }
        
        /* Footer */
        .email-footer { background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="brand-title">{{ setting('app_name', 'WINA SHOP') }}</div>
                <div class="alert-pill">🔔 Nouvelle Commande Reçue</div>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h1 class="order-headline">Commande #{{ $order->order_number }}</h1>
                <p class="order-date">Date : {{ $order->created_at->format('d/m/Y à H:i') }} &bull; Statut : <strong style="color: #ea580c;">{{ ucfirst($order->status ?? 'En attente') }}</strong></p>

                <!-- Client & Livraison -->
                <table class="info-grid">
                    <tr>
                        <td class="info-card" width="48%">
                            <div class="info-card-title">👤 Informations Client</div>
                            <p class="info-card-text"><strong>Nom :</strong> {{ $order->customer_name }}</p>
                            <p class="info-card-text"><strong>Téléphone :</strong> <a href="tel:{{ $order->customer_phone }}" style="color: #dc2626; font-weight: bold; text-decoration: none;">{{ $order->customer_phone ?: 'Non renseigné' }}</a></p>
                            <p class="info-card-text"><strong>Email :</strong> <a href="mailto:{{ $order->customer_email }}" style="color: #0284c7; text-decoration: none;">{{ $order->customer_email ?: 'Non renseigné' }}</a></p>
                            @if($order->ice)
                            <p class="info-card-text"><strong>ICE Entreprise / INDH :</strong> <span style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 12px; font-weight: 700;">{{ $order->ice }}</span></p>
                            @endif
                        </td>
                        <td width="4%">&nbsp;</td>
                        <td class="info-card" width="48%">
                            <div class="info-card-title">📍 Livraison & Paiement</div>
                            <p class="info-card-text"><strong>Ville :</strong> {{ $order->shipping_city ?: 'Non spécifiée' }}</p>
                            <p class="info-card-text"><strong>Adresse :</strong> {{ $order->shipping_address ?: 'Non renseignée' }}</p>
                            <p class="info-card-text"><strong>Règlement :</strong> {{ strtoupper($order->payment_method ?? 'COD (Paiement à la livraison)') }}</p>
                            <p class="info-card-text"><strong>Paiement :</strong> <span style="color: #ea580c; font-weight: bold;">{{ ucfirst($order->payment_status ?? 'En attente') }}</span></p>
                        </td>
                    </tr>
                </table>

                <!-- Articles Commandés -->
                <h3 style="font-size: 15px; font-weight: 800; color: #0f172a; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 0.5px;">Articles commandés ({{ $order->items->count() }})</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th style="text-align: center;">Qté</th>
                            <th style="text-align: right;">Prix unit.</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="item-name">{{ $item->product_name }}</div>
                                @if($item->product_sku && $item->product_sku !== 'N/A')
                                    <div class="item-sku">SKU: {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #0f172a;">
                                {{ $item->quantity }}
                            </td>
                            <td style="text-align: right; color: #475569;">
                                {{ number_format($item->price, 2, ',', ' ') }} DH
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                {{ number_format($item->subtotal ?: ($item->price * $item->quantity), 2, ',', ' ') }} DH
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Résumé Financier -->
                <div class="summary-box">
                    <table width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td style="padding: 4px 0; color: #64748b; font-size: 13.5px;">Sous-total HT :</td>
                            <td style="padding: 4px 0; text-align: right; color: #0f172a; font-weight: 600; font-size: 13.5px;">{{ number_format($order->subtotal, 2, ',', ' ') }} DH</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; color: #64748b; font-size: 13.5px;">TVA ({{ setting('tax_rate', 20) }}%) :</td>
                            <td style="padding: 4px 0; text-align: right; color: #0f172a; font-weight: 600; font-size: 13.5px;">{{ number_format($order->tax, 2, ',', ' ') }} DH</td>
                        </tr>
                        <tr>
                            <td style="padding: 4px 0; color: #64748b; font-size: 13.5px;">Frais de livraison :</td>
                            <td style="padding: 4px 0; text-align: right; color: #059669; font-weight: 700; font-size: 13.5px;">
                                {{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2, ',', ' ') . ' DH' : 'Gratuite' }}
                            </td>
                        </tr>
                        <tr style="border-top: 2px dashed #cbd5e1;">
                            <td style="padding: 12px 0 4px; font-size: 17px; font-weight: 800; color: #0f172a;">TOTAL TTC :</td>
                            <td style="padding: 12px 0 4px; text-align: right; font-size: 19px; font-weight: 800; color: #dc2626;">{{ number_format($order->total, 2, ',', ' ') }} DH</td>
                        </tr>
                    </table>
                </div>

                <!-- CTA Button -->
                <a href="{{ !empty($order->id) ? route('orders.show', $order->id) : url('/orders') }}" class="btn-cta">
                    👉 Traiter cette commande dans le Dashboard
                </a>

                <p style="font-size: 12.5px; color: #64748b; text-align: center; margin: 0;">
                    Connectez-vous à votre panneau d'administration pour valider le stock, imprimer le bordereau de livraison ou générer la facture ICE.
                </p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                Notification automatique générée par <strong>{{ setting('app_name', 'Wina Shop') }}</strong> &bull; Casablanca, Maroc<br>
                Ce message a été envoyé à l'adresse de notification configurée dans votre tableau de bord.
            </div>
        </div>
    </div>
</body>
</html>
