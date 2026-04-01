@extends('layouts.frontend')

@section('meta_title', 'Portfolio — Galerie de l\'Artisanat Moubdi3oun')

@section('content')
<!-- Editorial Hero -->
<section class="section-py bg-white overflow-hidden" style="padding-top: 120px;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <span class="badge-new mb-3 d-inline-block" data-aos="fade-down">L'Esthétique de l'Objet</span>
                <h1 class="display-1 fw-black text-uppercase ls-1 mb-5" data-aos="reveal-up" style="letter-spacing: -3px;">Galerie <br><span style="color: var(--accent);">Moubdi3oun</span></h1>
                <p class="lead text-muted mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="200">
                    Explorez une sélection curatée de nos créations les plus emblématiques. Chaque pièce est une exploration de la forme, du matériau et du confort.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Filterable Masonry Grid -->
<section class="section-py bg-white pt-0">
    <div class="container">
        <!-- Filters -->
        <div class="portfolio-filters" data-aos="fade-up" data-aos-delay="400">
            <button class="filter-btn active" data-filter="*">Tout Voir</button>
            @foreach($categories as $cat)
                <button class="filter-btn" data-filter=".cat-{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        <!-- Grid -->
        <div class="portfolio-grid" id="portfolioGrid">
            @foreach($products as $index => $product)
                @php
                    $isTall = in_array($index % 6, [1, 4]); // Editorial variation
                    $isWide = in_array($index % 6, [0, 5]);
                    $sizeClass = '';
                    if($isTall) $sizeClass = 'tall';
                    // if($isWide) $sizeClass = 'wide';
                @endphp
                <div class="portfolio-item cat-{{ $product->productCategory->id }} {{ $sizeClass }}" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                    <div class="portfolio-card-v3">
                        <img src="{{ $product->main_image ? Storage::url($product->main_image) : 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80' }}" alt="{{ $product->name }}">
                        <div class="portfolio-overlay-v3">
                            <span class="small text-uppercase fw-bold ls-2 text-white-50 mb-2">{{ $product->productCategory->name }}</span>
                            <div class="portfolio-title-reveal">
                                <h4 class="fw-black text-white text-uppercase ls-1 mb-3">{{ $product->name }}</h4>
                            </div>
                            <a href="{{ route('shop.show', $product->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-4 text-uppercase fw-bold" style="font-size: 0.65rem;">Voir le projet</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Artisan Quote / Break -->
<section class="section-py bg-dark text-white text-center overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row justify-content-center py-5">
            <div class="col-lg-8" data-aos="zoom-in">
                <i class="fas fa-quote-left fs-1 mb-4 opacity-25" style="color: var(--accent);"></i>
                <h2 class="fw-black text-uppercase ls-1 h1 mb-4">"Le bois a une âme, nous ne faisons que la révéler."</h2>
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <span class="small text-uppercase fw-bold ls-2 opacity-50">Maître Artisan</span>
                    <div style="width: 40px; height: 1px; background: var(--accent);"></div>
                    <span class="small text-uppercase fw-black ls-2">Moubdi3oun Atelier</span>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Isotope after images are loaded
    const grid = document.querySelector('#portfolioGrid');
    const iso = new Isotope(grid, {
        itemSelector: '.portfolio-item',
        percentPosition: true,
        transitionDuration: '0.6s',
        hiddenStyle: { opacity: 0, transform: 'scale(0.8)' },
        visibleStyle: { opacity: 1, transform: 'scale(1)' },
        masonry: {
            columnWidth: '.portfolio-item'
        }
    });

    imagesLoaded(grid).on('progress', function() {
        iso.layout();
    });

    // Filtering logic
    const filters = document.querySelector('.portfolio-filters');
    filters.addEventListener('click', function(event) {
        if (!event.target.classList.contains('filter-btn')) return;
        
        const filterValue = event.target.getAttribute('data-filter');
        iso.arrange({ filter: filterValue });

        // Update active class
        filters.querySelector('.active').classList.remove('active');
        event.target.classList.add('active');
    });

    // Custom Animation Reveal-up (GSAP style via Intersection Observer)
    const observerOptions = { threshold: 0.1 };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, observerOptions);

    document.querySelectorAll('[data-aos="reveal-up"]').forEach(el => observer.observe(el));
});
</script>

<style>
/* Custom Reveal Animation Styles */
[data-aos="reveal-up"] {
    position: relative;
    overflow: hidden;
    color: transparent;
}
[data-aos="reveal-up"]::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--accent);
    transform: translateX(-100%);
    transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
[data-aos="reveal-up"].revealed {
    color: inherit;
}
[data-aos="reveal-up"].revealed::after {
    transform: translateX(101%);
}
</style>
@endpush
