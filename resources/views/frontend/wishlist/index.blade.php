@extends('layouts.frontend')

@section('meta_title', 'Mes Favoris — Coopérative Aït Oumdis')

@section('content')
<header class="sp-page-hero" style="background-image: linear-gradient(90deg, rgba(8, 26, 19, 0.93) 0%, rgba(8, 26, 19, 0.78) 45%, rgba(8, 26, 19, 0.35) 82%, rgba(8, 26, 19, 0.65) 100%), url('{{ asset('assets/images/wishlist-hero.jpg') }}'); background-size: cover; background-position: center right; background-repeat: no-repeat; padding: 130px 0 45px 0; color: #ffffff; position: relative;">
  <div class="container">
    <nav class="sp-breadcrumb" aria-label="Fil d'Ariane" style="margin-bottom: 14px; display: flex; align-items: center; gap: 8px; font-size: 0.84rem;">
      <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.65); text-decoration: none;">Accueil</a>
      <span class="sep" style="color: rgba(226,173,80,0.5);">/</span>
      <span class="current" style="color: #e2ad50; font-weight: 600;">Mes Favoris</span>
    </nav>
    <div style="max-width: 680px;">
      <span style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 2px; color: #e2ad50; font-weight: 700; display: block; margin-bottom: 8px;">Vos Rituels Coup de Cœur</span>
      <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(2rem, 3.2vw, 2.7rem); font-weight: 600; color: #ffffff; margin: 0 0 10px 0;">Mes Favoris</h1>
      <p style="font-size: 0.95rem; line-height: 1.6; color: rgba(255,255,255,0.85); margin: 0;">Retrouvez vos soins botaniques préférés et ajoutez-les à votre panier en un clic.</p>
    </div>
  </div>
</header>

<div class="py-5" style="background-color: #faf7f2; background-image: url('{{ asset('assets/images/botanical_pattern_bg.jpg') }}'); background-size: 850px auto; background-repeat: repeat; min-height: 70vh;">
    <div class="container">

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
            <h4 class="fw-bold text-muted">Votre liste de favoris est vide</h4>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill mt-3">Découvrir la boutique</a>
        </div>
        @endif
    </div>
</div>

<script>
function removeFromWishlist(e, productId, btn) {
    if(!confirm('Retirer de vos favoris ?')) return;
    
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
