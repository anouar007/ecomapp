<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }} - {{ $invoice->invoice_number }}</title>
    <style>
        /* PDF specific resets */
        @page {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #1e293b;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 10px;
        }

        .accent-bar {
            height: 6px;
            background: #10b981;
            width: 100%;
        }

        .container {
            padding: 26px 36px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .company-info p {
            margin: 1px 0;
            color: #64748b;
            font-size: 9px;
        }

        .fiscal-tag {
            background: #f8fafc;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #f1f5f9;
            font-size: 8px;
            color: #94a3b8;
            font-weight: bold;
            margin-right: 4px;
        }

        .section-title {
            color: #059669;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .client-name {
            font-size: 15px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 3px 0;
        }

        .label-sm {
            color: #94a3b8;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            border: 1px solid #e2e8f0;
        }

        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
            color: #1e293b;
        }

        .terms-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 14px;
        }

        .terms-content {
            font-size: 8.5px;
            color: #334155;
            line-height: 1.5;
            white-space: pre-line;
        }

        .signature-box {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            min-height: 80px;
        }
    </style>
</head>
<body>
    <div class="accent-bar"></div>

    <div class="container">
        <!-- Header Section -->
        <table style="width: 100%; margin-bottom: 18px;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    @php
                        $logoSetting = setting('company_logo') ?? setting('app_logo');
                        $logoPath = $logoSetting ? public_path('storage/' . $logoSetting) : null;
                    @endphp
                    @if($logoPath && file_exists($logoPath))
                        <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}" style="max-height: 45px; margin-bottom: 8px;">
                    @else
                        <div class="company-name">{{ setting('company_name', config('app.name')) }}</div>
                    @endif
                    
                    <div class="company-info">
                        <p style="font-weight: bold; color: #334155;">{{ setting('company_name', config('app.name')) }}</p>
                        @if(setting('company_address'))
                            <p>{{ setting('company_address') }}</p>
                        @endif
                        <p>
                            @if(setting('company_phone'))
                                <span>{{ setting('company_phone') }}</span>
                            @endif
                            @if(setting('company_email'))
                                <span>• {{ setting('company_email') }}</span>
                            @endif
                        </p>
                        <p style="margin-top: 4px;">
                            @if(setting('company_tax_id'))
                                <span class="fiscal-tag">ICE: {{ setting('company_tax_id') }}</span>
                            @endif
                            @if(setting('company_registry_id'))
                                <span class="fiscal-tag">RC: {{ setting('company_registry_id') }}</span>
                            @endif
                            @if(setting('company_patente'))
                                <span class="fiscal-tag">Patente: {{ setting('company_patente') }}</span>
                            @endif
                            @if(setting('company_fiscal_id'))
                                <span class="fiscal-tag">IF: {{ setting('company_fiscal_id') }}</span>
                            @endif
                        </p>
                    </div>
                </td>
                <td style="width: 45%; vertical-align: top; text-align: right;">
                    <div style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; border-radius: 4px; padding: 4px 10px; display: inline-block; font-weight: bold; font-size: 11px; margin-bottom: 8px;">
                        {{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }}
                    </div>
                    
                    <table style="width: 100%; margin-top: 4px;">
                        <tr>
                            <td style="text-align: right; padding-bottom: 2px;">
                                <span class="label-sm">Réf. Garantie :</span>
                                <span style="font-weight: bold; color: #1e293b; font-size: 11px;">GAR-{{ $invoice->invoice_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right; padding-bottom: 2px;">
                                <span class="label-sm">Facture N° :</span>
                                <span style="font-weight: bold; color: #0284c7;">#{{ $invoice->invoice_number }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right; padding-bottom: 2px;">
                                <span class="label-sm">Date d'Émission :</span>
                                <span style="font-weight: bold; color: #1e293b;">{{ $invoice->issued_at->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;">
                                <span class="label-sm">Durée de Garantie :</span>
                                <span style="font-weight: bold; color: #059669;">{{ setting('guarantee_period_default', '12 Mois') }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Client Information Block -->
        <table style="width: 100%; margin-bottom: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
            <tr>
                <td style="padding: 12px 16px;">
                    <div class="section-title" style="margin-bottom: 4px;">Informations du Client / Bénéficiaire</div>
                    <div class="client-name">{{ $invoice->customer_name }}</div>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%; font-size: 9px; color: #64748b; vertical-align: top;">
                                @if($invoice->customer_phone)
                                    <div><strong>Téléphone :</strong> {{ $invoice->customer_phone }}</div>
                                @endif
                                @if($invoice->customer_email)
                                    <div><strong>E-mail :</strong> {{ $invoice->customer_email }}</div>
                                @endif
                            </td>
                            <td style="width: 50%; font-size: 9px; color: #64748b; vertical-align: top;">
                                @if($invoice->customer_address)
                                    <div><strong>Adresse :</strong> {{ $invoice->customer_address }}</div>
                                @endif
                                @if($invoice->ice)
                                    <div><strong>ICE :</strong> {{ $invoice->ice }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Covered Items Table -->
        <div style="font-weight: bold; font-size: 10px; color: #1e293b; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
            Produits & Matériels Couverts par la Garantie
        </div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">N°</th>
                    <th style="width: 25%; text-align: left;">Référence / SKU</th>
                    <th style="width: 45%; text-align: left;">Désignation de l'Article</th>
                    <th style="width: 10%; text-align: center;">Quantité</th>
                    <th style="width: 15%; text-align: right;">Garantie</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $index => $item)
                <tr>
                    <td style="text-align: center; color: #94a3b8;">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; color: #0284c7;">
                        {{ $item->product_sku ?? ($item->product->sku ?? 'N/A') }}
                    </td>
                    <td>
                        <strong style="color: #1e293b;">{{ $item->product_name ?? ($item->product->name ?? 'Produit') }}</strong>
                    </td>
                    <td style="text-align: center; font-weight: bold;">{{ $item->quantity }}</td>
                    <td style="text-align: right; color: #059669; font-weight: bold;">
                        {{ setting('guarantee_period_default', '12 Mois') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8;">Aucun article trouvé dans cette facture</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Warranty Terms & Conditions -->
        <div class="terms-box">
            <div style="font-weight: bold; font-size: 9.5px; color: #059669; text-transform: uppercase; margin-bottom: 6px;">
                Conditions Générales de Garantie
            </div>
            <div class="terms-content">
{{ setting('guarantee_terms', "1. La présente garantie couvre tous les défauts de fabrication et de fonctionnement du matériel pour la durée spécifiée à compter de la date d'émission de la facture.\n2. La garantie s'applique uniquement sur présentation du présent bon de garantie accompagné de la facture d'achat originale.\n3. Sont exclus de la garantie : les dommages dus à une mauvaise utilisation, à une négligence, aux surtensions électriques, aux dégâts des eaux, ou à toute intervention technique non autorisée.\n4. En cas de panne couverte par la garantie, notre service après-vente procédera gratuitement à la réparation ou au remplacement du composant défectueux dans les meilleurs délais.\n5. Les consommables, accessoires d'usure et pièces externes ne sont pas couverts par la garantie.") }}
            </div>

            @if(setting('guarantee_notes'))
            <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1; font-size: 8px; color: #64748b; font-style: italic;">
                <strong>Remarque importante :</strong> {{ setting('guarantee_notes') }}
            </div>
            @endif
        </div>

        <!-- Signatures Block -->
        <table style="width: 100%; margin-top: 18px;">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div class="signature-box">
                        <div class="label-sm" style="color: #64748b; margin-bottom: 4px;">Signature du Client / Bon pour accord</div>
                        <div style="font-size: 8px; color: #94a3b8; font-style: italic; margin-top: 45px;">
                            Mention manuscrite "Lu et approuvé"
                        </div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; vertical-align: top;">
                    <div class="signature-box">
                        <div class="label-sm" style="color: #64748b; margin-bottom: 4px;">Cachet & Signature de l'Entreprise</div>
                        
                        @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                            @php
                                $stampPath = public_path('storage/' . setting('company_stamp'));
                                $stampW = round(160 * intval(setting('company_stamp_scale', 100)) / 100);
                            @endphp
                            @if(file_exists($stampPath))
                            <div style="margin-top: 6px;">
                                <img src="data:image/{{ pathinfo($stampPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($stampPath)) }}" style="width: {{ $stampW }}px; height: auto; object-fit: contain;">
                            </div>
                            @endif
                        @else
                            <div style="font-size: 8px; color: #94a3b8; font-style: italic; margin-top: 45px;">
                                Signataire Autorisé
                            </div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
