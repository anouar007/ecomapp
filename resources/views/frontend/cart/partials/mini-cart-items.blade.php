@php $total = 0; @endphp
@forelse(session('cart', []) as $id => $details)
    @php $total += $details['price'] * $details['quantity']; @endphp
    <div class="cart-item bg-white p-3 rounded-4 shadow-sm mb-3 position-relative border border-light" id="cart-item-{{ $id }}">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3 position-relative">
                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 80px; height: 80px;">
                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-light text-dark border shadow-sm" style="font-size: 0.7rem;">x{{ $details['quantity'] }}</span>
            </div>
            <div class="flex-grow-1 min-w-0">
                <h6 class="fw-bold mb-1 text-truncate pe-4" title="{{ $details['name'] }}">{{ $details['name'] }}</h6>
                <p class="mb-2 text-muted small">{{ $details['category_name'] ?? 'Produit' }}</p>
                
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="text-primary fw-bold" style="font-size: 1.1rem;">{{ currency($details['price']) }}</span>
                    
                    <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-1 border">
                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] - 1 }})">
                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                        </button>
                        <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly style="width: 30px;">
                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty({{ $id }}, {{ $details['quantity'] + 1 }})">
                            <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-sm text-danger position-absolute top-0 end-0 mt-2 me-2 opacity-50 hover-opacity-100 transition-all" onclick="removeItem({{ $id }})" title="Supprimer">
            <i class="fas fa-times"></i>
        </button>
    </div>
@empty
    <div class="text-center py-5 mt-5">
        <div class="mb-4 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
            <i class="fas fa-shopping-basket fa-3x text-muted opacity-25"></i>
        </div>
        <h5 class="fw-bold text-dark">Votre panier est vide</h5>
        <p class="text-muted small mb-4">Vous n'avez encore rien ajouté à votre panier.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 shadow-sm">Commencer les achats</a>
    </div>
@endforelse
