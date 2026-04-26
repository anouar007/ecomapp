@forelse($cart as $key => $details)
    <div class="d-flex flex-column flex-md-row gap-4 mb-4 p-4 bg-white rounded-4 shadow-sm border border-light position-relative" data-aos="fade-up">
        {{-- Product Image --}}
        <div class="rounded-4 overflow-hidden shadow-sm mx-auto mx-md-0" style="width: 120px; height: 150px; flex-shrink: 0;">
            <img src="{{ !empty($details['image']) ? Storage::url($details['image']) : asset('images/placeholder-product.jpg') }}" class="w-100 h-100 object-fit-cover">
        </div>

        {{-- Product Details --}}
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="fw-bold text-dark mb-0">{{ $details['name'] }}</h5>
                <button class="btn btn-link text-danger p-0" onclick="removeItem('{{ $key }}')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            
            <div class="d-flex gap-2 mb-3">
                @if(!empty($details['size']))
                    <span class="badge bg-light text-muted border fw-normal">{{ __('Size:') }} {{ $details['size'] }}</span>
                @endif
                <span class="badge bg-light text-muted border fw-normal">{{ __('Price:') }} {{ currency($details['price']) }}</span>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-auto">
                {{-- Quantity Selector --}}
                <div class="d-flex align-items-center border rounded-pill bg-light">
                    <button class="btn px-3 py-2" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})"><i class="fas fa-minus small"></i></button>
                    <span class="fw-bold px-3">{{ $details['quantity'] }}</span>
                    <button class="btn px-3 py-2" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})"><i class="fas fa-plus small"></i></button>
                </div>

                {{-- Subtotal --}}
                <div class="text-end">
                    <span class="text-muted small d-block">{{ __('Total') }}</span>
                    <span class="h5 fw-bold text-gold mb-0">{{ currency($details['price'] * $details['quantity']) }}</span>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-5">
        <h4 class="fw-bold text-muted">{{ __('Your cart is empty') }}</h4>
    </div>
@endforelse
