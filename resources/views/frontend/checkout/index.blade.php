@extends('layouts.frontend')

@section('meta_title', 'Finaliser ma commande — Coopérative Aït Oumdis')


@section('content')
<style>
    /* Premium Form Styling */
    .premium-input:focus {
        background-color: #ffffff !important;
        border-color: #e2ad50 !important;
        box-shadow: 0 0 0 4px rgba(226, 173, 80, 0.15) !important;
        outline: none;
    }
    .premium-form-card {
        background: #ffffff;
        border: 1px solid rgba(12, 38, 30, 0.08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(12, 38, 30, 0.03);
    }
    
    /* Premium Product Cards for Checkout */
    .checkout-product-card {
        background: #ffffff;
        border: 1px solid rgba(12, 38, 30, 0.06);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .checkout-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
</style>

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
        <div class="row">
            <div class="col-lg-7">
                <div class="premium-form-card mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Informations de livraison</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">NOM COMPLET</label>
                                    <input type="text" name="customer_name" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">ADRESSE E-MAIL <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="email" name="customer_email" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="Pour la confirmation de commande">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">NUMÉRO DE TÉLÉPHONE</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               title="Enter 9 digits (e.g. 612345678)"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">ICE <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="text" name="ice" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="Identifiant Commun de l'Entreprise">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">ADRESSE</label>
                                    <input type="text" name="shipping_address" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">VILLE</label>
                                    <select name="shipping_city" class="form-select py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" required>
                                        <option value="">Choisir la ville</option>
                                        <option value="Casablanca">Casablanca</option>
                                        <option value="Rabat">Rabat</option>
                                        <option value="Fès">Fès</option>
                                        <option value="Marrakech">Marrakech</option>
                                        <option value="Tanger">Tanger</option>
                                        <option value="Salé">Salé</option>
                                        <option value="Meknès">Meknès</option>
                                        <option value="Oujda">Oujda</option>
                                        <option value="Kénitra">Kénitra</option>
                                        <option value="Agadir">Agadir</option>
                                        <option value="Tétouan">Tétouan</option>
                                        <option value="Temara">Temara</option>
                                        <option value="Safi">Safi</option>
                                        <option value="Mohammedia">Mohammedia</option>
                                        <option value="Khouribga">Khouribga</option>
                                        <option value="El Jadida">El Jadida</option>
                                        <option value="Béni Mellal">Béni Mellal</option>
                                        <option value="Aït Melloul">Aït Melloul</option>
                                        <option value="Nador">Nador</option>
                                        <option value="Dar Bouazza">Dar Bouazza</option>
                                        <option value="Taza">Taza</option>
                                        <option value="Settat">Settat</option>
                                        <option value="Berrechid">Berrechid</option>
                                        <option value="Khémisset">Khémisset</option>
                                        <option value="Inezgane">Inezgane</option>
                                        <option value="Larache">Larache</option>
                                        <option value="Guelmim">Guelmim</option>
                                        <option value="Ksar El Kebir">Ksar El Kebir</option>
                                        <option value="Al Hoceïma">Al Hoceïma</option>
                                        <option value="Ouarzazate">Ouarzazate</option>
                                        <option value="Essaouira">Essaouira</option>
                                        <option value="Bouskoura">Bouskoura</option>
                                        <option value="Fquih Ben Salah">Fquih Ben Salah</option>
                                        <option value="Dcheira El Jihadia">Dcheira El Jihadia</option>
                                        <option value="Oued Zem">Oued Zem</option>
                                        <option value="Sidi Slimane">Sidi Slimane</option>
                                        <option value="Errachidia">Errachidia</option>
                                        <option value="Guercif">Guercif</option>
                                        <option value="Oulad Teïma">Oulad Teïma</option>
                                        <option value="Ben Guerir">Ben Guerir</option>
                                        <option value="Taroudant">Taroudant</option>
                                        <option value="Fnideq">Fnideq</option>
                                        <option value="Sefrou">Sefrou</option>
                                        <option value="Youssoufia">Youssoufia</option>
                                        <option value="Martil">Martil</option>
                                        <option value="Tiznit">Tiznit</option>
                                        <option value="Tan-Tan">Tan-Tan</option>
                                        <option value="Laâyoune">Laâyoune</option>
                                        <option value="Dakhla">Dakhla</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7a72;">RÉGION</label>
                                    <input type="text" name="shipping_state" class="form-control py-3 premium-input" style="background-color: #fbf9f4; border: 1px solid rgba(12,38,30,0.1); border-radius: 8px; color: #0c261e; font-weight: 500;" placeholder="ex. Casablanca-Settat">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="premium-form-card" style="background: #ffffff; border: 1px solid rgba(12,38,30,0.05) !important;">
                    <div class="card-header bg-white p-4 border-bottom-0 rounded-top-4">
                        <h5 class="fw-bold m-0" style="font-family: 'Playfair Display', serif; color: #0c261e; font-size: 1.3rem;">Récapitulatif</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($cart as $id => $details)
                        <div class="checkout-product-card d-flex align-items-center">
                            <div class="me-3 position-relative flex-shrink-0">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 70px; height: 70px;">
                                @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: #fbf9f4;">
                                    <i class="fas fa-image text-muted opacity-25"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="background: #e2ad50; color: #fff; width: 22px; height: 22px; font-size: 0.75rem; border: 2px solid #fff; font-weight: 700;">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0 pr-2">
                                <h6 class="fw-bold mb-1 text-truncate" style="color: #0c261e; font-size: 0.95rem; font-family: 'Playfair Display', serif;">{{ $details['name'] }}</h6>
                                <p class="text-muted small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ $details['category_name'] ?? 'Soin Naturel' }}</p>
                            </div>
                            <div class="fw-bold flex-shrink-0" style="color: #0c261e; font-size: 1.05rem;">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Sous-total</span>
                            <span class="fw-bold">{{ currency($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Livraison</span>
                            <span class="text-success fw-bold">Gratuit</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-top">
                            <span class="h5 fw-bold mb-0">Total</span>
                            <span class="h4 fw-bold text-primary mb-0">{{ currency($total) }}</span>
                        </div>

                        <div class="mobile-sticky-checkout"><button type="submit" form="checkout-form" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">
                            Commander ({{ currency($total) }})
                        </button></div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Retour au panier
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

