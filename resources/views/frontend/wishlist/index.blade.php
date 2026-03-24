@extends('layouts.frontend')

@section('meta_title', 'قائمة أمنياتي الملكية — ' . setting('app_name', 'Hijab Princesses'))

@section('content')
<div class="bg-surface section-py min-vh-100">
    <div class="container px-xl-5">
        
        <div class="d-flex flex-column mb-5" data-aos="fade-up">
            <h1 class="brand-heading h2 mb-2">قائمة أمنياتكِ الملكية</h1>
            <div class="bg-gold rounded" style="width: 60px; height: 4px;"></div>
        </div>

        @if($wishlistItems->count() > 0)
        <div class="row g-3 g-lg-4">
            @foreach($wishlistItems as $item)
            @php $product = $item->product; @endphp
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                <div class="brand-card border-0 shadow-sm h-100 bg-white overflow-hidden hvr-float">
                    <div class="position-relative">
                        <a href="{{ route('shop.show', $product->id) }}" class="d-block">
                            @php $pImg = $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg'); @endphp
                            <img src="{{ $pImg }}" class="card-img-top object-fit-cover" alt="{{ $product->name }}" style="aspect-ratio: 3/4; height: auto;">
                        </a>
                        <button class="btn btn-white shadow-sm rounded-circle position-absolute top-0 end-0 m-2 wishlist-btn text-gold" 
                                onclick="removeFromWishlist(event, {{ $product->id }}, this)"
                                data-product-id="{{ $product->id }}"
                                style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--brand-gold-subtle);">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body p-3 text-center">
                        <h6 class="brand-heading mb-2 h6 text-truncate">
                            <a href="{{ route('shop.show', $product->id) }}" class="text-decoration-none text-dark hover-gold transition-300">{{ $product->name }}</a>
                        </h6>
                        <p class="text-gold fw-bold mb-3 font-body small">{{ $product->formatted_price }}</p>
                        <a href="{{ route('shop.show', $product->id) }}" class="btn-brand-outline w-100 py-1 small text-decoration-none">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            {{ $wishlistItems->links() }}
        </div>
        @else
        <div class="text-center py-5" data-aos="fade-up">
            <div class="mb-5 bg-gold-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                <i class="far fa-heart fa-4x text-gold opacity-50"></i>
            </div>
            <h2 class="brand-heading mb-3">قائمة أمنياتكِ خالية حالياً</h2>
            <p class="text-muted mb-5 font-body text-center mx-auto" style="max-width: 400px;">لا تدعي أجمل العبايات والخمارات تفوتكِ. أضيفيها هنا لتتسوقيها لاحقاً.</p>
            <a href="{{ route('shop.index') }}" class="btn-brand-primary px-5 py-3 text-decoration-none hvr-grow">اكتشفي التشكيلة الآن</a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function removeFromWishlist(e, productId, btn) {
    Swal.fire({
        title: 'حذف من القائمة؟',
        text: "هل تريدين إزالة هذا المنتج من قائمة أمنياتكِ؟",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#c5a059',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'نعم، إزالة',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('wishlist.toggle') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'removed') {
                    btn.closest('.col-6').remove();
                    if(document.querySelectorAll('.col-6').length === 0) {
                        location.reload();
                    }
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title:'تمت الإزالة بنجاح', showConfirmButton:false, timer:2000 });
                }
            })
            .catch(console.error);
        }
    });
}
</script>
@endpush
@endsection
