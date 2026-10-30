<div class="mobile-product-card my-2" data-product-id="{{ $product->id }}" id="mobile-product-card-{{ $product->id }}">
    <!-- Main Info Row (Checkbox + Image + Details + Price) -->
    <div class="mobile-prod-main-row">
        <!-- Selection Checkbox -->
        <div class="mobile-prod-check-col">
            <input type="checkbox" 
                   class="form-check-input product-checkbox" 
                   value="{{ $product->id }}" 
                   id="check-mob-{{ $product->id }}"
                   onchange="updateSelection()"
                   title="Sélectionner ce produit">
        </div>

        <!-- Product Thumbnail with 4K Quick Trigger -->
        <div class="mobile-prod-thumb-wrap">
            <div class="mobile-prod-thumb">
                <img id="mob-prod-img-{{ $product->id }}" 
                     src="{{ $product->thumbnail }}" 
                     alt="{{ $product->name }}" 
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                     @if($product->image_padding_style) style="padding: {{ $product->image_padding_style }} !important; box-sizing: border-box !important;" @endif>
            </div>
            <button type="button" 
                    class="mobile-prod-thumb-badge" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-thumbnail="{{ $product->thumbnail }}"
                    data-product-resolution="{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}"
                    onclick="open4kFinderFromElement(this)"
                    title="Studio 4K Ultra-HD">
                <i class="fas fa-wand-magic-sparkles"></i>
            </button>
        </div>

        <!-- Center Details -->
        <div class="mobile-prod-content">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <!-- Title -->
                <div class="min-w-0 flex-grow-1">
                    <a href="{{ route('products.edit', ['product' => $product->id, 'return_url' => request()->fullUrl()]) }}" class="mobile-prod-title" title="{{ $product->name }}">
                        {{ $product->name }}
                    </a>
                    @if($product->variants && $product->variants->isNotEmpty())
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-1.5 py-0.5 ms-1" style="font-size: 0.65rem;">
                            <i class="fas fa-layer-group me-1"></i>{{ $product->variants->count() }} var.
                        </span>
                    @endif
                </div>

                <!-- Price Top-Right -->
                <div class="mobile-prod-price-box text-end flex-shrink-0">
                    @if($product->isOnSale())
                        <div class="mobile-prod-price-val text-success">{{ currency($product->sale_price) }}</div>
                        <div class="text-muted small text-decoration-line-through">{{ currency($product->price) }}</div>
                    @else
                        <div class="mobile-prod-price-val">{{ currency($product->price) }}</div>
                    @endif
                </div>
            </div>

            <!-- Tags Row (SKU, Category, Stock, Resolution) -->
            <div class="mobile-prod-tags-row">
                <span class="mobile-tag-sku" title="SKU">{{ $product->sku }}</span>

                @if($product->category_name)
                    <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="mobile-tag-cat" title="Catégorie: {{ $product->category_name }}">
                        <i class="fas fa-folder me-1 opacity-50"></i>{{ Str::limit($product->category_name, 14) }}
                    </a>
                @endif

                @php
                    $stock = $product->stock;
                    $min = $product->min_stock ?? 5;
                    if ($stock <= 0) {
                        $stockClass = 'out';
                        $dotColor = 'red';
                        $stockText = 'Rupture (0)';
                    } elseif ($stock <= $min) {
                        $stockClass = 'low';
                        $dotColor = 'amber';
                        $stockText = 'Faible: ' . $stock;
                    } else {
                        $stockClass = 'in';
                        $dotColor = 'green';
                        $stockText = 'Stock: ' . $stock;
                    }
                @endphp
                <span class="mobile-tag-stock mobile-stock-{{ $stockClass }}">
                    <span class="status-dot dot-{{ $dotColor }}"></span>{{ $stockText }}
                </span>

                <span id="mob-prod-badge-{{ $product->id }}" class="mobile-tag-res">
                    {!! $product->image_quality_badge !!}
                </span>
            </div>
        </div>
    </div>

    <!-- Bottom Action & Status Toolbar -->
    <div class="mobile-prod-toolbar">
        <div class="d-flex align-items-center gap-1.5 flex-wrap">
            @php
                $st = strtolower($product->status);
                $isActive = in_array($st, ['active', 'actif']);
            @endphp
            <span class="mobile-status-pill {{ $isActive ? 'active' : 'inactive' }}">
                <span class="status-dot {{ $isActive ? 'dot-green' : 'dot-gray' }}"></span>
                {{ $isActive ? 'Actif' : 'Inactif' }}
            </span>
            <div class="prod-order-quick-wrap d-inline-flex align-items-center gap-1 bg-light rounded-pill px-2 py-0.5 border" title="Ordre d'affichage sur la boutique (1 = premier)">
                <span class="text-muted fw-bold" style="font-size: 0.65rem;">Ordre</span>
                <input type="number" 
                       min="0" 
                       class="form-control form-control-sm text-center p-0 prod-sort-order-input" 
                       value="{{ $product->sort_order ?? 0 }}" 
                       data-product-id="{{ $product->id }}"
                       data-original-value="{{ $product->sort_order ?? 0 }}"
                       style="width: 38px; height: 22px; font-size: 0.75rem; font-weight: 700; border-radius: 4px; {{ ($product->sort_order ?? 0) > 0 ? 'background-color: #eff6ff; color: #1d4ed8;' : 'background-color: transparent; color: #64748b;' }}; border: 1px solid #cbd5e1;">
                <span class="order-saved-indicator" style="display:none; font-size: 0.72rem; color: #16a34a;">
                    <i class="fas fa-check-circle"></i>
                </span>
            </div>
            @if($product->cost_price)
                <span class="mobile-cost-badge" title="Prix de revient">
                    Coût: {{ currency($product->cost_price) }}
                </span>
            @endif
        </div>

        <div class="mobile-action-btns">
            <!-- Boutique Link -->
            <a href="{{ route('shop.show', $product->slug ?: $product->id) }}" 
               target="_blank" 
               class="mobile-act-btn mobile-act-view" 
               title="Voir sur la boutique">
                <i class="fas fa-eye"></i>
            </a>

            <!-- Studio 4K -->
            <button type="button" 
                    class="mobile-act-btn mobile-act-4k" 
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-thumbnail="{{ $product->thumbnail }}"
                    data-product-resolution="{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}"
                    onclick="open4kFinderFromElement(this)" 
                    title="Studio 4K Ultra-HD">
                <i class="fas fa-wand-magic-sparkles"></i>
                <span>4K</span>
            </button>

            <!-- Modifier -->
            <a href="{{ route('products.edit', ['product' => $product->id, 'return_url' => request()->fullUrl()]) }}" 
               class="mobile-act-btn mobile-act-edit" 
               title="Modifier le produit">
                <i class="fas fa-pencil-alt"></i>
                <span>Modifier</span>
            </a>

            <!-- Supprimer -->
            <form method="POST" 
                  action="{{ route('products.destroy', $product->id) }}" 
                  class="m-0 p-0 d-inline"
                  data-confirm-delete="true"
                  data-ajax-delete="true"
                  data-product-id="{{ $product->id }}"
                  data-item-type="product"
                  data-item-name="{{ $product->name }}">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="mobile-act-btn mobile-act-delete" 
                        title="Supprimer">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
