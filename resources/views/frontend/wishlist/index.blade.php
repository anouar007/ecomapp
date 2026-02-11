@extends('layouts.frontend')

@section('meta_title', 'My Wishlist - Speed Platform')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h2 class="fw-bold mb-4">My Wishlist</h2>

        @if($wishlistItems->count() > 0)
        <div class="row g-4">
            @foreach($wishlistItems as $item)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 product-card overflow-hidden">
                    <div class="position-relative">
                        <a href="{{ route('shop.show', $item->product->id) }}">
                            @if($item->product->main_image)
                            <img src="{{ Storage::url($item->product->main_image) }}" class="card-img-top" alt="{{ $item->product->name }}" style="height: 250px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                            </div>
                            @endif
                        </a>
                        <button class="btn btn-light shadow-sm rounded-circle position-absolute top-0 end-0 m-3 wishlist-btn text-danger" 
                                onclick="removeFromWishlist(event, {{ $item->product->id }}, this)"
                                data-product-id="{{ $item->product->id }}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-2">
                            <a href="{{ route('shop.show', $item->product->id) }}" class="text-decoration-none text-dark">{{ $item->product->name }}</a>
                        </h5>
                        <p class="text-primary fw-bold mb-0">{{ $item->product->formatted_price }}</p>
                    </div>
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
            <h4 class="fw-bold text-muted">Your wishlist is empty</h4>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill mt-3">Start Shopping</a>
        </div>
        @endif
    </div>
</div>

<script>
function removeFromWishlist(e, productId, btn) {
    if(!confirm('Remove from wishlist?')) return;
    
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
