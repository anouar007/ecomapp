@php
    $typeColor = match($movement->type) {
        'in' => 'success',
        'out' => 'danger',
        'adjustment' => 'warning',
        'transfer' => 'info',
        'return' => 'primary',
        default => 'secondary'
    };

    $typeLabel = match($movement->type) {
        'in' => 'Entrée de stock',
        'out' => 'Sortie de stock',
        'adjustment' => 'Ajustement',
        'transfer' => 'Transfert',
        'return' => 'Retour client',
        default => ucfirst($movement->type)
    };

    $typeIcon = match($movement->type) {
        'in' => 'fa-arrow-down',
        'out' => 'fa-arrow-up',
        'adjustment' => 'fa-sliders-h',
        'transfer' => 'fa-exchange-alt',
        'return' => 'fa-undo',
        default => 'fa-circle'
    };

    $qtyPrefix = ($movement->quantity > 0 && in_array($movement->type, ['in', 'return'])) ? '+' : (($movement->type === 'out') ? '-' : '');
    $qtyColor = ($movement->type === 'in' || $movement->type === 'return') ? '#10b981' : (($movement->type === 'out') ? '#ef4444' : '#2563eb');
@endphp

<div class="mobile-movement-card mb-3 p-3 bg-white border rounded-3 shadow-sm">
    <!-- Top Header: Type Pill & Timestamp -->
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
        <span class="badge bg-{{ $typeColor }}-subtle text-{{ $typeColor }} border border-{{ $typeColor }}-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
            <i class="fas {{ $typeIcon }}"></i>
            <span>{{ $typeLabel }}</span>
        </span>
        <span class="text-muted small" style="font-size: 0.72rem;">
            <i class="far fa-clock me-1 opacity-75"></i>{{ $movement->created_at->format('d/m/Y') }} à {{ $movement->created_at->format('H:i') }}
        </span>
    </div>

    <!-- Product Info -->
    <div class="d-flex align-items-center gap-2.5 mb-2.5">
        @if($movement->product)
            <div class="mobile-inv-avatar flex-shrink-0" style="width: 44px; height: 44px;">
                <img src="{{ $movement->product->thumbnail }}" 
                     alt="{{ $movement->product->name }}" 
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
            </div>
            <div class="min-w-0 flex-grow-1">
                <a href="{{ route('products.edit', $movement->product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.88rem;">
                    {{ $movement->product->name }}
                </a>
                <span class="badge bg-light text-secondary border font-monospace mt-0.5" style="font-size: 0.65rem;">
                    {{ $movement->product->sku ?? 'SANS-SKU' }}
                </span>
            </div>
        @else
            <div class="mobile-inv-avatar flex-shrink-0 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 44px; height: 44px;">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="min-w-0 flex-grow-1">
                <span class="text-muted italic">Produit supprimé</span>
            </div>
        @endif
    </div>

    <!-- Movement Quantities: Before -> After -->
    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between mb-2">
        <div>
            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Variation</div>
            <div class="fw-extrabold fs-6" style="color: {{ $qtyColor }};">
                {{ $qtyPrefix }}{{ $movement->quantity }} <span class="small fw-normal text-muted" style="font-size: 0.7rem;">unité(s)</span>
            </div>
        </div>
        <div class="text-end">
            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Niveau de Stock</div>
            <div class="d-flex align-items-center gap-1.5 justify-content-end mt-0.5">
                <span class="badge bg-secondary-subtle text-secondary px-2 py-0.5" style="font-size: 0.75rem;">{{ $movement->stock_before ?? 0 }}</span>
                <i class="fas fa-arrow-right text-muted opacity-50" style="font-size: 0.65rem;"></i>
                <span class="badge bg-primary text-white px-2 py-0.5" style="font-size: 0.75rem;">{{ $movement->stock_after ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Reason & Reference -->
    @if($movement->reason || ($movement->reference_type && $movement->reference_id))
    <div class="small text-muted mb-2" style="font-size: 0.75rem;">
        @if($movement->reason)
            <div class="text-dark"><i class="fas fa-comment-alt text-secondary opacity-50 me-1"></i>{{ $movement->reason }}</div>
        @endif
        @if($movement->reference_type && $movement->reference_id)
            <div class="mt-0.5 text-muted">
                <i class="fas fa-receipt me-1 opacity-75"></i>Ref : <code>{{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}</code>
            </div>
        @endif
    </div>
    @endif

    <!-- Operator -->
    <div class="d-flex align-items-center justify-content-between pt-2 border-top text-muted small" style="font-size: 0.72rem;">
        <span><i class="fas fa-user-circle me-1 opacity-75"></i>Opérateur :</span>
        <span class="fw-semibold text-dark">{{ $movement->creator->name ?? 'Système' }}</span>
    </div>
</div>
