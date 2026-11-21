@extends('layouts.app')

@section('title', 'Caisse POS')

@push('styles')
<style>
.main-content {
    padding: 0 !important;
    height: calc(100vh - 70px);
    overflow: hidden;
    background: #f8fafc;
}

/* === 3-Column Pro POS Layout === */
.pos-layout {
    display: grid;
    grid-template-columns: 1fr 315px 320px;
    height: 100%;
    min-height: 0;
    gap: 0;
}

@media (min-width: 1440px) {
    .pos-layout {
        grid-template-columns: 1fr 360px 360px;
    }
}

@media (max-width: 1200px) and (min-width: 1024px) {
    .pos-layout {
        grid-template-columns: 1fr 295px 300px;
    }
}

/* Mobile / Tablet Tab Bar */
.pos-mobile-tabs {
    display: none;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    gap: 8px;
}

.pos-mobile-tab {
    flex: 1 1 0;
    min-width: 0;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    border-radius: 10px;
    padding: 6px 4px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pos-mobile-tab .step-badge-num {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #334155;
    font-size: 10.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.pos-mobile-tab.active {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 3px 10px rgba(220, 38, 38, 0.22);
}

.pos-mobile-tab.active .step-badge-num {
    background: #ffffff;
    color: #dc2626;
}

.pos-mobile-tab.done {
    background: #f0fdf4;
    color: #15803d;
    border-color: #bbf7d0;
}

.pos-mobile-tab.done .step-badge-num {
    background: #16a34a;
    color: #ffffff;
}

#posStepConfirmBox {
    display: none;
}

@media (max-width: 1023px) {
    .main-content {
        height: auto !important;
        min-height: calc(100vh - 60px) !important;
        overflow: visible !important;
        overflow-y: visible !important;
    }
    .pos-mobile-tabs {
        display: flex !important;
        position: fixed !important;
        top: 60px !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 45 !important;
        padding: 6px 8px !important;
        gap: 5px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #e2e8f0 !important;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.08) !important;
    }
    .pos-mobile-tab {
        min-height: 46px;
    }
    .pos-layout {
        display: block !important;
        height: auto !important;
        overflow: visible !important;
        padding-top: 60px !important;
    }
    .pos-pane:not(.mobile-active) {
        display: none !important;
    }
    .pos-pane.mobile-active {
        display: flex !important;
        flex-direction: column !important;
        height: auto !important;
        min-height: calc(100vh - 130px) !important;
        overflow: visible !important;
        padding-bottom: 96px !important;
    }
    .pos-products-area,
    .cart-items-panel,
    .order-form-panel {
        height: auto !important;
        min-height: 0 !important;
        overflow: visible !important;
    }
    .pos-header {
        display: flex !important;
        flex-wrap: wrap !important;
        padding: 10px !important;
        gap: 8px !important;
        background: #ffffff !important;
    }
    .pos-header .search-container {
        display: block !important;
        flex: 1 1 calc(100% - 96px) !important;
        min-width: 170px !important;
        order: 1 !important;
    }
    .pos-header .search-input {
        display: block !important;
        width: 100% !important;
        height: 42px !important;
        font-size: 14px !important;
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 10px !important;
    }
    .pos-header #posMobileFiltersToggle {
        order: 2 !important;
        height: 42px !important;
    }
    .pos-header .pos-filters-collapse {
        order: 4 !important;
    }
    .products-grid {
        flex: none !important;
        height: auto !important;
        min-height: 280px !important;
        max-height: none !important;
        overflow: visible !important;
        padding: 10px !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 8px !important;
    }
    .items-list,
    .panel-content {
        flex: none !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
    }
    /* Step 3 (Client), Step 4 (Payment), Step 5 (Confirmation) sub-panes inside #paneCheckout */
    #paneCheckout[data-mobile-step="3"] #posStepDiscountBox,
    #paneCheckout[data-mobile-step="3"] #posStepPaymentBox,
    #paneCheckout[data-mobile-step="3"] #posStepConfirmBox,
    #paneCheckout[data-mobile-step="3"] .order-totals {
        display: none !important;
    }
    #paneCheckout[data-mobile-step="4"] #posStepClientBox,
    #paneCheckout[data-mobile-step="4"] #posStepConfirmBox,
    #paneCheckout[data-mobile-step="4"] .order-totals {
        display: none !important;
    }
    #paneCheckout[data-mobile-step="5"] #posStepClientBox,
    #paneCheckout[data-mobile-step="5"] #posStepDiscountBox,
    #paneCheckout[data-mobile-step="5"] #posStepPaymentBox {
        display: none !important;
    }
    #paneCheckout[data-mobile-step="5"] #posStepConfirmBox {
        display: block !important;
    }
    .pos-product-card {
        min-height: 170px !important;
    }
    .card-img-wrapper {
        height: 92px !important;
        min-height: 92px !important;
    }
    .control-btn,
    .cart-item-remove {
        width: 34px !important;
        height: 34px !important;
    }
    .payment-pill {
        min-height: 38px;
        padding: 7px 12px !important;
        font-size: 12.5px !important;
    }
    .cash-preset-btn {
        min-height: 36px;
        padding: 5px 11px !important;
        font-size: 12px !important;
    }
    .pay-btn {
        min-height: 48px;
        font-size: 15px !important;
    }
}

@media (max-width: 767.98px) {
    .pos-filters-collapse {
        display: flex;
        width: 100%;
        gap: 6px;
        flex-wrap: wrap;
        padding-top: 6px;
        border-top: 1px solid #f1f5f9;
    }
    .pos-filters-collapse.collapsed-mobile {
        display: none !important;
    }
    .pos-filters-collapse .pos-store-picker {
        flex: 1 1 calc(50% - 4px);
        min-width: 130px;
    }
    .pos-filters-collapse .pos-store-select {
        width: 100%;
    }
}

@media (min-width: 768px) {
    .pos-filters-collapse {
        display: contents;
    }
}

@media (hover: none) {
    .pos-product-card:hover {
        transform: none;
        box-shadow: none;
    }
    .pos-product-card:hover .overlay-add {
        opacity: 0;
    }
    .pos-product-card:active {
        transform: scale(0.97);
        border-color: #dc2626;
    }
}

.pos-mobile-bottom-bar {
    display: none;
}

@media (max-width: 1023px) {
    .pos-mobile-bottom-bar.visible {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 45;
        background: #0f172a;
        color: #ffffff;
        padding: 10px 14px calc(10px + env(safe-area-inset-bottom));
        box-shadow: 0 -6px 24px rgba(15, 23, 42, 0.22);
    }
}

/* === Left: Products Catalog === */
.pos-products-area {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
    background: #f8fafc;
}

.pos-header {
    flex-shrink: 0;
    padding: 10px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.search-container {
    position: relative;
    flex: 1 1 220px;
    min-width: 180px;
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.search-input {
    width: 100%;
    padding: 10px 14px 10px 38px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13.5px;
    background: #f8fafc;
    color: #0f172a;
    transition: all 0.2s;
}

.search-input:focus {
    outline: none;
    background: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.08);
}

.pos-store-picker {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 5px 10px;
}

.pos-store-picker i {
    color: #dc2626;
    font-size: 13px;
}

.pos-store-select {
    border: none;
    background: transparent;
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    cursor: pointer;
    outline: none;
    padding-right: 4px;
}

.pos-terminal-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
    font-size: 12px;
    font-weight: 700;
}

.pos-kpi-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 11px;
    border-radius: 10px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    font-size: 12px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.pos-icon-btn {
    height: 34px;
    padding: 0 11px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s;
}

.pos-icon-btn:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
}

.qty-input-inline {
    width: 34px;
    border: none;
    background: transparent;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    -moz-appearance: textfield;
}
.qty-input-inline::-webkit-outer-spin-button,
.qty-input-inline::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.cash-presets {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 6px;
}

.cash-preset-btn {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
    border-radius: 6px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.cash-preset-btn:hover {
    border-color: #dc2626;
    color: #dc2626;
    background: #fef2f2;
}

.categories-wrapper {
    flex-shrink: 0;
    padding: 8px 16px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
    white-space: nowrap;
}

.categories-wrapper::-webkit-scrollbar {
    height: 4px;
}
.categories-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.category-pills {
    display: inline-flex;
    gap: 8px;
}

.category-pill {
    padding: 6px 14px;
    border-radius: 9999px;
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
}

.category-pill:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.category-pill.active {
    background: #dc2626;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
}

.products-grid {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 14px 16px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(158px, 1fr));
    grid-auto-rows: max-content;
    gap: 12px;
    align-content: start;
}

.pos-product-card {
    display: block;
    height: auto;
    min-height: 205px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.pos-product-card:hover {
    transform: translateY(-2px);
    border-color: #dc2626;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}

.pos-product-card.out-of-stock-card {
    opacity: 0.58;
    cursor: not-allowed;
}

.pos-product-card.out-of-stock-card:hover {
    transform: none;
    border-color: #e2e8f0;
    box-shadow: none;
}

.card-img-wrapper {
    position: relative;
    width: 100%;
    height: 120px;
    min-height: 120px;
    background: #f8fafc;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #f1f5f9;
}

.card-img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    padding: 6px;
    display: block;
}

.overlay-add {
    position: absolute;
    inset: 0;
    background: rgba(220, 38, 38, 0.78);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.15s;
}

.pos-product-card:hover .overlay-add {
    opacity: 1;
}

.overlay-add i {
    color: #ffffff;
    font-size: 24px;
}

.qty-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    z-index: 3;
    background: #dc2626;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    min-width: 24px;
    height: 24px;
    padding: 0 7px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
}

.sale-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 3;
    background: #0f172a;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 6px;
    letter-spacing: 0.4px;
}

.stock-badge {
    position: absolute;
    top: 96px;
    right: 8px;
    z-index: 2;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.stock-badge.in-stock { background: #dcfce7; color: #166534; }
.stock-badge.low-stock { background: #fef3c7; color: #92400e; }
.stock-badge.out-of-stock { background: #fee2e2; color: #991b1b; }

.card-stores-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 7px;
}

.store-tag-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 9.5px;
    font-weight: 700;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.store-tag-pill.active-store {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}

.store-tag-pill.global-fallback {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}

.card-content {
    padding: 10px 12px 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 4px;
}

.card-sku {
    font-size: 10.5px;
    color: #94a3b8;
    font-family: ui-monospace, SFMono-Regular, monospace;
    margin-bottom: 8px;
}

.card-footer {
    margin-top: auto;
    display: flex;
    align-items: baseline;
    gap: 6px;
}

.card-price {
    font-size: 14.5px;
    font-weight: 800;
    color: #0f172a;
}

.card-price.sale {
    color: #dc2626;
}

.card-original-price {
    font-size: 11px;
    color: #94a3b8;
    text-decoration: line-through;
}

/* === Middle & Right Panels === */
.cart-items-panel,
.order-form-panel {
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.order-form-panel {
    background: #f8fafc;
}

.panel-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    flex-shrink: 0;
}

.panel-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.panel-title i {
    color: #dc2626;
}

.panel-badge {
    background: #dc2626;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 9999px;
}

.items-list {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    min-height: 0;
}

.cart-item-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 11px 12px;
    margin-bottom: 10px;
    transition: background-color 0.15s, border-color 0.15s;
}

.cart-item-card:nth-child(odd) {
    background: #ffffff;
}

.cart-item-card:nth-child(even) {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.cart-item-card:hover {
    background: #e9eef5;
    border-color: #cbd5e1;
}

.cart-item-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
}

.cart-item-thumb {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: contain;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px;
    flex-shrink: 0;
}

.cart-item-name {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
}

.cart-item-remove {
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 6px;
    font-size: 12px;
}

.cart-item-remove:hover {
    color: #dc2626;
    background: #fee2e2;
}

.cart-item-mid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.item-controls {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 2px;
}

.control-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: none;
    background: #f8fafc;
    color: #334155;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
}

.control-btn:hover {
    background: #dc2626;
    color: #ffffff;
}

.qty-text {
    min-width: 22px;
    text-align: center;
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}

.cart-item-discount-row {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: end;
    gap: 8px;
}

.cart-item-field {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}

.cart-item-field-label {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
}

.item-price-input {
    width: 100%;
    min-width: 0;
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12px;
    background: #ffffff;
    text-align: right;
    color: #0f172a;
}

.item-price-input:focus {
    outline: none;
    border-color: #0284c7;
}

.item-price-input.has-custom-price {
    border-color: #0284c7;
    background: #f0f9ff;
    font-weight: 700;
    color: #0369a1;
}

.cart-item-store-row {
    margin-top: 8px;
    padding-top: 7px;
    border-top: 1px dashed #e2e8f0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.cart-store-select {
    flex: 1;
    min-width: 0;
    padding: 4px 8px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    background: #ffffff;
    cursor: pointer;
}

.cart-store-select:focus {
    outline: none;
    border-color: #dc2626;
}

.item-disc-inputs {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.item-disc-input {
    width: 64px;
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12px;
    background: #ffffff;
    text-align: right;
}

.item-disc-input:focus {
    outline: none;
    border-color: #dc2626;
}

.item-disc-type {
    padding: 4px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 11.5px;
    background: #ffffff;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
}

/* Form Panel */
.panel-content {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 10px 12px;
}

.pos-card-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 8px;
}

.pos-section-label {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.compact-input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
    background: #ffffff;
    color: #0f172a;
    transition: border-color 0.15s;
}

.compact-input:focus {
    outline: none;
    border-color: #dc2626;
}

.payment-pills {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}

.payment-pill {
    padding: 7px 6px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    color: #334155;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all 0.15s;
}

.payment-pill:hover:not(:disabled) {
    border-color: #94a3b8;
    background: #ffffff;
}

.payment-pill.active {
    background: #fef2f2;
    border-color: #dc2626;
    color: #dc2626;
}

.payment-pill:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

#posStatusPill.dropdown-toggle::after {
    display: none !important;
}

.pos-tax-toggle-group {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 999px;
    padding: 2px;
    gap: 2px;
}

.pos-tax-toggle-btn {
    border: none;
    background: transparent;
    color: #475569;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 4px 12px;
    border-radius: 999px;
    cursor: pointer;
    transition: all 0.15s ease;
    line-height: 1.25;
}

.pos-tax-toggle-btn:hover {
    color: #0f172a;
}

.pos-tax-toggle-btn.active[data-tax-mode="ttc"] {
    background: #1e40af;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(30, 64, 175, 0.35);
}

.pos-tax-toggle-btn.active[data-tax-mode="ht"] {
    background: #ea580c;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(234, 88, 12, 0.35);
}

