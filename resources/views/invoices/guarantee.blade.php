@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    .guarantee-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }
    
    .guarantee-accent-bar {
        height: 8px;
        background: linear-gradient(90deg, #10b981 0%, #059669 50%, #0284c7 100%);
    }
    
    .btn-action {
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .items-table th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .items-table td {
        border-bottom: 1px solid #f1f5f9;
    }
    
    .guarantee-badge {
        padding: 6px 14px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }

    .terms-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-top: 24px;
    }
    
    .terms-text {
        font-size: 13px;
        line-height: 1.7;
        color: #334155;
        white-space: pre-line;
    }
</style>

<div style="padding: 40px 24px; max-width: 1100px; margin: 0 auto;">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <a href="{{ route('invoices.show', $invoice) }}" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la Facture #{{ $invoice->invoice_number }}
                </a>
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0; letter-spacing: -0.5px; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-shield-alt" style="color: #10b981;"></i>
                    {{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }}
                    <span style="color: #64748b; font-weight: 400; font-size: 20px;">#GAR-{{ $invoice->invoice_number }}</span>
                </h1>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <!-- Download PDF Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #10b981; border-color: #10b981; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-download"></i> Télécharger le PDF
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp" style="color: #10b981; width: 16px;"></i> Télécharger avec Cachet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="far fa-file-pdf text-muted" style="width: 16px;"></i> Télécharger sans Cachet
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Print Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-action btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: white; color: #475569; border-color: #e2e8f0; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size: 13px; border-radius: 10px;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 1]) }}">
                                <i class="fas fa-stamp" style="color: #10b981; width: 16px;"></i> Imprimer avec Cachet
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 0]) }}">
                                <i class="fas fa-print text-muted" style="width: 16px;"></i> Imprimer sans Cachet
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-action btn-outline-secondary" style="background: white; color: #475569; border-color: #e2e8f0; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-invoice"></i> Voir la Facture
                </a>
            </div>
        </div>
    </div>

    <!-- Guarantee Content Card -->
    <div class="guarantee-card">
        <div class="guarantee-accent-bar"></div>
        <div style="padding: 40px;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 24px; flex-wrap: wrap; gap: 20px;">
                <div>
                    @if(setting('company_logo') || setting('app_logo'))
                        <img src="{{ asset('storage/' . (setting('company_logo') ?? setting('app_logo'))) }}" alt="Logo" style="max-height: 55px; margin-bottom: 12px; object-fit: contain;">
                    @else
                        <h2 style="font-size: 26px; font-weight: 900; color: #1e293b; margin: 0 0 8px 0;">{{ setting('company_name', config('app.name')) }}</h2>
                    @endif
                    
                    <div style="color: #64748b; font-size: 13px; line-height: 1.5;">
                        <p style="margin: 0; font-weight: 600; color: #334155;">{{ setting('company_name', config('app.name')) }}</p>
                        @if(setting('company_address'))
                            <p style="margin: 0;">{{ setting('company_address') }}</p>
                        @endif
                        <p style="margin: 0;">
                            @if(setting('company_phone'))
                                <span>{{ setting('company_phone') }}</span>
                            @endif
                            @if(setting('company_email'))
                                <span style="margin-left: 8px; margin-right: 8px;">•</span>
                                <span>{{ setting('company_email') }}</span>
                            @endif
                        </p>
                        <div style="margin-top: 6px; font-size: 11px; color: #94a3b8; display: flex; flex-wrap: wrap; gap: 8px;">
                            @if(setting('company_tax_id'))
                                <span><strong>ICE:</strong> {{ setting('company_tax_id') }}</span>
                            @endif
                            @if(setting('company_registry_id'))
                                <span><strong>RC:</strong> {{ setting('company_registry_id') }}</span>
                            @endif
                            @if(setting('company_patente'))
                                <span><strong>Patente:</strong> {{ setting('company_patente') }}</span>
                            @endif
                            @if(setting('company_fiscal_id'))
                                <span><strong>IF:</strong> {{ setting('company_fiscal_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="text-align: right;">
                    <span class="guarantee-badge">
                        <i class="fas fa-shield-alt"></i> {{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }}
                    </span>
                    <div style="margin-top: 14px;">
                        <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px 0;">Réf. Garantie</p>
                        <p style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0;">GAR-{{ $invoice->invoice_number }}</p>
                    </div>
                    <div style="margin-top: 10px;">
                        <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px 0;">Facture N°</p>
                        <p style="font-size: 14px; font-weight: 700; color: #0284c7; margin: 0;">#{{ $invoice->invoice_number }} ({{ $invoice->issued_at->format('d/m/Y') }})</p>
                    </div>
                    <div style="margin-top: 10px;">
                        <p style="color: #94a3b8; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px 0;">Durée de Garantie</p>
                        <p style="font-size: 14px; font-weight: 700; color: #059669; margin: 0;">{{ setting('guarantee_period_default', '12 Mois') }}</p>
                    </div>
                </div>
            </div>

            <!-- Client & Guarantee Info Cards -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
                <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #f1f5f9;">
                    <p style="color: #10b981; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-user-check"></i> Informations du Client / Bénéficiaire
                    </p>
                    <h3 style="font-size: 18px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0;">{{ $invoice->customer_name }}</h3>
                    <div style="color: #64748b; font-size: 13px; line-height: 1.6;">
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

                <div style="background: #f8fafc; padding: 24px; border-radius: 16px; border: 1px solid #f1f5f9;">
                    <p style="color: #0284c7; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-calendar-check"></i> Validité & Période de Couverture
                    </p>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <p style="color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Date de Début</p>
                            <p style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0;">{{ $invoice->issued_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p style="color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Durée de Garantie</p>
                            <p style="font-size: 15px; font-weight: 700; color: #059669; margin: 0;">{{ setting('guarantee_period_default', '12 Mois') }}</p>
                        </div>
                    </div>
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0; font-size: 12px; color: #64748b;">
                        <i class="fas fa-info-circle text-primary me-1"></i> Valable exclusivement pour les équipements et matériels mentionnés ci-dessous.
                    </div>
                </div>
            </div>

            <!-- Invoice Covered Items Table -->
            <div style="margin-bottom: 32px;">
                <p style="color: #1e293b; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-boxes" style="color: #10b981;"></i> Produits & Équipements Couverts
                </p>
                <div style="overflow-x: auto; border: 1px solid #f1f5f9; border-radius: 16px;">
                    <table class="items-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr>
                                <th style="padding: 14px 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; width: 60px;">N°</th>
                                <th style="padding: 14px 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Référence / SKU</th>
                                <th style="padding: 14px 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">Désignation de l'Article</th>
                                <th style="padding: 14px 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; text-align: center; width: 100px;">Quantité</th>
                                <th style="padding: 14px 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px; text-align: right; width: 160px;">Période de Garantie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoice->items as $index => $item)
                            <tr>
                                <td style="padding: 16px 20px; font-size: 13px; font-weight: 600; color: #94a3b8;">{{ sprintf('%02d', $index + 1) }}</td>
                                <td style="padding: 16px 20px; font-size: 12px; font-weight: 700; color: #0284c7;">
                                    {{ $item->product_sku ?? ($item->product->sku ?? 'N/A') }}
                                </td>
                                <td style="padding: 16px 20px;">
                                    <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 14px;">{{ $item->product_name ?? ($item->product->name ?? 'Produit') }}</p>
                                </td>
                                <td style="padding: 16px 20px; text-align: center; font-weight: 700; color: #1e293b; font-size: 14px;">
                                    {{ $item->quantity }}
                                </td>
                                <td style="padding: 16px 20px; text-align: right;">
                                    <span style="background: #ecfdf5; color: #059669; font-weight: 700; padding: 4px 10px; border-radius: 6px; font-size: 12px;">
                                        {{ setting('guarantee_period_default', '12 Mois') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="padding: 24px; text-align: center; color: #94a3b8;">Aucun article trouvé dans cette facture.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Guarantee Terms & Conditions Section -->
            <div class="terms-box">
                <p style="color: #1e293b; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-file-contract" style="color: #0284c7;"></i> Conditions Générales de Garantie
                </p>
                <div class="terms-text">
{{ setting('guarantee_terms', "1. La présente garantie couvre tous les défauts de fabrication et de fonctionnement du matériel pour la durée spécifiée à compter de la date d'émission de la facture.\n2. La garantie s'applique uniquement sur présentation du présent bon de garantie accompagné de la facture d'achat originale.\n3. Sont exclus de la garantie : les dommages dus à une mauvaise utilisation, à une négligence, aux surtensions électriques, aux dégâts des eaux, ou à toute intervention technique non autorisée.\n4. En cas de panne couverte par la garantie, notre service après-vente procédera gratuitement à la réparation ou au remplacement du composant défectueux dans les meilleurs délais.\n5. Les consommables, accessoires d'usure et pièces externes ne sont pas couverts par la garantie.") }}
                </div>

                @if(setting('guarantee_notes'))
                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed #cbd5e1; font-size: 12px; color: #64748b; font-style: italic;">
                    <strong>Remarque :</strong> {{ setting('guarantee_notes') }}
                </div>
                @endif
            </div>

            <!-- Signatures & Stamps Block -->
            <div style="margin-top: 36px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
                <div style="border: 1px dashed #cbd5e1; border-radius: 16px; padding: 20px; min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                    <p style="color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                        Signature du Client / Bon pour accord
                    </p>
                    <p style="color: #94a3b8; font-size: 11px; font-style: italic; margin: 0;">
                        Mention manuscrite "Lu et approuvé"
                    </p>
                </div>

                <div style="border: 1px dashed #cbd5e1; border-radius: 16px; padding: 20px; min-height: 140px; display: flex; flex-direction: column; justify-content: space-between;">
                    <p style="color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                        Cachet & Signature de l'Entreprise
                    </p>
                    
                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                    <div style="margin-top: 8px;">
                        <img src="{{ asset('storage/' . setting('company_stamp')) }}" alt="Company Stamp" style="width: {{ round(160 * intval(setting('company_stamp_scale', 100)) / 100) }}px; height: auto; object-fit: contain; display: inline-block;">
                    </div>
                    @else
                    <p style="color: #94a3b8; font-size: 11px; font-style: italic; margin: 0;">
                        Signataire Autorisé
                    </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
