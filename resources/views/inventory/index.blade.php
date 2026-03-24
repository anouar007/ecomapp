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
            <div class="brand-stat-value font-inter">{{ number_format($stats['total_products']) }}</div>
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

    <!-- Mobile Inventory Cards -->
    <div class="d-lg-none mt-3 px-1">
        @forelse($products as $product)
        <div class="glass-card mb-3 p-3 border-0 shadow-soft">
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
                    <div class="text-muted small font-inter" style="font-size: 0.7rem;">{{ $product->sku ?? 'NO-SKU' }}</div>
                </div>
                <div class="text-end">
                    @if($product->track_inventory)
                        <div class="fw-bold font-inter fs-5">{{ $product->stock ?? 0 }}</div>
                        <div class="text-muted small" style="font-size: 0.65rem;">{{ __('UNITS') }}</div>
                    @else
                        <span class="badge bg-light text-muted">{{ __('N/A') }}</span>
                    @endif
                </div>
            </div>

            @if($product->track_inventory)
            <div class="row g-2 mb-3 py-2 border-top border-bottom" style="border-style: dashed !important; border-color: #f1f5f9 !important;">
                <div class="col-6">
                    <div class="text-muted small mb-1">{{ __('Stock Status') }}</div>
                    @php
                        $stock = $product->stock ?? 0;
                        $threshold = $product->low_stock_threshold ?? 10;
                        $badgeClass = $stock <= 0 ? 'danger' : ($stock <= $threshold ? 'warning' : 'success');
                        $badgeText = $stock <= 0 ? __('Out') : ($stock <= $threshold ? __('Low') : __('In Stock'));
                    @endphp
                    <span class="brand-badge {{ $badgeClass }}" style="font-size: 0.65rem; padding: 2px 8px;">{{ $badgeText }}</span>
                </div>
                <div class="col-6 text-end">
                    <div class="text-muted small mb-1">{{ __('Valuation') }}</div>
                    <div class="fw-bold text-dark font-inter" style="font-size: 0.85rem;">{{ currency(($product->stock ?? 0) * ($product->cost_price ?? 0)) }}</div>
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div class="text-muted" style="font-size: 0.65rem;">{{ __('30D VOL') }}</div>
                        <div class="fw-bold font-inter small">{{ number_format($product->sold_last_30_days ?? 0) }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($product->track_inventory)
                    <button class="btn btn-sm btn-brand-primary rounded-pill px-3" onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->translated_name) }}', {{ $product->stock ?? 0 }})">
                        <i class="fas fa-sliders-h me-1"></i> {{ __('Adjust') }}
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
    <div class="brand-table-card d-none d-lg-block mt-4">
        <div class="table-responsive" style="max-height: 65vh;">
            <table class="brand-table">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 40px; padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Stock Level') }}</th>
                        <th class="text-center">{{ __('30d Sales') }}</th>
                        <th class="text-center">{{ __('Forecasting') }}</th>
                        <th class="text-center">{{ __('Reorder Pt') }}</th>
                        <th>{{ __('Value') }}</th>
                        <th class="text-end" style="padding-right: 1.5rem;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input product-check" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="brand-avatar">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="">
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $product->translated_name }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.65rem;">{{ $product->sku ?? 'NO-SKU' }}</span>
                                        <span class="text-muted small">•</span>
                                        <span class="text-muted small">{{ $product->category->name ?? __('Uncategorized') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($product->track_inventory)
                                <div class="d-flex flex-column gap-2" style="min-width: 120px;">
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $stock = $product->stock ?? 0;
                                            $threshold = $product->low_stock_threshold ?? 10;
                                            $badgeClass = 'success';
                                            $badgeText = __('In Stock');
                                            $barClass = 'success';
                                            if ($stock <= 0) {
                                                $badgeClass = 'danger';
                                                $badgeText = __('Out');
                                                $barClass = 'danger';
                                            } elseif ($stock <= $threshold) {
                                                $badgeClass = 'warning';
                                                $badgeText = __('Low');
                                                $barClass = 'warning';
                                            }
                                            $percent = min(100, $stock > 0 ? ($stock / ($threshold * 3)) * 100 : 0);
                                        @endphp
                                        <span class="fw-bold fs-6">{{ $stock }}</span>
                                        <span class="brand-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px; border-radius: 10px; background: #f1f5f9; width: 100px;">
                                        <div class="progress-bar bg-{{ $barClass === 'success' ? 'success' : ($barClass === 'warning' ? 'warning' : 'danger') }}" 
                                             role="progressbar" style="width: {{ $percent }}%; border-radius: 10px;"></div>
                                    </div>
                                </div>
                            @else
                                <span class="brand-badge" style="background: #f1f5f9; color: #94a3b8;">{{ __('Not Tracked') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($product->sold_last_30_days > 0)
                                <div class="fw-bold">{{ number_format($product->sold_last_30_days) }}</div>
                                <div class="text-muted small">{{ __('units/mo') }}</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $daysText = '—';
                                $badgeType = '';
                                if ($product->track_inventory && $product->stock > 0 && $product->sold_last_30_days > 0) {
                                    $dailyVelocity = $product->sold_last_30_days / 30;
                                    $daysCalc = round($product->stock / $dailyVelocity);
                                    if ($daysCalc > 365) {
                                        $daysText = '> 1 yr';
                                        $badgeType = 'success';
                                    } elseif ($daysCalc > 30) {
                                        $daysText = $daysCalc . ' days';
                                        $badgeType = 'success';
                                    } elseif ($daysCalc > 7) {
                                        $daysText = $daysCalc . ' days';
                                        $badgeType = 'warning';
                                    } else {
                                        $daysText = $daysCalc . ' days';
                                        $badgeType = 'danger';
                                    }
                                } elseif ($product->track_inventory && $product->stock <= 0) {
                                    $daysText = '0 days';
                                    $badgeType = 'danger';
                                }
                            @endphp
                            @if($badgeType)
                                <span class="brand-badge {{ $badgeType }}" style="font-size: 0.75rem;">
                                    <i class="fas fa-hourglass-half me-1"></i> {{ $daysText }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $reorderPt = '—';
                                $reorderClass = '';
                                if ($product->track_inventory && $product->sold_last_30_days > 0) {
                                    $avgDailySales = $product->sold_last_30_days / 30;
                                    $leadTime = $product->lead_time_days ?? 7;
                                    $safetyStock = $product->safety_stock ?? 5;
                                    $reorderPt = ceil(($avgDailySales * $leadTime) + $safetyStock);
                                    if (($product->stock ?? 0) <= $reorderPt) {
                                        $reorderClass = 'text-danger fw-bold';
                                    }
                                }
                            @endphp
                            <span class="{{ $reorderClass }}">{{ $reorderPt }}</span>
                        </td>
                        <td>
                            @if($product->track_inventory && $product->cost_price)
                                <div class="fw-bold text-dark">{{ currency(($product->stock ?? 0) * $product->cost_price) }}</div>
                                <div class="text-muted small">{{ currency($product->cost_price) }} / unit</div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                @if($product->track_inventory)
                                <button type="button" class="btn-action-icon" 
                                        onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->translated_name) }}', {{ $product->stock ?? 0 }})"
                                        title="Adjust Stock">
                                    <i class="fas fa-sliders-h"></i>
                                </button>
                                @endif
                                <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" 
                                   class="btn-action-icon" title="View History">
                                    <i class="fas fa-history"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
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
            <div class="modal-content glass-card shadow-lg" style="border: none;">
                <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0;">
                    <h5 class="modal-title fw-bold text-dark">{{ __('Stock Adjustment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="p-3 mb-4 d-flex align-items-center gap-3" style="background: #f0f9ff; border-radius: var(--radius-lg);">
                        <div class="brand-avatar" style="background: var(--primary-color); color: white;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" id="modalProductName" style="font-size: 1rem;"></div>
                            <div class="text-primary small fw-semibold">{{ __('Current Level:') }} <span id="modalCurrentStock" class="font-inter"></span> {{ __('units') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Adjustment Type') }}</label>
                        <select name="adjustment_type" class="form-select custom-select-premium" required onchange="updateReasonPlaceholder(this.value)">
                            <option value="in">➕ {{ __('Add units') }}</option>
                            <option value="out">➖ {{ __('Remove units') }}</option>
                            <option value="adjustment">🔄 {{ __('Manual Correction') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Quantity') }}</label>
                        <input type="number" name="quantity" class="form-control brand-input font-inter" required min="1" placeholder="0">
                        <div class="form-text mt-1 small opacity-75" id="quantityHelp">{{ __('Total units to be added.') }}</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-uppercase text-muted">{{ __('Reference/Reason') }}</label>
                        <textarea name="reason" class="form-control brand-input" rows="2" required placeholder="{{ __('e.g., Weekly restocking') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding: 0 1.5rem 1.5rem;">
                    <button type="submit" class="btn btn-brand-primary w-100 py-3 fw-bold">{{ __('Confirm Adjustment') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAdjustModal(productId, productName, currentStock) {
        document.getElementById('modalProductName').textContent = productName;
        document.getElementById('modalCurrentStock').textContent = currentStock;
        
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
                quantityHelp.textContent = "{{ __('Units to ADD to stock.') }}";
                break;
            case 'out':
                textarea.placeholder = "{{ __('e.g., Damaged item, expired') }}";
                quantityHelp.textContent = "{{ __('Units to REMOVE from stock.') }}";
                break;
            case 'adjustment':
                textarea.placeholder = "{{ __('e.g., Physical audit adjustment') }}";
                quantityHelp.textContent = "{{ __('The NEW absolute total quantity.') }}";
                break;
        }
    }
</script>
@endpush
@endsection
