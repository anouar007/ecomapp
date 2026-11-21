@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number)

@push('styles')
<style>
.order-history-filter-pill {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    border-radius: 9999px;
    padding: 0.25rem 0.72rem;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.order-history-filter-pill:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.order-history-filter-pill.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}
.order-history-timeline {
    position: relative;
    padding-left: 2rem;
}
.order-history-timeline::before {
    content: '';
    position: absolute;
    left: 13px;
    top: 8px;
    bottom: 8px;
    width: 2px;
    background: #e2e8f0;
}
.order-history-item {
    position: relative;
    padding-bottom: 1.15rem;
}
.order-history-item:last-child {
    padding-bottom: 0;
}
.order-history-node {
    position: absolute;
    left: -2rem;
    top: 2px;
    width: 28px;
    height: 28px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.1);
    z-index: 2;
}
.order-history-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.85rem 1rem;
    transition: border-color 0.15s ease, background-color 0.15s ease;
}
.order-history-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
}
.order-diff-chip {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.35rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.22rem 0.55rem;
    font-size: 0.76rem;
}
</style>
@endpush

@section('content')
    @php
        $showTaxMode = $order->normalized_tax_mode;
        $showStLower = strtolower((string) $order->status);
        $normalizedShowStatus = match ($showStLower) {
            'delivered', 'livrée' => 'delivered',
            'shipped', 'expédiée' => 'shipped',
            'processing', 'en cours' => 'processing',
            'en_demande' => 'en_demande',
            'reserved', 'réservé' => 'reserved',
            'cancelled', 'annulée' => 'cancelled',
            default => 'pending',
        };
        $showStatusText = match ($normalizedShowStatus) {
            'delivered' => 'Livrée',
            'shipped' => 'Expédiée',
            'processing' => 'En cours',
            'en_demande' => 'Sur Commande',
            'reserved' => 'Réservé',
            'cancelled' => 'Annulée',
            default => 'En attente',
        };
        $showStatusDot = match ($normalizedShowStatus) {
            'delivered' => 'green',
            'shipped' => 'teal',
            'processing' => 'blue',
            'en_demande' => 'indigo',
            'reserved' => 'purple',
            'cancelled' => 'red',
            default => 'amber',
        };
        $showStatusClass = match ($normalizedShowStatus) {
            'delivered' => 'success',
            'cancelled' => 'danger',
            'shipped' => 'info',
            'processing' => 'primary',
            'en_demande' => 'indigo',
            'reserved' => 'purple',
            default => 'warning',
        };
        $showPayLower = strtolower((string) $order->payment_status);
        $showPayBadgeClass = match ($showPayLower) {
            'paid', 'payé' => 'pay-badge-paid',
            'failed', 'échoué' => 'pay-badge-failed',
            'refunded', 'remboursé' => 'pay-badge-refunded',
            default => 'pay-badge-pending',
        };
        $showPayShortLabel = match ($showPayLower) {
            'paid', 'payé' => 'Payé',
            'partial', 'partiel' => 'Partiel',
            'failed', 'échoué' => 'Échoué',
            'refunded', 'remboursé' => 'Remboursé',
            default => 'En attente',
        };
        $showPayFullText = match ($showPayLower) {
            'paid', 'payé' => 'Règlement Confirmé (Payé)',
            'partial', 'partiel' => 'Paiement Partiel',
            'failed', 'échoué' => 'Paiement Échoué',
            'refunded', 'remboursé' => 'Commande Remboursée',
            default => 'En attente de paiement',
        };
        $showMethodLabel = match (strtolower((string) $order->payment_method)) {
            'cash' => 'Espèces',
            'card' => 'Carte bancaire',
            'virement', 'bank_transfer' => 'Virement bancaire',
            'credit' => 'Crédit client',
            'cod', 'cash_on_delivery' => 'Paiement à la livraison',
            'other' => 'Autre',
            default => ucfirst(str_replace('_', ' ', $order->payment_method ?: 'Non spécifié')),
        };
        $canEditOrderShow = auth()->user()?->can('manage_orders') || auth()->user()?->can('edit_orders');
        $showCleanPhone = '';
        if ($order->customer_phone) {
            $showCleanPhone = preg_replace('/[^0-9]/', '', (string) $order->customer_phone);
            if (str_starts_with($showCleanPhone, '0')) {
                $showCleanPhone = '212' . substr($showCleanPhone, 1);
            }
        }
    @endphp

    <div class="order-show-page">
    <!-- Executive Order Hero Header + 4-KPI Summary Strip -->
    <div class="order-show-hero order-hero-accent-{{ $normalizedShowStatus }}">
        <div class="order-show-hero-top">
            <div class="w-100 d-md-flex align-items-start justify-content-between gap-3">
                <!-- Mobile App Top Bar (< 768px) -->
                <div class="order-mobile-topbar d-flex d-md-none">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <a href="{{ $order->isPosOrder() ? route('orders.pos') : route('orders.index') }}"
                           class="order-mobile-back-btn"
                           title="Retour aux commandes">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="min-w-0">
                            <div class="d-flex align-items-center gap-1.5">
                                <h1 class="order-hero-title text-truncate">
                                    <span class="font-monospace text-dark">#{{ $order->order_number }}</span>
                                </h1>
                                <button type="button"
                                        class="order-copy-inline-btn"
                                        onclick="copyShowOrderNumber('{{ $order->order_number }}', this)"
                                        title="Copier le N° de commande">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                {{ $order->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                    <span class="order-channel-pill {{ $order->isPosOrder() ? 'is-pos' : 'is-web' }} flex-shrink-0">
                        <i class="fas {{ $order->isPosOrder() ? 'fa-store' : 'fa-globe' }}"></i>
                        <span>{{ $order->isPosOrder() ? 'POS' : 'Web' }}</span>
                    </span>
                </div>

                <!-- Left Title & Pills (Desktop & Pills Row shared cleanly with zero duplicate IDs) -->
                <div class="d-flex align-items-start gap-3 min-w-0 flex-grow-1">
                    <div class="order-show-hero-icon d-none d-md-flex">
                        <i class="fas {{ $order->isPosOrder() ? 'fa-cash-register' : 'fa-receipt' }}"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-none d-md-flex align-items-center gap-2 text-muted small mb-1 flex-wrap">
                            <a href="{{ $order->isPosOrder() ? route('orders.pos') : route('orders.index') }}" class="text-decoration-none text-muted fw-medium">
                                {{ $order->isPosOrder() ? 'Commandes POS' : 'Commandes Site Web' }}
                            </a>
                            <i class="fas fa-chevron-right" style="font-size: 9px;"></i>
                            <span class="text-dark fw-semibold font-monospace">#{{ $order->order_number }}</span>
                            <i class="fas fa-chevron-right" style="font-size: 9px;"></i>
                            <span class="text-muted">Détails</span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap order-hero-pills">
                            <span class="order-hero-title d-none d-md-inline">
                                Commande <span class="font-monospace text-dark">#{{ $order->order_number }}</span>
                            </span>
                            <button type="button"
                                    class="order-copy-inline-btn d-none d-md-inline-flex"
                                    onclick="copyShowOrderNumber('{{ $order->order_number }}', this)"
                                    title="Copier le N° de commande">
                                <i class="far fa-copy"></i>
                            </button>

                            <span class="order-channel-pill {{ $order->isPosOrder() ? 'is-pos' : 'is-web' }} d-none d-md-inline-flex">
                                <i class="fas {{ $order->isPosOrder() ? 'fa-store' : 'fa-globe' }}"></i>
                                <span>{{ $order->isPosOrder() ? 'Caisse POS' : 'Site Web' }}</span>
                            </span>

                            @if($canEditOrderShow)
                                <div class="dropdown d-inline-block">
                                    <button type="button"
                                            class="brand-badge brand-badge-btn {{ $showStatusClass }} status-pill-{{ $normalizedShowStatus }} dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            data-bs-auto-close="true"
                                            aria-expanded="false"
                                            id="show-status-pill-{{ $order->id }}"
                                            title="Changer le statut de la commande">
                                        <span class="status-dot dot-{{ $showStatusDot }}"></span>
                                        <span>{{ $showStatusText }}</span>
                                        <i class="fas fa-chevron-down ms-0.5" style="font-size: 0.6rem;"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="show-status-pill-{{ $order->id }}">
                                        <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'pending')">
                                                <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                                                @if($normalizedShowStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'en_demande' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'en_demande')">
                                                <span><span class="status-dot dot-indigo me-1.5"></span> Sur Commande</span>
                                                @if($normalizedShowStatus === 'en_demande') <i class="fas fa-check text-primary small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'reserved' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'reserved')">
                                                <span><span class="status-dot dot-purple me-1.5"></span> Réservé</span>
                                                @if($normalizedShowStatus === 'reserved') <i class="fas fa-check text-purple small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'processing' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'processing')">
                                                <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                                                @if($normalizedShowStatus === 'processing') <i class="fas fa-check text-primary small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'shipped' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'shipped')">
                                                <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                                                @if($normalizedShowStatus === 'shipped') <i class="fas fa-check text-info small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedShowStatus === 'delivered' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'delivered')">
                                                <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                                                @if($normalizedShowStatus === 'delivered') <i class="fas fa-check text-success small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $normalizedShowStatus === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateShowStatus({{ $order->id }}, 'cancelled')">
                                                <span><span class="status-dot dot-red me-1.5"></span> Annulée</span>
                                                @if($normalizedShowStatus === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="brand-badge brand-badge-btn {{ $showStatusClass }} status-pill-{{ $normalizedShowStatus }}">
                                    <span class="status-dot dot-{{ $showStatusDot }}"></span>
                                    <span>{{ $showStatusText }}</span>
                                </span>
                            @endif

                            <div class="dropdown d-inline-block">
                                <button type="button"
                                        class="tax-mode-pill tax-mode-{{ $showTaxMode }} dropdown-toggle"
                                        data-bs-toggle="dropdown"
                                        data-bs-auto-close="true"
                                        aria-expanded="false"
                                        id="show-tax-pill-{{ $order->id }}"
                                        title="Changer le régime TTC / HT">
                                    <span id="show-tax-label-{{ $order->id }}">{{ $order->tax_mode_label }}</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="show-tax-pill-{{ $order->id }}">
                                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Régime fiscal</li>
                                    <li>
                                        <button type="button" data-tax-mode="ttc" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $showTaxMode === 'ttc' ? 'active' : '' }}" onclick="quickUpdateShowTaxMode({{ $order->id }}, 'ttc')">
                                            <span><span class="status-dot tax-dot-ttc me-1.5"></span> TTC</span>
                                            @if($showTaxMode === 'ttc') <i class="fas fa-check text-primary small"></i> @endif
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" data-tax-mode="ht" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $showTaxMode === 'ht' ? 'active' : '' }}" onclick="quickUpdateShowTaxMode({{ $order->id }}, 'ht')">
                                            <span><span class="status-dot tax-dot-ht me-1.5"></span> HT</span>
                                            @if($showTaxMode === 'ht') <i class="fas fa-check text-warning small"></i> @endif
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            @php
                                $showDeliveryType = $order->effective_delivery_type;
                                $showDeliveryIcon = match ($showDeliveryType) {
                                    'our_team' => 'fa-people-carry-box',
                                    'ozon' => 'fa-box-open',
                                    default => 'fa-store',
                                };
                            @endphp
                            @if($canEditOrderShow)
                                <div class="dropdown d-inline-block">
                                    <button type="button"
                                            class="delivery-type-pill delivery-type-{{ $showDeliveryType }} dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            data-bs-auto-close="true"
                                            aria-expanded="false"
                                            id="show-delivery-pill-{{ $order->id }}"
                                            title="Changer le type de livraison">
                                        <i class="fas {{ $showDeliveryIcon }}" id="show-delivery-icon-{{ $order->id }}"></i>
                                        <span id="show-delivery-label-{{ $order->id }}">{{ $order->effective_delivery_type_label }}</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="show-delivery-pill-{{ $order->id }}">
                                        <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Type de livraison</li>
                                        <li>
                                            <button type="button" data-delivery-type="store" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $showDeliveryType === 'store' ? 'active' : '' }}" onclick="quickUpdateShowDeliveryType({{ $order->id }}, 'store')">
                                                <span><span class="status-dot delivery-dot-store me-1.5"></span> Store</span>
                                                @if($showDeliveryType === 'store') <i class="fas fa-check text-dark small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-delivery-type="our_team" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $showDeliveryType === 'our_team' ? 'active' : '' }}" onclick="quickUpdateShowDeliveryType({{ $order->id }}, 'our_team')">
                                                <span><span class="status-dot delivery-dot-our_team me-1.5"></span> Our Team</span>
                                                @if($showDeliveryType === 'our_team') <i class="fas fa-check text-primary small"></i> @endif
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" data-delivery-type="ozon" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $showDeliveryType === 'ozon' ? 'active' : '' }}" onclick="quickUpdateShowDeliveryType({{ $order->id }}, 'ozon')">
                                                <span><span class="status-dot delivery-dot-ozon me-1.5"></span> Ozon</span>
                                                @if($showDeliveryType === 'ozon') <i class="fas fa-check text-warning small"></i> @endif
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <span class="delivery-type-pill delivery-type-{{ $showDeliveryType }}" id="show-delivery-pill-{{ $order->id }}">
                                    <i class="fas {{ $showDeliveryIcon }}" id="show-delivery-icon-{{ $order->id }}"></i>
                                    <span id="show-delivery-label-{{ $order->id }}">{{ $order->effective_delivery_type_label }}</span>
                                </span>
                            @endif

                            <span class="brand-badge {{ $showPayBadgeClass }} d-inline-flex d-md-none ms-auto" style="font-size: 0.7rem;">
                                {{ $showPayShortLabel }}
                            </span>
                        </div>

                        <!-- Mobile Highlight Strip: Total + 1-Tap Call / WhatsApp (< 768px) -->
                        <div class="order-mobile-hero-total d-flex d-md-none">
                            <div class="min-w-0">
                                <div class="text-muted text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em; font-weight: 700;">
                                    Total <span id="show-mobile-tax-label">{{ $order->tax_mode_label }}</span> &bull; {{ $order->items->sum('quantity') }} article{{ $order->items->sum('quantity') > 1 ? 's' : '' }}
                                </div>
                                <div class="order-mobile-hero-amount">{{ currency($order->total) }}</div>
                            </div>
                            @if($order->customer_phone)
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <a href="tel:{{ $order->customer_phone }}" class="order-quick-contact-btn is-call" title="Appeler {{ $order->customer_name ?: 'le client' }}">
                                        <i class="fas fa-phone-alt"></i>
                                    </a>
                                    @if($showCleanPhone)
                                        <a href="https://wa.me/{{ $showCleanPhone }}" target="_blank" rel="noopener" class="order-quick-contact-btn is-whatsapp" title="WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action Desktop (>= 992px, mobile utilise la barre tactile fixe en bas) -->
                <div class="brand-header-actions d-none d-lg-flex align-items-center gap-2 flex-wrap flex-shrink-0">
                    <a href="{{ $order->isPosOrder() ? route('orders.pos') : route('orders.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary d-inline-flex align-items-center gap-1.5 shadow-2xs fw-semibold" title="Retour aux commandes">
                        <i class="fas fa-arrow-left"></i> <span>Retour</span>
                    </a>

                    @if(!$order->invoice)
                        <form action="{{ route('orders.generate-invoice', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs fw-semibold">
                                <i class="fas fa-file-invoice"></i> <span>Créer Facture</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white fw-semibold">
                            <i class="fas fa-file-invoice"></i> <span>Facture #{{ $order->invoice->invoice_number }}</span>
                        </a>
                        <a href="{{ route('invoices.delivery-note', $order->invoice) }}" class="btn btn-sm btn-info rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white fw-semibold" style="background: #0284c7; border-color: #0284c7;" title="Consulter le Bon de Livraison conforme Maroc">
                            <i class="fas fa-truck-loading"></i> <span>BL #{{ $order->invoice->delivery_note_number }}</span>
                        </a>
                    @endif

                    @if(!$order->quote)
                        <form action="{{ route('orders.generate-quote', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs fw-semibold" style="color: #b45309; border-color: #fde68a; background: #fffdf5;">
                                <i class="fas fa-file-alt"></i> <span>Créer Devis</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('invoices.show', $order->quote) }}" class="btn btn-sm btn-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm fw-semibold" style="color: #78350f;">
                            <i class="fas fa-file-alt"></i> <span>Devis #{{ $order->quote->invoice_number }}</span>
                        </a>
                    @endif

                    @if($canEditOrderShow)
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5 text-white fw-semibold" style="background: #dc2626; border-color: #dc2626;">
                            <i class="fas fa-pencil-alt"></i> <span>Modifier</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- KPI Strip (4 columns on Desktop, sleek 2x2 on Tablet/Mobile) -->
        <div class="order-show-kpi-grid">
            <div class="order-show-kpi-item d-none d-md-block">
                <div class="order-show-kpi-label">
                    <i class="fas fa-coins text-danger"></i>
                    <span>Total <span id="show-kpi-tax-label">{{ $order->tax_mode_label }}</span></span>
                </div>
                <div class="order-show-kpi-value order-money-strong text-danger">{{ currency($order->total) }}</div>
                <div class="order-show-kpi-sub">
                    Livraison : <strong>{{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : 'Gratuite' }}</strong>
                    @if($order->discount > 0)
                        &bull; Remise : <strong class="text-success">-{{ currency($order->discount) }}</strong>
                    @endif
                </div>
            </div>

            <div class="order-show-kpi-item">
                <div class="order-show-kpi-label">
                    <i class="fas fa-credit-card text-success"></i>
                    <span>Paiement</span>
                </div>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="brand-badge {{ $showPayBadgeClass }} py-0.5 px-2 d-none d-md-inline-flex" style="font-size: 0.72rem;">{{ $showPayShortLabel }}</span>
                    <span class="order-money-strong text-dark small">{{ $showMethodLabel }}</span>
                </div>
                <div class="order-show-kpi-sub">
                    @if((float) ($order->remaining_amount ?? 0) > 0)
                        Reste : <strong class="text-danger">{{ currency($order->remaining_amount) }}</strong>
                    @else
                        {{ $showPayFullText }}
                    @endif
                </div>
            </div>

            <div class="order-show-kpi-item">
                <div class="order-show-kpi-label">
                    <i class="fas fa-boxes-stacked text-primary"></i>
                    <span>Stock &amp; Articles</span>
                </div>
                <div class="order-show-kpi-value order-money-strong" style="font-size: 1.02rem;">
                    {{ $order->items->count() }} ligne{{ $order->items->count() > 1 ? 's' : '' }} &bull; {{ $order->items->sum('quantity') }} u.
                </div>
                <div class="order-show-kpi-sub">
                    @if($order->stock_deducted)
                        <span class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Stock déduit</span>
                    @else
                        <span class="text-warning-emphasis fw-semibold"><i class="fas fa-clock me-1"></i>En attente</span>
                    @endif
                </div>
            </div>

            <div class="order-show-kpi-item d-none d-md-block">
                <div class="order-show-kpi-label">
                    <i class="far fa-calendar-alt text-secondary"></i>
                    <span>Client &amp; Date</span>
                </div>
                <div class="order-money-strong text-dark text-truncate" style="font-size: 0.96rem;">{{ $order->customer_name ?: 'Client invité' }}</div>
                <div class="order-show-kpi-sub text-truncate">
                    {{ $order->created_at->format('d/m/Y à H:i') }}
                    @if($order->shipping_city)
                        &bull; {{ $order->shipping_city }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alerte Blacklist si client sur liste noire -->
    @if($order->is_blacklisted)
        <div class="alert alert-danger border-danger border-2 rounded-4 shadow-sm mb-4 d-flex align-items-start gap-3 p-3.5 bg-danger-subtle text-danger" role="alert">
            <div style="font-size: 1.6rem;" class="text-danger flex-shrink-0 mt-0.5">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="alert-heading fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                        <span>ATTENTION : Client sur Liste Noire</span>
                        <span class="badge bg-danger text-white">Alerte sécurité</span>
                    </h5>
                    <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}" onsubmit="return confirm('Confirmer le retrait de ce client de la liste noire ?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger bg-white shadow-2xs rounded-pill px-3 py-1 fw-semibold">
                            <i class="fas fa-check-circle me-1 text-success"></i> Retirer de la liste noire
                        </button>
                    </form>
                </div>
                <div class="mt-1 text-danger-emphasis small">
                    <strong>Motif :</strong> {{ $order->blacklist_reason ?: 'Client blacklisté (motif non spécifié).' }}
                    @if($order->customer_record && $order->customer_record->blacklisted_at)
                        <span class="opacity-75 ms-2">&bull; Date d'inscription : {{ $order->customer_record->blacklisted_at->format('d/m/Y à H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Stepper de progression de la commande (Compact App Card sur Mobile, Horizontal Stepper sur Desktop) -->
    @php
        $steps = ['pending', 'en_demande', 'reserved', 'processing', 'shipped', 'delivered'];
        $currentStatusIndex = array_search(strtolower($order->status), $steps);
        if (strtolower($order->status) === 'cancelled') $currentStatusIndex = -1;
        $stepLabels = [
            'pending' => ['name' => 'En attente', 'short' => 'Attente', 'icon' => 'fa-clock'],
            'en_demande' => ['name' => 'Sur Commande', 'short' => 'Cmd.', 'icon' => 'fa-clipboard-list'],
            'reserved' => ['name' => 'Réservé', 'short' => 'Réservé', 'icon' => 'fa-bookmark'],
            'processing' => ['name' => 'En préparation', 'short' => 'Prépa.', 'icon' => 'fa-cog'],
            'shipped' => ['name' => 'Expédiée', 'short' => 'Expédiée', 'icon' => 'fa-truck-fast'],
            'delivered' => ['name' => 'Livrée', 'short' => 'Livrée', 'icon' => 'fa-circle-check'],
        ];
        $activeStepIdx = ($currentStatusIndex !== false && $currentStatusIndex >= 0) ? (int) $currentStatusIndex : 0;
        $activeStepKey = $steps[$activeStepIdx] ?? 'pending';
        $stepPercent = ($currentStatusIndex !== false && $currentStatusIndex >= 0 && count($steps) > 1)
            ? round(($currentStatusIndex / (count($steps) - 1)) * 100)
            : 0;
    @endphp

    @if(strtolower($order->status) === 'cancelled')
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-3 mb-md-4 d-flex align-items-center gap-3 p-3">
            <i class="fas fa-times-circle fs-3 text-danger"></i>
            <div>
                <h6 class="fw-bold mb-0">Commande Annulée</h6>
                <div class="small opacity-75">Cette commande a été annulée le {{ $order->updated_at->format('d/m/Y à H:i') }}. Aucun envoi n'est prévu.</div>
            </div>
        </div>
    @else
        <!-- Mobile Compact Progress Card (< 768px) -->
        <div class="order-mobile-progress-card d-block d-md-none">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <span class="status-dot dot-{{ $showStatusDot }} flex-shrink-0"></span>
                    <span class="order-money-strong text-dark text-truncate" style="font-size: 0.88rem;">
                        {{ $stepLabels[$activeStepKey]['name'] ?? $showStatusText }}
                    </span>
                </div>
                <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                    Étape {{ $activeStepIdx + 1 }} / {{ count($steps) }}
                </span>
            </div>
            <div class="order-mobile-progress-track">
                <div class="order-mobile-progress-fill fill-{{ $normalizedShowStatus }}" style="width: {{ max(8, $stepPercent) }}%;"></div>
            </div>
            <div class="order-mobile-steps-dots">
                @foreach($steps as $idx => $stKey)
                    @php
                        $isDone = $idx < $currentStatusIndex;
                        $isCur = $idx === $currentStatusIndex;
                        $chipState = $isDone ? 'is-done' : ($isCur ? 'is-current' : '');
                    @endphp
                    <div class="order-mobile-step-chip {{ $chipState }}">
                        <div class="order-mobile-step-dot">
                            @if($isDone)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $stepLabels[$stKey]['icon'] }}"></i>
                            @endif
                        </div>
                        <span class="order-mobile-step-lbl">{{ $stepLabels[$stKey]['short'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Desktop Full Horizontal Stepper (>= 768px) -->
        <div class="order-stepper-wrap d-none d-md-block mb-4">
            <div class="order-stepper">
                <div class="order-stepper-track">
                    <div class="order-stepper-progress" style="width: {{ $stepPercent }}%;"></div>
                </div>

                @foreach($steps as $idx => $stKey)
                    @php
                        $isDone = $idx < $currentStatusIndex;
                        $isCur = $idx === $currentStatusIndex;
                        $stClass = $isDone ? 'completed' : ($isCur ? 'active' : '');
                    @endphp
                    <div class="order-step-item {{ $stClass }}">
                        <div class="order-step-circle">
                            @if($isDone)
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $stepLabels[$stKey]['icon'] }}"></i>
                            @endif
                        </div>
                        <div class="order-step-text">
                            {{ $stepLabels[$stKey]['name'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Mobile / Tablet Sticky Quick-Jump Section Pills (< 992px) -->
    <div class="order-mobile-nav-pills d-flex d-lg-none" aria-label="Navigation rapide de la commande">
        <a href="#orderItemsSection" class="order-mobile-jump-pill">
            <i class="fas fa-box-open text-danger"></i>
            <span>Articles ({{ $order->items->count() }})</span>
        </a>
        <a href="#orderSummarySection" class="order-mobile-jump-pill">
            <i class="fas fa-calculator text-success"></i>
            <span>Résumé &amp; Docs</span>
        </a>
        <a href="#orderClientSection" class="order-mobile-jump-pill">
            <i class="fas fa-user text-primary"></i>
            <span>Client</span>
        </a>
        <a href="#orderHistorySection" class="order-mobile-jump-pill">
            <i class="fas fa-clock-rotate-left text-secondary"></i>
            <span>Historique ({{ ($orderHistory ?? collect())->count() }})</span>
        </a>
    </div>

    @php $forcedOosItems = $order->forcedOutOfStockItems(); @endphp
    @if($forcedOosItems->isNotEmpty())
        <div class="alert alert-warning border border-warning-subtle rounded-4 shadow-2xs mb-4 d-flex align-items-start gap-3" role="alert">
            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Produit(s) en rupture de stock réelle (affiché(s) « En stock » au client)</h6>
                <p class="small mb-1 text-dark">
                    Cette commande contient {{ $forcedOosItems->count() > 1 ? 'des articles actuellement hors stock' : 'un article actuellement hors stock' }} avec l'option <strong>Toujours en stock</strong>. Contactez le client pour lui proposer une alternative si nécessaire :
                </p>
                <ul class="small mb-0 ps-3 fw-semibold text-dark">
                    @foreach($forcedOosItems as $oosItem)
                        <li>{{ $oosItem->display_name }} <span class="text-muted fw-normal">(qté commandée : {{ $oosItem->quantity }})</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Colonne Principale (Gauche) : Articles et Notes -->
        <div class="col-12 col-lg-8">
            <!-- Articles commandés -->
            <div class="product-form-card mb-4" id="orderItemsSection">
                <div class="product-form-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="product-form-card-title m-0">
                            <i class="fas fa-box-open text-danger"></i> Articles commandés
                            <span class="badge rounded-pill bg-light text-secondary border px-2.5 ms-1" style="font-size: 0.72rem;">
                                {{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }}
                            </span>
                        </h2>
                        @if($order->stock_deducted)
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                <i class="fas fa-check-circle me-1"></i>Stock déduit
                            </span>
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.72rem;">
                                <i class="fas fa-clock me-1"></i>Prélèvement en attente
                            </span>
                        @endif
                    </div>

                    @if(isset($stores) && $stores->isNotEmpty() && $order->items->isNotEmpty())
                        <form action="{{ route('orders.update-stores', $order) }}" method="POST" class="d-flex align-items-center gap-1.5 m-0 w-100 w-sm-auto justify-content-end">
                            @csrf
                            <input type="hidden" name="apply_all" value="1">
                            <select name="bulk_store_id" class="form-select form-select-sm flex-grow-1 flex-sm-grow-0" style="min-width: 155px; max-width: 220px;" title="Affecter tous les articles à un magasin ou dépôt">
                                <option value="">— Tout désaffecter —</option>
                                @foreach($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->code }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 fw-semibold text-nowrap flex-shrink-0" title="Appliquer ce dépôt à tous les articles">
                                <i class="fas fa-warehouse me-1"></i>Tout affecter
                            </button>
                        </form>
                    @endif
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 rounded-3 py-2 px-3 small mb-3">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0 order-items-table">
                        <thead>
                            <tr>
                                <th class="border-0 ps-3">Produit</th>
                                <th class="border-0" style="min-width: 225px;">Magasin / Dépôt source</th>
                                <th class="border-0 text-center">P.U.</th>
                                <th class="border-0 text-center">Quantité</th>
                                <th class="border-0 text-end pe-3">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @php
                                    $itemStoreStocks = $item->product ? $item->product->storeStocks->keyBy('store_id') : collect();
                                    $hasItemStoreAllocations = $itemStoreStocks->isNotEmpty();
                                    $assignedStockRow = $item->store_id ? $itemStoreStocks->get($item->store_id) : null;
                                @endphp
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="order-item-thumb-box">
                                                <img src="{{ $item->product_image }}" 
                                                     alt="{{ $item->clean_product_name }}" 
                                                     style="width: 100%; height: 100%; object-fit: contain;"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                                     loading="lazy">
                                            </div>
                                            <div>
                                                @if($item->product)
                                                    <a href="{{ route('products.edit', $item->product) }}" class="fw-bold text-dark text-decoration-none d-block hover-danger">
                                                        {{ $item->clean_product_name }}
                                                    </a>
                                                @else
                                                    <span class="fw-bold text-dark d-block">{{ $item->clean_product_name }}</span>
                                                @endif
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                    <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}</span>
                                                    @if($item->variant_name)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.72rem;">
                                                            <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                                        </span>
                                                    @endif
                                                    @if($item->isForcedOutOfStock())
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-normal" style="font-size: 0.72rem;" title="Ce produit a un stock réel à 0 mais est forcé « En stock »">
                                                            <i class="fas fa-triangle-exclamation text-warning me-1"></i>Hors stock réel (Forcé en stock)
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(strtolower((string) $order->status) !== 'cancelled' && (auth()->user()?->can('manage_orders') || auth()->user()?->can('edit_orders')))
                                                    <div class="mt-1.5">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 fw-semibold replace-item-trigger"
                                                                style="font-size: 0.72rem;"
                                                                onclick="openReplaceItemModal(this)"
                                                                data-item-id="{{ $item->id }}"
                                                                data-replace-url="{{ route('orders.items.replace', [$order, $item]) }}"
                                                                data-product-id="{{ $item->product_id }}"
                                                                data-product-name="{{ $item->clean_product_name }}"
                                                                data-product-sku="{{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}"
                                                                data-variant-name="{{ $item->variant_name ?? '' }}"
                                                                data-product-image="{{ $item->product_image }}"
                                                                data-store-id="{{ $item->store_id ?? '' }}"
                                                                data-store-name="{{ $item->store?->name ?? 'Non assigné' }}"
                                                                data-price="{{ (float) $item->price }}"
                                                                data-quantity="{{ (int) $item->quantity }}"
                                                                data-subtotal="{{ (float) ($item->subtotal ?? ($item->price * $item->quantity)) }}">
                                                            <i class="fas fa-right-left me-1"></i>Changer produit
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <form action="{{ route('orders.items.update-store', [$order, $item]) }}"
                                              method="POST"
                                              class="item-store-form m-0"
                                              data-item-id="{{ $item->id }}">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <select name="store_id"
                                                        class="form-select form-select-sm item-store-select"
                                                        aria-label="Magasin ou dépôt pour {{ $item->clean_product_name }}">
                                                    <option value="">— Non assigné —</option>
                                                    @foreach($stores ?? [] as $st)
                                                        @php
                                                            $stStock = $itemStoreStocks->get($st->id);
                                                            $stQty = $stStock ? (int) $stStock->quantity : 0;
                                                            $stLoc = $stStock?->location_code ? ' [' . $stStock->location_code . ']' : '';
                                                            $stLabel = $st->name . ($hasItemStoreAllocations ? " — Stock: {$stQty}{$stLoc}" : '');
                                                        @endphp
                                                        <option value="{{ $st->id }}" {{ (int) $item->store_id === (int) $st->id ? 'selected' : '' }}>
                                                            {{ $stLabel }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-outline-primary px-2" title="Enregistrer ce dépôt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap item-store-meta" id="item-store-meta-{{ $item->id }}">
                                                @if($item->store)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">
                                                        <i class="fas fa-warehouse me-1"></i>{{ $item->store->name }}
                                                    </span>
                                                    @if($assignedStockRow?->location_code)
                                                        <span class="badge bg-light text-secondary border" style="font-size: 0.68rem;">
                                                            <i class="fas fa-map-pin text-danger me-1"></i>{{ $assignedStockRow->location_code }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;">
                                                        <i class="fas fa-exclamation-circle me-1"></i>Dépôt à attribuer
                                                    </span>
                                                @endif
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center py-3 fw-semibold text-secondary tabular-nums">
                                        @if($item->original_price && (float) $item->original_price > (float) $item->price)
                                            <del class="text-muted small d-block">{{ currency($item->original_price) }}</del>
                                            <span class="text-success fw-bold">{{ currency($item->price) }}</span>
                                        @else
                                            {{ currency($item->price) }}
                                        @endif
                                    </td>
                                    <td class="text-center py-3 tabular-nums">
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold" style="border-radius: 8px;">
                                            x{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 py-3 fw-bold text-dark font-heading tabular-nums">
                                        {{ currency($item->subtotal ?? ($item->price * $item->quantity)) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if($order->items->isNotEmpty())
                            @php
                                $itemsGrossTotal = (float) $order->items->sum(fn ($it) => $it->subtotal ?? ($it->price * $it->quantity));
                            @endphp
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="ps-3 small text-muted fw-semibold">
                                        Total des articles commandés
                                    </td>
                                    <td class="text-center tabular-nums">
                                        <span class="badge bg-white text-dark border fw-bold px-2.5 py-1">{{ $order->items->sum('quantity') }} u.</span>
                                    </td>
                                    <td class="text-end pe-3 fw-extrabold text-dark font-heading tabular-nums">
                                        {{ currency($itemsGrossTotal) }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Mobile Items Cards (Touch-friendly) -->
                <div class="d-block d-md-none">
                    @foreach($order->items as $item)
                        @php
                            $itemStoreStocks = $item->product ? $item->product->storeStocks->keyBy('store_id') : collect();
                            $hasItemStoreAllocations = $itemStoreStocks->isNotEmpty();
                        @endphp
                        <div class="order-mobile-item-card mb-2.5">
                            <div class="d-flex align-items-start gap-2.5">
                                <div class="order-item-thumb-box" style="width: 56px; height: 56px;">
                                    <img src="{{ $item->product_image }}" 
                                         alt="{{ $item->clean_product_name }}" 
                                         style="width: 100%; height: 100%; object-fit: contain;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                         loading="lazy">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    @if($item->product)
                                        <a href="{{ route('products.edit', $item->product) }}" class="order-mobile-item-name">
                                            {{ $item->clean_product_name }}
                                        </a>
                                    @else
                                        <div class="order-mobile-item-name">
                                            {{ $item->clean_product_name }}
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                        <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}</span>
                                        @if($item->variant_name)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.7rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                            </span>
                                        @endif
                                        @if($item->isForcedOutOfStock())
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-normal" style="font-size: 0.7rem;">
                                                <i class="fas fa-triangle-exclamation text-warning me-1"></i>Hors stock réel (Forcé)
                                            </span>
                                        @endif
                                    </div>
                                    <form action="{{ route('orders.items.update-store', [$order, $item]) }}"
                                          method="POST"
                                          class="item-store-form mt-2.5 m-0"
                                          data-item-id="{{ $item->id }}">
                                        @csrf
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-primary px-2.5"><i class="fas fa-warehouse"></i></span>
                                            <select name="store_id" class="form-select item-store-select" style="min-height: 40px; font-size: 0.84rem;" aria-label="Dépôt pour {{ $item->clean_product_name }}">
                                                <option value="">— Non assigné —</option>
                                                @foreach($stores ?? [] as $st)
                                                    @php
                                                        $stStock = $itemStoreStocks->get($st->id);
                                                        $stQty = $stStock ? (int) $stStock->quantity : 0;
                                                        $stLoc = $stStock?->location_code ? ' [' . $stStock->location_code . ']' : '';
                                                        $stLabel = $st->name . ($hasItemStoreAllocations ? " — {$stQty} dispo{$stLoc}" : '');
                                                    @endphp
                                                    <option value="{{ $st->id }}" {{ (int) $item->store_id === (int) $st->id ? 'selected' : '' }}>
                                                        {{ $stLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-outline-primary px-3" style="min-height: 40px;" title="Enregistrer">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                    <div class="order-mobile-item-pricebar tabular-nums">
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            @if($item->original_price && (float) $item->original_price > (float) $item->price)
                                                <del class="text-muted me-1">{{ currency($item->original_price) }}</del>
                                                <span class="text-success order-money-strong">{{ currency($item->price) }}</span> &times; {{ $item->quantity }}
                                            @else
                                                {{ currency($item->price) }} &times; {{ $item->quantity }}
                                            @endif
                                        </span>
                                        <span class="order-money-strong text-dark" style="font-size: 0.96rem;">
                                            {{ currency($item->subtotal ?? ($item->price * $item->quantity)) }}
                                        </span>
                                    </div>
                                    @if(strtolower((string) $order->status) !== 'cancelled' && (auth()->user()?->can('manage_orders') || auth()->user()?->can('edit_orders')))
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary w-100 fw-semibold replace-item-trigger"
                                                    style="font-size: 0.78rem; min-height: 38px;"
                                                    onclick="openReplaceItemModal(this)"
                                                    data-item-id="{{ $item->id }}"
                                                    data-replace-url="{{ route('orders.items.replace', [$order, $item]) }}"
                                                    data-product-id="{{ $item->product_id }}"
                                                    data-product-name="{{ $item->clean_product_name }}"
                                                    data-product-sku="{{ $item->product_sku ?: ($item->product?->sku ?? 'N/A') }}"
                                                    data-variant-name="{{ $item->variant_name ?? '' }}"
                                                    data-product-image="{{ $item->product_image }}"
                                                    data-store-id="{{ $item->store_id ?? '' }}"
                                                    data-store-name="{{ $item->store?->name ?? 'Non assigné' }}"
                                                    data-price="{{ (float) $item->price }}"
                                                    data-quantity="{{ (int) $item->quantity }}"
                                                    data-subtotal="{{ (float) ($item->subtotal ?? ($item->price * $item->quantity)) }}">
                                                <i class="fas fa-right-left me-1.5"></i>Changer produit / Stock
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes de la commande -->
            @if($order->notes)
                <div class="product-form-card mb-4">
                    <div class="product-form-card-header">
                        <h2 class="product-form-card-title">
                            <i class="fas fa-comment-dots text-warning"></i> Notes & Remarques
                        </h2>
                    </div>
                    <div class="p-3 rounded-3 bg-light border text-secondary" style="font-style: italic; line-height: 1.6; border-left: 3px solid #dc2626 !important;">
                        &ldquo;{{ $order->notes }}&rdquo;
                    </div>
                </div>
            @endif

            <!-- Historique complet & Traçabilité -->
            @php
                $historyCollection = $orderHistory ?? collect();
                $histStatusCount = $historyCollection->where('category', 'status')->count();
                $histStockCount = $historyCollection->where('category', 'stock')->count();
                $histPaymentCount = $historyCollection->where('category', 'payment')->count();
            @endphp
            <div class="product-form-card" id="orderHistorySection">
                <div class="product-form-card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h2 class="product-form-card-title m-0">
                        <i class="fas fa-clock-rotate-left text-danger"></i> Historique complet &amp; Traçabilité
                        <span class="badge bg-light text-dark border rounded-pill ms-1.5" id="orderHistoryVisibleCount">{{ $historyCollection->count() }}</span>
                    </h2>
                    <div class="d-flex flex-wrap gap-1.5 order-scroll-pills" id="orderHistoryFilters">
                        <button type="button" class="order-history-filter-pill active" data-history-cat="all">
                            Tout ({{ $historyCollection->count() }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="status">
                            Statuts &amp; Modifs ({{ $histStatusCount }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="stock">
                            Stock ({{ $histStockCount }})
                        </button>
                        <button type="button" class="order-history-filter-pill" data-history-cat="payment">
                            Paiements ({{ $histPaymentCount }})
                        </button>
                    </div>
                </div>

                @if($historyCollection->isNotEmpty())
                    <div class="order-history-timeline mt-2" id="orderHistoryTimeline">
                        @foreach($historyCollection as $event)
                            <div class="order-history-item" data-history-cat="{{ $event->category }}">
                                <div class="order-history-node {{ $event->badge_class }} border">
                                    <i class="{{ $event->icon }}"></i>
                                </div>
                                <div class="order-history-card">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                            {{ html_entity_decode((string) $event->title, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                        </div>
                                        <div class="small text-muted d-flex align-items-center gap-2">
                                            @if($event->timestamp)
                                                <span title="{{ $event->timestamp->format('d/m/Y H:i:s') }}">
                                                    <i class="far fa-clock me-1"></i>{{ $event->timestamp->format('d/m/Y à H:i') }}
                                                </span>
                                                <span class="badge bg-white text-secondary border rounded-pill" style="font-size: 0.68rem;">
                                                    {{ $event->timestamp->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($event->description)
                                        <div class="small text-secondary mb-2">{{ html_entity_decode((string) $event->description, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</div>
                                    @endif

                                    @if(!empty($event->changes))
                                        <div class="d-flex flex-wrap gap-1.5 mb-2">
                                            @foreach($event->changes as $chg)
                                                <div class="order-diff-chip">
                                                    <span class="fw-semibold text-secondary">{{ $chg['field'] }} :</span>
                                                    @if($chg['old'] !== null)
                                                        <span class="text-danger text-decoration-line-through">{{ html_entity_decode((string) $chg['old'], ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</span>
                                                        <i class="fas fa-arrow-right text-muted" style="font-size: 0.65rem;"></i>
                                                    @endif
                                                    <span class="fw-bold text-dark">{{ html_entity_decode((string) $chg['new'], ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(!empty($event->meta))
                                        <div class="d-flex flex-wrap gap-1.5 mb-2">
                                            @foreach($event->meta as $mKey => $mVal)
                                                <div class="order-diff-chip">
                                                    <span class="fw-semibold text-secondary">{{ $mKey }} :</span>
                                                    <span class="fw-bold text-dark">{{ html_entity_decode((string) $mVal, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-1.5 border-top border-light-subtle text-muted" style="font-size: 0.74rem;">
                                        <span>
                                            <i class="fas fa-user-shield me-1 text-secondary"></i>
                                            Par : <strong class="text-dark">{{ $event->actor }}</strong>
                                        </span>
                                        @if(!empty($event->ip))
                                            <span class="font-monospace">IP: {{ $event->ip }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="orderHistoryEmptyCat" class="text-center py-4 text-muted small d-none">
                        <i class="fas fa-filter fa-lg mb-2 opacity-25 d-block"></i>
                        Aucun événement dans cette catégorie pour cette commande.
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        <i class="fas fa-history fa-2x mb-2 opacity-25 d-block"></i>
                        Aucun historique détaillé enregistré pour cette commande.
                    </div>
                @endif
            </div>
        </div>

        <!-- Colonne Latérale (Droite) : Récapitulatif, Client & Paiement -->
        <div class="col-12 col-lg-4">
            <div class="order-show-sidebar">
            <!-- Récapitulatif Financier -->
            <div class="product-form-card mb-4" id="orderSummarySection">
                <div class="product-form-card-header d-flex align-items-center justify-content-between gap-2">
                    <h2 class="product-form-card-title mb-0">
                        <i class="fas fa-calculator text-success"></i> Récapitulatif Financier
                    </h2>
                    <span class="tax-mode-pill tax-mode-{{ $order->normalized_tax_mode }}" id="show-summary-tax-pill">{{ $order->tax_mode_label }}</span>
                </div>

                <div class="d-flex flex-column gap-2.5 tabular-nums">
                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>Sous-total HT</span>
                        <span class="fw-semibold text-dark">{{ currency($order->subtotal) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%)</span>
                        <span class="fw-semibold text-dark">{{ currency($order->tax) }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-secondary small">
                        <span>Frais de livraison</span>
                        <span class="fw-semibold text-dark">{{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : 'Gratuit' }}</span>
                    </div>

                    @if($order->discount > 0)
                        <div class="d-flex justify-content-between align-items-center text-success small">
                            <span>Remise</span>
                            <span class="fw-semibold">-{{ currency($order->discount) }}</span>
                        </div>
                    @endif

                    <div class="order-total-highlight d-flex justify-content-between align-items-center mt-1">
                        <span class="order-money-strong text-dark fs-6">Total <span id="show-total-tax-label">{{ $order->tax_mode_label }}</span></span>
                        <span class="order-money-strong text-danger fs-4">{{ currency($order->total) }}</span>
                    </div>

                    @if((float) ($order->remaining_amount ?? 0) > 0 && (float) $order->total > 0)
                        @php
                            $paidProgressPct = min(100, max(0, round((((float) ($order->paid_amount ?? 0)) / (float) $order->total) * 100)));
                        @endphp
                        <div class="order-info-tile mt-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Réglé : <strong class="text-success">{{ currency($order->paid_amount ?? 0) }}</strong></span>
                                <span class="text-muted">Reste : <strong class="text-danger">{{ currency($order->remaining_amount) }}</strong></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 999px; background: #e2e8f0;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $paidProgressPct }}%;" aria-valuenow="{{ $paidProgressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Statut du Paiement -->
                    <div class="mt-1 text-center p-2.5 rounded-pill {{ $showPayBadgeClass }} fw-semibold small">
                        <i class="fas fa-credit-card me-1.5"></i> {{ $showPayFullText }}
                    </div>

                    <!-- Accès rapide Documents (Mobile & Tablette) -->
                    <div class="d-flex d-lg-none flex-wrap gap-2 mt-1 pt-2.5 border-top">
                        @if(!$order->invoice)
                            <form action="{{ route('orders.generate-invoice', $order) }}" method="POST" class="flex-fill m-0 p-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success w-100 fw-semibold">
                                    <i class="fas fa-file-invoice me-1"></i>Créer Facture
                                </button>
                            </form>
                        @else
                            <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-sm btn-success flex-fill fw-semibold text-white">
                                <i class="fas fa-file-invoice me-1"></i>Facture #{{ $order->invoice->invoice_number }}
                            </a>
                            <a href="{{ route('invoices.delivery-note', $order->invoice) }}" class="btn btn-sm btn-info flex-fill fw-semibold text-white" style="background: #0284c7; border-color: #0284c7;">
                                <i class="fas fa-truck-loading me-1"></i>BL #{{ $order->invoice->delivery_note_number }}
                            </a>
                        @endif

                        @if(!$order->quote)
                            <form action="{{ route('orders.generate-quote', $order) }}" method="POST" class="flex-fill m-0 p-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning w-100 fw-semibold" style="color: #b45309; border-color: #fde68a; background: #fffdf5;">
                                    <i class="fas fa-file-alt me-1"></i>Créer Devis
                                </button>
                            </form>
                        @else
                            <a href="{{ route('invoices.show', $order->quote) }}" class="btn btn-sm btn-warning flex-fill fw-semibold" style="color: #78350f;">
                                <i class="fas fa-file-alt me-1"></i>Devis #{{ $order->quote->invoice_number }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Fiche Client & Livraison -->
            <div class="product-form-card mb-4 {{ $order->is_blacklisted ? 'border-danger' : '' }}" id="orderClientSection">
                <div class="product-form-card-header d-flex align-items-center justify-content-between">
                    <h2 class="product-form-card-title m-0">
                        <i class="fas fa-user-circle text-primary"></i> Client & Livraison
                    </h2>
                    @if($order->is_blacklisted)
                        <span class="badge bg-danger text-white fw-bold px-2 py-1" style="font-size: 0.72rem;">
                            <i class="fas fa-ban me-1"></i>Liste Noire
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: {{ $order->is_blacklisted ? 'linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%)' : 'linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)' }}; color: {{ $order->is_blacklisted ? '#dc2626' : '#2563eb' }}; font-weight: 800; font-size: 1.15rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid {{ $order->is_blacklisted ? '#f87171' : '#bfdbfe' }}; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);">
                        @if($order->is_blacklisted)
                            <i class="fas fa-ban"></i>
                        @else
                            {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <span class="fw-bold text-dark text-truncate">{{ $order->customer_name ?: 'Client invité' }}</span>
                            @php $tier = $order->customer_tier; @endphp
                            <span class="badge {{ $tier['badge_class'] }}" style="font-size: 0.7rem; padding: 2px 6px;" title="{{ $tier['description'] }}">
                                <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                            </span>
                        </div>
                        <div class="text-muted small mt-0.5">
                            @if($order->customer_record)
                                <span>{{ $order->customer_record->total_orders }} commande(s) &bull; {{ currency($order->customer_record->total_spent) }}</span>
                            @else
                                <span>Nouvelle commande enregistrée</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 text-secondary small">
                    @if($order->customer_email)
                        <div class="order-info-tile d-flex align-items-center gap-2">
                            <i class="far fa-envelope text-primary" style="width: 16px;"></i>
                            <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none text-dark hover-danger text-truncate fw-medium">{{ $order->customer_email }}</a>
                        </div>
                    @endif

                    @if($order->customer_phone)
                        <div class="order-info-tile d-flex align-items-center gap-2">
                            <i class="fas fa-phone-alt text-primary" style="width: 16px;"></i>
                            <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-dark hover-danger fw-semibold">{{ $order->customer_phone }}</a>
                            @php
                                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '212' . substr($cleanPhone, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none ms-auto px-2 py-1" title="Contacter sur WhatsApp">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                    @endif

                    @if($order->shipping_address)
                        <div class="order-info-tile d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt text-danger mt-1" style="width: 16px;"></i>
                            <div>
                                <span class="fw-semibold text-dark d-block mb-0.5">Adresse de livraison</span>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }} {{ $order->shipping_zip ? '(' . $order->shipping_zip . ')' : '' }}
                                <span class="text-muted">• {{ $order->shipping_country ?: 'Maroc' }}</span>
                            </div>
                        </div>
                    @endif

                    @php
                        $tileDeliveryColor = match ($order->effective_delivery_type) {
                            'our_team' => '#1d4ed8',
                            'ozon' => '#b45309',
                            default => '#0f172a',
                        };
                        $tileDeliveryIcon = match ($order->effective_delivery_type) {
                            'our_team' => 'fa-people-carry-box',
                            'ozon' => 'fa-box-open',
                            default => 'fa-store',
                        };
                    @endphp
                    <div class="order-info-tile d-flex align-items-center justify-content-between gap-2" id="show-delivery-tile-wrap">
                        <span class="text-muted">
                            <i class="fas fa-shipping-fast me-1 text-danger"></i>Type de livraison
                        </span>
                        <span class="fw-bold d-inline-flex align-items-center gap-1" style="color: {{ $tileDeliveryColor }};" id="show-delivery-tile-val">
                            <i class="fas {{ $tileDeliveryIcon }}" id="show-delivery-tile-icon"></i>
                            <span id="show-delivery-tile-label">{{ $order->effective_delivery_type_label }}</span>
                        </span>
                    </div>

                    @if($order->isPosOrder() || $order->createdByUser)
                        <div class="order-info-tile d-flex align-items-center justify-content-between gap-2" id="show-created-by-tile">
                            <span class="text-muted">
                                <i class="fas fa-user-tie me-1 text-primary"></i>{{ $order->isPosOrder() ? 'Créée par (POS)' : 'Créée par' }}
                            </span>
                            <span class="fw-bold text-dark">
                                {{ $order->createdByUser?->name ?? 'Utilisateur inconnu' }}
                            </span>
                        </div>
                    @endif

                    @if(!$order->isPosOrder() || $order->confirmedByUser)
                        <div class="order-info-tile d-flex align-items-center justify-content-between gap-2" id="show-confirmed-by-tile">
                            <span class="text-muted">
                                <i class="fas fa-user-check me-1 text-success"></i>Confirmée par
                            </span>
                            @if($order->confirmedByUser)
                                <div class="text-end">
                                    <div class="fw-bold text-success">{{ $order->confirmedByUser->name }}</div>
                                    @if($order->confirmed_at)
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $order->confirmed_at->format('d/m/Y à H:i') }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted fst-italic">En attente de confirmation</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Actions Blacklist et Historique Client -->
                <div class="pt-3 mt-3 border-top d-flex flex-column gap-2">
                    @if($order->customer_record)
                        <a href="{{ route('customers.show', $order->customer_record) }}" class="btn btn-sm btn-light border rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold shadow-2xs">
                            <i class="fas fa-user-clock text-primary"></i> <span>Voir l'historique complet du client</span>
                        </a>
                    @endif

                    @if($order->is_blacklisted)
                        <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}" onsubmit="return confirm('Confirmer le retrait de ce client de la liste noire ?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold">
                                <i class="fas fa-check-circle"></i> <span>Retirer de la Liste Noire</span>
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#blacklistOrderModal">
                            <i class="fas fa-ban"></i> <span>Ajouter à la Liste Noire</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Détails du Règlement -->
            <div class="product-form-card">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-money-check-dollar text-secondary"></i> Modalités de Paiement
                    </h2>
                </div>

                <div class="d-flex flex-column gap-2.5 small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Mode :</span>
                        @php
                            $orderMethodDisplay = match(strtolower((string) $order->payment_method)) {
                                'cash' => 'Espèces',
                                'card' => 'Carte bancaire',
                                'virement' => 'Virement bancaire',
                                'credit' => 'Crédit',
                                'other' => 'Autre',
                                default => ucfirst(str_replace('_', ' ', $order->payment_method ?: 'Non spécifié')),
                            };
                        @endphp
                        <span class="fw-bold text-dark">{{ $orderMethodDisplay }}</span>
                    </div>

                    @if($order->payment_status === 'partial' || (float) ($order->remaining_amount ?? 0) > 0)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Payé :</span>
                            <span class="fw-bold text-success">{{ currency($order->paid_amount ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Reste à payer :</span>
                            <span class="fw-bold text-danger">{{ currency($order->remaining_amount ?? 0) }}</span>
                        </div>
                        @if($order->payment_due_date)
                            @php
                                $isOverdue = $order->payment_due_date->isPast() && !$order->payment_due_date->isToday() && $order->payment_status !== 'paid';
                            @endphp
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Échéance :</span>
                                <span class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-dark' }}">
                                    {{ $order->payment_due_date->format('d/m/Y') }}
                                    @if($isOverdue)
                                        <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.68rem;">En retard</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endif

                    @if($order->transaction_id)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Réf. Transaction :</span>
                            <span class="font-monospace fw-semibold text-dark">{{ $order->transaction_id }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Date d'enregistrement :</span>
                        <span class="text-dark">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>

                    @if($order->payment_status !== 'paid' && $order->customer_id)
                        <div class="pt-2.5 mt-1 border-top">
                            <a href="{{ route('customers.show', $order->customer_id) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold w-100">
                                <i class="fas fa-wallet me-1.5"></i> Encaisser / Solde Client ({{ currency($order->customer->current_balance ?? $order->total) }})
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Barre d'actions tactile fixe en bas (Mobile & Tablette < 992px) -->
    <div class="order-show-mobile-dock d-flex d-lg-none" aria-label="Actions rapides commande">
        @if(!$order->invoice)
            <form action="{{ route('orders.generate-invoice', $order) }}" method="POST" class="d-flex flex-fill m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-outline-success order-dock-btn w-100">
                    <i class="fas fa-file-invoice"></i> <span>Facture</span>
                </button>
            </form>
        @else
            <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-success order-dock-btn text-white">
                <i class="fas fa-file-invoice"></i> <span>Facture</span>
            </a>
        @endif

        @if($order->invoice)
            <a href="{{ route('invoices.delivery-note', $order->invoice) }}" class="btn btn-info order-dock-btn text-white" style="background: #0284c7; border-color: #0284c7;">
                <i class="fas fa-truck-loading"></i> <span>BL</span>
            </a>
        @elseif($order->quote)
            <a href="{{ route('invoices.show', $order->quote) }}" class="btn btn-warning order-dock-btn" style="color: #78350f;">
                <i class="fas fa-file-alt"></i> <span>Devis</span>
            </a>
        @else
            <form action="{{ route('orders.generate-quote', $order) }}" method="POST" class="d-flex flex-fill m-0 p-0">
                @csrf
                <button type="submit" class="btn btn-outline-warning order-dock-btn w-100" style="color: #b45309; border-color: #fde68a; background: #fffdf5;">
                    <i class="fas fa-file-alt"></i> <span>Devis</span>
                </button>
            </form>
        @endif

        @if($showCleanPhone)
            <a href="https://wa.me/{{ $showCleanPhone }}"
               target="_blank"
               rel="noopener"
               class="btn btn-success order-dock-icon-btn text-white"
               style="background: #16a34a; border-color: #16a34a;"
               title="Contacter sur WhatsApp">
                <i class="fab fa-whatsapp fs-6"></i>
            </a>
        @endif

        @if($canEditOrderShow)
            <a href="{{ route('orders.edit', $order) }}"
               class="btn btn-danger order-dock-btn text-white"
               style="background: #dc2626; border-color: #dc2626;">
                <i class="fas fa-pencil-alt"></i> <span>Modifier</span>
            </a>
        @endif
    </div>
    </div> <!-- /.order-show-page -->

    <!-- Modal d'ajout à la Liste Noire -->
    <div class="modal fade" id="blacklistOrderModal" tabindex="-1" aria-labelledby="blacklistOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3" style="background: #fef2f2; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #fecaca;">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-danger mb-0" id="blacklistOrderModalLabel">Placer sur Liste Noire</h5>
                            <div class="text-muted small">Ce client sera signalé sur toutes ses commandes futures</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('orders.toggle-blacklist', $order) }}">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="small text-muted mb-1">Client concerné :</div>
                            <div class="fw-bold text-dark fs-6">{{ $order->customer_name ?: 'Client sans nom' }}</div>
                            <div class="small text-secondary">{{ $order->customer_phone ?: $order->customer_email }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Motif du blacklistage <span class="text-danger">*</span></label>
                            <select name="reason_preset" class="form-select form-select-sm mb-2" onchange="if(this.value){ document.getElementById('customReason').value = this.value; }">
                                <option value="">-- Choisir un motif fréquent --</option>
                                <option value="Colis refusé à la livraison sans motif">Colis refusé à la livraison sans motif</option>
                                <option value="Fausse commande / Numéro injoignable">Fausse commande / Numéro injoignable</option>
                                <option value="Impayé / Chèque rejeté">Impayé / Chèque rejeté</option>
                                <option value="Comportement abusif / Harcèlement">Comportement abusif / Harcèlement</option>
                                <option value="Retours répétés injustifiés">Retours répétés injustifiés</option>
                            </select>
                            <textarea name="reason" id="customReason" class="form-control" rows="3" placeholder="Précisez la raison détaillée du blacklistage..." required>Colis refusé à la livraison sans motif</textarea>
                            <div class="form-text small">Cette note sera visible par tous les administrateurs lors de la réception de nouvelles commandes.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 py-1.5 fw-semibold shadow-xs" style="background: #dc2626; border-color: #dc2626;">
                            <i class="fas fa-ban me-1.5"></i> Confirmer le Blacklistage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(strtolower((string) $order->status) !== 'cancelled' && (auth()->user()?->can('manage_orders') || auth()->user()?->can('edit_orders')))
    <!-- Modal Remplacement Produit & Stock -->
    <div class="modal fade" id="replaceOrderItemModal" tabindex="-1" aria-labelledby="replaceOrderItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-3.5 pb-3" style="background: #eff6ff; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1rem; border: 1px solid #bfdbfe;">
                            <i class="fas fa-right-left"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="replaceOrderItemModalLabel">Remplacer le produit</h5>
                            <div class="text-muted small">Commande <span class="font-monospace fw-semibold">#{{ $order->order_number }}</span> &bull; Mise à jour produit, stock et totaux</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Article actuel -->
                    <div class="p-3 rounded-3 mb-3 border" style="background: #f8fafc;">
                        <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.05em;">Article actuel</div>
                        <div class="d-flex align-items-center gap-3" id="replaceOldItemBanner">
                            <img id="replaceOldImg" src="{{ asset('images/camera/cat_cameras.jpg') }}" alt="" style="width: 44px; height: 44px; object-fit: contain; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; padding: 2px;">
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-bold text-dark text-truncate" id="replaceOldName">—</div>
                                <div class="small text-muted d-flex flex-wrap gap-2">
                                    <span class="font-monospace" id="replaceOldSku"></span>
                                    <span id="replaceOldStore"></span>
                                    <span id="replaceOldPriceQty" class="fw-semibold text-dark"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recherche du nouveau produit -->
                    <div class="mb-3">
                        <label for="replaceProductSearch" class="form-label fw-semibold text-dark small mb-1.5">
                            <i class="fas fa-search text-primary me-1"></i>1. Rechercher le produit de remplacement (Nom ou SKU)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="replaceProductSearch" class="form-control" placeholder="Tapez un nom de produit ou un SKU..." autocomplete="off">
                        </div>
                        <div id="replaceProductResults" class="border rounded-3 mt-2 bg-white" style="max-height: 220px; overflow-y: auto;">
                            <div class="text-center text-muted small py-3">Chargement des produits...</div>
                        </div>
                    </div>

                    <!-- Configuration du produit sélectionné -->
                    <div id="replaceSelectedConfig" class="d-none border rounded-3 p-3" style="background: #f8fafc;">
                        <div class="d-flex align-items-center justify-content-between gap-2 pb-2.5 mb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2.5 min-w-0">
                                <img id="replaceNewImg" src="{{ asset('images/camera/cat_cameras.jpg') }}" alt="" style="width: 44px; height: 44px; object-fit: contain; border-radius: 8px; background: #fff; border: 1px solid #e2e8f0; padding: 2px; flex-shrink: 0;">
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" id="replaceNewName">—</div>
                                    <div class="small text-muted font-monospace" id="replaceNewMeta">—</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 flex-shrink-0" onclick="clearReplacementSelection()">
                                <i class="fas fa-times me-1"></i>Changer
                            </button>
                        </div>

                        <div class="row g-3">
                            <!-- Variante (affichée uniquement si le produit en possède) -->
                            <div class="col-12 d-none" id="replaceVariantWrap">
                                <label for="replaceVariantSelect" class="form-label fw-semibold text-dark small mb-1">
                                    <i class="fas fa-layer-group text-primary me-1"></i>Variante <span class="text-danger">*</span>
                                </label>
                                <select id="replaceVariantSelect" class="form-select form-select-sm"></select>
                            </div>

                            <!-- Magasin / Dépôt source (Stock) -->
                            <div class="col-12 col-md-6">
                                <label for="replaceStoreSelect" class="form-label fw-semibold text-dark small mb-1">
                                    <i class="fas fa-warehouse text-primary me-1"></i>Dépôt / Magasin source (Stock)
                                </label>
                                <select id="replaceStoreSelect" class="form-select form-select-sm"></select>
                                <div id="replaceStockHint" class="small mt-1"></div>
                            </div>

                            <!-- Quantité -->
                            <div class="col-6 col-md-3">
                                <label for="replaceQuantityInput" class="form-label fw-semibold text-dark small mb-1">Quantité <span class="text-danger">*</span></label>
                                <input type="number" id="replaceQuantityInput" class="form-control form-control-sm fw-bold" min="1" max="9999" value="1">
                            </div>

                            <!-- Prix unitaire -->
                            <div class="col-6 col-md-3">
                                <label for="replacePriceInput" class="form-label fw-semibold text-dark small mb-1">Prix unitaire ({{ setting('currency_symbol', 'DH') }})</label>
                                <input type="number" id="replacePriceInput" class="form-control form-control-sm fw-bold" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Aperçu financier -->
                        <div class="mt-3 pt-2.5 border-top d-flex flex-wrap align-items-center justify-content-between gap-2 small" id="replacePricePreview">
                            <div>
                                <span class="text-muted">Sous-total ligne :</span>
                                <span id="replacePreviewLineOld" class="text-decoration-line-through text-muted ms-1">0</span>
                                <i class="fas fa-arrow-right text-muted mx-1" style="font-size: 0.7rem;"></i>
                                <strong id="replacePreviewLineNew" class="text-primary">0</strong>
                            </div>
                            <div>
                                <span class="text-muted">Nouveau total commande estimé :</span>
                                <strong id="replacePreviewOrderTotal" class="text-danger fs-6 ms-1">0</strong>
                            </div>
                        </div>

                        @if($order->stock_deducted)
                            <div class="alert alert-info border-0 rounded-3 py-2 px-3 small mt-2.5 mb-0">
                                <i class="fas fa-boxes-stacked me-1"></i>
                                Le stock déjà déduit de l'ancien produit sera automatiquement restitué, puis le nouveau produit sera prélevé depuis le dépôt choisi.
                            </div>
                        @else
                            <div class="alert alert-secondary border-0 rounded-3 py-2 px-3 small mt-2.5 mb-0">
                                <i class="fas fa-clock me-1"></i>
                                Le stock n'a pas encore été déduit sur cette commande ; il sera prélevé automatiquement lors du passage à <strong>Livrée</strong>.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border-top px-4 py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" id="confirmReplaceItemBtn" class="btn btn-sm btn-primary rounded-pill px-4 py-1.5 fw-semibold shadow-xs" disabled onclick="submitReplaceOrderItem()">
                        <i class="fas fa-check me-1.5"></i> Confirmer le remplacement
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

@push('scripts')
@php
    $replaceStoresPayload = ($stores ?? collect())->map(fn ($s) => [
        'id' => (int) $s->id,
        'name' => $s->name,
        'code' => $s->code,
        'is_default' => (bool) $s->is_default,
    ])->values();
@endphp
<script>
(function() {
function initOrderShow() {
    document.querySelectorAll('.item-store-form').forEach(form => {
        const select = form.querySelector('.item-store-select');
        let previousValue = select ? select.value : '';

        if (select) {
            select.addEventListener('change', function() {
                form.dispatchEvent(new Event('submit', { cancelable: true }));
            });
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const itemId = form.getAttribute('data-item-id');
            const submitBtn = form.querySelector('button[type="submit"]');
            const storeId = select ? select.value : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            try {
                const response = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ store_id: storeId || null })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Impossible de mettre à jour le dépôt.');
                }

                previousValue = storeId;

                // Keep desktop and mobile selects for the same item synchronized
                document.querySelectorAll(`.item-store-form[data-item-id="${itemId}"] .item-store-select`).forEach(sel => {
                    sel.value = storeId;
                });

                const metaEl = document.getElementById(`item-store-meta-${itemId}`);
                if (metaEl) {
                    if (data.item && data.item.store_name) {
                        const locBadge = data.item.location_code
                            ? `<span class="badge bg-light text-secondary border" style="font-size: 0.68rem;"><i class="fas fa-map-pin text-danger me-1"></i>${data.item.location_code}</span>`
                            : '';
                        metaEl.innerHTML = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;"><i class="fas fa-warehouse me-1"></i>${data.item.store_name}</span>${locBadge}`;
                    } else {
                        metaEl.innerHTML = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size: 0.68rem;"><i class="fas fa-exclamation-circle me-1"></i>Dépôt à attribuer</span>`;
                    }
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 2600,
                        timerProgressBar: true,
                    });
                }
            } catch (err) {
                if (select) {
                    select.value = previousValue;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stock insuffisant / Erreur',
                        text: err.message,
                        confirmButtonColor: '#dc2626',
                    });
                } else {
                    alert(err.message);
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check"></i>';
                }
            }
        });
    });

    // Live zero-refresh filter for Order History timeline
    const historyFilterBtns = document.querySelectorAll('#orderHistoryFilters .order-history-filter-pill');
    const historyItems = document.querySelectorAll('#orderHistoryTimeline .order-history-item');
    const historyVisibleCount = document.getElementById('orderHistoryVisibleCount');
    const historyEmptyCat = document.getElementById('orderHistoryEmptyCat');

    historyFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const cat = this.dataset.historyCat || 'all';
            historyFilterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            let visible = 0;
            historyItems.forEach(item => {
                const match = cat === 'all' || item.dataset.historyCat === cat;
                item.classList.toggle('d-none', !match);
                if (match) visible++;
            });

            if (historyVisibleCount) historyVisibleCount.textContent = visible;
            if (historyEmptyCat) historyEmptyCat.classList.toggle('d-none', visible > 0);
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOrderShow);
} else {
    initOrderShow();
}

window.copyShowOrderNumber = function(orderNum, btn) {
    if (!orderNum || !btn) return;
    const origHtml = btn.innerHTML;
    const ok = () => {
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => { btn.innerHTML = origHtml; }, 1400);
    };
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(String(orderNum)).then(ok).catch(() => {});
    } else {
        const tmp = document.createElement('textarea');
        tmp.value = String(orderNum);
        tmp.style.position = 'fixed';
        tmp.style.left = '-9999px';
        document.body.appendChild(tmp);
        tmp.select();
        try { document.execCommand('copy'); ok(); } catch (e) {}
        document.body.removeChild(tmp);
    }
};

window.quickUpdateShowStatus = async function(orderId, value) {
    const pill = document.getElementById(`show-status-pill-${orderId}`);
    if (pill) {
        pill.style.opacity = '0.6';
        pill.style.pointerEvents = 'none';
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            bootstrap.Dropdown.getInstance(pill)?.hide();
        }
        pill.classList.remove('show');
        pill.closest('.dropdown')?.querySelector('.dropdown-menu')?.classList.remove('show');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/orders/${orderId}/quick-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: value }),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            throw new Error(data.message || 'Impossible de modifier le statut.');
        }

        if (typeof Swal !== 'undefined') {
            await Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || 'Statut mis à jour',
                showConfirmButton: false,
                timer: 900,
            });
        }
        window.location.reload();
    } catch (err) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Erreur', text: err.message, confirmButtonColor: '#dc2626' });
        } else {
            alert(err.message);
        }
        if (pill) {
            pill.style.opacity = '1';
            pill.style.pointerEvents = '';
        }
    }
};

window.quickUpdateShowTaxMode = async function(orderId, value) {
    const pill = document.getElementById(`show-tax-pill-${orderId}`);
    if (pill) {
        pill.style.opacity = '0.6';
        pill.style.pointerEvents = 'none';
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            bootstrap.Dropdown.getInstance(pill)?.hide();
        }
        pill.classList.remove('show');
        pill.closest('.dropdown')?.querySelector('.dropdown-menu')?.classList.remove('show');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/orders/${orderId}/quick-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ tax_mode: value }),
        });
        const data = await res.json();
        if (!res.ok || !data.success || !data.order) {
            throw new Error(data.message || 'Impossible de modifier le régime.');
        }

        const tm = (data.order.tax_mode || 'ht').toLowerCase() === 'ttc' ? 'ttc' : 'ht';
        const tmLabel = tm === 'ht' ? 'HT' : 'TTC';
        const tmCheckClass = tm === 'ht' ? 'text-warning' : 'text-primary';

        if (pill) {
            pill.className = `tax-mode-pill tax-mode-${tm} dropdown-toggle`;
            const lbl = document.getElementById(`show-tax-label-${orderId}`);
            if (lbl) lbl.textContent = tmLabel;
            const menu = pill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(item => {
                    const isSel = item.dataset.taxMode === tm;
                    item.classList.toggle('active', isSel);
                    const chk = item.querySelector('.fa-check');
                    if (chk) chk.remove();
                    if (isSel) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${tmCheckClass} small"></i>`);
                    }
                });
            }
        }

        const summaryPill = document.getElementById('show-summary-tax-pill');
        if (summaryPill) {
            summaryPill.className = `tax-mode-pill tax-mode-${tm}`;
            summaryPill.textContent = tmLabel;
        }
        const totalLbl = document.getElementById('show-total-tax-label');
        if (totalLbl) totalLbl.textContent = tmLabel;
        const kpiTaxLbl = document.getElementById('show-kpi-tax-label');
        if (kpiTaxLbl) kpiTaxLbl.textContent = tmLabel;
        const mobileTaxLbl = document.getElementById('show-mobile-tax-label');
        if (mobileTaxLbl) mobileTaxLbl.textContent = tmLabel;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || `Régime mis à jour : ${tmLabel}`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        }
    } catch (err) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Erreur', text: err.message, confirmButtonColor: '#dc2626' });
        } else {
            alert(err.message);
        }
    } finally {
        if (pill) {
            pill.style.opacity = '1';
            pill.style.pointerEvents = '';
        }
    }
};

window.quickUpdateShowDeliveryType = async function(orderId, value) {
    const pill = document.getElementById(`show-delivery-pill-${orderId}`);
    if (pill) {
        pill.style.opacity = '0.6';
        pill.style.pointerEvents = 'none';
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            bootstrap.Dropdown.getInstance(pill)?.hide();
        }
        pill.classList.remove('show');
        pill.closest('.dropdown')?.querySelector('.dropdown-menu')?.classList.remove('show');
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    try {
        const res = await fetch(`/orders/${orderId}/quick-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ delivery_type: value }),
        });
        const data = await res.json();
        if (!res.ok || !data.success || !data.order) {
            throw new Error(data.message || 'Impossible de modifier le type de livraison.');
        }

        const metaMap = {
            store: { label: 'Store', icon: 'fa-store', checkClass: 'text-dark', color: '#0f172a' },
            our_team: { label: 'Our Team', icon: 'fa-people-carry-box', checkClass: 'text-primary', color: '#1d4ed8' },
            ozon: { label: 'Ozon', icon: 'fa-box-open', checkClass: 'text-warning', color: '#b45309' },
        };
        const dt = metaMap[data.order.delivery_type] ? data.order.delivery_type : 'ozon';
        const info = metaMap[dt];
        const finalLabel = data.order.delivery_type_label || info.label;

        if (pill) {
            pill.className = `delivery-type-pill delivery-type-${dt} dropdown-toggle`;
            const iconEl = document.getElementById(`show-delivery-icon-${orderId}`);
            if (iconEl) iconEl.className = `fas ${info.icon}`;
            const lbl = document.getElementById(`show-delivery-label-${orderId}`);
            if (lbl) lbl.textContent = finalLabel;
            const menu = pill.closest('.dropdown')?.querySelector('.dropdown-menu');
            if (menu) {
                menu.querySelectorAll('.dropdown-item').forEach(item => {
                    const isSel = item.dataset.deliveryType === dt;
                    item.classList.toggle('active', isSel);
                    const chk = item.querySelector('.fa-check');
                    if (chk) chk.remove();
                    if (isSel) {
                        item.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${info.checkClass} small"></i>`);
                    }
                });
            }
        }

        const tileVal = document.getElementById('show-delivery-tile-val');
        if (tileVal) tileVal.style.color = info.color;
        const tileIcon = document.getElementById('show-delivery-tile-icon');
        if (tileIcon) tileIcon.className = `fas ${info.icon}`;
        const tileLbl = document.getElementById('show-delivery-tile-label');
        if (tileLbl) tileLbl.textContent = finalLabel;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: data.message || `Type de livraison : ${finalLabel}`,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
            });
        }
    } catch (err) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Erreur', text: err.message, confirmButtonColor: '#dc2626' });
        } else {
            alert(err.message);
        }
    } finally {
        if (pill) {
            pill.style.opacity = '1';
            pill.style.pointerEvents = '';
        }
    }
};

