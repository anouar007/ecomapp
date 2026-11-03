@extends('layouts.app')

@section('title', 'Codes Promo & Affiliation')

@section('content')
<!-- En-tête de page (Clean & Pro) -->
<div class="brand-header">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div>
            <h1 class="brand-title">{{ __('Codes Promo & Affiliation') }}</h1>
            <p class="brand-subtitle">{{ __('Gérez vos réductions standards, minimums de commande, plages de prix et programmes d\'ambassadeurs') }}</p>
        </div>
    </div>
    <div class="brand-header-actions">
        <a href="{{ route('coupons.create') }}" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3 py-2" style="font-size: 0.84rem;">
            <i class="fas fa-plus me-1"></i> <span class="d-none d-sm-inline">{{ __('Nouveau code promo') }}</span><span class="d-sm-none">{{ __('Créer') }}</span>
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
</div>
@endif

<!-- Metric KPI Stat Cards -->
<div class="row g-2.5 g-md-3 mb-4">
    <!-- Total Codes -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Total Codes') }}</div>
                    <div class="stat-kpi-value text-dark">{{ number_format($stats['total_coupons']) }}</div>
                    <div class="stat-kpi-sub text-muted">{{ __('Tous créés') }}</div>
                </div>
                <div class="stat-kpi-icon bg-primary-subtle text-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Actifs -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Actifs') }}</div>
                    <div class="stat-kpi-value text-success">{{ number_format($stats['active_coupons']) }}</div>
                    <div class="stat-kpi-sub text-success"><i class="fas fa-circle text-success me-1" style="font-size: 7px;"></i>{{ __('En service') }}</div>
                </div>
                <div class="stat-kpi-icon bg-success-subtle text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Affiliés -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Affiliés') }}</div>
                    <div class="stat-kpi-value text-info">{{ number_format($stats['affiliate_coupons'] ?? 0) }}</div>
                    <div class="stat-kpi-sub text-info">{{ __('Ambassadeurs') }}</div>
                </div>
                <div class="stat-kpi-icon bg-info-subtle text-info">
                    <i class="fas fa-share-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Points Accordés -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Points Gagnés') }}</div>
                    <div class="stat-kpi-value text-warning">{{ number_format($stats['total_affiliate_points'] ?? 0) }}</div>
                    <div class="stat-kpi-sub text-muted">{{ __('Aux affiliés') }}</div>
                </div>
                <div class="stat-kpi-icon bg-warning-subtle text-warning">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Utilisations -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Commandes') }}</div>
                    <div class="stat-kpi-value text-dark">{{ number_format($stats['total_usage']) }}</div>
                    <div class="stat-kpi-sub text-muted">{{ __('Utilisations') }}</div>
                </div>
                <div class="stat-kpi-icon" style="background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Économies Clients -->
    <div class="col-6 col-lg-2">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Économies') }}</div>
                    <div class="stat-kpi-value text-danger" style="font-size: 1.15rem;">{{ currency($stats['total_savings']) }}</div>
                    <div class="stat-kpi-sub text-muted">{{ __('Remises accordées') }}</div>
                </div>
                <div class="stat-kpi-icon bg-danger-subtle text-danger">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres de recherche -->
