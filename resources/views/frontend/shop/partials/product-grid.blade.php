<div class="row g-4">
    @forelse($products as $product)
    <div class="col-12 col-md-6 col-xl-4">
        @include('frontend.partials.product-card', ['product' => $product])
    </div>
    @empty
    <div class="col-12">
        <div class="shop-empty text-center py-5" style="background: #ffffff; border-radius: 20px; border: 1.5px dashed #dcebe0; padding: 48px 24px;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #eaf5e8; color: #2f8825; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-leaf fa-lg"></i>
            </div>
            <h5 style="font-family: 'Playfair Display', Georgia, serif; font-size: 1.3rem; color: #1e4d34; font-weight: 700;">Aucun produit trouvé</h5>
            <p style="color: #5a6b60; font-size: 0.9rem; max-width: 420px; margin: 0 auto 20px;">Modifiez vos critères ou réinitialisez les filtres pour découvrir nos produits.</p>
            <a href="{{ route('shop.index') }}" class="aod-btn-green" style="padding: 10px 22px; font-size: 0.88rem;">
                <i class="fas fa-redo"></i>
                <span>Réinitialiser les filtres</span>
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
