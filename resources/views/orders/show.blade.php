@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
<style>
    /* Custom styles for the order timeline */
    .timeline-container {
        padding: 20px 0;
        margin-bottom: 30px;
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
        background: #e2e8f0;
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
        width: 34px;
        height: 34px;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 14px;
        color: #94a3b8;
        transition: all 0.3s ease;
    }
    .step-label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    /* Active State */
    .timeline-step.active .step-icon {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
    }
    .timeline-step.active .step-label {
        color: #1e293b;
    }
    /* Completed State */
    .timeline-step.completed .step-icon {
        background: var(--success-gradient);
        border-color: transparent;
        color: white;
    }
    .timeline-step.completed .step-label {
        color: #059669;
    }
    
    /* Order Layout */
    .order-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .order-grid {
            grid-template-columns: 1fr;
        }
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #64748b;
        font-weight: 500;
    }
    .info-value {
        color: #1e293b;
        font-weight: 600;
        text-align: right;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <a href="{{ route('orders.index') }}" class="btn-action btn-action-view" style="width: 32px; height: 32px; border-radius: 8px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="page-title" style="margin: 0; font-size: 28px;">Order #{{ $order->order_number }}</h1>
                <span class="badge {{ $order->status_badge_class }}" style="margin: 0;">{{ ucfirst($order->status) }}</span>
            </div>
            <p class="page-subtitle">
                Placed on {{ $order->created_at->format('F d, Y') }} at {{ $order->created_at->format('h:i A') }}
            </p>
        </div>
        <div style="display: flex; gap: 10px;">
            @if(!$order->invoice)
            <form action="{{ route('orders.generate-invoice', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-invoice"></i> Generate Invoice
                </button>
            </form>
            @else
            <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-secondary">
                <i class="fas fa-file-invoice"></i> Invoice
            </a>
            @endif
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Order
            </a>
        </div>
    </div>
</div>

<div class="order-grid">
    <!-- Main Content Column -->
    <div class="main-column">
        
        <!-- Progress Tracker -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                @php
                    $steps = ['pending', 'processing', 'shipped', 'delivered'];
                    $currentStatusIndex = array_search($order->status, $steps);
                    if ($order->status == 'cancelled') $currentStatusIndex = -1;
                @endphp
                
                @if($order->status == 'cancelled')
                   <div class="alert alert-danger" style="margin: 0;">
                        <i class="fas fa-times-circle"></i>
                        <div>
                            <strong>Order Cancelled</strong>
                            <p style="margin: 4px 0 0 0; font-size: 13px;">This order was cancelled on {{ $order->updated_at->format('M d, Y') }}</p>
                        </div>
                   </div> 
                @else
                <div class="timeline-container">
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
                                <div class="step-label">{{ ucfirst($step) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-box-open"></i> Order Items ({{ $order->items->count() }})
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px;">Product Items</th>
                                <th class="text-right">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right" style="padding-right: 24px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td style="padding-left: 24px;">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #cbd5e1;">
                                            <i class="fas fa-image fa-lg"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px;">{{ $item->product_name }}</div>
                                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right" style="font-weight: 500;">{{ currency($item->price) }}</td>
                                <td class="text-center">
                                    <span style="background: #f1f5f9; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 13px;">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-right" style="padding-right: 24px; font-weight: 700; color: #1e293b;">
                                    {{ currency($item->price * $item->quantity) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Notes Section -->
        @if($order->notes)
        <div class="card" style="margin-top: 24px;">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-sticky-note"></i> Order Notes</div>
            </div>
            <div class="card-body">
                <p style="color: #475569; line-height: 1.6; margin: 0; font-style: italic;">"{{ $order->notes }}"</p>
            </div>
        </div>
        @endif
        
    </div>

    <!-- Sidebar Column -->
    <div class="sidebar-column">
        
        <!-- Order Summary -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-calculator"></i> Summary</div>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Subtotal</span>
                    <span class="info-value">{{ currency($order->subtotal) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tax</span>
                    <span class="info-value">{{ currency($order->tax) }}</span>
                </div>
                <!-- Shipping -->
                <div class="info-row">
                    <span class="info-label">Shipping</span>
                    <span class="info-value">{{ $order->shipping_cost > 0 ? currency($order->shipping_cost) : 'Free' }}</span>
                </div>
                <!-- Discount -->
                @if($order->discount > 0)
                <div class="info-row">
                    <span class="info-label">Discount</span>
                    <span class="info-value" style="color: #10b981;">-{{ currency($order->discount) }}</span>
                </div>
                @endif
                
                <div style="margin-top: 16px; padding-top: 16px; border-top: 2px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 16px;">Total</span>
                    <span style="font-weight: 800; color: #6366f1; font-size: 24px;">{{ currency($order->total) }}</span>
                </div>
                
                <div style="margin-top: 20px; text-align: center;">
                    <span class="badge {{ $order->payment_status_badge_class }}" style="width: 100%; justify-content: center; padding: 10px; font-size: 14px;">
                        Payment: {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-user"></i> Customer</div>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #3730a3; font-weight: 700; font-size: 18px;">
                        {{ substr($order->customer_name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #1e293b;">{{ $order->customer_name }}</div>
                        <div style="font-size: 12px; color: #64748b;">Customer</div>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; gap: 10px;">
                        <i class="fas fa-envelope" style="color: #94a3b8; margin-top: 3px;"></i>
                        <span style="font-size: 14px; color: #334155;">{{ $order->customer_email ?? 'No email' }}</span>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <i class="fas fa-phone" style="color: #94a3b8; margin-top: 3px;"></i>
                        <span style="font-size: 14px; color: #334155;">{{ $order->customer_phone ?? 'No phone' }}</span>
                    </div>
                    @if($order->shipping_address)
                    <div style="display: flex; gap: 10px;">
                        <i class="fas fa-map-marker-alt" style="color: #94a3b8; margin-top: 3px;"></i>
                        <span style="font-size: 14px; color: #334155; line-height: 1.5;">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }} {{ $order->shipping_zip }}<br>
                            {{ $order->shipping_country }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-credit-card"></i> Payment</div>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="info-label">Method</span>
                    <span class="info-value" style="text-transform: capitalize;">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                </div>
                @if($order->transaction_id)
                <div class="info-row">
                    <span class="info-label">Trans. ID</span>
                    <span class="info-value" style="font-family: monospace; font-size: 13px;">{{ $order->transaction_id }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