<div class="brand-card mb-4">
    <div class="brand-card-body p-3">
        <form action="{{ route('coupons.index') }}" method="GET" id="couponsFilterForm">
            <div class="row g-2 align-items-center">
                <!-- Recherche Rapide -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" 
                               placeholder="{{ __('Code ou nom de campagne...') }}" 
                               value="{{ request('search') }}">
                    </div>
                </div>

                @php
                    $hasActiveFilters = request('type') || request('status') || request()->filled('is_affiliate');
                @endphp

                <!-- Bouton Drawer Filtres Mobile (d-md-none) -->
                <div class="col-12 d-md-none">
                    <button type="button" 
                            class="btn w-100 {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center justify-content-center gap-1.5"
                            data-bs-toggle="collapse" 
                            data-bs-target="#couponFiltersCollapse" 
                            aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                        <i class="fas fa-sliders-h"></i>
                        <span>{{ __('Filtres avancés') }}</span>
                        @if($hasActiveFilters)
                            <span class="badge bg-white text-dark rounded-pill px-1.5 ms-1">✓</span>
                        @endif
                    </button>
                </div>

                <!-- Filtres Desktop & Mobile Collapse -->
                <div class="col-12 col-md-8 collapse d-md-block" id="couponFiltersCollapse">
                    <div class="row g-2 pt-2 pt-md-0">
                        <div class="col-12 col-sm-4">
                            <select name="type" class="form-select bg-light border-0">
                                <option value="">-- {{ __('Tous types') }} --</option>
                                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>{{ __('Pourcentage (%)') }}</option>
                                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>{{ __('Montant fixe (DH)') }}</option>
                                <option value="free_shipping" {{ request('type') == 'free_shipping' ? 'selected' : '' }}>{{ __('Livraison gratuite') }}</option>
                                <option value="buy_x_get_y" {{ request('type') == 'buy_x_get_y' ? 'selected' : '' }}>{{ __('Buy X Get Y') }}</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-3">
                            <select name="status" class="form-select bg-light border-0">
                                <option value="">-- {{ __('Tous statuts') }} --</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Actif') }}</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactif') }}</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expiré') }}</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-3">
                            <select name="is_affiliate" class="form-select bg-light border-0">
                                <option value="">-- {{ __('Tous') }} --</option>
                                <option value="1" {{ request('is_affiliate') === '1' ? 'selected' : '' }}>{{ __('Affiliés') }}</option>
                                <option value="0" {{ request('is_affiliate') === '0' ? 'selected' : '' }}>{{ __('Standards') }}</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-2 d-flex gap-1.5">
                            <button type="submit" class="btn-brand-primary btn-sm flex-grow-1 justify-content-center" title="{{ __('Filtrer') }}">
                                <i class="fas fa-filter me-1"></i> <span class="d-none d-md-inline">{{ __('Filtrer') }}</span><span class="d-md-none">{{ __('Appliquer') }}</span>
                            </button>
                            @if(request()->anyFilled(['search', 'type', 'status', 'is_affiliate']))
                                <a href="{{ route('coupons.index') }}" class="btn-brand-light btn-sm flex-shrink-0" title="{{ __('Réinitialiser') }}">
                                    <i class="fas fa-redo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- 1. VUE MOBILE TOUCH-FIRST (d-block d-md-none, ZÉRO DÉFILEMENT) -->
