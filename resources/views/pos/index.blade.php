@extends('layouts.app')

@section('title', 'POS Terminal')

@push('styles')
<style>
/* Reset & Layout */
.main-content {
    padding: 0 !important;
    height: calc(100vh - 70px); /* Exact match for --navbar-height */
    overflow: hidden;
    background: #f1f5f9;
}

/* === 3-Column Layout: Products | Cart Items | Order Form === */
.pos-layout {
    display: grid;
    grid-template-columns: 1fr 320px 340px;
    height: 100%;
    gap: 0;
}

/* Large screens (1400px+): Comfortable spacing */
@media (min-width: 1400px) {
    .pos-layout {
        grid-template-columns: 1fr 340px 380px;
    }
}

/* Medium-large (1200-1400px): Balanced */
@media (max-width: 1399px) and (min-width: 1200px) {
    .pos-layout {
        grid-template-columns: 1fr 300px 340px;
    }
}

/* Tablet landscape (1024-1199px): Tighter */
@media (max-width: 1199px) and (min-width: 1024px) {
    .pos-layout {
        grid-template-columns: 1fr 280px 300px;
    }
}

/* Tablet portrait (768-1023px): Stack cart panels vertically */
@media (max-width: 1023px) and (min-width: 768px) {
    .pos-layout {
        grid-template-columns: 1fr 380px;
        grid-template-rows: 1fr;
    }
    
    .pos-order-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .cart-items-panel,
    .order-form-panel {
        border-left: none !important;
        flex: 1;
        min-height: 0;
    }
    
    .cart-items-panel {
        border-bottom: 1px solid #e2e8f0;
        max-height: 45%;
    }
    
    .order-form-panel {
        max-height: 55%;
    }
}

/* Mobile (<768px): Full stack */
@media (max-width: 767px) {
    .main-content {
        height: auto;
        min-height: calc(100vh - 70px);
        overflow: auto;
        padding-bottom: 100px;
    }
    
    .pos-layout {
        grid-template-columns: 1fr;
        display: flex;
        flex-direction: column;
    }

    .pos-products-area {
        height: auto;
        min-height: 50vh;
        overflow: visible;
        padding: 16px;
    }
    
    .products-grid {
        overflow: visible;
        max-height: none;
    }

    .pos-order-wrapper {
        display: flex;
        flex-direction: column;
        border-top: 2px solid #e2e8f0;
    }
    
    .cart-items-panel {
        border-left: none !important;
        border-bottom: 1px solid #e2e8f0;
        max-height: 280px;
    }
    
    .order-form-panel {
        border-left: none !important;
        position: sticky;
        bottom: 0;
        background: white;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
        z-index: 50;
    }
    
    .panel-header {
        padding: 12px 16px;
    }
    
    .panel-content {
        padding: 12px 16px;
    }
    
    .order-form-panel .panel-content {
        max-height: 60vh;
        overflow-y: auto;
    }
}

/* Left Panel: Products */
.pos-products-area {
    padding: 24px;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.pos-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.search-container {
    position: relative;
    width: 100%;
    max-width: 600px;
}

.search-input {
    width: 100%;
    padding: 16px 24px 16px 50px;
    border: none;
    border-radius: 16px;
    background: white;
    font-size: 16px;
    color: #1e293b;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.3s ease;
}

.search-input::placeholder {
    color: #94a3b8;
}

.search-input:focus {
    outline: none;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}

.search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 18px;
}

/* Categories - Horizontal Scroll */
.categories-wrapper {
    margin-bottom: 24px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 4px; /* Space for scrollbar if any */
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
    flex-shrink: 0;
}
.categories-wrapper::-webkit-scrollbar {
    display: none;
}

.category-pills {
    display: flex;
    gap: 12px;
}

.category-pill {
    padding: 10px 20px;
    background: white;
    border-radius: 50px;
    color: #64748b;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    user-select: none;
}

.category-pill:hover {
    background: #f8fafc;
    color: #334155;
    transform: translateY(-1px);
}

.category-pill.active {
    background: #6366f1;
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    grid-auto-rows: min-content;
    gap: 20px;
    overflow-y: auto;
    padding-right: 8px;
    padding-bottom: 24px;
    flex: 1;
    align-content: start;
}

/* Custom Scrollbar for products */
.products-grid::-webkit-scrollbar {
    width: 6px;
}
.products-grid::-webkit-scrollbar-track {
    background: transparent;
}
.products-grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 20px;
}

/* Product Card */
.pos-product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
}

.pos-product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    border-color: #6366f1;
}

