@forelse(session('cart', []) as $key => $details)
    <div class="mc-item" id="cart-item-{{ $key }}">
        <img src="{{ !empty($details['image']) && strval($details['image']) !== '0' ? Storage::url($details['image']) : asset('images/placeholder-product.jpg') }}"
             alt="{{ $details['name'] }}" class="mc-item-img">
        <div class="mc-item-info">
            <div class="mc-item-name">{{ $details['name'] }}</div>
            <div class="mc-tags d-flex align-items-center gap-2">
                @if(!empty($details['image']))
                    <img src="{{ Storage::url($details['image']) }}" alt="Style" 
                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 1.5px solid #c5a059;">
                @endif
                @if(!empty($details['size']))
                    <span class="mc-tag">{{ $details['size'] }}</span>
                @endif
            </div>
            <div class="mc-item-bottom">
                <span class="mc-price">{{ currency($details['price']) }}</span>
                <div class="mc-qty">
                    <button class="mc-qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input class="mc-qty-val" value="{{ $details['quantity'] }}" readonly>
                    <button class="mc-qty-btn" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <button class="mc-delete" onclick="removeItem('{{ $key }}')" title="حذف">
            <i class="fas fa-times"></i>
        </button>
    </div>
@empty
    <div class="mc-empty">
        <div class="mc-empty-icon">🛍️</div>
        <h5>سلتك فارغة</h5>
        <p>لم تقومي بإضافة أي منتج بعد.</p>
        <a href="{{ route('shop.index') }}" class="mc-shop-btn" data-bs-dismiss="offcanvas">ابدئي التسوق</a>
    </div>
@endforelse

