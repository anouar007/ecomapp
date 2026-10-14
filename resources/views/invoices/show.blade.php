@extends('layouts.app')

@section('content')
@php $withStamp = $withStamp ?? ($invoice->with_stamp ?? true); @endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

    /* ====================================================
       PROFESSIONAL MOROCCAN FACTURE / DEVIS — VIEW / PREVIEW
       Brand: Deep Slate #0f172a | Moroccan Ruby #dc2626
       Identical to Print & PDF layout (DGI Moroccan compliant)
       ==================================================== */

    .invoice-view-wrapper {
        padding: 32px 20px 60px 20px;
        max-width: 1040px;
        margin: 0 auto;
        font-family: "Roboto", sans-serif;
    }

    /* ── Pro Header & Action Buttons System ── */
    .invoice-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .invoice-top-actions {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Pro Button Base */
    .btn-pro,
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 38px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        font-family: inherit;
        border-radius: 9px;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        user-select: none;
        line-height: 1;
    }

    .btn-pro:hover,
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }

    .btn-pro:active,
    .btn-action:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }

    .btn-pro:focus-visible,
    .btn-action:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.15);
    }

    /* Remove bootstrap arrow and use custom pro chevron */
    .btn-pro.dropdown-toggle::after,
    .btn-action.dropdown-toggle::after {
        display: none !important;
    }

    .btn-chevron {
        font-size: 9.5px;
        transition: transform 0.2s ease;
        opacity: 0.6;
        margin-left: 3px;
    }

    .dropdown.show .btn-chevron,
    .dropdown.show button .btn-chevron {
        transform: rotate(180deg);
    }

    /* Dark Slate Primary (Télécharger PDF) */
    .btn-pro-primary {
        background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%) !important;
        color: #ffffff !important;
        border-color: #0f172a !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.12), 0 3px 8px -1px rgba(15, 23, 42, 0.24) !important;
    }

    .btn-pro-primary:hover {
        background: linear-gradient(180deg, #334155 0%, #1e293b 100%) !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px -1px rgba(15, 23, 42, 0.34) !important;
    }

    .btn-pro-primary i:not(.btn-chevron) {
        color: #ffffff;
    }

    /* Secondary White Card (Imprimer, Modifier) */
    .btn-pro-secondary {
        background: #ffffff !important;
        color: #1e293b !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    }

    .btn-pro-secondary:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06) !important;
    }

    .btn-pro-secondary i:not(.btn-chevron) {
        color: #64748b;
        transition: color 0.15s;
    }

    .btn-pro-secondary:hover i:not(.btn-chevron) {
        color: #0f172a;
    }

    /* Emerald Soft Tint (Garantie) */
    .btn-pro-emerald {
        background: linear-gradient(180deg, #f0fdf4 0%, #ecfdf5 100%) !important;
        color: #047857 !important;
        border-color: #a7f3d0 !important;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.06) !important;
    }

    .btn-pro-emerald:hover {
        background: linear-gradient(180deg, #dcfce7 0%, #d1fae5 100%) !important;
        color: #065f46 !important;
        border-color: #6ee7b7 !important;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.14) !important;
    }

    .btn-pro-emerald i:not(.btn-chevron) {
        color: #059669;
    }

    /* Success Gradient (Paiement) */
    .btn-pro-success {
        background: linear-gradient(180deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border-color: #059669 !important;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.15), 0 3px 8px rgba(16, 185, 129, 0.2) !important;
    }

    .btn-pro-success:hover {
        background: linear-gradient(180deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        border-color: #047857 !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28) !important;
    }

    /* Dropdown Floating Glass Card */
    .pro-dropdown-menu {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 12px 30px -4px rgba(15, 23, 42, 0.14), 0 4px 10px -2px rgba(15, 23, 42, 0.05) !important;
        padding: 6px !important;
        min-width: 240px !important;
        background: #ffffff !important;
        margin-top: 6px !important;
    }

    .pro-dropdown-header {
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.6px !important;
        padding: 6px 10px 4px !important;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pro-dropdown-item {
        padding: 7px 10px !important;
        border-radius: 8px !important;
        font-size: 12.5px !important;
        font-weight: 500 !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
    }

    .pro-dropdown-item:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }

    .pro-icon-box {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        flex-shrink: 0;
    }

    .pro-icon-box.emerald {
        background: #ecfdf5;
        color: #059669;
    }

    .pro-icon-box.slate {
        background: #f1f5f9;
        color: #64748b;
    }

    .pro-icon-box.amber {
        background: #fef3c7;
        color: #d97706;
    }

    .pro-dropdown-divider {
        border-top: 1px solid #f1f5f9 !important;
        margin: 5px 0 !important;
        list-style: none;
    }

    /* ── Pro Mode Aperçu Bar & Segmented Control ── */
    .invoice-preview-controls-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .preview-mode-tag {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .segmented-control {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 3px;
        gap: 2px;
    }

    .segmented-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 13px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.16s ease;
        white-space: nowrap;
        line-height: 1.2;
    }

    .segmented-btn:hover {
        color: #0f172a;
    }

    .segmented-btn.active {
        background: #ffffff;
        color: #0f172a;
        font-weight: 700;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .segmented-btn.active i {
        color: #0f172a;
    }

    .segmented-btn:not(.active) i {
        color: #94a3b8;
    }

    .stamp-pill-toggle {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5.5px 13px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        white-space: nowrap;
        line-height: 1.2;
    }

    .stamp-pill-toggle.stamp-on {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.06);
    }

    .stamp-pill-toggle.stamp-on:hover {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.12);
    }

    .stamp-pill-toggle.stamp-off {
        background: #ffffff;
        color: #64748b;
        border: 1px dashed #cbd5e1;
    }

    .stamp-pill-toggle.stamp-off:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #94a3b8;
        transform: translateY(-1px);
    }

    .stamp-indicator-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .stamp-pill-toggle.stamp-on .stamp-indicator-dot {
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }

    .stamp-pill-toggle.stamp-off .stamp-indicator-dot {
        background: #cbd5e1;
    }

    /* Back Link Pro */
    .invoice-back-link {
        color: #64748b;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        font-size: 12.5px;
        padding: 4px 10px;
        margin-left: -10px;
        border-radius: 6px;
        transition: all 0.15s ease;
        margin-bottom: 6px;
    }

    .invoice-back-link:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .invoice-back-link i {
        font-size: 11px;
        transition: transform 0.15s ease;
    }

    .invoice-back-link:hover i {
        transform: translateX(-2px);
    }

    /* ── The Document Card (Identical to Print) ── */
    .invoice-card {
        background: #ffffff;
        border-radius: 4px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin: 0 auto;
        color: #1e293b;
    }

    .accent-bar {
        height: 3px;
        background: #334155;
    }

    .doc-body {
        padding: 28px 36px;
    }

    /* ── Header ── */
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1.5px solid #e2e8f0;
        padding-bottom: 18px;
        margin-bottom: 18px;
    }

    .header-brand {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-logo {
        max-height: 54px;
        max-width: 140px;
        object-fit: contain;
    }

    .brand-text {
        display: flex;
        flex-direction: column;
    }

    .company-name {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        letter-spacing: -0.3px;
        margin: 0;
    }

    .company-tagline {
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
        margin-top: 4px;
        letter-spacing: 0.2px;
    }

    /* ── Document Type & Meta Block ── */
    .doc-badge-block {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .doc-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .doc-type-label {
        font-size: 24px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        line-height: 1;
        color: #0f172a;
    }

    .doc-type-label.facture { color: #0f172a; }
    .doc-type-label.devis   { color: #0f172a; }

    .doc-number-badge {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 3px 10px;
        border-radius: 4px;
        letter-spacing: 0.3px;
    }

    .doc-meta-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 12px;
        font-size: 11.5px;
        color: #64748b;
        margin-top: 4px;
    }

    .meta-item {
        white-space: nowrap;
    }

    .meta-k {
        color: #64748b;
    }

    .meta-v {
        font-weight: 700;
        color: #0f172a;
    }

    /* ── Parties row ── */
    .parties-row {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
    }

    .party-box {
        flex: 1;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-top: 2px solid #475569;
        padding: 14px 16px;
        border-radius: 4px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .party-box.client {
        border-top-color: #475569;
    }

    .party-role {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #64748b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .party-box.client .party-role {
        color: #64748b;
    }

    .party-name {
        font-size: 14.5px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .party-detail {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.5;
    }

    .fiscal-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 8px;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        font-size: 11px;
        color: #334155;
    }

    .fiscal-grid .f-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 3px;
        padding: 2px 7px;
        font-size: 10.5px;
        white-space: nowrap;
    }

    .fiscal-grid strong {
        color: #0f172a;
        font-weight: 700;
    }

    .client-ice-badge {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        margin-top: 6px;
    }

    /* ── Items table ── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        border-bottom: 1.5px solid #cbd5e1;
    }

    .items-table thead tr {
        background: #f8fafc;
    }

    .items-table thead th {
        padding: 10px 12px;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #475569;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1.5px solid #e2e8f0;
        text-align: left;
    }

    .items-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .items-table tbody tr:nth-child(even) {
        background: #fafafa;
    }

    .items-table tbody td {
        padding: 9px 12px;
        vertical-align: middle;
        font-size: 12px;
        color: #1e293b;
    }

    .item-name {
        font-size: 12.5px;
        font-weight: 700;
        color: #0f172a;
    }

    .item-sku {
        font-size: 10px;
        color: #64748b;
        margin-top: 2px;
    }

    /* ── Summary & Bottom Balanced Row ── */
    .summary-row {
        display: flex;
        gap: 18px;
        align-items: flex-start;
    }

    .summary-left {
        flex: 1.25;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .summary-right {
        flex: 1;
        max-width: 370px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .words-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #64748b;
        padding: 10px 14px;
        border-radius: 0 4px 4px 0;
    }

    .words-label {
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 3px;
    }

    .words-text {
        font-size: 11.5px;
        color: #475569;
        line-height: 1.4;
    }

    .words-highlight {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 2px;
        display: block;
    }

    .validity-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 11px;
        color: #64748b;
        line-height: 1.4;
    }

    .legal-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 11px;
        color: #64748b;
        line-height: 1.4;
    }

    .notes-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #94a3b8;
        padding: 8px 12px;
        border-radius: 0 4px 4px 0;
    }

    .notes-label {
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        margin-bottom: 2px;
    }

    .notes-text {
        font-size: 11px;
        color: #475569;
        margin: 0;
        line-height: 1.4;
    }

    .totals-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 14px 18px;
        color: #0f172a;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
        font-size: 12px;
    }

    .total-row-label {
        color: #64748b;
        font-weight: 500;
    }

    .total-row-val {
        color: #0f172a;
        font-weight: 700;
        font-size: 12.5px;
    }

    .totals-divider {
        border: none;
        border-top: 1.5px solid #e2e8f0;
        margin: 8px 0;
    }

    .grand-total-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }

    .grand-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #0f172a;
    }

    .grand-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.3px;
    }

    .stamp-box {
        background: transparent;
        border: none;
        padding: 6px 0 0 0;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stamp-img {
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    /* ── Footer ── */
    .doc-footer {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 14px 36px;
        text-align: center;
        color: #64748b;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* ── Mobile First & Super Responsive Layouts ── */
    @media (max-width: 768px) {
        .invoice-view-wrapper {
            padding: 12px 10px 40px 10px !important;
        }
        .invoice-top-bar {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 14px !important;
        }
        .invoice-top-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
            width: 100% !important;
        }
        .invoice-top-actions .btn-pro,
        .invoice-top-actions .btn-action,
        .invoice-top-actions .dropdown,
        .invoice-top-actions .dropdown > button {
            width: 100% !important;
            justify-content: center !important;
            font-size: 12px !important;
            padding: 0 10px !important;
        }
        .doc-body {
            padding: 16px 14px !important;
        }
        .header-row {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 14px !important;
            padding-bottom: 14px !important;
            margin-bottom: 14px !important;
        }
        .header-brand {
            width: 100% !important;
        }
        .doc-badge-block {
            align-items: flex-start !important;
            text-align: left !important;
            width: 100% !important;
        }
        .doc-title-row {
            width: 100% !important;
            justify-content: space-between !important;
        }
        .doc-meta-grid {
            justify-content: flex-start !important;
            width: 100% !important;
            gap: 6px 12px !important;
        }
        .parties-row {
            flex-direction: column !important;
            gap: 12px !important;
            margin-bottom: 16px !important;
        }
        .party-box {
            width: 100% !important;
            padding: 12px 14px !important;
        }
        .items-table {
            min-width: 520px !important;
        }
        .summary-row {
            flex-direction: column-reverse !important;
            gap: 14px !important;
        }
        .summary-left, .summary-right {
            width: 100% !important;
            max-width: none !important;
        }
        .totals-card {
            padding: 14px 16px !important;
        }
        .grand-value {
            font-size: 19px !important;
        }
        .doc-footer {
            padding: 12px 16px !important;
            font-size: 10px !important;
        }
    }
</style>

<div class="invoice-view-wrapper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 24px;">
        <div class="invoice-top-bar">
            <div>
                <a href="{{ route('invoices.index') }}" class="invoice-back-link">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                    <span>{{ __('Retour aux Factures / Devis') }}</span>
                </a>
                <div style="display: flex; align-items: center; gap: 12px; margin-top: 2px;">
                    <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px; display: inline-flex; align-items: baseline; gap: 8px;">
                        <span>{{ $invoice->isQuote() ? 'DEVIS' : 'FACTURE' }}</span>
                        <span style="color: #64748b; font-weight: 600; font-size: 19px;">#{{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </h1>
                </div>

                {{-- Interactive Mode Controls (Facture / Devis & Stamp) --}}
                <div class="invoice-preview-controls-bar">
                    <span class="preview-mode-tag">
                        <i class="fas fa-sliders" style="font-size: 10px;"></i>
                        <span>{{ __('Mode Aperçu') }}:</span>
                    </span>

                    {{-- Segmented Facture / Devis switcher --}}
                    <div class="segmented-control" role="group" aria-label="Type de document">
                        <a href="{{ route('invoices.show', [$invoice, 'as' => 'facture', 'with_stamp' => $withStamp ? 1 : 0]) }}" 
                           class="segmented-btn {{ !$invoice->isQuote() ? 'active' : '' }}"
                           title="Afficher comme Facture">
                            <i class="fas fa-file-invoice"></i>
                            <span>Facture</span>
                        </a>
                        <a href="{{ route('invoices.show', [$invoice, 'as' => 'devis', 'with_stamp' => $withStamp ? 1 : 0]) }}" 
                           class="segmented-btn {{ $invoice->isQuote() ? 'active' : '' }}"
                           title="Afficher comme Devis">
                            <i class="fas fa-file-signature"></i>
                            <span>Devis</span>
                        </a>
                    </div>

                    {{-- Toggle Stamp Pill --}}
                    <a href="{{ route('invoices.show', [$invoice, 'as' => $invoice->isQuote() ? 'devis' : 'facture', 'with_stamp' => $withStamp ? 0 : 1]) }}"
                       class="stamp-pill-toggle {{ $withStamp ? 'stamp-on' : 'stamp-off' }}"
                       title="{{ $withStamp ? 'Désactiver le cachet officiel' : 'Activer le cachet officiel' }}">
                        <span class="stamp-indicator-dot"></span>
                        <i class="fas fa-stamp" style="font-size: 11px;"></i>
                        <span>{{ $withStamp ? 'Avec Cachet' : 'Sans Cachet' }}</span>
                    </a>
                </div>
            </div>

            <div class="invoice-top-actions">
                <!-- Download PDF Dropdown -->
                <div class="dropdown">
                    <button class="btn-pro btn-pro-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                        <i class="fas fa-file-pdf"></i>
                        <span>{{ __('Télécharger PDF') }}</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li class="pro-dropdown-header">
                            <i class="fas fa-file-invoice text-dark"></i> Facture
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Facture avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Document officiel tamponné</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="far fa-file-pdf"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Facture sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Version standard épurée</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-file-signature text-danger"></i> Devis
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Devis avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Proposition avec cachet</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.download', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="far fa-file-pdf"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Devis sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Proposition commerciale</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Print Dropdown -->
                <div class="dropdown">
                    <button class="btn-pro btn-pro-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                        <i class="fas fa-print"></i>
                        <span>{{ __('Imprimer') }}</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li class="pro-dropdown-header">
                            <i class="fas fa-file-invoice text-dark"></i> Facture
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer Facture (Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Prêt pour signature</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'facture', 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer Facture (Standard)</span>
                                    <span class="text-muted" style="font-size: 11px;">Sans cachet</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-file-signature text-danger"></i> Devis
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer Devis (Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Avec cachet officiel</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.print', [$invoice, 'as' => 'devis', 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer Devis (Standard)</span>
                                    <span class="text-muted" style="font-size: 11px;">Sans cachet</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Guarantee Certificate Dropdown -->
                <div class="dropdown">
                    <button class="btn-pro btn-pro-emerald dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                        <i class="fas fa-shield-alt"></i>
                        <span>{{ __('Garantie') }}</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li class="pro-dropdown-header">
                            <i class="fas fa-shield-alt text-success"></i> Certificat de Garantie
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.guarantee', $invoice) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-eye"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Consulter le Certificat</span>
                                    <span class="text-muted" style="font-size: 11px;">Page dédiée & mentions</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-download text-muted"></i> Téléchargement PDF
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">PDF avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Certificat certifié</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.guarantee.download', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="far fa-file-pdf"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">PDF sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Version vierge</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-print text-muted"></i> Impression Directe
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer (avec Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Format A4 certifié</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.guarantee.print', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer (sans Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Format A4 standard</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                @if($invoice->canEdit())
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn-pro btn-pro-secondary">
                    <i class="fas fa-pen-to-square"></i>
                    <span>{{ __('Modifier') }}</span>
                </a>
                @endif

                @if($invoice->isInvoice() && $invoice->remaining_balance > 0)
                <button type="button" class="btn-pro btn-pro-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>{{ __('Paiement') }}</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- ══════════ EXACT MOROCCAN DOCUMENT VIEW (IDENTICAL TO PRINT & PDF) ══════════ -->
    <div class="invoice-card">
        <div class="accent-bar"></div>
        <div class="doc-body">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="header-row">
                {{-- Left: Brand & Logo --}}
                <div class="header-brand">
                    @if(setting('app_logo'))
                        <img src="/storage/{{ setting('app_logo') }}" class="header-logo" alt="Logo" onerror="this.src='{{ asset('storage/' . setting('app_logo')) }}'">
                    @endif
                    <div class="brand-text">
                        <div class="company-name">{{ setting('company_name', setting('app_name')) }}</div>
                        @if(setting('company_website'))
                            <div class="company-tagline">{{ setting('company_website') }}</div>
                        @endif
                    </div>
                </div>

                {{-- Right: Document Type, Number & Key Dates --}}
                <div class="doc-badge-block">
                    <div class="doc-title-row">
                        <span class="doc-type-label {{ $invoice->isQuote() ? 'devis' : 'facture' }}">
                            {{ $invoice->isQuote() ? 'DEVIS' : 'FACTURE' }}
                        </span>
                        <span class="doc-number-badge">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-grid">
                        <div class="meta-item"><span class="meta-k">Date :</span> <span class="meta-v">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
                        @if($invoice->isQuote())
                            <div class="meta-item"><span class="meta-k">Validité :</span> <span class="meta-v">{{ $invoice->issued_at->addDays(30)->format('d/m/Y') }} (30j)</span></div>
                        @elseif($invoice->due_date)
                            <div class="meta-item"><span class="meta-k">Échéance :</span> <span class="meta-v">{{ $invoice->due_date->format('d/m/Y') }}</span></div>
                        @endif
                        @if($invoice->order && $invoice->order->order_number)
                            <div class="meta-item"><span class="meta-k">Réf. Commande :</span> <span class="meta-v">{{ $invoice->order->order_number }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ PARTIES (ÉMETTEUR & CLIENT) ══════════ --}}
            <div class="parties-row">
                {{-- Vendor / Émetteur --}}
                <div class="party-box">
                    <div>
                        <div class="party-role"><i class="fas fa-building"></i>Émetteur / Fournisseur</div>
                        <div class="party-name">{{ setting('company_name', setting('app_name')) }}</div>
                        <div class="party-detail">
                            @if(setting('company_address'))<div>{{ setting('company_address') }}</div>@endif
                            <div style="margin-top:3px;">
                                @if(setting('company_phone'))<span><i class="fas fa-phone" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ setting('company_phone') }}</span>@endif
                                @if(setting('company_phone') && setting('company_email'))&nbsp;•&nbsp;@endif
                                @if(setting('company_email'))<span><i class="fas fa-envelope" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ setting('company_email') }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @if(setting('company_tax_id') || setting('company_registry_id') || setting('company_fiscal_id') || setting('company_patente'))
                    <div class="fiscal-grid">
                        @if(setting('company_tax_id'))<div class="f-item"><strong>ICE:</strong> {{ setting('company_tax_id') }}</div>@endif
                        @if(setting('company_registry_id'))<div class="f-item"><strong>RC:</strong> {{ setting('company_registry_id') }}</div>@endif
                        @if(setting('company_fiscal_id'))<div class="f-item"><strong>IF:</strong> {{ setting('company_fiscal_id') }}</div>@endif
                        @if(setting('company_patente'))<div class="f-item"><strong>Patente:</strong> {{ setting('company_patente') }}</div>@endif
                    </div>
                    @endif
                </div>

                {{-- Client / Destinataire --}}
                <div class="party-box client">
                    <div>
                        <div class="party-role"><i class="fas fa-user-check"></i>{{ $invoice->isQuote() ? 'Destinataire' : 'Client / Facturé à' }}</div>
                        <div class="party-name">{{ $invoice->customer_name }}</div>
                        <div class="party-detail">
                            @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                            @if($address)<div>{{ $address }}</div>@endif
                            <div style="margin-top:3px;">
                                @if($invoice->customer_phone)<span><i class="fas fa-phone" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $invoice->customer_phone }}</span>@endif
                                @if($invoice->customer_phone && $invoice->customer_email)&nbsp;•&nbsp;@endif
                                @if($invoice->customer_email)<span><i class="fas fa-envelope" style="font-size:10px; color:#94a3b8; margin-right:3px;"></i>{{ $invoice->customer_email }}</span>@endif
                            </div>
                        </div>
                    </div>
                    @if($invoice->ice)
                    <div style="margin-top:10px; padding-top:8px; border-top:1px solid #e2e8f0;">
                        <span class="client-ice-badge"><strong>ICE Client :</strong> {{ $invoice->ice }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ══════════ ITEMS TABLE ══════════ --}}
            <div class="table-responsive border-0 mb-3" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="items-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:5%; text-align:center;">N°</th>
                            <th style="width:45%;">Désignation / Description</th>
                            <th style="width:8%; text-align:center;">Qté</th>
                            <th style="width:14%; text-align:right;">P.U. TTC</th>
                            <th style="width:10%; text-align:center;">{{ $invoice->tax_label }} %</th>
                            <th style="width:18%; text-align:right;">Total TTC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td style="text-align:center; color:#94a3b8; font-size:11px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="item-name">{{ $item->product_name }}</div>
                                @if($item->product_sku)
                                <div class="item-sku">Réf : {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:center; font-weight:700; font-size:13px;">{{ $item->quantity }}</td>
                            <td style="text-align:right; font-size:12.5px;">{{ $item->formatted_unit_price }}</td>
                            <td style="text-align:center; color:#64748b; font-size:12px;">{{ $invoice->formatted_tax_rate }}</td>
                            <td style="text-align:right; font-weight:800; font-size:13px; color:#0f172a;">{{ $item->formatted_total_price }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ══════════ SUMMARY ROW (BALANCED SPACE USAGE) ══════════ --}}
            <div class="summary-row">
                {{-- Left: Arrêté + Mentions légales + Notes --}}
                <div class="summary-left">
                    <div class="words-card">
                        <div class="words-label"><i class="fas fa-file-contract" style="margin-right:4px;"></i>Montant en Lettres</div>
                        <div class="words-text">
                            {{ $invoice->isQuote() ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :' }}
                            <span class="words-highlight">{{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC</span>
                        </div>
                    </div>

                    @if($invoice->isQuote())
                    <div class="validity-banner">
                        <i class="fas fa-calendar-check" style="margin-right:5px;"></i>
                        Validité de l'offre : <strong>30 jours</strong> à compter de la date d'émission. Ce document ne constitue pas une facture.
                    </div>
                    @else
                    <div class="legal-banner">
                        <i class="fas fa-shield-alt" style="margin-right:5px; color:#0f172a;"></i>
                        Facture payable à réception. En cas de retard de paiement, des pénalités seront appliquées conformément aux dispositions légales (CGI Maroc).
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-card">
                        <div class="notes-label"><i class="fas fa-sticky-note" style="margin-right:4px;"></i>Observations / Notes</div>
                        <p class="notes-text">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Right: Totals Card + Cachet Box --}}
                <div class="summary-right">
                    <div class="totals-card">
                        <div class="total-row">
                            <span class="total-row-label">Sous-total HT</span>
                            <span class="total-row-val">{{ $invoice->formatted_subtotal }}</span>
                        </div>
                        <div class="total-row">
                            <span class="total-row-label">{{ $invoice->tax_label }} ({{ $invoice->formatted_tax_rate }})</span>
                            <span class="total-row-val">{{ $invoice->formatted_tax_amount }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                        <div class="total-row">
                            <span class="total-row-label">Remise</span>
                            <span class="total-row-val" style="color:#059669;">- {{ $invoice->formatted_discount_amount }}</span>
                        </div>
                        @endif
                        <hr class="totals-divider">
                        <div class="grand-total-row">
                            <span class="grand-label">Total TTC</span>
                            <span class="grand-value">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>

                    {{-- Cachet Société placed right under Totals for perfect visual balance --}}
                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                    <div class="stamp-box">
                        <img src="/storage/{{ setting('company_stamp') }}"
                             class="stamp-img"
                             style="max-width:{{ round(140 * intval(setting('company_stamp_scale', 100)) / 100) }}px; max-height:85px;"
                             alt="Cachet"
                             onerror="this.src='{{ asset('storage/' . setting('company_stamp')) }}'">
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ══════════ FOOTER ══════════ --}}
        <div class="doc-footer">
            <i class="far fa-handshake" style="margin-right:6px; font-size:11px;"></i>
            {{ $invoice->isQuote() ? 'Merci de votre confiance !' : 'Merci pour votre achat !' }}
        </div>
    </div>

    {{-- Payment History (Admin Section) --}}
    @if($invoice->payments->count() > 0)
    <div style="margin-top: 36px; background: white; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h3 style="font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 16px;">
            <i class="fas fa-receipt me-2" style="color: #64748b;"></i>{{ __('Historique des Règlements') }}
        </h3>
        
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Date') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Mode') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase;">{{ __('Référence') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase; text-align: right;">{{ __('Montant') }}</th>
                    <th style="font-size: 11px; text-transform: uppercase; text-align: center;">{{ __('Justificatif') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td style="font-size: 12px; color: #475569;">{{ $payment->payment_date->translatedFormat('d M, Y') }}</td>
                    <td style="font-size: 12px; font-weight: 600; color: #0f172a;">{{ __(ucfirst(str_replace('_', ' ', $payment->payment_method))) }}</td>
                    <td style="font-size: 12px; color: #64748b;">{{ $payment->transaction_reference ?? '-' }}</td>
                    <td style="font-size: 12px; font-weight: 700; color: #059669; text-align: right;">{{ currency($payment->amount) }}</td>
                    <td style="text-align: center;">
                        @if($payment->proof_file_path)
                        <a href="{{ asset('storage/' . $payment->proof_file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px;">
                            <i class="fas fa-file-alt"></i> {{ __('Voir') }}
                        </a>
                        @else
                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Record Payment Modal -->
@if($invoice->isInvoice() && $invoice->remaining_balance > 0)
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Enregistrer un Paiement') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('payments.store', $invoice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Montant du règlement') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ setting('currency_symbol', 'DH') }}</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $invoice->remaining_balance }}" max="{{ $invoice->remaining_balance }}" required>
                        </div>
                        <small class="text-muted">{{ __('Solde restant') }}: {{ $invoice->formatted_remaining_balance }}</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Date de paiement') }}</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Mode de paiement') }}</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">{{ __('Espèces') }}</option>
                            <option value="bank_transfer">{{ __('Virement bancaire') }}</option>
                            <option value="check">{{ __('Chèque') }}</option>
                            <option value="card">{{ __('Carte bancaire') }}</option>
                            <option value="other">{{ __('Autre') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Numéro de référence / Chèque / Transaction') }}</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="ex: CHQ-928120">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Justificatif de paiement') }}</label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn btn-primary" style="background:#0f172a; border-color:#0f172a;">{{ __('Enregistrer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
