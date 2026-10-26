@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
    
    .guarantee-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        font-family: "Roboto", sans-serif;
    }
    
    .guarantee-accent-bar {
        height: 8px;
        background: linear-gradient(90deg, #10b981 0%, #059669 50%, #0284c7 100%);
    }
    
    /* Pro Button Base */
    .btn-pro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 38px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border-radius: 9px;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        user-select: none;
        line-height: 1;
    }

    .btn-pro:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }

    .btn-pro.dropdown-toggle::after {
        display: none !important;
    }

    .btn-chevron {
        font-size: 9.5px;
        transition: transform 0.2s ease;
        opacity: 0.6;
        margin-left: 3px;
    }

    .dropdown.show .btn-chevron {
        transform: rotate(180deg);
    }

    .btn-pro-emerald {
        background: linear-gradient(180deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border-color: #059669 !important;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.15), 0 3px 8px rgba(16, 185, 129, 0.2) !important;
    }

    .btn-pro-emerald:hover {
        background: linear-gradient(180deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28) !important;
    }

    .btn-pro-secondary {
        background: #ffffff !important;
        color: #1e293b !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    .btn-pro-secondary:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06) !important;
    }

    .btn-pro-secondary i:not(.btn-chevron) {
        color: #64748b;
    }

    .pro-dropdown-menu {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.14), 0 4px 10px -2px rgba(15, 23, 42, 0.05) !important;
        padding: 6px !important;
        min-width: 230px !important;
        background: #ffffff !important;
        margin-top: 6px !important;
    }

    .pro-dropdown-item {
        padding: 7px 10px !important;
        border-radius: 8px !important;
        font-size: 12.5px !important;
        font-weight: 500 !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
    }

    .pro-dropdown-item:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .pro-icon-box {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        flex-shrink: 0;
    }

    .pro-icon-box.emerald {
        background: #ecfdf5;
        color: #059669;
    }

    .pro-icon-box.slate {
        background: #f1f5f9;
        color: #64748b;
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
                    <button class="btn-pro btn-pro-emerald dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-pdf"></i>
                        <span>Télécharger le PDF</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Télécharger avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Document certifié officiel</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="far fa-file-pdf"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Télécharger sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Version vierge standard</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Print Dropdown -->
                <div class="dropdown">
                    <button class="btn-pro btn-pro-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-print"></i>
                        <span>Imprimer</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Prêt à remettre au client</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Impression sans tampon</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('invoices.show', $invoice) }}" class="btn-pro btn-pro-secondary">
                    <i class="fas fa-file-invoice"></i>
                    <span>Voir la Facture</span>
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
                    @if(app_logo_url())
                        <img src="{{ app_logo_url() }}" alt="Logo" style="max-height: 55px; margin-bottom: 12px; object-fit: contain;">
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
