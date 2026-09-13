@php
    $variants = $product->variants->where('status', 'active');
    $selectedVariant = $variants->firstWhere('stock', '>', 0) ?? $variants->first();
@endphp
<div class="sizes" role="group" aria-label="الحجم">
@forelse($variants as $variant)
    <label class="size-option {{ $variant->id === $selectedVariant?->id ? 'selected' : '' }} {{ $variant->stock < 1 ? 'unavailable' : '' }}">
        <input type="radio" name="variant_id" value="{{ $variant->id }}" data-price="{{ $variant->price ?? $product->price }}" data-stock="{{ $variant->stock }}" @checked($variant->id === $selectedVariant?->id) @disabled($variant->stock < 1)>
        <span>{{ implode(' · ', array_filter([$variant->size, $variant->color])) ?: 'الحجم القياسي' }}</span>
    </label>
@empty
    <span class="size-option selected">الحجم القياسي</span>
@endforelse
</div>
