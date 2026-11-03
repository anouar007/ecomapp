@extends('layouts.app')

@section('title', 'Détails du Code Promo - ' . $coupon->code)

@section('content')
<!-- En-tête de page -->
<div class="brand-header">
    <div class="d-flex align-items-center gap-2.5">
        <div class="brand-header-icon flex-shrink-0">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="brand-title font-monospace">{{ $coupon->code }}</h1>
                <span class="badge bg-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }}-subtle text-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }} border border-{{ $coupon->status == 'active' ? 'success' : ($coupon->status == 'expired' ? 'danger' : 'secondary') }}-subtle text-uppercase px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                    {{ ucfirst($coupon->status) }}
                </span>
                @if($coupon->is_affiliate)
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill" style="font-size: 0.72rem;">
                        <i class="fas fa-share-alt me-1"></i> {{ __('Affiliation Active') }}
                    </span>
                @endif
            </div>
            <p class="brand-subtitle">{{ $coupon->name }}</p>
        </div>
    </div>
    <div class="brand-header-actions">
        <a href="{{ route('coupons.index') }}" class="btn-brand-light">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Retour') }}
        </a>
        <a href="{{ route('coupons.edit', $coupon) }}" class="btn-brand-primary">
            <i class="fas fa-edit me-1"></i> {{ __('Modifier') }}
        </a>
        <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce code promo ?') }}');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2" {{ $coupon->usage_count > 0 ? 'disabled title="Impossible de supprimer un code avec historique"' : '' }}>
                <i class="fas fa-trash-alt me-1"></i> {{ __('Supprimer') }}
            </button>
        </form>
    </div>
</div>

