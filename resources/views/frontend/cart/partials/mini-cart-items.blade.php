@forelse(session('cart', []) as $key => $details)
    <div class="d-flex gap-3 mb-4 p-3 bg-white rounded-3 shadow-sm border border-light position-relative">
        <div class="rounded-3 overflow-hidden" style="width: 70px; height: 90px; flex-shrink: 0;">
            <img src="{{ !empty($details['image']) ? Storage::url($details['image']) : asset('images/placeholder-product.jpg') }}" class="w-100 h-100 object-fit-cover">
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold text-dark small mb-1 pe-4">{{ $details['name'] }}</h6>
            <div class="d-flex gap-2 mb-2">
                @if(!empty($details['size']))
                    <span class="badge bg-light text-muted border fw-normal">{{ $details['size'] }}</span>
                @endif
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-gold fw-bold">{{ currency($details['price']) }}</span>
                
                <div class="d-flex align-items-center border rounded-pill bg-light">
                    <button class="btn btn-sm px-2" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})"><i class="fas fa-minus x-small"></i></button>
                    <span class="small fw-bold px-2">{{ $details['quantity'] }}</span>
                    <button class="btn btn-sm px-2" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})"><i class="fas fa-plus x-small"></i></button>
                </div>
            </div>
        </div>
        <button class="btn btn-link text-danger p-0 position-absolute top-0 end-0 m-2" onclick="removeItem('{{ $key }}')">
            <i class="fas fa-times small"></i>
        </button>
    </div>
@empty
    <div class="text-center py-5">
        <div class="fs-1 mb-3">🛒</div>
        <h6 class="fw-bold text-dark">{{ __('Your cart is empty') }}</h6>
        <p class="small text-muted mb-4">{{ __('Browse our products and add something special!') }}</p>
        <a href="{{ route('shop.index') }}" class="btn btn-brand-primary rounded-pill px-4" data-bs-dismiss="offcanvas">{{ __('Start Shopping') }}</a>
    </div>
@endforelse
