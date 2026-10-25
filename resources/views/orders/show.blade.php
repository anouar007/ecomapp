@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number)

@section('content')
    <!-- En-tête de la page (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                    <a href="{{ route('orders.index') }}" class="text-decoration-none text-muted">Commandes</a>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-dark fw-semibold font-monospace">#{{ $order->order_number }}</span>
                    <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                    <span class="text-muted">Détails</span>
                </div>
                <h1 class="brand-title">Commande <span class="font-monospace">#{{ $order->order_number }}</span></h1>
                <p class="brand-subtitle">
                    Passée le <strong>{{ $order->created_at->format('d/m/Y') }}</strong> à {{ $order->created_at->format('H:i') }} &bull; 
                    Client : <strong>{{ $order->customer_name ?: 'Client invité' }}</strong>
                </p>
            </div>
        </div>

        <!-- Boutons d'action responsive -->
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <!-- Retour -->
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary d-inline-flex align-items-center gap-1.5 shadow-2xs" title="Retour aux commandes">
                <i class="fas fa-arrow-left"></i> <span>Retour</span>
            </a>

            <!-- Facture -->
            @if(!$order->invoice)
                <form action="{{ route('orders.generate-invoice', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs">
                        <i class="fas fa-file-invoice"></i> <span>Créer Facture</span>
                    </button>
                </form>
            @else
                <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white">
                    <i class="fas fa-file-invoice"></i> <span>Facture #{{ $order->invoice->invoice_number }}</span>
                </a>
                <a href="{{ route('invoices.delivery-note', $order->invoice) }}" class="btn btn-sm btn-info rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm text-white" style="background: #0284c7; border-color: #0284c7;" title="Consulter le Bon de Livraison conforme Maroc">
                    <i class="fas fa-truck-loading"></i> <span>BL #{{ $order->invoice->delivery_note_number }}</span>
                </a>
            @endif

            <!-- Devis -->
            @if(!$order->quote)
                <form action="{{ route('orders.generate-quote', $order) }}" method="POST" class="d-inline-flex m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-2xs" style="color: #b45309; border-color: #fde68a; background: #fffdf5;">
                        <i class="fas fa-file-alt"></i> <span>Créer Devis</span>
                    </button>
                </form>
            @else
                <a href="{{ route('invoices.show', $order->quote) }}" class="btn btn-sm btn-warning rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" style="color: #78350f;">
                    <i class="fas fa-file-alt"></i> <span>Devis #{{ $order->quote->invoice_number }}</span>
                </a>
            @endif

            <!-- Modifier -->
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-danger rounded-pill px-3.5 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5 text-white" style="background: #dc2626; border-color: #dc2626;">
                <i class="fas fa-pencil-alt"></i> <span>Modifier</span>
            </a>
        </div>
    </div>

    <!-- Stepper de progression de la commande -->
    @php
        $steps = ['pending', 'processing', 'shipped', 'delivered'];
        $currentStatusIndex = array_search(strtolower($order->status), $steps);
        if (strtolower($order->status) === 'cancelled') $currentStatusIndex = -1;
    @endphp

    @if(strtolower($order->status) === 'cancelled')
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center gap-3 p-3">
            <i class="fas fa-times-circle fs-3 text-danger"></i>
            <div>
                <h6 class="fw-bold mb-0">Commande Annulée</h6>
                <div class="small opacity-75">Cette commande a été annulée le {{ $order->updated_at->format('d/m/Y à H:i') }}. Aucun envoi n'est prévu.</div>
            </div>
        </div>
    @else
        <div class="order-stepper-wrap mb-4">
            <div class="order-stepper">
                @php
                    $stepPercent = match($currentStatusIndex) {
                        0 => 0,
                        1 => 33,
                        2 => 66,
                        3 => 100,
                        default => 0
                    };
                @endphp
                <div class="order-stepper-track">
                    <div class="order-stepper-progress" style="width: {{ $stepPercent }}%;"></div>
                </div>

                @php
                    $stepLabels = [
                        'pending' => ['name' => 'En attente', 'icon' => 'fa-clock'],
                        'processing' => ['name' => 'En préparation', 'icon' => 'fa-cog'],
                        'shipped' => ['name' => 'Expédiée', 'icon' => 'fa-truck-fast'],
                        'delivered' => ['name' => 'Livrée', 'icon' => 'fa-circle-check'],
                    ];
                @endphp

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

    <div class="row g-4">
        <!-- Colonne Principale (Gauche) : Articles et Notes -->
        <div class="col-12 col-lg-8">
            <!-- Articles commandés -->
            <div class="product-form-card mb-4">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-box-open text-danger"></i> Articles commandés
                        <span class="badge rounded-pill bg-light text-secondary border px-2.5 ms-1" style="font-size: 0.72rem;">
                            {{ $order->items->count() }} article{{ $order->items->count() > 1 ? 's' : '' }}
                        </span>
                    </h2>
                </div>

                <!-- Desktop Table View -->
                <div class="table-responsive d-none d-md-block">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 ps-3">Produit</th>
                                <th class="border-0 text-center">P.U. TTC</th>
                                <th class="border-0 text-center">Quantité</th>
                                <th class="border-0 text-end pe-3">Total TTC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div style="width: 48px; height: 48px; border-radius: 10px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                <img src="{{ $item->product ? $item->product->thumbnail : asset('images/camera/cat_cameras.jpg') }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     style="width: 100%; height: 100%; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                            </div>
                                            <div>
                                                @if($item->product)
                                                    <a href="{{ route('products.edit', $item->product) }}" class="fw-bold text-dark text-decoration-none d-block hover-danger">
                                                        {{ $item->product_name }}
                                                    </a>
                                                @else
                                                    <span class="fw-bold text-dark d-block">{{ $item->product_name }}</span>
                                                @endif
                                                <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                                    <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product->sku ?? 'N/A') }}</span>
                                                    @if($item->variant_name)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.72rem;">
                                                            <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-3 fw-semibold text-secondary">
                                        {{ currency($item->price) }}
                                    </td>
                                    <td class="text-center py-3">
                                        <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-bold" style="border-radius: 8px;">
                                            x{{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 py-3 fw-bold text-dark font-heading">
                                        {{ currency($item->price * $item->quantity) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Items Cards (Touch-friendly) -->
                <div class="d-block d-md-none">
                    @foreach($order->items as $item)
                        <div class="p-3 border rounded-3 mb-2 bg-light bg-opacity-25">
                            <div class="d-flex align-items-start gap-2.5">
                                <div style="width: 50px; height: 50px; border-radius: 10px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                    <img src="{{ $item->product ? $item->product->thumbnail : asset('images/camera/cat_cameras.jpg') }}" 
                                         alt="{{ $item->product_name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;"
                                         onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    @if($item->product)
                                        <a href="{{ route('products.edit', $item->product) }}" class="fw-bold text-dark text-decoration-none text-truncate d-block" style="font-size: 0.9rem;">
                                            {{ $item->product_name }}
                                        </a>
                                    @else
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">
                                            {{ $item->product_name }}
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                                        <span class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: ($item->product->sku ?? 'N/A') }}</span>
                                        @if($item->variant_name)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-normal" style="font-size: 0.7rem;">
                                                <i class="fas fa-layer-group me-1"></i>{{ $item->variant_name }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <span class="badge bg-white text-dark border px-2 py-1">
                                            {{ currency($item->price) }} &times; {{ $item->quantity }}
                                        </span>
                                        <span class="fw-bold text-dark font-heading">
                                            {{ currency($item->price * $item->quantity) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes de la commande -->
            @if($order->notes)
                <div class="product-form-card">
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
        </div>

        <!-- Colonne Latérale (Droite) : Récapitulatif, Client & Paiement -->
        <div class="col-12 col-lg-4">
            <!-- Récapitulatif Financier -->
            <div class="product-form-card mb-4">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-calculator text-success"></i> Récapitulatif Financier
                    </h2>
                </div>

                <div class="d-flex flex-column gap-2.5">
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

                    <div class="pt-2.5 mt-1 border-top d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-6">Total TTC</span>
                        <span class="fw-extrabold font-heading text-danger fs-4">{{ currency($order->total) }}</span>
                    </div>

                    <!-- Statut du Paiement -->
                    @php
                        $payLower = strtolower($order->payment_status);
                        $payBadgeClass = match($payLower) {
                            'paid', 'payé' => 'bg-success-subtle text-success border-success-subtle',
                            'failed', 'échoué' => 'bg-danger-subtle text-danger border-danger-subtle',
                            'refunded', 'remboursé' => 'bg-secondary-subtle text-secondary',
                            default => 'bg-warning-subtle text-warning border-warning-subtle'
                        };
                        $payText = match($payLower) {
                            'paid' => 'Règlement Confirmé (Payé)',
                            'failed' => 'Paiement Échoué',
                            'refunded' => 'Commande Remboursée',
                            default => 'En attente de paiement'
                        };
                    @endphp
                    <div class="mt-2 text-center p-2.5 rounded-pill border {{ $payBadgeClass }} fw-semibold small">
                        <i class="fas fa-credit-card me-1.5"></i> {{ $payText }}
                    </div>
                </div>
            </div>

            <!-- Fiche Client & Livraison -->
            <div class="product-form-card mb-4">
                <div class="product-form-card-header">
                    <h2 class="product-form-card-title">
                        <i class="fas fa-user-circle text-primary"></i> Client & Livraison
                    </h2>
                </div>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #dc2626; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #fca5a5; box-shadow: 0 2px 6px rgba(220, 38, 38, 0.12);">
                        {{ strtoupper(substr($order->customer_name ?: 'C', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-dark text-truncate">{{ $order->customer_name ?: 'Client invité' }}</div>
                        <div class="text-muted small">Client de la boutique</div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 text-secondary small">
                    @if($order->customer_email)
                        <div class="d-flex align-items-center gap-2">
                            <i class="far fa-envelope text-muted" style="width: 16px;"></i>
                            <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none text-dark hover-danger text-truncate">{{ $order->customer_email }}</a>
                        </div>
                    @endif

                    @if($order->customer_phone)
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-phone-alt text-muted" style="width: 16px;"></i>
                            <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-dark hover-danger">{{ $order->customer_phone }}</a>
                        </div>
                    @endif

                    @if($order->shipping_address)
                        <div class="d-flex align-items-start gap-2 pt-2 border-top mt-1">
                            <i class="fas fa-map-marker-alt text-danger mt-1" style="width: 16px;"></i>
                            <div>
                                <span class="fw-semibold text-dark">Adresse de livraison :</span><br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }} {{ $order->shipping_zip ? '(' . $order->shipping_zip . ')' : '' }}<br>
                                <span class="text-muted">{{ $order->shipping_country ?: 'Maroc' }}</span>
                            </div>
                        </div>
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
                        <span class="fw-bold text-dark text-capitalize">{{ str_replace('_', ' ', $order->payment_method ?: 'Non spécifié') }}</span>
                    </div>

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
                </div>
            </div>
        </div>
    </div>
@endsection