.pos-delivery-toggle-group {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 3px;
}

.pos-delivery-toggle-btn {
    border: none;
    background: transparent;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 6px;
    border-radius: 7px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    white-space: nowrap;
    transition: all 0.15s ease;
    line-height: 1.2;
}

.pos-delivery-toggle-btn:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.7);
}

.pos-delivery-toggle-btn.active[data-delivery-type="store"] {
    background: #0f172a;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.3);
}

.pos-delivery-toggle-btn.active[data-delivery-type="our_team"] {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(37, 99, 235, 0.32);
}

.pos-delivery-toggle-btn.active[data-delivery-type="ozon"] {
    background: #d97706;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(217, 119, 6, 0.32);
}

.order-totals {
    background: #ffffff;
    padding: 10px 14px 12px;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}

.bill-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 12.5px;
    color: #64748b;
}

.bill-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px dashed #e2e8f0;
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}

.pay-btn {
    width: 100%;
    margin-top: 12px;
    padding: 13px 16px;
    background: #dc2626;
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.pay-btn:hover:not(:disabled) {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
}

.pay-btn:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}
</style>
@endpush

@section('content')
@php
    $currencySymbol = setting('currency_symbol', 'DH');
@endphp

<!-- Mobile 5-Step Wizard Switcher -->
<div class="pos-mobile-tabs" id="posMobileStepper">
    <button type="button" class="pos-mobile-tab active" data-step="1" onclick="goToMobileStep(1)">
        <span class="step-badge-num">1</span>
        <span>Produits</span>
    </button>
    <button type="button" class="pos-mobile-tab" data-step="2" onclick="goToMobileStep(2)">
        <span class="step-badge-num">2</span>
        <span>Panier (<span id="mobileCartBadge">0</span>)</span>
    </button>
    <button type="button" class="pos-mobile-tab" data-step="3" onclick="goToMobileStep(3)">
        <span class="step-badge-num">3</span>
        <span>Client</span>
    </button>
    <button type="button" class="pos-mobile-tab" data-step="4" onclick="goToMobileStep(4)">
        <span class="step-badge-num">4</span>
        <span>Paiement</span>
    </button>
    <button type="button" class="pos-mobile-tab" data-step="5" onclick="goToMobileStep(5)">
        <span class="step-badge-num">5</span>
        <span>Confirm.</span>
    </button>
</div>

