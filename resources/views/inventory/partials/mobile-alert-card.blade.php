@php
    $isOutOfStock = ($alert->alert_type === 'out_of_stock');
    $isAcknowledged = (bool)$alert->acknowledged_at;
@endphp

<div class="mobile-alert-card mb-3 p-3 bg-white border {{ !$isAcknowledged ? 'border-warning-subtle shadow-sm' : 'border-light' }} rounded-3">
    <!-- Top Header: Severity & State -->
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
        <div>
            @if($isOutOfStock)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
                    <i class="fas fa-times-circle"></i> Rupture de stock
                </span>
            @else
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle d-inline-flex align-items-center gap-1.5 px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i> Stock faible
                </span>
            @endif
        </div>
        <div>
            @if($isAcknowledged)
                <span class="badge bg-success-subtle text-success px-2 py-0.5" style="font-size: 0.7rem;">
                    <i class="fas fa-check me-1"></i>Traitée
                </span>
            @else
                <span class="badge bg-warning text-dark px-2 py-0.5" style="font-size: 0.7rem;">
                    <i class="fas fa-clock me-1"></i>En attente
                </span>
            @endif
        </div>
    </div>

    <!-- Product Details -->
    <div class="d-flex align-items-center gap-2.5 mb-2.5">
        @if($alert->product)
            <div class="mobile-inv-avatar flex-shrink-0" style="width: 44px; height: 44px;">
                <img src="{{ $alert->product->thumbnail }}" 
                     alt="{{ $alert->product->name }}" 
                     loading="lazy"
                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
            </div>
            <div class="min-w-0 flex-grow-1">
                <a href="{{ route('products.edit', $alert->product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.88rem;">
                    {{ $alert->product->name }}
                </a>
                <div class="d-flex align-items-center gap-2 mt-0.5">
                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                        {{ $alert->product->sku ?? 'SANS-SKU' }}
                    </span>
                    <span class="text-muted small" style="font-size: 0.7rem;">
                        <i class="far fa-clock me-0.5 opacity-75"></i>{{ $alert->triggered_at->diffForHumans() }}
                    </span>
                </div>
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

    <!-- Stock level vs threshold metrics -->
    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between mb-2.5">
        <div>
            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Stock au déclenchement</div>
            <div class="fw-bold {{ $alert->current_stock <= 0 ? 'text-danger' : 'text-warning' }} fs-6">
                {{ $alert->current_stock }} <span class="small fw-normal text-muted" style="font-size: 0.7rem;">unité(s)</span>
            </div>
        </div>
        <div class="text-end">
            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.04em;">Seuil d'alerte</div>
            <div class="fw-bold text-dark fs-6">
                {{ $alert->threshold_value }} <span class="small fw-normal text-muted" style="font-size: 0.7rem;">unité(s)</span>
            </div>
        </div>
    </div>

    <!-- Acknowledged info if processed -->
    @if($isAcknowledged && $alert->acknowledgedBy)
    <div class="p-2 rounded-2 bg-success-subtle border border-success-subtle mb-2.5 small text-success" style="font-size: 0.72rem;">
        <i class="fas fa-check-circle me-1"></i> Traitée par <strong>{{ $alert->acknowledgedBy->name }}</strong> le {{ $alert->acknowledged_at->format('d/m/Y H:i') }}
        @if($alert->notes)
            <div class="mt-1 text-dark fst-italic">« {{ $alert->notes }} »</div>
        @endif
    </div>
    @endif

    <!-- Actions -->
    <div class="d-flex align-items-center gap-2 pt-2 border-top">
        @if(!$isAcknowledged)
        <form action="{{ route('inventory.acknowledge-alert', $alert) }}" method="POST" class="flex-grow-1 m-0">
            @csrf
            <button type="submit" class="btn btn-outline-success btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1.5 py-1.5" style="font-size: 0.78rem; border-radius: 8px;">
                <i class="fas fa-check"></i> <span>Marquer traitée</span>
            </button>
        </form>
        @endif

        @if($alert->product)
        <a href="{{ route('inventory.adjust', $alert->product) }}" class="btn btn-primary btn-sm flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1.5 py-1.5 text-white" style="font-size: 0.78rem; border-radius: 8px; background: #2563eb; border-color: #2563eb;">
            <i class="fas fa-sliders-h"></i> <span>Réapprovisionner</span>
        </a>
        @endif
    </div>
</div>
