@extends('layouts.customer')

@section('dashboard_content')
<h3 class="fw-bold mb-4">{{ __('Dashboard') }}</h3>

@if((isset($affiliateCoupons) && $affiliateCoupons->count() > 0) || ($user->affiliate_points ?? 0) > 0)
<div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-4 border-success overflow-hidden">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill mb-1">
                    <i class="fas fa-gem me-1"></i> {{ __('Programme Ambassadeur & Affiliation') }}
                </span>
                <h5 class="fw-bold mb-0 text-dark">{{ __('Mes Récompenses & Liens Partagés') }}</h5>
            </div>
            <div class="text-end">
                <span class="text-muted small d-block">{{ __('Mon Solde de Points') }}</span>
                <span class="fs-3 fw-bold text-success">{{ number_format($user->affiliate_points ?? 0) }} <small class="fs-6 fw-normal">pts</small></span>
            </div>
        </div>

        @if(isset($affiliateCoupons) && $affiliateCoupons->count() > 0)
            <div class="row g-3">
                @foreach($affiliateCoupons as $affCoupon)
                <div class="col-12">
                    <div class="p-3 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <div>
                                <span class="fw-bold fs-5 font-monospace text-primary me-2">{{ $affCoupon->code }}</span>
                                <span class="badge bg-primary text-uppercase">{{ $affCoupon->formatted_value }} {{ __('offerts au client') }}</span>
                            </div>
                            <div class="small text-muted">
                                <i class="fas fa-users me-1"></i> {{ $affCoupon->usage_count }} {{ __('utilisations') }}
                                <span class="mx-1">•</span>
                                <strong class="text-success">+{{ number_format($affCoupon->total_affiliate_points_awarded) }} pts</strong> {{ __('gagnés') }}
                            </div>
                        </div>

                        <p class="text-muted small mb-2">
                            {{ __('Partagez ce code ou votre lien direct. À chaque achat client avec ce code, vous gagnez') }}
                            <strong>
                                @if($affCoupon->affiliate_points_type === 'percentage')
                                    {{ $affCoupon->affiliate_points_value }}% {{ __('du montant en points') }}
                                @else
                                    {{ number_format($affCoupon->affiliate_points_value) }} {{ __('points') }}
                                @endif
                            </strong> !
                        </p>

                        <div class="input-group">
                            <span class="input-group-text bg-white small text-muted"><i class="fas fa-link"></i></span>
                            <input type="text" class="form-control form-control-sm font-monospace bg-white" id="affiliate_link_{{ $affCoupon->id }}" value="{{ url('/shop?promo=' . $affCoupon->code) }}" readonly>
                            <button class="btn btn-dark btn-sm px-3" type="button" onclick="copyAffiliateLink('affiliate_link_{{ $affCoupon->id }}', this)">
                                <i class="fas fa-copy me-1"></i> {{ __('Copier le lien') }}
                            </button>
                            <a href="https://wa.me/?text={{ urlencode('Profitez d\'une réduction avec mon code promo ' . $affCoupon->code . ' sur : ' . url('/shop?promo=' . $affCoupon->code)) }}" target="_blank" class="btn btn-success btn-sm px-3">
                                <i class="fab fa-whatsapp me-1"></i> {{ __('Partager') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
function copyAffiliateLink(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-1"></i> {{ __("Copié !") }}';
        setTimeout(() => { btn.innerHTML = originalText; }, 2000);
    });
}
</script>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-primary text-white">
            <div class="card-body p-4">
                <h6 class="opacity-75 mb-3">{{ __('TOTAL') }} {{ __('My Orders') }}</h6>
                <h2 class="fw-bold display-4 mb-0">{{ $user->orders()->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <h6 class="text-muted fw-bold mb-3">{{ __('My Account') }}</h6>
                <p class="mb-1 fw-bold">{{ $user->name }}</p>
                <p class="text-muted mb-0">{{ $user->email }}</p>
                <a href="{{ route('customer.profile') }}" class="btn btn-link px-0 text-decoration-none small fw-bold">{{ __('Profile Settings') }} &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white p-3 p-md-4 border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 fs-6 fs-md-5">{{ __('Recently added') }}</h5>
            <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">{{ __('See all') }}</a>
        </div>
    </div>

    {{-- Desktop Table (d-none d-md-block) --}}
    <div class="table-responsive d-none d-md-block">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">{{ __('Order Number') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th class="pe-4 text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td class="ps-4 fw-bold">#{{ $order->order_number }}</td>
                    <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="badge {{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span></td>
                    <td class="fw-bold">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm rounded-circle">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-50 mb-2">{{ __('No orders found') }}</div>
                        <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill">{{ __('Start Shopping') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Touch Cards (d-block d-md-none, zero horizontal scroll) --}}
    <div class="d-block d-md-none p-2.5">
        @forelse($recentOrders as $order)
        <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-dark font-monospace" style="font-size: 0.95rem;">
                    #{{ $order->order_number }}
                </span>
                <span class="badge {{ $order->status_badge_class }} px-2.5 py-1" style="font-size: 0.72rem;">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="d-flex align-items-center justify-content-between text-muted small mb-2.5">
                <span><i class="far fa-calendar-alt me-1 text-muted"></i>{{ $order->created_at->format('d M Y') }}</span>
                <span class="fw-bold text-danger fs-6">{{ $order->formatted_total }}</span>
            </div>
            <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-light btn-sm w-100 rounded-pill py-2 fw-semibold text-dark d-flex align-items-center justify-content-center gap-1.5 border">
                <span>{{ __('View details') }}</span>
                <i class="fas fa-chevron-right small text-muted"></i>
            </a>
        </div>
        @empty
        <div class="text-center py-4">
            <div class="opacity-50 mb-2">{{ __('No orders found') }}</div>
            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm rounded-pill">{{ __('Start Shopping') }}</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
