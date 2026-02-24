@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold m-0">Order #{{ $order->order_number }}</h3>
    <a href="{{ route('customer.orders') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        &larr; Retour aux commandes
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-bottom">
                <h5 class="fw-bold m-0">Articles commandés</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-cube text-muted opacity-50"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $item->product_name }}</h6>
                                        <small class="text-muted">Qté : {{ $item->quantity }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold">
                                {{ currency($item->subtotal) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td class="text-end pe-4 py-3 border-0">Sous-total</td>
                            <td class="text-end pe-4 py-3 border-0 fw-bold">{{ currency($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-2 border-0">Livraison</td>
                            <td class="text-end pe-4 py-2 border-0">{{ currency($order->shipping_cost) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-dark">Total</td>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-primary">{{ $order->formatted_total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-3">ADRESSE DE LIVRAISON</h6>
                <p class="mb-0 fw-bold">{{ $order->shipping_address }}</p>
                <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                <p class="mb-0 text-muted">{{ $order->shipping_country }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-3">INFO COMMANDE</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>État</span>
                    <span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Paiement</span>
                    <span class="badge {{ $order->payment_status_badge_class }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Date</span>
                    <span class="fw-bold">{{ $order->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
