@php $total = 0; @endphp
@forelse(session('cart', []) as $key => $details)
    @php $total += $details['price'] * $details['quantity']; @endphp
    <div class="cart-item bg-white p-3 rounded-4 border border-light mb-3 position-relative transition-all hover-shadow-sm" id="cart-item-{{ $key }}">
        <div class="d-flex align-items-center gap-3">
            {{-- Product Image --}}
            <div class="flex-shrink-0 position-relative">
                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" 
                     class="rounded-3 object-fit-cover shadow-xs" 
                     style="width: 90px; height: 100px;">
            </div>

            {{-- Product Info --}}
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-black text-dark mb-0 text-truncate pe-3" title="{{ $details['name'] }}" style="font-size: 1rem; letter-spacing: -0.3px;">
                        {{ $details['name'] }}
                    </h6>
                    <button class="btn btn-sm btn-link text-muted p-0 border-0 opacity-40 hover-opacity-100 transition-all" 
                            onclick="removeItem('{{ $key }}')" title="{{ __('Remove') }}">
                        <i class="fas fa-times" style="font-size: 0.9rem;"></i>
                    </button>
                </div>
                
                <div class="small text-muted mb-3 opacity-75 fw-medium">
                    @if(($details['color'] ?? null)) {{ $details['color'] }} @endif
                    @if(($details['color'] ?? null) && ($details['size'] ?? null)) | @endif
                    @if(($details['size'] ?? null)) {{ $details['size'] }} @endif
                </div>
                
                {{-- Pricing & Qty Alignment Fix --}}
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column">
                        <span class="fw-black text-dark" style="font-size: 1.1rem; color: var(--accent) !important;">{{ currency($details['price']) }}</span>
                    </div>
                    
                    <div class="mc-qty-selector bg-light rounded-pill border d-flex align-items-center px-2 py-1">
                        <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="text" class="qty-input mx-1" value="{{ $details['quantity'] }}" readonly style="width: 20px;">
                        <button class="qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5 mt-4">
        <div class="mb-4 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
            <i class="fas fa-shopping-basket fa-3x text-muted opacity-25"></i>
        </div>
        <h5 class="fw-black text-uppercase ls-1">{{ __('Your bag is empty') }}</h5>
        <p class="text-muted small mb-4 opacity-75">{{ __('It seems you haven\'t added any masterpieces yet.') }}</p>
        <a href="{{ route('shop.index') }}" class="btn btn-dark rounded-pill px-5 shadow-sm text-uppercase fw-bold ls-1 small">
            {{ __('Discover Collections') }}
        </a>
    </div>
@endforelse