<!-- ============================================================== -->
<div class="d-block d-md-none mb-4">
    <div class="d-flex align-items-center justify-content-between px-1 mb-2">
        <span class="fw-bold text-dark" style="font-size: 0.92rem;">
            <i class="fas fa-ticket-alt text-danger me-1.5 opacity-75"></i>{{ __('Liste des codes') }}
        </span>
        <span class="badge rounded-pill bg-danger-subtle text-danger fw-bold px-2 py-0.5" style="font-size: 0.72rem;">
            {{ $coupons->total() }}
        </span>
    </div>

    @forelse($coupons as $coupon)
    <div class="card border-0 shadow-sm rounded-4 mb-2.5 p-3 bg-white position-relative">
        <!-- Ligne 1 : Code + Badges -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
                <span class="font-monospace fw-bold fs-6 text-primary">{{ $coupon->code }}</span>
                <span class="badge bg-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }}-subtle text-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }} border text-uppercase" style="font-size: 0.65rem; padding: 2px 6px;">
                    {{ ucfirst($coupon->status) }}
                </span>
            </div>
            <div class="fw-bold text-dark fs-6">{{ $coupon->formatted_value }}</div>
        </div>

        <!-- Nom / Titre -->
        <div class="fw-semibold text-dark small mb-2 text-truncate">{{ $coupon->name }}</div>

        <!-- Détails & Conditions en Grille Compacte -->
        <div class="p-2.5 bg-light rounded-3 mb-2 small">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">{{ __('Type :') }}</span>
                <span class="text-dark fw-medium">{{ $coupon->type_label }}</span>
            </div>

            @if($coupon->min_order_amount > 0)
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">{{ __('Min. commande :') }}</span>
                <span class="text-dark fw-bold">{{ currency($coupon->min_order_amount) }}</span>
            </div>
            @endif

            @if($coupon->min_product_price > 0 || $coupon->max_product_price > 0)
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">{{ __('Plage prix art. :') }}</span>
                <span class="text-dark fw-medium">
                    {{ $coupon->min_product_price > 0 ? currency($coupon->min_product_price) : '0' }} &rarr; {{ $coupon->max_product_price > 0 ? currency($coupon->max_product_price) : '∞' }}
                </span>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">{{ __('Utilisations :') }}</span>
                <span class="fw-semibold text-dark">
                    {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?: '∞' }}
                </span>
            </div>
        </div>

        <!-- Badge Affilié si activé -->
        @if($coupon->is_affiliate)
        <div class="d-flex align-items-center justify-content-between p-2 bg-success-subtle text-success rounded-3 mb-2 small">
            <div class="d-flex align-items-center gap-1.5 text-truncate" style="max-width: 75%;">
                <i class="fas fa-user-check"></i>
                <span class="fw-semibold text-truncate">{{ $coupon->beneficiary_name ?: __('Affilié actif') }}</span>
            </div>
            <span class="badge bg-success text-white" style="font-size: 0.68rem;">
                +{{ $coupon->affiliate_points_type === 'percentage' ? $coupon->affiliate_points_value . '%' : number_format($coupon->affiliate_points_value) . ' pts' }}
            </span>
        </div>
        @endif

        <!-- Boutons d'Action Mobile -->
        <div class="d-flex align-items-center gap-2 pt-2 border-top mt-1">
            <a href="{{ route('coupons.show', $coupon) }}" class="btn btn-outline-secondary btn-sm rounded-pill flex-grow-1 text-center py-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-eye me-1"></i> {{ __('Détails') }}
            </a>
            <a href="{{ route('coupons.edit', $coupon) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1 text-center py-1.5" style="font-size: 0.8rem;">
                <i class="fas fa-edit me-1"></i> {{ __('Modifier') }}
            </a>
        </div>
    </div>
    @empty
    <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm">
        <i class="fas fa-ticket-alt opacity-25 fa-3x mb-3 text-muted d-block"></i>
        <h6 class="fw-bold text-dark">{{ __('Aucun code promo trouvé') }}</h6>
        <p class="text-muted small mb-3">{{ __('Aucun enregistrement ne correspond à vos critères.') }}</p>
        <a href="{{ route('coupons.create') }}" class="btn-brand-primary btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Créer un code') }}
        </a>
    </div>
    @endforelse

    <!-- Pagination Mobile -->
    @if($coupons->hasPages())
    <div class="bg-white p-3 rounded-4 border shadow-sm mt-3 mb-4 text-center">
        {{ $coupons->links() }}
    </div>
    @endif
</div>