<div class="pos-layout">
    <!-- Left: Catalog -->
    <div class="pos-products-area pos-pane mobile-active" id="paneCatalog">
        <div class="pos-header">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom ou SKU..." style="padding-right: 32px;">
                <button type="button" id="clearSearchBtn" onclick="clearSearchInput()" title="Effacer" style="display:none;position:absolute;right:8px;top:50%;transform:translateY(-50%);border:none;background:transparent;color:#94a3b8;cursor:pointer;padding:4px;">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>

            <button type="button" class="pos-icon-btn d-md-none" id="posMobileFiltersToggle" onclick="toggleMobilePosFilters()" title="Filtres">
                <i class="fas fa-sliders-h text-danger"></i>
                <span>Filtres</span>
            </button>

            <div class="pos-filters-collapse" id="posFiltersWrap">
                @if(isset($stores) && $stores->count() > 0)
                <div class="pos-store-picker" title="Point de vente / Dépôt actif">
                    <i class="fas fa-warehouse"></i>
                    <select id="posStoreSelect" class="pos-store-select">
                        <option value="" selected>Tous les magasins / dépôts</option>
                        @foreach($stores as $posStore)
                            <option value="{{ $posStore->id }}">
                                {{ $posStore->name }} ({{ $posStore->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(isset($brands) && $brands->count() > 0)
                <div class="pos-store-picker" title="Filtrer par marque">
                    <i class="fas fa-tag"></i>
                    <select id="brandFilter" class="pos-store-select">
                        <option value="">Toutes marques</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

            </div>

            <button type="button" id="resetFiltersBtn" class="pos-icon-btn" onclick="resetPosFilters()" style="display: none;" title="Réinitialiser les filtres">
                <i class="fas fa-rotate-left text-danger"></i>
            </button>

            <button type="button" class="pos-icon-btn" onclick="openHeldOrdersModal()" title="Tickets mis en attente">
                <i class="fas fa-pause-circle text-warning"></i>
                <span class="d-none d-sm-inline">Attente</span>
                <span class="badge bg-warning text-dark rounded-pill" id="heldCountBadge" style="font-size: 10px;">0</span>
            </button>

            <button type="button" class="pos-icon-btn d-none d-lg-inline-flex" onclick="togglePosFullscreen()" title="Plein écran">
                <i class="fas fa-expand" id="fullscreenIcon"></i>
            </button>
        </div>

        <div class="categories-wrapper">
            <div class="category-pills">
                <div class="category-pill category-btn active" data-category="">Tous les articles</div>
                @foreach($categories as $category)
                    <div class="category-pill category-btn" data-category="{{ $category->id }}">{{ $category->name }}</div>
                @endforeach
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            <div style="grid-column: 1/-1; text-align: center; padding-top: 90px; color: #94a3b8;">
                <i class="fas fa-circle-notch fa-spin fa-2x"></i>
                <p style="margin-top: 12px; font-weight: 500; font-size: 13px;">Chargement du catalogue...</p>
            </div>
        </div>
    </div>

    <!-- Middle: Cart Items + Per-Item Discount -->
    <div class="cart-items-panel pos-pane" id="paneCart">
        <div class="panel-header">
            <div class="panel-title">
                <i class="fas fa-shopping-cart"></i>
                Panier
                <span class="panel-badge" id="cartCount">0</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="holdCurrentCart()" title="Mettre ce panier en attente"
                        style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; height: 32px; padding: 0 10px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700;">
                    <i class="fas fa-pause"></i>
                    <span>Attente</span>
                </button>
                <button type="button" onclick="clearCartConfirm()" title="Vider le panier (F4)"
                        style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                </button>
            </div>
        </div>

        <div class="items-list" id="cartItems"></div>
    </div>

    <!-- Right: Client Info + Global Discount + Payment -->
    <div class="order-form-panel pos-pane" id="paneCheckout" data-mobile-step="3">
        <div class="panel-header">
            <div class="panel-title">
                <i class="fas fa-file-invoice-dollar"></i>
                <span id="paneCheckoutTitle">Client & Encaissement</span>
            </div>
        </div>

        <div class="panel-content">
            <!-- Step 3 on Mobile: Customer Selection + Editable Info -->
            <div class="pos-card-box" id="posStepClientBox">
                <div class="pos-section-label">
                    <span><i class="fas fa-user me-1 text-danger"></i> Client</span>
                    <button type="button" onclick="resetCustomerFields()" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px; color: #64748b;">
                        Nouveau / Passage
                    </button>
                </div>

                <input type="text" id="customerFilterInput" class="compact-input mb-2" placeholder="🔍 Filtrer client (nom, tél, ville)..." style="font-size: 12px; padding: 6px 10px; background: #f8fafc;">

                <select id="customerSelect" class="compact-input mb-2" style="cursor: pointer;">
                    <option value="">🚶 Client de passage (Walk-in)</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-email="{{ $customer->email }}"
                                data-phone="{{ $customer->phone }}"
                                data-city="{{ $customer->city }}"
                                data-credit-limit="{{ $customer->credit_limit }}"
                                data-balance="{{ $customer->current_balance }}">
                            {{ $customer->name }}{{ $customer->phone ? ' • ' . $customer->phone : '' }}{{ $customer->city ? ' (' . $customer->city . ')' : '' }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden" id="customerId" value="">
                <input type="hidden" id="customerEmail" value="">

                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" id="customerName" class="compact-input" value="Client de passage" placeholder="Nom du client *">
                    </div>
                    <div class="col-6">
                        <input type="tel" id="customerPhone" class="compact-input" placeholder="Téléphone (10 chiffres)" inputmode="numeric" maxlength="10" pattern="[0-9]{10}">
                    </div>
                    <div class="col-6">
                        <input type="text" id="customerCity" list="posCityList" class="compact-input" placeholder="Ville">
                        <datalist id="posCityList">
                            <option value="Casablanca">
                            <option value="Rabat">
                            <option value="Marrakech">
                            <option value="Tanger">
                            <option value="Fès">
                            <option value="Agadir">
                            <option value="Meknès">
                            <option value="Oujda">
                            <option value="Kénitra">
                            <option value="Tétouan">
                            <option value="Safi">
                            <option value="Mohammedia">
                            <option value="El Jadida">
                            <option value="Béni Mellal">
                            <option value="Nador">
                        </datalist>
                    </div>
                </div>

                <div class="form-check form-switch mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <input class="form-check-input" type="checkbox" id="saveCustomerCheckbox" style="cursor: pointer;">
                    <label class="form-check-label" for="saveCustomerCheckbox" style="font-size: 12px; font-weight: 600; color: #334155; cursor: pointer;">
                        Enregistrer / mettre à jour ce client
                    </label>
                </div>

                <!-- Credit Bar for Selected Registered Customer -->
                <div id="creditInfo" style="display: none; margin-top: 10px; padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px;">
                        <span style="color: #64748b; font-weight: 600;">Crédit client :</span>
                        <span id="creditUsed" style="font-weight: 700; color: #0f172a;"></span>
                    </div>
                    <div style="height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden;">
                        <div id="creditBar" style="height: 100%; background: #10b981; width: 0%; transition: width 0.3s;"></div>
                    </div>
                </div>
            </div>

            <!-- Step 4 on Mobile: Global Discount -->
            <div class="pos-card-box" id="posStepDiscountBox">
                <div class="pos-section-label">
                    <span><i class="fas fa-tag me-1 text-danger"></i> Remise globale</span>
                </div>
                <div style="display: flex; gap: 8px;">
                    <input type="number" id="discountAmount" class="compact-input" placeholder="0" min="0" step="0.01" style="flex: 1;">
                    <select id="discountType" class="compact-input" style="width: 78px; font-weight: 600;">
                        <option value="fixed" selected>{{ $currencySymbol }}</option>
                        <option value="percent">%</option>
                    </select>
                </div>
                <div id="globalDiscountSplitPreview" style="display: none; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #e2e8f0; font-size: 11.5px;"></div>
            </div>

            <!-- Step 4 on Mobile: Payment Method & Cash Tendered -->
            <div class="pos-card-box" id="posStepPaymentBox">
                <div class="pos-section-label">
                    <span><i class="fas fa-credit-card me-1 text-danger"></i> Mode de paiement</span>
                </div>
                <input type="hidden" id="paymentMethod" value="cash">
                <div class="payment-pills mb-2">
                    <button type="button" class="payment-pill active" data-method="cash" onclick="selectPaymentMethod('cash', this)">💵 Espèces</button>
                    <button type="button" class="payment-pill" data-method="card" onclick="selectPaymentMethod('card', this)">💳 Carte</button>
                    <button type="button" class="payment-pill" data-method="virement" onclick="selectPaymentMethod('virement', this)">🏛️ Virement</button>
                    <button type="button" class="payment-pill" data-method="other" onclick="selectPaymentMethod('other', this)">⚪ Autre</button>
                </div>

                <!-- Paiement Partiel (Acompte + Échéance) -->
                <div id="partialPaymentToggleWrap" class="mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="isPartialPayment" onchange="togglePartialPaymentBox()" style="cursor: pointer;">
                        <label class="form-check-label" for="isPartialPayment" style="font-size: 12px; font-weight: 700; color: #0f172a; cursor: pointer;">
                            <i class="fas fa-calendar-alt text-warning me-1"></i> Paiement partiel (Acompte + Échéance)
                        </label>
                    </div>
                    <div id="partialPaymentFields" style="display: none; margin-top: 8px; padding: 9px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="partialPaidAmount" style="font-size: 11px; color: #78350f; font-weight: 700; display: block; margin-bottom: 3px;">
                                    Payé maintenant ({{ $currencySymbol }})
                                </label>
                                <input type="number" id="partialPaidAmount" class="compact-input" placeholder="0.00" min="0" step="0.01" oninput="updatePartialPaymentSummary()" style="font-weight: 700; text-align: right;">
                            </div>
                            <div class="col-6">
                                <label for="paymentDueDate" style="font-size: 11px; color: #78350f; font-weight: 700; display: block; margin-bottom: 3px;">
                                    Date limite (Échéance) *
                                </label>
                                <input type="date" id="paymentDueDate" class="compact-input" min="{{ now()->toDateString() }}" value="{{ now()->addDays(15)->toDateString() }}" style="font-size: 12px;">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-1.5 border-top" style="border-color: #fde68a !important; font-size: 12px;">
                            <span style="color: #92400e; font-weight: 600;">Reste à payer :</span>
                            <strong id="partialRemainingDisplay" style="color: #dc2626; font-size: 13.5px;">{{ currency(0) }}</strong>
                        </div>
                        <div id="partialClientNotice" style="display: none; margin-top: 6px; padding: 6px 8px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; color: #991b1b; font-size: 11px; font-weight: 600;">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <span><i class="fas fa-exclamation-triangle me-1"></i> Nom réel et téléphone du client requis.</span>
                                <button type="button" onclick="goToMobileStep(3)" class="btn btn-link btn-sm p-0 text-danger fw-bold d-lg-none text-decoration-none" style="font-size: 11px;">Compléter</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="cashTenderedBox" class="mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <label for="amountReceived" style="font-size: 11.5px; color: #64748b; font-weight: 600; white-space: nowrap;">Montant reçu :</label>
                        <input type="number" id="amountReceived" class="compact-input" placeholder="0.00" min="0" step="0.01" style="width: 120px; text-align: right; font-weight: 700;">
                    </div>
                    <div class="cash-presets">
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset('exact')">Exact</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(50)">50</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(100)">100</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(200)">200</button>
                        <button type="button" class="cash-preset-btn" onclick="setCashPreset(500)">500</button>
                    </div>
                    <div id="changeRow" class="d-flex justify-content-between align-items-center mt-1" style="font-size: 12px; display: none !important;">
                        <span style="color: #64748b;">Monnaie à rendre :</span>
                        <strong id="changeAmount" style="color: #16a34a;">{{ currency(0) }}</strong>
                    </div>
                </div>

                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between gap-2" style="border-color: #f1f5f9 !important;">
                    <input type="hidden" id="orderStatus" value="delivered">
                    <div style="min-width: 0;">
                        <div style="font-size: 11.5px; color: #334155; font-weight: 700; line-height: 1.25;">
                            <i class="fas fa-truck-fast me-1 text-danger"></i> Statut commande
                        </div>
                        <div id="orderStatusStockHint" style="font-size: 10.5px; font-weight: 600; color: #16a34a; line-height: 1.25;">Stock déduit</div>
                    </div>
                    <div class="dropdown d-inline-block flex-shrink-0">
                        <button type="button"
                                class="brand-badge brand-badge-btn success status-pill-delivered dropdown-toggle"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="true"
                                data-bs-boundary="viewport"
                                data-bs-popper-config='{"strategy":"fixed"}'
                                aria-expanded="false"
                                id="posStatusPill"
                                title="Changer le statut de livraison">
                            <span class="status-dot dot-green" id="posStatusDot"></span>
                            <span id="posStatusLabel">Livrée</span>
                            <i class="fas fa-chevron-down ms-0.5" style="font-size: 0.6rem;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border rounded-3 p-1.5 status-dropdown-menu" aria-labelledby="posStatusPill" id="posStatusMenu">
                            <li class="dropdown-header text-uppercase text-muted fw-bold px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.05em;">Statut de livraison</li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between" data-status="pending" onclick="setPosOrderStatus('pending')">
                                    <span><span class="status-dot dot-amber me-1.5"></span> En attente</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between" data-status="en_demande" onclick="setPosOrderStatus('en_demande')">
                                    <span><span class="status-dot dot-blue me-1.5"></span> Sur Commande</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between" data-status="reserved" onclick="setPosOrderStatus('reserved')">
                                    <span><span class="status-dot dot-purple me-1.5"></span> Réservé</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between" data-status="processing" onclick="setPosOrderStatus('processing')">
                                    <span><span class="status-dot dot-blue me-1.5"></span> En cours</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between" data-status="shipped" onclick="setPosOrderStatus('shipped')">
                                    <span><span class="status-dot dot-teal me-1.5"></span> Expédiée</span>
                                </button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-2 py-1.5 px-2 d-flex align-items-center justify-content-between active" data-status="delivered" onclick="setPosOrderStatus('delivered')">
                                    <span><span class="status-dot dot-green me-1.5"></span> Livrée</span>
                                    <i class="fas fa-check text-success small"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <input type="hidden" id="orderDeliveryType" value="store">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span style="font-size: 11.5px; color: #334155; font-weight: 700; line-height: 1.25;">
                            <i class="fas fa-shipping-fast me-1 text-danger"></i> Type de livraison
                        </span>
                    </div>
                    <div class="pos-delivery-toggle-group" id="posDeliveryTypeGroup" role="group" aria-label="Type de livraison">
                        <button type="button" class="pos-delivery-toggle-btn active" data-delivery-type="store" onclick="setPosDeliveryType('store')">
                            <i class="fas fa-store"></i>
                            <span>Store</span>
                        </button>
                        <button type="button" class="pos-delivery-toggle-btn" data-delivery-type="our_team" onclick="setPosDeliveryType('our_team')">
                            <i class="fas fa-people-carry-box"></i>
                            <span>Our Team</span>
                        </button>
                        <button type="button" class="pos-delivery-toggle-btn" data-delivery-type="ozon" onclick="setPosDeliveryType('ozon')">
                            <i class="fas fa-box-open"></i>
                            <span>Ozon</span>
                        </button>
                    </div>
                </div>

                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between gap-2" style="border-color: #f1f5f9 !important;">
                    <input type="hidden" id="orderTaxMode" value="ht">
                    <div style="font-size: 11.5px; color: #334155; font-weight: 700;">
                        <i class="fas fa-tags me-1 text-danger"></i> Régime (TTC / HT)
                    </div>
                    <div class="pos-tax-toggle-group" id="posTaxModeGroup" role="group" aria-label="Régime TTC ou HT">
                        <button type="button" class="pos-tax-toggle-btn" data-tax-mode="ttc" onclick="setPosTaxMode('ttc')">TTC</button>
                        <button type="button" class="pos-tax-toggle-btn active" data-tax-mode="ht" onclick="setPosTaxMode('ht')">HT</button>
                    </div>
                </div>

                <input type="text" id="orderNotes" class="compact-input mt-2" placeholder="Note interne (optionnel)">
            </div>

            <!-- Step 5 on Mobile: Confirmation Summary -->
            <div class="pos-card-box" id="posStepConfirmBox">
                <div class="pos-section-label">
                    <span><i class="fas fa-clipboard-check me-1 text-danger"></i> Récapitulatif de la commande</span>
                    <button type="button" onclick="goToMobileStep(2)" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 11px; color: #dc2626;">
                        Modifier panier
                    </button>
                </div>
                <div id="posMobileConfirmSummary" style="font-size: 12.5px; color: #334155;"></div>
            </div>
        </div>

        <!-- Totals & Checkout Footer -->
        <div class="order-totals">
            <div class="bill-row">
                <span>Sous-total articles</span>
                <span id="subtotal" style="font-weight: 600; color: #0f172a;">{{ currency(0) }}</span>
            </div>
            <div class="bill-row" id="totalDiscountRow">
                <span>Remise totale</span>
                <span id="totalDiscountDisplay" style="font-weight: 600; color: #64748b;">{{ currency(0) }}</span>
            </div>
            <div class="bill-total">
                <span>Total</span>
                <span id="total" style="color: #dc2626;">{{ currency(0) }}</span>
            </div>

            <div class="d-none d-lg-block" style="margin-top: 8px; font-size: 10.5px; color: #64748b; text-align: center;">
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F2</kbd> Rechercher
                <span class="mx-1">•</span>
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F4</kbd> Vider
                <span class="mx-1">•</span>
                <kbd style="background: #f1f5f9; color: #334155; padding: 1px 5px; border-radius: 3px; border: 1px solid #cbd5e1;">F9</kbd> Encaisser
            </div>

            <button type="button" class="pay-btn" id="checkoutBtn" onclick="checkout()" disabled>
                <i class="fas fa-check-circle"></i>
                <span id="checkoutBtnLabel">Encaisser {{ currency(0) }}</span>
            </button>

            <button type="button" class="btn btn-outline-secondary w-100 mt-2 rounded-3 fw-semibold d-lg-none" onclick="goToMobileStep(4)" style="font-size: 13px; min-height: 42px;">
                <i class="fas fa-arrow-left me-1"></i> Retour à l'étape 4 (Paiement)
            </button>
        </div>
    </div>
</div>

<!-- Sticky Mobile Step Wizard Bottom Bar (Steps 1 to 4) -->
<div class="pos-mobile-bottom-bar" id="mobilePosBottomBar">
    <button type="button" id="mobileWizardPrevBtn" onclick="prevMobileStep()" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold flex-shrink-0" style="min-height: 40px; display: none;">
        <i class="fas fa-arrow-left me-1"></i> <span id="mobileWizardPrevLabel">Retour</span>
    </button>
    <div style="min-width: 0; flex: 1;">
        <div style="font-size: 11px; color: #94a3b8; line-height: 1.2;">
            <span id="mobileBottomStepHint">Étape 1/5</span> • <span id="mobileBottomCount">0</span> art.
        </div>
        <div id="mobileBottomTotal" style="font-size: 15.5px; font-weight: 800; color: #ffffff; line-height: 1.25;">
            {{ currency(0) }}
        </div>
    </div>
    <button type="button" id="mobileWizardNextBtn" onclick="nextMobileStep()" class="btn btn-sm btn-danger rounded-pill px-3.5 fw-bold flex-shrink-0" style="min-height: 40px;">
        <span id="mobileWizardNextLabel">Panier</span> <i class="fas fa-arrow-right ms-1"></i>
    </button>
</div>

@push('scripts')
@php
    $posStoresPayload = ($stores ?? collect())->map(function ($s) {
        return [
            'id' => (int) $s->id,
            'name' => $s->name,
            'code' => $s->code,
            'is_default' => (bool) $s->is_default,
        ];
    })->values();
@endphp
<script>
window.currencyConfig = {
    symbol: '{{ setting("currency_symbol", "DH") }}',
    position: '{{ setting("currency_position", "after") }}',
    decimals: {{ (int) setting("currency_decimals", 2) }},
    decimal_separator: '{{ setting("decimal_separator", ".") }}',
    thousands_separator: '{{ setting("thousands_separator", ",") }}',
    tax_rate: {{ floatval(setting("tax_rate", 20)) }} / 100
};

window.posStores = @json($posStoresPayload);

window.posState = {
    products: [],
    cart: [],
    currentTotal: 0,
    todayCount: {{ (int) ($todayPosCount ?? 0) }},
    todayTotal: {{ (float) ($todayPosTotal ?? 0) }},
    saveCustomerManualOverride: false
};

window.POS_HELD_STORAGE_KEY = 'wina_pos_held_orders_v1';

function getHeldOrders() {
    try {
        return JSON.parse(localStorage.getItem(window.POS_HELD_STORAGE_KEY) || '[]');
    } catch {
        return [];
    }
}

function saveHeldOrders(list) {
    localStorage.setItem(window.POS_HELD_STORAGE_KEY, JSON.stringify(list));
    refreshHeldBadge();
}

function refreshHeldBadge() {
    const badge = document.getElementById('heldCountBadge');
    if (badge) badge.textContent = getHeldOrders().length;
}

window.togglePosFullscreen = function() {
    const icon = document.getElementById('fullscreenIcon');
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen?.();
        if (icon) icon.className = 'fas fa-compress';
    } else {
        document.exitFullscreen?.();
        if (icon) icon.className = 'fas fa-expand';
    }
};

window.setCashPreset = function(val) {
    const input = document.getElementById('amountReceived');
    if (!input) return;
    input.value = val === 'exact'
        ? (window.posState.currentTotal > 0 ? window.posState.currentTotal.toFixed(2) : '')
        : Number(val).toFixed(2);
    updateCashChange();
    updateMobileBottomBar();
};

window.holdCurrentCart = function() {
    if (!window.posState.cart.length) {
        Swal.fire({ icon: 'info', title: 'Panier vide', text: 'Ajoutez au moins un produit avant de mettre en attente.', timer: 1600, showConfirmButton: false });
        return;
    }
    const list = getHeldOrders();
    list.unshift({
        id: Date.now(),
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        customerName: document.getElementById('customerName')?.value || 'Client de passage',
        customerPhone: document.getElementById('customerPhone')?.value || '',
        customerCity: document.getElementById('customerCity')?.value || '',
        customerId: document.getElementById('customerId')?.value || '',
        discountAmount: document.getElementById('discountAmount')?.value || '',
        discountType: document.getElementById('discountType')?.value || 'fixed',
        isPartialPayment: !!document.getElementById('isPartialPayment')?.checked,
        partialPaidAmount: document.getElementById('partialPaidAmount')?.value || '',
        paymentDueDate: document.getElementById('paymentDueDate')?.value || '',
        orderStatus: document.getElementById('orderStatus')?.value || 'delivered',
        deliveryType: document.getElementById('orderDeliveryType')?.value || 'store',
        taxMode: document.getElementById('orderTaxMode')?.value || 'ht',
        notes: document.getElementById('orderNotes')?.value || '',
        total: window.posState.currentTotal,
        cart: window.posState.cart
    });
    saveHeldOrders(list);

    window.posState.cart = [];
    document.getElementById('discountAmount').value = '';
    document.getElementById('discountType').value = 'fixed';
    if (document.getElementById('isPartialPayment')) document.getElementById('isPartialPayment').checked = false;
    if (document.getElementById('partialPaidAmount')) document.getElementById('partialPaidAmount').value = '';
    if (document.getElementById('paymentDueDate')) document.getElementById('paymentDueDate').value = '{{ now()->addDays(15)->toDateString() }}';
    togglePartialPaymentBox();
    setPosOrderStatus('delivered');
    setPosDeliveryType('store');
    setPosTaxMode('ht');
    document.getElementById('orderNotes').value = '';
    resetCustomerFields();
    renderCart();
    renderProducts();

    Swal.fire({ icon: 'success', title: 'Ticket mis en attente', timer: 1200, showConfirmButton: false });
};

window.openHeldOrdersModal = function() {
    const list = getHeldOrders();
    if (!list.length) {
        Swal.fire({ icon: 'info', title: 'Aucun ticket en attente', text: 'Les paniers mis en pause apparaîtront ici.' });
        return;
    }
    const rows = list.map((h, idx) => `
        <div class="d-flex align-items-center justify-content-between p-2 mb-2 border rounded bg-light text-start">
            <div>
                <div class="fw-bold text-dark" style="font-size:13px;">${escapeHtml(h.customerName)} <span class="text-muted fw-normal">(${h.time})</span></div>
                <div class="text-muted" style="font-size:11.5px;">${h.cart.length} article(s) • <strong class="text-danger">${formatCurrency(h.total)}</strong></div>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-danger" onclick="resumeHeldOrder(${idx})">Reprendre</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deleteHeldOrder(${idx})"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `).join('');

    Swal.fire({
        title: 'Tickets en attente',
        html: `<div style="max-height:320px;overflow-y:auto;">${rows}</div>`,
        showConfirmButton: false,
        showCloseButton: true
    });
};

window.resumeHeldOrder = function(idx) {
    const list = getHeldOrders();
    const held = list[idx];
    if (!held) return;
    list.splice(idx, 1);
    saveHeldOrders(list);

    window.posState.cart = (held.cart || []).map(item => {
        const prod = (window.posState.products || []).find(p => p.id === item.product_id);
        const cloned = { ...item };
        if (!Array.isArray(cloned.stores)) {
            cloned.stores = prod && Array.isArray(prod.stores) ? prod.stores.map(s => ({ ...s })) : [];
        }
        if (cloned.global_stock === undefined || cloned.global_stock === null) {
            cloned.global_stock = prod ? (parseInt(prod.global_stock ?? prod.stock, 10) || 0) : (parseInt(cloned.stock, 10) || 0);
        }
        if (cloned.store_id === undefined) {
            cloned.store_id = resolveDefaultItemStoreId(prod || cloned);
        }
        cloned.stock = getItemMaxStock(cloned);
        return cloned;
    });
    if (held.customerId) {
        document.getElementById('customerSelect').value = held.customerId;
        document.getElementById('customerSelect').dispatchEvent(new Event('change'));
    } else {
        resetCustomerFields();
        document.getElementById('customerName').value = held.customerName || 'Client de passage';
        document.getElementById('customerPhone').value = held.customerPhone || '';
        document.getElementById('customerCity').value = held.customerCity || '';
    }
    document.getElementById('discountAmount').value = held.discountAmount || '';
    document.getElementById('discountType').value = held.discountType || 'fixed';
    if (document.getElementById('isPartialPayment')) document.getElementById('isPartialPayment').checked = !!held.isPartialPayment;
    if (document.getElementById('partialPaidAmount')) document.getElementById('partialPaidAmount').value = held.partialPaidAmount || '';
    if (document.getElementById('paymentDueDate') && held.paymentDueDate) document.getElementById('paymentDueDate').value = held.paymentDueDate;
    togglePartialPaymentBox();
    setPosOrderStatus(held.orderStatus || 'delivered');
    setPosDeliveryType(held.deliveryType || 'store');
    setPosTaxMode(held.taxMode || 'ht');
    document.getElementById('orderNotes').value = held.notes || '';
    renderCart();
    renderProducts();
    Swal.close();
};

window.deleteHeldOrder = function(idx) {
    const list = getHeldOrders();
    list.splice(idx, 1);
    saveHeldOrders(list);
    openHeldOrdersModal();
};

window.formatCurrency = function(amount) {
    const cfg = window.currencyConfig;
    const num = Number(amount || 0);
    const formatted = num.toFixed(cfg.decimals)
        .replace(/\d(?=(\d{3})+\.)/g, '$&' + cfg.thousands_separator)
        .replace('.', cfg.decimal_separator);
    return cfg.position === 'before' ? cfg.symbol + formatted : formatted + ' ' + cfg.symbol;
};

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, s => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[s]));
}