.pos-product-card:active {
    transform: scale(0.98);
}

.card-img-wrapper {
    height: 140px;
    min-height: 140px;
    width: 100%;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

.card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.pos-product-card:hover .card-img {
    transform: scale(1.05);
}

.overlay-add {
    position: absolute;
    inset: 0;
    background: rgba(99, 102, 241, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.pos-product-card:hover .overlay-add {
    opacity: 1;
}

.card-content {
    padding: 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-title {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
    margin-bottom: 4px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-footer {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-price {
    font-weight: 700;
    color: #6366f1;
    font-size: 16px;
}

.card-stock {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(255, 255, 255, 0.9);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.qty-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #10b981;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
    z-index: 2;
    animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes popIn {
    from { transform: scale(0); }
    to { transform: scale(1); }
}

/* Stock Badges */
.stock-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 4px;
}

.stock-badge.in-stock {
    background: rgba(16, 185, 129, 0.15);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.stock-badge.low-stock {
    background: rgba(245, 158, 11, 0.15);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.stock-badge.out-of-stock {
    background: rgba(239, 68, 68, 0.15);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

/* Sale Badge */
.sale-badge {
    position: absolute;
    top: 10px;
    left: 50px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
    z-index: 2;
}

/* Price Variations */
.card-price-wrapper {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.card-price.sale {
    color: #ef4444;
}

.card-original-price {
    font-size: 12px;
    color: #94a3b8;
    text-decoration: line-through;
}

/* SKU */
.card-sku {
    font-size: 10px;
    color: #94a3b8;
    font-family: 'SF Mono', monospace;
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* No Image Placeholder */
.no-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    color: #94a3b8;
}

.no-image-placeholder i {
    font-size: 40px;
    opacity: 0.6;
}

/* Out of Stock Card */
.pos-product-card.out-of-stock-card {
    opacity: 0.65;
    cursor: not-allowed;
}

.pos-product-card.out-of-stock-card:hover {
    transform: none;
    box-shadow: none;
    border-color: #f1f5f9;
}

/* Overlay Add Icon Styling */
.overlay-add i {
    color: white;
    font-size: 28px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Order Wrapper - Container for both panels on tablet */
.pos-order-wrapper {
    display: contents;
}

@media (max-width: 1023px) {
    .pos-order-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
}

/* === Cart Items Panel === */
.cart-items-panel {
    background: white;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

/* === Order Form Panel === */
.order-form-panel {
    background: #f8fafc;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

/* Panel Headers */
.panel-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    background: white;
}

.panel-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.panel-title i {
    color: #6366f1;
    font-size: 14px;
}

.panel-badge {
    background: #6366f1;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 8px;
}

/* Panel Content Areas */
.panel-content {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    min-height: 0;
}

/* Custom scrollbar for panels */
.panel-content::-webkit-scrollbar {
    width: 4px;
}
.panel-content::-webkit-scrollbar-track {
    background: transparent;
}
.panel-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.panel-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Cart Items List */
.items-list {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #fff;
    min-height: 0;
}

.cart-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px dashed #e2e8f0;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(10px); }
    to { opacity: 1; transform: translateX(0); }
}

.item-details {
    flex: 1;
    padding-right: 12px;
}

.item-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
    line-height: 1.3;
}

.item-price {
    color: #64748b;
    font-size: 13px;
}

.item-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.control-btn {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
}

.control-btn:hover {
    border-color: #6366f1;
    color: #6366f1;
    background: #f8fafc;
}

.control-btn:active {
    background: #f1f5f9;
}

.qty-text {
    font-weight: 600;
    min-width: 20px;
    text-align: center;
    font-size: 14px;
    color: #1e293b;
}

/* Order Form Footer with Totals */
.order-totals {
    background: white;
    padding: 16px;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.bill-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
    color: #64748b;
}

.bill-total {
    display: flex;
    justify-content: space-between;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px dashed #cbd5e1;
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
}

.pay-btn {
    width: 100%;
    margin-top: 20px;
    padding: 16px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.pay-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(79, 70, 229, 0.4);
}

.pay-btn:active {
    transform: translateY(0);
}

.pay-btn:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Customer Form embedded cleanly */
.customer-section {
    margin-top: 15px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.compact-form-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.compact-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    transition: border-color 0.2s;
}
.compact-input:focus {
    outline: none;
    border-color: #6366f1;
}

/* Loading State */
.loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    border-radius: 12px;
}
</style>
@endpush

@section('content')
<div class="pos-layout">
    <!-- Left: Products -->
    <div class="pos-products-area">
        <div class="pos-header">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Search products by name or SKU..." autofocus>
            </div>
            <div style="font-weight: 700; color: #334155; display: flex; align-items: center; margin-left: 20px;">
                <div style="width: 40px; height: 40px; background: #e0e7ff; color: #6366f1; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                    <i class="fas fa-store-alt" style="font-size: 18px;"></i>
                </div>
                <div style="display: none; @media(min-width: 768px){display:block;}">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600;">TERMINAL</div>
                    <div style="font-size: 16px; color: #1e293b;">POS-{{ str_pad(auth()->id(), 3, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <div class="categories-wrapper">
            <div class="category-pills">
                <div class="category-pill category-btn active" data-category="">All Items</div>
                @foreach($categories as $category)
                    <div class="category-pill category-btn" data-category="{{ $category->id }}">{{ $category->name }}</div>
                @endforeach
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            <!-- Products will be injected here via JS -->
            <div style="grid-column: 1/-1; text-align: center; padding-top: 100px; color: #94a3b8;">
                <i class="fas fa-circle-notch fa-spin fa-3x" style="color: #cbd5e1;"></i>
                <p style="margin-top: 16px; font-weight: 500;">Loading catalog...</p>
            </div>
        </div>
    </div>
    <!-- Order Wrapper: Contains Cart Items + Order Form -->
    <div class="pos-order-wrapper">
        
        <!-- Panel 1: Cart Items -->
        <div class="cart-items-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-shopping-cart"></i>
                    Cart
                    <span class="panel-badge" id="cartCount">0</span>
                </div>
                <button onclick="clearCart()" title="Clear Cart (F4)" style="background: #fee2e2; color: #ef4444; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">
                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                </button>
            </div>

            <div class="items-list" id="cartItems">
                <!-- Cart Items -->
                <div style="text-align: center; margin-top: 60px; color: #94a3b8;">
                    <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-shopping-basket" style="font-size: 24px; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-size: 14px; font-weight: 600; color: #64748b;">Empty Cart</h3>
                    <p style="font-size: 12px; margin-top: 4px;">Click products to add</p>
                </div>
            </div>
        </div>

        <!-- Panel 2: Order Form -->
        <div class="order-form-panel">
            <div class="panel-header">
                <div class="panel-title">
                    <i class="fas fa-file-invoice"></i>
                    Order Details
                </div>
            </div>

            <div class="panel-content">
                <!-- Customer Selection -->
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; display: block;">Customer</label>
                    <select id="customerSelect" class="compact-input" style="background: white; cursor: pointer; font-size: 13px;">
                        <option value="">🚶 Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}"
                                    data-email="{{ $customer->email }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-credit-limit="{{ $customer->credit_limit }}"
                                    data-balance="{{ $customer->current_balance }}">
                                {{ $customer->name }} ({{ $customer->customer_code }})
                                @if($customer->credit_limit > 0)
                                    - Credit: {{ currency($customer->credit_limit) }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Info Badge -->
                <div id="customerInfo" style="display: none; padding: 10px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 8px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);">
                    <div style="display: flex; align-items: center; margin-bottom: 4px;">
                        <div style="flex: 1;">
                            <div id="customerDisplayName" style="font-weight: 600; color: white; font-size: 13px; margin-bottom: 2px;"></div>
                            <div id="customerContact" style="font-size: 10px; color: rgba(255,255,255,0.85);"></div>
                        </div>
                    </div>
                    <div id="creditInfo" style="display: none; margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2);">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 10px; margin-bottom: 4px;">
                            <span style="color: rgba(255,255,255,0.9);">Credit:</span>
                            <span id="creditUsed" style="font-weight: 700; color: white; font-size: 11px;"></span>
                        </div>
                        <div style="height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; overflow: hidden;">
                            <div id="creditBar" style="height: 100%; background: white; width: 0%; transition: width 0.3s;"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="customerId">
                <input type="hidden" id="customerName" value="Walk-in Customer">
                <input type="hidden" id="customerEmail">
                <input type="hidden" id="customerPhone">
                
                <!-- Discount Input -->
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; display: block;">Discount</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="number" id="discountAmount" class="compact-input" placeholder="0" min="0" step="0.01" style="flex: 1; font-size: 13px;">
                        <select id="discountType" class="compact-input" style="width: 65px; font-size: 13px; padding: 8px;">
                            <option value="percent">%</option>
                            <option value="fixed">$</option>
                        </select>
                    </div>
                </div>

                <!-- Payment Method -->
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; display: block;">Payment</label>
                    <select id="paymentMethod" class="compact-input" style="background: white; font-size: 13px;">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Credit Card</option>
                        <option value="mobile">📱 Mobile Payment</option>
                        <option value="credit" id="creditOption" disabled style="color: #9ca3af;">🏦 On Credit</option>
                        <option value="other">⚪ Other</option>
                    </select>
                </div>
            </div>

            <!-- Order Totals - Fixed at bottom -->
            <div class="order-totals">
                <div class="bill-row">
                    <span>Subtotal</span>
                    <span id="subtotal" style="font-weight: 600; color: #1e293b;">{{ currency(0) }}</span>
                </div>
                <div class="bill-row" id="discountRow" style="display: none;">
                    <span>Discount</span>
                    <span id="discountDisplay" style="font-weight: 600; color: #10b981;">-{{ currency(0) }}</span>
                </div>
                <div class="bill-row">
                    <span>Tax ({{ setting('tax_rate', 0) }}%)</span>
                    <span id="tax" style="font-weight: 600; color: #1e293b;">{{ currency(0) }}</span>
                </div>
                <div class="bill-total">
                    <span>Total</span>
                    <span id="total" style="color: #6366f1;">{{ currency(0) }}</span>
                </div>

                <!-- Keyboard Shortcuts -->
                <div style="margin-top: 10px; font-size: 9px; color: #94a3b8; text-align: center;">
                    <kbd style="background: #e2e8f0; padding: 1px 4px; border-radius: 2px; font-family: monospace;">F2</kbd> Search
                    <span style="margin: 0 6px;">•</span>
                    <kbd style="background: #e2e8f0; padding: 1px 4px; border-radius: 2px; font-family: monospace;">F4</kbd> Clear
                    <span style="margin: 0 6px;">•</span>
                    <kbd style="background: #e2e8f0; padding: 1px 4px; border-radius: 2px; font-family: monospace;">F9</kbd> Pay
                </div>

                <button class="pay-btn" id="checkoutBtn" onclick="checkout()" disabled>
                    Charge {{ currency(0) }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Config
    window.currencyConfig = {
        symbol: '{{ setting("currency_symbol", "$") }}',
        position: '{{ setting("currency_position", "before") }}',
        decimals: {{ setting("currency_decimals", 2) }},
        decimal_separator: '{{ setting("decimal_separator", ".") }}',
        thousands_separator: '{{ setting("thousands_separator", ",") }}',
        tax_rate: {{ setting("tax_rate", 0) }} / 100
    };
    
    // Override default POS display function to match new UI
    window.customDisplayProducts = function(products) {
        const grid = document.getElementById('productsGrid');
        if (!grid) return;

        if (products.length === 0) {
            grid.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding-top: 60px; color: #94a3b8;">
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p>No matching products found</p>
                </div>
            `;
            return;
        }

        grid.innerHTML = products.map(product => {
            const cartItem = cart.find(item => item.product_id === product.id);
            const quantityBadge = cartItem ? `<div class="qty-badge">${cartItem.quantity}</div>` : '';
            
            // Build image path correctly
            let imageSrc = '';
            if (product.image) {
                imageSrc = product.image.startsWith('http') ? product.image : `/storage/${product.image}`;
            }
            
            const imageHtml = imageSrc 
                ? `<img src="${imageSrc}" class="card-img" alt="${product.name}" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'no-image-placeholder\\'><i class=\\'fas fa-box\\'></i></div>'">`
                : `<div class="no-image-placeholder"><i class="fas fa-box"></i></div>`;

            // Stock status with colors
            let stockBadgeHtml = '';
            let isOutOfStock = product.stock <= 0;
            if (isOutOfStock) {
                stockBadgeHtml = `<div class="stock-badge out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</div>`;
            } else if (product.stock <= 5) {
                stockBadgeHtml = `<div class="stock-badge low-stock"><i class="fas fa-exclamation-triangle"></i> ${product.stock} left</div>`;
            } else {
                stockBadgeHtml = `<div class="stock-badge in-stock"><i class="fas fa-check-circle"></i> ${product.stock}</div>`;
            }
            
            // Sale badge if applicable
            const saleBadge = product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price) 
                ? `<div class="sale-badge">SALE</div>` 
                : '';
            
            // Price display
            let priceHtml = '';
            if (product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price)) {
                priceHtml = `
                    <div class="card-price-wrapper">
                        <span class="card-price sale">${formatCurrency(parseFloat(product.sale_price))}</span>
                        <span class="card-original-price">${formatCurrency(parseFloat(product.price))}</span>
                    </div>
                `;
            } else {
                priceHtml = `<div class="card-price">${formatCurrency(parseFloat(product.price))}</div>`;
            }
            
            // SKU display
            const skuHtml = product.sku ? `<div class="card-sku">${product.sku}</div>` : '';

            return `
                <div class="pos-product-card ${isOutOfStock ? 'out-of-stock-card' : ''}" 
                     onclick="${isOutOfStock ? '' : `addToCartWithFeedback(${product.id})`}" 
                     title="${product.name}">
                    ${quantityBadge}
                    ${saleBadge}
                    <div class="card-img-wrapper">
                        ${imageHtml}
                        ${!isOutOfStock ? `<div class="overlay-add"><i class="fas fa-plus"></i></div>` : ''}
                    </div>
                    ${stockBadgeHtml}
                    <div class="card-content">
                        <div class="card-title">${product.name}</div>
                        ${skuHtml}
                        <div class="card-footer">
                            ${priceHtml}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    };

    // Override Cart Display
    window.customUpdateCartUI = function() {
        const container = document.getElementById('cartItems');
        const cartCountBadge = document.getElementById('cartCount');
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        
        // Update cart count badge
        if (cartCountBadge) {
            cartCountBadge.textContent = totalItems;
            cartCountBadge.style.display = totalItems > 0 ? 'inline-block' : 'none';
        }
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; margin-top: 60px; color: #94a3b8;">
                    <div style="width: 64px; height: 64px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-shopping-basket" style="font-size: 24px; color: #cbd5e1;"></i>
                    </div>
                    <h3 style="font-size: 14px; font-weight: 600; color: #64748b;">Empty Cart</h3>
                    <p style="font-size: 12px; margin-top: 4px;">Click products to add</p>
                </div>
            `;
            document.getElementById('checkoutBtn').disabled = true;
            document.getElementById('checkoutBtn').innerHTML = `Charge ${formatCurrency(0)}`;
        } else {
            container.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="item-details">
                        <h4>${item.name}</h4>
                        <div class="item-price">${formatCurrency(item.price)}</div>
                    </div>
                    <div class="item-controls">
                        <div class="control-btn" onclick="updateQuantity(${item.product_id}, -1)">
                            <i class="fas fa-minus" style="font-size: 10px;"></i>
                        </div>
                        <div class="qty-text">${item.quantity}</div>
                        <div class="control-btn" onclick="updateQuantity(${item.product_id}, 1)">
                            <i class="fas fa-plus" style="font-size: 10px;"></i>
                        </div>
                    </div>
                    <div style="font-weight: 600; color: #334155; min-width: 70px; text-align: right;">
                        ${formatCurrency(item.price * item.quantity)}
                    </div>
                </div>
            `).join('');
            document.getElementById('checkoutBtn').disabled = false;
        }
        
        // Update Total Button Text
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const total = subtotal * (1 + window.currencyConfig.tax_rate);
        
        if(cart.length > 0) {
            document.getElementById('checkoutBtn').innerHTML = `Charge ${formatCurrency(total)}`;
        }
    };

    // Add wrapper for feedback
    window.addToCartWithFeedback = function(id) {
        const product = products.find(p => p.id === id);
        if (product && product.stock > 0) {
            addToCart(id);
            
            // Simple visual feedback if Toast isn't available
            if(typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: 'Added to cart',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        }
    };

    // Helper: Format Currency
    window.formatCurrency = function(amount) {
        const config = window.currencyConfig;
        const formatted = Number(amount).toFixed(config.decimals)
            .replace(/\d(?=(\d{3})+\.)/g, '$&' + config.thousands_separator)
            .replace('.', config.decimal_separator);
        return config.position === 'before' ? config.symbol + formatted : formatted + ' ' + config.symbol;
    };
</script>
<script src="{{ asset('js/pos.js') }}"></script>
<script src="{{ asset('js/pos-customer.js') }}"></script>
<script>
    // Monkey Patch the display functions from pos.js after it loads
    document.addEventListener('DOMContentLoaded', function() {
        
        // Override global functions expected by pos.js or the UI
        displayProducts = window.customDisplayProducts;
        
        // Intercept updateCart to inject our UI logic
        const originalUpdateCart = window.updateCart || function(){};
        
        window.updateCart = function() {
            window.customUpdateCartUI();
            updateTotals(); // Keep original total calculation logic from pos.js
            displayProducts(products); // Refresh badging on grid
        };

        // Re-bind category pills
        const pills = document.querySelectorAll('.category-pill');
        pills.forEach(pill => {
            pill.addEventListener('click', function() {
                pills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Initial Load
        loadAllProducts();
    });
</script>
@endpush
@endsection
