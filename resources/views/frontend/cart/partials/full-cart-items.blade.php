@php $total = 0; @endphp
@forelse($cart as $key => $details)
    @php $total += $details['price'] * $details['quantity']; @endphp
    <div class="brand-card p-3 p-md-4 mb-4 border-0 shadow-sm bg-white position-relative overflow-hidden" id="cart-item-{{ $key }}" data-aos="fade-up">
        <div class="row align-items-center g-3 g-md-4">
            {{-- Image --}}
            <div class="col-4 col-md-2">
                @php $pImg = !empty($details['image']) && strval($details['image']) !== '0' ? Storage::url($details['image']) : asset('images/placeholder-product.jpg'); @endphp
                <img src="{{ $pImg }}" alt="{{ $details['name'] }}" class="img-fluid rounded shadow-sm object-fit-cover w-100" style="aspect-ratio: 3/4;">
            </div>

            {{-- Info --}}
            <div class="col-8 col-md-4">
                @php $pId = $details['product_id'] ?? (is_numeric($key) ? $key : explode('_', $key)[0]); @endphp
                <h5 class="fw-bold mb-1">
                    <a href="{{ route('shop.show', $pId) }}" class="text-decoration-none text-dark hover-gold transition-300">{{ $details['name'] }}</a>
                </h5>
                <div class="d-flex align-items-center flex-wrap gap-2 mt-2">
                    @if(!empty($details['image']))
                        <div class="rounded-circle border border-gold-light overflow-hidden shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <img src="{{ Storage::url($details['image']) }}" alt="Style" class="w-100 h-100 object-fit-cover">
                        </div>
                    @endif
                    @if(!empty($details['size']))
                        <span class="small text-muted py-1 px-2 bg-light border rounded-pill">المقاس: {{ $details['size'] }}</span>
                    @endif
                </div>
                <div class="d-md-none mt-2 d-flex align-items-center gap-2">
                    <span class="fw-bold text-gold">{{ currency($details['price']) }}</span>
                    @if(isset($details['original_price']) && $details['original_price'] > $details['price'])
                        <span class="text-danger small text-decoration-line-through">{{ currency($details['original_price']) }}</span>
                    @endif
                </div>
            </div>

            {{-- Price (Desktop) --}}
            <div class="col-md-2 d-none d-md-block text-center fw-medium text-muted">
                <div class="d-flex flex-column align-items-center">
                    <span>{{ currency($details['price']) }}</span>
                    @if(isset($details['original_price']) && $details['original_price'] > $details['price'])
                        <span class="text-danger small text-decoration-line-through" style="font-size: 0.8rem;">{{ currency($details['original_price']) }}</span>
                    @endif
                </div>
            </div>

            {{-- Quantity --}}
            <div class="col-7 col-md-2">
                <div class="d-flex align-items-center border rounded-pill bg-white px-2 py-1 mx-auto" style="max-width: 110px;">
                    <button class="btn btn-sm btn-link text-muted p-0 flex-grow-1" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                        <i class="fas fa-minus small"></i>
                    </button>
                    <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" style="width: 48px;" value="{{ $details['quantity'] }}" readonly>
                    <button class="btn btn-sm btn-link text-muted p-0 flex-grow-1" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                        <i class="fas fa-plus small"></i>
                    </button>
                </div>
            </div>

            {{-- Subtotal --}}
            <div class="col-4 col-md-2 text-end text-md-center">
                <div class="small text-muted d-md-none mb-1">المجموع</div>
                <div class="fw-bold text-gold fs-5">{{ currency($details['price'] * $details['quantity']) }}</div>
            </div>

            {{-- Delete Button (Dedicated Column) --}}
            <div class="col-1 col-md-1 text-end">
                <button class="btn btn-link text-danger p-0 opacity-50 hover-opacity-100 transition-300" onclick="removeItem('{{ $key }}')" title="حذف">
                    <i class="fas fa-trash-alt fs-6"></i>
                </button>
            </div>
        </div>
    </div>
@empty

    <div class="text-center py-5" data-aos="fade-up">
        <div class="mb-4 bg-gold-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
            <i class="fas fa-shopping-bag fa-3x text-gold opacity-30"></i>
        </div>
        <h3 class="brand-heading mb-3">سلتكِ لا تزال بانتظارك</h3>
        <p class="text-muted mb-4 font-body">أضيفي لمسة من الأناقة لمشترياتك اليوم.</p>
        <a href="{{ route('shop.index') }}" class="btn-brand-primary px-5 py-3 text-decoration-none">اكتشفي التشكيلة الآن</a>
    </div>
@endforelse
