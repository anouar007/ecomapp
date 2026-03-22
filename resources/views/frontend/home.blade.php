@extends('layouts.frontend')

@section('meta_title', 'أناقة الأميرة — متجر العبايات والخمارات الفاخرة')
@section('meta_description', 'اكتشفي تشكيلتنا الحصرية من العبايات الفاخرة والخمارات الأنيقة. جودة عالية وتوصيل لكل مدن المغرب.')

@section('content')

{{-- =============================================
     VIDEO HERO
     ============================================= --}}
<section class="video-hero">
    <video autoplay muted loop playsinline class="video-hero-bg">
        <source src="https://assets.mixkit.co/videos/preview/mixkit-fashion-model-showing-off-her-clothes-14243-large.mp4" type="video/mp4">
    </video>
    <div class="video-hero-overlay"></div>
    <div class="video-hero-content" data-aos="zoom-out" data-aos-duration="1200">
        <h1 class="display- hero fw-black mb-4">
            تألقي كالأميرة مع<br>
            <span class="text-primary">أرقى العبايات</span>
        </h1>
        <p class="lead mb-5 text-white-50">
            اكتشفي مجموعتنا الجديدة من العبايات والخمارات المصممة بعناية لتناسب أناقتك اليومية ومناسباتك الخاصة.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="{{ route('shop.index') }}" class="btn-hero-primary px-5 py-3">
                تسوقي الآن
                <i class="fas fa-shopping-bag ms-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- =============================================
     CIRCULAR CATEGORIES
     ============================================= --}}
<section class="section-py bg-white overflow-hidden">
    <div class="container">
        <div class="section-header mb-4" data-aos="fade-up">
            <h2 class="section-title">تسوقي حسب الفئة</h2>
        </div>
        
        <div class="cat-circle-list" data-aos="fade-up" data-aos-delay="100">
            @foreach($allCategories as $category)
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="cat-circle-item">
                <div class="cat-circle-img">
                    <img src="{{ $category->image ? (Str::startsWith($category->image, 'http') ? $category->image : Storage::url($category->image)) : asset('images/placeholder-cat.jpg') }}" alt="{{ $category->translated_name }}">
                </div>
                <span class="cat-circle-name">{{ $category->translated_name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- =============================================
     HORIZONTAL SCROLLS PER CATEGORY
     ============================================= --}}
@foreach($categoriesWithProducts as $category)
<section class="section-py {{ $loop->even ? 'bg-surface' : 'bg-white' }} overflow-hidden">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
            <h3 class="fw-bold m-0">{{ $category->translated_name }}</h3>
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="text-primary fw-bold text-decoration-none">
                عرض الكل <i class="fas fa-chevron-left ms-1 small"></i>
            </a>
        </div>

        <div class="h-scroll-container pb-4" data-aos="fade-up" data-aos-delay="100">
            @foreach($category->products as $product)
            <div class="h-scroll-item">
                <div class="product-card-v2">
                    <div class="product-v2-image">
                        <img src="{{ $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg') }}" alt="{{ $product->translated_name }}">
                        <div class="product-v2-overlay">
                            <a href="{{ route('shop.show', $product->id) }}" class="btn-overlay">
                                <i class="fas fa-eye me-2"></i> تفاصيل
                            </a>
                        </div>
                    </div>
                    <div class="product-v2-body">
                        <h4 class="product-v2-name mb-2">{{ Str::limit($product->translated_name, 35) }}</h4>
                        <div class="product-v2-price">
                            @if($product->isOnSale())
                                <span class="price-sale" id="pcard-price-v2-{{ $product->id }}">{{ $product->formatted_sale_price }}</span>
                                <span class="price-old">{{ $product->formatted_price }}</span>
                            @else
                                <span class="price-sale" id="pcard-price-v2-{{ $product->id }}">{{ $product->formatted_price }}</span>
                            @endif
                        </div>

                        {{-- Card Variations Selector --}}
                        @if($product->variants->count() > 0)
                        <div class="pcard-variants mt-2">
                             @php 
                                $sizes = $product->available_sizes;
                                $colors = $product->available_colors;
                            @endphp

                            @if($colors->count() > 0)
                            <div class="pcard-variant-row">
                                <span class="pcard-variant-label">اللون:</span>
                                @foreach($colors as $color)
                                <div class="pcard-color-dot" 
                                     style="background: {{ $color->color_code ?: '#eee' }}" 
                                     onclick="selectCardVariant({{ $product->id }}, 'color', '{{ $color->color }}', this, 'v2')"
                                     title="{{ $color->color }}">
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($sizes->count() > 0)
                            <div class="pcard-variant-row">
                                <span class="pcard-variant-label">المقاس:</span>
                                @foreach($sizes as $size)
                                <div class="pcard-size-pill" 
                                     onclick="selectCardVariant({{ $product->id }}, 'size', '{{ $size }}', this, 'v2')">
                                    {{ $size }}
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <input type="hidden" id="card-selected-variant-v2-{{ $product->id }}" value="">
                        </div>
                        <script>
                            if (typeof window.cardVariants === 'undefined') window.cardVariants = {};
                            window.cardVariants[{{ $product->id }}] = {!! $product->variants_json !!};
                        </script>
                        @endif

                        @if(!$product->isInStock())
                        <div class="out-of-stock-label mt-2"><i class="fas fa-exclamation-circle me-1"></i>نفذ من المخزن</div>
                        @endif

                        <button onclick="addToCart({{ $product->id }}, 'v2')" class="btn btn-primary w-100 mt-3 rounded-pill">
                            أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endforeach

