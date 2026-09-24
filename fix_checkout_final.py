import re

filepath = '/Applications/XAMPP/xamppfiles/htdocs/ecome/resources/views/frontend/checkout/index.blade.php'

new_checkout_html = """@extends('layouts.frontend')

@section('meta_title', 'Finaliser ma commande — Coopérative Aït Oumdis')

@section('content')
<div class="container mt-5 mb-4">
    <nav class="sp-breadcrumb mb-3" aria-label="Fil d'Ariane" style="display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
      <a href="{{ route('home') }}" style="color: #6b7a72; text-decoration: none;">Accueil</a>
      <span class="sep" style="color: #c28d32;">/</span>
      <a href="{{ route('cart.index') }}" style="color: #6b7a72; text-decoration: none;">Panier</a>
      <span class="sep" style="color: #c28d32;">/</span>
      <span class="current" style="color: #0c261e; font-weight: 600;">Commande</span>
    </nav>
    <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.8rem, 3vw, 2.4rem); font-weight: 700; color: #0c261e; margin: 0;">Finaliser ma Commande</h1>
</div>

<div class="py-5" style="background-color: #faf7f2; background-image: url('{{ asset('assets/images/botanical_pattern_bg.jpg') }}'); background-size: 850px auto; background-repeat: repeat; min-height: 70vh;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7">
                <!-- Delivery Form -->
                <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff; padding: 10px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; color: #0c261e;">1. Informations de livraison</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">NOM COMPLET</label>
                                    <input type="text" name="customer_name" class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">ADRESSE E-MAIL <span style="font-weight: 400; text-transform: none;">(optionnel)</span></label>
                                    <input type="email" name="customer_email" class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="Pour le suivi">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">NUMÉRO DE TÉLÉPHONE</label>
                                    <input type="text" name="customer_phone" class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="+212 6XXXXXXXX" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">ADRESSE EXACTE</label>
                                    <input type="text" name="shipping_address" class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="N° de rue, bâtiment, appartement..." required>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">VILLE</label>
                                    <select name="shipping_city" class="form-select py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" required>
                                        <option value="" selected disabled>Choisir la ville</option>
                                        <option value="Casablanca">Casablanca</option>
                                        <option value="Rabat">Rabat</option>
                                        <option value="Marrakech">Marrakech</option>
                                        <option value="Fès">Fès</option>
                                        <option value="Tanger">Tanger</option>
                                        <option value="Agadir">Agadir</option>
                                        <option value="Meknès">Meknès</option>
                                        <option value="Oujda">Oujda</option>
                                        <option value="Kenitra">Kenitra</option>
                                        <option value="Tétouan">Tétouan</option>
                                        <option value="Salé">Salé</option>
                                        <option value="Temara">Temara</option>
                                        <option value="Safi">Safi</option>
                                        <option value="Mohammedia">Mohammedia</option>
                                        <option value="Khouribga">Khouribga</option>
                                        <option value="El Jadida">El Jadida</option>
                                        <option value="Béni Mellal">Béni Mellal</option>
                                        <option value="Aït Melloul">Aït Melloul</option>
                                        <option value="Nador">Nador</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">RÉGION</label>
                                    <input type="text" name="shipping_state" class="form-control py-3" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="ex. Casa-Settat">
                                </div>
                            </div>
                        
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card border-0 rounded-4 shadow-sm" style="background: #ffffff; padding: 10px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif; color: #0c261e;">2. Mode de paiement</h4>
                        <div class="alert border-0 rounded-3 mb-4" style="background: rgba(226, 173, 80, 0.1); color: #0c261e;">
                            <i class="fas fa-info-circle me-2" style="color: #c28d32;"></i> Votre commande sera payée <strong>uniquement à la livraison</strong>.
                        </div>
                        <label class="d-block position-relative rounded-3 p-3 border" style="background: #fbf9f4; border-color: #c28d32 !important; cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <input class="form-check-input ms-0 me-3 mt-0" type="radio" name="payment_method" id="cod" value="cod" checked style="width: 20px; height: 20px;">
                                <div>
                                    <span class="fw-bold d-block" style="color: #0c261e; font-size: 1.05rem;">Paiement à la livraison</span>
                                    <span class="small text-muted">Payez en espèces lorsque vous recevez votre colis.</span>
                                </div>
                                <i class="fas fa-wallet ms-auto" style="color: #c28d32; font-size: 1.5rem; opacity: 0.8;"></i>
                            </div>
                        </label>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm sticky-top" style="background: #ffffff; top: 100px;">
                    <div class="card-header bg-white p-4 pb-0 border-bottom-0 rounded-top-4">
                        <h4 class="fw-bold m-0" style="font-family: 'Playfair Display', serif; color: #0c261e;">Récapitulatif</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="checkout-product-list mb-4">
                            @foreach($cart as $id => $details)
                            <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(12,38,30,0.05) !important;">
                                <div class="me-3 position-relative flex-shrink-0">
                                    @if($details['image'])
                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 70px; height: 70px; border: 1px solid rgba(12,38,30,0.05);">
                                    @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center border" style="width: 70px; height: 70px; background: #fbf9f4; border-color: rgba(12,38,30,0.05);">
                                        <i class="fas fa-image text-muted opacity-25"></i>
                                    </div>
                                    @endif
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="background: #e2ad50; color: #fff; width: 22px; height: 22px; font-size: 0.75rem; border: 2px solid #fff; font-weight: 700;">{{ $details['quantity'] }}</span>
                                </div>
                                <div class="flex-grow-1 min-w-0 pe-3">
                                    <h6 class="fw-bold mb-1 text-truncate" style="color: #0c261e; font-size: 0.95rem;">{{ $details['name'] }}</h6>
                                    <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                                </div>
                                <div class="fw-bold flex-shrink-0" style="color: #c28d32; font-size: 1rem;">{{ currency($details['price'] * $details['quantity']) }}</div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="bg-light rounded-3 p-3 mb-4" style="background-color: #fbf9f4 !important; border: 1px dashed rgba(226,173,80,0.4);">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="color: #6b7a72; font-size: 0.9rem;">Sous-total</span>
                                <span class="fw-bold" style="color: #0c261e;">{{ currency($total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="color: #6b7a72; font-size: 0.9rem;">Livraison</span>
                                <span class="fw-bold" style="color: #10b981;">Gratuit</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mb-4">
                            <span class="fw-bold" style="color: #0c261e; font-size: 1.1rem;">Total à payer</span>
                            <span class="fw-bold" style="color: #0c261e; font-size: 1.6rem; line-height: 1;">{{ currency($total) }}</span>
                        </div>

                        <div class="mobile-sticky-checkout">
                            <button type="submit" form="checkout-form" class="btn w-100 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: #0c261e; color: #fff; height: 56px; font-size: 1.1rem; border: none; transition: transform 0.2s ease;">
                                <i class="fas fa-lock small opacity-75"></i> Confirmer la commande
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cart.index') }}" class="text-decoration-none small" style="color: #6b7a72; font-weight: 500;">
                        <i class="fas fa-arrow-left me-1"></i> Modifier mon panier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.body.classList.add('checkout-page');
</script>
@endpush
@endsection
"""

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_checkout_html)
print("Checkout page completely rewritten")
