<div class="row g-4">
    @forelse($products as $product)
    <div class="col-6 col-md-4">
        <div class="pcard">
            {{-- Image --}}
            <div class="pcard-img">
                <a href="{{ route('shop.show', $product->id) }}">
                    @php
                        $thumb = $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg');
                    @endphp
                    <img src="{{ $thumb }}" alt="{{ $product->translated_name }}" loading="lazy">
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
            </div>

            {{-- Info --}}
            <div class="pcard-body">
                @if($product->productCategory)
                <div class="pcard-cat text-uppercase small ls-1 fw-bold">{{ $product->productCategory->translated_name }}</div>
                @endif
                <h4 class="pcard-name">
                    <a href="{{ route('shop.show', $product->id) }}">{{ Str::limit($product->translated_name, 50) }}</a>
                </h4>
                <div class="pcard-price">
                    @if($product->isOnSale())
                        <span class="text-dark">{{ $product->formatted_sale_price }}</span>
                        <span class="pcard-price-old">{{ $product->formatted_price }}</span>
                    @else
                        <span class="text-dark">{{ $product->formatted_price }}</span>
                    @endif
                </div>

                    {{-- Card Variations Selector --}}
                    @if($product->variants->count() > 0)
                    <div class="pcard-variants mt-3 pt-3 border-top">
                        @php 
                            $sizes = $product->available_sizes;
                            $colors = $product->available_colors;
                        @endphp

                        @if($colors->count() > 1)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="small text-muted fw-bold">Variante:</span>
                            @foreach($colors as $color)
                            <div class="rounded-circle border" 
                                 style="width: 15px; height: 15px; background: {{ $color->color_code ?: '#eee' }};" 
                                 title="{{ $color->color }}">
                            </div>
                            @endforeach
                        </div>
                        @elseif($sizes->count() > 0)
                         <span class="small text-muted">{{ $sizes->count() }} dimensions disponibles</span>
                        @endif
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
