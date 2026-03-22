@extends('layouts.frontend')

@section('meta_title', 'سلة التسوق — ' . setting('app_name', 'Hijab Princesses'))

@section('content')
<div class="bg-surface section-py min-vh-100">
    <div class="container">
        <h1 class="fw-black mb-5 h2 border-start-primary ps-3">سلة التسوق</h1>

        @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="bg-light border-bottom">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 text-muted small fw-bold">المنتج</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-center fw-bold">الثمن</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-center fw-bold" style="width: 150px;">الكمية</th>
                                        <th scope="col" class="py-3 px-4 text-muted small text-end fw-bold">المجموع</th>
                                        <th scope="col" class="py-3 px-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach(session('cart') as $key => $details)
                                    @php $total += $details['price'] * $details['quantity']; @endphp
                                    <tr class="border-bottom" id="cart-row-{{ $key }}">
                                        <td class="py-4 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    @if($details['image'])
                                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm object-fit-cover" style="width: 80px; height: 100px;">
                                                    @else
                                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted" style="width: 80px; height: 100px;">
                                                        <i class="fas fa-image fa-2x"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    @php $pId = $details['product_id'] ?? (is_numeric($key) ? $key : explode('_', $key)[0]); @endphp
                                                    <h6 class="fw-bold mb-1 ml-3"><a href="{{ route('shop.show', $pId) }}" class="text-decoration-none text-dark">{{ $details['name'] }}</a></h6>
                                                    <div class="d-flex gap-2 mt-2 ml-3">
                                                        @if(($details['color'] ?? null))
                                                            <span class="badge bg-light text-dark border fw-normal">اللون: {{ $details['color'] }}</span>
                                                        @endif
                                                        @if(($details['size'] ?? null))
                                                            <span class="badge bg-light text-dark border fw-normal">المقاس: {{ $details['size'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center py-4 px-4 fw-bold text-nowrap">{{ currency($details['price']) }}</td>
                                        <td class="text-center py-4 px-4">
                                            <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-2 py-1 border mx-auto" style="width: 110px;">
                                                <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100" onclick="updateQty('{{ $key }}', {{ $details['quantity'] - 1 }})">
                                                    <i class="fas fa-minus small"></i>
                                                </button>
                                                <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="{{ $details['quantity'] }}" readonly>
                                                <button class="btn btn-sm btn-link text-dark text-decoration-none p-0 w-100" onclick="updateQty('{{ $key }}', {{ $details['quantity'] + 1 }})">
                                                    <i class="fas fa-plus small"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-end py-4 px-4 fw-bold text-primary h5 mb-0 text-nowrap">{{ currency($details['price'] * $details['quantity']) }}</td>
                                        <td class="text-end py-4 px-4">
                                            <button class="btn btn-link text-danger p-2 opacity-50 rounded-circle" onclick="removeItem('{{ $key }}')" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('shop.index') }}" class="btn btn-link text-muted text-decoration-none mt-3">
                    <i class="fas fa-arrow-right me-2"></i> العودة للمتجر ومواصلة التسوق
                </a>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px; z-index: 1;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">ملخص الطلب</h5>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">المجموع الفرعي</span>
                            <span class="fw-bold h5 mb-0">{{ currency($total) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">التوصيل</span>
                            <span class="text-success fw-bold">مجاني</span>
                        </div>
                        <hr class="my-4 opacity-10">
                        <div class="d-flex justify-content-between mb-5 align-items-center">
                            <span class="h4 fw-black mb-0">الإجمالي</span>
                            <span class="h3 fw-black text-primary mb-0">{{ currency($total) }}</span>
                        </div>
                        <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3 shadow" onclick="location.href='{{ route('checkout.index') }}'">
                            إتمام الطلب الآن <i class="fas fa-chevron-left ms-2"></i>
                        </button>
                        <p class="small text-muted text-center mb-0">الدفع عند الاستلام متاح لجميع المدن</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5 mt-4">
            <div class="mb-5 bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 150px; height: 150px;">
                <i class="fas fa-shopping-basket fa-4x text-muted opacity-25"></i>
            </div>
            <h3 class="fw-bold mb-3">سلة التسوق فارغة</h3>
            <p class="text-muted mb-5">لم تقومي بإضافة أي منتجات للسلة بعد.</p>
            <a href="{{ route('shop.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow">ابدئي التسوق الآن</a>
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
        if (data.success) location.reload();
    });
}

function removeItem(id) {
    Swal.fire({
        title: 'هل أنت متأكدة؟',
        text: "سيتم حذف هذا المنتج من السلة",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5a059',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذفيه',
        cancelButtonText: 'إلغاء'
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
