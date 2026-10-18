@extends('layouts.app')

@section('title', 'Bon de Livraison #' . $invoice->delivery_note_number)

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
    
    .delivery-note-wrapper {
        max-width: 960px;
        margin: 0 auto 40px auto;
        font-family: "Roboto", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .dn-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .dn-top-actions {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Pro Buttons */
    .btn-pro {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 38px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 9px;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        user-select: none;
    }

    .btn-pro:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }

    .btn-pro-primary {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
        color: #ffffff !important;
        border-color: #0f172a !important;
    }

    .btn-pro-primary:hover {
        background: linear-gradient(180deg, #334155 0%, #1e293b 100%) !important;
        color: #ffffff !important;
    }

    .btn-pro-secondary {
        background: #ffffff !important;
        color: #1e293b !important;
        border-color: #e2e8f0 !important;
    }

    .btn-pro-secondary:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
    }

    .btn-pro-amber {
        background: linear-gradient(180deg, #d97706 0%, #b45309 100%) !important;
        color: #ffffff !important;
        border-color: #b45309 !important;
    }

    .btn-pro-amber:hover {
        background: linear-gradient(180deg, #b45309 0%, #92400e 100%) !important;
        color: #ffffff !important;
    }

    .pro-dropdown-menu {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.14) !important;
        padding: 6px !important;
        min-width: 240px !important;
    }

    .pro-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 8px;
        color: #1e293b;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.15s;
    }

    .pro-dropdown-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Delivery Note Card */
    .dn-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        min-height: 1060px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .dn-accent-bar {
        height: 6px;
        background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
        width: 100%;
        flex-shrink: 0;
    }

    .dn-body {
        padding: 36px 40px 16px 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .dn-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 22px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 22px;
        gap: 20px;
    }

    .dn-brand {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .dn-logo {
        max-height: 58px;
        max-width: 170px;
        object-fit: contain;
    }

    .dn-brand-name {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.3px;
        margin-bottom: 2px;
    }

    .dn-brand-sub {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
    }

    .dn-badge-block {
        text-align: right;
    }

    .dn-title-badge {
        display: inline-block;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .dn-number {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .dn-meta-list {
        font-size: 11.5px;
        color: #64748b;
        line-height: 1.5;
    }

    .dn-meta-list strong {
        color: #334155;
    }

    /* ── Client Banner (Full Width, Low Height) ── */
    .dn-client-banner {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3.5px solid #0284c7;
        border-radius: 6px;
        padding: 10px 16px;
        margin-bottom: 20px;
    }

    .dn-client-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
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
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }

    .dn-client-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .dn-client-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.2px;
    }

    .dn-client-details {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 11.5px;
        color: #475569;
        flex-wrap: wrap;
    }

    /* Items Table */
    .dn-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        border-radius: 6px;
        overflow: hidden;
    }

    .dn-table thead tr {
        background: #f8fafc;
        border-bottom: 1.5px solid #cbd5e1;
    }

    .dn-table th {
        padding: 10px 12px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #475569;
        text-align: left;
    }

    .dn-table td {
        padding: 10px 12px;
        font-size: 12px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .dn-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .item-name {
        font-weight: 700;
        color: #0f172a;
    }

    .item-sku {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }

    .check-box {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 1.5px solid #94a3b8;
        border-radius: 3px;
        vertical-align: middle;
        margin-left: 6px;
        background: #ffffff;
        text-align: center;
        line-height: 13px;
        font-size: 10px;
        color: #059669;
        font-weight: bold;
    }

    /* Summary & Totals */
    .dn-summary-row {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .dn-summary-left {
        flex: 1.25;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dn-summary-right {
        flex: 1;
        max-width: 360px;
    }

    .legal-notice-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #0284c7;
        border-radius: 0 6px 6px 0;
        padding: 10px 14px;
        font-size: 11px;
        color: #475569;
        line-height: 1.45;
    }

    .legal-notice-card strong {
        color: #0f172a;
    }

    .dn-totals-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 14px 18px;
        color: #0f172a;
    }

    .dn-total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .dn-total-label {
        color: #64748b;
    }

    .dn-total-val {
        font-weight: 700;
        color: #0f172a;
    }

    .dn-grand-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding-top: 8px;
        border-top: 1.5px solid #e2e8f0;
        margin-top: 6px;
    }

    .dn-grand-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #0f172a;
    }

    .dn-grand-val {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Dual Signature & Discharge Section */
    .dn-signatures-section {
        display: flex;
        gap: 18px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px dashed #cbd5e1;
    }

    .signature-box {
        flex: 1;
        background: #ffffff;
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        padding: 14px 16px;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .signature-box-header {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #334155;
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 6px;
        margin-bottom: 8px;
    }

    .signature-stamp-area {
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .stamp-preview-img {
        max-width: 140px;
        max-height: 80px;
        object-fit: contain;
    }

    .signature-box-footer {
        font-size: 10px;
        color: #94a3b8;
        border-top: 1px solid #f1f5f9;
        padding-top: 6px;
    }

    /* Footer */
    .dn-footer {
        background: #ffffff;
        border-top: 1.5px solid #e2e8f0;
        padding: 14px 40px;
        text-align: center;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
        margin-top: auto;
        flex-shrink: 0;
    }
</style>

<div class="delivery-note-wrapper">
    <!-- Action Bar -->
    <div class="dn-top-bar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('invoices.show', $invoice) }}" class="btn-pro btn-pro-secondary">
                <i class="fas fa-arrow-left"></i> <span>Retour au document</span>
            </a>
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-1.5 rounded-pill" style="font-size: 12px;">
                <i class="fas fa-truck me-1"></i> Bon de Livraison officiel (Maroc)
            </span>
        </div>

        <div class="dn-top-actions">
            <!-- Download Dropdown -->
            <div class="dropdown">
                <button class="btn-pro btn-pro-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-pdf"></i>
                    <span>Télécharger PDF</span>
                    <i class="fas fa-chevron-down ms-1" style="font-size: 9px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                    <li>
                        <a class="pro-dropdown-item" href="{{ route('invoices.delivery-note.download', [$invoice, 'with_stamp' => 1]) }}">
                            <i class="fas fa-stamp text-success"></i>
                            <div>
                                <span class="fw-semibold">BL avec Cachet Officiel</span><br>
                                <small class="text-muted">Certifié avec tampon fournisseur</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="pro-dropdown-item" href="{{ route('invoices.delivery-note.download', [$invoice, 'with_stamp' => 0]) }}">
                            <i class="far fa-file-pdf text-muted"></i>
                            <div>
                                <span class="fw-semibold">BL sans Cachet</span><br>
                                <small class="text-muted">Pour émargement manuel</small>
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
                    <i class="fas fa-chevron-down ms-1" style="font-size: 9px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                    <li>
                        <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.delivery-note.print', [$invoice, 'with_stamp' => 1]) }}">
                            <i class="fas fa-stamp text-success"></i>
                            <div>
                                <span class="fw-semibold">Imprimer avec Cachet</span><br>
                                <small class="text-muted">Format A4 prêt pour signature</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.delivery-note.print', [$invoice, 'with_stamp' => 0]) }}">
                            <i class="fas fa-print text-muted"></i>
                            <div>
                                <span class="fw-semibold">Imprimer sans Cachet</span><br>
                                <small class="text-muted">Format standard</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Official Moroccan Delivery Note Document -->
    <div class="dn-card">
        <div class="dn-accent-bar"></div>

        <div class="dn-body">
            <!-- Header Row -->
            <div class="dn-header-row">
                <div class="dn-brand">
                    @if(setting('app_logo'))
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" class="dn-logo" alt="Logo">
                    @endif
                    <div>
                        <div class="dn-brand-name">{{ setting('company_name', setting('app_name', 'WINA SHOP')) }}</div>
                        <div class="dn-brand-sub">{{ setting('company_tagline', 'Matériel Audiovisuel Professionnel — Maroc') }}</div>
                    </div>
                </div>

                <div class="dn-badge-block">
                    <div class="dn-title-badge">BON DE LIVRAISON</div>
                    <div class="dn-number">N° {{ $invoice->delivery_note_number }}</div>
                    <div class="dn-meta-list">
                        <div><strong>Date d'émission :</strong> {{ $invoice->issued_at->format('d/m/Y') }}</div>
                        @if($invoice->order && $invoice->order->order_number)
                            <div><strong>Réf. Commande :</strong> {{ $invoice->order->order_number }}</div>
                        @endif
                        <div><strong>Réf. Document :</strong> {{ $invoice->display_number ?? $invoice->invoice_number }}</div>
                    </div>
                </div>
            </div>

            <!-- Client Banner (Full Width, Low Height) -->
            <div class="dn-client-banner">
                <div class="dn-client-header">
                    <span class="dn-client-role">
                        <i class="fas fa-map-marker-alt me-1"></i>Destinataire / Lieu de Livraison
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

            <!-- Products Table (Quantities + HT Price) -->
            <table class="dn-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">N°</th>
                        <th style="width: 15%;">Réf / SKU</th>
                        <th style="width: 35%;">Désignation des Articles</th>
                        <th style="width: 12%; text-align: right;">P.U. HT</th>
                        <th style="width: 8%; text-align: center;">Qté Cde</th>
                        <th style="width: 10%; text-align: center;">Qté Livrée</th>
                        <th style="width: 15%; text-align: right;">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td style="text-align: center; color: #94a3b8; font-size: 11px;">{{ $i + 1 }}</td>
                        <td style="font-family: monospace; font-size: 11px; color: #475569;">
                            {{ $item->product_sku ?: 'ART-' . str_pad($item->product_id ?? $item->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                        </td>
                        <td style="text-align: right; font-size: 12px;">{{ $item->formatted_unit_price_ht }}</td>
                        <td style="text-align: center; font-weight: 600; color: #64748b;">{{ $item->quantity }}</td>
                        <td style="text-align: center; font-weight: 700; color: #0284c7;">
                            {{ $item->quantity }} <span class="check-box" title="Marchandise vérifiée">✓</span>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">{{ $item->formatted_total_price_ht }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary & Legal Mentions -->
            <div class="dn-summary-row">
                <div class="dn-summary-left">
                    <div class="legal-notice-card">
                        <strong><i class="fas fa-shield-alt me-1 text-primary"></i>Conformité de livraison (Loi 16-99 & Code de Commerce Marocain) :</strong><br>
                        Le présent bon de livraison atteste de la remise effective et de la réception conforme des marchandises susmentionnées. 
                        Les marchandises voyagent aux risques et périls du destinataire. Le client est tenu de vérifier l'état des colis et la conformité des produits dès la livraison. 
                        Toute réserve ou avarie doit être constatée sur ce bon et confirmée par écrit dans un délai de <strong>48 heures</strong> (Article 347 du Code de Commerce Marocain).
                    </div>

                    @if($invoice->notes)
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; font-size:11px; color:#475569;">
                        <strong>Observations :</strong> {{ $invoice->notes }}
                    </div>
                    @endif
                </div>

                <div class="dn-summary-right">
                    <div class="dn-totals-card">
                        <div class="dn-total-row">
                            <span class="dn-total-label">Sous-total HT</span>
                            <span class="dn-total-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="dn-total-row">
                            <span class="dn-total-label">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="dn-total-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->shipping_cost > 0)
                        <div class="dn-total-row">
                            <span class="dn-total-label">Frais de livraison</span>
                            <span class="dn-total-val">{{ $invoice->formatted_shipping_cost }}</span>
                        </div>
                        @endif
                        <div class="dn-grand-row">
                            <span class="dn-grand-label">Total TTC</span>
                            <span class="dn-grand-val">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dual Signature & Discharge Section (Cadre d'émargement obligatoire) -->
            <div class="dn-signatures-section">
                <!-- Supplier / Carrier Stamp & Signature -->
                <div class="signature-box" style="min-height: 200px;">
                    <div class="signature-box-header">
                        <span><i class="fas fa-warehouse me-1 text-primary"></i>Pour l'Expéditeur / Livreur</span>
                        <span>Délivré par</span>
                    </div>
                    <div class="signature-stamp-area" style="min-height: 125px;">
                        @if(($withStamp ?? true) && setting('company_stamp'))
                            <img src="{{ asset('storage/' . setting('company_stamp')) }}" 
                                 class="stamp-preview-img" 
                                 alt="Cachet Fournisseur">
                        @else
                            <span class="text-muted small fst-italic">Cachet & Signature Société</span>
                        @endif
                    </div>
                    <div class="signature-box-footer">
                        Date : {{ $invoice->issued_at->format('d/m/Y') }}
                    </div>
                </div>

                <!-- Recipient / Customer Signature & Discharge (Spacious free space) -->
                <div class="signature-box" style="min-height: 200px;">
                    <div class="signature-box-header">
                        <span><i class="fas fa-user-check me-1 text-success"></i>Pour le Destinataire / Réceptionnaire</span>
                        <span>Décharge</span>
                    </div>
                    <div class="signature-stamp-area" style="flex-direction: column; justify-content: flex-start; align-items: stretch; min-height: 125px; text-align: left; padding: 2px 4px;">
                        <span class="text-muted" style="font-size: 11px; font-weight: 500;">
                            Mention manuscrite : <strong>"Bon pour réception conforme"</strong>
                        </span>
                        <div style="flex: 1; width: 100%; min-height: 85px; margin-top: 6px; border: 1.5px dashed #cbd5e1; border-radius: 6px; background: #fafafa; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 10.5px; color: #94a3b8; font-style: italic;">
                                Espace réservé au client (Cachet, Signature, Date, Réserves)
                            </span>
                        </div>
                    </div>
                    <div class="signature-box-footer d-flex justify-content-between">
                        <span>Date & Heure : ..............................</span>
                        <span>Nom & Signature</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="dn-footer">
            {{ setting('company_name', setting('app_name')) }} &bull; 
            ICE : {{ setting('company_tax_id', setting('company_ice', '002938475000089')) }} &bull; 
            IF : {{ setting('company_fiscal_id', setting('company_if', 'N/A')) }} &bull; 
            RC : {{ setting('company_registry_id', setting('company_rc', 'N/A')) }} &bull; 
            Patente : {{ setting('company_patente', 'N/A') }}
        </div>
    </div>
</div>
@endsection
