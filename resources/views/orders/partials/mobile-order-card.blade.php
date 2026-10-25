<div class="mobile-order-card my-2" id="mobile-order-card-{{ $order->id }}">
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
            </div>
            <div class="mobile-order-date">
                <i class="far fa-clock me-1 opacity-75"></i>{{ $order->created_at->format('d/m/Y') }} à {{ $order->created_at->format('H:i') }}
            </div>
        </div>

        <div class="mobile-order-price-wrap">
            <div class="mobile-order-price">
                {{ $order->formatted_total }}
            </div>
            @php
                $payStatus = strtolower($order->payment_status);
                $isPaid = in_array($payStatus, ['paid', 'payé']);
                $payPillClass = $isPaid ? 'paid' : ($payStatus === 'failed' ? 'failed' : ($payStatus === 'refunded' ? 'refunded' : 'pending'));
                $payPillLabel = match($payStatus) {
                    'paid', 'payé' => 'Payé',
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
            <!-- Interactive Quick Payment Switcher -->
            <div class="dropdown">
                <button type="button" 
                        class="mobile-order-pay-pill {{ $payPillClass }} border-0 dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        data-bs-boundary="viewport"
                        data-bs-popper-config='{"strategy":"fixed"}'
                        aria-expanded="false"
                        id="mob-pay-pill-{{ $order->id }}"
                        title="Changer le paiement">
                    <i class="fas {{ $payPillIcon }}" id="mob-pay-icon-{{ $order->id }}"></i>
                    <span id="mob-pay-label-{{ $order->id }}">{{ $payPillLabel }}</span>
                    <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.55rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="mob-pay-pill-{{ $order->id }}">
                    <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">État du paiement</li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'pending')">
                            <span><i class="fas fa-clock text-warning me-1.5"></i> En attente</span>
                            @if($payStatus === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $isPaid ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'paid')">
                            <span><i class="fas fa-check-circle text-success me-1.5"></i> Payé</span>
                            @if($isPaid) <i class="fas fa-check text-success small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'failed' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'failed')">
                            <span><i class="fas fa-exclamation-circle text-danger me-1.5"></i> Échoué</span>
                            @if($payStatus === 'failed') <i class="fas fa-check text-danger small"></i> @endif
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $payStatus === 'refunded' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'payment_status', 'refunded')">
                            <span><i class="fas fa-undo text-secondary me-1.5"></i> Remboursé</span>
                            @if($payStatus === 'refunded') <i class="fas fa-check text-secondary small"></i> @endif
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Customer Info Row -->
    <div class="mobile-order-customer">
        <div class="mobile-order-cust-avatar">
            {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
        </div>
        <div class="min-w-0 flex-grow-1">
            <div class="mobile-order-cust-name text-truncate">
                {{ $order->customer_name ?: 'Client invité' }}
            </div>
            <div class="mobile-order-cust-sub text-truncate d-flex align-items-center gap-2">
                <span>
                    <i class="far fa-envelope me-1 opacity-75"></i>{{ $order->customer_email ?: ($order->customer_phone ?: 'Aucun contact') }}
                </span>
                @if($order->shipping_city)
                    <span class="text-muted opacity-75">
                        <i class="fas fa-map-marker-alt text-danger me-0.5"></i>{{ $order->shipping_city }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Items Preview Snippet -->
    @php
        $firstItem = $order->items->first();
        $totalItemsCount = $order->items->count();
    @endphp
    @if($firstItem)
        <div class="mobile-order-item-snippet">
            @if($firstItem->product && $firstItem->product->thumbnail)
                <img src="{{ $firstItem->product->thumbnail }}" 
                     alt="{{ $firstItem->product_name }}" 
                     class="mobile-order-item-img"
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
            @else
                <div class="mobile-order-item-placeholder">
                    <i class="fas fa-box"></i>
                </div>
            @endif
            <div class="min-w-0 flex-grow-1">
                <div class="mobile-order-item-name text-truncate">
                    {{ $firstItem->product_name }}
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
            </div>
        </div>
    @endif

    <!-- Tags Row: Items Count, Interactive Delivery Status, Payment Method -->
    <div class="mobile-order-tags">
        <span class="mobile-order-items-pill">
            <i class="fas fa-box text-muted me-1"></i>{{ $totalItemsCount }} article{{ $totalItemsCount > 1 ? 's' : '' }}
        </span>

        @php
            $stLower = strtolower($order->status);
            $statusStyle = match($stLower) {
                'delivered', 'livrée' => ['bg' => '#ecfdf5', 'color' => '#047857', 'border' => '#d1fae5', 'dot' => 'dot-green', 'text' => 'Livrée'],
                'shipped', 'expédiée' => ['bg' => '#f0fdfa', 'color' => '#0d9488', 'border' => '#ccfbf1', 'dot' => 'dot-teal', 'text' => 'Expédiée'],
                'processing', 'en cours' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#dbeafe', 'dot' => 'dot-blue', 'text' => 'En cours'],
                'cancelled', 'annulée' => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'border' => '#fecaca', 'dot' => 'dot-red', 'text' => 'Annulée'],
                default => ['bg' => '#fffbeb', 'color' => '#b45309', 'border' => '#fde68a', 'dot' => 'dot-amber', 'text' => 'En attente']
            };
        @endphp

        <!-- Delivery Status Pill (Interactive Quick Switcher) -->
        <div class="dropdown d-inline-block">
            <button type="button" 
                    class="mobile-order-badge dropdown-toggle border-0" 
                    data-bs-toggle="dropdown" 
                    data-bs-boundary="viewport"
                    data-bs-popper-config='{"strategy":"fixed"}'
                    aria-expanded="false"
                    id="mob-status-pill-{{ $order->id }}"
                    style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }}; border: 1px solid {{ $statusStyle['border'] }}; cursor: pointer;"
                    title="Changer le statut de livraison">
                <span class="status-dot {{ $statusStyle['dot'] }}" id="mob-status-dot-{{ $order->id }}"></span>
                <span id="mob-status-label-{{ $order->id }}">{{ $statusStyle['text'] }}</span>
                <i class="fas fa-chevron-down opacity-50 ms-0.5" style="font-size: 0.55rem;"></i>
            </button>
            <ul class="dropdown-menu shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="mob-status-pill-{{ $order->id }}">
                <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                <li>
                    <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $stLower === 'pending' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'pending')">
                        <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                        @if($stLower === 'pending') <i class="fas fa-check text-warning small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $stLower === 'processing' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'processing')">
                        <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                        @if($stLower === 'processing') <i class="fas fa-check text-primary small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $stLower === 'shipped' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'shipped')">
                        <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                        @if($stLower === 'shipped') <i class="fas fa-check text-info small"></i> @endif
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between {{ $stLower === 'delivered' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'delivered')">
                        <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                        @if($stLower === 'delivered') <i class="fas fa-check text-success small"></i> @endif
                    </button>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between text-danger {{ $stLower === 'cancelled' ? 'active' : '' }}" onclick="quickUpdateOrder({{ $order->id }}, 'status', 'cancelled')">
                        <span><span class="status-dot dot-red me-1.5"></span> Annulée</span>
                        @if($stLower === 'cancelled') <i class="fas fa-check text-danger small"></i> @endif
                    </button>
                </li>
            </ul>
        </div>

        @if($order->payment_method)
            <span class="mobile-order-badge" style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;">
                <i class="fas fa-wallet text-muted me-1"></i>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
            </span>
        @endif
    </div>

    <!-- Bottom Actions Toolbar -->
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
                <span class="text-muted small" style="font-size: 0.72rem;">
                    <i class="fas fa-receipt text-muted me-1"></i>#{{ substr($order->order_number, -6) }}
                </span>
            @endif
        </div>

        <div class="mobile-action-btns">
            <!-- Voir -->
            <a href="{{ route('orders.show', $order) }}" class="mobile-act-btn mobile-act-view" title="Consulter la commande">
                <i class="fas fa-eye"></i>
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
