@extends('layouts.customer')

@section('dashboard_content')
<h3 class="fw-bold mb-4">Mes commandes</h3>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3">N° Commande</th>
                    <th>Date</th>
                    <th>État</th>
                    <th>Total</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                    <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="fw-bold">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold small">
                            Voir les détails
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted opacity-25 mb-3"></i>
                        <h6 class="fw-bold text-muted">Aucune commande trouvée</h6>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill mt-2">Commencer les achats</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white p-3 border-top-0">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
