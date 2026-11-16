@php
    $existingVariants = old('variants');
    if (is_null($existingVariants) && isset($product) && $product->relationLoaded('variants')) {
        $existingVariants = $product->variants->map(fn ($v) => [
            'size' => $v->size,
            'price' => $v->price,
        ])->toArray();
    }
    $existingVariants = is_array($existingVariants) ? array_values($existingVariants) : [];
@endphp

<div class="form-group" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 12px;">
        <div>
            <label class="form-label mb-0" style="font-weight: 700; color: #1e293b;">
                <i class="fas fa-balance-scale text-success me-1"></i> Sizes &amp; Prices (Variants)
            </label>
            <small class="form-help d-block mt-1">Manage multiple sizes and prices (e.g. 1kg, 500g, 250g for Honey or 800g, 400g for Amlou). The first row becomes the default price &amp; size.</small>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" class="btn btn-sm btn-outline-success" onclick="applyVariantPreset('honey')">
                <i class="fas fa-magic me-1"></i> Honey (1kg, 500g, 250g)
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="applyVariantPreset('amlou')">
                <i class="fas fa-magic me-1"></i> Amlou (800g, 400g)
            </button>
            <button type="button" class="btn btn-sm btn-dark" onclick="addVariantRow('', '')">
                <i class="fas fa-plus me-1"></i> Add Size
            </button>
        </div>
    </div>

    <div id="variantsContainer" style="display: flex; flex-direction: column; gap: 10px;">
        @foreach($existingVariants as $idx => $v)
        <div class="variant-row" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; align-items: center;">
            <input type="text" name="variants[{{ $idx }}][size]" class="form-control" value="{{ $v['size'] ?? '' }}" placeholder="Size (e.g. 1kg, 500g, 250g, 800g, 400g)">
            <input type="number" name="variants[{{ $idx }}][price]" class="form-control" value="{{ $v['price'] ?? '' }}" placeholder="Price (DH)" step="0.01" min="0">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariantRow(this)" title="Remove size">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        @endforeach
    </div>
</div>

<script>
let variantIndex = {{ count($existingVariants) }};

function addVariantRow(size = '', price = '') {
    const container = document.getElementById('variantsContainer');
    if (!container) return;
    const row = document.createElement('div');
    row.className = 'variant-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr auto; gap: 12px; align-items: center;';
    row.innerHTML = `
        <input type="text" name="variants[${variantIndex}][size]" class="form-control" value="${size}" placeholder="Size (e.g. 1kg, 500g, 250g, 800g, 400g)">
        <input type="number" name="variants[${variantIndex}][price]" class="form-control" value="${price}" placeholder="Price (DH)" step="0.01" min="0">
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeVariantRow(this)" title="Remove size">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(row);
    variantIndex++;
}

function removeVariantRow(btn) {
    const row = btn.closest('.variant-row');
    if (row) row.remove();
}

function applyVariantPreset(type) {
    const container = document.getElementById('variantsContainer');
    const basePriceInput = document.getElementById('price');
    const basePrice = basePriceInput && parseFloat(basePriceInput.value) > 0 ? parseFloat(basePriceInput.value) : 0;

    if (!container) return;
    container.innerHTML = '';
    variantIndex = 0;

    if (type === 'honey') {
        const p1 = basePrice || 200;
        addVariantRow('1kg', p1);
        addVariantRow('500g', Math.round(p1 / 2));
        addVariantRow('250g', Math.round(p1 / 4));
    } else if (type === 'amlou') {
        const p1 = basePrice || 200;
        addVariantRow('800g', p1);
        addVariantRow('400g', Math.round(p1 / 2));
    }
}
</script>
