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
                            <span class="price-sale text-primary">{{ $product->formatted_price }}</span>
                        </div>
                        <button onclick="addToCart({{ $product->id }})" class="btn btn-primary w-100 mt-3 rounded-pill">
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
function addToCart(productId) {
    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: 1 })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) throw new Error((data && data.message) || `حدث خطأ في الخادم`);
        
        const countEl = document.getElementById('header-cart-count');
        if(countEl && data.cartCount !== undefined) countEl.textContent = data.cartCount;
        
        Swal.fire({ 
            toast:true, 
            position:'top-start', 
            icon:'success', 
            title:'تمت الإضافة للسلة!', 
            showConfirmButton:false, 
            timer:2500, 
            background:'#000', 
            color:'#fff' 
        });
    })
    .catch(error => {
        Swal.fire({ toast:true, position:'top-start', icon:'error', title: error.message || 'خطأ', showConfirmButton:false, timer:3000 });
    });
}
</script>
@endpush
