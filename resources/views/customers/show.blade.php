@extends('layouts.app')

@section('title', 'Profil Client - ' . $customer->name)

@section('content')
@php
    $statusClasses = [
        'active' => ['class' => 'success', 'dot' => 'dot-green', 'label' => 'Actif'],
        'inactive' => ['class' => 'warning', 'dot' => 'dot-amber', 'label' => 'Inactif'],
        'blocked' => ['class' => 'danger', 'dot' => 'dot-red', 'label' => 'Bloqué'],
    ];
    $statusInfo = $statusClasses[$customer->status] ?? ['class' => 'secondary', 'dot' => 'dot-gray', 'label' => ucfirst($customer->status_label)];
    $initials = collect(explode(' ', $customer->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
    $utilization = $customer->credit_limit > 0 ? min(100, ($customer->current_balance / $customer->credit_limit) * 100) : 0;
@endphp

<!-- En-tête de page 360 Client (Executive Brand Header) -->
<div class="brand-header">
    <div class="d-flex align-items-center gap-3">
        <div class="mobile-cust-avatar" style="width: 50px; height: 50px; min-width: 50px; font-size: 1.2rem; border-radius: 14px; {{ $customer->is_blacklisted ? 'background: #fee2e2; color: #dc2626; border: 1.5px solid #fca5a5;' : '' }}">
            @if($customer->is_blacklisted)
                <i class="fas fa-ban"></i>
            @else
                {{ strtoupper($initials ?: 'CL') }}
            @endif
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h1 class="brand-title mb-0" style="font-size: 1.35rem;">{{ $customer->name }}</h1>
                @if($customer->is_blacklisted)
                    <span class="badge bg-danger text-white border border-danger-subtle fw-bold" style="font-size: 0.76rem;" title="Liste Noire">
                        <i class="fas fa-ban me-1"></i>Liste Noire
                    </span>
                @endif
                @php $tier = $customer->tier; @endphp
                <span class="badge {{ $tier['badge_class'] }}" style="font-size: 0.76rem; padding: 3px 8px;" title="{{ $tier['description'] }}">
                    <i class="fas {{ $tier['icon'] }} me-1"></i>{{ $tier['label'] }}
                </span>
                <span class="badge bg-light text-secondary border font-monospace px-2 py-0.5" style="font-size: 0.76rem;">
                    {{ $customer->customer_code }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-3 text-muted flex-wrap" style="font-size: 0.82rem;">
                <a href="mailto:{{ $customer->email }}" class="text-secondary text-decoration-none">
                    <i class="fas fa-envelope me-1 text-muted"></i>{{ $customer->email }}
                </a>
                @if($customer->phone)
                    <a href="tel:{{ $customer->phone }}" class="text-secondary text-decoration-none font-monospace fw-semibold">
                        <i class="fas fa-phone-alt me-1 text-muted"></i>{{ $customer->phone }}
                    </a>
                @endif
                @if($customer->customerGroup)
                    <span class="badge rounded-pill px-2 py-0.5" style="font-size: 0.72rem; background-color: {{ $customer->customerGroup->color }}18; color: {{ $customer->customerGroup->color }}; border: 1px solid {{ $customer->customerGroup->color }}40;">
                        <i class="fas fa-tag me-1"></i>{{ $customer->customerGroup->name }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
        @if($customer->phone)
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '212' . substr($cleanPhone, 1);
                }
                $waMsg = urlencode("Bonjour {$customer->name}, l'équipe WINA SHOP espère que vous allez bien ! Avez-vous besoin d'une information sur nos nouveautés ?");
            @endphp
            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" target="_blank" class="btn btn-outline-success shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fab fa-whatsapp"></i> <span>WhatsApp</span>
            </a>
        @endif

        @if($customer->is_blacklisted)
            <form method="POST" action="{{ route('customers.toggle-blacklist', $customer) }}" onsubmit="return confirm('Retirer ce client de la liste noire ?');" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-check-circle"></i> <span>Retirer de la Liste Noire</span>
                </button>
            </form>
        @else
            <button type="button" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;" data-bs-toggle="modal" data-bs-target="#blacklistCustomerModal">
                <i class="fas fa-ban"></i> <span>+ Liste Noire</span>
            </button>
        @endif

        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
            <i class="fas fa-arrow-left"></i> <span>Retour</span>
        </a>
        <button type="button" class="btn btn-outline-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;" data-bs-toggle="modal" data-bs-target="#addPointsModal">
            <i class="fas fa-gift"></i> <span>Points</span>
        </button>
        <a href="{{ route('customers.edit', $customer) }}" class="btn-brand-primary rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm text-decoration-none" style="font-size: 0.84rem;">
            <i class="fas fa-edit"></i> <span>Modifier</span>
        </a>
    </div>
</div>

<!-- Alerte Blacklist si client sur liste noire -->
@if($customer->is_blacklisted)
<div class="alert alert-danger border-danger border-2 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3 p-3.5 bg-danger-subtle text-danger" role="alert">
    <div style="font-size: 1.6rem;" class="text-danger flex-shrink-0 mt-0.5">
        <i class="fas fa-triangle-exclamation"></i>
    </div>
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="alert-heading fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                <span>ATTENTION : Ce client est inscrit sur la Liste Noire</span>
                <span class="badge bg-danger text-white">Profil bloqué</span>
            </h5>
            <form method="POST" action="{{ route('customers.toggle-blacklist', $customer) }}" onsubmit="return confirm('Confirmer le retrait de ce client de la liste noire ?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger bg-white shadow-2xs rounded-pill px-3 py-1 fw-semibold">
                    <i class="fas fa-check-circle me-1 text-success"></i> Retirer de la liste noire
                </button>
            </form>
        </div>
        <div class="mt-1 text-danger-emphasis small">
            <strong>Motif :</strong> {{ $customer->blacklist_reason ?: 'Aucun motif renseigné.' }}
            @if($customer->blacklisted_at)
                <span class="opacity-75 ms-2">&bull; Date d'inscription : {{ $customer->blacklisted_at->format('d/m/Y à H:i') }}</span>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Bannière Suggestion de Relance si client dormant -->
@if($customer->is_dormant)
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4 d-flex flex-row align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #ede9fe 0%, #e0e7ff 100%); border-left: 4px solid #6366f1 !important;">
    <div class="d-flex align-items-center gap-3">
        <div style="width: 42px; height: 42px; border-radius: 12px; background: #6366f1; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
            <i class="fas fa-bell"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-0 text-dark">Client régulier inactif depuis {{ $customer->days_since_last_order }} jours</h6>
            <div class="small text-secondary">Ce client a déjà commandé {{ $customer->total_orders }} fois mais n'a pas passé commande récemment. Relancez-le pour fidéliser !</div>
        </div>
    </div>
    @if($customer->phone)
        @php
            $relanceMsg = urlencode("Bonjour {$customer->name}, l'équipe WINA SHOP espère que tout va bien pour vous ! Cela fait un petit moment que nous n'avons pas eu le plaisir de vous servir. Nous serions ravis de vous présenter nos dernières nouveautés !");
        @endphp
        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $relanceMsg }}" target="_blank" class="btn btn-sm btn-indigo text-white rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="background: #4f46e5; border-color: #4f46e5;">
            <i class="fab fa-whatsapp me-1.5"></i> Envoyer un message de relance
        </a>
    @endif
</div>
@endif

<!-- KPI Cards Row (Row of 4) -->
<div class="row g-3 mb-4">
    <!-- Badge Fidélité / Tier -->
    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">Rang & Fidélité</div>
                    <div class="stat-kpi-value text-dark" style="font-size: 1.15rem;">{{ $tier['label'] }}</div>
                    <div class="stat-kpi-sub text-muted">
                        <i class="fas {{ $tier['icon'] }} text-warning me-1"></i>{{ $tier['short'] }}
                    </div>
                </div>
                <div class="stat-kpi-icon" style="{{ $tier['style'] }}">
                    <i class="fas {{ $tier['icon'] }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Commandes Totales -->
    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">Commandes Totales</div>
                    <div class="stat-kpi-value text-primary">{{ $customerStats['total_orders'] }}</div>
                    <div class="stat-kpi-sub text-muted">
                        <i class="fas fa-check-circle text-success me-1"></i>{{ $customerStats['delivered_orders'] }} livrées
                    </div>
                </div>
                <div class="stat-kpi-icon bg-primary-subtle text-primary">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Dépensé (LTV) -->
    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">Total Dépensé (LTV)</div>
                    <div class="stat-kpi-value text-success" style="font-size: clamp(0.95rem, 3.2vw, 1.35rem);">{{ currency($customerStats['total_spent']) }}</div>
                    <div class="stat-kpi-sub text-muted">
                        <i class="fas fa-coins text-success me-1"></i>Chiffre d'affaires
                    </div>
                </div>
                <div class="stat-kpi-icon bg-success-subtle text-success">
                    <i class="fas fa-vault"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Panier Moyen -->
    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">Panier Moyen</div>
                    <div class="stat-kpi-value text-info" style="font-size: clamp(0.95rem, 3.2vw, 1.35rem);">{{ currency($customerStats['avg_order_value']) }}</div>
                    <div class="stat-kpi-sub text-muted">
                        <i class="fas fa-calculator text-info me-1"></i>Moy. / commande
                    </div>
                </div>
                <div class="stat-kpi-icon bg-info-subtle text-info">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Ligne de Crédit & Encours Client (si configuré) -->
@if($customer->credit_limit > 0 || $customer->current_balance > 0)
<div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-kpi-icon bg-white text-dark" style="width: 36px; height: 36px; font-size: 1rem;">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Gestion de l'Encours & Crédit Accordé</h6>
                <div class="text-white-50 small">Plafond de trésorerie autorisé pour ce compte client</div>
            </div>
        </div>
        @if($customer->hasReachedCreditLimit())
            <span class="badge bg-danger text-white rounded-pill px-2.5 py-1">
                <i class="fas fa-exclamation-triangle me-1"></i>Plafond dépassé
            </span>
        @else
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                <i class="fas fa-check-circle me-1"></i>Solde conforme
            </span>
        @endif
    </div>

    <div class="row g-3 align-items-center">
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Limite de Crédit</div>
            <div class="fs-5 fw-bold text-white">{{ currency($customer->credit_limit) }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Encours Actuel</div>
            <div class="fs-5 fw-bold {{ $customer->current_balance > $customer->credit_limit ? 'text-danger' : 'text-warning' }}">
                {{ currency($customer->current_balance) }}
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Crédit Disponible</div>
            <div class="fs-5 fw-bold text-success">
                {{ currency(max(0, $customer->credit_limit - $customer->current_balance)) }}
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="d-flex align-items-center justify-content-between text-white-50 small" style="font-size: 0.7rem;">
                <span>Utilisation</span>
                <span class="text-white fw-bold">{{ number_format($utilization, 1) }}%</span>
            </div>
            <div class="progress mt-1" style="height: 6px; background: rgba(255, 255, 255, 0.2); border-radius: 99px;">
                <div class="progress-bar {{ $utilization > 90 ? 'bg-danger' : ($utilization > 70 ? 'bg-warning' : 'bg-success') }}" 
                     role="progressbar" 
                     style="width: {{ $utilization }}%; border-radius: 99px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Informations Personnelles & Coordonnées (2 Colonnes) -->
<div class="row g-3 mb-4">
    <!-- Identité & Contact -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <i class="fas fa-id-card text-primary" style="font-size: 1.1rem;"></i>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Identité & Contact</h6>
            </div>
            <div class="row g-2.5">
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Nom complet</span>
                    <span class="fw-semibold text-dark">{{ $customer->name }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Code Client</span>
                    <span class="font-monospace text-secondary">{{ $customer->customer_code }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Adresse E-mail</span>
                    <a href="mailto:{{ $customer->email }}" class="text-primary text-decoration-none fw-semibold">
                        {{ $customer->email }}
                    </a>
                </div>
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Téléphone</span>
                    @if($customer->phone)
                        <a href="tel:{{ $customer->phone }}" class="text-dark font-monospace text-decoration-none">
                            {{ $customer->phone }}
                        </a>
                    @else
                        <span class="text-muted fst-italic small">Non renseigné</span>
                    @endif
                </div>
                @if($customer->ice)
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Identifiant ICE</span>
                    <span class="font-monospace fw-semibold text-dark">{{ $customer->ice }}</span>
                </div>
                @endif
                @if($customer->date_of_birth)
                <div class="col-sm-6">
                    <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Date de naissance</span>
                    <span class="text-dark">{{ $customer->date_of_birth->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Adresse & Localisation -->
    <div class="col-12 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <i class="fas fa-map-marker-alt text-danger" style="font-size: 1.1rem;"></i>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Adresse & Facturation</h6>
            </div>
            @if($customer->address || $customer->city || $customer->country)
                <div class="row g-2.5">
                    @if($customer->address)
                    <div class="col-12">
                        <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Adresse</span>
                        <span class="text-dark fw-medium">{{ $customer->address }}</span>
                    </div>
                    @endif
                    <div class="col-sm-6">
                        <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Ville</span>
                        <span class="text-dark">{{ $customer->city ?: '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Région / État</span>
                        <span class="text-dark">{{ $customer->state ?: '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Code Postal</span>
                        <span class="font-monospace text-dark">{{ $customer->zip ?: '—' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block small text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Pays</span>
                        <span class="text-dark fw-medium">{{ $customer->country ?: 'Maroc' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-map-pin mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
                    <p class="small mb-0">Aucune adresse enregistrée pour ce client.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Groupe Client & Remarques (si présents) -->
@if($customer->customerGroup || $customer->notes)
<div class="row g-3 mb-4">
    @if($customer->customerGroup)
    <div class="col-12 {{ $customer->notes ? 'col-md-6' : '' }}">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <i class="fas fa-tags text-info" style="font-size: 1.1rem;"></i>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Groupe & Avantages Tarifaires</h6>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 fs-6 fw-bold" style="background-color: {{ $customer->customerGroup->color }}18; color: {{ $customer->customerGroup->color }}; border: 1px solid {{ $customer->customerGroup->color }}40;">
                    <i class="fas fa-tag me-1.5"></i>{{ $customer->customerGroup->name }}
                </span>
                <div class="text-muted small">
                    Remise appliquée: <strong class="text-dark">{{ $customer->customerGroup->formatted_discount }}</strong>
                </div>
            </div>
            @if($customer->customerGroup->benefits)
                <p class="text-muted small mt-2 mb-0">{{ $customer->customerGroup->benefits }}</p>
            @endif
        </div>
    </div>
    @endif

    @if($customer->notes)
    <div class="col-12 {{ $customer->customerGroup ? 'col-md-6' : '' }}">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <i class="fas fa-sticky-note text-warning" style="font-size: 1.1rem;"></i>
                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Notes & Instructions Particulières</h6>
            </div>
            <p class="text-dark small mb-0" style="line-height: 1.6; white-space: pre-line;">{{ $customer->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Commandes Récentes (Desktop & Mobile) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-shopping-cart text-primary" style="font-size: 1.1rem;"></i>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Commandes Récentes ({{ $recentOrders->count() }})</h6>
        </div>
        <a href="{{ route('orders.index', ['search' => $customer->email]) }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
            Voir tout l'historique
        </a>
    </div>

    @if($recentOrders->count() > 0)
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.78rem; font-weight: 600;">N° Commande</th>
                        <th style="font-size: 0.78rem; font-weight: 600;">Date</th>
                        <th style="font-size: 0.78rem; font-weight: 600;">Articles</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-end">Montant Total</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-center">Statut</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="text-muted small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="small">{{ $order->items->count() }} article(s)</td>
                        <td class="text-end fw-bold text-dark">{{ currency($order->total) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $order->status_badge_class ?? 'secondary' }} rounded-pill px-2.5 py-1">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Voir la commande">
                                <i class="fas fa-eye me-1"></i> Consulter
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="d-block d-md-none">
            <div class="d-flex flex-column gap-2">
                @foreach($recentOrders as $order)
                <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <a href="{{ route('orders.show', $order) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $order->order_number }}
                        </a>
                        <span class="badge bg-{{ $order->status_badge_class ?? 'secondary' }} rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-1">
                        <span>{{ $order->created_at->format('d/m/Y') }} &bull; {{ $order->items->count() }} art.</span>
                        <strong class="text-dark fs-6">{{ currency($order->total) }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-box-open mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
            <p class="small mb-0">Aucune commande enregistrée pour ce client.</p>
        </div>
    @endif
</div>

<!-- Factures Récentes (Desktop & Mobile) -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-file-invoice-dollar text-success" style="font-size: 1.1rem;"></i>
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Factures Récentes ({{ $recentInvoices->count() }})</h6>
        </div>
        <a href="{{ route('invoices.index', ['search' => $customer->email]) }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
            Toutes les factures
        </a>
    </div>

    @if($recentInvoices->count() > 0)
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.78rem; font-weight: 600;">N° Facture</th>
                        <th style="font-size: 0.78rem; font-weight: 600;">Date d'Émission</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-end">Montant TTC</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-end">Reste à Payer</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-center">Statut Paiement</th>
                        <th style="font-size: 0.78rem; font-weight: 600;" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentInvoices as $invoice)
                    @php
                        $payBadges = [
                            'paid' => ['class' => 'success', 'label' => 'Payée'],
                            'partial' => ['class' => 'warning', 'label' => 'Partielle'],
                            'unpaid' => ['class' => 'danger', 'label' => 'Impayée'],
                        ];
                        $payInfo = $payBadges[$invoice->payment_status] ?? ['class' => 'secondary', 'label' => ucfirst($invoice->payment_status)];
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="text-muted small">{{ $invoice->issued_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="text-end fw-bold text-dark">{{ $invoice->formatted_total_amount }}</td>
                        <td class="text-end fw-bold {{ $invoice->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ currency($invoice->remaining_balance) }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $payInfo['class'] }}-subtle text-{{ $payInfo['class'] }} border border-{{ $payInfo['class'] }}-subtle rounded-pill px-2.5 py-1">
                                {{ $payInfo['label'] }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1" title="Voir la facture">
                                <i class="fas fa-eye me-1"></i> Facture
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="d-block d-md-none">
            <div class="d-flex flex-column gap-2">
                @foreach($recentInvoices as $invoice)
                @php
                    $payBadges = [
                        'paid' => ['class' => 'success', 'label' => 'Payée'],
                        'partial' => ['class' => 'warning', 'label' => 'Partielle'],
                        'unpaid' => ['class' => 'danger', 'label' => 'Impayée'],
                    ];
                    $payInfo = $payBadges[$invoice->payment_status] ?? ['class' => 'secondary', 'label' => ucfirst($invoice->payment_status)];
                @endphp
                <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <a href="{{ route('invoices.show', $invoice) }}" class="fw-bold text-dark text-decoration-none">
                            {{ $invoice->invoice_number }}
                        </a>
                        <span class="badge bg-{{ $payInfo['class'] }}-subtle text-{{ $payInfo['class'] }} border border-{{ $payInfo['class'] }}-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                            {{ $payInfo['label'] }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted small mt-1">
                        <span>{{ $invoice->issued_at?->format('d/m/Y') }}</span>
                        <strong class="text-dark fs-6">{{ $invoice->formatted_total_amount }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-file-invoice mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
            <p class="small mb-0">Aucune facture enregistrée pour ce client.</p>
        </div>
    @endif
</div>

<!-- Modal Créditer des Points de Fidélité -->
<div class="modal fade" id="addPointsModal" tabindex="-1" aria-labelledby="addPointsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('customers.add-points', $customer) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold" id="addPointsModalLabel">
                        <i class="fas fa-gift text-primary me-2"></i> Créditer des Points de Fidélité
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nombre de points à ajouter <span class="text-danger">*</span></label>
                        <input type="number" name="points" class="form-control" min="1" step="1" required placeholder="Ex: 50">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted text-uppercase">Motif / Commentaire</label>
                        <input type="text" name="reason" class="form-control" placeholder="Ex: Geste commercial, Campagne promo">
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-brand-primary rounded-pill px-4">Créditer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Inscrire sur Liste Noire -->
<div class="modal fade" id="blacklistCustomerModal" tabindex="-1" aria-labelledby="blacklistCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('customers.toggle-blacklist', $customer) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3" style="background: #fef2f2;">
                    <h5 class="modal-title fw-bold text-danger" id="blacklistCustomerModalLabel">
                        <i class="fas fa-ban me-2"></i> Placer {{ $customer->name }} sur Liste Noire
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-secondary small mb-3">
                        Ce client sera marqué comme <strong>Bloqué</strong> et un badge d'alerte rouge s'affichera automatiquement sur toutes ses commandes passées et futures.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Motif du blacklistage <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm mb-2" onchange="if(this.value){ document.getElementById('custBlacklistReason').value = this.value; }">
                            <option value="">-- Choisir un motif fréquent --</option>
                            <option value="Colis refusé à la livraison sans motif">Colis refusé à la livraison sans motif</option>
                            <option value="Fausse commande / Numéro injoignable">Fausse commande / Numéro injoignable</option>
                            <option value="Impayé / Chèque rejeté">Impayé / Chèque rejeté</option>
                            <option value="Comportement abusif / Harcèlement">Comportement abusif / Harcèlement</option>
                            <option value="Retours répétés injustifiés">Retours répétés injustifiés</option>
                        </select>
                        <textarea name="reason" id="custBlacklistReason" class="form-control" rows="3" placeholder="Décrivez la raison..." required>Colis refusé à la livraison sans motif</textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold" style="background: #dc2626; border-color: #dc2626;">
                        <i class="fas fa-ban me-1.5"></i> Confirmer le Blacklistage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
