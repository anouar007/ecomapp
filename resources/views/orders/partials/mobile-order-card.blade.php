@php
    $stLower = strtolower($order->status);
    $normalizedStatus = match($stLower) {
        'delivered', 'livrée' => 'delivered',
        'shipped', 'expédiée' => 'shipped',
        'processing', 'en cours' => 'processing',
        'en_demande' => 'en_demande',
        'reserved', 'réservé' => 'reserved',
        'cancelled', 'annulée' => 'cancelled',
        default => 'pending'
    };
    $statusStyle = match($normalizedStatus) {
        'delivered' => ['bg' => '#059669', 'color' => '#ffffff', 'border' => '#047857', 'dot' => 'dot-green', 'text' => 'Livrée'],
        'shipped' => ['bg' => '#0093ac', 'color' => '#ffffff', 'border' => '#0e7490', 'dot' => 'dot-teal', 'text' => 'Expédiée'],
        'processing' => ['bg' => '#2563eb', 'color' => '#ffffff', 'border' => '#1d4ed8', 'dot' => 'dot-blue', 'text' => 'En cours'],
        'en_demande' => ['bg' => '#c61aff', 'color' => '#ffffff', 'border' => '#a800e0', 'dot' => 'dot-indigo', 'text' => 'Sur Commande'],
        'reserved' => ['bg' => '#7c3aed', 'color' => '#ffffff', 'border' => '#6d28d9', 'dot' => 'dot-purple', 'text' => 'Réservé'],
        'cancelled' => ['bg' => '#dc2626', 'color' => '#ffffff', 'border' => '#b91c1c', 'dot' => 'dot-red', 'text' => 'Annulée'],
        default => ['bg' => '#d97706', 'color' => '#ffffff', 'border' => '#b45309', 'dot' => 'dot-amber', 'text' => 'En attente']
    };
@endphp

