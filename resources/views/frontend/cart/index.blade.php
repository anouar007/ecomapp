@extends('layouts.frontend')

@section('meta_title', 'Mon Panier — Coopérative Aït Oumdis')

@section('content')
<style>
@media (max-width: 768px) {
    .aod-cart-header { padding: 24px 0 20px !important; }
    .aod-cart-section { padding: 24px 0 90px !important; }
    .aod-cart-item-card { padding: 14px !important; border-radius: 14px !important; }
    .aod-cart-item-img { width: 64px !important; height: 64px !important; }
    .aod-cart-summary { position: static !important; padding: 20px 16px !important; }
}
</style>
{{-- Clean Moroccan Zellige Cart Header --}}
<header class="aod-zellige-soft aod-cart-header" style="padding: 40px 0 32px; border-bottom: 1px solid #e3ede6;">
    <div class="aod-container" style="position: relative; z-index: 1;">
        <nav aria-label="Fil d'Ariane" style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px; font-size: 0.84rem;">
            <a href="{{ route('home') }}" style="color: #5f6f65; text-decoration: none; font-weight: 600;">Accueil</a>
            <span style="color: #2f8825;">/</span>
            <span style="color: #2f8825; font-weight: 700;">Mon Panier</span>
        </nav>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
            <div>
                <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.85rem, 3vw, 2.35rem); font-weight: 700; color: #2b302c; margin: 0 0 6px 0;">
                    Mon Panier
                </h1>
                <p style="font-size: 0.94rem; color: #5f6f65; margin: 0;">
                    Vérifiez vos produits du terroir avant de finaliser votre commande.
                </p>
            </div>

            @if(session('cart') && count(session('cart')) > 0)
            <div style="background: #edf6ed; color: #1e4d34; border: 1px solid #e3ede6; padding: 8px 16px; border-radius: 9999px; font-size: 0.84rem; font-weight: 700;">
                {{ count(session('cart')) }} article{{ count(session('cart')) > 1 ? 's' : '' }} sélectionné{{ count(session('cart')) > 1 ? 's' : '' }}
            </div>
            @endif
        </div>
    </div>
</header>

<section class="aod-cart-section" style="background-color: #ffffff; padding: 48px 0 90px; min-height: 60vh;">
    <div class="aod-container">
        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex flex-column gap-3">
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $details)
                    @php $total += $details['price'] * $details['quantity']; @endphp
                    <div class="rounded-4 aod-cart-item-card" style="background: #ffffff; border: 1px solid #e3ede6; padding: 18px 20px; box-shadow: 0 2px 10px rgba(30, 77, 52, 0.03);" id="cart-row-{{ $id }}">
                        <div class="row align-items-center g-3">
                            <!-- Image & Name -->
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    @if(!empty($details['image']))
                                        <img src="{{ product_image_url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover aod-cart-item-img" style="width: 82px; height: 82px; border: 1px solid #e3ede6; background: #f6f4ee;">
                                    @else
                                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 82px; height: 82px; background: #f8fbf9; border: 1px solid #e3ede6; color: #2f8825;">
                                            <i class="fas fa-leaf fa-lg"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <p class="mb-1 text-uppercase" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.06em; color: #2f8825;">
                                        {{ $details['category_name'] ?? 'Produit du Terroir' }}
                                    </p>
                                    <h6 class="fw-bold mb-1" style="font-size: 1rem; line-height: 1.35;">
                                        <a href="{{ route('shop.show', $details['product_id'] ?? $id) }}" class="text-decoration-none" style="color: #1e4d34;">{{ $details['name'] }}</a>
                                    </h6>
                                    <div class="d-md-none mt-1">
                                        <span class="fw-bold" style="color: #2f8825; font-size: 1.05rem;">{{ currency($details['price']) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Price (Desktop) -->
                            <div class="col-md-2 d-none d-md-block text-center">
                                <span class="fw-bold" style="color: #1e4d34; font-size: 1.05rem;">{{ currency($details['price']) }}</span>
                            </div>

                            <!-- Quantity -->
                            <div class="col-6 col-md-3 d-flex justify-content-start justify-content-md-center align-items-center">
                                <div class="rounded-pill d-flex align-items-center px-1 py-1" style="background: #f8fbf9; border: 1px solid #e3ede6; width: 112px;">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty('{{ $id }}', {{ $details['quantity'] - 1 }})" style="color: #1e4d34 !important;" aria-label="Diminuer">
                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="color: #1e4d34; font-size: 0.92rem;">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 w-100 h-100 d-flex align-items-center justify-content-center" onclick="updateQty('{{ $id }}', {{ $details['quantity'] + 1 }})" style="color: #1e4d34 !important;" aria-label="Augmenter">
                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remove -->
                            <div class="col-6 col-md-1 d-flex justify-content-end align-items-center">
                                <button type="button" class="btn btn-sm p-2 rounded-circle" onclick="removeItem('{{ $id }}')" title="Supprimer" style="background: rgba(220,38,38,0.08); color: #dc2626; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border: none;">
                                    <i class="fas fa-times" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="rounded-4 sticky-top aod-cart-summary" style="top: 96px; z-index: 1; background: #f8fbf9; border: 1px solid #e3ede6; padding: 26px;">
                    <h5 class="fw-bold mb-4" style="font-family: 'Playfair Display', Georgia, serif; color: #1e4d34; font-size: 1.25rem;">
                        Récapitulatif
                    </h5>
                    <div class="d-flex justify-content-between mb-3" style="font-size: 0.92rem; color: #5f6f65;">
                        <span>Sous-total</span>
                        <span class="fw-bold" style="color: #1e4d34;">{{ currency($total) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3" style="font-size: 0.92rem; color: #5f6f65;">
                        <span>Livraison</span>
                        <span class="fw-semibold" style="color: #2f8825;">Calculée selon la ville</span>
                    </div>
                    <hr style="border-color: #dcebe0; opacity: 1; margin: 18px 0;">
                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <span class="fw-bold" style="font-size: 1.05rem; color: #2b302c;">Total estimé</span>
                        <span class="fw-bold" style="font-size: 1.4rem; color: #1e4d34;">{{ currency($total) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="aod-btn-green w-100 mb-3" style="padding: 13px 24px;">
                        <span>Passer la commande</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>

                    <a href="{{ route('shop.index') }}" class="aod-btn-outline w-100" style="padding: 11px 20px; font-size: 0.86rem;">
                        <i class="fas fa-arrow-left"></i>
                        <span>Continuer mes achats</span>
                    </a>

                    <div class="d-flex align-items-center justify-content-center gap-2 mt-3" style="font-size: 0.78rem; color: #5f6f65; font-weight: 600;">
                        <i class="fas fa-hand-holding-usd" style="color: #2f8825;"></i>
                        <span>Paiement en espèces à la livraison</span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5" style="background: #f8fbf9; border: 1.5px dashed #e3ede6; border-radius: 22px; padding: 64px 24px;">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 90px; height: 90px; background: #edf6ed; color: #2f8825;">
                <i class="fas fa-shopping-basket fa-2x"></i>
            </div>
            <h3 class="fw-bold mb-2" style="font-family: 'Playfair Display', Georgia, serif; color: #1e4d34;">Votre panier est vide</h3>
            <p class="mb-4" style="color: #5f6f65; max-width: 420px; margin: 0 auto;">Découvrez nos miels 100% purs, notre Amlou artisanal et nos produits du terroir marocain.</p>
            <a href="{{ route('shop.index') }}" class="aod-btn-green">
                <span>Découvrir nos produits</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>
</section>
@endsection
