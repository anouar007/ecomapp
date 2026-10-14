<div class="mobile-category-card my-2 {{ ($level ?? 0) > 0 ? 'is-child' : '' }}">
    <!-- Indicator for subcategory -->
    @if((($level ?? 0) > 0) || (isset($isFiltered) && $isFiltered && $category->parent))
        <div class="mobile-cat-parent-badge">
            <i class="fas fa-level-up-alt fa-rotate-90 text-danger me-1"></i>
            <span>Sous-catégorie de : <strong class="text-dark">{{ $category->parent?->name ?? 'Parent' }}</strong></span>
        </div>
    @endif

    <div class="mobile-cat-header">
        <div class="mobile-cat-main">
            <div class="mobile-cat-avatar">
                @if($category->image)
                    <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                @elseif($category->icon)
                    <i class="{{ $category->icon }} text-muted"></i>
                @else
                    <i class="fas fa-folder text-muted"></i>
                @endif
            </div>
            <div class="min-w-0">
                <h3 class="mobile-cat-title text-truncate">{{ $category->name }}</h3>
                <span class="mobile-cat-slug">{{ $category->slug }}</span>
            </div>
        </div>
        <div class="flex-shrink-0">
            @php $isActive = in_array(strtolower($category->status), ['active', 'actif']); @endphp
            <span class="brand-badge {{ $isActive ? 'success' : 'danger' }}">
                <span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                {{ $isActive ? 'Actif' : 'Inactif' }}
            </span>
        </div>
    </div>

    @if($category->description)
        <p class="mobile-cat-desc">{{ $category->description }}</p>
    @endif

    <div class="mobile-cat-meta">
        <!-- Produits associés link -->
        <a href="{{ route('products.index', ['category' => $category->id]) }}" class="mobile-cat-pill" title="Voir les produits">
            <i class="fas fa-box text-primary"></i>
            <span><strong>{{ $category->products_count }}</strong> {{ $category->products_count > 1 ? 'produits' : 'produit' }}</span>
        </a>

        @if($category->hasChildren() && (!isset($isFiltered) || !$isFiltered))
            <span class="mobile-cat-pill">
                <i class="fas fa-sitemap text-warning"></i>
                <span><strong>{{ $category->children->count() }}</strong> sous-catégories</span>
            </span>
        @endif

        <span class="mobile-cat-pill">
            <i class="fas fa-sort-numeric-down text-muted"></i>
            <span>Ordre: {{ $category->sort_order ?? 0 }}</span>
        </span>
    </div>

    <div class="mobile-cat-actions">
        <a href="{{ route('shop.index', ['category' => $category->slug]) }}" 
           target="_blank" 
           class="btn btn-sm btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" 
           style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0.45rem;">
            <i class="fas fa-external-link-alt text-danger"></i>
            <span>Boutique</span>
        </a>
        <a href="{{ route('categories.edit', $category) }}" 
           class="btn btn-sm btn-outline-primary flex-grow-1 d-flex align-items-center justify-content-center gap-1.5" 
           style="border-radius: 8px; font-weight: 600; font-size: 0.8rem; padding: 0.45rem;">
            <i class="fas fa-edit"></i>
            <span>Modifier</span>
        </a>
        @if($category->products_count == 0 && !$category->hasChildren())
            <form method="POST"
                  action="{{ route('categories.destroy', $category->id) }}"
                  style="display: inline-block; margin: 0;"
                  data-confirm-delete="true"
                  data-item-type="category"
                  data-item-name="{{ $category->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center" 
                        style="width: 38px; height: 38px; border-radius: 8px; padding: 0;" 
                        title="Supprimer">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        @else
            <button type="button" 
                    class="btn btn-sm btn-light border d-flex align-items-center justify-content-center" 
                    style="width: 38px; height: 38px; border-radius: 8px; opacity: 0.4; cursor: not-allowed; padding: 0;" 
                    title="Suppression verrouillée : contient {{ $category->products_count }} produit(s) ou des sous-catégories" 
                    disabled>
                <i class="fas fa-lock text-muted"></i>
            </button>
        @endif
    </div>
</div>
