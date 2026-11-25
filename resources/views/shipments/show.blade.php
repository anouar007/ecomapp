@extends('layouts.app')

@section('title', 'Détail de l\'Arrivage ' . $shipment->reference . ' (Admin)')

@section('content')
<div class="container-fluid py-4">

    <!-- Top Executive Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.35rem;">
                        <i class="fas fa-boxes-packing text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge {{ $shipment->status_badge_class }} rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                {{ $shipment->status_label }}
                            </span>
                            <span class="text-white-50 small">• Réception du {{ $shipment->received_date->format('d/m/Y') }}</span>
                            @if($shipment->store)
                                <span class="badge store-badge-themed rounded-pill px-2 py-0.5" style="font-size: 0.68rem; {{ $shipment->store->badge_style }}">
                                    <i class="fas fa-warehouse me-1"></i>{{ $shipment->store->name }}
                                </span>
                            @endif
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0 font-monospace">{{ $shipment->reference }}</h1>
                        <p class="text-white-50 small mb-0 mt-1">Fournisseur : <strong>{{ $shipment->supplier_name }}</strong> @if($shipment->invoice_number) • Facture N° : <strong>{{ $shipment->invoice_number }}</strong> @endif</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('shipments.index') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-arrow-left me-1.5"></i> Tous les Arrivages
                    </a>
                    <a href="{{ route('inventory.arrivals', ['search' => $shipment->reference]) }}" class="btn btn-outline-warning rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-layer-group me-1.5"></i> Suivi FIFO global
                    </a>
                    <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs text-dark" onclick="window.print()" style="font-size: 0.85rem;">
                        <i class="fas fa-print me-1.5"></i> Imprimer le Bon
                    </button>
                </div>
            </div>
        </div>
    </div>

    @php $fifoSummary = $shipment->fifo_summary; @endphp

    @if(!empty($fifoSummary['has_batches']))
    <!-- Suivi FIFO & Bénéfices Réels de cet Arrivage -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="border-left: 4px solid #0f172a !important;">
        <div class="card-body p-3.5">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-dark"><i class="fas fa-layer-group text-primary me-1.5"></i>Suivi FIFO &amp; Bénéfices Réels</span>
                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1" style="font-size: 0.74rem; font-weight: 700 !important;">
                        {{ $fifoSummary['status_label'] }}
                    </span>
                </div>
                <div class="text-muted small">
                    <i class="far fa-clock me-1"></i>Durée d'écoulement : <strong class="text-dark">{{ $fifoSummary['sell_duration_label'] }}</strong>
                </div>
            </div>
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center justify-content-between small mb-1">
                        <span class="text-muted">Progression FIFO :</span>
                        <span class="fw-bold text-dark">{{ $fifoSummary['sold_units'] }} / {{ $fifoSummary['initial_units'] }} vendu(s) ({{ $fifoSummary['sold_percent'] }}%)</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 999px;">
                        <div class="progress-bar {{ $fifoSummary['status'] === 'sold_out' ? 'bg-secondary' : ($fifoSummary['status'] === 'selling' ? 'bg-primary' : 'bg-success') }}"
                             style="width: {{ min(100, $fifoSummary['sold_percent']) }}%"></div>
                    </div>
                    <div class="small text-muted mt-1">Stock restant sur cet arrivage : <strong class="text-dark">{{ $fifoSummary['remaining_units'] }} u.</strong></div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="p-2.5 rounded-3 bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 0.65rem;">CA Réalisé</div>
                        <div class="fw-bold text-dark">{{ currency($fifoSummary['realized_revenue']) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 rounded-3 border text-center" style="background: #ecfdf5; border-color: #a7f3d0 !important;">
                        <div class="text-muted text-uppercase" style="font-size: 0.65rem;">Bénéfice Réalisé FIFO</div>
                        <div class="fw-bold {{ $fifoSummary['realized_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $fifoSummary['realized_profit'] >= 0 ? '+' : '' }}{{ currency($fifoSummary['realized_profit']) }}
                            <span class="small fw-normal">({{ $fifoSummary['realized_margin_pct'] }}%)</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-2.5 rounded-3 bg-light border text-center">
                        <div class="text-muted text-uppercase" style="font-size: 0.65rem;">Potentiel sur Restant</div>
                        <div class="fw-bold {{ $fifoSummary['estimated_remaining_profit'] >= 0 ? 'text-primary' : 'text-danger' }}">{{ $fifoSummary['estimated_remaining_profit'] >= 0 ? '+' : '' }}{{ currency($fifoSummary['estimated_remaining_profit']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 4 Financial Breakdown Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Coût Net Marchandises -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Coût Net Marchandises</span>
                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-dark mb-1">{{ currency($shipment->total_items_cost) }}</div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>{{ $shipment->items->count() }} référence(s) • {{ $shipment->total_units }} unité(s)</span>
                </div>
            </div>
        </div>

        <!-- 2. Frais d'Approche (Douane & Fret) -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Frais d'Approche</span>
                    <div class="rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-plane-departure"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-info mb-1">{{ currency($shipment->total_extra_fees) }}</div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.72rem;">
                    <span>Fret: {{ currency($shipment->shipping_cost) }} • Douane: {{ currency($shipment->customs_cost) }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Coût Global Rendu -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 text-white" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Coût Global Rendu</span>
                    <div class="rounded-3 bg-white text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-sack-dollar"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-white mb-1">{{ currency($shipment->total_landed_cost) }}</div>
                <div class="pt-2 border-top mt-2 text-white-50 small" style="font-size: 0.74rem;">
                    <span>Coût de revient total des stocks de cet arrivage</span>
                </div>
            </div>
        </div>

        <!-- 4. Marge Moyenne Projetée -->
        <div class="col-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Marge Moyenne Projetée</span>
                    <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
                <div class="h3 fw-bold text-success mb-1">{{ $shipment->average_margin }}%</div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>Marge brute sur prix public actuel</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Details Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">Détail des Coûts par Produit pour cet Arrivage ({{ $shipment->items->count() }})</h6>
            <div class="text-muted small">
                @if($shipment->stock_applied)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                        <i class="fas fa-check me-1"></i> Stock physique incrémenté
                    </span>
                @endif
                @if($shipment->update_catalog_costs)
                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 ms-1">
                        <i class="fas fa-sync me-1"></i> Coût catalogue synchronisé
                    </span>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 24%;">Article</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 7%;">Qté</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 11%;">Achat Fourn.</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 10%;">Frais / pc</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 11%;">Coût Rendu</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 11%;">Total Coût</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 14%;">Écoulement FIFO</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Bénéfice &amp; Temps</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($shipment->items as $item)
                    @php $batch = $item->stockBatch; @endphp
                    <tr class="hover-bg-light">
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="{{ $item->product->thumbnail }}" alt="{{ $item->product->name }}" 
                                     class="shadow-xs" style="width: 40px; height: 40px; object-fit: contain; background: #fff; padding: 2px; border-radius: 8px; border: 1px solid #e2e8f0;"
                                     onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 240px;" title="{{ $item->product->name }}">
                                        {{ $item->product->name }}
                                    </div>
                                    <div class="text-muted small d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.72rem;">
                                        <span>SKU: <span class="font-monospace text-secondary">{{ $item->product->sku ?? 'SANS SKU' }}</span></span>
                                        @if($batch)
                                            <a href="{{ route('inventory.arrivals', ['product_id' => $item->product_id]) }}" class="text-primary text-decoration-none">
                                                · File FIFO <i class="fas fa-arrow-up-right-from-square" style="font-size: 0.62rem;"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-center fw-bold fs-6">{{ $item->quantity }}</td>
                        <td class="py-3 text-end fw-semibold text-dark">{{ currency($item->unit_cost) }}</td>
                        <td class="py-3 text-end text-muted small">+{{ currency($item->allocated_fees_per_unit) }}</td>
                        <td class="py-3 text-end">
                            <span class="fw-bold fs-6 text-dark">{{ currency($item->landed_unit_cost) }}</span>
                        </td>
                        <td class="py-3 text-end fw-bold text-dark">{{ currency($item->subtotal_cost) }}</td>
                        <td class="py-3">
                            @if($batch)
                                <div class="mb-1">
                                    <span class="badge {{ $batch->status === 'sold_out' ? 'bg-secondary-subtle text-secondary' : ($batch->status === 'selling' ? 'bg-primary-subtle text-primary' : ($batch->status === 'active' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning')) }} rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        {{ $batch->status_label }}
                                    </span>
                                </div>
                                <div class="small text-dark" style="font-size: 0.74rem;">
                                    <strong>{{ $batch->sold_quantity }}</strong>/{{ $batch->initial_quantity }} vendu · Reste <strong>{{ $batch->remaining_quantity }}</strong>
                                </div>
                            @else
                                <span class="text-muted small">Stock non appliqué</span>
                            @endif
                        </td>
                        <td class="pe-3 py-3 text-end">
                            @if($batch && $batch->sold_quantity > 0)
                                <div class="fw-bold {{ $batch->realized_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $batch->realized_profit >= 0 ? '+' : '' }}{{ currency($batch->realized_profit) }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.69rem;">
                                    <i class="far fa-clock me-0.5"></i>{{ $batch->sell_duration_label }}
                                </div>
                            @else
                                <div class="fw-bold {{ $item->unit_profit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ currency($item->unit_profit) }} / pc
                                </div>
                                <span class="badge {{ $item->margin_percentage >= 30 ? 'bg-success-subtle text-success' : ($item->margin_percentage >= 15 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning') }} rounded-pill px-2 py-0.5 mt-0.5" style="font-size: 0.7rem;">
                                    {{ $item->margin_percentage }}% prévu
                                </span>
                                @if($batch)
                                    <div class="text-muted small mt-0.5" style="font-size: 0.68rem;">{{ $batch->sell_duration_label }}</div>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($shipment->notes)
        <div class="p-3 border-top bg-light">
            <span class="text-muted small fw-bold">Notes sur l'arrivage :</span>
            <p class="mb-0 small text-secondary mt-1">{{ $shipment->notes }}</p>
        </div>
        @endif
    </div>

</div>
@endsection
