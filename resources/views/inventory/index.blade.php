@extends('layouts.app')

@section('title', __('Inventory Management'))

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                {{ __('Inventory Management') }}
            </h1>
            <p class="brand-subtitle">{{ __('Monitor stock levels, track sales velocity, and manage reorder points') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.alerts') }}" class="btn btn-brand-light font-inter">
                <i class="fas fa-bell me-1" style="color: var(--warning-color)"></i> {{ __('Stock Alerts') }}
            </a>
            <a href="{{ route('inventory.movements') }}" class="btn btn-brand-light font-inter">
                <i class="fas fa-history me-1" style="color: var(--primary-color)"></i> {{ __('Movements') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid mb-4">
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon primary">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="brand-stat-label">{{ __('Total Products') }}</div>
            <div class="brand-stat-value font-inter text-primary">{{ number_format($stats['total_products']) }}</div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="brand-stat-label">{{ __('Low Stock') }}</div>
            <div class="brand-stat-value font-inter text-warning">{{ number_format($stats['low_stock']) }}</div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="brand-stat-label">{{ __('Out of Stock') }}</div>
            <div class="brand-stat-value font-inter text-danger">{{ number_format($stats['out_of_stock']) }}</div>
        </div>
        
        <div class="brand-stat-card shadow-soft">
            <div class="brand-stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="brand-stat-label">{{ __('Stock Value') }}</div>
            <div class="brand-stat-value font-inter text-success">{{ currency($stats['total_stock_value']) }}</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar px-3 py-3">
        <!-- Mobile Toggle -->
        <div class="d-lg-none mb-2">
            <button class="btn btn-brand-light w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#mobileInventoryFilters" aria-expanded="false" aria-controls="mobileInventoryFilters">
                <span><i class="fas fa-filter me-2" style="color: var(--primary-color)"></i> {{ __('Filters & Search') }}</span>
                <i class="fas fa-chevron-down opacity-50"></i>
            </button>
        </div>
        
        <div class="collapse d-lg-block" id="mobileInventoryFilters">
            <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-lg-5">
                    <div class="brand-search-wrapper w-100">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control font-inter" 
                               placeholder="{{ __('Search product or SKU...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-6 col-lg-3">
                    <select name="category_id" class="form-select custom-select-premium font-inter">
                        <option value="">{{ __('Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->translated_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-lg-2">
                    <select name="stock_status" class="form-select custom-select-premium font-inter">
                        <option value="">{{ __('Status') }}</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>{{ __('Low') }}</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out') }}</option>
                    </select>
                </div>
                
                <div class="col-12 col-lg-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand-primary flex-grow-1">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-brand-light px-3">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Inventory Cards -->
    <div class="d-lg-none mt-3 px-1">
        @forelse($products as $product)
        <div class="glass-card mb-3 p-3 border-0 shadow-soft product-container" data-product-id="{{ $product->id }}">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="brand-avatar" style="width: 50px; height: 50px; border-radius: 12px; background: #f1f5f9;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    @else
                        <i class="fas fa-box text-muted"></i>
                    @endif
                </div>
                <div class="flex-grow-1 min-width-0">
                    <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $product->translated_name }}</h6>
                    <div class="text-muted small font-inter" style="font-size: 0.7rem;">{{ $product->sku ?? __('NO-SKU') }}</div>
                </div>
                <div class="text-end">
                    @if($product->track_inventory)
                        <div class="fw-bold font-inter fs-5 product-total-stock-display">{{ $product->total_stock }}</div>
                        <div class="text-muted small" style="font-size: 0.65rem;">{{ __('TOTAL') }}</div>
                    @else
                        <span class="badge bg-light text-muted">{{ __('N/A') }}</span>
                    @endif
                </div>
            </div>

            @if($product->track_inventory)
                @if($product->variants->count() > 0)
                    <div class="variant-mobile-list mt-3">
                        <p class="text-uppercase small fw-bold text-muted mb-2" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ __('Product Options') }}</p>
                        @foreach($product->variants as $variant)
                            <div class="variant-card mb-2 d-flex align-items-center justify-content-between variant-container" 
                                 data-product-id="{{ $product->id }}" 
                                 data-variant-id="{{ $variant->id }}">
                                <div class="d-flex align-items-center gap-2 variant-info-wrapper">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($variant->color_image)
                                            <img src="{{ $variant->color_image_url }}" class="variant-thumb" alt="">
                                        @elseif($variant->color)
                                            <span class="color-dot shadow-inner" style="width: 20px; height: 20px; border-radius: 50%; background: {{ $variant->color }}"></span>
                                        @endif
                                        
                                        @if($variant->size)
                                            <span class="text-dark fw-bold font-inter ms-1" style="font-size: 0.85rem;">{{ $variant->size }}</span>
                                        @endif
                                    </div>
                                    <div class="text-muted mx-1">/</div>
                                    <div class="text-muted font-monospace" style="font-size: 0.65rem;">{{ $variant->sku ?? ($product->sku . '-' . ($variant->size ?: $variant->color)) }}</div>
                                </div>
                                <div class="variant-actions-wrapper">
                                    <div class="stock-stepper">
                                        <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', '{{ $variant->id }}', 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <input type="number" 
                                               class="stock-input variant-stock-display {{ ($variant->stock ?? 0) <= 0 ? 'out' : (($variant->stock ?? 0) <= 5 ? 'low' : 'in') }}" 
                                               value="{{ $variant->stock ?? 0 }}"
                                               onchange="saveQuantity('{{ $product->id }}', '{{ $variant->id }}', this)">
                                        <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', '{{ $variant->id }}', -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: #f8fafc;">
                        <span class="small fw-semibold text-muted">{{ __('Direct Stock Management') }}</span>
                        <div class="stock-stepper">
                            <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', null, 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                            <input type="number" 
                                   class="stock-input product-stock-display {{ ($product->stock ?? 0) <= 0 ? 'out' : (($product->stock ?? 0) <= 5 ? 'low' : 'in') }}" 
                                   value="{{ $product->stock ?? 0 }}"
                                   onchange="saveQuantity('{{ $product->id }}', null, this)">
                            <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', null, -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                @endif
            @endif

            <div class="d-flex justify-content-end align-items-center mt-3 pt-2 border-top">
                <div class="d-flex gap-2">
                    @if($product->track_inventory)
                    <button class="btn btn-sm btn-brand-light rounded-pill px-3" onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->translated_name) }}', {{ $product->stock ?? 0 }})">
                        <i class="fas fa-sliders-h me-1"></i> {{ __('Manual') }}
                    </button>
                    @endif
                    <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-history me-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
            <div class="glass-card p-5 text-center">
                <i class="fas fa-boxes opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold">{{ __('No inventory items') }}</h5>
            </div>
        @endforelse
    </div>

    <!-- Inventory Table (Desktop Only) -->
    <div class="brand-table-card d-none d-lg-block mt-4 overflow-hidden">
        <div class="table-responsive" style="max-height: 70vh;">
            <table class="brand-table mb-0">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 50px; padding-left: 1.5rem;"></th>
                        <th>{{ __('Product Details') }}</th>
                        <th class="text-center">{{ __('Stock Level & Quick Actions') }}</th>
                        <th class="text-center">{{ __('30d Vol') }}</th>
                        <th class="text-center">{{ __('Value') }}</th>
                        <th class="text-end" style="padding-right: 1.5rem;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="product-container" data-product-id="{{ $product->id }}">
                        <td style="padding-left: 1.5rem;">
                            @if($product->variants->count() > 0)
                                <button type="button" class="inventory-expand-btn" data-product-id="{{ $product->id }}" onclick="toggleVariants('{{ $product->id }}', this)">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="brand-avatar shadow-xs" style="width: 48px; height: 48px;">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="">
                                    @else
                                        <i class="fas fa-box opacity-50"></i>
                                    @endif
                                </div>
                                <div style="max-width: 250px;">
                                    <div class="fw-bold text-dark text-truncate">{{ $product->translated_name }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.65rem;">{{ $product->sku ?? __('NO-SKU') }}</span>
                                        @if($product->variants->count() > 0)
                                            <span class="badge bg-soft-primary text-primary" style="font-size: 0.6rem;">{{ $product->variants->count() }} {{ __('Variants') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                @if($product->track_inventory)
                                        @if($product->variants->count() == 0)
                                            <div class="stock-stepper">
                                                <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', null, 1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <input type="number" 
                                                       class="stock-input product-total-stock-display {{ ($product->total_stock <= ($product->low_stock_threshold ?? 10)) ? 'out' : 'in' }}" 
                                                       value="{{ $product->total_stock }}"
                                                       onchange="saveQuantity('{{ $product->id }}', null, this)">
                                                <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', null, -1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <div class="fw-bold fs-4 font-inter product-total-stock-display {{ ($product->total_stock <= ($product->low_stock_threshold ?? 10)) ? 'text-danger' : 'text-dark' }}">
                                                    {{ $product->total_stock }}
                                                </div>
                                                <div class="text-muted small text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">{{ __('Total Units') }}</div>
                                            </div>
                                        @endif
                                    
                                    @php
                                        $stock = $product->total_stock;
                                        $threshold = $product->low_stock_threshold ?? 10;
                                        $percent = min(100, $stock > 0 ? ($stock / ($threshold * 4)) * 100 : 0);
                                        $barColor = $stock <= 0 ? '#ef4444' : ($stock <= $threshold ? '#f59e0b' : '#10b981');
                                    @endphp
                                    <div class="progress" style="height: 4px; width: 120px; border-radius: 10px; background: #f1f5f9;">
                                        <div class="progress-bar product-stock-progress-bar" role="progressbar" 
                                             style="width: {{ $percent }}%; background-color: {{ $barColor }};"></div>
                                    </div>
                                @else
                                    <span class="text-muted small italic">{{ __('Inventory not tracked') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($product->sold_last_30_days > 0)
                                <div class="fw-bold font-inter">{{ number_format($product->sold_last_30_days) }}</div>
                                <div class="text-muted small">{{ __('units/mo') }}</div>
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($product->track_inventory && $product->cost_price)
                                <div class="fw-bold text-dark font-inter">{{ currency($product->total_stock * $product->cost_price) }}</div>
                                <div class="text-muted small">{{ currency($product->cost_price) }} {{ __('avg cost') }}</div>
                            @else
                                <span class="text-muted opacity-25">—</span>
                            @endif
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn-action-icon" 
                                        onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->translated_name) }}', {{ $product->total_stock }})"
                                        title="{{ __('Manual Adjustment') }}">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" 
                                   class="btn-action-icon" title="{{ __('History') }}">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    @if($product->variants->count() > 0)
                    <tr class="variant-row" id="variants-{{ $product->id }}">
                        <td colspan="6" class="p-0 border-0">
                            <div class="px-5 py-3" style="background: rgba(99, 102, 241, 0.02);">
                                <div class="row g-3">
                                    @foreach($product->variants as $variant)
                                        <div class="col-md-4 col-xl-3">
                                            <div class="variant-card d-flex align-items-center justify-content-between variant-container" 
                                                 data-product-id="{{ $product->id }}" 
                                                 data-variant-id="{{ $variant->id }}">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($variant->color_image)
                                                            <img src="{{ $variant->color_image_url }}" class="variant-thumb" alt="">
                                                        @elseif($variant->color)
                                                            <span class="color-dot shadow-inner" style="width: 20px; height: 20px; border-radius: 50%; background: {{ $variant->color }}"></span>
                                                        @endif
                                                        
                                                        @if($variant->size)
                                                            <span class="text-dark fw-bold font-inter ms-1" style="font-size: 0.85rem;">{{ $variant->size }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted">/</div>
                                                    <div class="text-muted font-monospace" style="font-size: 0.65rem;">{{ $variant->sku ?? ($product->sku . '-' . ($variant->size ?: $variant->color)) }}</div>
                                                </div>
                                                <div class="stock-stepper">
                                                    <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', '{{ $variant->id }}', 1)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <input type="number" 
                                                           class="stock-input variant-stock-display {{ ($variant->stock ?? 0) <= 0 ? 'out' : (($variant->stock ?? 0) <= 5 ? 'low' : 'in') }}" 
                                                           value="{{ $variant->stock ?? 0 }}"
                                                           onchange="saveQuantity('{{ $product->id }}', '{{ $variant->id }}', this)">
                                                    <button type="button" class="quick-adj-btn" onclick="quickUpdate('{{ $product->id }}', '{{ $variant->id }}', -1)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <h5 class="fw-bold text-dark">{{ __('No products found') }}</h5>
                                <p class="text-muted">{{ __('Try adjusting your search or filter criteria') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $products->links() }}
        </div>
        @endif
    </div>

<!-- Quick Adjust Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="adjustStockForm" method="POST" action="">
            @csrf
            <input type="hidden" name="variant_id" id="modalVariantId">
            <div class="modal-content glass-card shadow-lg" style="border: none;">
                <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0;">
                    <h5 class="modal-title fw-bold text-dark">{{ __('Manual Adjustment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="p-3 mb-4 d-flex align-items-center gap-3" style="background: #f0f9ff; border-radius: var(--radius-lg);">
                        <div class="brand-avatar" style="background: var(--primary-color); color: white;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark" id="modalProductName" style="font-size: 1rem;"></div>
                            <div id="modalVariantName" class="badge bg-soft-primary text-primary mt-1" style="display: none;"></div>
                            <div class="text-primary small fw-semibold mt-1">
                                {{ __('Current Level:') }} <span id="modalCurrentStock" class="font-inter"></span> {{ __('units') }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Adjustment Type') }}</label>
                        <select name="adjustment_type" class="form-select custom-select-premium" required onchange="updateReasonPlaceholder(this.value)">
                            <option value="in">➕ {{ __('Stock In') }}</option>
                            <option value="out">➖ {{ __('Stock Out') }}</option>
                            <option value="adjustment">🔄 {{ __('Correct Absolute Value') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Quantity') }}</label>
                        <input type="number" name="quantity" class="form-control brand-input font-inter" required min="0" placeholder="0">
                        <div class="form-text mt-1 small opacity-75" id="quantityHelp">{{ __('Units to ADD to current stock.') }}</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Note / Reason') }}</label>
                        <textarea name="reason" class="form-control brand-input" rows="2" required placeholder="{{ __('e.g., Weekly restocking') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding: 0 1.5rem 1.5rem;">
                    <button type="submit" class="btn btn-brand-primary w-100 py-3 fw-bold">{{ __('Apply Adjustment') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleVariants(productId, badge) {
        const row = document.getElementById('variants-' + productId);
        if (row.classList.contains('show')) {
            row.classList.remove('show');
            badge.classList.remove('active');
        } else {
            row.classList.add('show');
            badge.classList.add('active');
        }
    }

    function openAdjustModal(productId, productName, currentStock, variantId = null, variantName = null) {
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalCurrentStock').textContent = currentStock;
        document.getElementById('modalVariantId').value = variantId;
        
        const vn = document.getElementById('modalVariantName');
        if (variantName) {
            vn.textContent = variantName;
            vn.style.display = 'inline-block';
        } else {
            vn.style.display = 'none';
        }
        
        const form = document.getElementById('adjustStockForm');
        form.action = `/inventory/${productId}/adjust`;
        
        new bootstrap.Modal(document.getElementById('adjustStockModal')).show();
    }

    function updateReasonPlaceholder(type) {
        const textarea = document.querySelector('textarea[name="reason"]');
        const quantityHelp = document.getElementById('quantityHelp');
        
        switch(type) {
            case 'in':
                textarea.placeholder = "{{ __('e.g., Restocked from supplier') }}";
                quantityHelp.textContent = "{{ __('Units to ADD to current stock.') }}";
                break;
            case 'out':
                textarea.placeholder = "{{ __('e.g., Damaged item, expired') }}";
                quantityHelp.textContent = "{{ __('Units to REMOVE from current stock.') }}";
                break;
            case 'adjustment':
                textarea.placeholder = "{{ __('e.g., Physical audit correction') }}";
                quantityHelp.textContent = "{{ __('The NEW absolute total quantity for this item.') }}";
                break;
        }
    }

    // AJAX Quick Update
    // AJAX Quick Update (Relative)
    async function quickUpdate(productId, variantId, change) {
        const btn = event.currentTarget;
        btn.disabled = true;
        
        // Find the adjacent input to update local value immediately
        const container = btn.closest('.stock-stepper');
        const input = container.querySelector('.stock-input');
        const originalVal = parseInt(input.value);
        const newVal = Math.max(0, originalVal + change);
        input.value = newVal;

        try {
            const response = await fetch("{{ route('inventory.quick-update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    change: change
                })
            });

            const data = await response.json();
            if (data.success) {
                updateUI(productId, variantId, data);
            } else {
                input.value = originalVal; // Revert
                alert(data.message || "{{ __('Update failed') }}");
            }
        } catch (error) {
            input.value = originalVal; // Revert
            console.error(error);
            alert("{{ __('Something went wrong') }}");
        } finally {
            btn.disabled = false;
        }
    }

    // AJAX Save Quantity (Absolute)
    async function saveQuantity(productId, variantId, input) {
        const newVal = parseInt(input.value);
        if (isNaN(newVal) || newVal < 0) {
            alert("{{ __('Please enter a valid positive number') }}");
            return;
        }

        input.disabled = true;
        input.style.opacity = '0.5';

        try {
            const response = await fetch("{{ route('inventory.quick-update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_id: variantId,
                    new_quantity: newVal
                })
            });

            const data = await response.json();
            if (data.success) {
                updateUI(productId, variantId, data);
            } else {
                alert(data.message || "{{ __('Update failed') }}");
                // Revert to data state if possible or reload
            }
        } catch (error) {
            console.error(error);
            alert("{{ __('Something went wrong') }}");
        } finally {
            input.disabled = false;
            input.style.opacity = '1';
        }
    }

    function updateUI(productId, variantId, data) {
        // Find all containers that might need updating
        const selectors = variantId 
            ? `.variant-stock-display[onchange*="'${variantId}'"]`
            : `.product-stock-display[onchange*="'${productId}'"], .product-total-stock-display[onchange*="'${productId}'"]`;
        
        document.querySelectorAll(selectors).forEach(el => {
            if (el.tagName === 'INPUT') {
                el.value = data.new_stock;
                // Update color classes
                el.classList.remove('in', 'low', 'out');
                if (data.new_stock <= 0) el.classList.add('out');
                else if (data.new_stock <= 5) el.classList.add('low');
                else el.classList.add('in');
            } else {
                el.textContent = data.new_stock;
                el.classList.toggle('text-danger', data.new_stock <= 5);
            }
        });

        // Update the non-editable total display if it exists
        document.querySelectorAll(`.product-container[data-product-id="${productId}"]`).forEach(container => {
            const totalDisp = container.querySelector('.product-total-stock-display');
            if (totalDisp) {
                if (totalDisp.tagName === 'INPUT') {
                    totalDisp.value = data.total_stock;
                    totalDisp.classList.toggle('out', data.total_stock <= 5);
                } else {
                    totalDisp.textContent = data.total_stock;
                    totalDisp.classList.toggle('text-danger', data.total_stock <= 5);
                }
            }
            
            const progressBar = container.querySelector('.product-stock-progress-bar');
            if (progressBar) {
                const percent = Math.min(100, (data.total_stock / 40) * 100);
                progressBar.style.width = percent + '%';
                progressBar.style.backgroundColor = data.total_stock <= 0 ? '#ef4444' : (data.total_stock <= 10 ? '#f59e0b' : '#10b981');
            }
        });
        
        // Success feedback
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 show position-fixed bottom-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `<div class="d-flex"><div class="toast-body"><i class="fas fa-check-circle me-2"></i> ${"{{ __('Stock updated successfully') }}"}</div></div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }
</script>
@endpush
@endsection
