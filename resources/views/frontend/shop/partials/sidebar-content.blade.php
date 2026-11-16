{{-- Search --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1.5px solid #dcebe0; box-shadow: 0 4px 18px rgba(30,77,52,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.95rem; font-weight: 800; color: #1e4d34; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-search" style="color: #2f8825;"></i>
        <span>Rechercher un produit</span>
    </h6>
    <form id="searchForm">
        <div class="shop-search-wrap" style="position: relative; display: flex; align-items: center;">
            <input type="text" name="q" class="shop-search-input"
                   placeholder="Miel de thym, daghmous, amlou…" value="{{ request('q') }}"
                   style="width: 100%; height: 42px; border-radius: 10px; border: 1.5px solid #dcebe0; padding: 0 40px 0 14px; font-size: 0.88rem; outline: none; background: #f5faf6; font-family: inherit;">
            <button type="submit" class="shop-search-btn" style="position: absolute; right: 6px; width: 30px; height: 30px; border-radius: 8px; border: none; background: #2f8825; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </button>
        </div>
    </form>
</div>

{{-- Categories --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1.5px solid #dcebe0; box-shadow: 0 4px 18px rgba(30,77,52,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.95rem; font-weight: 800; color: #1e4d34; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-th-large" style="color: #2f8825;"></i>
        <span>Catégories</span>
    </h6>
    <ul class="shop-cat-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px;">
        <li>
            <a href="#" class="shop-cat-link category-filter {{ !request('category') ? 'active' : '' }}" data-slug="" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 700; color: {{ !request('category') ? '#2f8825' : '#2b302c' }}; background: {{ !request('category') ? '#eaf5e8' : 'transparent' }};">
                <span>Tous les produits</span>
                <span class="shop-cat-count" style="font-size: 0.76rem; background: #eaf5e8; color: #2f8825; padding: 2px 8px; border-radius: 9999px;">{{ \App\Models\Product::where('status','active')->count() }}</span>
            </a>
        </li>
        @foreach($categories as $cat)
        <li>
            <a href="#" class="shop-cat-link category-filter {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 700; color: {{ request('category') == $cat->slug ? '#2f8825' : '#2b302c' }}; background: {{ request('category') == $cat->slug ? '#eaf5e8' : 'transparent' }};">
                <span>{{ $cat->name }}</span>
                <span class="shop-cat-count" style="font-size: 0.76rem; background: #eaf5e8; color: #2f8825; padding: 2px 8px; border-radius: 9999px;">{{ $cat->products()->where('status','active')->count() }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>

{{-- Price Range --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1.5px solid #dcebe0; box-shadow: 0 4px 18px rgba(30,77,52,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.95rem; font-weight: 800; color: #1e4d34; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-tags" style="color: #2f8825;"></i>
        <span>Prix (DH)</span>
    </h6>
    <form id="priceFilterForm">
        <div class="shop-price-inputs" style="display: flex; align-items: center; gap: 8px;">
            <input type="number" name="min_price" class="shop-price-input"
                   placeholder="Min DH" value="{{ request('min_price') }}" min="0" style="width: 50%; height: 38px; border-radius: 8px; border: 1.5px solid #dcebe0; padding: 0 10px; font-size: 0.85rem; outline: none; background: #f5faf6;">
            <span class="shop-price-sep" style="color: #5a6b60;">—</span>
            <input type="number" name="max_price" class="shop-price-input"
                   placeholder="Max DH" value="{{ request('max_price') }}" min="0" style="width: 50%; height: 38px; border-radius: 8px; border: 1.5px solid #dcebe0; padding: 0 10px; font-size: 0.85rem; outline: none; background: #f5faf6;">
        </div>
        <button type="submit" class="shop-apply-btn w-100 mt-3" style="height: 38px; border-radius: 8px; border: none; background: #2f8825; color: #ffffff; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
            <i class="fas fa-filter me-2"></i>Filtrer
        </button>
    </form>
</div>

{{-- Contact Direct Coopérative --}}
<div class="shop-filter-card" style="background: #2f8825; color: #ffffff; border-radius: 18px; padding: 20px;">
    <h6 style="font-size: 0.95rem; font-weight: 800; color: #ffffff; margin-bottom: 12px;">
        📞 Commande Directe
    </h6>
    <div class="d-flex flex-column gap-2" style="font-size: 0.88rem; font-weight: 700;">
        <a href="tel:+212706770940" style="color: #ffffff; text-decoration: none;"><i class="fas fa-phone-alt me-2"></i>07 06 77 09 40</a>
        <a href="tel:+212670035202" style="color: #ffffff; text-decoration: none;"><i class="fas fa-phone-alt me-2"></i>06 70 03 52 02</a>
        <div style="font-size: 0.78rem; font-weight: 500; opacity: 0.9; margin-top: 4px;">Livraison partout au Maroc • Paiement à la réception</div>
    </div>
</div>
