@extends('layouts.app')

@section('title', 'Facturation & Devis')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h1 class="brand-title">Facturation & Devis</h1>
                <p class="brand-subtitle">Gérez vos factures officielles, devis proforma et suivez vos règlements clients</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('export.invoices') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            <a href="{{ route('invoices.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
                <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">Nouveau document</span><span class="d-sm-none">Créer</span>
            </a>
        </div>
    </div>

    <!-- Segmented Navigation Tabs (Executive Quick Filters) -->
    <div class="invoice-nav-tabs">
        <a href="{{ route('invoices.index') }}" 
           class="invoice-nav-tab {{ !request('type') && !request('status') ? 'active' : '' }}">
            <i class="fas fa-layer-group"></i>
            <span>Tous les documents</span>
            <span class="tab-badge">{{ ($stats['total_invoices'] ?? 0) + ($stats['total_quotes'] ?? 0) }}</span>
        </a>
        <a href="{{ route('invoices.index', ['type' => 'invoice']) }}" 
           class="invoice-nav-tab {{ request('type') === 'invoice' && !request('status') ? 'active' : '' }}">
            <i class="fas fa-file-invoice text-primary"></i>
            <span>Factures</span>
            <span class="tab-badge">{{ $stats['total_invoices'] ?? 0 }}</span>
        </a>
        <a href="{{ route('invoices.index', ['type' => 'quote']) }}" 
           class="invoice-nav-tab {{ request('type') === 'quote' && !request('status') ? 'active' : '' }}">
            <i class="fas fa-file-signature text-warning"></i>
            <span>Devis</span>
            <span class="tab-badge">{{ $stats['total_quotes'] ?? 0 }}</span>
        </a>
        <a href="{{ route('invoices.index', ['status' => 'unpaid']) }}" 
           class="invoice-nav-tab {{ request('status') === 'unpaid' ? 'active' : '' }}">
            <i class="fas fa-clock text-warning"></i>
            <span>Impayés</span>
            <span class="tab-badge badge-unpaid">{{ $stats['unpaid_count'] ?? 0 }}</span>
        </a>
        <a href="{{ route('invoices.index', ['status' => 'paid']) }}" 
           class="invoice-nav-tab {{ request('status') === 'paid' ? 'active' : '' }}">
            <i class="fas fa-check-circle text-success"></i>
            <span>Payés</span>
            <span class="tab-badge badge-paid">{{ $stats['paid_count'] ?? 0 }}</span>
        </a>
    </div>

    <!-- KPI Metric Stat Cards (Row of 4) -->
    <div class="row g-3 mb-4">
        <!-- Total Factures / Devis -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('invoices.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') && !request('type') && !request('search') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Documents</div>
                        <div class="stat-kpi-value text-dark" id="kpi-total-invoices">{{ number_format(($stats['total_invoices'] ?? 0) + ($stats['total_quotes'] ?? 0), 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Factures & Devis</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Règlements Encaissés (Payé) -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('invoices.index', ['status' => 'paid']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'paid' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Encaissé (Payé)</div>
                        <div class="stat-kpi-value text-success" id="kpi-paid-amount">{{ currency($stats['paid_amount'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-check-circle text-success me-1"></i>Règlements reçus</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- En Attente / Impayé -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('invoices.index', ['status' => 'unpaid']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'unpaid' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">En Attente (Impayé)</div>
                        <div class="stat-kpi-value text-warning" id="kpi-unpaid-amount">{{ currency($stats['unpaid_amount'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-clock text-warning me-1"></i>En attente de paiement</div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Chiffre d'Affaires Global -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('invoices.index', ['type' => 'invoice']) }}" class="stat-kpi-card text-decoration-none {{ request('type') === 'invoice' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Volume Facturé</div>
                        <div class="stat-kpi-value" id="kpi-total-revenue" style="color: #059669;">{{ currency($stats['total_revenue'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted"><i class="fas fa-chart-line text-success me-1"></i>Factures hors annulation</div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('invoices.index') }}" id="invoiceFilterForm">
            <!-- Row 1 : Champ Recherche + Actions Principales (Filtrer + Reset + Compteur) -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par N° document, client, email..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $activeFilterKeys = ['type', 'status', 'with_stamp', 'payment_method', 'start_date', 'end_date'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#invoiceFiltersCollapse" 
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Actions Desktop Principales (Filtrer + Reset + Compteur) (d-none d-md-flex) -->
                <div class="d-none d-md-flex align-items-center gap-2">
                    <button type="submit" class="btn-brand-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5" title="Appliquer la recherche et les filtres">
                        <i class="fas fa-filter"></i> <span>Filtrer</span>
                    </button>
                    @if(request('search') || $hasActiveFilters)
                        <a href="{{ route('invoices.index') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        <i class="fas fa-file-invoice text-primary me-1.5"></i>{{ $invoices->total() }} document(s)
                    </span>
                </div>
            </div>

            <!-- Row 2 : Filtres Déroulants Épurés (Sans emojis, Style Pro) -->
            <div class="collapse d-md-block {{ $hasActiveFilters ? 'show' : '' }} mt-2.5 pt-2.5 border-top" id="invoiceFiltersCollapse">
                <div class="row g-2 align-items-center">
                    <!-- Type de document -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <select name="type" class="form-select form-select-sm w-100" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="invoice" {{ request('type') === 'invoice' ? 'selected' : '' }}>Factures</option>
                            <option value="quote" {{ request('type') === 'quote' ? 'selected' : '' }}>Devis</option>
                        </select>
                    </div>

                    <!-- Statut de paiement -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <select name="status" class="form-select form-select-sm w-100" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payé</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Impayé</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Règlement partiel</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>

                    <!-- Cachet -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <select name="with_stamp" class="form-select form-select-sm w-100" onchange="this.form.submit()">
                            <option value="">Tous les documents</option>
                            <option value="1" {{ request('with_stamp') === '1' ? 'selected' : '' }}>Avec Cachet officiel</option>
                            <option value="0" {{ request('with_stamp') === '0' ? 'selected' : '' }}>Sans Cachet</option>
                        </select>
                    </div>

                    <!-- Mode de paiement -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <select name="payment_method" class="form-select form-select-sm w-100" onchange="this.form.submit()">
                            <option value="">Tous les modes</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Carte bancaire</option>
                            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>Autre mode</option>
                        </select>
                    </div>

                    <!-- Date début -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="input-group input-group-sm w-100">
                            <span class="input-group-text bg-light text-muted border-end-0 fw-semibold px-2" style="font-size: 0.75rem;">Du</span>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm border-start-0" title="Date de début" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Date fin -->
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="input-group input-group-sm w-100">
                            <span class="input-group-text bg-light text-muted border-end-0 fw-semibold px-2" style="font-size: 0.75rem;">Au</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm border-start-0" title="Date de fin" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Bouton Appliquer & Reset sur Mobile (d-md-none) -->
                    <div class="col-12 d-md-none mt-2 pt-2 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-brand-primary flex-grow-1 justify-content-center">
                            <i class="fas fa-filter me-1"></i> Appliquer les filtres
                        </button>
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-brand-light flex-shrink-0" title="Réinitialiser">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges with Quick Removal Pills -->
    @if(request('search') || $hasActiveFilters)
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3 px-1">
            <span class="text-muted small fw-semibold">
                <i class="fas fa-filter text-primary me-1"></i> Filtres appliqués :
            </span>

            @if(request('search'))
                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Recherche : "{{ request('search') }}" <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('type'))
                <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Type : {{ request('type') === 'quote' ? 'Devis' : 'Facture' }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('status'))
                @php
                    $statusNames = ['paid' => 'Payé', 'unpaid' => 'Impayé', 'partial' => 'Partiel', 'cancelled' => 'Annulé'];
                @endphp
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Statut : {{ $statusNames[request('status')] ?? request('status') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('with_stamp') !== null && request('with_stamp') !== '')
                <a href="{{ request()->fullUrlWithQuery(['with_stamp' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Cachet : {{ request('with_stamp') == '1' ? 'Avec Cachet' : 'Sans Cachet' }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('payment_method'))
                <a href="{{ request()->fullUrlWithQuery(['payment_method' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Mode : {{ ucfirst(str_replace('_', ' ', request('payment_method'))) }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            @if(request('start_date') || request('end_date'))
                <a href="{{ request()->fullUrlWithQuery(['start_date' => null, 'end_date' => null]) }}" class="badge bg-white text-dark border rounded-pill px-2.5 py-1.5 text-decoration-none d-inline-flex align-items-center gap-1 shadow-2xs" title="Supprimer ce filtre">
                    Période : {{ request('start_date') }} au {{ request('end_date') }} <i class="fas fa-times text-muted ms-0.5"></i>
                </a>
            @endif

            <a href="{{ route('invoices.index') }}" class="small text-danger fw-semibold text-decoration-none ms-1">
                Effacer tout
            </a>
        </div>
    @endif

    <!-- Mobile Invoices Container (Touch First, d-block d-md-none) -->
    <div class="mobile-orders-container d-block d-md-none">
        <div class="d-flex align-items-center justify-content-between px-1 mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-dark" style="font-size: 0.92rem; letter-spacing: -0.01em;">
                    <i class="fas fa-file-invoice text-primary me-1.5 opacity-75"></i>Documents récents
                </span>
                <span class="badge rounded-pill bg-primary-subtle text-primary fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
                    {{ $invoices->total() }}
                </span>
            </div>
            @if(request('search') || $hasActiveFilters)
                <a href="{{ route('invoices.index') }}" class="small text-danger fw-semibold text-decoration-none" style="font-size: 0.75rem;">
                    <i class="fas fa-times me-0.5"></i>Effacer filtres
                </a>
            @endif
        </div>

        @forelse($invoices as $invoice)
            @include('invoices.partials.mobile-invoice-card', ['invoice' => $invoice])
        @empty
            <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm mb-4">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px;">
                    <i class="fas fa-file-invoice-dollar text-muted"></i>
                </div>
                <h6 class="fw-bold text-dark">Aucun document trouvé</h6>
                <p class="text-muted small mb-0">Aucune facture ou devis ne correspond à vos critères de recherche.</p>
                @if(request('search') || $hasActiveFilters)
                    <a href="{{ route('invoices.index') }}" class="btn-brand-primary btn-sm mt-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                    </a>
                @endif
            </div>
        @endforelse

        <!-- Mobile Pagination Footer -->
        @if($invoices->hasPages())
            <div class="bg-white p-3 rounded-4 border shadow-sm mt-3 mb-4">
                <div class="text-muted small text-center mb-2">
                    Affichage de <span class="fw-semibold text-dark">{{ $invoices->firstItem() ?? 0 }}</span> à <span class="fw-semibold text-dark">{{ $invoices->lastItem() ?? 0 }}</span> sur <span class="fw-semibold text-dark">{{ $invoices->total() }}</span>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $invoices->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Desktop Table (Spacious 6-Column Pro Layout, d-none d-md-block) -->
    <div class="brand-table-card invoices-table-card d-none d-md-block">
        <div class="table-responsive">
            <table class="brand-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 20%; padding-left: 1.25rem;">Document</th>
                        <th style="width: 23%;">Client</th>
                        <th style="width: 16%;">Émission & Échéance</th>
                        <th class="text-end" style="width: 14%;">Montant Total TTC</th>
                        <th class="text-center" style="width: 15%;">Règlement & Mode</th>
                        <th class="text-end" style="width: 12%; padding-right: 1.25rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <!-- 1. Document (N°, Type & Cachet) -->
                        <td style="padding-left: 1.25rem;">
                            <div class="d-flex align-items-center gap-1.5 text-nowrap">
                                <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-dark text-decoration-none font-monospace text-nowrap" style="font-size: 0.9rem;" title="Consulter la fiche">
                                    #{{ $invoice->display_number ?? $invoice->invoice_number }}
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-light border-0 p-1 text-muted" 
                                        style="font-size: 0.7rem; line-height: 1;"
                                        onclick="copyInvoiceNumber('{{ $invoice->display_number ?? $invoice->invoice_number }}', this)"
                                        title="Copier le N°">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                @if($invoice->isQuote())
                                    <span class="badge-doc-quote">
                                        <i class="fas fa-file-signature" style="font-size: 0.6rem;"></i> DEVIS
                                    </span>
                                @else
                                    <span class="badge-doc-invoice">
                                        <i class="fas fa-file-invoice" style="font-size: 0.6rem;"></i> FACTURE
                                    </span>
                                @endif

                                @if($invoice->with_stamp)
                                    <span class="badge-doc-stamp" title="Document officiel certifié avec cachet">
                                        <i class="fas fa-stamp" style="font-size: 0.6rem;"></i> Cachet
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- 2. Client (Avatar, Nom, Contact, ICE) -->
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="invoice-client-avatar">
                                    {{ strtoupper(substr($invoice->customer_name ?: 'C', 0, 1)) }}
                                </div>
                                <div class="min-w-0" style="max-width: 220px;">
                                    <div class="fw-semibold text-dark text-truncate" style="font-size: 0.88rem;" title="{{ $invoice->customer_name ?: 'Client sans nom' }}">
                                        {{ $invoice->customer_name ?: 'Client sans nom' }}
                                    </div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.74rem;" title="{{ $invoice->customer_email ?: ($invoice->customer_phone ?: 'Aucun contact') }}">
                                        @if($invoice->customer_email)
                                            <i class="far fa-envelope me-1 opacity-75"></i>{{ $invoice->customer_email }}
                                        @elseif($invoice->customer_phone)
                                            <i class="fas fa-phone me-1 opacity-75"></i>{{ $invoice->customer_phone }}
                                        @else
                                            <span class="opacity-50">Aucun contact</span>
                                        @endif
                                    </div>
                                    @if(!empty($invoice->ice))
                                        <div class="text-muted small text-truncate mt-0.5" style="font-size: 0.68rem;">
                                            <span class="badge bg-light text-secondary border px-1 py-0">ICE: {{ $invoice->ice }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- 3. Dates (Émission & Échéance) -->
                        <td>
                            <div class="text-dark fw-semibold" style="font-size: 0.85rem;">
                                <i class="far fa-calendar-alt text-muted me-1"></i>{{ $invoice->issued_at->format('d/m/Y') }}
                            </div>
                            <div class="mt-1">
                                @if($invoice->isOverdue())
                                    <div class="badge-overdue-alert" title="Paiement en retard">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Échue le {{ $invoice->due_date->format('d/m/Y') }}</span>
                                    </div>
                                @elseif($invoice->due_date)
                                    <div class="text-muted" style="font-size: 0.74rem;">
                                        <i class="far fa-clock text-muted me-1"></i>Échéance : {{ $invoice->due_date->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="text-muted opacity-75" style="font-size: 0.72rem;">
                                        À {{ $invoice->issued_at->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- 4. Montant Total TTC -->
                        <td class="text-end">
                            <div class="fw-bold text-dark invoice-amount-value">
                                {{ $invoice->formatted_total_amount }}
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">
                                @if($invoice->payment_status === 'partial')
                                    <span class="text-danger fw-semibold">Reste : {{ $invoice->formatted_remaining_balance }}</span>
                                @elseif($invoice->items && $invoice->items->count())
                                    <span>{{ $invoice->items->count() }} article{{ $invoice->items->count() > 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- 5. Règlement & Mode (Interactive Dropdown + Method) -->
                        <td class="text-center">
                            @php
                                $pStatus = strtolower($invoice->payment_status);
                                $pClass = match($pStatus) {
                                    'paid' => 'success',
                                    'unpaid' => 'warning',
                                    'partial' => 'info',
                                    'cancelled' => 'danger',
                                    default => 'warning'
                                };
                                $pText = match($pStatus) {
                                    'paid' => 'Payé',
                                    'unpaid' => 'Impayé',
                                    'partial' => 'Partiel',
                                    'cancelled' => 'Annulé',
                                    default => ucfirst($invoice->payment_status)
                                };
                                $pDot = match($pStatus) {
                                    'paid' => 'green',
                                    'unpaid' => 'amber',
                                    'partial' => 'blue',
                                    'cancelled' => 'red',
                                    default => 'amber'
                                };
                            @endphp
                            <div class="dropdown d-inline-block">
                                <button type="button" 
                                        class="brand-badge brand-badge-btn {{ $pClass }} dropdown-toggle" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        id="dt-pay-pill-{{ $invoice->id }}"
                                        title="Changer le statut de paiement">
                                    <span class="status-dot dot-{{ $pDot }}" id="dt-pay-dot-{{ $invoice->id }}"></span>
                                    <span id="dt-pay-label-{{ $invoice->id }}">{{ $pText }}</span>
                                    <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.6rem;"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="dt-pay-pill-{{ $invoice->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de paiement</li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'paid' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'paid')">
                                            <span><span class="status-dot dot-green me-1.5"></span> Payé</span>
                                            @if($pStatus === 'paid') <i class="fas fa-check text-success small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'unpaid' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'unpaid')">
                                            <span><span class="status-dot dot-amber me-1.5"></span> Impayé</span>
                                            @if($pStatus === 'unpaid') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $pStatus === 'partial' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'partial')">
                                            <span><span class="status-dot dot-blue me-1.5"></span> Partiel</span>
                                            @if($pStatus === 'partial') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $pStatus === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateInvoice({{ $invoice->id }}, 'cancelled')">
                                            <span><span class="status-dot dot-red me-1.5"></span> Annulé</span>
                                            @if($pStatus === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Mode de règlement sous le statut -->
                            <div class="mt-1">
                                @php
                                    $pm = strtolower($invoice->payment_method ?? '');
                                @endphp
                                @if($pm === 'cash')
                                    <span class="text-muted" style="font-size: 0.74rem;" title="Paiement en espèces">
                                        <i class="fas fa-money-bill-wave text-success me-1"></i>Espèces
                                    </span>
                                @elseif($pm === 'card')
                                    <span class="text-muted" style="font-size: 0.74rem;" title="Carte bancaire">
                                        <i class="fas fa-credit-card text-primary me-1"></i>Carte
                                    </span>
                                @elseif($pm === 'bank_transfer')
                                    <span class="text-muted" style="font-size: 0.74rem;" title="Virement bancaire">
                                        <i class="fas fa-university text-info me-1"></i>Virement
                                    </span>
                                @else
                                    <span class="text-muted opacity-75" style="font-size: 0.74rem;" title="{{ $invoice->payment_method }}">
                                        {{ ucfirst(str_replace('_', ' ', $invoice->payment_method ?: 'Non spécifié')) }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- 6. Actions (Aperçu + Email + Menu "...") -->
                        <td class="text-end" style="padding-right: 1.25rem;">
                            <div class="action-buttons-group">
                                <!-- Aperçu Rapide Direct -->
                                <button type="button" 
                                        class="action-btn action-btn-view" 
                                        title="Aperçu rapide interactif" 
                                        onclick="openInvoicePreview('{{ $invoice->id }}', '{{ $invoice->display_number ?? $invoice->invoice_number }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }})">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Envoyer par Email Direct -->
                                <button type="button" 
                                        class="action-btn action-btn-email" 
                                        title="Envoyer par email au client"
                                        onclick="openInvoiceEmailModal({{ $invoice->id }}, '{{ addslashes($invoice->display_number ?? $invoice->invoice_number) }}', '{{ addslashes($invoice->customer_name ?? '') }}', '{{ addslashes($invoice->customer_email ?? '') }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }}, '{{ $invoice->formatted_total_amount }}')">
                                    <i class="fas fa-paper-plane"></i>
                                </button>

                                <!-- Menu Déroulant Plus d'Actions "..." -->
                                <div class="dropdown d-inline-block">
                                    <button class="action-btn action-btn-more" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            data-bs-boundary="viewport"
                                            aria-expanded="false" 
                                            title="Plus d'actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end invoice-actions-dropdown">
                                        <li class="dropdown-header text-uppercase fw-bold">Aperçu & Consultation</li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.show', $invoice) }}">
                                                <i class="fas fa-external-link-alt text-primary" style="width: 16px;"></i>
                                                <span>Consulter la fiche complète</span>
                                            </a>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item" onclick="openInvoicePreview('{{ $invoice->id }}', '{{ $invoice->display_number ?? $invoice->invoice_number }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }})">
                                                <i class="fas fa-eye text-info" style="width: 16px;"></i>
                                                <span>Aperçu rapide du document</span>
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="dropdown-header text-uppercase fw-bold">Documents PDF & Impression</li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp text-indigo" style="color: #6366f1; width: 16px;"></i>
                                                <span>Facture avec Cachet</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 16px;"></i>
                                                <span>Facture standard</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                                <i class="fas fa-stamp text-warning" style="width: 16px;"></i>
                                                <span>Devis avec Cachet</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                                <i class="far fa-file-pdf text-muted" style="width: 16px;"></i>
                                                <span>Devis standard</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => $invoice->isQuote() ? 'devis' : 'facture', 'with_stamp' => $invoice->with_stamp ? 1 : 0]) }}">
                                                <i class="fas fa-print text-secondary" style="width: 16px;"></i>
                                                <span>Imprimer le document</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="dropdown-header text-uppercase fw-bold">Gestion & Modification</li>
                                        <li>
                                            <button type="button" class="dropdown-item" onclick="openInvoiceEmailModal({{ $invoice->id }}, '{{ addslashes($invoice->display_number ?? $invoice->invoice_number) }}', '{{ addslashes($invoice->customer_name ?? '') }}', '{{ addslashes($invoice->customer_email ?? '') }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $invoice->with_stamp ? 'true' : 'false' }}, '{{ $invoice->formatted_total_amount }}')">
                                                <i class="fas fa-paper-plane text-primary" style="width: 16px;"></i>
                                                <span>Envoyer par email</span>
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.edit', $invoice) }}">
                                                <i class="fas fa-pencil-alt text-dark" style="width: 16px;"></i>
                                                <span>Modifier le document</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" 
                                                  action="{{ route('invoices.destroy', $invoice) }}" 
                                                  class="m-0 p-0"
                                                  data-confirm-delete="true"
                                                  data-item-type="document"
                                                  data-item-name="{{ ($invoice->isQuote() ? 'Devis #' : 'Facture #') . ($invoice->display_number ?? $invoice->invoice_number) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt text-danger" style="width: 16px;"></i>
                                                    <span>Supprimer le document</span>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-file-invoice-dollar text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucun document trouvé</h5>
                                <p class="text-muted">Aucune facture ou devis ne correspond à vos critères de recherche.</p>
                                @if(request('search') || $hasActiveFilters)
                                    <a href="{{ route('invoices.index') }}" class="btn-brand-primary btn-sm mt-2">
                                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Affichage de {{ $invoices->firstItem() ?? 0 }} à {{ $invoices->lastItem() ?? 0 }} sur {{ $invoices->total() }} documents
            </span>
            <div>
                {{ $invoices->links() }}
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Preview Modal -->
    <div class="modal fade" id="invoicePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen-sm-down modal-dialog-centered" style="max-width: 960px;">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);">
                <div class="modal-header py-2.5 px-3 bg-dark text-white border-0">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center w-100 gap-2">
                        <!-- Top row on mobile: Title & Close -->
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-truncate me-2" id="previewModalTitle" style="font-size: 14px; max-width: 260px;">Aperçu du document</span>
                            <button type="button" class="btn-close btn-close-white d-md-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Action controls: Type toggle, Stamp, Print & Download -->
                        <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-2 flex-wrap">
                            <div class="d-flex align-items-center gap-1.5">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-light px-2.5" id="previewBtnFacture" onclick="setPreviewDocType('facture')">Facture</button>
                                    <button type="button" class="btn btn-sm btn-outline-light px-2.5" id="previewBtnDevis" onclick="setPreviewDocType('devis')">Devis</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-light px-2.5" id="previewBtnStamp" onclick="togglePreviewStamp()">
                                    <i class="fas fa-stamp me-1"></i> <span id="previewStampLabel">Cachet</span>
                                </button>
                            </div>

                            <div class="d-flex align-items-center gap-1.5">
                                <button type="button" class="btn btn-sm btn-info text-white px-2.5" onclick="openEmailFromPreview()"><i class="fas fa-paper-plane me-1"></i> Email</button>
                                <button type="button" class="btn btn-sm btn-light px-2.5" onclick="printFromPreview()"><i class="fas fa-print me-1"></i> Imprimer</button>
                                <a href="#" id="previewDownloadLink" class="btn btn-sm btn-danger px-2.5"><i class="fas fa-download me-1"></i> PDF</a>
                                <button type="button" class="btn-close btn-close-white ms-2 d-none d-md-inline-block" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-0" style="background: #cbd5e1; height: 75vh; max-height: calc(100vh - 105px);">
                    <iframe id="previewIframe" src="about:blank" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'Envoi par Email (Devis & Factures) -->
    @include('invoices.partials.email-modal')
@endsection

@push('scripts')
<script>
    /**
     * Copy invoice number helper
     */
    function copyInvoiceNumber(num, btn) {
        if (!num) return;
        const textToCopy = num.toString().trim();
        const originalHtml = btn.innerHTML;

        const showSuccess = function() {
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 1500);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(textToCopy).then(showSuccess).catch(function() {
                fallbackCopy(textToCopy, showSuccess);
            });
        } else {
            fallbackCopy(textToCopy, showSuccess);
        }
    }

    function fallbackCopy(text, callback) {
        const temp = document.createElement('textarea');
        temp.value = text;
        temp.style.position = 'fixed';
        temp.style.left = '-9999px';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        try {
            document.execCommand('copy');
            if (callback) callback();
        } catch (e) {
            console.error('Copy failed:', e);
        }
        document.body.removeChild(temp);
    }

    /**
     * Quick In-Place Payment Status Updater for Invoices
     */
    function quickUpdateInvoice(invoiceId, statusValue) {
        if (!invoiceId || !statusValue) return;

        const dtPill = document.getElementById(`dt-pay-pill-${invoiceId}`);
        const mobPill = document.getElementById(`mob-pay-pill-${invoiceId}`);

        if (dtPill) {
            dtPill.style.opacity = '0.6';
            dtPill.disabled = true;
        }
        if (mobPill) {
            mobPill.style.opacity = '0.6';
            mobPill.disabled = true;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(`/invoices/${invoiceId}/quick-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_status: statusValue
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.invoice) {
                updateInvoiceUI(invoiceId, data.invoice);

                if (data.stats) {
                    updateInvoiceKPIStats(data.stats);
                }

                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Mise à jour effectuée avec succès'
                    });
                }
            } else {
                throw new Error(data.message || 'Erreur lors de la mise à jour');
            }
        })
        .catch(error => {
            console.error('Quick update error:', error);
            const msg = error.message || 'Impossible de modifier le statut de paiement';
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    html: msg,
                    confirmButtonColor: '#dc2626'
                });
            } else {
                alert(msg);
            }
        })
        .finally(() => {
            if (dtPill) {
                dtPill.style.opacity = '1';
                dtPill.disabled = false;
            }
            if (mobPill) {
                mobPill.style.opacity = '1';
                mobPill.disabled = false;
            }
        });
    }

    /**
     * Dynamically update UI badges for desktop table and mobile cards
     */
    function updateInvoiceUI(invoiceId, invoice) {
        const st = (invoice.payment_status || '').toLowerCase();
        const stMap = {
            'paid': { label: 'Payé', dot: 'green', badgeClass: 'success', textClass: 'text-success', mobClass: 'paid', icon: 'fa-check-circle' },
            'unpaid': { label: 'Impayé', dot: 'amber', badgeClass: 'warning', textClass: 'text-warning', mobClass: 'pending', icon: 'fa-clock' },
            'partial': { label: 'Partiel', dot: 'blue', badgeClass: 'info', textClass: 'text-info', mobClass: 'pending', icon: 'fa-adjust' },
            'cancelled': { label: 'Annulé', dot: 'red', badgeClass: 'danger', textClass: 'text-danger', mobClass: 'failed', icon: 'fa-times-circle' }
        };
        const sInfo = stMap[st] || { label: invoice.status_label || st, dot: 'amber', badgeClass: 'warning', textClass: 'text-warning', mobClass: 'pending', icon: 'fa-clock' };

        // Desktop Pill
        const dtPill = document.getElementById(`dt-pay-pill-${invoiceId}`);
        if (dtPill) {
            dtPill.className = `brand-badge brand-badge-btn ${sInfo.badgeClass} dropdown-toggle`;
            const dtLabel = document.getElementById(`dt-pay-label-${invoiceId}`);
            if (dtLabel) dtLabel.textContent = sInfo.label;
            const dtDot = document.getElementById(`dt-pay-dot-${invoiceId}`);
            if (dtDot) dtDot.className = `status-dot dot-${sInfo.dot}`;

            const dtMenu = dtPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (dtMenu) {
                dtMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        // Mobile Pill
        const mobPill = document.getElementById(`mob-pay-pill-${invoiceId}`);
        if (mobPill) {
            mobPill.className = `mobile-order-pay-pill ${sInfo.mobClass} border-0 dropdown-toggle`;
            const mobLabel = document.getElementById(`mob-pay-label-${invoiceId}`);
            if (mobLabel) mobLabel.textContent = sInfo.label;
            const mobIcon = document.getElementById(`mob-pay-icon-${invoiceId}`);
            if (mobIcon) mobIcon.className = `fas ${sInfo.icon}`;

            const mobMenu = mobPill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (mobMenu) {
                mobMenu.querySelectorAll('.dropdown-item').forEach(item => {
                    const fn = item.getAttribute('onclick') || '';
                    const isSelected = fn.includes(`'${st}'`);
                    item.classList.toggle('active', isSelected);
                    const check = item.querySelector('.fa-check');
                    if (check) check.remove();
                    if (isSelected) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${sInfo.textClass} small"></i>`);
                    }
                });
            }
        }

        initStatusDropdowns();
    }

    /**
     * Update real-time KPI metrics
     */
    function updateInvoiceKPIStats(stats) {
        if (!stats) return;
        if (stats.paid_amount_formatted !== undefined) {
            const el = document.getElementById('kpi-paid-amount');
            if (el) el.textContent = stats.paid_amount_formatted;
        }
        if (stats.unpaid_amount_formatted !== undefined) {
            const el = document.getElementById('kpi-unpaid-amount');
            if (el) el.textContent = stats.unpaid_amount_formatted;
        }
        if (stats.total_revenue_formatted !== undefined) {
            const el = document.getElementById('kpi-total-revenue');
            if (el) el.textContent = stats.total_revenue_formatted;
        }
        if (stats.total_invoices !== undefined) {
            const el = document.getElementById('kpi-total-invoices');
            const totalDocs = (stats.total_invoices || 0) + (stats.total_quotes || 0);
            if (el) el.textContent = new Intl.NumberFormat('fr-FR').format(totalDocs);
        }
    }

    /**
     * Initialize dropdowns with reliable Popper positioning
     */
    function initStatusDropdowns() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) return;
        const triggers = document.querySelectorAll('.brand-badge-btn[data-bs-toggle="dropdown"], .mobile-order-pay-pill[data-bs-toggle="dropdown"], .mobile-act-btn.dropdown-toggle, .action-btn-more[data-bs-toggle="dropdown"]');
        triggers.forEach(el => {
            const current = bootstrap.Dropdown.getInstance(el);
            if (current) current.dispose();

            const isMobileCard = !!el.closest('.mobile-order-card');
            new bootstrap.Dropdown(el, {
                popperConfig: function(defaultBsPopperConfig) {
                    return {
                        ...defaultBsPopperConfig,
                        strategy: isMobileCard ? 'fixed' : 'absolute',
                        modifiers: [
                            ...(defaultBsPopperConfig?.modifiers || []),
                            {
                                name: 'preventOverflow',
                                options: {
                                    boundary: 'viewport',
                                    padding: 8
                                }
                            },
                            {
                                name: 'flip',
                                options: {
                                    fallbackPlacements: ['top', 'bottom']
                                }
                            }
                        ]
                    };
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initStatusDropdowns();
    });

    // Preview Modal Logic
    let currentPreviewId = null;
    let currentPreviewType = 'facture';
    let currentPreviewStamp = true;

    function openInvoicePreview(id, number, type, withStamp) {
        currentPreviewId = id;
        currentPreviewType = type || 'facture';
        currentPreviewStamp = withStamp !== false;
        document.getElementById('previewModalTitle').innerText = (currentPreviewType === 'devis' ? 'Devis #' : 'Facture #') + number;
        updatePreviewUI();
        const modalEl = document.getElementById('invoicePreviewModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    function setPreviewDocType(type) {
        currentPreviewType = type;
        updatePreviewUI();
    }

    function togglePreviewStamp() {
        currentPreviewStamp = !currentPreviewStamp;
        updatePreviewUI();
    }

    function updatePreviewUI() {
        if (!currentPreviewId) return;
        const btnF = document.getElementById('previewBtnFacture');
        const btnD = document.getElementById('previewBtnDevis');
        const btnS = document.getElementById('previewBtnStamp');
        const stampLabel = document.getElementById('previewStampLabel');

        if (currentPreviewType === 'facture') {
            btnF.className = 'btn btn-sm btn-light text-dark fw-bold';
            btnD.className = 'btn btn-sm btn-outline-light';
        } else {
            btnF.className = 'btn btn-sm btn-outline-light';
            btnD.className = 'btn btn-sm btn-danger fw-bold';
        }

        if (currentPreviewStamp) {
            btnS.className = 'btn btn-sm btn-success fw-bold';
            stampLabel.innerText = 'Avec Cachet';
        } else {
            btnS.className = 'btn btn-sm btn-outline-light';
            stampLabel.innerText = 'Sans Cachet';
        }

        const url = `/invoices/${currentPreviewId}/print?preview=1&as=${currentPreviewType}&with_stamp=${currentPreviewStamp ? 1 : 0}`;
        document.getElementById('previewIframe').src = url;

        const downloadUrl = `/invoices/${currentPreviewId}/download?as=${currentPreviewType}&with_stamp=${currentPreviewStamp ? 1 : 0}`;
        document.getElementById('previewDownloadLink').href = downloadUrl;
    }

    function printFromPreview() {
        const iframe = document.getElementById('previewIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }

    function openEmailFromPreview() {
        if (!currentPreviewId) return;
        const number = document.getElementById('previewModalTitle')?.innerText?.replace(/^(Devis|Facture)\s*#/i, '') || '';
        openInvoiceEmailModal(currentPreviewId, number, '', '', currentPreviewType, currentPreviewStamp, '');
    }
</script>
@endpush
