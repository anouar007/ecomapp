@if($products->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4 opacity-25"></i>
        <h3 class="brand-heading h4 mb-3">{{ __('No products currently available') }}</h3>
        <p class="text-muted font-body mb-0">{{ __('We are working on adding new pieces soon, stay tuned!') }}</p>
    </div>
@else
    <div class="row g-3 g-lg-4" id="products-grid">
        @foreach($products as $product)
            <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up">
                @include('frontend.partials.product_card_v2', ['product' => $product])
            </div>
        @endforeach
    </div>
@endif
