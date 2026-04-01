@extends('layouts.app')

@section('title', __('Order Details - ') . $order->order_number)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
    /* Custom styles for the order timeline */
    .timeline-container {
        padding: 20px 0;
        margin-bottom: 20px;
        position: relative;
    }
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
    }
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 4px;
        background: #f1f5f9;
        z-index: 1;
        border-radius: 4px;
    }
    .timeline-step {
        position: relative;
        z-index: 2;
        text-align: center;
        width: 25%;
    }
    .step-icon {
        width: 32px;
        height: 32px;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 13px;
        color: #94a3b8;
        transition: all 0.3s ease;
    }
    .step-label {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @media (min-width: 768px) {
        .step-icon { width: 38px; height: 38px; font-size: 16px; margin-bottom: 12px; }
        .step-label { font-size: 12px; }
        .timeline-steps::before { top: 18px; }
    }

    /* Active State */
    .timeline-step.active .step-icon {
        background: var(--primary-gradient, linear-gradient(135deg, #6366f1, #4f46e5));
        border-color: transparent;
        color: white;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
    }
    .timeline-step.active .step-label {
        color: #1e293b;
    }
    /* Completed State */
    .timeline-step.completed .step-icon {
        background: var(--success-gradient, linear-gradient(135deg, #10b981, #059669));
        border-color: transparent;
        color: white;
    }
    .timeline-step.completed .step-label {
        color: #059669;
    }
    
    /* Responsive Layout */
    .order-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .order-grid { grid-template-columns: 1fr; }
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-weight: 500; }
    .info-value { color: #1e293b; font-weight: 600; text-align: right; }

    /* Mobile Item Cards */
    .mobile-item-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 12px;
        display: flex;
        gap: 12px;
    }
    .mobile-item-img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
    }
    .brand-badge-select {
        border: none;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.8rem;
        border-radius: 99px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        outline: none;
        appearance: none;
        -webkit-appearance: none;
        text-align: center;
        transition: all 0.3s ease;
    }
    .brand-badge-select.info { background: linear-gradient(135deg, #3b82f6, #2563eb) !important; color: #fff !important; }
    .brand-badge-select.success { background: linear-gradient(135deg, #10b981, #059669) !important; color: #fff !important; }
    .brand-badge-select.warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; color: #fff !important; }
    .brand-badge-select.danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; color: #fff !important; }
    .brand-badge-select option { background: white; color: #333; font-weight: normal; }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="glass-card mb-4 p-3 p-lg-4 border-0 shadow-soft">
    <div class="row align-items-center g-3">
        <div class="col-12 col-lg-auto text-center text-lg-start">
            <a href="{{ route('orders.index') }}" class="btn-action btn-action-view d-inline-flex align-items-center justify-content-center p-0 mb-3 mb-lg-0 me-lg-3" style="width: 40px; height: 40px; border-radius: 12px;">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <div class="col-12 col-lg text-center text-lg-start">
            <div class="d-flex flex-column flex-lg-row align-items-center gap-2 mb-2">
                <h1 class="h3 fw-bold mb-0 text-dark">#{{ $order->order_number }}</h1>
                <select class="brand-badge-select status-update-ajax {{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'info') }}" 
                        data-order-id="{{ $order->id }}"
                        data-update-url="{{ route('orders.update', $order) }}"
                        style="width: 140px;">
                    @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ __(ucfirst($st)) }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-muted small mb-0">
                <i class="far fa-calendar-alt me-1"></i> {{ $order->created_at->format('F d, Y') }} 
                <span class="mx-1 fs-xs opacity-50">•</span>
                <i class="far fa-clock me-1"></i> {{ $order->created_at->format('h:i A') }}
            </p>
        </div>
        <div class="col-12 col-lg-auto">
            <div class="d-flex flex-wrap justify-content-center gap-2">
                    @if($order->invoice)
                    <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-brand-secondary btn-sm px-3 py-2">
                        <i class="fas fa-file-invoice me-2"></i> {{ __('Invoice') }}
                    </a>
                    @endif
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-brand-primary btn-sm px-3 py-2">
                        <i class="fas fa-edit me-2"></i> {{ __('Edit Order') }}
                    </a>
            </div>
        </div>
    </div>
</div>

<div class="order-grid">
    <!-- Main Content Column -->
    <div class="main-column">
        
        <!-- Progress Tracker -->
        <div class="glass-card mb-4 p-3 p-lg-4 border-0 shadow-soft">
            @php
                $steps = ['pending', 'processing', 'shipped', 'delivered'];
                $currentStatusIndex = array_search($order->status, $steps);
                if ($order->status == 'cancelled') $currentStatusIndex = -1;
            @endphp
            
            @if($order->status == 'cancelled')
               <div class="alert alert-danger d-flex align-items-center m-0 border-0 rounded-4 p-3" style="background: #fef2f2;">
                    <div class="brand-avatar bg-danger text-white me-3" style="width: 48px; height: 48px; min-width: 48px;">
                        <i class="fas fa-times"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-danger">{{ __('Order Cancelled') }}</h6>
                        <p class="text-danger small opacity-75 mb-0">{{ __('This order was cancelled on') }} {{ $order->updated_at->format('M d, Y') }}</p>
                    </div>
               </div> 
            @else
            <div class="timeline-container m-0">
                <div class="timeline-steps">
                    @foreach($steps as $index => $step)
                        @php
                            $isCompleted = $index < $currentStatusIndex;
                            $isActive = $index === $currentStatusIndex;
                            $statusClass = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
                            
                            $icons = [
                                'pending' => 'fa-clock',
                                'processing' => 'fa-cog',
                                'shipped' => 'fa-truck',
                                'delivered' => 'fa-check-circle'
                            ];
                        @endphp
                        <div class="timeline-step {{ $statusClass }}">
                            <div class="step-icon">
                                <i class="fas {{ $icons[$step] }}"></i>
                            </div>
                            <div class="step-label">{{ __(ucfirst($step)) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="glass-card mb-4 border-0 shadow-soft overflow-hidden">
            <div class="card-header bg-transparent border-bottom p-4">
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center">
                    <i class="fas fa-box-open me-2 text-primary"></i> 
                    {{ __('Order Items') }} 
                    <span class="badge bg-light text-muted ms-2 px-2 py-1 rounded-pill" style="font-size: 11px;">{{ $order->items->count() }}</span>
                </h5>
            </div>
            
            <!-- Items (Desktop Table) -->
            <div class="table-responsive d-none d-lg-block">
                <table class="brand-table mb-0">
                    <thead>
                        <tr>
                            <th style="padding-left: 24px;">{{ __('Product') }}</th>
                            <th class="text-end">{{ __('Price') }}</th>
                            <th class="text-center">{{ __('Qty') }}</th>
                            <th class="text-end" style="padding-right: 24px;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td style="padding-left: 24px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="product-img-thumb" style="width: 50px; height: 50px; min-width: 50px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                        @if($item->display_image)
                                            <img src="{{ Storage::url($item->display_image) }}" alt="{{ $item->product_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <i class="fas fa-image text-muted opacity-30"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $item->product_name }}</div>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            <span class="text-muted small">#{{ $item->product_sku }}</span>
                                            @if($item->color)
                                                <span class="badge bg-light text-muted border-0 py-1 px-2" style="font-size: 10px;">{{ $item->color }}</span>
                                            @endif
                                            @if($item->size)
                                                <span class="badge bg-light text-muted border-0 py-1 px-2" style="font-size: 10px;">{{ $item->size }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end text-muted">{{ currency($item->price) }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold px-3 py-2" style="border-radius: 8px;">{{ $item->quantity }}</span>
                            </td>
                            <td class="text-end fw-bold text-dark" style="padding-right: 24px;">
                                {{ currency($item->price * $item->quantity) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Items (Mobile Cards) -->
            <div class="d-lg-none p-3">
                @foreach($order->items as $item)
                <div class="mobile-item-card shadow-sm border-0">
                    <div class="mobile-item-img d-flex align-items-center justify-content-center">
                        @if($item->display_image)
                            <img src="{{ Storage::url($item->display_image) }}" class="rounded-3" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fas fa-image opacity-30"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark mb-1 d-flex justify-content-between align-items-start">
                            <span>{{ $item->product_name }}</span>
                            <span class="text-primary small fw-bold">{{ currency($item->price * $item->quantity) }}</span>
                        </div>
                        <div class="text-muted small mb-2 d-flex gap-2">
                            <span>SKU: {{ $item->product_sku }}</span>
                            <span>x{{ $item->quantity }}</span>
                        </div>
                        <div class="d-flex gap-1 flex-wrap">
                            @if($item->color)
                                <span class="badge bg-light text-secondary rounded-pill px-2" style="font-size: 10px;">{{ $item->color }}</span>
                            @endif
                            @if($item->size)
                                <span class="badge bg-light text-secondary rounded-pill px-2" style="font-size: 10px;">{{ $item->size }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Notes Section -->
        @if($order->notes)
        <div class="glass-card mb-4 p-4 border-0 shadow-soft">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                <i class="fas fa-comment-alt me-2 text-warning"></i> {{ __('Order Notes') }}
            </h6>
            <div class="p-3 bg-light rounded-4">
                <i class="fas fa-quote-left text-muted opacity-20 fa-lg me-2"></i>
                <p class="text-muted mb-0 d-inline-block">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar Column -->
    <div class="sidebar-column">
        
        <!-- Order Summary -->
        <div class="glass-card mb-4 border-0 shadow-soft overflow-hidden">
            <div class="card-header bg-transparent border-bottom p-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-calculator me-2 text-primary"></i> {{ __('Summary') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="info-row">
                    <span class="info-label">{{ __('Subtotal') }}</span>
                    <span class="info-value">{{ currency($order->subtotal) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Tax') }}</span>
                    <span class="info-value">{{ currency($order->tax) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('Shipping') }}</span>
                    <span class="info-value">{{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : __('Free') }}</span>
                </div>
                @if($order->discount > 0)
                <div class="info-row">
                    <span class="info-label">{{ __('Discount') }}</span>
                    <span class="info-value text-success">-{{ currency($order->discount) }}</span>
                </div>
                @endif
                
                <div class="mt-4 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h6 fw-bold text-dark mb-0">{{ __('Grand Total') }}</span>
                        <span class="h3 fw-bolder text-primary mb-0">{{ currency($order->total) }}</span>
                    </div>
                    
                    <div class="d-flex flex-column gap-2 mb-2">
                        <label class="text-muted small fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">{{ __('Payment Status') }}</label>
                        <select class="brand-badge-select payment-update-ajax {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning') }} w-100 justify-content-center py-2 fs-6 rounded-4"
                                data-order-id="{{ $order->id }}"
                                data-update-url="{{ route('orders.update', $order) }}">
                            @foreach(['pending', 'paid', 'failed', 'refunded'] as $pst)
                                <option value="{{ $pst }}" {{ $order->payment_status === $pst ? 'selected' : '' }}>{{ __(ucfirst($pst)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="glass-card mb-4 border-0 shadow-soft overflow-hidden">
            <div class="card-header bg-transparent border-bottom p-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-circle me-2 text-primary"></i> {{ __('Customer Details') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="brand-avatar bg-primary text-white fs-4" style="width: 54px; height: 54px; min-width: 54px;">
                        {{ substr($order->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $order->customer_name }}</div>
                        <div class="text-muted small"><i class="fas fa-id-badge me-1"></i> {{ __('Verified Customer') }}</div>
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0 text-muted"><i class="fas fa-envelope fa-fw"></i></div>
                        <div class="text-dark">{{ $order->customer_email ?? __('N/A') }}</div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0 text-muted"><i class="fas fa-phone fa-fw"></i></div>
                        <div class="text-dark">{{ $order->customer_phone ?? __('N/A') }}</div>
                    </div>
                    @if($order->shipping_address)
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0 text-muted"><i class="fas fa-map-marker-alt fa-fw"></i></div>
                        <div class="text-dark small lh-base">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }} {{ $order->shipping_zip }}<br>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 10px;">{{ $order->shipping_country }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Payment Details -->
        <div class="glass-card mb-4 border-0 shadow-soft overflow-hidden">
            <div class="card-header bg-transparent border-bottom p-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-credit-card me-2 text-primary"></i> {{ __('Transaction Info') }}</h6>
            </div>
            <div class="card-body p-4">
                <div class="info-row">
                    <span class="info-label">{{ __('Method') }}</span>
                    <span class="info-value text-capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                </div>
                @if($order->transaction_id)
                <div class="info-row">
                    <span class="info-label">{{ __('Ref. ID') }}</span>
                    <span class="info-value small font-monospace">{{ $order->transaction_id }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">{{ __('Processed') }}</span>
                    <span class="info-value">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateStatus = async (el, field) => {
        const orderId = el.dataset.orderId;
        const url = el.dataset.updateUrl;
        const value = el.value;
        
        // Add loading state
        el.style.opacity = '0.5';
        el.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ [field]: value })
            });

            const data = await response.json();

            if (data.success) {
                // Since this is the details page, we might want to refresh to update the timeline
                // But for now, just show toast and update badge color
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                // Update classes
                el.classList.remove('info', 'success', 'warning', 'danger');
                if (field === 'status') {
                    el.classList.add(value === 'delivered' ? 'success' : (value === 'cancelled' ? 'danger' : 'info'));
                    // Optional: auto-refresh after a delay to update the timeline
                    setTimeout(() => location.reload(), 1500);
                } else {
                    el.classList.add(value === 'paid' ? 'success' : (value === 'failed' ? 'danger' : 'warning'));
                }
            } else {
                throw new Error(data.message || 'Update failed');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: error.message
            });
            setTimeout(() => location.reload(), 1500);
        } finally {
            el.style.opacity = '1';
            el.disabled = false;
        }
    };

    document.querySelectorAll('.status-update-ajax').forEach(select => {
        select.addEventListener('change', () => updateStatus(select, 'status'));
    });

    document.querySelectorAll('.payment-update-ajax').forEach(select => {
        select.addEventListener('change', () => updateStatus(select, 'payment_status'));
    });
});
</script>
@endsection

