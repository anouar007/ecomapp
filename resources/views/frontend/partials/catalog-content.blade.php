@if(request('search') || request('sort'))
    {{-- SEARCH OR SORT VIEW (Single List) --}}
    <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
        <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
        <div>
            <h2 class="brand-heading mb-0 h3">نتائج البحث</h2>
            <p class="text-muted small mb-0 font-body fw-bold">تم العثory على <span class="text-gold">{{ $products->count() }}</span> قطعة</p>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="brand-heading">لم نجد أي منتجات تطابق بحثك</h4>
            <a href="javascript:void(0)" onclick="loadCategory('')" class="btn btn-brand-primary rounded-pill px-4 mt-3">عرض كل المجموعة</a>
        </div>
    @else
        <div class="row g-3 g-lg-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    @include('frontend.partials.product_card_v2', ['product' => $product])
                </div>
            @endforeach
        </div>
    @endif

@elseif(false) {{-- Removed Single Category View to show all categories at once --}}
@else
    {{-- ALL PRODUCTS GROUPED BY CATEGORY (Home View) --}}
    @foreach($categoriesWithProducts as $category)
        <div class="category-section-luxe mb-5" data-aos="fade-up" id="category-{{ $category->slug }}">
            <div class="section-premium-header">
                <h2 class="section-premium-title">{{ $category->translated_name }}</h2>
                <div class="section-premium-divider">
                    <div class="section-premium-line"></div>
                    <i class="fas fa-crown section-premium-icon"></i>
                    <div class="section-premium-line"></div>
                </div>
            </div>
            
            <div class="row g-3 g-lg-4">
                @foreach($category->products as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('frontend.partials.product_card_v2', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
