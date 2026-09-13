@forelse($products as $product)
    @include('storefront.partials.product-card')
@empty
    <div class="empty-state" id="no-results"><h2>لا توجد منتجات مطابقة</h2><p>جرّب تغيير البحث أو الفلاتر.</p><a class="primary" href="{{ route('shop.index') }}">عرض جميع المنتجات</a></div>
@endforelse
