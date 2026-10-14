<tr>
    <td>
        <div class="d-flex align-items-center gap-3" style="{{ $level > 0 ? 'padding-left: ' . ($level * 36) . 'px;' : '' }}">
            @if($level > 0)
                <span class="cat-tree-branch">└─</span>
            @endif
            <div class="brand-avatar">
                @if($category->image)
                    <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                @elseif($category->icon)
                    <i class="{{ $category->icon }}"></i>
                @else
                    <i class="fas fa-folder"></i>
                @endif
            </div>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark fs-6">{{ $category->name }}</span>
                    @if(isset($isFiltered) && $isFiltered && $category->parent)
                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.65rem;" title="Catégorie parente">
                            <i class="fas fa-level-up-alt fa-rotate-90 text-danger me-1"></i>{{ $category->parent->name }}
                        </span>
                    @endif
                </div>
                @if($category->description)
                    <div class="text-muted small text-truncate" style="max-width: 320px;">
                        {{ $category->description }}
                    </div>
                @endif
                @if($category->hasChildren() && (!isset($isFiltered) || !$isFiltered))
                    <div class="mt-1" style="font-size: 0.68rem; color: var(--primary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
                        <i class="fas fa-sitemap me-1"></i> {{ $category->children->count() }} sous-catégorie(s)
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-light text-secondary font-monospace px-2 py-1" style="font-size: 0.72rem; border: 1px solid #e2e8f0; border-radius: 6px;">
            {{ $category->slug }}
        </span>
    </td>
    <td class="text-center">
        <a href="{{ route('products.index', ['category' => $category->id]) }}" 
           class="brand-badge info text-decoration-none" 
           title="Voir les {{ $category->products_count }} produits dans cette catégorie">
            <i class="fas fa-box me-1"></i> {{ $category->products_count }} {{ $category->products_count > 1 ? 'produits' : 'produit' }}
        </a>
    </td>
    <td class="text-center">
        <span class="badge bg-white text-muted border px-2 py-1 font-monospace" style="font-size: 0.72rem; border-radius: 6px;" title="Ordre de tri">
            #{{ $category->sort_order ?? 0 }}
        </span>
    </td>
    <td class="text-center">
        @php
            $isActive = in_array(strtolower($category->status), ['active', 'actif']);
        @endphp
        <span class="brand-badge {{ $isActive ? 'success' : 'danger' }}">
            <span class="badge-dot" style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
            {{ $isActive ? 'Actif' : 'Inactif' }}
        </span>
    </td>
    <td style="padding-right: 1.5rem;">
        <div class="action-buttons-group">
            <a href="{{ route('shop.index', ['category' => $category->slug]) }}" 
               target="_blank" 
               class="action-btn action-btn-view" 
               title="Voir sur la boutique (nouvel onglet)">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <a href="{{ route('categories.edit', $category) }}" 
               class="action-btn action-btn-edit" 
               title="Modifier la catégorie">
                <i class="fas fa-edit"></i>
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
                    <button type="submit" class="action-btn action-btn-delete" title="Supprimer la catégorie">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @else
                <button type="button" 
                        class="action-btn text-muted" 
                        style="opacity: 0.4; cursor: not-allowed; background: #f8fafc;" 
                        title="Suppression verrouillée : contient {{ $category->products_count }} produit(s) ou des sous-catégories" 
                        disabled>
                    <i class="fas fa-lock"></i>
                </button>
            @endif
        </div>
    </td>
</tr>