<div class="mobile-order-card mobile-order-status-{{ $normalizedStatus }} my-2" id="mobile-order-card-{{ $order->id }}">
    <!-- Top Row: Order Number, Date and Price -->
    <div class="mobile-order-header">
        <div>
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <a href="{{ route('orders.show', $order) }}" class="mobile-order-num" title="Voir la commande">
                    <i class="fas fa-receipt text-danger opacity-75"></i>
                    #{{ $order->order_number }}
                </a>
                <button type="button" 
                        class="mobile-copy-btn" 
                        onclick="copyOrderNumber('{{ $order->order_number }}', this)" 
                        title="Copier le N°">
                    <i class="far fa-copy"></i>
                </button>
                @php $forcedOosItems = $order->forcedOutOfStockItems(); @endphp
                @if($forcedOosItems->isNotEmpty())
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5"
                          style="font-size: 0.65rem;"
                          title="Produit hors stock (forcé) : {{ $forcedOosItems->pluck('display_name')->implode(', ') }}">
                        <i class="fas fa-triangle-exclamation text-warning me-1"></i>Rupture ({{ $forcedOosItems->count() }})
                    </span>
                @endif
            </div>
            <div class="mobile-order-date">
                <i class="far fa-clock me-1 opacity-75"></i>{{ $order->created_at->format('d/m/Y') }} à {{ $order->created_at->format('H:i') }}
            </div>
        </div>

        <div class="mobile-order-price-wrap tabular-nums">
            <div class="mobile-order-price" id="mob-order-total-{{ $order->id }}">
                {{ $order->formatted_total }}
            </div>
            @php $mobTaxMode = $order->normalized_tax_mode; @endphp
            <div class="dropdown d-inline-block mt-1">
                <button type="button"
                        class="tax-mode-pill tax-mode-{{ $mobTaxMode }} dropdown-toggle"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true"
                        data-bs-boundary="viewport"
                        data-bs-popper-config='{"strategy":"fixed"}'
                        aria-expanded="false"
                        id="mob-tax-pill-{{ $order->id }}"
                        title="Changer le régime TTC / HT">
                    <span id="mob-tax-label-{{ $order->id }}">{{ $order->tax_mode_label }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu text-start" aria-labelledby="mob-tax-pill-{{ $order->id }}">
                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Régime fiscal</li>
                    <li>
                        <button type="button" data-tax-mode="ttc" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobTaxMode === 'ttc' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'tax_mode', 'ttc')">
                            <span><span class="status-dot tax-dot-ttc me-1.5"></span> TTC</span>
                            @if($mobTaxMode === 'ttc') <i class="fas fa-check text-primary small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" data-tax-mode="ht" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobTaxMode === 'ht' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'tax_mode', 'ht')">
                            <span><span class="status-dot tax-dot-ht me-1.5"></span> HT</span>
                            @if($mobTaxMode === 'ht') <i class="fas fa-check text-warning small"></i> @endif
                        </button>
                    </li>
                </ul>
            </div>
            @php
                $payStatus = strtolower($order->payment_status);
                $isPaid = in_array($payStatus, ['paid', 'payé']);
                $payPillClass = $isPaid ? 'paid' : ($payStatus === 'failed' ? 'failed' : ($payStatus === 'refunded' ? 'refunded' : 'pending'));
                $payPillLabel = match($payStatus) {
                    'paid', 'payé' => 'Payé',
                    'partial', 'partiel' => 'Partiel',
                    'failed', 'échoué' => 'Échoué',
                    'refunded', 'remboursé' => 'Remboursé',
                    default => 'En attente'
                };
                $payPillIcon = match($payStatus) {
                    'paid', 'payé' => 'fa-check-circle',
                    'failed', 'échoué' => 'fa-exclamation-circle',
                    'refunded', 'remboursé' => 'fa-undo',
                    default => 'fa-clock'
                };
            @endphp
            <!-- Static Payment Status Badge (Hidden on mobile) -->
            <div class="d-inline-block d-none">
                <span class="mobile-order-pay-pill {{ $payPillClass }} border-0"
                      id="mob-pay-pill-{{ $order->id }}"
                      title="Statut de paiement : {{ $payPillLabel }}">
                    <i class="fas {{ $payPillIcon }}" id="mob-pay-icon-{{ $order->id }}"></i>
                    <span id="mob-pay-label-{{ $order->id }}">{{ $payPillLabel }}</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Customer Info Row -->
    @php
        $mobCleanPhone = '';
        if ($order->customer_phone) {
            $mobCleanPhone = preg_replace('/[^0-9]/', '', (string) $order->customer_phone);
            if (str_starts_with($mobCleanPhone, '0')) {
                $mobCleanPhone = '212' . substr($mobCleanPhone, 1);
            }
        }
    @endphp
    <div class="mobile-order-customer">
        <div class="mobile-order-cust-avatar" style="{{ $order->is_blacklisted ? 'background: #fee2e2; color: #dc2626; border-color: #fca5a5;' : '' }}">
            @if($order->is_blacklisted)
                <i class="fas fa-ban"></i>
            @else
                {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
            @endif
        </div>
        <div class="min-w-0 flex-grow-1">
            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                <span class="mobile-order-cust-name text-truncate {{ $order->is_blacklisted ? 'text-danger' : '' }}" style="{{ $order->is_blacklisted ? 'color: #dc2626 !important;' : '' }}">
                    {{ $order->customer_name ?: 'Client invité' }}
                </span>
                @if($order->is_blacklisted)
                    <span class="badge bg-danger text-white fw-bold py-0.5 px-1.5 d-none" style="font-size: 0.65rem;" title="Client sur liste noire : {{ $order->blacklist_reason ?: 'Sans motif' }}">
                        <i class="fas fa-ban me-0.5"></i>Liste Noire
                    </span>
                @endif
                @php $tier = $order->customer_tier; @endphp
                <span class="badge {{ $tier['badge_class'] }} py-0.5 px-1.5 d-none" style="font-size: 0.65rem;" title="{{ $tier['description'] }}">
                    <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                </span>
            </div>
            <div class="mobile-order-cust-sub text-truncate d-flex align-items-center gap-2">
                <span>
                    <i class="fas fa-{{ $order->customer_phone ? 'phone' : 'envelope' }} me-1 opacity-75"></i>{{ $order->customer_phone ?: ($order->customer_email ?: 'Aucun contact') }}
                </span>
                @if($order->shipping_city)
                    <span class="text-muted opacity-75">
                        <i class="fas fa-map-marker-alt text-danger me-0.5"></i>{{ $order->shipping_city }}
                    </span>
                @endif
            </div>
        </div>
        @if($order->customer_phone || $order->customer_record)
            <div class="d-flex align-items-center gap-1.5 flex-shrink-0 ms-1">
                @if($order->customer_phone)
                    <a href="tel:{{ $order->customer_phone }}" class="mobile-order-quick-btn is-call" title="Appeler {{ $order->customer_name ?: 'le client' }}">
                        <i class="fas fa-phone-alt"></i>
                    </a>
                @endif
                @if($mobCleanPhone)
                    <a href="https://wa.me/{{ $mobCleanPhone }}" target="_blank" rel="noopener" class="mobile-order-quick-btn is-whatsapp" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif
                @if($order->customer_record && !$order->customer_phone)
                    <a href="{{ route('customers.show', $order->customer_record) }}" class="mobile-order-quick-btn is-call" title="Voir fiche client">
                        <i class="fas fa-user"></i>
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Zone 3: Order Items Preview Snippet + Depot -->
    @php
        $firstItem = $order->items->first();
        $totalItemsCount = $order->items->count();
        $mobStores = $order->items->pluck('store')->filter()->unique('id');
        $mobUnassigned = $order->items->whereNull('store_id')->isNotEmpty();
    @endphp
    @if($firstItem)
        <div class="mobile-order-item-snippet">
            <div style="width: 48px; height: 48px; border-radius: 10px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 3px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ $firstItem->product_image }}" 
                     alt="{{ $firstItem->clean_product_name }}" 
                     style="width: 100%; height: 100%; object-fit: contain;"
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                     loading="lazy">
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="mobile-order-item-name-clamp">
                    {{ $firstItem->clean_product_name }}
                    @if($firstItem->variant_name)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal ms-1" style="font-size: 0.65rem;">{{ $firstItem->variant_name }}</span>
                    @endif
                </div>
                <div class="mobile-order-item-meta">
                    <span class="badge bg-light text-dark border px-1.5 py-0.5">x{{ $firstItem->quantity }}</span>
                    <span class="text-muted">{{ currency($firstItem->price) }}</span>
                    @if($totalItemsCount > 1)
                        <span class="badge bg-light text-secondary border px-1.5 py-0.5 ms-auto">
                            +{{ $totalItemsCount - 1 }} autre{{ $totalItemsCount > 2 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                @if($mobStores->isNotEmpty() || $mobUnassigned)
                    <div class="mobile-order-item-depot">
                        @foreach($mobStores as $mst)
                            <span class="badge store-badge-themed px-2 py-0.5" style="font-size: 0.66rem; {{ $mst->badge_style }}">
                                <i class="fas fa-warehouse me-1"></i>{{ $mst->name }}
                            </span>
                        @endforeach
                        @if($mobUnassigned)
                            <a href="{{ route('orders.show', $order) }}" class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-0.5 text-decoration-none" style="font-size: 0.66rem;">
                                <i class="fas fa-store-slash me-1"></i>Dépôt à affecter
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Zone 4: Interactive Controls Row (Status, Delivery Type, Payment Method) -->
    <div class="mobile-order-controls-row">
        <!-- Delivery Status Pill (Interactive Quick Switcher) -->
        <div class="dropdown d-inline-block">
            <button type="button" 
                    class="mobile-order-badge dropdown-toggle border-0" 
                    data-bs-toggle="dropdown" 
                    data-bs-auto-close="true"
                    data-bs-boundary="viewport"
                    data-bs-popper-config='{"strategy":"fixed"}'
                    aria-expanded="false"
                    id="mob-status-pill-{{ $order->id }}"
                    style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }}; border: 1px solid {{ $statusStyle['border'] }} !important; cursor: pointer;"
                    title="Changer le statut de livraison">
                <span class="status-dot {{ $statusStyle['dot'] }}" id="mob-status-dot-{{ $order->id }}"></span>
                <span id="mob-status-label-{{ $order->id }}">{{ $statusStyle['text'] }}</span>
                <i class="fas fa-chevron-down ms-0.5" style="font-size: 0.55rem;"></i>
            </button>
            <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="mob-status-pill-{{ $order->id }}">
                <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                <li>
                    <button type="button" data-status="pending" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'pending')">
                        <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                        @if($normalizedStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="en_demande" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'en_demande' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'en_demande')">
                        <span><span class="status-dot dot-blue me-1.5"></span> Sur Commande</span>
                        @if($normalizedStatus === 'en_demande') <i class="fas fa-check text-primary small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="reserved" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'reserved' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'reserved')">
                        <span><span class="status-dot dot-purple me-1.5"></span> Réservé</span>
                        @if($normalizedStatus === 'reserved') <i class="fas fa-check text-purple small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="processing" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'processing' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'processing')">
                        <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                        @if($normalizedStatus === 'processing') <i class="fas fa-check text-primary small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="shipped" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'shipped' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'shipped')">
                        <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                        @if($normalizedStatus === 'shipped') <i class="fas fa-check text-info small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="delivered" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $normalizedStatus === 'delivered' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'delivered')">
                        <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                        @if($normalizedStatus === 'delivered') <i class="fas fa-check text-success small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-status="cancelled" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $normalizedStatus === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'cancelled')">
                        <span><span class="status-dot dot-red me-1.5"></span> Annulée</span>
                        @if($normalizedStatus === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                    </button>
                </li>
            </ul>
        </div>

        @php
            $mobDeliveryType = $order->effective_delivery_type;
            $mobDeliveryIcon = match ($mobDeliveryType) {
                'our_team' => 'fa-people-carry-box',
                'ozon' => 'fa-box-open',
                'autre' => 'fa-truck-arrow-right',
                default => 'fa-store',
            };
        @endphp
        <div class="dropdown d-inline-block">
            <button type="button"
                    class="delivery-type-pill delivery-type-{{ $mobDeliveryType }} dropdown-toggle"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="true"
                    data-bs-boundary="viewport"
                    data-bs-popper-config='{"strategy":"fixed"}'
                    data-shipping-cost="{{ round((float) ($order->shipping_cost ?? 0), 2) }}"
                    data-suggested-shipping="{{ \App\Services\DeliveryService::getDeliveryCost($order->shipping_city, 40.0) }}"
                    data-shipping-city="{{ $order->shipping_city ?? '' }}"
                    aria-expanded="false"
                    id="mob-delivery-pill-{{ $order->id }}"
                    title="Changer le type de livraison">
                <i class="fas {{ $mobDeliveryIcon }}" id="mob-delivery-icon-{{ $order->id }}"></i>
                <span id="mob-delivery-label-{{ $order->id }}">{{ $order->effective_delivery_type_label }}</span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu text-start" aria-labelledby="mob-delivery-pill-{{ $order->id }}">
                <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Type de livraison</li>
                <li>
                    <button type="button" data-delivery-type="store" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobDeliveryType === 'store' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'store')">
                        <span><span class="status-dot delivery-dot-store me-1.5"></span> Store</span>
                        @if($mobDeliveryType === 'store') <i class="fas fa-check text-danger small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-delivery-type="our_team" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobDeliveryType === 'our_team' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'our_team')">
                        <span><span class="status-dot delivery-dot-our_team me-1.5"></span> Livreur</span>
                        @if($mobDeliveryType === 'our_team') <i class="fas fa-check text-primary small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-delivery-type="ozon" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobDeliveryType === 'ozon' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'ozon')">
                        <span><span class="status-dot delivery-dot-ozon me-1.5"></span> Ozon</span>
                        @if($mobDeliveryType === 'ozon') <i class="fas fa-check text-warning small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" data-delivery-type="autre" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $mobDeliveryType === 'autre' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'delivery_type', 'autre')">
                        <span><span class="status-dot delivery-dot-autre me-1.5"></span> Autre</span>
                        @if($mobDeliveryType === 'autre') <i class="fas fa-check text-purple small"></i> @endif
                    </button>
                </li>
            </ul>
        </div>

        @if($order->payment_method)
            @php
                $mobPayMethodLabel = match(strtolower((string) $order->payment_method)) {
                    'cod', 'cash_on_delivery' => 'COD',
                    'cash' => 'Espèces',
                    'card' => 'Carte',
                    'virement', 'bank_transfer' => 'Virement',
                    'cheque', 'chèque' => 'Chèque',
                    'credit' => 'Crédit',
                    'other' => 'Autre',
                    default => ucfirst(str_replace('_', ' ', (string) $order->payment_method)),
                };
            @endphp
            <span class="mobile-order-badge mobile-order-payment-pill ms-auto" style="background: rgba(255, 255, 255, 0.88); color: #475569; border: 1px solid #cbd5e1;">
                <i class="fas fa-wallet text-muted"></i><span>{{ $mobPayMethodLabel }}</span>
            </span>
        @endif
    </div>

    <!-- Zone 5a: Staff Attribution (Creator / First Confirmer) -->
    @if($order->createdByUser || !$order->isPosOrder() || ($order->confirmedByUser && (int) $order->confirmed_by !== (int) $order->created_by))
        <div class="mobile-order-staff-row {{ (!$order->createdByUser && !$order->confirmedByUser) ? 'd-none' : '' }}" id="mob-staff-row-{{ $order->id }}">
            @if($order->createdByUser)
                <span class="order-staff-chip is-creator" title="Créée par {{ $order->createdByUser->name }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Créée : {{ $order->createdByUser->name }}</span>
                </span>
            @endif
            @if(!$order->isPosOrder() || ($order->confirmedByUser && (int) $order->confirmed_by !== (int) $order->created_by))
                <span class="order-staff-chip is-confirmed {{ $order->confirmedByUser ? '' : 'd-none' }}"
                      id="mob-confirmed-by-{{ $order->id }}"
                      title="{{ $order->confirmedByUser ? 'Confirmée par ' . $order->confirmedByUser->name : '' }}">
                    <i class="fas fa-user-check"></i>
                    <span id="mob-confirmed-name-{{ $order->id }}">{{ $order->confirmedByUser ? 'Confirmée : ' . $order->confirmedByUser->name : '' }}</span>
                </span>
            @endif
        </div>
    @endif

    <!-- Zone 5b: Bottom Actions Toolbar -->
    <div class="mobile-order-toolbar">
        <div>
            @if($order->invoice)
                <a href="{{ route('invoices.show', $order->invoice) }}" class="mobile-order-doc-btn">
                    <i class="fas fa-file-invoice text-success"></i>
                    <span>Facture</span>
                </a>
            @elseif($order->quote)
                <a href="{{ route('invoices.show', $order->quote) }}" class="mobile-order-doc-btn">
                    <i class="fas fa-file-alt text-warning"></i>
                    <span>Devis</span>
                </a>
            @else
                <span class="mobile-order-items-pill">
                    <i class="fas fa-box text-muted me-1"></i>{{ $totalItemsCount }} article{{ $totalItemsCount > 1 ? 's' : '' }}
                </span>
            @endif
        </div>

        <div class="mobile-action-btns">
            <!-- Voir -->
            <a href="{{ route('orders.show', $order) }}" class="mobile-act-btn mobile-act-view" title="Consulter la commande">
                <i class="fas fa-eye"></i>
                <span>Détails</span>
            </a>

            <!-- Modifier -->
            <a href="{{ route('orders.edit', $order) }}" class="mobile-act-btn mobile-act-edit" title="Modifier la commande">
                <i class="fas fa-pencil-alt"></i>
                <span>Modifier</span>
            </a>

            <!-- Supprimer (if pending or cancelled) -->
            @if(in_array($order->status, ['pending', 'cancelled']))
                <form method="POST" 
                      action="{{ route('orders.destroy', $order->id) }}" 
                      class="m-0 p-0 d-inline"
                      data-confirm-delete="true"
                      data-item-type="order"
                      data-item-name="Commande #{{ $order->order_number }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mobile-act-btn mobile-act-delete" title="Supprimer la commande">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