<!-- ============================================================== -->
<!-- 2. VUE TABLEAU DESKTOP (d-none d-md-block)                     -->
<!-- ============================================================== -->
<div class="brand-table-card d-none d-md-block mb-4">
    <div class="table-responsive">
        <table class="brand-table align-middle">
            <thead>
                <tr>
                    <th style="width: 18%; padding-left: 1.25rem;">{{ __('Code Promo') }}</th>
                    <th style="width: 14%;">{{ __('Valeur & Type') }}</th>
                    <th style="width: 20%;">{{ __('Conditions & Tranche') }}</th>
                    <th style="width: 18%;">{{ __('Affilié / Points') }}</th>
                    <th class="text-center" style="width: 10%;">{{ __('Utilisations') }}</th>
                    <th style="width: 10%;">{{ __('Statut') }}</th>
                    <th class="text-end" style="width: 10%; padding-right: 1.25rem;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                <tr>
                    <td style="padding-left: 1.25rem;">
                        <a href="{{ route('coupons.show', $coupon) }}" class="fw-bold font-monospace text-primary text-decoration-none fs-6">
                            {{ $coupon->code }}
                        </a>
                        <div class="text-muted small text-truncate" style="max-width: 180px;" title="{{ $coupon->name }}">
                            {{ $coupon->name }}
                        </div>
                    </td>

                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $coupon->formatted_value }}</div>
                        <span class="badge bg-light text-muted border text-uppercase" style="font-size: 0.68rem;">
                            {{ $coupon->type_label }}
                        </span>
                    </td>

                    <td>
                        <div class="small">
                            @if($coupon->min_order_amount > 0)
                                <div class="text-dark">
                                    <i class="fas fa-coins text-warning me-1"></i>
                                    {{ __('Min. commande :') }} <strong>{{ currency($coupon->min_order_amount) }}</strong>
                                </div>
                            @else
                                <span class="text-muted small">{{ __('Sans min. panier') }}</span>
                            @endif

                            @if($coupon->min_product_price > 0 || $coupon->max_product_price > 0)
                                <div class="text-muted mt-0.5" style="font-size: 0.74rem;">
                                    <i class="fas fa-tags text-primary me-1"></i>
                                    {{ $coupon->min_product_price > 0 ? currency($coupon->min_product_price) : '0' }} &rarr; {{ $coupon->max_product_price > 0 ? currency($coupon->max_product_price) : '∞' }}
                                </div>
                            @endif
                        </div>
                    </td>

                    <td>
                        @if($coupon->is_affiliate)
                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                    <i class="fas fa-gift me-1"></i>
                                    @if($coupon->affiliate_points_type === 'percentage')
                                        {{ $coupon->affiliate_points_value }}% pts
                                    @else
                                        +{{ number_format($coupon->affiliate_points_value) }} pts
                                    @endif
                                </span>
                            </div>
                            <div class="small text-truncate text-muted" style="max-width: 160px;" title="{{ $coupon->beneficiary_name }}">
                                <i class="fas fa-user small me-1"></i> {{ $coupon->beneficiary_name ?: __('Non assigné') }}
                            </div>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <div class="fw-semibold text-dark">
                            {{ $coupon->usage_count }} <span class="text-muted small fw-normal">/ {{ $coupon->usage_limit ?: '∞' }}</span>
                        </div>
                        @if($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit)
                            <span class="badge bg-danger text-uppercase" style="font-size: 0.65rem;">{{ __('Épuisé') }}</span>
                        @endif
                    </td>

                    <td>
                        <span class="badge bg-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }}-subtle text-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }} border border-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }}-subtle text-uppercase px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                            {{ ucfirst($coupon->status) }}
                        </span>
                    </td>

                    <td class="text-end" style="padding-right: 1.25rem;">
                        <div class="action-buttons-group justify-content-end">
                            <a href="{{ route('coupons.show', $coupon) }}" class="action-btn action-btn-view" title="{{ __('Voir') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('coupons.edit', $coupon) }}" class="action-btn action-btn-edit" title="{{ __('Modifier') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fas fa-ticket-alt opacity-25 fa-3x mb-3 text-muted d-block"></i>
                        <h6 class="fw-bold text-dark">{{ __('Aucun code promo trouvé') }}</h6>
                        <p class="text-muted small mb-3">{{ __('Créez votre premier code promotionnel pour dynamiser vos ventes.') }}</p>
                        <a href="{{ route('coupons.create') }}" class="btn-brand-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> {{ __('Créer un code promo') }}
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
    <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
        <div class="small text-muted">
            {{ __('Affichage de') }} {{ $coupons->firstItem() ?? 0 }} {{ __('à') }} {{ $coupons->lastItem() ?? 0 }} {{ __('sur') }} {{ $coupons->total() }}
        </div>
        <div>
            {{ $coupons->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
