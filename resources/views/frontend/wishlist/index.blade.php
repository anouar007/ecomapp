@extends('layouts.frontend')

@section('meta_title', __('My Wishlist') . ' - ' . setting('app_name', 'Speed Platform'))

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">{{ __('My Wishlist') }}</h2>

        @if($wishlistItems->count() > 0)
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 home-products-grid">
            @foreach($wishlistItems as $item)
            @php
                $product = $item->product;
                $isInCart = isset(session('cart', [])[$product->id]);
                $isAr = app()->getLocale() === 'ar';
            @endphp
            <div class="col" id="wishlist-item-{{ $product->id }}">
                <div class="position-relative h-100">
                    <button class="btn btn-sm btn-light shadow-sm position-absolute top-0 end-0 m-2 wishlist-btn text-danger z-3" 
                            style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                            onclick="removeFromWishlist(event, {{ $product->id }}, this)"
                            data-product-id="{{ $product->id }}"
                            title="{{ __('Remove from wishlist') }}">
                        <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                    </button>
                    @include('frontend.partials.product_card_slider', ['product' => $product])
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-5">
            {{ $wishlistItems->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="far fa-heart fa-3x text-muted opacity-25 mb-3"></i>
            <h4 class="fw-bold text-muted">{{ __('Your wishlist is empty') }}</h4>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill mt-3">{{ __('Start Shopping') }}</a>
        </div>
        @endif
    </div>
</div>

<script>
function removeFromWishlist(e, productId, btn) {
    if(!confirm('{{ __('Remove from wishlist?') }}')) return;
    
    fetch("{{ route('wishlist.toggle') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'removed') {
            btn.closest('.col-md-3').remove();
            if(document.querySelectorAll('.col-md-3').length === 0) {
                location.reload();
            }
        }
    })
    .catch(console.error);
}
</script>
@endsection