function normalizePosPhone(raw) {
    let digits = String(raw || '').replace(/\D/g, '');
    if (digits.startsWith('212') && digits.length === 12) {
        digits = '0' + digits.slice(3);
    } else if (digits.startsWith('00212') && digits.length === 14) {
        digits = '0' + digits.slice(5);
    }
    return digits.slice(0, 10);
}

window.goToMobileStep = function(step) {
    const targetStep = Math.max(1, Math.min(5, parseInt(step, 10) || 1));
    const cart = window.posState?.cart || [];

    if (targetStep > 1 && cart.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Panier vide', text: 'Sélectionnez au moins un produit à l\'étape 1.' });
        return;
    }

    if (targetStep >= 4) {
        const cName = (document.getElementById('customerName')?.value || '').trim();
        if (!cName) {
            Swal.fire({ icon: 'warning', title: 'Nom du client requis', text: 'Veuillez renseigner le nom du client à l\'étape 3.' });
            if (window.posState.mobileStep !== 3) window.goToMobileStep(3);
            return;
        }
        const cPhone = (document.getElementById('customerPhone')?.value || '').trim();
        if (cPhone !== '' && !/^\d{10}$/.test(cPhone)) {
            Swal.fire({ icon: 'warning', title: 'Téléphone invalide', text: 'Le numéro de téléphone doit contenir exactement 10 chiffres.' });
            if (window.posState.mobileStep !== 3) window.goToMobileStep(3);
            document.getElementById('customerPhone')?.focus();
            return;
        }
    }

    if (targetStep === 5 && document.getElementById('isPartialPayment')?.checked) {
        if (!validatePartialClientInfo()) {
            return;
        }
        const total = window.posState?.currentTotal || 0;
        const paidNow = parseFloat(document.getElementById('partialPaidAmount')?.value) || 0;
        const dueDate = (document.getElementById('paymentDueDate')?.value || '').trim();
        if (paidNow < 0 || paidNow >= total) {
            Swal.fire({ icon: 'warning', title: 'Acompte invalide', text: 'Le montant payé maintenant doit être inférieur au total.' });
            if (window.posState.mobileStep !== 4) window.goToMobileStep(4);
            return;
        }
        if (!dueDate) {
            Swal.fire({ icon: 'warning', title: 'Échéance requise', text: 'Veuillez indiquer la date limite pour le reste à payer.' });
            if (window.posState.mobileStep !== 4) window.goToMobileStep(4);
            return;
        }
    }

    window.posState.mobileStep = targetStep;

    // Update top 5-step progress tabs
    document.querySelectorAll('#posMobileStepper .pos-mobile-tab').forEach(tab => {
        const s = parseInt(tab.dataset.step, 10);
        tab.classList.toggle('active', s === targetStep);
        tab.classList.toggle('done', s < targetStep);
        const badge = tab.querySelector('.step-badge-num');
        if (badge) {
            badge.innerHTML = s < targetStep ? '<i class="fas fa-check" style="font-size:9px;"></i>' : String(s);
        }
    });

    // Switch visible pane & checkout sub-step
    document.querySelectorAll('.pos-pane').forEach(el => el.classList.remove('mobile-active'));
    const checkoutPane = document.getElementById('paneCheckout');
    const checkoutTitle = document.getElementById('paneCheckoutTitle');

    if (targetStep === 1) {
        document.getElementById('paneCatalog')?.classList.add('mobile-active');
    } else if (targetStep === 2) {
        document.getElementById('paneCart')?.classList.add('mobile-active');
    } else {
        if (checkoutPane) {
            checkoutPane.setAttribute('data-mobile-step', String(targetStep));
            checkoutPane.classList.add('mobile-active');
        }
        if (checkoutTitle) {
            if (targetStep === 3) checkoutTitle.textContent = 'Étape 3 : Informations Client';
            else if (targetStep === 4) checkoutTitle.textContent = 'Étape 4 : Remise & Paiement';
            else checkoutTitle.textContent = 'Étape 5 : Confirmation & Encaissement';
        }
        if (targetStep === 5) {
            renderMobileConfirmSummary();
        }
    }

    updateMobileBottomBar();
    if (window.innerWidth < 1024) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

window.nextMobileStep = function() {
    const cur = window.posState?.mobileStep || 1;
    if (cur < 5) window.goToMobileStep(cur + 1);
};

window.prevMobileStep = function() {
    const cur = window.posState?.mobileStep || 1;
    if (cur > 1) window.goToMobileStep(cur - 1);
};

function switchMobilePane(paneId) {
    if (paneId === 'paneCatalog') window.goToMobileStep(1);
    else if (paneId === 'paneCart') window.goToMobileStep(2);
    else window.goToMobileStep(3);
}

function renderMobileConfirmSummary() {
    const box = document.getElementById('posMobileConfirmSummary');
    if (!box) return;

    const cart = window.posState?.cart || [];
    const cName = (document.getElementById('customerName')?.value || 'Client de passage').trim();
    const cPhone = (document.getElementById('customerPhone')?.value || '').trim();
    const cCity = (document.getElementById('customerCity')?.value || '').trim();
    const method = document.getElementById('paymentMethod')?.value || 'cash';
    const methodLabels = {
        cash: '💵 Espèces',
        card: '💳 Carte bancaire',
        virement: '🏛️ Virement bancaire',
        credit: '🏦 À crédit',
        other: '⚪ Autre'
    };
    const isPartial = !!document.getElementById('isPartialPayment')?.checked && method !== 'credit';
    const partialPaidNow = Math.max(0, parseFloat(document.getElementById('partialPaidAmount')?.value) || 0);
    const partialDueDate = (document.getElementById('paymentDueDate')?.value || '').trim();
    const orderStatus = document.getElementById('orderStatus')?.value || 'delivered';
    const orderStatusBadgeHtml = formatPosStatusBadgeHtml(orderStatus);
    const orderDeliveryType = document.getElementById('orderDeliveryType')?.value || 'store';
    const orderDeliveryBadgeHtml = formatPosDeliveryBadgeHtml(orderDeliveryType);
    const orderTaxMode = document.getElementById('orderTaxMode')?.value || 'ht';
    const orderTaxBadgeHtml = formatPosTaxBadgeHtml(orderTaxMode);
    const notes = (document.getElementById('orderNotes')?.value || '').trim();
    const received = parseFloat(document.getElementById('amountReceived')?.value);
    const pricing = computeCartPricing();
    const total = pricing.total;

    const partialHtml = isPartial
        ? `<div class="mt-1 small" style="color:#92400e;">
               Paiement partiel — Payé : <strong>${formatCurrency(partialPaidNow)}</strong> • Reste : <strong class="text-danger">${formatCurrency(Math.max(0, total - partialPaidNow))}</strong>
               ${partialDueDate ? ` • Échéance : <strong>${escapeHtml(partialDueDate.split('-').reverse().join('/'))}</strong>` : ''}
           </div>`
        : '';

    const itemsHtml = pricing.lines.map(line => {
        const item = line.item;
        const isCustomDiff = line.hasCustomPrice && Math.abs(line.basePrice - (parseFloat(item.price) || 0)) >= 0.005;
        const customBadge = isCustomDiff
            ? '<span class="badge bg-primary-subtle text-primary me-1" style="font-size:9.5px;">Perso</span>'
            : '';
        let unitHtml = '';
        if (line.totalLineDiscount > 0) {
            unitHtml = `<div style="font-size:10.5px;color:#64748b;">${customBadge}<del>${formatCurrency(line.basePrice)}</del> <strong style="color:#16a34a;">${formatCurrency(line.finalUnitPrice)}/u</strong></div>`;
        } else if (isCustomDiff) {
            unitHtml = `<div style="font-size:10.5px;color:#0284c7;">Prix perso : <strong>${formatCurrency(line.basePrice)}/u</strong></div>`;
        }
        const storeName = getItemStoreName(item);
        const storeHtml = storeName
            ? `<div style="font-size:10.5px;color:#64748b;"><i class="fas fa-warehouse me-1"></i>${escapeHtml(storeName)}</div>`
            : '';
        return `<div class="d-flex justify-content-between align-items-center py-1 border-bottom" style="border-color:#f1f5f9 !important;">
            <div class="text-truncate me-2">
                <span><strong>${item.quantity}×</strong> ${escapeHtml(item.name)}</span>
                ${storeHtml}
                ${unitHtml}
            </div>
            <strong class="flex-shrink-0">${formatCurrency(line.finalNet)}</strong>
        </div>`;
    }).join('');

    const changeHtml = (method === 'cash' && !isNaN(received) && received > 0)
        ? `<div class="d-flex justify-content-between mt-1">
               <span class="text-muted">Reçu : <strong>${formatCurrency(received)}</strong></span>
               <span class="${received >= total ? 'text-success' : 'text-danger'} fw-bold">
                   ${received >= total ? 'Monnaie : ' + formatCurrency(received - total) : 'Manque : ' + formatCurrency(total - received)}
               </span>
           </div>`
        : '';

    box.innerHTML = `
        <div class="p-2.5 rounded-3 mb-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold"><i class="fas fa-user me-1"></i>Client</span>
                <button type="button" onclick="goToMobileStep(3)" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:11px;">Modifier</button>
            </div>
            <div class="fw-bold text-dark">${escapeHtml(cName)}</div>
            ${(cPhone || cCity) ? `<div class="text-muted small">${escapeHtml([cPhone, cCity].filter(Boolean).join(' • '))}</div>` : ''}
        </div>
        <div class="p-2.5 rounded-3 mb-2" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-semibold"><i class="fas fa-credit-card me-1"></i>Paiement</span>
                <button type="button" onclick="goToMobileStep(4)" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:11px;">Modifier</button>
            </div>
            <div class="fw-bold text-dark">${methodLabels[method] || method}</div>
            <div class="small mt-1 d-flex align-items-center gap-1.5 flex-wrap" style="color:#334155;"><span>Statut :</span> ${orderStatusBadgeHtml} <span class="ms-1">Livraison :</span> ${orderDeliveryBadgeHtml} <span class="ms-1">Régime :</span> ${orderTaxBadgeHtml}</div>
            ${partialHtml}
            ${changeHtml}
            ${notes ? `<div class="text-muted small mt-1"><i class="fas fa-sticky-note me-1"></i>${escapeHtml(notes)}</div>` : ''}
        </div>
        <div class="p-2.5 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <div class="text-muted small fw-semibold mb-1"><i class="fas fa-shopping-basket me-1"></i>Articles (${cart.reduce((s, i) => s + i.quantity, 0)})</div>
            ${itemsHtml}
        </div>
    `;
}

function toggleMobilePosFilters() {
    const wrap = document.getElementById('posFiltersWrap');
    const btn = document.getElementById('posMobileFiltersToggle');
    if (!wrap) return;
    const isCollapsed = wrap.classList.toggle('collapsed-mobile');
    if (btn) {
        btn.style.borderColor = !isCollapsed ? '#dc2626' : '#e2e8f0';
        btn.style.background = !isCollapsed ? '#fef2f2' : '#ffffff';
    }
}

function updateMobileBottomBar() {
    const bar = document.getElementById('mobilePosBottomBar');
    if (!bar) return;
    const cart = window.posState?.cart || [];
    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    const step = window.posState?.mobileStep || 1;

    const countEl = document.getElementById('mobileBottomCount');
    const totalEl = document.getElementById('mobileBottomTotal');
    const stepHintEl = document.getElementById('mobileBottomStepHint');
    const prevBtn = document.getElementById('mobileWizardPrevBtn');
    const prevLabel = document.getElementById('mobileWizardPrevLabel');
    const nextBtn = document.getElementById('mobileWizardNextBtn');
    const nextLabel = document.getElementById('mobileWizardNextLabel');

    if (countEl) countEl.textContent = totalItems;
    if (totalEl) totalEl.textContent = formatCurrency(window.posState?.currentTotal || 0);
    if (stepHintEl) stepHintEl.textContent = `Étape ${step}/5`;

    const prevNames = { 2: 'Produits', 3: 'Panier', 4: 'Client' };
    const nextNames = { 1: '2. Panier', 2: '3. Client', 3: '4. Paiement', 4: '5. Confirmation' };

    if (prevBtn) {
        prevBtn.style.display = step > 1 && step < 5 ? '' : 'none';
        if (prevLabel && prevNames[step]) prevLabel.textContent = prevNames[step];
    }
    if (nextBtn) {
        nextBtn.disabled = totalItems === 0;
        if (nextLabel && nextNames[step]) nextLabel.textContent = nextNames[step];
    }

    if (step === 5) {
        renderMobileConfirmSummary();
    }

    if ((totalItems > 0 || step > 1) && step < 5) {
        bar.classList.add('visible');
    } else {
        bar.classList.remove('visible');
    }
}

