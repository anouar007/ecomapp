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
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
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
                    <a href="{{ route('products.edit', $product) }}" class="mobile-prod-title" title="{{ $product->name }}">
                        {{ $product->name }}
                    </a>
                </div>

                <!-- Price Top-Right -->
                <div class="mobile-prod-price-box text-end flex-shrink-0">
                    <div class="mobile-prod-price-val">{{ currency($product->price) }}</div>
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
            <a href="{{ route('products.edit', $product) }}" 
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