<!-- Metric Stat Cards -->
<div class="row g-2.5 g-md-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Valeur Remise') }}</div>
                    <div class="stat-kpi-value text-primary">{{ $coupon->formatted_value }}</div>
                    <div class="stat-kpi-sub text-muted">{{ $coupon->type_label }}</div>
                </div>
                <div class="stat-kpi-icon bg-primary-subtle text-primary">
                    <i class="fas fa-percent"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Utilisations') }}</div>
                    <div class="stat-kpi-value text-dark">{{ $coupon->usage_count }} <span class="fs-6 text-muted fw-normal">/ {{ $coupon->usage_limit ?: '∞' }}</span></div>
                    <div class="stat-kpi-sub text-muted">{{ __('Commandes validées') }}</div>
                </div>
                <div class="stat-kpi-icon bg-info-subtle text-info">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Économies Clients') }}</div>
                    <div class="stat-kpi-value text-danger fs-5">{{ currency($coupon->usages->sum('discount_amount')) }}</div>
                    <div class="stat-kpi-sub text-muted">{{ __('Total remises') }}</div>
                </div>
                <div class="stat-kpi-icon bg-danger-subtle text-danger">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-kpi-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-kpi-label">{{ __('Points Affilié') }}</div>
                    <div class="stat-kpi-value text-success">+{{ number_format($coupon->total_affiliate_points_awarded) }}</div>
                    <div class="stat-kpi-sub text-success">{{ __('Points accordés') }}</div>
                </div>
                <div class="stat-kpi-icon bg-success-subtle text-success">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Colonne Principale (Gauche) -->
    <div class="col-12 col-lg-8">
        <!-- 1. Détails & Restrictions -->
        <div class="brand-card mb-4">
            <div class="brand-card-header">
                <h5 class="brand-card-title">
                    <i class="fas fa-sliders-h text-primary me-2"></i> {{ __('Conditions & Restrictions') }}
                </h5>
            </div>
            <div class="brand-card-body p-4">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <div class="text-muted small text-uppercase fw-bold mb-2">
                                <i class="fas fa-coins text-warning me-1"></i> {{ __('Panier & Plafond') }}
                            </div>
                            <div class="d-flex justify-content-between mb-1.5 small">
                                <span class="text-muted">{{ __('Minimum commande :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->min_order_amount > 0 ? currency($coupon->min_order_amount) : __('Aucun minimum') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1.5 small">
                                <span class="text-muted">{{ __('Plafond max remise :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->max_discount_amount > 0 ? currency($coupon->max_discount_amount) : __('Illimité') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">{{ __('1ère commande seule :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->first_order_only ? __('Oui') : __('Non') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3 h-100 border">
                            <div class="text-muted small text-uppercase fw-bold mb-2">
                                <i class="far fa-calendar-alt text-primary me-1"></i> {{ __('Validité & Fréquence') }}
                            </div>
                            <div class="d-flex justify-content-between mb-1.5 small">
                                <span class="text-muted">{{ __('Date début :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->valid_from ? $coupon->valid_from->format('d/m/Y H:i') : __('Immédiat') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1.5 small">
                                <span class="text-muted">{{ __('Date expiration :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->valid_to ? $coupon->valid_to->format('d/m/Y H:i') : __('Illimitée') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">{{ __('Limite par client :') }}</span>
                                <span class="fw-bold text-dark">{{ $coupon->per_customer_limit ?: __('Illimité') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plage de prix et Scope -->
                <div class="mt-3 p-3 bg-light rounded-3 border">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-6">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-1">{{ __('Champ d\'application :') }}</span>
                            @if($coupon->applicable_to === 'all')
                                <span class="badge bg-info-subtle text-info border px-2.5 py-1 rounded-pill">{{ __('Tous les produits') }}</span>
                            @elseif($coupon->applicable_to === 'specific_products')
                                <span class="badge bg-primary-subtle text-primary border px-2.5 py-1 rounded-pill">
                                    {{ __('Produits spécifiques') }} ({{ count($coupon->applicable_ids ?? []) }})
                                </span>
                            @elseif($coupon->applicable_to === 'specific_categories')
                                <span class="badge bg-warning-subtle text-warning border px-2.5 py-1 rounded-pill">
                                    {{ __('Catégories spécifiques') }} ({{ count($coupon->applicable_ids ?? []) }})
                                </span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted small text-uppercase fw-bold d-block mb-1">{{ __('Plage de prix unitaire :') }}</span>
                            <span class="fw-bold text-dark">
                                @if($coupon->min_product_price > 0 && $coupon->max_product_price > 0)
                                    {{ currency($coupon->min_product_price) }} &rarr; {{ currency($coupon->max_product_price) }}
                                @elseif($coupon->min_product_price > 0)
                                    &ge; {{ currency($coupon->min_product_price) }}
                                @elseif($coupon->max_product_price > 0)
                                    &le; {{ currency($coupon->max_product_price) }}
                                @else
                                    <span class="text-muted fw-normal">{{ __('Tous prix confondus') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Historique des Utilisations -->
        <div class="brand-card">
            <div class="brand-card-header d-flex justify-content-between align-items-center">
                <h5 class="brand-card-title mb-0">
                    <i class="fas fa-history text-primary me-2"></i> {{ __('Historique des Utilisations') }} ({{ $coupon->usages->count() }})
                </h5>
                <span class="badge bg-primary-subtle text-primary">
                    {{ __('Total remises :') }} {{ currency($coupon->usages->sum('discount_amount')) }}
                </span>
            </div>
            
            <!-- Mobile Usages Cards (d-block d-md-none) -->
            <div class="d-block d-md-none p-3">
                @forelse($coupon->usages as $usage)
                <div class="p-3 bg-light rounded-3 border mb-2 small">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark">
                            @if($usage->order)
                                <a href="{{ route('orders.show', $usage->order_id) }}" class="text-decoration-none">
                                    #{{ $usage->order->order_number }}
                                </a>
                            @else
                                #{{ $usage->order_id }}
                            @endif
                        </span>
                        <span class="text-danger fw-bold">-{{ currency($usage->discount_amount) }}</span>
                    </div>
                    <div class="text-muted mb-1 text-truncate">{{ $usage->customer_email ?: __('Client invité') }}</div>
                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                        <span>Total: {{ currency($usage->order_total) }}</span>
                        @if($coupon->is_affiliate)
                            <span class="text-success fw-bold">+{{ $usage->affiliate_points_awarded }} pts</span>
                        @endif
                        <span>{{ $usage->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-ticket-alt opacity-25 fa-2x mb-2 d-block"></i>
                    {{ __('Aucune commande enregistrée avec ce code promo.') }}
                </div>
                @endforelse
            </div>

            <!-- Desktop Usages Table (d-none d-md-block) -->
            <div class="table-responsive d-none d-md-block">
                <table class="brand-table align-middle">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.25rem;">{{ __('Commande') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Total Commande') }}</th>
                            <th>{{ __('Réduction Accordée') }}</th>
                            @if($coupon->is_affiliate)
                                <th>{{ __('Points Affilié') }}</th>
                            @endif
                            <th class="text-end" style="padding-right: 1.25rem;">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupon->usages as $usage)
                        <tr>
                            <td style="padding-left: 1.25rem;">
                                @if($usage->order)
                                    <a href="{{ route('orders.show', $usage->order_id) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                        #{{ $usage->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-muted">#{{ $usage->order_id }}</span>
                                @endif
                            </td>
                            <td>{{ $usage->customer_email ?: __('Client invité') }}</td>
                            <td class="fw-bold">{{ currency($usage->order_total) }}</td>
                            <td class="text-danger fw-bold">-{{ currency($usage->discount_amount) }}</td>
                            @if($coupon->is_affiliate)
                                <td>
                                    <span class="badge bg-success-subtle text-success">
                                        +{{ $usage->affiliate_points_awarded }} pts
                                    </span>
                                </td>
                            @endif
                            <td class="text-end text-muted small" style="padding-right: 1.25rem;">{{ $usage->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $coupon->is_affiliate ? 6 : 5 }}" class="text-center py-4 text-muted">
                                <i class="fas fa-ticket-alt opacity-25 fa-2x mb-2 d-block"></i>
                                {{ __('Aucune commande enregistrée avec ce code promo.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Colonne Latérale (Droite) : Affiliation & Partage -->
    <div class="col-12 col-lg-4">
        @if($coupon->is_affiliate)
        <div class="brand-card mb-4 border-start border-4 border-success">
            <div class="brand-card-header">
                <h5 class="brand-card-title text-success">
                    <i class="fas fa-share-alt me-1.5"></i> {{ __('Programme Affilié / Ambassadeur') }}
                </h5>
            </div>
            <div class="brand-card-body p-4">
                <div class="mb-3">
                    <div class="text-muted small text-uppercase fw-bold mb-1">{{ __('Bénéficiaire des Points') }}</div>
                    @if($coupon->affiliateUser)
                        <div class="fw-bold fs-6 text-dark">{{ $coupon->affiliateUser->name }}</div>
                        <div class="text-muted small mb-2">{{ $coupon->affiliateUser->email }}</div>
                        <span class="badge bg-primary-subtle text-primary mb-2">{{ __('Compte Utilisateur') }}</span>
                        <div class="p-2.5 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">{{ __('Solde actuel :') }}</span>
                            <span class="fw-bold fs-5 text-success">{{ number_format($coupon->affiliateUser->affiliate_points ?? 0) }} pts</span>
                        </div>
                    @elseif($coupon->affiliateCustomer)
                        <div class="fw-bold fs-6 text-dark">{{ $coupon->affiliateCustomer->name }}</div>
                        <div class="text-muted small mb-2">{{ $coupon->affiliateCustomer->email ?? $coupon->affiliateCustomer->phone }}</div>
                        <span class="badge bg-info-subtle text-info mb-2">{{ __('Fiche Client CRM') }}</span>
                        <div class="p-2.5 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">{{ __('Points fidélité :') }}</span>
                            <span class="fw-bold fs-5 text-success">{{ number_format($coupon->affiliateCustomer->loyalty_points ?? 0) }} pts</span>
                        </div>
                    @else
                        <span class="text-muted">{{ __('Non assigné') }}</span>
                    @endif
                </div>

                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <div class="text-muted small text-uppercase fw-bold mb-1">{{ __('Règle de Récompense') }}</div>
                    <div class="fw-bold text-dark">
                        @if($coupon->affiliate_points_type === 'percentage')
                            {{ $coupon->affiliate_points_value }}% {{ __('du montant en points') }}
                        @else
                            {{ number_format($coupon->affiliate_points_value) }} {{ __('points par commande client') }}
                        @endif
                    </div>
                </div>

                <div class="p-3 bg-success-subtle text-success rounded-3 mb-3">
                    <div class="small fw-semibold mb-1">{{ __('Points Cumulés Générés :') }}</div>
                    <div class="fs-4 fw-bold">+{{ number_format($coupon->total_affiliate_points_awarded) }} pts</div>
                </div>

                <!-- Lien Partageable -->
                <div>
                    <label class="form-label small fw-bold text-uppercase text-muted">{{ __('Lien Direct de Parrainage') }}</label>
                    <div class="input-group mb-2">
                        <input type="text" id="shareableLinkInput" class="form-control form-control-sm font-monospace bg-white" value="{{ url('/shop?promo=' . $coupon->code) }}" readonly>
                        <button type="button" class="btn btn-outline-success btn-sm" id="btnCopyShareLink" title="{{ __('Copier') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <div class="d-grid">
                        <a href="https://wa.me/?text={{ urlencode('Profitez d\'une réduction avec le code promo ' . $coupon->code . ' sur notre boutique : ' . url('/shop?promo=' . $coupon->code)) }}" target="_blank" class="btn btn-success btn-sm rounded-pill py-2">
                            <i class="fab fa-whatsapp me-1"></i> {{ __('Partager sur WhatsApp') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Carte Partage Rapide du Code -->
        <div class="brand-card">
            <div class="brand-card-header">
                <h5 class="brand-card-title">{{ __('Copie Rapide du Code') }}</h5>
            </div>
            <div class="brand-card-body p-4 text-center">
                <div class="input-group mb-2">
                    <input type="text" class="form-control font-monospace fw-bold text-center fs-5 text-primary" value="{{ $coupon->code }}" readonly>
                    <button class="btn btn-brand-primary" type="button" onclick="copyCodeText('{{ $coupon->code }}')">
                        <i class="fas fa-copy me-1"></i> {{ __('Copier') }}
                    </button>
                </div>
                <div class="text-muted small">{{ __('Partagez ce code avec vos clients ou sur vos réseaux sociaux.') }}</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyCodeText(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '{{ __("Code copié !") }}',
                text: text,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            alert('{{ __("Code copié dans le presse-papier !") }}');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const btnCopy = document.getElementById('btnCopyShareLink');
    const linkInput = document.getElementById('shareableLinkInput');
    if (btnCopy && linkInput) {
        btnCopy.addEventListener('click', function() {
            linkInput.select();
            navigator.clipboard.writeText(linkInput.value).then(() => {
                const originalHtml = btnCopy.innerHTML;
                btnCopy.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => { btnCopy.innerHTML = originalHtml; }, 2000);
            });
        });
    }
});
</script>
@endpush
@endsection
