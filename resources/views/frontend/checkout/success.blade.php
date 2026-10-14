@extends('layouts.frontend')

@section('meta_title', __('Order confirmed') . ' #' . $order->order_number . ' — ' . setting('app_name', 'WINA SHOP'))

@section('content')
<div class="bg-light py-4 py-md-5 min-vh-100">
    <div class="container">
        <!-- Top Success Celebration -->
        <div class="text-center mb-4">
            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 72px; height: 72px; background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
                <i class="fas fa-check fa-2x"></i>
            </div>
            <h1 class="fw-bold mb-2 font-heading text-dark">{{ __('Order confirmed!') }}</h1>
            <p class="text-muted lead fs-6 mb-3">
                {{ __('Thank you for your trust,') }} <strong class="text-dark">{{ $order->customer_name }}</strong> ! {{ __('We have received your order and are preparing it right away.') }}
            </p>
            <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-xs border">
                <span class="text-muted small fw-semibold">{{ __('Order:') }}</span>
                <span class="fw-bold text-dark font-monospace">#{{ $order->order_number }}</span>
                <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-1" onclick="navigator.clipboard.writeText('{{ $order->order_number }}'); alert('{{ __('Order number copied!') }}');" title="{{ __('Copy') }}">
                    <i class="far fa-copy"></i>
                </button>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Left: Order Items & Delivery Details -->
            <div class="col-12 col-lg-7">
                <!-- Items Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white p-3.5 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 fs-6">
                            <i class="fas fa-boxes-stacked text-primary me-2"></i>{{ __('Articles commandés') }} ({{ $order->items->count() }})
                        </h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                            <i class="fas fa-clock me-1"></i>{{ ucfirst($order->status ?? 'En attente') }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light small text-muted text-uppercase">
                                    <tr>
                                        <th class="border-0 ps-3 py-2.5">{{ __('Produit') }}</th>
                                        <th class="border-0 text-center py-2.5" style="width: 110px;">{{ __('P.U. TTC') }}</th>
                                        <th class="border-0 text-center py-2.5" style="width: 70px;">{{ __('Qté') }}</th>
                                        <th class="border-0 text-end pe-3 py-2.5" style="width: 120px;">{{ __('Total TTC') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    @php
                                        $prodImg = $item->product && $item->product->thumbnail ? $item->product->thumbnail : asset('images/camera/cat_cameras.jpg');
                                    @endphp
                                    <tr>
                                        <td class="ps-3 py-3">
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                                    <img src="{{ $prodImg }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="fw-bold text-dark text-truncate" style="max-width: 260px;">{{ $item->product_name }}</div>
                                                    <div class="text-muted small font-monospace">SKU: {{ $item->product_sku ?: 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 text-secondary small">
                                            {{ currency($item->price) }}
                                        </td>
                                        <td class="text-center py-3">
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                                &times;{{ $item->quantity }}
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
                    </div>
                </div>

                <!-- Delivery & Contact Info -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white p-3.5 border-bottom">
                        <h5 class="fw-bold m-0 fs-6">
                            <i class="fas fa-truck text-danger me-2"></i>{{ __('Informations de livraison & Paiement') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small mb-1">{{ __('Destinataire') }}</div>
                                <div class="fw-bold text-dark">{{ $order->customer_name }}</div>
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-phone-alt me-1 text-danger"></i>
                                    <a href="tel:{{ $order->customer_phone }}" class="text-decoration-none text-dark">{{ $order->customer_phone }}</a>
                                </div>
                                @if($order->customer_email)
                                <div class="text-muted small mt-0.5">
                                    <i class="far fa-envelope me-1 text-primary"></i>
                                    <span class="text-dark">{{ $order->customer_email }}</span>
                                </div>
                                @endif
                                @if($order->ice)
                                <div class="text-muted small mt-1">
                                    <strong>ICE :</strong> <span class="badge bg-light text-dark border">{{ $order->ice }}</span>
                                </div>
                                @endif
                            </div>

                            <div class="col-sm-6">
                                <div class="text-muted small mb-1">{{ __('Adresse de livraison') }}</div>
                                <div class="fw-bold text-dark">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $order->shipping_city }}
                                </div>
                                <div class="text-secondary small mt-0.5">{{ $order->shipping_address }}</div>
                                @if($order->shipping_state)
                                <div class="text-muted small">{{ $order->shipping_state }}, Maroc</div>
                                @endif

                                <div class="mt-3 pt-2 border-top">
                                    <div class="text-muted small mb-1">{{ __('Mode de règlement') }}</div>
                                    <div class="fw-bold text-success small">
                                        <i class="fas fa-hand-holding-dollar me-1"></i>
                                        {{ __('Paiement à la livraison (Cash on Delivery)') }}
                                    </div>
                                    <div class="text-muted small mt-0.5" style="font-size: 0.75rem;">
                                        Réglez en espèces au livreur lors de la réception du colis.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Financial Summary & Next Steps -->
            <div class="col-12 col-lg-5">
                <!-- Summary Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white p-3.5 border-bottom">
                        <h5 class="fw-bold m-0 fs-6">
                            <i class="fas fa-receipt text-success me-2"></i>{{ __('Récapitulatif Financier') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2 text-secondary small">
                            <span>{{ __('Sous-total HT') }}</span>
                            <span class="fw-semibold text-dark">{{ currency($order->subtotal) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-secondary small">
                            <span>{{ setting('tax_label', 'TVA') }} ({{ floatval(setting('tax_rate', 20)) }}%)</span>
                            <span class="fw-semibold text-dark">{{ currency($order->tax) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-secondary small">
                            <span>{{ __('Frais de livraison') }}</span>
                            <span class="fw-semibold {{ $order->shipping_cost > 0 ? 'text-dark' : 'text-success' }}">
                                {{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : __('Gratuit') }}
                            </span>
                        </div>
                        <div class="pt-3 border-top d-flex justify-content-between align-items-baseline mb-4">
                            <div>
                                <span class="h5 fw-bold text-dark mb-0 d-block">{{ __('Total TTC') }}</span>
                                <span class="text-muted small" style="font-size: 0.75rem;"><i class="fas fa-shield-alt text-success me-1"></i>Montant net à payer</span>
                            </div>
                            <span class="h3 fw-bold text-danger mb-0 font-heading">{{ $order->formatted_total }}</span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-dark py-2.5 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2" onclick="window.print()">
                                <i class="fas fa-print"></i>
                                <span>{{ __('Imprimer le reçu') }}</span>
                            </button>
                            <a href="{{ route('shop.index') }}" class="btn btn-primary py-2.5 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="fas fa-shopping-bag"></i>
                                <span>{{ __('Continuer vos achats') }}</span>
                            </a>
                            @if(auth()->check())
                            <a href="{{ route('customer.orders') }}" class="btn btn-link text-muted small py-1 text-decoration-none text-center">
                                <i class="fas fa-history me-1"></i>{{ __('Consulter l\'historique de mes commandes') }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Next Steps Info Card -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-circle-info text-primary me-2"></i>{{ __('Prochaines étapes') }}
                    </h6>
                    <div class="d-flex flex-column gap-3 small text-secondary">
                        <div class="d-flex gap-2.5">
                            <div class="badge rounded-circle bg-primary-subtle text-primary fw-bold" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
                            <div>
                                <strong class="text-dark d-block">Confirmation de commande</strong>
                                Notre service client peut vous contacter par téléphone pour valider l'adresse de livraison.
                            </div>
                        </div>
                        <div class="d-flex gap-2.5">
                            <div class="badge rounded-circle bg-primary-subtle text-primary fw-bold" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
                            <div>
                                <strong class="text-dark d-block">Expédition rapide</strong>
                                Votre colis est emballé avec soin et confié à notre transporteur express.
                            </div>
                        </div>
                        <div class="d-flex gap-2.5">
                            <div class="badge rounded-circle bg-primary-subtle text-primary fw-bold" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
                            <div>
                                <strong class="text-dark d-block">Paiement à la livraison</strong>
                                Vous réglez directement le livreur à la réception de votre colis.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
