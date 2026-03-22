<div class="row g-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4">
        <div class="pcard">
            {{-- Image --}}
            <div class="pcard-img">
                <a href="{{ route('shop.show', $product->id) }}">
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->translated_name }}" loading="lazy">
                    @else
                        <div class="pcard-no-img"><i class="fas fa-print"></i></div>
                    @endif
                </a>

                {{-- Badges --}}
                <div class="pcard-badges">
                    @if(!$product->isInStock())
                        <span class="pcard-badge pcard-badge--oos">Rupture</span>
                    @elseif($product->created_at->diffInDays(now()) < 14)
                        <span class="pcard-badge pcard-badge--new">Nouveau</span>
                    @elseif($product->isOnSale())
                        <span class="pcard-badge pcard-badge--sale">−{{ $product->discount_percentage }}%</span>
                    @endif
                </div>

                {{-- Hover overlay actions --}}
                <div class="pcard-overlay">
                    @if($product->isInStock())
                    <button class="pcard-overlay-btn" onclick="addToCart({{ $product->id }})" title="Ajouter au panier">
                        <i class="fas fa-cart-plus"></i> Ajouter
                    </button>
                    @endif
                    <a href="{{ route('shop.show', $product->id) }}" class="pcard-overlay-btn pcard-overlay-btn--ghost" title="Voir le produit">
                        <i class="fas fa-eye"></i> Détails
                    </a>
                </div>
            </div>

            {{-- Info --}}
            <div class="pcard-body">
                @if($product->category_name)
                <div class="pcard-cat">{{ $product->category_name }}</div>
                @endif
                <h4 class="pcard-name">
                    <a href="{{ route('shop.show', $product->id) }}">{{ Str::limit($product->translated_name, 42) }}</a>
                </h4>
                <div class="pcard-rating">
                    <div class="pcard-stars">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fa{{ $i < round($product->reviews_avg_rating ?? 0) ? 's' : 'r' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="pcard-reviews">({{ $product->reviews_count ?? 0 }})</span>
                </div>
                <div class="pcard-price">
                    @if($product->isOnSale())
                        <span class="pcard-price-current" id="pcard-price-{{ $product->id }}">{{ $product->formatted_sale_price }}</span>
                        <span class="pcard-price-old">{{ $product->formatted_price }}</span>
                    @else
                        <span class="pcard-price-current" id="pcard-price-{{ $product->id }}">{{ $product->formatted_price }}</span>
                    @endif
                </div>

                {{-- Card Variations Selector --}}
                @if($product->variants->count() > 0)
                <div class="pcard-variants">
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
                             onclick="selectCardVariant({{ $product->id }}, 'color', '{{ $color->color }}', this)"
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
                             onclick="selectCardVariant({{ $product->id }}, 'size', '{{ $size }}', this)">
                            {{ $size }}
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <input type="hidden" id="card-selected-variant-{{ $product->id }}" value="">
                    <script>
                        if (typeof cardVariants === 'undefined') window.cardVariants = {};
                        window.cardVariants[{{ $product->id }}] = {!! $product->variants_json !!};
                    </script>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty">
            <i class="fas fa-print shop-empty-icon"></i>
            <h5>Aucun produit trouvé</h5>
            <p>Modifiez vos filtres ou votre recherche pour voir plus de résultats.</p>
            <a href="{{ route('shop.index') }}" class="shop-apply-btn d-inline-flex gap-2 align-items-center">
                <i class="fas fa-redo"></i> Réinitialiser les filtres
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($products->hasPages())
<div class="mt-5 d-flex justify-content-center shop-pagination">
    {{ $products->links() }}
</div>
@endif