function resolveDefaultItemStoreId(product) {
    const posList = (Array.isArray(product?.stores) ? product.stores : [])
        .filter(s => (parseInt(s.quantity, 10) || 0) > 0);
    const selectedFilterId = document.getElementById('posStoreSelect')?.value || '';
    const allStores = window.posStores || [];
    const defaultStoreId = allStores.find(s => s.is_default)?.id || allStores[0]?.id || null;

    if (posList.length > 0) {
        if (selectedFilterId && posList.some(s => Number(s.id) === Number(selectedFilterId))) {
            return Number(selectedFilterId);
        }
        if (defaultStoreId && posList.some(s => Number(s.id) === Number(defaultStoreId))) {
            return Number(defaultStoreId);
        }
        const sorted = [...posList].sort((a, b) => (parseInt(b.quantity, 10) || 0) - (parseInt(a.quantity, 10) || 0));
        return Number(sorted[0].id);
    }

    if (selectedFilterId) return Number(selectedFilterId);
    return defaultStoreId ? Number(defaultStoreId) : null;
}

function getItemMaxStock(item) {
    if (Array.isArray(item.stores) && item.stores.length > 0) {
        const row = item.store_id !== null && item.store_id !== undefined
            ? item.stores.find(s => Number(s.id) === Number(item.store_id))
            : null;
        if (row) {
            return parseInt(row.quantity, 10) || 0;
        }
        return parseInt(item.stock, 10) || 0;
    }
    return parseInt(item.global_stock ?? item.stock, 10) || 0;
}

function getItemStoreName(item) {
    if (item.store_id === null || item.store_id === undefined || item.store_id === '') {
        return '';
    }
    const targetId = Number(item.store_id);
    const fromItem = (Array.isArray(item.stores) ? item.stores : []).find(s => Number(s.id) === targetId);
    if (fromItem?.name) return fromItem.name;
    const fromGlobal = (window.posStores || []).find(s => Number(s.id) === targetId);
    return fromGlobal?.name || '';
}

function renderCartStoreOptions(item) {
    const mergedStores = [];
    const seen = new Set();

    (window.posStores || []).forEach(st => {
        const sid = Number(st.id);
        if (!seen.has(sid)) {
            seen.add(sid);
            mergedStores.push({ id: sid, name: st.name });
        }
    });
    (Array.isArray(item.stores) ? item.stores : []).forEach(st => {
        const sid = Number(st.id);
        if (!seen.has(sid)) {
            seen.add(sid);
            mergedStores.push({ id: sid, name: st.name });
        }
    });

    const hasStoreAlloc = Array.isArray(item.stores) && item.stores.length > 0;
    if (hasStoreAlloc) {
        return mergedStores.map(st => {
            const found = item.stores.find(s => Number(s.id) === st.id);
            const qty = parseInt(found?.quantity, 10) || 0;
            const selected = Number(item.store_id) === st.id ? 'selected' : '';
            const disabled = qty <= 0 ? 'disabled' : '';
            return `<option value="${st.id}" ${selected} ${disabled}>${escapeHtml(st.name)} (${qty})</option>`;
        }).join('');
    }

    const globalQty = parseInt(item.global_stock ?? item.stock, 10) || 0;
    if (mergedStores.length > 0) {
        return mergedStores.map(st => {
            const selected = Number(item.store_id) === st.id ? 'selected' : '';
            return `<option value="${st.id}" ${selected}>${escapeHtml(st.name)} (Stock global : ${globalQty})</option>`;
        }).join('');
    }

    return `<option value="">Stock global (${globalQty})</option>`;
}

function hasItemCustomPrice(item) {
    if (item.custom_price === null || item.custom_price === undefined || item.custom_price === '') {
        return false;
    }
    const parsed = parseFloat(item.custom_price);
    return !isNaN(parsed) && parsed >= 0;
}

function getItemBasePrice(item) {
    if (hasItemCustomPrice(item)) {
        return +parseFloat(item.custom_price).toFixed(2);
    }
    return +(parseFloat(item.price) || 0).toFixed(2);
}

function computeItemLine(item) {
    const unitPrice = getItemBasePrice(item);
    const qty = parseInt(item.quantity, 10) || 1;
    const gross = +(unitPrice * qty).toFixed(2);
    const discVal = parseFloat(item.discount_amount) || 0;
    const discType = item.discount_type || 'fixed';

    let lineDiscount = 0;
    if (discVal > 0 && gross > 0) {
        if (discType === 'percent') {
            lineDiscount = +((gross * Math.min(discVal, 100)) / 100).toFixed(2);
        } else {
            lineDiscount = +Math.min(discVal, gross).toFixed(2);
        }
    }
    const net = +Math.max(0, gross - lineDiscount).toFixed(2);
    return { basePrice: unitPrice, gross, lineDiscount, net };
}

function computeCartPricing() {
    const cart = window.posState?.cart || [];
    let grossSubtotal = 0;
    let itemDiscountsTotal = 0;
    let itemsNetSubtotal = 0;

    const lines = cart.map(item => {
        const { basePrice, gross, lineDiscount, net } = computeItemLine(item);
        grossSubtotal = +(grossSubtotal + gross).toFixed(2);
        itemDiscountsTotal = +(itemDiscountsTotal + lineDiscount).toFixed(2);
        itemsNetSubtotal = +(itemsNetSubtotal + net).toFixed(2);
        return {
            item,
            basePrice,
            hasCustomPrice: hasItemCustomPrice(item),
            gross,
            itemDiscount: lineDiscount,
            netBeforeGlobal: net,
            globalShare: 0,
            totalLineDiscount: lineDiscount,
            finalNet: net,
            finalUnitPrice: basePrice,
        };
    });

    const discountVal = parseFloat(document.getElementById('discountAmount')?.value) || 0;
    const discountType = document.getElementById('discountType')?.value || 'fixed';
    let globalDiscount = 0;
    if (discountVal > 0 && itemsNetSubtotal > 0) {
        globalDiscount = discountType === 'percent'
            ? +((itemsNetSubtotal * Math.min(discountVal, 100)) / 100).toFixed(2)
            : +Math.min(discountVal, itemsNetSubtotal).toFixed(2);
    }

    if (globalDiscount > 0 && itemsNetSubtotal > 0) {
        let lastEligibleIdx = -1;
        lines.forEach((line, idx) => {
            if (line.netBeforeGlobal > 0) {
                lastEligibleIdx = idx;
            }
        });

        let allocated = 0;
        lines.forEach((line, idx) => {
            if (line.netBeforeGlobal <= 0) return;

            let globalShare = 0;
            if (idx === lastEligibleIdx) {
                globalShare = +Math.min(line.netBeforeGlobal, Math.max(0, globalDiscount - allocated)).toFixed(2);
            } else {
                globalShare = +Math.min(line.netBeforeGlobal, globalDiscount * (line.netBeforeGlobal / itemsNetSubtotal)).toFixed(2);
                allocated = +(allocated + globalShare).toFixed(2);
            }

            const qty = parseInt(line.item.quantity, 10) || 1;
            const finalNet = +Math.max(0, line.netBeforeGlobal - globalShare).toFixed(2);
            line.globalShare = globalShare;
            line.totalLineDiscount = +(line.itemDiscount + globalShare).toFixed(2);
            line.finalNet = finalNet;
            line.finalUnitPrice = qty > 0 ? +(finalNet / qty).toFixed(2) : line.basePrice;
        });
    } else {
        lines.forEach(line => {
            const qty = parseInt(line.item.quantity, 10) || 1;
            line.finalUnitPrice = qty > 0 ? +(line.finalNet / qty).toFixed(2) : line.basePrice;
        });
    }

    const total = +Math.max(0, itemsNetSubtotal - globalDiscount).toFixed(2);
    const totalDiscount = +(itemDiscountsTotal + globalDiscount).toFixed(2);
    const linesById = {};
    lines.forEach(line => {
        linesById[line.item.product_id] = line;
    });

    return {
        lines,
        linesById,
        grossSubtotal,
        itemDiscountsTotal,
        itemsNetSubtotal,
        globalDiscount,
        totalDiscount,
        total,
    };
}

window.clearSearchInput = function() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        input.focus();
    }
    loadProducts();
};

window.resetPosFilters = function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    const brandFilter = document.getElementById('brandFilter');
    if (brandFilter) brandFilter.value = '';
    const stockFilter = document.getElementById('stockFilter');
    if (stockFilter) stockFilter.value = 'all';
    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.category-btn[data-category=""]')?.classList.add('active');
    loadProducts();
};

function updateFilterUiState() {
    const query = (document.getElementById('searchInput')?.value || '').trim();
    const categoryId = document.querySelector('.category-btn.active')?.dataset.category || '';
    const brandId = document.getElementById('brandFilter')?.value || '';
    const stockFilter = document.getElementById('stockFilter')?.value || 'all';

    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) clearBtn.style.display = query !== '' ? 'block' : 'none';

    const resetBtn = document.getElementById('resetFiltersBtn');
    if (resetBtn) {
        const hasFilters = query !== '' || categoryId !== '' || brandId !== '' || stockFilter !== 'all';
        resetBtn.style.display = hasFilters ? 'inline-flex' : 'none';
    }
}

function loadProducts() {
    const query = (document.getElementById('searchInput')?.value || '').trim();
    const categoryId = document.querySelector('.category-btn.active')?.dataset.category || '';
    const brandId = document.getElementById('brandFilter')?.value || '';
    const stockFilter = document.getElementById('stockFilter')?.value || 'all';
    const storeId = document.getElementById('posStoreSelect')?.value || '';

    updateFilterUiState();

    let url = `{{ route('pos.search') }}?query=${encodeURIComponent(query)}&stock_filter=${encodeURIComponent(stockFilter)}`;
    if (categoryId) url += `&category_id=${encodeURIComponent(categoryId)}`;
    if (brandId) url += `&brand_id=${encodeURIComponent(brandId)}`;
    if (storeId) url += `&store_id=${encodeURIComponent(storeId)}`;

    fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
        .then(res => {
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(data => {
            window.posState.products = data;
            renderProducts();
        })
        .catch(err => {
            const grid = document.getElementById('productsGrid');
            if (grid) {
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding-top:60px;color:#dc2626;">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Erreur de chargement : ${escapeHtml(err.message)}</p>
                </div>`;
            }
        });
}

function renderProducts() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    const products = window.posState.products;
    if (!products.length) {
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding-top:70px;color:#94a3b8;">
            <i class="fas fa-search fa-3x mb-3" style="opacity:0.35;"></i>
            <p class="fw-semibold">Aucun produit correspondant</p>
        </div>`;
        return;
    }

    const fallbackSrc = '{{ asset("images/camera/cat_cameras.jpg") }}';
    grid.innerHTML = products.map(product => {
        const cartItem = window.posState.cart.find(i => i.product_id === product.id);
        const qtyBadge = cartItem ? `<div class="qty-badge">${cartItem.quantity}</div>` : '';
        const imgSrc = resolvePosProductImg(product.image);

        const isOut = product.stock <= 0;
        let stockBadge = '';
        if (isOut) {
            stockBadge = `<div class="stock-badge out-of-stock"><i class="fas fa-times-circle"></i> Épuisé</div>`;
        } else if (product.stock <= 5) {
            stockBadge = `<div class="stock-badge low-stock"><i class="fas fa-exclamation-triangle"></i> ${product.stock}</div>`;
        } else {
            stockBadge = `<div class="stock-badge in-stock"><i class="fas fa-check-circle"></i> ${product.stock}</div>`;
        }

        const hasSale = product.sale_price && parseFloat(product.sale_price) < parseFloat(product.regular_price);
        const saleBadge = hasSale ? `<div class="sale-badge">PROMO</div>` : '';
        const priceHtml = hasSale
            ? `<span class="card-price sale">${formatCurrency(product.sale_price)}</span>
               <span class="card-original-price">${formatCurrency(product.regular_price)}</span>`
            : `<span class="card-price">${formatCurrency(product.price)}</span>`;

        const selectedStoreId = document.getElementById('posStoreSelect')?.value || '';
        const storesList = Array.isArray(product.stores) ? product.stores : [];
        let storeTagsHtml = '';
        if (storesList.length > 0) {
            storeTagsHtml = `<div class="card-stores-tags">` + storesList.map(st => {
                const isSelected = selectedStoreId && String(st.id) === String(selectedStoreId);
                return `<span class="store-tag-pill ${isSelected ? 'active-store' : ''}" title="${escapeHtml(st.name)} : ${st.quantity} en stock">
                    <i class="fas fa-store" style="font-size:8px;"></i> ${escapeHtml(st.name)} (${st.quantity})
                </span>`;
            }).join('') + `</div>`;
        } else if (product.global_stock > 0) {
            storeTagsHtml = `<div class="card-stores-tags"><span class="store-tag-pill global-fallback" title="Stock global non réparti par dépôt"><i class="fas fa-cubes" style="font-size:8px;"></i> Stock global (${product.global_stock})</span></div>`;
        }

        return `
            <div class="pos-product-card ${isOut ? 'out-of-stock-card' : ''}"
                 onclick="${isOut ? '' : `addToCart(${product.id})`}"
                 aria-disabled="${isOut ? 'true' : 'false'}"
                 title="${escapeHtml(product.name)}${isOut ? ' — Rupture de stock' : ''}">
                ${qtyBadge}
                ${saleBadge}
                <div class="card-img-wrapper">
                    <img src="${imgSrc}" class="card-img" alt="${escapeHtml(product.name)}" loading="lazy" onerror="this.onerror=null;this.src='${fallbackSrc}';">
                    ${!isOut ? `<div class="overlay-add"><i class="fas fa-plus"></i></div>` : ''}
                </div>
                ${stockBadge}
                <div class="card-content">
                    <div class="card-title">${escapeHtml(product.name)}</div>
                    ${product.sku ? `<div class="card-sku">${escapeHtml(product.sku)}</div>` : ''}
                    ${storeTagsHtml}
                    <div class="card-footer">${priceHtml}</div>
                </div>
            </div>
        `;
    }).join('');
}

window.addToCart = function(productId) {
    const product = window.posState.products.find(p => p.id === productId);
    if (!product || product.stock <= 0) return;

    const existing = window.posState.cart.find(i => i.product_id === productId);
    if (existing) {
        if (product.image) {
            existing.image = product.image;
        }
        existing.stores = Array.isArray(product.stores) ? product.stores.map(s => ({ ...s })) : (existing.stores || []);
        existing.global_stock = parseInt(product.global_stock ?? product.stock, 10) || existing.global_stock || 0;
        const maxStock = getItemMaxStock(existing);
        existing.stock = maxStock;
        if (existing.quantity >= maxStock) {
            const storeName = getItemStoreName(existing);
            Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible${storeName ? ' (' + storeName + ')' : ''} : ${maxStock}`, timer: 1600, showConfirmButton: false });
            return;
        }
        existing.quantity++;
    } else {
        const newItem = {
            product_id: product.id,
            name: product.name,
            image: product.image || '',
            price: parseFloat(product.price),
            custom_price: null,
            quantity: 1,
            stock: 0,
            global_stock: parseInt(product.global_stock ?? product.stock, 10) || 0,
            stores: Array.isArray(product.stores) ? product.stores.map(s => ({ ...s })) : [],
            store_id: resolveDefaultItemStoreId(product),
            discount_amount: 0,
            discount_type: 'fixed'
        };
        newItem.stock = getItemMaxStock(newItem);
        window.posState.cart.push(newItem);
    }

    renderCart();
    renderProducts();
};

window.updateQuantity = function(productId, delta) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    const next = item.quantity + delta;
    if (next <= 0) {
        removeFromCart(productId);
        return;
    }
    const maxStock = getItemMaxStock(item);
    item.stock = maxStock;
    if (next > maxStock) {
        const storeName = getItemStoreName(item);
        Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible${storeName ? ' (' + storeName + ')' : ''} : ${maxStock}`, timer: 1600, showConfirmButton: false });
        return;
    }
    item.quantity = next;
    renderCart();
    renderProducts();
};

window.setItemQuantity = function(productId, rawVal) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;
    let qty = parseInt(rawVal, 10);
    if (isNaN(qty) || qty <= 0) {
        removeFromCart(productId);
        return;
    }
    const maxStock = getItemMaxStock(item);
    item.stock = maxStock;
    if (qty > maxStock) {
        qty = maxStock;
        const storeName = getItemStoreName(item);
        Swal.fire({ icon: 'warning', title: 'Stock limité', text: `Disponible${storeName ? ' (' + storeName + ')' : ''} : ${maxStock}`, timer: 1400, showConfirmButton: false });
    }
    item.quantity = qty;
    renderCart();
    renderProducts();
};

window.removeFromCart = function(productId) {
    window.posState.cart = window.posState.cart.filter(i => i.product_id !== productId);
    renderCart();
    renderProducts();
};

window.updateItemStore = function(productId, rawStoreId) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    item.store_id = rawStoreId ? parseInt(rawStoreId, 10) : null;
    const maxStock = getItemMaxStock(item);
    item.stock = maxStock;

    if (maxStock > 0 && item.quantity > maxStock) {
        item.quantity = maxStock;
        const storeName = getItemStoreName(item);
        Swal.fire({
            icon: 'warning',
            title: 'Stock limité',
            text: `Quantité ajustée à ${maxStock}${storeName ? ' (' + storeName + ')' : ''}`,
            timer: 1600,
            showConfirmButton: false
        });
    }

    renderCart();
    renderProducts();
};

window.updateItemCustomPrice = function(productId, rawVal, inputEl) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    if (inputEl && inputEl.validity && inputEl.validity.badInput) {
        return;
    }

    const trimmed = String(rawVal ?? '').trim();
    if (trimmed === '') {
        item.custom_price = null;
    } else {
        const parsed = parseFloat(trimmed);
        if (!isNaN(parsed)) {
            if (parsed < 0) {
                item.custom_price = 0;
                if (inputEl) inputEl.value = '0';
            } else {
                item.custom_price = parsed;
            }
        }
    }

    if (inputEl) {
        inputEl.classList.toggle('has-custom-price', hasItemCustomPrice(item));
    }

    updateTotals();
};

