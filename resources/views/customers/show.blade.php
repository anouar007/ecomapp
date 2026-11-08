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

<!-- Section Ligne de Crédit, Avoirs & Encours Client -->
<div class="card border-0 shadow-sm rounded-4 p-3.5 mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="stat-kpi-icon bg-white text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                <i class="fas fa-wallet text-primary"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-white" style="font-size: 1rem;">Crédit, Avoirs & Solde Trésorerie</h6>
                <div class="text-white-50 small">Suivi des créances clients et avoirs du magasin</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-semibold shadow-xs" 
                    onclick="openCustomerPaymentModal()">
                <i class="fas fa-coins me-1"></i> Nouveau Règlement / Avoir
            </button>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5 fw-semibold"
                    onclick="openCreditLimitModal()">
                <i class="fas fa-sliders me-1"></i> Plafond
            </button>
        </div>
    </div>

    <div class="row g-3 align-items-center">
        <!-- 1. Limite de Crédit -->
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Plafond Autorisé</div>
            <div class="fs-5 fw-bold text-white">{{ currency($customer->credit_limit) }}</div>
        </div>

        <!-- 2. Solde Actuel (Débiteur ou Créditeur) -->
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Situation du Solde</div>
            @if($customer->current_balance > 0.01)
                <div class="fs-5 fw-bold text-danger">
                    <i class="fas fa-arrow-down me-1" style="font-size: 0.85rem;"></i>{{ currency($customer->current_balance) }}
                </div>
                <div class="badge bg-danger text-white rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.65rem;">
                    Le client doit au magasin
                </div>
            @elseif($customer->current_balance < -0.01)
                <div class="fs-5 fw-bold text-info">
                    <i class="fas fa-arrow-up me-1" style="font-size: 0.85rem;"></i>{{ currency(abs($customer->current_balance)) }}
                </div>
                <div class="badge bg-info text-white rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.65rem;">
                    Avoir dû au client
                </div>
            @else
                <div class="fs-5 fw-bold text-success">0,00 DH</div>
                <div class="badge bg-success text-white rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.65rem;">
                    Compte entièrement soldé
                </div>
            @endif
        </div>

        <!-- 3. Crédit Disponible -->
        <div class="col-6 col-md-3">
            <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Crédit Disponible</div>
            <div class="fs-5 fw-bold text-success">
                {{ currency(max(0, $customer->credit_limit - $customer->current_balance)) }}
            </div>
        </div>

        <!-- 4. Taux d'utilisation -->
        <div class="col-6 col-md-3">
            @if($customer->credit_limit > 0)
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
            @else
                <div class="text-white-50 small text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">Plafond</div>
                <div class="text-white-50 small mt-1">Non défini (au comptant)</div>
            @endif
        </div>
    </div>
</div>

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

