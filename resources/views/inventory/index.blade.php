@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                Inventory Management
            </h1>
            <p class="brand-subtitle">Monitor stock levels, track sales velocity, and manage reorder points</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.alerts') }}" class="btn-brand-light">
                <i class="fas fa-bell me-2" style="color: var(--warning-color)"></i>Stock Alerts
            </a>
            <a href="{{ route('inventory.movements') }}" class="btn-brand-light">
                <i class="fas fa-history me-2" style="color: var(--primary-color)"></i>Movements
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="brand-stat-label">Total Products</div>
            <div class="brand-stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-info-circle"></i> Tracked items in inventory
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="brand-stat-label">Low Stock</div>
            <div class="brand-stat-value">{{ number_format($stats['low_stock']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-clock"></i> Items need restocking
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="brand-stat-label">Out of Stock</div>
            <div class="brand-stat-value">{{ number_format($stats['out_of_stock']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-bolt"></i> Immediate action required
            </div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="brand-stat-label">Stock Value</div>
            <div class="brand-stat-value">{{ currency($stats['total_stock_value']) }}</div>
            <div class="brand-stat-desc">
                <i class="fas fa-chart-line"></i> Total inventory worth
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('inventory.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="brand-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by product name or SKU..."
                       value="{{ request('search') }}">
            </div>
            
            <select name="category_id" class="form-select w-auto">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="stock_status" class="form-select w-auto">
                <option value="">All Status</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            
            <button type="submit" class="btn-brand-primary">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('inventory.index') }}" class="btn-brand-light" title="Reset">
                <i class="fas fa-redo"></i>
            </a>
            
            <div class="ms-auto">
                <a href="{{ route('inventory.export', request()->all()) }}" class="btn-brand-outline">
                    <i class="fas fa-download text-primary"></i>
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="brand-table-card">
        <div class="table-responsive" style="max-height: 65vh;">
            <table class="brand-table">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 40px; padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th>Product</th>
                        <th>Stock Level</th>
                        <th class="text-center">30d Sales</th>
                        <th class="text-center">Forecasting</th>
                        <th class="text-center">Reorder Pt</th>
                        <th>Value</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
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
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.65rem;">{{ $product->sku ?? 'NO-SKU' }}</span>
                                        <span class="text-muted small">•</span>
                                        <span class="text-muted small">{{ $product->category->name ?? 'Uncategorized' }}</span>
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
                                            $badgeText = 'In Stock';
                                            $barClass = 'success';
                                            if ($stock <= 0) {
                                                $badgeClass = 'danger';
                                                $badgeText = 'Out';
                                                $barClass = 'danger';
                                            } elseif ($stock <= $threshold) {
                                                $badgeClass = 'warning';
                                                $badgeText = 'Low';
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
                                <span class="brand-badge" style="background: #f1f5f9; color: #94a3b8;">Not Tracked</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($product->sold_last_30_days > 0)
                                <div class="fw-bold">{{ number_format($product->sold_last_30_days) }}</div>
                                <div class="text-muted small">units/mo</div>
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
                                        onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $product->stock ?? 0 }})"
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
                                <h5 class="fw-bold text-dark">No products found</h5>
                                <p class="text-muted">Try adjusting your search or filter criteria</p>
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
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-lg);">
                <div class="modal-header border-0 pb-0" style="padding: 1.5rem 1.5rem 0;">
                    <h5 class="modal-title fw-bold">Stock Adjustment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="p-3 mb-4 d-flex align-items-center gap-3" style="background: #f0f9ff; border-radius: var(--radius-lg);">
                        <div class="brand-avatar" style="background: #0ea5e9; color: white;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" id="modalProductName" style="font-size: 1rem;"></div>
                            <div class="text-primary small fw-semibold">Current Level: <span id="modalCurrentStock"></span> units</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Method</label>
                        <select name="adjustment_type" class="form-select brand-input" required onchange="updateReasonPlaceholder(this.value)" style="border-radius: var(--radius-md);">
                            <option value="in">➕ Add units</option>
                            <option value="out">➖ Remove units</option>
                            <option value="adjustment">🔄 Manual Correction</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Quantity</label>
                        <input type="number" name="quantity" class="form-control brand-input" required min="1" placeholder="0" style="border-radius: var(--radius-md);">
                        <div class="form-text" id="quantityHelp">Total quantity to be added to stock.</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.05em; color: #64748b;">Adjustment Reason</label>
                        <textarea name="reason" class="form-control brand-input" rows="2" required placeholder="e.g., Weekly restocking from supplier" style="border-radius: var(--radius-md);"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding: 0 1.5rem 1.5rem;">
                    <button type="button" class="btn w-100 mb-2 py-3 fw-bold" style="background: var(--gradient-primary); color: white; border-radius: var(--radius-md); border: none;" onclick="this.form.submit()">Confirm Adjustment</button>
                    <button type="button" class="btn btn-link w-100 text-muted text-decoration-none small" data-bs-dismiss="modal">Cancel and go back</button>
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
                textarea.placeholder = "e.g., Restocked from supplier, customer return";
                quantityHelp.textContent = "Number of units to ADD to the current stock level.";
                break;
            case 'out':
                textarea.placeholder = "e.g., Damaged item, expired stock, office use";
                quantityHelp.textContent = "Number of units to REMOVE from the current stock level.";
                break;
            case 'adjustment':
                textarea.placeholder = "e.g., Physical inventory audit, sync fix";
                quantityHelp.textContent = "The final correct absolute number of units in stock.";
                break;
        }
    }
</script>
@endpush
@endsection