const ORDER_REPLACE_CTX = {
    searchUrl: @json(route('orders.products.search')),
    orderTotal: @json((float) $order->total),
    stockDeducted: @json((bool) $order->stock_deducted),
    fallbackImg: @json(asset('images/camera/cat_cameras.jpg')),
    currencySymbol: @json(setting('currency_symbol', 'DH')),
    currencyPosition: @json(setting('currency_position', 'after')),
    currencyDecimals: @json((int) setting('currency_decimals', 2)),
    decimalSep: @json(setting('decimal_separator', '.')),
    thousandsSep: @json(setting('thousands_separator', ' ')),
    stores: @json($replaceStoresPayload),
};

const replaceState = {
    replaceUrl: '',
    oldStoreId: null,
    oldSubtotal: 0,
    oldQty: 1,
    products: [],
    selectedProduct: null,
    searchTimer: null,
};

function fmtOrderMoney(val) {
    const num = Number(val || 0);
    const dec = Number.isInteger(ORDER_REPLACE_CTX.currencyDecimals) ? ORDER_REPLACE_CTX.currencyDecimals : 2;
    const fixed = num.toFixed(dec);
    const parts = fixed.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ORDER_REPLACE_CTX.thousandsSep);
    const numStr = parts.length > 1 ? parts.join(ORDER_REPLACE_CTX.decimalSep) : parts[0];
    return ORDER_REPLACE_CTX.currencyPosition === 'before'
        ? ORDER_REPLACE_CTX.currencySymbol + numStr
        : numStr + ' ' + ORDER_REPLACE_CTX.currencySymbol;
}

function escapeReplaceHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, s => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[s]));
}

window.openReplaceItemModal = function(btn) {
    const modalEl = document.getElementById('replaceOrderItemModal');
    if (!modalEl) return;

    replaceState.replaceUrl = btn.getAttribute('data-replace-url') || '';
    replaceState.oldStoreId = btn.getAttribute('data-store-id') ? parseInt(btn.getAttribute('data-store-id'), 10) : null;
    replaceState.oldSubtotal = parseFloat(btn.getAttribute('data-subtotal')) || 0;
    replaceState.oldQty = Math.max(1, parseInt(btn.getAttribute('data-quantity'), 10) || 1);
    replaceState.selectedProduct = null;

    const oldName = btn.getAttribute('data-product-name') || 'Produit';
    const oldVariant = btn.getAttribute('data-variant-name') || '';
    const oldSku = btn.getAttribute('data-product-sku') || 'N/A';
    const oldStoreName = btn.getAttribute('data-store-name') || 'Non assigné';
    const oldPrice = parseFloat(btn.getAttribute('data-price')) || 0;
    const oldImg = btn.getAttribute('data-product-image') || ORDER_REPLACE_CTX.fallbackImg;

    document.getElementById('replaceOldImg').src = oldImg;
    document.getElementById('replaceOldName').textContent = oldVariant ? `${oldName} (${oldVariant})` : oldName;
    document.getElementById('replaceOldSku').textContent = `SKU: ${oldSku}`;
    document.getElementById('replaceOldStore').textContent = `Dépôt : ${oldStoreName}`;
    document.getElementById('replaceOldPriceQty').textContent = `${fmtOrderMoney(oldPrice)} × ${replaceState.oldQty} = ${fmtOrderMoney(replaceState.oldSubtotal)}`;

    const searchInput = document.getElementById('replaceProductSearch');
    if (searchInput) searchInput.value = '';
    document.getElementById('replaceSelectedConfig')?.classList.add('d-none');
    const confirmBtn = document.getElementById('confirmReplaceItemBtn');
    if (confirmBtn) confirmBtn.disabled = true;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    fetchReplacementProducts('');
};

