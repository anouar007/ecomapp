@extends('layouts.frontend')

@section('meta_title', 'سلة التسوق الملكية — ' . setting('app_name', 'Hijab Princesses'))

@section('content')
<div class="bg-surface section-py min-vh-100">
    <div class="container px-xl-5">
        
        <div class="d-flex flex-column mb-5" data-aos="fade-up">
            <h1 class="brand-heading h2 mb-2">سلة التسوق الخاصة بكِ</h1>
            <div class="bg-gold rounded" style="width: 60px; height: 4px;"></div>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4 g-lg-5">
            {{-- LEFT: ITEMS LIST --}}
            <div class="col-lg-8" data-aos="fade-up">
                <div class="brand-card p-0 overflow-hidden border-0 shadow-sm bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 font-body">
                            <thead class="bg-gold-light border-bottom border-gold-subtle">
                                <tr>
                                    <th scope="col" class="py-3 px-4 text-dark small fw-bold text-uppercase">المنتج</th>
                                    <th scope="col" class="py-3 px-4 text-center text-dark small fw-bold text-uppercase d-none d-md-table-cell">السعر</th>
                                    <th scope="col" class="py-3 px-4 text-center text-dark small fw-bold text-uppercase" style="width: 140px;">الكمية</th>
                                    <th scope="col" class="py-3 px-4 text-end text-dark small fw-bold text-uppercase">المجموع</th>
                                    <th scope="col" class="py-3 px-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @foreach(session('cart') as $key => $details)
                                @php $total += $details['price'] * $details['quantity']; @endphp
                                <tr class="border-bottom border-light" id="cart-row-{{ $key }}">
                                    <td class="py-4 px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-3">
                                                @php $pImg = !empty($details['image']) && strval($details['image']) !== '0' ? Storage::url($details['image']) : asset('images/placeholder-product.jpg'); @endphp
                                                <img src="{{ $pImg }}" alt="{{ $details['name'] }}" class="rounded shadow-sm object-fit-cover" style="width: 70px; height: 90px;">
                                            </div>
                                            <div>
                                                @php $pId = $details['product_id'] ?? (is_numeric($key) ? $key : explode('_', $key)[0]); @endphp
                                                <h6 class="fw-bold mb-1"><a href="{{ route('shop.show', $pId) }}" class="text-decoration-none text-dark hover-gold transition-300">{{ $details['name'] }}</a></h6>
                                                <div class="d-flex flex-wrap gap-2 mt-1">
                                                    @if(($details['color'] ?? null))
                                                        <span class="small text-muted py-0 px-2 bg-light border rounded">اللون: {{ $details['color'] }}</span>
                                                    @endif
                                                    @if(($details['size'] ?? null))
                                                        <span class="small text-muted py-0 px-2 bg-light border rounded">المقاس: {{ $details['size'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center py-4 px-4 d-none d-md-table-cell fw-medium">{{ currency($details['price']) }}</td>
                                    <td class="text-center py-4 px-4">
                                        <div class="d-flex align-items-center border rounded-pill bg-white px-2 py-1 mx-auto" style="width: 100px;">
                                            <button class="btn btn-sm btn-link text-muted p-0 flex-grow-1" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                                                <i class="fas fa-minus x-small"></i>
                                            </button>
                                            <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" style="width: 30px;" value="{{ $details['quantity'] }}" readonly>
                                            <button class="btn btn-sm btn-link text-muted p-0 flex-grow-1" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                                                <i class="fas fa-plus x-small"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-end py-4 px-4 fw-bold text-gold fs-5">{{ currency($details['price'] * $details['quantity']) }}</td>
                                    <td class="text-end py-4 px-4">
                                        <button class="btn btn-sm btn-light text-danger rounded-circle border-0 hvr-shrink" onclick="removeItem('{{ $key }}')" title="حذف">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('shop.index') }}" class="btn-brand-outline px-4 text-decoration-none small font-body">
                         مواصلة التسوق <i class="fas fa-arrow-left ms-2 small"></i>
                    </a>
                </div>
            </div>

            {{-- RIGHT: SUMMARY --}}
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="brand-card p-4 border-0 shadow-sm bg-white sticky-top" style="top: 100px; z-index: 10;">
                    <h5 class="brand-heading h4 mb-4">ملخص الطلب</h5>
                    
                    <div class="d-flex justify-content-between mb-3 font-body">
                        <span class="text-muted">المجموع الفرعي</span>
                        <span class="fw-bold">{{ currency($total) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3 font-body">
                        <span class="text-muted">التوصيل</span>
                        <div class="text-end">
                            <span class="text-gold fw-bold">مجاني</span>
                            <div class="small text-muted" style="font-size: 0.7rem;">لكل أرجاء المغرب 🇲🇦</div>
                        </div>
                    </div>

                    <div class="bg-gold-light my-4" style="height: 1px;"></div>
                    
                    <div class="d-flex justify-content-between mb-5 align-items-center font-body">
                        <span class="h5 fw-bold mb-0">المجموع النهائي</span>
                        <span class="h3 fw-bold text-gold mb-0">{{ currency($total) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-brand-primary w-100 py-3 mb-3 text-center text-decoration-none hvr-grow">
                        تابعي لإتمام الطلب <i class="fas fa-crown ms-2"></i>
                    </a>
                    
                    <div class="text-center">
                        <span class="small text-muted font-body"><i class="fas fa-shield-alt me-1"></i> دفع آمن عند الاستلام</span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <div class="mb-5 bg-gold-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                <i class="fas fa-shopping-bag fa-4x text-gold opacity-50"></i>
            </div>
            <h2 class="brand-heading mb-3">سلتكِ لا تزال بانتظارك</h2>
            <p class="text-muted mb-5 font-body">أضيفي لمسة من الأناقة لمشترياتك اليوم.</p>
            <a href="{{ route('shop.index') }}" class="btn-brand-primary px-5 py-3 text-decoration-none hvr-grow">اكتشفي التشكيلة الآن</a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateQty(id, qty) {
    if (qty < 1) return;
    fetch('{{ route('cart.update') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({ toast:true, position:'top-start', icon:'success', title:'تم تحديث الكمية', showConfirmButton:false, timer:1500 });
            setTimeout(() => location.reload(), 1500);
        }
    });
}

function removeItem(id) {
    Swal.fire({
        title: 'هل تريدين الحذف؟',
        text: "سوف يتم إزالة هذا المنتج من سلتكِ الملكية.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5a059',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'نعم، احذفيه',
        cancelButtonText: 'إبقاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('cart.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) location.reload();
            });
        }
    });
}
</script>
@endpush
