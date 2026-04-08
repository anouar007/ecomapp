@if($products->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4 opacity-25"></i>
        <h3 class="brand-heading h4 mb-3">لا توجد منتجات حالياً</h3>
        <p class="text-muted font-body mb-0">نعمل على إضافة قطع جديدة قريباً، ابقي بالقرب!</p>
    </div>
@else
    <div class="row g-3 g-lg-4" id="products-grid">
        @foreach($products as $product)
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                @include('frontend.partials.product_card_v2', ['product' => $product])
            </div>
        @endforeach
    </div>
@endif
