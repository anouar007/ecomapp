@extends('layouts.frontend')

@section('meta_title', 'Checkout - Speed Platform')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Shipping Information</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">FULL NAME</label>
                                    <input type="text" name="customer_name" class="form-control bg-light border-0 py-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">EMAIL ADDRESS <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="email" name="customer_email" class="form-control bg-light border-0 py-2" placeholder="For order confirmation">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">PHONE NUMBER</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control bg-light border-0 py-2" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               title="Enter 9 digits (e.g. 612345678)"
                                               required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">ADDRESS</label>
                                    <input type="text" name="shipping_address" class="form-control bg-light border-0 py-2" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">CITY</label>
                                    <select name="shipping_city" class="form-select bg-light border-0 py-2" required>
                                        <option value="">Select City</option>
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
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">REGION</label>
                                    <input type="text" name="shipping_state" class="form-control bg-light border-0 py-2" placeholder="e.g. Casablanca-Settat">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">CODE POSTAL</label>
                                    <input type="text" name="shipping_zip" class="form-control bg-light border-0 py-2" required placeholder="e.g. 20000">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">COUNTRY</label>
                                    <select name="shipping_country" class="form-select bg-light border-0 py-2" required>
                                        <option value="Morocco" selected>Morocco</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Payment</h4>
                        <div class="alert alert-info border-0 rounded-3">
                            <i class="fas fa-info-circle me-2"></i> For demo purposes, this store uses <strong>Cash on Delivery</strong> (COD) or Check payments.
                        </div>
                        <div class="form-check p-3 border rounded-3 bg-white mb-2">
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white p-4 border-bottom-0">
                        <h5 class="fw-bold m-0">Order Summary</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($cart as $id => $details)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 position-relative">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-image text-muted opacity-25"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary tiny-badge">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $details['name'] }}</h6>
                            </div>
                            <div class="fw-bold">${{ number_format($details['price'] * $details['quantity'], 2) }}</div>
                        </div>
                        @endforeach
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Shipping</span>
                            <span class="text-success fw-bold">Free</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-top">
                            <span class="h5 fw-bold mb-0">Total</span>
                            <span class="h4 fw-bold text-primary mb-0">${{ number_format($total, 2) }}</span>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">
                            Place Order (${{ number_format($total, 2) }})
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Return to Cart
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
