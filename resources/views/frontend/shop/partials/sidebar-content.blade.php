{{-- Search --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1px solid rgba(12,38,30,0.07); box-shadow: 0 4px 18px rgba(12,38,30,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1rem; font-weight: 700; color: #0c261e; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c28d32" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span>Recherche Botanique</span>
    </h6>
    <form id="searchForm">
        <div class="shop-search-wrap" style="position: relative; display: flex; align-items: center;">
            <input type="text" name="q" class="shop-search-input"
                   placeholder="Ex: romarin, solaire, argan…" value="{{ request('q') }}"
                   style="width: 100%; height: 42px; border-radius: 10px; border: 1.5px solid rgba(12,38,30,0.12); padding: 0 40px 0 14px; font-size: 0.88rem; outline: none; background: #faf8f3; font-family: inherit;">
            <button type="submit" class="shop-search-btn" style="position: absolute; right: 6px; width: 30px; height: 30px; border-radius: 8px; border: none; background: #0c261e; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            </button>
        </div>
    </form>
</div>

{{-- Categories --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1px solid rgba(12,38,30,0.07); box-shadow: 0 4px 18px rgba(12,38,30,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1rem; font-weight: 700; color: #0c261e; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c28d32" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        <span>Collections</span>
    </h6>
    <ul class="shop-cat-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px;">
        <li>
            <a href="#" class="shop-cat-link category-filter {{ !request('category') ? 'active' : '' }}" data-slug="" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 600; color: {{ !request('category') ? '#c28d32' : '#0c261e' }}; background: {{ !request('category') ? '#fbf8f0' : 'transparent' }};">
                <span>Tous les soins</span>
                <span class="shop-cat-count" style="font-size: 0.76rem; background: rgba(12,38,30,0.06); padding: 2px 8px; border-radius: 9999px;">{{ \App\Models\Product::where('status','active')->count() }}</span>
            </a>
        </li>
        @foreach($categories as $cat)
        <li>
            <a href="#" class="shop-cat-link category-filter {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; border-radius: 8px; text-decoration: none; font-size: 0.88rem; font-weight: 600; color: {{ request('category') == $cat->slug ? '#c28d32' : '#0c261e' }}; background: {{ request('category') == $cat->slug ? '#fbf8f0' : 'transparent' }};">
                <span>{{ $cat->name }}</span>
                <span class="shop-cat-count" style="font-size: 0.76rem; background: rgba(12,38,30,0.06); padding: 2px 8px; border-radius: 9999px;">{{ $cat->products()->where('status','active')->count() }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>

{{-- Price Range --}}
<div class="shop-filter-card mb-4" style="background: #ffffff; border-radius: 18px; padding: 20px; border: 1px solid rgba(12,38,30,0.07); box-shadow: 0 4px 18px rgba(12,38,30,0.03);">
    <h6 class="shop-filter-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 1rem; font-weight: 700; color: #0c261e; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#c28d32" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span>Budget (DH)</span>
    </h6>
    <form id="priceFilterForm">
        <div class="shop-price-inputs" style="display: flex; align-items: center; gap: 8px;">
            <input type="number" name="min_price" class="shop-price-input"
                   placeholder="Min DH" value="{{ request('min_price') }}" min="0" style="width: 50%; height: 38px; border-radius: 8px; border: 1.5px solid rgba(12,38,30,0.12); padding: 0 10px; font-size: 0.85rem; outline: none; background: #faf8f3;">
            <span class="shop-price-sep" style="color: #6b7a72;">—</span>
            <input type="number" name="max_price" class="shop-price-input"
                   placeholder="Max DH" value="{{ request('max_price') }}" min="0" style="width: 50%; height: 38px; border-radius: 8px; border: 1.5px solid rgba(12,38,30,0.12); padding: 0 10px; font-size: 0.85rem; outline: none; background: #faf8f3;">
        </div>
        <button type="submit" class="shop-apply-btn w-100 mt-3" style="height: 38px; border-radius: 8px; border: none; background: #0c261e; color: #ffffff; font-weight: 700; font-size: 0.85rem; cursor: pointer;">
            <i class="fas fa-filter me-2"></i>Filtrer
        </button>
    </form>
</div>

{{-- Engagements Coopérative --}}
<div class="shop-filter-card" style="background: linear-gradient(145deg, #fbfaf6 0%, #f4eee3 100%); border-radius: 18px; padding: 20px; border: 1px solid rgba(226,173,80,0.3);">
    <h6 class="shop-filter-title" style="font-family: 'Playfair Display', Georgia, serif; font-size: 0.95rem; font-weight: 700; color: #0c261e; margin-bottom: 12px;">
        🌿 Nos Engagements Bio
    </h6>
    <div class="d-flex flex-column gap-2" style="font-size: 0.8rem; color: #4b5563; line-height: 1.4;">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-check-circle text-success" style="color: #15803d !important;"></i>
            <span>100% Ingrédients naturels</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-mountain text-muted" style="color: #8c6014 !important;"></i>
            <span>Plantes sauvages du Haut Atlas</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-hand-holding-heart text-danger" style="color: #c28d32 !important;"></i>
            <span>Commerce équitable & solidaire</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-truck text-primary" style="color: #0c261e !important;"></i>
            <span>Livraison rapide partout au Maroc</span>
        </div>
    </div>
</div>