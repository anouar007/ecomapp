<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }} - {{ $invoice->invoice_number }}</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #f8fafc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .guarantee-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            max-width: 950px;
            margin: 0 auto;
            position: relative;
            page-break-inside: avoid;
        }

        .guarantee-accent-bar {
            height: 6px;
            background: linear-gradient(90deg, #10b981 0%, #059669 50%, #0284c7 100%);
        }

        .guarantee-content {
            padding: 40px;
        }

        .company-name {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 12px 0;
            letter-spacing: -1px;
            line-height: 1;
        }

        .company-details p {
            margin: 0 0 4px 0;
            color: #64748b;
            font-size: 13px;
        }

        .fiscal-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 4px;
        }

        .guarantee-title-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .section-label {
            color: #059669;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .client-name {
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 8px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background: #f8fafc;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .terms-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .terms-text {
            font-size: 12px;
            color: #334155;
            line-height: 1.65;
            white-space: pre-line;
        }

        .signature-box {
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 16px;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .btn-print-floating {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #10b981;
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            transition: all 0.2s;
        }

        .btn-print-floating:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(16, 185, 129, 0.5);
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }

            .guarantee-card {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }

            .guarantee-content {
                padding: 20px !important;
            }

            .btn-print-floating, .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Action Floating Controls -->
    <button class="btn-print-floating no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimer le Certificat
    </button>

    <div style="padding: 40px 20px;">
        <div class="guarantee-card">
            <div class="guarantee-accent-bar"></div>
            
            <div class="guarantee-content">
                <!-- Header Section -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 24px;">
                    <div>
                        @if(setting('company_logo') || setting('app_logo'))
                            <img src="{{ asset('storage/' . (setting('company_logo') ?? setting('app_logo'))) }}" alt="Logo" style="max-height: 55px; margin-bottom: 12px; object-fit: contain;">
                        @else
                            <h1 class="company-name">{{ setting('company_name', config('app.name')) }}</h1>
                        @endif
                        
                        <div class="company-details">
                            <p style="font-weight: 700; color: #1e293b;">{{ setting('company_name', config('app.name')) }}</p>
                            @if(setting('company_address'))
                                <p>{{ setting('company_address') }}</p>
                            @endif
                            <p>
                                @if(setting('company_phone'))
                                    <span>{{ setting('company_phone') }}</span>
                                @endif
                                @if(setting('company_email'))
                                    <span style="margin: 0 6px;">•</span>
                                    <span>{{ setting('company_email') }}</span>
                                @endif
                            </p>
                            <div style="margin-top: 6px;">
                                @if(setting('company_tax_id'))
                                    <span class="fiscal-badge">ICE: {{ setting('company_tax_id') }}</span>
                                @endif
                                @if(setting('company_registry_id'))
                                    <span class="fiscal-badge">RC: {{ setting('company_registry_id') }}</span>
                                @endif
                                @if(setting('company_patente'))
                                    <span class="fiscal-badge">Patente: {{ setting('company_patente') }}</span>
                                @endif
                                @if(setting('company_fiscal_id'))
                                    <span class="fiscal-badge">IF: {{ setting('company_fiscal_id') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <span class="guarantee-title-badge">
                            <i class="fas fa-shield-alt"></i> {{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }}
                        </span>
                        
                        <div style="margin-top: 14px;">
                            <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0 0 2px 0;">Réf. Garantie</p>
                            <p style="font-size: 17px; font-weight: 800; color: #1e293b; margin: 0;">GAR-{{ $invoice->invoice_number }}</p>
                        </div>
                        <div style="margin-top: 8px;">
                            <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0 0 2px 0;">Facture N°</p>
                            <p style="font-size: 14px; font-weight: 700; color: #0284c7; margin: 0;">#{{ $invoice->invoice_number }} ({{ $invoice->issued_at->format('d/m/Y') }})</p>
                        </div>
                        <div style="margin-top: 8px;">
                            <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0 0 2px 0;">Durée de Garantie</p>
                            <p style="font-size: 14px; font-weight: 700; color: #059669; margin: 0;">{{ setting('guarantee_period_default', '12 Mois') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Client Info & Coverage Details Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="section-label">Informations du Client / Bénéficiaire</div>
                        <div class="client-name">{{ $invoice->customer_name }}</div>
                        <div style="font-size: 13px; color: #64748b; line-height: 1.6;">
                            @if($invoice->customer_phone)
                                <p style="margin: 0;"><i class="fas fa-phone-alt me-1" style="font-size: 11px;"></i> {{ $invoice->customer_phone }}</p>
                            @endif
                            @if($invoice->customer_email)
                                <p style="margin: 0;"><i class="fas fa-envelope me-1" style="font-size: 11px;"></i> {{ $invoice->customer_email }}</p>
                            @endif
                            @if($invoice->customer_address)
                                <p style="margin: 0;"><i class="fas fa-map-marker-alt me-1" style="font-size: 11px;"></i> {{ $invoice->customer_address }}</p>
                            @endif
                            @if($invoice->ice)
                                <p style="margin: 4px 0 0 0;"><span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 700;">ICE: {{ $invoice->ice }}</span></p>
                            @endif
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="section-label" style="color: #0284c7;">Validité & Période de Couverture</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
                            <div>
                                <p style="color: #94a3b8; font-size: 11px; font-weight: 800; margin: 0 0 2px 0;">Date de Début</p>
                                <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">{{ $invoice->issued_at->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p style="color: #94a3b8; font-size: 11px; font-weight: 800; margin: 0 0 2px 0;">Durée de Garantie</p>
                                <p style="font-size: 15px; font-weight: 700; color: #059669; margin: 0;">{{ setting('guarantee_period_default', '12 Mois') }}</p>
                            </div>
                        </div>
                        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0; font-size: 12px; color: #64748b;">
                            Valable exclusivement pour les équipements et matériels mentionnés dans ce certificat.
                        </div>
                    </div>
                </div>

                <!-- Covered Items Table -->
                <div style="margin-bottom: 24px;">
                    <div style="font-size: 13px; font-weight: 800; color: #1e293b; text-transform: uppercase; margin-bottom: 8px;">
                        <i class="fas fa-boxes text-emerald me-1" style="color: #10b981;"></i> Produits & Équipements Couverts
                    </div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">N°</th>
                                <th style="width: 160px; text-align: left;">Référence / SKU</th>
                                <th style="text-align: left;">Désignation de l'Article</th>
                                <th style="width: 90px; text-align: center;">Quantité</th>
                                <th style="width: 140px; text-align: right;">Garantie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $index => $item)
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-weight: 600;">{{ sprintf('%02d', $index + 1) }}</td>
                                <td style="font-weight: 700; color: #0284c7;">
                                    {{ $item->product_sku ?? ($item->product->sku ?? 'N/A') }}
                                </td>
                                <td>
                                    <strong style="color: #1e293b;">{{ $item->product_name ?? ($item->product->name ?? 'Produit') }}</strong>
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #1e293b;">{{ $item->quantity }}</td>
                                <td style="text-align: right; color: #059669; font-weight: 700;">
                                    {{ setting('guarantee_period_default', '12 Mois') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Aucun article trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Terms and Conditions Block -->
                <div class="terms-box">
                    <div style="font-size: 12px; font-weight: 800; color: #059669; text-transform: uppercase; margin-bottom: 8px;">
                        <i class="fas fa-file-contract me-1"></i> Conditions Générales de Garantie
                    </div>
                    <div class="terms-text">
{{ setting('guarantee_terms', "1. La présente garantie couvre tous les défauts de fabrication et de fonctionnement du matériel pour la durée spécifiée à compter de la date d'émission de la facture.\n2. La garantie s'applique uniquement sur présentation du présent bon de garantie accompagné de la facture d'achat originale.\n3. Sont exclus de la garantie : les dommages dus à une mauvaise utilisation, à une négligence, aux surtensions électriques, aux dégâts des eaux, ou à toute intervention technique non autorisée.\n4. En cas de panne couverte par la garantie, notre service après-vente procédera gratuitement à la réparation ou au remplacement du composant défectueux dans les meilleurs délais.\n5. Les consommables, accessoires d'usure et pièces externes ne sont pas couverts par la garantie.") }}
                    </div>

                    @if(setting('guarantee_notes'))
                    <div style="margin-top: 12px; padding-top: 10px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #64748b; font-style: italic;">
                        <strong>Remarque importante :</strong> {{ setting('guarantee_notes') }}
                    </div>
                    @endif
                </div>

                <!-- Signatures and Stamps Block -->
                <div style="margin-top: 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div class="signature-box">
                        <p style="color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0;">
                            Signature du Client / Bon pour accord
                        </p>
                        <p style="color: #94a3b8; font-size: 10px; font-style: italic; margin: 0;">
                            Mention manuscrite "Lu et approuvé"
                        </p>
                    </div>

                    <div class="signature-box">
                        <p style="color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0;">
                            Cachet & Signature de l'Entreprise
                        </p>
                        
                        @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                        <div style="margin-top: 6px;">
                            <img src="{{ asset('storage/' . setting('company_stamp')) }}" alt="Company Stamp" style="width: {{ round(160 * intval(setting('company_stamp_scale', 100)) / 100) }}px; height: auto; object-fit: contain; display: inline-block;">
                        </div>
                        @else
                        <p style="color: #94a3b8; font-size: 10px; font-style: italic; margin: 0;">
                            Signataire Autorisé
                        </p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
