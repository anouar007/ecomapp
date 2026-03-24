<div class="row g-3 g-lg-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4 col-lg-3">
        @include('frontend.partials.product_card_v2', ['product' => $product])
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted opacity-25"></i>
        </div>
        <h3 class="brand-heading text-muted">لم نجد أي منتجات</h3>
        <p class="text-muted small">جربي اختيار فئة أخرى أو العودة للمتجر الرئيسي.</p>
        <a href="{{ route('shop.index') }}" class="btn-brand-outline px-5 mt-3 text-decoration-none">عرض كل المنتجات</a>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
