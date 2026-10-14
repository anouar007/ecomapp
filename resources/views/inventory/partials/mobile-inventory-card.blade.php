@php
    $stock = $product->stock ?? 0;
    $threshold = $product->low_stock_threshold ?? 10;
    $isTracked = (bool)$product->track_inventory;
    
    // Status badges & colors
    if (!$isTracked) {
        $statusClass = 'secondary';
        $statusLabel = 'Non suivi';
        $statusDot = 'dot-gray';
        $barColor = '#94a3b8';
        $percent = 0;
    } elseif ($stock <= 0) {
        $statusClass = 'danger';
        $statusLabel = 'Rupture';
        $statusDot = 'dot-red';
        $barColor = '#ef4444';
        $percent = 0;
    } elseif ($stock <= $threshold) {
        $statusClass = 'warning';
        $statusLabel = 'Faible';
        $statusDot = 'dot-amber';
        $barColor = '#f59e0b';
        $percent = max(12, min(100, ($stock / max(1, $threshold * 2)) * 100));
    } else {
        $statusClass = 'success';
        $statusLabel = 'En stock';
        $statusDot = 'dot-green';
        $barColor = '#10b981';
        $percent = min(100, ($stock / max(1, $threshold * 3)) * 100);
    }

    // Days sales forecast
    $daysText = '—';
    $badgeType = 'secondary';
    if ($isTracked && $stock > 0 && ($product->sold_last_30_days ?? 0) > 0) {
        $dailyVelocity = $product->sold_last_30_days / 30;
        $daysCalc = round($stock / $dailyVelocity);
        if ($daysCalc > 365) {
            $daysText = '> 1 an';
            $badgeType = 'success';
        } elseif ($daysCalc > 30) {
            $daysText = $daysCalc . ' j';
            $badgeType = 'success';
        } elseif ($daysCalc > 7) {
            $daysText = $daysCalc . ' j';
            $badgeType = 'warning';
        } else {
            $daysText = $daysCalc . ' j';
            $badgeType = 'danger';
        }
    } elseif ($isTracked && $stock <= 0) {
        $daysText = '0 j';
        $badgeType = 'danger';
    }

    $stockVal = ($isTracked && $product->cost_price) ? ($stock * $product->cost_price) : null;
@endphp

<div class="mobile-inventory-card my-2 p-3 bg-white border rounded-3 shadow-sm" id="mobile-inventory-card-{{ $product->id }}">
    <!-- Main Info Row (Thumbnail + Details + Stock Number & Status) -->
    <div class="d-flex align-items-start gap-2">
        <!-- Thumbnail -->
        <div class="mobile-inv-avatar flex-shrink-0" style="width: 48px; height: 48px; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden; background: #f8fafc;">
            <img src="{{ $product->thumbnail }}" 
                 alt="{{ $product->name }}" 
                 loading="lazy"
                 style="width: 100%; height: 100%; object-fit: cover;"
                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
        </div>

        <!-- Center: Title, SKU, Category -->
        <div class="min-w-0 flex-grow-1">
            <a href="{{ route('products.edit', $product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.88rem; line-height: 1.3;" title="{{ $product->name }}">
                {{ $product->name }}
            </a>
            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-0.5">
                <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.64rem;">
                    {{ $product->sku ?? 'SANS-SKU' }}
                </span>
                <span class="text-muted small">•</span>
                <span class="text-muted small text-truncate" style="font-size: 0.7rem; max-width: 130px;">
                    {{ $product->productCategory->name ?? ($product->category->name ?? 'Non classé') }}
                </span>
            </div>
        </div>

        <!-- Right: Stock Pill & Quantity -->
        <div class="text-end flex-shrink-0">
            <div class="d-flex align-items-baseline justify-content-end gap-1">
                <span class="fw-extrabold fs-6 text-dark font-heading" id="mob-stock-val-{{ $product->id }}">{{ $stock }}</span>
                <span class="text-muted small" style="font-size: 0.68rem;">u.</span>
            </div>
            <span class="brand-badge {{ $statusClass }} d-inline-flex align-items-center gap-1 py-0.5 px-1.5 mt-0.5" style="font-size: 0.65rem;">
                <span class="status-dot {{ $statusDot }}" style="width: 5px; height: 5px;"></span>
                <span>{{ $statusLabel }}</span>
            </span>
        </div>
    </div>

    <!-- Slim Progress Bar -->
    @if($isTracked)
    <div class="w-100 my-2" style="background: #f1f5f9; height: 4px; border-radius: 999px; overflow: hidden;">
        <div style="width: {{ $percent }}%; height: 100%; background: {{ $barColor }}; border-radius: 999px; transition: width 0.3s ease;"></div>
    </div>
    @endif

    <!-- Metrics Row (Compact Pill: Seuil, Autonomie, Ventes, Valeur) -->
    <div class="d-flex align-items-center justify-content-between text-muted small py-1.5 px-2 bg-light rounded-2 border my-2" style="font-size: 0.72rem;">
        <div class="d-flex align-items-center gap-1.5 text-truncate">
            <span>Seuil: <strong class="text-secondary">{{ $threshold }}</strong></span>
            @if($daysText !== '—')
                <span class="text-muted opacity-50">•</span>
                <span class="badge bg-{{ $badgeType }}-subtle text-{{ $badgeType }} border px-1.5 py-0.5" style="font-size: 0.65rem;">
                    <i class="fas fa-hourglass-half me-0.5"></i>{{ $daysText }}
                </span>
            @endif
            @if(($product->sold_last_30_days ?? 0) > 0)
                <span class="text-muted opacity-50">•</span>
                <span>{{ number_format($product->sold_last_30_days) }} v.</span>
            @endif
        </div>
        <div class="text-end flex-shrink-0 ps-1">
            @if($stockVal !== null)
                <span class="fw-bold text-dark font-heading">{{ currency($stockVal) }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </div>
    </div>

    <!-- Actions Toolbar (Clean, Pro, Harmonious Buttons) -->
    <div class="d-flex align-items-center justify-content-between pt-1">
        <div class="text-muted small" style="font-size: 0.72rem;">
            @if($product->cost_price)
                <span>Coût : <strong class="text-dark">{{ currency($product->cost_price) }}</strong></span>
            @endif
        </div>

        <div class="mobile-action-btns">
            @if($isTracked)
            <button type="button" 
                    class="mobile-act-btn mobile-act-adjust" 
                    onclick="openAdjustModal('{{ $product->id }}', '{{ addslashes($product->name) }}', {{ $stock }})"
                    title="Ajuster le stock">
                <i class="fas fa-sliders-h"></i>
                <span>Ajuster</span>
            </button>
            @endif

            <a href="{{ route('inventory.movements', ['product_id' => $product->id]) }}" 
               class="mobile-act-btn mobile-act-view" 
               title="Historique des mouvements">
                <i class="fas fa-history"></i>
            </a>

            <a href="{{ route('products.edit', $product) }}" 
               class="mobile-act-btn mobile-act-edit" 
               title="Modifier la fiche produit">
                <i class="fas fa-pencil-alt"></i>
            </a>
        </div>
    </div>
</div>