<!-- ======================================================= -->
<!-- SECTION HISTORIQUE DES RÈGLEMENTS & PREUVES DE PAIEMENT -->
<!-- ======================================================= -->
<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="stat-kpi-icon bg-light text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                <i class="fas fa-receipt text-danger"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0" style="font-size: 1rem;">Historique des Règlements & Justificatifs Photos</h5>
                <span class="text-muted small">Paiements encaissés, avoirs accordés, et pièces jointes photographiques</span>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-semibold shadow-xs" onclick="openCustomerPaymentModal()">
            <i class="fas fa-plus-circle me-1"></i> Nouveau Règlement
        </button>
    </div>

    @if(isset($recentTransactions) && $recentTransactions->count() > 0)
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.76rem; font-weight: 600;">Date & Réf</th>
                        <th style="font-size: 0.76rem; font-weight: 600;">Type d'Opération</th>
                        <th style="font-size: 0.76rem; font-weight: 600;">Mode & N° Chèque</th>
                        <th style="font-size: 0.76rem; font-weight: 600;" class="text-center">Preuve Photo</th>
                        <th style="font-size: 0.76rem; font-weight: 600;" class="text-end">Montant</th>
                        <th style="font-size: 0.76rem; font-weight: 600;" class="text-end">Nouveau Solde</th>
                        <th style="font-size: 0.76rem; font-weight: 600;">Opérateur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $trx)
                    <tr>
                        <td>
                            <div class="fw-bold font-monospace text-dark" style="font-size: 0.8rem;">{{ $trx->transaction_number }}</div>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $trx->payment_date->format('d/m/Y H:i') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $trx->type_badge_class }} border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                {{ $trx->type_label }}
                            </span>
                        </td>
                        <td class="small">
                            <div>{{ $trx->payment_method_label }}</div>
                            @if($trx->reference_number)
                                <span class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 0.65rem;">
                                    {{ $trx->reference_number }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($trx->proof_image_url)
                                <img src="{{ $trx->proof_image_url }}" alt="Preuve" style="width: 42px; height: 42px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; cursor: pointer;"
                                     onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($customer->name) }}')"
                                     title="Agrandir la preuve">
                            @else
                                <span class="text-muted small fst-italic">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                        </td>
                        <td class="text-end font-monospace small">
                            <span class="fw-semibold {{ $trx->balance_after > 0 ? 'text-danger' : ($trx->balance_after < 0 ? 'text-info' : 'text-success') }}">
                                {{ currency($trx->balance_after) }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $trx->createdBy->name ?? 'Système' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile View for Transactions -->
        <div class="d-block d-md-none">
            <div class="d-flex flex-column gap-2">
                @foreach($recentTransactions as $trx)
                <div class="border rounded-3 p-2.5 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold font-monospace small">{{ $trx->transaction_number }}</span>
                        <span class="fw-bold {{ $trx->direction === 'in' ? 'text-success' : 'text-danger' }}">
                            {{ $trx->direction === 'in' ? '+' : '-' }}{{ currency($trx->amount) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                        <span>{{ $trx->type_label }} • {{ $trx->payment_method_label }}</span>
                        <span>{{ $trx->payment_date->format('d/m/Y') }}</span>
                    </div>
                    @if($trx->proof_image_url)
                        <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2">
                            <img src="{{ $trx->proof_image_url }}" alt="Preuve" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px;"
                                 onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($customer->name) }}')">
                            <a href="javascript:void(0)" class="small text-primary text-decoration-none"
                               onclick="previewProofModal('{{ $trx->proof_image_url }}', '{{ $trx->transaction_number }}', '{{ addslashes($customer->name) }}')">
                                <i class="fas fa-expand me-1"></i> Voir la preuve de paiement
                            </a>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-receipt mb-2" style="font-size: 1.5rem; opacity: 0.5;"></i>
            <p class="small mb-0">Aucun règlement enregistré pour ce client pour le moment.</p>
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

<!-- Modal Règlement / Avoir pour ce Client -->
<div class="modal fade" id="customerPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('client-credits.transactions.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 fs-6">Enregistrer un Règlement / Avoir</h5>
                        <p class="small text-white-50 mb-0" style="font-size: 0.76rem;">Client : {{ $customer->name }} (Solde : {{ currency($customer->current_balance) }})</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark mb-1">Type d'opération <span class="text-danger">*</span></label>
                        <select name="type" id="cust_modal_type" class="form-select rounded-3" required>
                            <option value="payment_received" {{ $customer->current_balance > 0 ? 'selected' : '' }}>Règlement reçu du client (Diminue sa dette)</option>
                            <option value="refund_to_client" {{ $customer->current_balance < 0 ? 'selected' : '' }}>Remboursement versé au client (Diminue son avoir)</option>
                            <option value="credit_given">Vente à crédit accordée (Augmente la dette)</option>
                            <option value="credit_note">Avoir accordé au client (Augmente la dette du magasin)</option>
                            <option value="adjustment">Ajustement manuel de solde</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">Montant (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control rounded-start-3 fw-bold" 
                                   value="{{ $customer->current_balance > 0 ? (float)$customer->current_balance : ($customer->current_balance < 0 ? abs((float)$customer->current_balance) : '') }}" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select rounded-3" required>
                            <option value="especes" selected>Espèces / Cash</option>
                            <option value="virement">Virement bancaire</option>
                            <option value="cheque">Chèque bancaire</option>
                            <option value="versement">Versement bancaire</option>
                            <option value="tpe">Carte bancaire (TPE)</option>
                            <option value="avoir">Déduction sur Avoir</option>
                            <option value="autre">Autre mode</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">N° Réf. Transaction / N° Chèque</label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="Ex: CHQ-981244 ou VIR-BAM-0021">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de l'opération</label>
                        <input type="datetime-local" name="payment_date" class="form-control rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fas fa-camera text-primary me-1"></i> Preuve de paiement (Image reçue / photo reçu)
                        </label>
                        <input type="file" name="proof_image" accept="image/*" class="form-control rounded-3" onchange="previewCustPaymentImage(this)">
                        <div class="form-text" style="font-size: 0.72rem;">Formats : JPG, PNG, WEBP (Max 5MB).</div>
                    </div>
                </div>

                <div id="custImagePreviewBox" class="d-none mb-3 p-2 rounded-3 bg-light border text-center">
                    <img id="custImagePreviewTarget" src="" alt="Aperçu Preuve" style="max-height: 180px; max-width: 100%; border-radius: 8px;">
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold text-dark mb-1">Notes / Motif du règlement</label>
                    <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Précisez le détail de l'opération..."></textarea>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3.5" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold shadow-xs">
                    <i class="fas fa-check-circle me-1.5"></i> Enregistrer l'Opération
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Plafond de Crédit -->
<div class="modal fade" id="creditLimitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" action="{{ route('customers.update-credit-limit', $customer) }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-2.5 px-3 border-0">
                <h6 class="modal-title fw-bold mb-0 fs-6">Plafond de Crédit</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-3">
                <label class="form-label small fw-bold text-dark mb-1">Montant Max Autorisé (DH)</label>
                <div class="input-group">
                    <input type="number" step="0.01" min="0" name="credit_limit" value="{{ (float)$customer->credit_limit }}" class="form-control rounded-start-3 fw-bold" required>
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

<!-- Modal Zoom Preuve Photo -->
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
function openCustomerPaymentModal() {
    const modal = new bootstrap.Modal(document.getElementById('customerPaymentModal'));
    modal.show();
}

function openCreditLimitModal() {
    const modal = new bootstrap.Modal(document.getElementById('creditLimitModal'));
    modal.show();
}

function previewCustPaymentImage(input) {
    const box = document.getElementById('custImagePreviewBox');
    const target = document.getElementById('custImagePreviewTarget');
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