window.updateItemDiscount = function(productId, value, type) {
    const item = window.posState.cart.find(i => i.product_id === productId);
    if (!item) return;

    if (value !== undefined) item.discount_amount = Math.max(0, parseFloat(value) || 0);
    if (type !== undefined) item.discount_type = type;

    updateTotals();
};

function formatCartUnitPriceHtml(line) {
    if (line.totalLineDiscount > 0) {
        return `<strong style="color:#16a34a;">${formatCurrency(line.finalUnitPrice)} / u</strong> <span style="color:#94a3b8;font-size:10.5px;">(après remise)</span>`;
    }
    return '';
}

function formatLineDiscountTitle(line) {
    if (line.itemDiscount > 0 && line.globalShare > 0) {
        return `Remise produit : -${formatCurrency(line.itemDiscount)} | Remise globale : -${formatCurrency(line.globalShare)}`;
    }
    if (line.globalShare > 0) {
        return `Part remise globale : -${formatCurrency(line.globalShare)}`;
    }
    if (line.itemDiscount > 0) {
        return `Remise produit : -${formatCurrency(line.itemDiscount)}`;
    }
    return '';
}

function resolvePosProductImg(rawImg) {
    const fallbackSrc = '{{ asset("images/camera/cat_cameras.jpg") }}';
    if (!rawImg) return fallbackSrc;
    if (/^(https?:)?\//.test(rawImg)) return rawImg;
    if (rawImg.startsWith('images/')) return '/' + rawImg;
    return '/storage/' + rawImg;
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const countBadge = document.getElementById('cartCount');
    const mobileBadge = document.getElementById('mobileCartBadge');
    const cart = window.posState.cart;

    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    if (countBadge) countBadge.textContent = totalItems;
    if (mobileBadge) mobileBadge.textContent = totalItems;

    if (!cart.length) {
        container.innerHTML = `<div style="text-align:center;margin-top:65px;color:#94a3b8;">
            <div style="width:60px;height:60px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="fas fa-shopping-basket" style="font-size:22px;color:#cbd5e1;"></i>
            </div>
            <h4 style="font-size:14px;font-weight:600;color:#64748b;">Panier vide</h4>
            <p style="font-size:12px;margin-top:4px;">Cliquez sur un produit pour l'ajouter</p>
        </div>`;
    } else {
        const pricing = computeCartPricing();
        container.innerHTML = cart.map(item => {
            const line = pricing.linesById[item.product_id];
            const isCustomDiff = line.hasCustomPrice && Math.abs(line.basePrice - (parseFloat(item.price) || 0)) >= 0.005;
            const maxQty = getItemMaxStock(item);
            const matchedCatalogProduct = !item.image ? (window.posState.products || []).find(p => p.id === item.product_id) : null;
            const cartThumbSrc = resolvePosProductImg(item.image || matchedCatalogProduct?.image || '');
            return `
                <div class="cart-item-card">
                    <div class="cart-item-top">
                        <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0">
                            <img src="${cartThumbSrc}"
                                 alt="${escapeHtml(item.name)}"
                                 class="cart-item-thumb"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset("images/camera/cat_cameras.jpg") }}';">
                            <div class="min-w-0 flex-grow-1">
                                <div class="cart-item-name">${escapeHtml(item.name)}</div>
                                <div id="cart-unit-price-${item.product_id}" style="font-size:11.5px;color:#64748b;display:${line.totalLineDiscount > 0 ? 'block' : 'none'};">${formatCartUnitPriceHtml(line)}</div>
                            </div>
                        </div>
                        <button type="button" class="cart-item-remove" onclick="removeFromCart(${item.product_id})" title="Retirer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="cart-item-mid">
                        <div class="item-controls">
                            <button type="button" class="control-btn" onclick="updateQuantity(${item.product_id}, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" class="qty-input-inline" min="1" max="${maxQty}" value="${item.quantity}" onchange="setItemQuantity(${item.product_id}, this.value)">
                            <button type="button" class="control-btn" onclick="updateQuantity(${item.product_id}, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="text-end">
                            <span id="cart-line-disc-${item.product_id}" class="badge bg-success-subtle text-success me-1" title="${escapeHtml(formatLineDiscountTitle(line))}" style="font-size:10.5px;display:${line.totalLineDiscount > 0 ? 'inline-block' : 'none'};">
                                ${line.totalLineDiscount > 0 ? `-${formatCurrency(line.totalLineDiscount)}` : ''}
                            </span>
                            <strong id="cart-line-net-${item.product_id}" style="font-size:13.5px;color:#0f172a;">${formatCurrency(line.finalNet)}</strong>
                        </div>
                    </div>
                    <div class="cart-item-store-row">
                        <label class="cart-item-field-label" for="cart-store-${item.product_id}" style="flex-shrink:0;">
                            <i class="fas fa-warehouse me-1 text-secondary"></i>Dépôt
                        </label>
                        <select id="cart-store-${item.product_id}" class="cart-store-select"
                                onchange="updateItemStore(${item.product_id}, this.value)">
                            ${renderCartStoreOptions(item)}
                        </select>
                    </div>
                    <div class="cart-item-discount-row">
                        <div class="cart-item-field">
                            <label class="cart-item-field-label" for="cart-custom-price-${item.product_id}">
                                <i class="fas fa-tag me-1 text-primary"></i>Prix (${window.currencyConfig.symbol})<span id="cart-custom-tag-${item.product_id}" class="badge bg-primary-subtle text-primary ms-1" style="font-size:9px;display:${isCustomDiff ? 'inline-block' : 'none'};">Perso</span>
                            </label>
                            <input type="number" id="cart-custom-price-${item.product_id}"
                                   class="item-price-input ${line.hasCustomPrice ? 'has-custom-price' : ''}"
                                   min="0" step="0.01"
                                   value="${line.hasCustomPrice ? item.custom_price : ''}"
                                   placeholder="${(parseFloat(item.price) || 0).toFixed(window.currencyConfig.decimals)}"
                                   title="Prix personnalisé unitaire (vide = prix catalogue)"
                                   oninput="updateItemCustomPrice(${item.product_id}, this.value, this)">
                        </div>
                        <div class="cart-item-field">
                            <label class="cart-item-field-label">
                                <i class="fas fa-percent me-1 text-danger"></i>Remise
                            </label>
                            <div class="item-disc-inputs">
                                <input type="number" class="item-disc-input" min="0" step="0.01"
                                       value="${item.discount_amount > 0 ? item.discount_amount : ''}"
                                       placeholder="0"
                                       oninput="updateItemDiscount(${item.product_id}, this.value, undefined)">
                                <select class="item-disc-type" onchange="updateItemDiscount(${item.product_id}, undefined, this.value)">
                                    <option value="fixed" ${(item.discount_type || 'fixed') === 'fixed' ? 'selected' : ''}>${window.currencyConfig.symbol}</option>
                                    <option value="percent" ${item.discount_type === 'percent' ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    updateTotals();
}

function updateTotals() {
    const cart = window.posState.cart;
    const pricing = computeCartPricing();

    pricing.lines.forEach(line => {
        const pid = line.item.product_id;
        const unitEl = document.getElementById(`cart-unit-price-${pid}`);
        const customTagEl = document.getElementById(`cart-custom-tag-${pid}`);
        const discEl = document.getElementById(`cart-line-disc-${pid}`);
        const netEl = document.getElementById(`cart-line-net-${pid}`);
        const isCustomDiff = line.hasCustomPrice && Math.abs(line.basePrice - (parseFloat(line.item.price) || 0)) >= 0.005;

        if (unitEl) {
            unitEl.innerHTML = formatCartUnitPriceHtml(line);
            unitEl.style.display = line.totalLineDiscount > 0 ? 'block' : 'none';
        }
        if (customTagEl) {
            customTagEl.style.display = isCustomDiff ? 'inline-block' : 'none';
        }
        if (discEl) {
            discEl.textContent = line.totalLineDiscount > 0 ? `-${formatCurrency(line.totalLineDiscount)}` : '';
            discEl.style.display = line.totalLineDiscount > 0 ? 'inline-block' : 'none';
            discEl.title = formatLineDiscountTitle(line);
        }
        if (netEl) netEl.textContent = formatCurrency(line.finalNet);
    });

    const splitPreviewEl = document.getElementById('globalDiscountSplitPreview');
    if (splitPreviewEl) {
        if (pricing.globalDiscount > 0 && pricing.lines.length > 0) {
            const rowsHtml = pricing.lines.map(line => {
                const qty = parseInt(line.item.quantity, 10) || 1;
                const qtyBadge = qty > 1 ? ` <span class="text-muted">(×${qty})</span>` : '';
                const totalTag = qty > 1 ? ` <span class="text-muted">• Total: ${formatCurrency(line.finalNet)}</span>` : '';
                return `<div class="d-flex justify-content-between align-items-center gap-2 py-1 border-bottom" style="border-color:#f1f5f9 !important;">
                    <span class="text-truncate" style="max-width:46%;" title="${escapeHtml(line.item.name)}">${escapeHtml(line.item.name)}${qtyBadge}</span>
                    <span class="text-end flex-shrink-0">
                        <del class="text-muted">${formatCurrency(line.basePrice)}</del> → <strong style="color:#16a34a;">${formatCurrency(line.finalUnitPrice)}</strong>
                        <span class="text-primary">(-${formatCurrency(line.globalShare)})</span>${totalTag}
                    </span>
                </div>`;
            }).join('');
            splitPreviewEl.innerHTML = `<div class="fw-semibold text-dark mb-1"><i class="fas fa-code-branch text-danger me-1"></i>Répartition par produit (nouveau prix) :</div>${rowsHtml}`;
            splitPreviewEl.style.display = 'block';
        } else {
            splitPreviewEl.innerHTML = '';
            splitPreviewEl.style.display = 'none';
        }
    }

    const total = pricing.total;
    const totalDiscount = pricing.totalDiscount;
    window.posState.currentTotal = total;

    document.getElementById('subtotal').textContent = formatCurrency(pricing.grossSubtotal);

    const totalDiscountEl = document.getElementById('totalDiscountDisplay');
    if (totalDiscountEl) {
        totalDiscountEl.textContent = totalDiscount > 0 ? '-' + formatCurrency(totalDiscount) : formatCurrency(0);
        totalDiscountEl.style.color = totalDiscount > 0 ? '#16a34a' : '#64748b';
    }
    document.getElementById('total').textContent = formatCurrency(total);

    const checkoutBtn = document.getElementById('checkoutBtn');
    const checkoutLabel = document.getElementById('checkoutBtnLabel');
    if (checkoutBtn) checkoutBtn.disabled = cart.length === 0;
    if (checkoutLabel) checkoutLabel.textContent = `Encaisser ${formatCurrency(total)}`;

    updateCashChange();
    updatePartialPaymentSummary();
    updateMobileBottomBar();
}

window.POS_ORDER_STATUS_META = {
    pending:    { label: 'En attente',   dot: 'amber',  badgeClass: 'warning status-pill-pending',    textClass: 'text-warning' },
    en_demande: { label: 'Sur Commande', dot: 'indigo', badgeClass: 'indigo status-pill-en_demande',  textClass: 'text-primary' },
    reserved:   { label: 'Réservé',      dot: 'purple', badgeClass: 'purple status-pill-reserved',   textClass: 'text-purple' },
    processing: { label: 'En cours',     dot: 'blue',   badgeClass: 'primary status-pill-processing', textClass: 'text-primary' },
    shipped:    { label: 'Expédiée',     dot: 'teal',   badgeClass: 'info status-pill-shipped',      textClass: 'text-info' },
    delivered:  { label: 'Livrée',       dot: 'green',  badgeClass: 'success status-pill-delivered', textClass: 'text-success' }
};

window.formatPosStatusBadgeHtml = function(statusVal) {
    const meta = window.POS_ORDER_STATUS_META[statusVal] || window.POS_ORDER_STATUS_META.delivered;
    return `<span class="brand-badge ${meta.badgeClass}" style="padding: 2px 9px; font-size: 11px;"><span class="status-dot dot-${meta.dot}"></span> ${escapeHtml(meta.label)}</span>`;
};

window.formatPosTaxBadgeHtml = function(taxMode) {
    const mode = String(taxMode || 'ht').toLowerCase() === 'ttc' ? 'ttc' : 'ht';
    const label = mode === 'ht' ? 'HT' : 'TTC';
    return `<span class="tax-mode-pill tax-mode-${mode}">${label}</span>`;
};

window.POS_DELIVERY_TYPE_META = {
    store:    { label: 'Store',    icon: 'fa-store',            bg: '#f1f5f9', color: '#0f172a', border: '#cbd5e1' },
    our_team: { label: 'Our Team', icon: 'fa-people-carry-box', bg: '#eff6ff', color: '#1d4ed8', border: '#bfdbfe' },
    ozon:     { label: 'Ozon',     icon: 'fa-box-open',         bg: '#fffbeb', color: '#b45309', border: '#fde68a' }
};

window.formatPosDeliveryBadgeHtml = function(deliveryType) {
    const key = String(deliveryType || 'store').toLowerCase();
    const meta = window.POS_DELIVERY_TYPE_META[key] || window.POS_DELIVERY_TYPE_META.store;
    return `<span class="badge d-inline-flex align-items-center gap-1" style="background:${meta.bg};color:${meta.color};border:1px solid ${meta.border};padding:2px 9px;font-size:11px;font-weight:700;"><i class="fas ${meta.icon}"></i> ${escapeHtml(meta.label)}</span>`;
};

window.setPosDeliveryType = function(deliveryType) {
    const raw = String(deliveryType || 'store').toLowerCase();
    const nextType = window.POS_DELIVERY_TYPE_META[raw] ? raw : 'store';
    const inputEl = document.getElementById('orderDeliveryType');
    if (inputEl) {
        inputEl.value = nextType;
    }
    document.querySelectorAll('#posDeliveryTypeGroup .pos-delivery-toggle-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.deliveryType === nextType);
    });
    if (window.posState?.mobileStep === 5 && typeof renderMobileConfirmSummary === 'function') {
        renderMobileConfirmSummary();
    }
};

window.setPosTaxMode = function(mode) {
    const nextMode = String(mode || 'ht').toLowerCase() === 'ttc' ? 'ttc' : 'ht';
    const inputEl = document.getElementById('orderTaxMode');
    if (inputEl) {
        inputEl.value = nextMode;
    }
    document.querySelectorAll('#posTaxModeGroup .pos-tax-toggle-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.taxMode === nextMode);
    });
    if (window.posState?.mobileStep === 5 && typeof renderMobileConfirmSummary === 'function') {
        renderMobileConfirmSummary();
    }
};

window.updateOrderStatusHint = function() {
    const raw = document.getElementById('orderStatus')?.value || 'delivered';
    const status = window.POS_ORDER_STATUS_META[raw] ? raw : 'delivered';
    const meta = window.POS_ORDER_STATUS_META[status];

    const pillEl = document.getElementById('posStatusPill');
    const dotEl = document.getElementById('posStatusDot');
    const labelEl = document.getElementById('posStatusLabel');
    if (pillEl) {
        pillEl.className = `brand-badge brand-badge-btn ${meta.badgeClass} dropdown-toggle`;
    }
    if (dotEl) {
        dotEl.className = `status-dot dot-${meta.dot}`;
    }
    if (labelEl) {
        labelEl.textContent = meta.label;
    }

    const menuEl = document.getElementById('posStatusMenu');
    if (menuEl) {
        menuEl.querySelectorAll('.dropdown-item').forEach(btn => {
            const isSelected = btn.dataset.status === status;
            btn.classList.toggle('active', isSelected);
            const oldCheck = btn.querySelector('.fa-check');
            if (oldCheck) oldCheck.remove();
            if (isSelected) {
                btn.insertAdjacentHTML('beforeend', ` <i class="fas fa-check ${meta.textClass} small"></i>`);
            }
        });
    }

    const hintEl = document.getElementById('orderStatusStockHint');
    if (hintEl) {
        if (status === 'delivered') {
            hintEl.textContent = 'Stock déduit';
            hintEl.style.color = '#16a34a';
        } else {
            hintEl.textContent = 'Stock déduit à la livraison';
            hintEl.style.color = '#d97706';
        }
    }
    if (typeof updateMobileBottomBar === 'function') {
        updateMobileBottomBar();
    }
};

window.setPosOrderStatus = function(statusVal) {
    const nextStatus = window.POS_ORDER_STATUS_META[statusVal] ? statusVal : 'delivered';
    const statusEl = document.getElementById('orderStatus');
    if (statusEl) {
        statusEl.value = nextStatus;
    }
    updateOrderStatusHint();

    const pillEl = document.getElementById('posStatusPill');
    if (pillEl) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const inst = bootstrap.Dropdown.getInstance(pillEl);
            if (inst) inst.hide();
        }
        pillEl.classList.remove('show');
        pillEl.setAttribute('aria-expanded', 'false');
    }
    document.getElementById('posStatusMenu')?.classList.remove('show');
};

window.togglePartialPaymentBox = function() {
    const method = document.getElementById('paymentMethod')?.value || 'cash';
    const toggle = document.getElementById('isPartialPayment');
    const fields = document.getElementById('partialPaymentFields');
    const cashBox = document.getElementById('cashTenderedBox');
    const isPartial = !!toggle?.checked && method !== 'credit';

    if (fields) fields.style.display = isPartial ? 'block' : 'none';
    if (cashBox) cashBox.style.display = (method === 'cash' && !isPartial) ? 'block' : 'none';

    updateCashChange();
    updatePartialPaymentSummary();
};

function hasValidPartialClientInfo() {
    const name = (document.getElementById('customerName')?.value || '').trim();
    const phone = (document.getElementById('customerPhone')?.value || '').trim();
    const lower = name.toLowerCase();
    return name !== '' && lower !== 'client de passage' && lower !== 'walk-in customer' && /^\d{10}$/.test(phone);
}

function syncAutoSaveCustomerCheckbox() {
    const cb = document.getElementById('saveCustomerCheckbox');
    if (!cb || window.posState?.saveCustomerManualOverride) {
        return;
    }

    const customerId = (document.getElementById('customerId')?.value || '').trim();
    const curName = (document.getElementById('customerName')?.value || '').trim();
    const curPhone = (document.getElementById('customerPhone')?.value || '').trim();

    if (!customerId) {
        cb.checked = hasValidPartialClientInfo();
        return;
    }

    const selectEl = document.getElementById('customerSelect');
    const opt = selectEl && selectEl.selectedIndex >= 0 ? selectEl.options[selectEl.selectedIndex] : null;
    const origName = (opt?.dataset.name || '').trim();
    const origPhone = normalizePosPhone(opt?.dataset.phone || '');

    cb.checked = hasValidPartialClientInfo() && (curName !== origName || curPhone !== origPhone);
}

function validatePartialClientInfo() {
    if (hasValidPartialClientInfo()) {
        return true;
    }

    const nameEl = document.getElementById('customerName');
    const phoneEl = document.getElementById('customerPhone');
    const name = (nameEl?.value || '').trim();
    const lower = name.toLowerCase();
    const needsName = name === '' || lower === 'client de passage' || lower === 'walk-in customer';

    Swal.fire({
        icon: 'warning',
        title: 'Client requis',
        text: 'Veuillez saisir le nom réel du client et un numéro de téléphone de 10 chiffres pour le paiement partiel.'
    }).then(() => {
        if (window.innerWidth < 1024) {
            window.goToMobileStep(3);
        }
        const targetEl = needsName ? nameEl : phoneEl;
        targetEl?.focus();
        if (needsName) {
            targetEl?.select();
        }
    });

    return false;
}

window.updatePartialPaymentSummary = function() {
    const cart = window.posState?.cart || [];
    const total = window.posState?.currentTotal || 0;
    const method = document.getElementById('paymentMethod')?.value || 'cash';
    const isPartial = !!document.getElementById('isPartialPayment')?.checked && method !== 'credit';
    const paidInput = document.getElementById('partialPaidAmount');
    const remEl = document.getElementById('partialRemainingDisplay');
    const clientNoticeEl = document.getElementById('partialClientNotice');
    const checkoutLabel = document.getElementById('checkoutBtnLabel');

    const paidNow = Math.max(0, parseFloat(paidInput?.value) || 0);
    const remaining = Math.max(0, +(total - paidNow).toFixed(2));
    if (remEl) remEl.textContent = formatCurrency(remaining);
    if (clientNoticeEl) {
        clientNoticeEl.style.display = (isPartial && !hasValidPartialClientInfo()) ? 'block' : 'none';
    }

    if (checkoutLabel) {
        if (isPartial && cart.length > 0) {
            checkoutLabel.textContent = `Encaisser acompte ${formatCurrency(paidNow)}`;
        } else {
            checkoutLabel.textContent = `Encaisser ${formatCurrency(total)}`;
        }
    }
};

function updateCashChange() {
    const method = document.getElementById('paymentMethod')?.value;
    const isPartial = !!document.getElementById('isPartialPayment')?.checked && method !== 'credit';
    const received = parseFloat(document.getElementById('amountReceived')?.value);
    const changeRow = document.getElementById('changeRow');
    const changeEl = document.getElementById('changeAmount');
    if (!changeRow || !changeEl) return;

    if (method === 'cash' && !isPartial && !isNaN(received) && received >= window.posState.currentTotal && window.posState.currentTotal > 0) {
        changeRow.style.setProperty('display', 'flex', 'important');
        changeEl.textContent = formatCurrency(received - window.posState.currentTotal);
    } else {
        changeRow.style.setProperty('display', 'none', 'important');
    }
}

window.selectPaymentMethod = function(method, btn) {
    document.getElementById('paymentMethod').value = method;
    document.querySelectorAll('.payment-pill').forEach(p => p.classList.remove('active'));
    btn?.classList.add('active');

    const partialWrap = document.getElementById('partialPaymentToggleWrap');
    const partialToggle = document.getElementById('isPartialPayment');
    const partialFields = document.getElementById('partialPaymentFields');
    if (method === 'credit') {
        if (partialToggle) partialToggle.checked = false;
        if (partialWrap) partialWrap.style.display = 'none';
        if (partialFields) partialFields.style.display = 'none';
    } else {
        if (partialWrap) partialWrap.style.display = 'block';
    }

    const isPartial = !!partialToggle?.checked && method !== 'credit';
    const cashBox = document.getElementById('cashTenderedBox');
    if (cashBox) cashBox.style.display = (method === 'cash' && !isPartial) ? 'block' : 'none';
    updateCashChange();
    updatePartialPaymentSummary();
    updateMobileBottomBar();
};

window.resetCustomerFields = function() {
    document.getElementById('customerSelect').value = '';
    document.getElementById('customerId').value = '';
    document.getElementById('customerEmail').value = '';
    document.getElementById('customerName').value = 'Client de passage';
    document.getElementById('customerPhone').value = '';
    document.getElementById('customerCity').value = '';
    document.getElementById('saveCustomerCheckbox').checked = false;
    if (window.posState) {
        window.posState.saveCustomerManualOverride = false;
    }
    document.getElementById('creditInfo').style.display = 'none';

    if (document.getElementById('paymentMethod').value === 'credit') {
        selectPaymentMethod('cash', document.querySelector('.payment-pill[data-method="cash"]'));
    }
    updatePartialPaymentSummary();
};

window.clearCartConfirm = function() {
    if (!window.posState.cart.length) return;
    Swal.fire({
        icon: 'question',
        title: 'Vider le panier ?',
        text: 'Tous les articles sélectionnés seront retirés.',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Oui, vider',
        cancelButtonText: 'Annuler'
    }).then(r => {
        if (r.isConfirmed) {
            window.posState.cart = [];
            renderCart();
            renderProducts();
        }
    });
};

window.checkout = function() {
    const cart = window.posState.cart;
    if (!cart.length) {
        Swal.fire({ icon: 'warning', title: 'Panier vide', text: 'Ajoutez au moins un produit.' });
        return;
    }

    const customerName = (document.getElementById('customerName')?.value || '').trim();
    const customerPhone = (document.getElementById('customerPhone')?.value || '').trim();
    const customerCity = (document.getElementById('customerCity')?.value || '').trim();
    const customerId = document.getElementById('customerId')?.value || null;
    const customerEmail = (document.getElementById('customerEmail')?.value || '').trim() || null;
    const saveCustomer = !!document.getElementById('saveCustomerCheckbox')?.checked;

    if (!customerName) {
        Swal.fire({ icon: 'warning', title: 'Nom requis', text: 'Veuillez saisir le nom du client.' });
        document.getElementById('customerName').focus();
        return;
    }

    if (customerPhone !== '' && !/^\d{10}$/.test(customerPhone)) {
        Swal.fire({ icon: 'warning', title: 'Téléphone invalide', text: 'Le numéro de téléphone doit contenir exactement 10 chiffres.' });
        document.getElementById('customerPhone')?.focus();
        return;
    }

    const paymentMethod = document.getElementById('paymentMethod').value;
    const total = window.posState.currentTotal;
    const isPartialPayment = !!document.getElementById('isPartialPayment')?.checked && paymentMethod !== 'credit';
    const partialPaidAmount = Math.max(0, parseFloat(document.getElementById('partialPaidAmount')?.value) || 0);
    const paymentDueDate = (document.getElementById('paymentDueDate')?.value || '').trim();

    if (isPartialPayment) {
        if (!validatePartialClientInfo()) {
            return;
        }
        if (partialPaidAmount < 0 || partialPaidAmount >= total) {
            Swal.fire({ icon: 'warning', title: 'Acompte invalide', text: 'Le montant payé maintenant doit être inférieur au total.' });
            document.getElementById('partialPaidAmount')?.focus();
            return;
        }
        if (!paymentDueDate) {
            Swal.fire({ icon: 'warning', title: 'Échéance requise', text: 'Veuillez indiquer la date limite pour le reste à payer.' });
            document.getElementById('paymentDueDate')?.focus();
            return;
        }
    }

    if (paymentMethod === 'credit') {
        if (!customerId) {
            Swal.fire({ icon: 'warning', title: 'Client requis', text: 'Sélectionnez un client enregistré avec plafond de crédit.' });
            return;
        }
        const opt = document.getElementById('customerSelect').selectedOptions[0];
        const creditLimit = parseFloat(opt?.dataset.creditLimit) || 0;
        const balance = parseFloat(opt?.dataset.balance) || 0;
        if (creditLimit <= 0 || (creditLimit - balance) < total) {
            Swal.fire({ icon: 'error', title: 'Crédit insuffisant', text: `Disponible : ${formatCurrency(Math.max(0, creditLimit - balance))}` });
            return;
        }
    }

    const btn = document.getElementById('checkoutBtn');
    const label = document.getElementById('checkoutBtnLabel');
    btn.disabled = true;
    label.textContent = 'Traitement...';

    const payload = {
        customer_id: customerId || null,
        customer_name: customerName,
        customer_email: customerEmail,
        customer_phone: customerPhone || null,
        customer_city: customerCity || null,
        save_customer: saveCustomer,
        store_id: document.getElementById('posStoreSelect')?.value || null,
        payment_method: paymentMethod,
        amount_received: isPartialPayment ? partialPaidAmount : (document.getElementById('amountReceived')?.value || null),
        is_partial_payment: isPartialPayment,
        paid_amount: isPartialPayment ? partialPaidAmount : null,
        payment_due_date: isPartialPayment ? paymentDueDate : null,
        status: document.getElementById('orderStatus')?.value || 'delivered',
        delivery_type: document.getElementById('orderDeliveryType')?.value || 'store',
        tax_mode: document.getElementById('orderTaxMode')?.value || 'ht',
        notes: document.getElementById('orderNotes')?.value || null,
        discount_amount: parseFloat(document.getElementById('discountAmount')?.value) || 0,
        discount_type: document.getElementById('discountType')?.value || 'fixed',
        items: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: getItemBasePrice(item),
            discount_amount: item.discount_amount || 0,
            discount_type: item.discount_type || 'fixed',
            store_id: item.store_id || null
        }))
    };

    fetch(`{{ route('pos.order') }}`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
            let msg = data.message || `Erreur serveur (${res.status})`;
            if (data.errors && typeof data.errors === 'object') {
                const firstErr = Object.values(data.errors).flat()[0];
                if (firstErr) msg = firstErr;
            }
            Swal.fire({ icon: 'error', title: 'Erreur', text: msg });
            return;
        }

        // Update/append customer option in dropdown if saved
        if (data.customer) {
            const select = document.getElementById('customerSelect');
            let opt = select.querySelector(`option[value="${data.customer.id}"]`);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = data.customer.id;
                select.appendChild(opt);
            }
            opt.dataset.name = data.customer.name || '';
            opt.dataset.email = data.customer.email || '';
            opt.dataset.phone = data.customer.phone || '';
            opt.dataset.city = data.customer.city || '';
            opt.dataset.creditLimit = data.customer.credit_limit || 0;
            opt.dataset.balance = data.customer.current_balance || 0;
            opt.textContent = `${data.customer.name}${data.customer.phone ? ' • ' + data.customer.phone : ''}${data.customer.city ? ' (' + data.customer.city + ')' : ''}`;
        }

        // Update today's live POS KPI pill
        window.posState.todayCount += 1;
        window.posState.todayTotal += parseFloat(data.order.total || 0);
        const cntEl = document.getElementById('todaySalesCount');
        const totEl = document.getElementById('todaySalesTotal');
        if (cntEl) cntEl.textContent = window.posState.todayCount;
        if (totEl) totEl.textContent = formatCurrency(window.posState.todayTotal);

        const received = parseFloat(document.getElementById('amountReceived')?.value);
        const changeHtml = (!isPartialPayment && paymentMethod === 'cash' && !isNaN(received) && received > data.order.total)
            ? `<p class="mb-1"><strong>Monnaie à rendre :</strong> <span class="text-success fw-bold">${formatCurrency(received - data.order.total)}</span></p>`
            : '';
        const partialOrderHtml = (data.order.remaining_amount && data.order.remaining_amount > 0)
            ? `<p class="mb-1"><strong>Montant payé :</strong> <span class="text-success fw-bold">${formatCurrency(data.order.paid_amount || 0)}</span></p>
               <p class="mb-1"><strong>Reste à payer :</strong> <span class="text-danger fw-bold">${formatCurrency(data.order.remaining_amount)}</span></p>
               ${data.order.payment_due_date ? `<p class="mb-1"><strong>Échéance :</strong> ${escapeHtml(data.order.payment_due_date)}</p>` : ''}`
            : '';
        const statusLabelHtml = formatPosStatusBadgeHtml(data.order.status || 'delivered');
        const deliveryTypeBadgeHtml = formatPosDeliveryBadgeHtml(data.order.delivery_type || 'store');
        const taxModeBadgeHtml = formatPosTaxBadgeHtml(data.order.tax_mode || 'ht');
        const savedClientHtml = (saveCustomer && data.customer)
            ? `<p class="mb-0 text-success small"><i class="fas fa-user-check me-1"></i>Client enregistré : <strong>${escapeHtml(data.customer.name)}</strong></p>`
            : '';

        Swal.fire({
            icon: 'success',
            title: 'Vente enregistrée !',
            html: `
                <div class="text-start p-2 rounded bg-light border mb-2">
                    <p class="mb-1"><strong>Commande :</strong> #${escapeHtml(data.order.order_number)}</p>
                    <p class="mb-1"><strong>Statut :</strong> ${statusLabelHtml}</p>
                    <p class="mb-1"><strong>Livraison :</strong> ${deliveryTypeBadgeHtml}</p>
                    <p class="mb-1"><strong>Régime :</strong> ${taxModeBadgeHtml}</p>
                    <p class="mb-1"><strong>Total commande :</strong> <strong class="text-danger">${formatCurrency(data.order.total)}</strong></p>
                    ${partialOrderHtml}
                    ${changeHtml}
                    ${savedClientHtml}
                </div>
                ${data.order.receipt_url ? `<a href="${data.order.receipt_url}" target="_blank" class="btn btn-sm btn-outline-dark w-100"><i class="fas fa-print me-1"></i> Imprimer Ticket 80mm</a>` : ''}
            `,
            showCancelButton: true,
            confirmButtonText: 'Nouvelle vente',
            cancelButtonText: 'Voir commande',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#0f172a'
        }).then(result => {
            if (result.dismiss === Swal.DismissReason.cancel && data.order.show_url) {
                window.location.href = data.order.show_url;
                return;
            }
            window.posState.cart = [];
            document.getElementById('discountAmount').value = '';
            document.getElementById('discountType').value = 'fixed';
            document.getElementById('amountReceived').value = '';
            if (document.getElementById('isPartialPayment')) document.getElementById('isPartialPayment').checked = false;
            if (document.getElementById('partialPaidAmount')) document.getElementById('partialPaidAmount').value = '';
            if (document.getElementById('paymentDueDate')) document.getElementById('paymentDueDate').value = '{{ now()->addDays(15)->toDateString() }}';
            togglePartialPaymentBox();
            setPosOrderStatus('delivered');
            setPosDeliveryType('store');
            setPosTaxMode('ht');
            document.getElementById('orderNotes').value = '';
            resetCustomerFields();
            renderCart();
            loadProducts();
            goToMobileStep(1);
        });
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Erreur réseau', text: 'Impossible de contacter le serveur.' });
    })
    .finally(() => {
        btn.disabled = window.posState.cart.length === 0;
        updatePartialPaymentSummary();
    });
};

function initPosTerminal() {
    // Auto-collapse sidebar on desktop so POS catalog has maximum workspace
    if (window.innerWidth >= 992) {
        document.querySelector('.sidebar')?.classList.add('collapsed');
        document.querySelector('.main-content')?.classList.add('expanded');
    }

    const searchInput = document.getElementById('searchInput');
    let searchTimer;
    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadProducts, 250);
    });
    searchInput?.addEventListener('keypress', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = (searchInput.value || '').trim().toLowerCase();
            const exactSkuMatch = q ? window.posState.products.find(p => (p.sku || '').toLowerCase() === q) : null;
            const targetProduct = exactSkuMatch || (window.posState.products.length === 1 ? window.posState.products[0] : null);
            if (targetProduct && targetProduct.stock > 0) {
                addToCart(targetProduct.id);
                searchInput.value = '';
                loadProducts();
            } else if (targetProduct && targetProduct.stock <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Rupture de stock',
                    text: `${targetProduct.name} est actuellement en rupture de stock.`,
                    timer: 1600,
                    showConfirmButton: false
                });
            } else {
                loadProducts();
            }
        }
    });

    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadProducts();
        });
    });

    document.getElementById('posStoreSelect')?.addEventListener('change', loadProducts);
    document.getElementById('brandFilter')?.addEventListener('change', loadProducts);
    document.getElementById('stockFilter')?.addEventListener('change', loadProducts);

    document.getElementById('discountAmount')?.addEventListener('input', updateTotals);
    document.getElementById('discountType')?.addEventListener('change', updateTotals);
    document.getElementById('amountReceived')?.addEventListener('input', updateCashChange);
    document.getElementById('saveCustomerCheckbox')?.addEventListener('change', function() {
        if (window.posState) {
            window.posState.saveCustomerManualOverride = true;
        }
    });

    const customerNameEl = document.getElementById('customerName');
    const clearDefaultWalkInName = function() {
        if (this.value.trim().toLowerCase() === 'client de passage') {
            this.value = '';
            updatePartialPaymentSummary();
            syncAutoSaveCustomerCheckbox();
        }
    };
    customerNameEl?.addEventListener('focus', clearDefaultWalkInName);
    customerNameEl?.addEventListener('click', clearDefaultWalkInName);
    customerNameEl?.addEventListener('blur', function() {
        if (this.value.trim() === '') {
            this.value = 'Client de passage';
            updatePartialPaymentSummary();
            syncAutoSaveCustomerCheckbox();
        }
    });
    customerNameEl?.addEventListener('input', function() {
        updatePartialPaymentSummary();
        syncAutoSaveCustomerCheckbox();
    });

    document.getElementById('customerPhone')?.addEventListener('input', function() {
        const cleaned = this.value.replace(/\D/g, '').slice(0, 10);
        if (this.value !== cleaned) {
            this.value = cleaned;
        }
        updatePartialPaymentSummary();
        syncAutoSaveCustomerCheckbox();
    });

    document.getElementById('customerSelect')?.addEventListener('change', function() {
        if (!this.value) {
            resetCustomerFields();
            return;
        }
        const opt = this.options[this.selectedIndex];
        document.getElementById('customerId').value = this.value;
        document.getElementById('customerName').value = opt.dataset.name || '';
        document.getElementById('customerEmail').value = opt.dataset.email || '';
        document.getElementById('customerPhone').value = normalizePosPhone(opt.dataset.phone || '');
        document.getElementById('customerCity').value = opt.dataset.city || '';
        if (window.posState) {
            window.posState.saveCustomerManualOverride = false;
        }
        const saveCb = document.getElementById('saveCustomerCheckbox');
        if (saveCb) {
            saveCb.checked = false;
        }

        const creditLimit = parseFloat(opt.dataset.creditLimit) || 0;
        const balance = parseFloat(opt.dataset.balance) || 0;
        const creditInfo = document.getElementById('creditInfo');

        if (creditLimit > 0) {
            const avail = Math.max(0, creditLimit - balance);
            const pct = Math.min(100, (balance / creditLimit) * 100);
            creditInfo.style.display = 'block';
            document.getElementById('creditUsed').textContent = `${formatCurrency(balance)} / ${formatCurrency(creditLimit)} (Dispo: ${formatCurrency(avail)})`;
            const bar = document.getElementById('creditBar');
            bar.style.width = pct + '%';
            bar.style.background = pct > 90 ? '#dc2626' : (pct > 75 ? '#f59e0b' : '#10b981');
        } else {
            creditInfo.style.display = 'none';
            if (document.getElementById('paymentMethod').value === 'credit') {
                selectPaymentMethod('cash', document.querySelector('.payment-pill[data-method="cash"]'));
            }
        }
        updatePartialPaymentSummary();
    });

    if (window.__posKeydownHandler) {
        document.removeEventListener('keydown', window.__posKeydownHandler);
    }
    window.__posKeydownHandler = function(e) {
        if (!document.getElementById('productsGrid')) return;
        const activeEl = document.activeElement;
        const isInput = activeEl && ['INPUT', 'SELECT', 'TEXTAREA'].includes(activeEl.tagName);
        const si = document.getElementById('searchInput');
        if (e.key === 'F2') {
            e.preventDefault();
            si?.focus();
            si?.select();
        } else if (e.key === 'F4' && !isInput) {
            e.preventDefault();
            clearCartConfirm();
        } else if (e.key === 'F9' && !isInput && window.posState.cart.length > 0) {
            e.preventDefault();
            checkout();
        }
    };
    document.addEventListener('keydown', window.__posKeydownHandler);

    document.getElementById('customerFilterInput')?.addEventListener('input', function() {
        const q = this.value.trim().toLowerCase();
        const select = document.getElementById('customerSelect');
        if (!select) return;
        Array.from(select.options).forEach((opt, idx) => {
            if (idx === 0) {
                opt.hidden = false;
                return;
            }
            const text = (opt.textContent + ' ' + (opt.dataset.phone || '') + ' ' + (opt.dataset.city || '')).toLowerCase();
            opt.hidden = q !== '' && !text.includes(q);
        });
    });

    const posStatusPillEl = document.getElementById('posStatusPill');
    if (posStatusPillEl && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
        bootstrap.Dropdown.getOrCreateInstance(posStatusPillEl, {
            autoClose: true,
            popperConfig: function(defaultBsPopperConfig) {
                return {
                    ...defaultBsPopperConfig,
                    strategy: 'fixed',
                    modifiers: [
                        ...(defaultBsPopperConfig?.modifiers || []),
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary: 'viewport',
                                padding: 8
                            }
                        },
                        {
                            name: 'flip',
                            options: {
                                fallbackPlacements: ['top', 'bottom']
                            }
                        }
                    ]
                };
            }
        });
    }

    refreshHeldBadge();
    renderCart();
    loadProducts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPosTerminal);
} else {
    initPosTerminal();
}
</script>
@endpush
@endsection
