{{-- Search --}}
<div class="shop-filter-card mb-4">
    <h6 class="shop-filter-title"><i class="fas fa-search me-2"></i>Recherche</h6>
    <form id="searchForm">
        <div class="shop-search-wrap">
            <input type="text" name="q" class="shop-search-input"
                   placeholder="Nom du produit…" value="{{ request('q') }}">
            <button type="submit" class="shop-search-btn">
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

{{-- Categories --}}
<div class="shop-filter-card mb-4">
    <h6 class="shop-filter-title"><i class="fas fa-th-large me-2"></i>Catégories</h6>
    <ul class="shop-cat-list">
        <li>
            <a href="#" class="shop-cat-link category-filter {{ !request('category') ? 'active' : '' }}" data-slug="">
                <span>Tous les produits</span>
                <span class="shop-cat-count">{{ \App\Models\Product::where('status','active')->count() }}</span>
            </a>
        </li>
        @foreach($categories as $cat)
        <li>
            <a href="#" class="shop-cat-link category-filter {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}">
                <span>{{ $cat->name }}</span>
                <span class="shop-cat-count">{{ $cat->products_count }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>

{{-- Price Range --}}
<div class="shop-filter-card mb-4">
    <h6 class="shop-filter-title"><i class="fas fa-tag me-2"></i>Fourchette de prix</h6>
    <form id="priceFilterForm">
        <div class="shop-price-inputs">
            <input type="number" name="min_price" class="shop-price-input"
                   placeholder="Min" value="{{ request('min_price') }}" min="0">
            <span class="shop-price-sep">—</span>
            <input type="number" name="max_price" class="shop-price-input"
                   placeholder="Max" value="{{ request('max_price') }}" min="0">
        </div>
        <button type="submit" class="shop-apply-btn w-100 mt-3">
            <i class="fas fa-filter me-2"></i>Appliquer
        </button>
    </form>
</div>

{{-- Quick Links --}}
<div class="shop-filter-card">
    <h6 class="shop-filter-title"><i class="fas fa-bolt me-2"></i>Raccourcis</h6>
    <div class="d-flex flex-column gap-2">
        <a href="{{ route('shop.index') }}?sort=newest" class="shop-quick-link">
            <i class="fas fa-star me-2 text-accent"></i>Nouveautés
        </a>
        <a href="{{ route('shop.index') }}?sort=price_asc" class="shop-quick-link">
            <i class="fas fa-sort-amount-up me-2 text-accent"></i>Prix croissant
        </a>
        <a href="{{ route('shop.index') }}?sort=price_desc" class="shop-quick-link">
            <i class="fas fa-sort-amount-down me-2 text-accent"></i>Prix décroissant
        </a>
    </div>
</div>
