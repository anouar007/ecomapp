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

    /* Info Blue Gradient (Envoyer par Email) */
    .btn-pro-info {
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border-color: #1d4ed8 !important;
        box-shadow: 0 1px 2px rgba(37, 99, 235, 0.15), 0 3px 8px rgba(37, 99, 235, 0.22) !important;
    }

    .btn-pro-info:hover {
        background: linear-gradient(180deg, #1d4ed8 0%, #1e40af 100%) !important;
        color: #ffffff !important;
        border-color: #1e40af !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3) !important;
    }

    .btn-pro-info i:not(.btn-chevron) {
        color: #ffffff;
    }

    /* Sky Blue Gradient (Bon de Livraison) */
    .btn-pro-sky {
        background: linear-gradient(180deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        border-color: #0369a1 !important;
        box-shadow: 0 1px 2px rgba(2, 132, 199, 0.15), 0 3px 8px rgba(2, 132, 199, 0.22) !important;
    }

    .btn-pro-sky:hover {
        background: linear-gradient(180deg, #0369a1 0%, #075985 100%) !important;
        color: #ffffff !important;
        border-color: #075985 !important;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3) !important;
    }

    .btn-pro-sky i:not(.btn-chevron) {
        color: #ffffff;
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
        border-radius: 6px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin: 0 auto;
        color: #1e293b;
        min-height: 1060px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .accent-bar {
        height: 4px;
        background: #0f172a;
        width: 100%;
        flex-shrink: 0;
    }

    .doc-body {
        padding: 28px 36px 16px 36px;
        flex: 1;
        display: flex;
        flex-direction: column;
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

    /* ── Wide, Low-Height Client Banner (Full Width, Low Height) ── */
    .client-banner {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 3.5px solid #0f172a;
        border-radius: 4px;
        padding: 10px 16px;
        margin-bottom: 18px;
        flex-shrink: 0;
    }

    .client-banner-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .client-role-badge {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .client-ice-badge {
        display: inline-block;
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #cbd5e1;
        padding: 1px 8px;
        border-radius: 3px;
        font-size: 10.5px;
        font-weight: 700;
    }

    .client-banner-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .client-main-name {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.2px;
    }

    .client-details-row {
        display: flex;
        align-items: center;
        gap: 16px;
        font-size: 11.5px;
        color: #475569;
        flex-wrap: wrap;
    }

    .c-info-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .c-info-item i {
        font-size: 10px;
        color: #94a3b8;
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
        border-top: 1.5px solid #e2e8f0;
        padding: 12px 36px;
        text-align: center;
        color: #64748b;
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.2px;
        line-height: 1.5;
        margin-top: auto;
        flex-shrink: 0;
    }

    .footer-legal {
        color: #334155;
        font-weight: 600;
    }

    .footer-fiscal {
        color: #64748b;
        margin-top: 2px;
    }

    .footer-closing {
        margin-top: 4px;
        font-size: 10.5px;
        color: #0f172a;
        font-weight: 700;
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
        .client-banner-body {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
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

                <!-- Delivery Note (Bon de Livraison) Dropdown -->
                <div class="dropdown">
                    <button class="btn-pro btn-pro-sky dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                        <i class="fas fa-truck"></i>
                        <span>{{ __('Bon de Livraison') }}</span>
                        <i class="fas fa-chevron-down btn-chevron"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end pro-dropdown-menu">
                        <li class="pro-dropdown-header">
                            <i class="fas fa-truck text-primary"></i> Bon de Livraison (BL Maroc)
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.delivery-note', $invoice) }}">
                                <span class="pro-icon-box emerald" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-eye"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Consulter le BL</span>
                                    <span class="text-muted" style="font-size: 11px;">Document conforme & décharge</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-file-pdf text-muted"></i> Télécharger PDF
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.delivery-note.download', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-stamp"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">BL avec Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Document officiel tamponné</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" href="{{ route('invoices.delivery-note.download', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="far fa-file-pdf"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">BL sans Cachet</span>
                                    <span class="text-muted" style="font-size: 11px;">Pour signature manuelle</span>
                                </div>
                            </a>
                        </li>
                        <li class="pro-dropdown-divider"></li>
                        <li class="pro-dropdown-header">
                            <i class="fas fa-print text-muted"></i> Impression Directe
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.delivery-note.print', [$invoice, 'with_stamp' => 1]) }}">
                                <span class="pro-icon-box emerald"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer BL (avec Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Format A4</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="pro-dropdown-item" target="_blank" href="{{ route('invoices.delivery-note.print', [$invoice, 'with_stamp' => 0]) }}">
                                <span class="pro-icon-box slate"><i class="fas fa-print"></i></span>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">Imprimer BL (sans Cachet)</span>
                                    <span class="text-muted" style="font-size: 11px;">Format A4 standard</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Send by Email Button -->
                <button type="button" 
                        class="btn-pro btn-pro-info" 
                        onclick="openInvoiceEmailModal({{ $invoice->id }}, '{{ addslashes($invoice->display_number ?? $invoice->invoice_number) }}', '{{ addslashes($invoice->customer_name ?? '') }}', '{{ addslashes($invoice->customer_email ?? '') }}', '{{ $invoice->isQuote() ? 'devis' : 'facture' }}', {{ $withStamp ? 'true' : 'false' }}, '{{ $invoice->formatted_total_amount }}')"
                        title="Envoyer la facture ou le devis par email au client">
                    <i class="fas fa-paper-plane"></i>
                    <span>{{ __('Envoyer par Email') }}</span>
                </button>

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

    @php
        $companyName = setting('company_name') ?: (setting('app_name') ?: config('app.name', 'WINA SHOP'));
        $companyTagline = setting('company_tagline') ?: (setting('company_website') ?: 'Vente de Matériel & Accessoires');
        $companyLogo = setting('company_logo') ?: setting('app_logo');
        $companyAddress = setting('company_address') ?: 'Casablanca, Maroc';
        $companyPhone = setting('company_phone');
        $companyEmail = setting('company_email') ?: config('mail.from.address', 'contact@winashop.com');
        $companyIce = setting('company_tax_id') ?: setting('company_ice', '002938475000089');
        $companyRc = setting('company_registry_id') ?: setting('company_rc', '49821');
        $companyIf = setting('company_fiscal_id') ?: setting('company_if', '39482710');
        $companyPatente = setting('company_patente') ?: '37920194';
        $companyCnss = setting('company_cnss');
        $isQuote = $invoice->isQuote();
    @endphp

    <!-- ══════════ EXACT MOROCCAN DOCUMENT VIEW (IDENTICAL TO PRINT & PDF) ══════════ -->
    <div class="invoice-card">
        <div class="accent-bar"></div>
        <div class="doc-body">

            {{-- ══════════ HEADER ══════════ --}}
            <div class="header-row">
                {{-- Left: Brand & Logo --}}
                <div class="header-brand">
                    @if(app_logo_url())
                        <img src="{{ app_logo_url() }}" class="header-logo" alt="Logo">
                    @else
                        <div style="width: 46px; height: 46px; background: #0f172a; color: #ffffff; font-size: 22px; font-weight: 800; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($companyName, 0, 1)) }}
                        </div>
                    @endif
                    <div class="brand-text">
                        <div class="company-name">{{ $companyName }}</div>
                        <div class="company-tagline">{{ $companyTagline }}</div>
                    </div>
                </div>

                {{-- Right: Document Type, Number & Key Dates --}}
                <div class="doc-badge-block">
                    <div class="doc-title-row">
                        <span class="doc-type-label {{ $isQuote ? 'devis' : 'facture' }}">
                            {{ $isQuote ? 'DEVIS' : 'FACTURE' }}
                        </span>
                        <span class="doc-number-badge">N° {{ $invoice->display_number ?? $invoice->invoice_number }}</span>
                    </div>
                    <div class="doc-meta-grid">
                        <div class="meta-item"><span class="meta-k">Date :</span> <span class="meta-v">{{ $invoice->issued_at->format('d/m/Y') }}</span></div>
                        @if($isQuote)
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

            {{-- ══════════ CLIENT (FULL WIDTH, LOW HEIGHT) ══════════ --}}
            <div class="client-banner">
                <div class="client-banner-header">
                    <span class="client-role-badge">
                        <i class="fas fa-user-check"></i>
                        {{ $isQuote ? 'Destinataire / Devis pour' : 'Client / Facturé à' }}
                    </span>
                    @if($invoice->ice)
                        <span class="client-ice-badge">ICE : {{ $invoice->ice }}</span>
                    @endif
                </div>
                <div class="client-banner-body">
                    <div class="client-main-name">{{ $invoice->customer_name }}</div>
                    <div class="client-details-row">
                        @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                        @if($address)
                            <span class="c-info-item"><i class="fas fa-map-marker-alt"></i> {{ $address }}{{ $invoice->customer_city ? ', ' . $invoice->customer_city : '' }}</span>
                        @elseif($invoice->customer_city)
                            <span class="c-info-item"><i class="fas fa-map-marker-alt"></i> {{ $invoice->customer_city }}</span>
                        @endif
                        @if($invoice->customer_phone)
                            <span class="c-info-item"><i class="fas fa-phone"></i> {{ $invoice->customer_phone }}</span>
                        @endif
                        @if($invoice->customer_email)
                            <span class="c-info-item"><i class="fas fa-envelope"></i> {{ $invoice->customer_email }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══════════ ITEMS TABLE ══════════ --}}
            <div class="table-responsive border-0 mb-3" style="overflow-x: auto; -webkit-overflow-scrolling: touch; flex: 1;">
                <table class="items-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:5%; text-align:center;">N°</th>
                            <th style="width:47%;">Désignation / Description</th>
                            <th style="width:18%; text-align:right;">P.U. HT</th>
                            <th style="width:10%; text-align:center;">Qté</th>
                            <th style="width:20%; text-align:right;">Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                        <tr>
                            <td style="text-align:center; color:#94a3b8; font-size:11px;">{{ $i + 1 }}</td>
                            <td>
                                <div class="item-name">
                                    {{ $item->product_name }}
                                    @if($item->variant_name)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size: 11px; font-weight: normal; vertical-align: middle;">{{ $item->variant_name }}</span>
                                    @endif
                                </div>
                                @if($item->product_sku)
                                <div class="item-sku">Réf : {{ $item->product_sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:right; font-size:12.5px;">{{ $item->formatted_unit_price_ht }}</td>
                            <td style="text-align:center; font-weight:700; font-size:13px;">{{ $item->quantity }}</td>
                            <td style="text-align:right; font-weight:800; font-size:13px; color:#0f172a;">{{ $item->formatted_total_price_ht }}</td>
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
                            {{ $isQuote ? 'Arrêté le présent devis à la somme de :' : 'Arrêtée la présente facture à la somme de :' }}
                            <span class="words-highlight">{{ $invoice->total_in_words }} {{ setting('currency_code', 'MAD') }} TTC</span>
                        </div>
                    </div>

                    @if($isQuote)
                    <div class="validity-banner">
                        <i class="fas fa-calendar-check" style="margin-right:5px; color:#0f172a;"></i>
                        Validité de l'offre : <strong>30 jours</strong>. Ce devis est une proposition commerciale. Pour accord, veuillez nous le retourner signé et revêtu de votre mention <em>« Bon pour accord »</em>.
                    </div>
                    @else
                    <div class="legal-banner">
                        <i class="fas fa-shield-alt" style="margin-right:5px; color:#0f172a;"></i>
                        Facture payable selon les conditions convenues. Mode de règlement : <strong>{{ ucfirst(str_replace('_', ' ', $invoice->payment_method ?: 'Espèces')) }}</strong>. En cas de retard de paiement, des pénalités seront appliquées conformément au CGI Maroc.
                    </div>
                    @endif

                    @if($invoice->notes)
                    <div class="notes-card">
                        <div class="notes-label"><i class="fas fa-sticky-note" style="margin-right:4px;"></i>Observations / Instructions</div>
                        <p class="notes-text">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Right: Totals Card --}}
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
                        @if($invoice->shipping_cost > 0)
                        <div class="total-row">
                            <span class="total-row-label">Frais de livraison</span>
                            <span class="total-row-val">{{ $invoice->formatted_shipping_cost }}</span>
                        </div>
                        @endif
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

                    {{-- Dedicated space for the auto-added Cachet --}}
                    <div class="stamp-box">
                        @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                            <img src="{{ asset('storage/' . setting('company_stamp')) }}"
                                 class="stamp-img"
                                 style="max-width:{{ round(140 * intval(setting('company_stamp_scale', 100)) / 100) }}px; max-height:80px;"
                                 alt="Cachet"
                                 onerror="this.src='/storage/{{ setting('company_stamp') }}'">
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════ EXECUTIVE MOROCCAN LEGAL FOOTER (PINNED TO BOTTOM) ══════════ --}}
        <div class="doc-footer">
            <div class="footer-legal">
                <strong>{{ $companyName }}</strong>
                @if(setting('company_legal_form', 'SARL')) &bull; Société {{ setting('company_legal_form', 'SARL') }} @endif
                @if(setting('company_capital')) &bull; Capital : {{ setting('company_capital') }} DH @endif
                @if($companyAddress) &bull; {{ $companyAddress }} @endif
                @if($companyPhone) &bull; Tél : {{ $companyPhone }} @endif
                @if($companyEmail) &bull; {{ $companyEmail }} @endif
            </div>
            <div class="footer-fiscal">
                @if($companyIce) <span><strong>ICE :</strong> {{ $companyIce }}</span> @endif
                @if($companyRc) <span>&bull; <strong>RC :</strong> {{ $companyRc }}</span> @endif
                @if($companyIf) <span>&bull; <strong>IF :</strong> {{ $companyIf }}</span> @endif
                @if($companyPatente) <span>&bull; <strong>Patente :</strong> {{ $companyPatente }}</span> @endif
                @if($companyCnss) <span>&bull; <strong>CNSS :</strong> {{ $companyCnss }}</span> @endif
            </div>
            <div class="footer-closing">
                <i class="fas fa-handshake" style="margin-right:4px; font-size:10.5px; color:#0f172a;"></i>
                {{ $isQuote ? 'Merci pour votre confiance ! Proposition soumise à nos conditions générales.' : 'Merci pour votre achat et votre confiance !' }}
            </div>
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

@include('invoices.partials.email-modal')
@endsection
