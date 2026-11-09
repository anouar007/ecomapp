@extends('layouts.app')

@section('title', 'Gestion des Crédits, Créances & Règlements Clients')

@push('styles')
<style>
/* ==========================================================================
   MOBILE-FIRST CLIENT CREDITS & TREASURY STYLES (ZERO HORIZONTAL SCROLL)
   ========================================================================== */
html, body, .main-content {
    overflow-x: hidden !important;
    max-width: 100% !important;
}
.container-fluid {
    overflow-x: hidden !important;
    max-width: 100% !important;
    padding-left: clamp(10px, 2vw, 24px) !important;
    padding-right: clamp(10px, 2vw, 24px) !important;
}
.chips-scroll-credit {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    align-items: center;
    overflow-x: visible;
}
.chip-credit {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.35rem 0.8rem;
    font-size: 0.78rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.chip-credit:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.chip-credit.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
}
.chip-credit.active .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Mobile Customer Card */
.credit-cust-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.credit-cust-card:active {
    transform: scale(0.995);
}

/* KPI Card */
.credit-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.15rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.credit-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
}
.kpi-icon-pill {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.contact-touch-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.65rem;
    border-radius: 9999px !important;
    font-size: 0.74rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #334155;
    text-decoration: none;
}
.contact-touch-pill:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.proof-thumbnail-btn {
    width: 46px;
    height: 46px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.proof-thumbnail-btn:hover {
    transform: scale(1.08);
}
.credit-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.credit-table {
    width: 100% !important;
    margin-bottom: 0 !important;
}
.credit-table th,
.credit-table td {
    vertical-align: middle;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-3 py-md-4">

    <!-- Top Executive Header (Mobile Responsive) -->
    <div class="card border-0 rounded-4 shadow-sm mb-3 mb-md-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-3.5 p-md-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
                        <i class="fas fa-wallet text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.7rem;">TRÉSORERIE & COMPTES</span>
                            <span class="text-white-50 small d-none d-sm-inline">• Créances & Avoirs Clients</span>
                        </div>
                        <h1 class="h4 h3-md fw-bold text-white mb-0 text-truncate">Crédits & Règlements Clients</h1>
                        <p class="text-white-50 small mb-0 mt-1 d-none d-md-block">Suivez en temps réel ce que les clients vous doivent (créances) et ce que le magasin leur doit (avoirs/acomptes).</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('client-credits.transactions') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 py-2 fw-semibold flex-fill flex-lg-grow-0 text-center shadow-xs">
                        <i class="fas fa-list-check me-1.5"></i> Grand Livre
                    </a>
                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm flex-fill flex-lg-grow-0" data-bs-toggle="modal" data-bs-target="#newPaymentModal">
                        <i class="fas fa-plus-circle me-1.5"></i> Nouveau Règlement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-3 mb-md-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-check fs-5"></i>
            <div class="small fw-semibold">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-3 mb-md-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-triangle-exclamation fs-5"></i>
            <div class="small fw-semibold">{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- 4 Perfectly Aligned Financial KPI Cards (2x2 on Mobile) -->
    <div class="row g-2.5 g-md-3 mb-3 mb-md-4">
        <!-- 1. Créances Clients -->
        <div class="col-6 col-xl-3">
            <div class="credit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Créances Clients</span>
                        <div class="kpi-icon-pill bg-danger-subtle text-danger">
                            <i class="fas fa-arrow-down-long"></i>
                        </div>
                    </div>
                    <div class="fs-5 fs-md-4 fw-bold text-danger mb-0">{{ currency($totalDebtors) }}</div>
                </div>
                <div class="pt-1.5 border-top mt-1.5 text-muted" style="font-size: 0.72rem;">
                    <span class="fw-bold text-dark">{{ $activeDebtorCount }}</span> compte(s) débiteur(s)
                </div>
            </div>
        </div>

        <!-- 2. Avoirs Clients -->
        <div class="col-6 col-xl-3">
            <div class="credit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Avoirs Clients</span>
                        <div class="kpi-icon-pill bg-info-subtle text-info">
                            <i class="fas fa-arrow-up-long"></i>
                        </div>
                    </div>
                    <div class="fs-5 fs-md-4 fw-bold text-info mb-0">{{ currency($totalCreditors) }}</div>
                </div>
                <div class="pt-1.5 border-top mt-1.5 text-muted" style="font-size: 0.72rem;">
                    <span class="fw-bold text-dark">{{ $activeCreditorCount }}</span> avoir(s) en cours
                </div>
            </div>
        </div>

        <!-- 3. Solde Net Trésorerie -->
        <div class="col-6 col-xl-3">
            <div class="credit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Solde Net</span>
                        <div class="kpi-icon-pill {{ $netBalance >= 0 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning' }}">
                            <i class="fas fa-scale-balanced"></i>
                        </div>
                    </div>
                    <div class="fs-5 fs-md-4 fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-warning' }} mb-0">
                        {{ currency($netBalance) }}
                    </div>
                </div>
                <div class="pt-1.5 border-top mt-1.5 text-muted" style="font-size: 0.72rem;">
                    {{ $netBalance >= 0 ? 'Balance en votre faveur' : 'Avoirs supérieurs' }}
                </div>
            </div>
        </div>

        <!-- 4. Règlements Encaissés du Mois -->
        <div class="col-6 col-xl-3">
            <div class="credit-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Encaissé ce mois</span>
                        <div class="kpi-icon-pill bg-success-subtle text-success">
                            <i class="fas fa-hand-holding-dollar"></i>
                        </div>
                    </div>
                    <div class="fs-5 fs-md-4 fw-bold text-success mb-0">{{ currency($collectedThisMonth) }}</div>
                </div>
                <div class="pt-1.5 border-top mt-1.5 text-muted" style="font-size: 0.72rem;">
                    Reçus en {{ now()->translatedFormat('M Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-3 g-md-4">
        
        <!-- Left Column: Customer Accounts Matrix (12 cols on desktop/laptop, 9 cols on wide XXL) -->
        <div class="col-12 col-xxl-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 mb-md-4">
                
                <!-- Filter Chips & Search Bar -->
                <div class="p-3 border-bottom bg-white">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2.5">
                        <!-- Fast Filter Touch Chips -->
                        <div class="chips-scroll-credit d-flex align-items-center gap-1.5 flex-wrap">
                            <a href="{{ route('client-credits.index', ['filter' => 'all']) }}" class="chip-credit {{ $filter === 'all' ? 'active' : '' }}">
                                <span>Tous</span>
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'debtors']) }}" class="chip-credit {{ $filter === 'debtors' ? 'active' : '' }}">
                                <i class="fas fa-arrow-down text-danger"></i>
                                <span>Débiteurs</span>
                                <span class="badge bg-danger text-white rounded-pill ms-1">{{ $activeDebtorCount }}</span>
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'creditors']) }}" class="chip-credit {{ $filter === 'creditors' ? 'active' : '' }}">
                                <i class="fas fa-arrow-up text-info"></i>
                                <span>Avoirs</span>
                                <span class="badge bg-info text-white rounded-pill ms-1">{{ $activeCreditorCount }}</span>
                            </a>
                            <a href="{{ route('client-credits.index', ['filter' => 'settled']) }}" class="chip-credit {{ $filter === 'settled' ? 'active' : '' }}">
                                <span>Soldés</span>
                            </a>
                        </div>

                        <!-- Search Box -->
                        <form method="GET" action="{{ route('client-credits.index') }}" class="d-flex align-items-center gap-1.5 flex-shrink-0">
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-start-0" placeholder="Rechercher client...">
                            </div>
                            @if(request('search'))
                                <a href="{{ route('client-credits.index', ['filter' => $filter]) }}" class="btn btn-sm btn-light border rounded-pill" title="Réinitialiser">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- 1. MOBILE USER CARDS VIEW (d-block d-lg-none) -->
                <div class="d-block d-lg-none p-2.5 space-y-2.5 bg-light bg-opacity-50">
                    @forelse($customers as $c)
                    <div class="credit-cust-card p-3 bg-white">
                        <!-- Top Header: Avatar + Name + Balance Badge -->
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2.5 min-w-0">
                                <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none text-truncate d-block" style="font-size: 0.92rem;">
                                        {{ $c->name }}
                                    </a>
                                    <div class="text-muted d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.72rem;">
                                        <span>{{ $c->customer_code ?? 'ID #'.$c->id }}</span>
                                        @if($c->city)
                                            <span>• {{ $c->city }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Balance Status Pill -->
                            <div class="flex-shrink-0">
                                @if($c->current_balance > 0.01)
                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.76rem;">
                                        <i class="fas fa-arrow-down me-1"></i>{{ currency($c->current_balance) }}
                                    </div>
                                @elseif($c->current_balance < -0.01)
                                    <div class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.76rem;">
                                        <i class="fas fa-arrow-up me-1"></i>Avoir: {{ currency(abs($c->current_balance)) }}
                                    </div>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">
                                        <i class="fas fa-check me-0.5"></i> Soldé
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Touch Contact Pills -->
                        <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                            @if($c->phone)
                            <a href="tel:{{ $c->phone }}" class="contact-touch-pill">
                                <i class="fas fa-phone text-success"></i>
                                <span>{{ $c->phone }}</span>
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->phone) }}" target="_blank" class="contact-touch-pill text-success" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                            @if($c->email)
                            <a href="mailto:{{ $c->email }}" class="contact-touch-pill">
                                <i class="fas fa-envelope text-primary"></i>
                                <span>Email</span>
                            </a>
                            @endif
                        </div>

                        <!-- Credit Limit Bar (if exists) -->
                        @if($c->credit_limit > 0)
                        @php
                            $mobUsage = min(100, max(0, ($c->current_balance / $c->credit_limit) * 100));
                            $mobBarColor = $mobUsage >= 90 ? 'bg-danger' : ($mobUsage >= 70 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="p-2 rounded-3 bg-light border mb-2.5">
                            <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
                                <span>Plafond : <strong class="text-dark">{{ currency($c->credit_limit) }}</strong></span>
                                <span class="fw-semibold text-dark">{{ round($mobUsage) }}%</span>
                            </div>
                            <div class="progress mt-1" style="height: 5px; border-radius: 99px;">
                                <div class="progress-bar {{ $mobBarColor }}" role="progressbar" style="width: {{ $mobUsage }}%;"></div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons Row (Touch Friendly) -->
                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                @php $lastMobTrx = $c->transactions()->first(); @endphp
                                @if($lastMobTrx)
                                    Dernier : {{ $lastMobTrx->payment_date->diffForHumans() }}
                                @else
                                    Aucun règlement
                                @endif
                            </span>

                            <div class="d-flex align-items-center gap-1.5">
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1 fw-semibold shadow-xs"
                                        onclick="openNewPaymentForCustomer({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->current_balance }})">
                                    <i class="fas fa-coins me-1"></i> Régler
                                </button>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-secondary"
                                        onclick="openLimitModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->credit_limit }})" title="Plafond">
                                    <i class="fas fa-sliders"></i>
                                </button>
                                <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-light border rounded-pill px-2 py-1 text-primary" title="Fiche 360">
                                    <i class="fas fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-wallet fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0 fw-semibold">Aucun client trouvé pour ce filtre.</p>
                    </div>
                    @endforelse
                </div>

                <!-- 2. DESKTOP TABULAR VIEW (d-none d-lg-block) -->
                <div class="credit-table-wrap d-none d-lg-block">
                    <table class="table align-middle mb-0 credit-table">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Client</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Statut du Solde</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Plafond Crédit</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">Dernier Règlement</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; min-width: 145px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($customers as $c)
                            <tr class="hover-bg-light">
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <a href="{{ route('customers.show', $c->id) }}" class="fw-bold text-dark text-decoration-none hover-primary" title="{{ $c->name }}" style="font-size: 0.88rem;">
                                                    {{ $c->name }}
                                                </a>
                                                @if($c->customer_code)
                                                    <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5 flex-shrink-0" style="font-size: 0.65rem;">
                                                        {{ $c->customer_code }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($c->phone || $c->city)
                                            <div class="d-flex align-items-center gap-1.5 text-muted small mt-0.5" style="font-size: 0.74rem;">
                                                @if($c->phone)
                                                    <span class="text-nowrap"><i class="fas fa-phone me-1 opacity-50"></i>{{ $c->phone }}</span>
                                                @endif
                                                @if($c->phone && $c->city)
                                                    <span class="opacity-50">•</span>
                                                @endif
                                                @if($c->city)
                                                    <span class="text-nowrap">{{ $c->city }}</span>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($c->current_balance > 0.01)
                                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.78rem;">
                                            <i class="fas fa-arrow-down"></i>
                                            <span>Doit : {{ currency($c->current_balance) }}</span>
                                        </div>
                                        <div class="text-muted mt-0.5" style="font-size: 0.7rem;">Créance client à encaisser</div>
                                    @elseif($c->current_balance < -0.01)
                                        <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill bg-info-subtle text-info border border-info-subtle fw-bold" style="font-size: 0.78rem;">
                                            <i class="fas fa-arrow-up"></i>
                                            <span>Avoir : {{ currency(abs($c->current_balance)) }}</span>
                                        </div>
                                        <div class="text-muted mt-0.5" style="font-size: 0.7rem;">Dû au client par le magasin</div>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.74rem;">
                                            <i class="fas fa-check me-1"></i> Compte Soldé (0 DH)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($c->credit_limit > 0)
                                        <div class="fw-semibold text-dark small">{{ currency($c->credit_limit) }}</div>
                                        @php
                                            $usage = min(100, max(0, ($c->current_balance / $c->credit_limit) * 100));
                                            $barColor = $usage >= 90 ? 'bg-danger' : ($usage >= 70 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress mt-1" style="height: 5px; width: 90px; max-width: 100%;">
                                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $usage }}%;"></div>
                                        </div>
                                        <div class="text-muted mt-0.5" style="font-size: 0.68rem;">{{ round($usage) }}% utilisé</div>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">Au comptant</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @php $lastTrx = $c->transactions()->first(); @endphp
                                    @if($lastTrx)
                                        <div class="small fw-semibold text-dark">{{ currency($lastTrx->amount) }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            {{ $lastTrx->payment_date->diffForHumans() }}
                                            @if($lastTrx->proof_image)
                                                <i class="fas fa-paperclip text-primary ms-1" title="Preuve attachée"></i>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">—</span>
                                    @endif
                                </td>
                                <td class="pe-3 py-2.5 text-end text-nowrap" style="min-width: 145px;">
                                    <div class="d-inline-flex align-items-center justify-content-end gap-1.5 text-nowrap">
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-2.5 py-1 shadow-xs fw-semibold text-nowrap"
                                                style="font-size: 0.75rem;"
                                                onclick="openNewPaymentForCustomer({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->current_balance }})"
                                                title="Enregistrer un règlement / avoir">
                                            <i class="fas fa-coins me-1"></i> Régler
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle text-secondary d-inline-flex align-items-center justify-content-center"
                                                style="width: 28px; height: 28px; padding: 0; font-size: 0.72rem;"
                                                onclick="openLimitModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ (float) $c->credit_limit }})"
                                                title="Modifier le plafond de crédit">
                                            <i class="fas fa-sliders"></i>
                                        </button>
                                        <a href="{{ route('customers.show', $c->id) }}" class="btn btn-sm btn-light border rounded-circle text-primary d-inline-flex align-items-center justify-content-center"
                                           style="width: 28px; height: 28px; padding: 0; font-size: 0.72rem;"
                                           title="Fiche client 360°">
                                            <i class="fas fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-wallet fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-semibold">Aucun client trouvé pour ce filtre.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                <div class="p-3 border-top bg-light">
                    {{ $customers->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Recent Payment Feed with Proofs (12 cols on desktop/laptop, 3 cols on wide XXL) -->
        <div class="col-12 col-xxl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 mb-md-4">
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Derniers Règlements</h6>
                        <span class="text-muted small">Avec justificatifs & preuves photos</span>
                    </div>
                    <a href="{{ route('client-credits.transactions') }}" class="btn btn-sm btn-light rounded-pill px-2.5 py-1 text-primary border" style="font-size: 0.75rem;">
                        Tout voir <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="list-group list-group-flush">
                    @forelse($recentTransactions as $trx)
                    <div class="list-group-item p-3 hover-bg-light">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1.5">
                            <div class="min-w-0">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <a href="{{ route('customers.show', $trx->customer_id) }}" class="fw-bold text-dark text-decoration-none small text-truncate">
                                        {{ $trx->customer->name ?? 'Client' }}
                                    </a>
                                    <span class="badge {{ $trx->type_badge_class }} border rounded-pill" style="font-size: 0.65rem;">
                                        {{ $trx->type_label }}
                                    </span>
                                </div>
                                <div class="text-muted mt-0.5" style="font-size: 0.7rem;">
                                    {{ $trx->payment_date->format('d/m/Y à H:i') }} • {{ $trx->payment_method_label }}
                                    @if($trx->reference_number)
                                        (Réf: {{ $trx->reference_number }})
                                    @endif
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }} small">
                                    {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                                </div>
                                <div class="text-muted font-monospace" style="font-size: 0.68rem;">
                                    Solde: {{ currency($trx->balance_after) }}
                                </div>
                            </div>
                        </div>

                        <!-- Proof of payment thumbnail if attached -->
                        @if($trx->proof_image_url)
                        <div class="d-flex align-items-center gap-2 mt-2 p-1.5 rounded-3 bg-light border">
                            <img src="{{ $trx->proof_image_url }}" alt="Preuve" class="proof-thumbnail-btn shadow-xs" 
                                 onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')">
                            <div class="small min-w-0">
                                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.74rem;">Preuve attachée</div>
                                <a href="javascript:void(0)" class="text-primary text-decoration-none" style="font-size: 0.7rem;"
                                   onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($trx->customer->name ?? '') }}')">
                                    <i class="fas fa-expand me-1"></i> Agrandir le document
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($trx->notes)
                        <div class="text-muted small fst-italic mt-1.5 bg-light p-1.5 rounded" style="font-size: 0.72rem;">
                            <i class="fas fa-comment-dots me-1 opacity-50"></i> {{ $trx->notes }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">
                        Aucun règlement enregistré récemment.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: ENREGISTRER UN RÈGLEMENT / OPÉRATION FINANCIÈRE  -->
<!-- ======================================================= -->
@include('client_credits.partials.payment_modal', ['allCustomersList' => $allCustomersList])

<!-- ======================================================= -->
<!-- MODAL: MODIFIER LE PLAFOND DE CRÉDIT CLIENT            -->
<!-- ======================================================= -->
<div class="modal fade" id="creditLimitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="creditLimitForm" method="POST" action="" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <h6 class="modal-title fw-bold mb-0 fs-6">Plafond de Crédit</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3">
                <div class="text-muted small mb-2" id="limitCustomerName">—</div>
                <label class="form-label small fw-bold text-dark mb-1">Montant Max Autorisé (DH)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" name="credit_limit" id="limitInput" class="form-control rounded-start-3 fw-bold" required>
                    <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                </div>
                <div class="form-text" style="font-size: 0.72rem;">0 = Aucun plafond (crédit non accordé par défaut).</div>
            </div>
            <div class="modal-footer bg-light px-3 py-2 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: ZOOM PREUVE DE PAIEMENT (IMAGE FULLSCREEN)      -->
<!-- ======================================================= -->
<div class="modal fade" id="proofViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice text-warning"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 fs-6" id="proofModalTitle">Justificatif de Paiement</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;" id="proofModalSub">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-2 text-center bg-black bg-opacity-10">
                <img id="proofModalImage" src="" alt="Preuve" style="max-width: 100%; max-height: 75vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            </div>
            <div class="modal-footer bg-white px-3 py-2 border-0 justify-content-between">
                <a id="proofDownloadBtn" href="" download class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-download me-1"></i> Télécharger le document
                </a>
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openNewPaymentForCustomer(customerId, customerName, currentBalance) {
    const select = document.getElementById('modal_customer_id');
    if (select) {
        select.value = customerId;
        onModalCustomerChange(select);
    }
    const modal = new bootstrap.Modal(document.getElementById('newPaymentModal'));
    modal.show();
}

function onModalCustomerChange(select) {
    const selected = select.options[select.selectedIndex];
    const preview = document.getElementById('modalCustomerBalancePreview');
    if (!selected || !selected.value) {
        if (preview) preview.classList.add('d-none');
        return;
    }
    const balance = parseFloat(selected.getAttribute('data-balance') || 0);
    if (!preview) return;
    preview.classList.remove('d-none');

    if (balance > 0.01) {
        preview.innerHTML = `<span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i>Le client doit actuellement au magasin : ${balance.toLocaleString('fr-FR', {minimumFractionDigits: 2})} DH</span>`;
        document.getElementById('modal_type').value = 'payment_received';
        document.getElementById('modal_amount').value = balance.toFixed(2);
    } else if (balance < -0.01) {
        const absVal = Math.abs(balance);
        preview.innerHTML = `<span class="text-info fw-bold"><i class="fas fa-arrow-up me-1"></i>Le magasin doit actuellement à ce client (Avoir) : ${absVal.toLocaleString('fr-FR', {minimumFractionDigits: 2})} DH</span>`;
        document.getElementById('modal_type').value = 'refund_to_client';
        document.getElementById('modal_amount').value = absVal.toFixed(2);
    } else {
        preview.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check me-1"></i>Le compte du client est actuellement soldé (0 DH).</span>`;
    }
}

function previewSelectedImage(input) {
    const box = document.getElementById('imagePreviewBox');
    const target = document.getElementById('imagePreviewTarget');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            target.src = e.target.result;
            box.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        box.classList.add('d-none');
    }
}

function openLimitModal(customerId, customerName, currentLimit) {
    document.getElementById('creditLimitForm').action = `/customers/${customerId}/credit-limit`;
    document.getElementById('limitCustomerName').textContent = `Client : ${customerName}`;
    document.getElementById('limitInput').value = currentLimit;
    const modal = new bootstrap.Modal(document.getElementById('creditLimitModal'));
    modal.show();
}

function previewProofModal(imageUrl, trxNumber, customerName) {
    document.getElementById('proofModalTitle').textContent = `Preuve de Paiement • ${trxNumber}`;
    document.getElementById('proofModalSub').textContent = `Client : ${customerName}`;
    document.getElementById('proofModalImage').src = imageUrl;
    document.getElementById('proofDownloadBtn').href = imageUrl;
    const modal = new bootstrap.Modal(document.getElementById('proofViewModal'));
    modal.show();
}
</script>
@endpush
@endsection