window.clearReplacementSelection = function() {
    replaceState.selectedProduct = null;
    document.getElementById('replaceSelectedConfig')?.classList.add('d-none');
    const confirmBtn = document.getElementById('confirmReplaceItemBtn');
    if (confirmBtn) confirmBtn.disabled = true;
    document.getElementById('replaceProductSearch')?.focus();
};

async function fetchReplacementProducts(query) {
    const box = document.getElementById('replaceProductResults');
    if (!box) return;
    box.innerHTML = '<div class="text-center text-muted small py-3"><i class="fas fa-spinner fa-spin me-1"></i>Recherche...</div>';

    try {
        const res = await fetch(`${ORDER_REPLACE_CTX.searchUrl}?q=${encodeURIComponent(query || '')}`, {
            headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();
        replaceState.products = Array.isArray(data.products) ? data.products : [];
        renderReplacementResults(replaceState.products);
    } catch (e) {
        box.innerHTML = '<div class="text-center text-danger small py-3">Erreur lors du chargement des produits.</div>';
    }
}

function renderReplacementResults(products) {
    const box = document.getElementById('replaceProductResults');
    if (!box) return;

    if (!products.length) {
        box.innerHTML = '<div class="text-center text-muted small py-3">Aucun produit actif trouvé.</div>';
        return;
    }

    box.innerHTML = products.map((p, idx) => {
        const storeBadges = Array.isArray(p.store_stocks) && p.store_stocks.length
            ? p.store_stocks.map(s => `<span class="badge ${s.quantity > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border'}" style="font-size:0.65rem;">${escapeReplaceHtml(s.store_name)}: ${s.quantity}</span>`).join(' ')
            : `<span class="badge bg-light text-secondary border" style="font-size:0.65rem;">Stock global : ${p.stock}</span>`;
        const isOut = p.track_inventory && !p.force_in_stock && p.stock <= 0;
        return `
            <button type="button"
                    class="w-100 text-start border-0 border-bottom bg-white p-2.5 d-flex align-items-center justify-content-between gap-2.5 replace-product-option"
                    style="cursor:pointer; transition: background 0.12s;"
                    onmouseover="this.style.background='#f8fafc'"
                    onmouseout="this.style.background='#ffffff'"
                    onclick="selectReplacementProductByIndex(${idx})">
                <div class="d-flex align-items-center gap-2.5 min-w-0 flex-grow-1">
                    <img src="${escapeReplaceHtml(p.image || ORDER_REPLACE_CTX.fallbackImg)}" alt="" style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:#fff;border:1px solid #e2e8f0;padding:2px;flex-shrink:0;" onerror="this.onerror=null;this.src='${escapeReplaceHtml(ORDER_REPLACE_CTX.fallbackImg)}';">
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-dark text-truncate small">${escapeReplaceHtml(p.name)}${p.has_variants ? ' <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle" style="font-size:0.62rem;">Variantes</span>' : ''}</div>
                        <div class="text-muted" style="font-size:0.7rem;">SKU: <span class="font-monospace">${escapeReplaceHtml(p.sku)}</span> &bull; Stock total : <strong class="${isOut ? 'text-danger' : 'text-success'}">${p.force_in_stock ? 'En stock (forcé)' : p.stock}</strong></div>
                        <div class="mt-0.5 d-flex flex-wrap gap-1">${storeBadges}</div>
                    </div>
                </div>
                <div class="text-end flex-shrink-0">
                    <div class="fw-bold text-danger small">${fmtOrderMoney(p.price)}</div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 mt-1" style="font-size:0.65rem;">Choisir</span>
                </div>
            </button>
        `;
    }).join('');
}

window.selectReplacementProductByIndex = function(idx) {
    const product = replaceState.products[idx];
    if (!product) return;
    replaceState.selectedProduct = product;

    document.getElementById('replaceSelectedConfig')?.classList.remove('d-none');
    document.getElementById('replaceNewImg').src = product.image || ORDER_REPLACE_CTX.fallbackImg;
    document.getElementById('replaceNewName').textContent = product.name;
    document.getElementById('replaceNewMeta').textContent = `SKU: ${product.sku} • Stock total : ${product.force_in_stock ? 'Forcé en stock' : product.stock}`;

    const variantWrap = document.getElementById('replaceVariantWrap');
    const variantSelect = document.getElementById('replaceVariantSelect');
    const priceInput = document.getElementById('replacePriceInput');
    const qtyInput = document.getElementById('replaceQuantityInput');

    if (qtyInput) {
        qtyInput.value = replaceState.oldQty;
    }

    if (product.has_variants && Array.isArray(product.variants) && product.variants.length > 0 && variantWrap && variantSelect) {
        variantWrap.classList.remove('d-none');
        variantSelect.innerHTML = product.variants.map(v => {
            return `<option value="${v.id}" data-price="${v.price}" data-stock="${v.stock}">${escapeReplaceHtml(v.name)} — ${fmtOrderMoney(v.price)} (Stock: ${v.stock})</option>`;
        }).join('');
        const firstInStock = product.variants.find(v => v.stock > 0) || product.variants[0];
        if (firstInStock) {
            variantSelect.value = String(firstInStock.id);
            if (priceInput) priceInput.value = Number(firstInStock.price).toFixed(2);
        }
    } else if (variantWrap && variantSelect) {
        variantWrap.classList.add('d-none');
        variantSelect.innerHTML = '';
        if (priceInput) priceInput.value = Number(product.price).toFixed(2);
    }

    populateReplaceStoreOptions(product);
    updateReplaceLivePreview();

    const confirmBtn = document.getElementById('confirmReplaceItemBtn');
    if (confirmBtn) confirmBtn.disabled = false;

    if (window.innerWidth < 768) {
        document.getElementById('replaceSelectedConfig')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
};

function populateReplaceStoreOptions(product) {
    const storeSelect = document.getElementById('replaceStoreSelect');
    if (!storeSelect) return;

    const qty = Math.max(1, parseInt(document.getElementById('replaceQuantityInput')?.value, 10) || 1);
    const storeStockMap = new Map();
    (product.store_stocks || []).forEach(ss => {
        storeStockMap.set(Number(ss.store_id), ss);
    });
    const hasAllocations = storeStockMap.size > 0;

    let optionsHtml = '<option value="">— Non assigné (Stock global) —</option>';
    let bestStoreId = '';
    let bestQty = -1;

    ORDER_REPLACE_CTX.stores.forEach(st => {
        const ss = storeStockMap.get(Number(st.id));
        const stQty = ss ? Number(ss.quantity) : 0;
        const loc = ss && ss.location_code ? ` [${ss.location_code}]` : '';
        const label = hasAllocations ? `${st.name} (${st.code}) — Stock: ${stQty}${loc}` : `${st.name} (${st.code})`;
        optionsHtml += `<option value="${st.id}" data-stock="${stQty}">${escapeReplaceHtml(label)}</option>`;

        if (hasAllocations && stQty > bestQty) {
            bestQty = stQty;
            bestStoreId = String(st.id);
        }
    });

    storeSelect.innerHTML = optionsHtml;

    if (replaceState.oldStoreId) {
        const oldSs = storeStockMap.get(Number(replaceState.oldStoreId));
        if (!hasAllocations || (oldSs && Number(oldSs.quantity) >= qty)) {
            storeSelect.value = String(replaceState.oldStoreId);
            return;
        }
    }

    if (bestStoreId && bestQty > 0) {
        storeSelect.value = bestStoreId;
    } else {
        const defaultStore = ORDER_REPLACE_CTX.stores.find(s => s.is_default);
        if (defaultStore && !hasAllocations) {
            storeSelect.value = String(defaultStore.id);
        } else {
            storeSelect.value = bestStoreId || '';
        }
    }
}

function updateReplaceLivePreview() {
    const product = replaceState.selectedProduct;
    if (!product) return;

    const qty = Math.max(1, parseInt(document.getElementById('replaceQuantityInput')?.value, 10) || 1);
    const price = Math.max(0, parseFloat(document.getElementById('replacePriceInput')?.value) || 0);
    const newLineSubtotal = price * qty;
    const estOrderTotal = Math.max(0, ORDER_REPLACE_CTX.orderTotal - replaceState.oldSubtotal + newLineSubtotal);

    const oldEl = document.getElementById('replacePreviewLineOld');
    const newEl = document.getElementById('replacePreviewLineNew');
    const totalEl = document.getElementById('replacePreviewOrderTotal');
    if (oldEl) oldEl.textContent = fmtOrderMoney(replaceState.oldSubtotal);
    if (newEl) newEl.textContent = fmtOrderMoney(newLineSubtotal);
    if (totalEl) totalEl.textContent = fmtOrderMoney(estOrderTotal);

    const hintEl = document.getElementById('replaceStockHint');
    if (!hintEl) return;

    if (product.force_in_stock || !product.track_inventory) {
        hintEl.className = 'small mt-1 text-success fw-semibold';
        hintEl.innerHTML = '<i class="fas fa-check-circle me-1"></i>Produit disponible (sans blocage de stock).';
        return;
    }

    const variantSel = document.getElementById('replaceVariantSelect');
    const selectedVarOpt = variantSel && !document.getElementById('replaceVariantWrap')?.classList.contains('d-none')
        ? variantSel.options[variantSel.selectedIndex]
        : null;
    const varOrGlobalStock = selectedVarOpt ? parseInt(selectedVarOpt.dataset.stock || '0', 10) : Number(product.stock || 0);

    const storeSel = document.getElementById('replaceStoreSelect');
    const storeId = storeSel ? storeSel.value : '';
    const hasAllocations = Array.isArray(product.store_stocks) && product.store_stocks.length > 0;
    const storeOpt = storeSel && storeSel.selectedIndex >= 0 ? storeSel.options[storeSel.selectedIndex] : null;
    const storeStock = storeOpt && storeOpt.dataset.stock !== undefined ? parseInt(storeOpt.dataset.stock, 10) : null;

    if (varOrGlobalStock < qty) {
        hintEl.className = 'small mt-1 text-danger fw-semibold';
        hintEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>Stock global insuffisant (${varOrGlobalStock} dispo, ${qty} requis).`;
    } else if (storeId && hasAllocations && storeStock !== null && storeStock < qty) {
        hintEl.className = 'small mt-1 text-danger fw-semibold';
        hintEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i>Stock insuffisant dans ce dépôt (${storeStock} dispo, ${qty} requis).`;
    } else {
        const availDisplay = (storeId && hasAllocations && storeStock !== null) ? storeStock : varOrGlobalStock;
        hintEl.className = 'small mt-1 text-success fw-semibold';
        hintEl.innerHTML = `<i class="fas fa-check-circle me-1"></i>Disponible : ${availDisplay} unité(s).`;
    }
}

window.submitReplaceOrderItem = async function() {
    if (!replaceState.selectedProduct || !replaceState.replaceUrl) return;

    const btn = document.getElementById('confirmReplaceItemBtn');
    const variantWrap = document.getElementById('replaceVariantWrap');
    const variantSel = document.getElementById('replaceVariantSelect');
    const storeSel = document.getElementById('replaceStoreSelect');
    const qtyInput = document.getElementById('replaceQuantityInput');
    const priceInput = document.getElementById('replacePriceInput');

    const payload = {
        product_id: replaceState.selectedProduct.id,
        variant_id: (variantWrap && !variantWrap.classList.contains('d-none') && variantSel?.value)
            ? parseInt(variantSel.value, 10)
            : null,
        store_id: storeSel?.value ? parseInt(storeSel.value, 10) : null,
        quantity: Math.max(1, parseInt(qtyInput?.value, 10) || 1),
        price: priceInput?.value !== '' ? parseFloat(priceInput.value) : replaceState.selectedProduct.price,
    };

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1.5"></i>Mise à jour...';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    try {
        const res = await fetch(replaceState.replaceUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
            let errMsg = data.message || 'Impossible de remplacer cet article.';
            if (data.errors && typeof data.errors === 'object') {
                const firstErr = Object.values(data.errors).flat()[0];
                if (firstErr) errMsg = firstErr;
            }
            throw new Error(errMsg);
        }

        const modalEl = document.getElementById('replaceOrderItemModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getInstance(modalEl)?.hide();
        }

        if (typeof Swal !== 'undefined') {
            await Swal.fire({
                icon: 'success',
                title: 'Produit remplacé',
                text: data.message,
                timer: 1400,
                showConfirmButton: false,
            });
        }
        window.location.reload();
    } catch (err) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Stock insuffisant / Erreur', text: err.message, confirmButtonColor: '#dc2626' });
        } else {
            alert(err.message);
        }
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check me-1.5"></i> Confirmer le remplacement';
        }
    }
};

document.getElementById('replaceProductSearch')?.addEventListener('input', function() {
    clearTimeout(replaceState.searchTimer);
    const q = this.value.trim();
    replaceState.searchTimer = setTimeout(() => fetchReplacementProducts(q), 250);
});

document.getElementById('replaceVariantSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const priceInput = document.getElementById('replacePriceInput');
    if (opt && opt.dataset.price !== undefined && priceInput) {
        priceInput.value = Number(opt.dataset.price).toFixed(2);
    }
    updateReplaceLivePreview();
});

document.getElementById('replaceStoreSelect')?.addEventListener('change', updateReplaceLivePreview);
document.getElementById('replaceQuantityInput')?.addEventListener('input', updateReplaceLivePreview);
document.getElementById('replacePriceInput')?.addEventListener('input', updateReplaceLivePreview);
})();
</script>
@endpush
@endsection