{{-- =============================================
     SHOPPING PROCESS
     ============================================= --}}
<section class="section-py bg-dark-gradient text-white">
    <div class="container text-center">
        <div class="section-header section-header-light mb-5" data-aos="fade-up">
            <h2 class="section-title">كيف تتسوقين معنا؟</h2>
            <p class="opacity-75">خطوات سهلة وبسيطة لطلب منتجاتك المفضلة</p>
        </div>

        <div class="row g-4 mt-2">
            @php
            $steps = [
                ['icon'=>'fa-shopping-cart', 'title'=>'الطلب من الموقع', 'desc'=>'اختاري منتجاتك المفضلة وأضيفيها لسلة التسوق ثم أكملي الطلب بسهولة.'],
                ['icon'=>'fa-phone-alt',     'title'=>'مكالمة التأكيد', 'desc'=>'سيقوم فريقنا بالاتصال بك خلال وقت قصير لتأكيد المقاسات والعنوان.'],
                ['icon'=>'fa-box-open',      'title'=>'تجهيز الطلب',    'desc'=>'يتم تغليف طلبك بعناية فائقة لضمان وصوله إليك في أجمل صورة.'],
                ['icon'=>'fa-truck',         'title'=>'التوصيل للمنزل', 'desc'=>'يصلك المندوب حتى باب بيتك، والدفع عند الاستلام.'],
            ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="text-center">
                    <div class="app-icon mx-auto mb-4" style="width: 70px; height: 70px; font-size: 1.8rem;">
                        <i class="fas {{ $step['icon'] }}"></i>
                    </div>
                    <h5 class="fw-bold mb-3 text-primary">{{ $step['title'] }}</h5>
                    <p class="small opacity-75">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>

function toggleFaq(btn) {
    const item   = btn.closest('.faq-item');
    const answer = item.querySelector('.faq-answer');
    const allItems = document.querySelectorAll('.faq-item');

    allItems.forEach(el => {
        if (el !== item) {
            el.classList.remove('faq-open');
            el.querySelector('.faq-answer').style.display = 'none';
        }
    });

    if (item.classList.contains('faq-open')) {
        item.classList.remove('faq-open');
        answer.style.display = 'none';
    } else {
        item.classList.add('faq-open');
        answer.style.display = 'block';
    }
}

// ── Variant Selection Logic ──
function selectCardVariant(productId, type, value, element, prefix) {
    const container = element.closest('.pcard-variants');
    const row = element.closest('.pcard-variant-row');
    
    // Toggle active class
    row.querySelectorAll('.pcard-color-dot, .pcard-size-pill').forEach(el => el.classList.remove('active'));
    element.classList.add('active');

    // Update state
    if (!window.selectedCardVariants) window.selectedCardVariants = {};
    if (!window.selectedCardVariants[productId]) window.selectedCardVariants[productId] = {};
    window.selectedCardVariants[productId][type] = value;

    // Check if we have a complete match
    const variants = window.cardVariants[productId];
    const selection = window.selectedCardVariants[productId];
    
    const match = variants.find(v => {
        let isMatch = true;
        if (selection.color && v.color !== selection.color) isMatch = false;
        if (selection.size && v.size !== selection.size) isMatch = false;
        return isMatch;
    });

    if (match) {
        // Update price
        const priceEl = document.getElementById(`pcard-price-${prefix}-${productId}`);
        if (priceEl) priceEl.innerText = match.formatted_price;
        
        // Update hidden input
        const input = document.getElementById(`card-selected-variant-${prefix}-${productId}`);
        if (input) input.value = match.id;
    }
}

function addToCart(productId, prefix) {
    const variants = window.cardVariants ? window.cardVariants[productId] : null;
    const selectedVariantId = prefix ? document.getElementById(`card-selected-variant-${prefix}-${productId}`)?.value : null;

    if (variants && variants.length > 0 && !selectedVariantId) {
        Swal.fire({
            icon: 'warning',
            text: 'يرجى اختيار اللون والمقاس أولاً',
            confirmButtonText: 'حسناً',
            confirmButtonColor: 'var(--accent)'
        });
        return;
    }

    const payload = {
        quantity: 1,
        variant_id: selectedVariantId
    };

    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;

        if (!response.ok) {
            throw new Error((data && data.message) || `حدث خطأ في الخادم`);
        }
        
        // Success
        if (window.showMiniCart) window.showMiniCart();
        
        Swal.fire({
            icon: 'success',
            title: 'تمت الإضافة!',
            text: 'تمت إضافة المنتج إلى سلتك بنجاح',
            showConfirmButton: false,
            timer: 1500,
            position: 'top-end',
            toast: true
        });
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        Swal.fire({
            icon: 'error',
            title: 'عذراً',
            text: error.message,
            confirmButtonText: 'حسناً',
            confirmButtonColor: 'var(--accent)'
        });
    });
}
</script>
@endpush
