@extends('layouts.frontend')

@push('styles')
<style>
    /* Guarantee strong & bold tags render with crisp, clear bold weight */
    .pdp-description b,
    .pdp-description strong,
    .pdp-enrich-body b,
    .pdp-enrich-body strong,
    .entry-content b,
    .entry-content strong {
        font-weight: 700 !important;
        color: #0f172a;
    }

    /* Disable zoom on product image */
    .pdp-main-image-wrap {
        cursor: default !important;
        position: relative;
    }
    .pdp-main-image-wrap:hover .pdp-main-image {
        transform: none !important;
    }

    /* PDP Main Gallery Swiper */
    .pdp-gallery-swiper {
        width: 100%;
        height: 100%;
        position: relative;
    }
    .pdp-gallery-swiper .swiper-wrapper {
        align-items: center;
    }
    .pdp-gallery-swiper .swiper-slide {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: transparent;
    }
    .pdp-gallery-swiper .pdp-main-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        user-select: none;
        -webkit-user-drag: none;
    }
    .pdp-gallery-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        opacity: 0.85;
    }
    .pdp-gallery-nav:hover {
        background: #ffffff;
        opacity: 1;
        transform: translateY(-50%) scale(1.08);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16);
    }
    .pdp-gallery-prev {
        left: 10px;
    }
    .pdp-gallery-next {
        right: 10px;
    }
    .pdp-gallery-nav.swiper-button-disabled {
        opacity: 0;
        pointer-events: none;
    }
    .pdp-gallery-pagination {
        position: absolute;
        bottom: 10px !important;
        left: 0;
        right: 0;
        z-index: 10;
        display: flex;
        justify-content: center;
        gap: 6px;
    }
    .pdp-gallery-bullet {
        width: 8px;
        height: 8px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.25);
        cursor: pointer;
        transition: all 0.25s ease;
    }
    .pdp-gallery-bullet-active {
        width: 22px;
        background: var(--accent, #011962);
    }
    @media (min-width: 992px) {
        .pdp-gallery-pagination {
            display: none; /* Desktop uses thumbnails below */
        }
    }

    /* Pixel-perfect alignment for Row 1 (Qty + AddToCart) & Row 2 (Partager + Acheter) */
    .pdp-qty-wrap,
    .pdp-share-btn {
        flex: 0 0 124px !important;
        width: 124px !important;
        min-width: 124px !important;
        max-width: 124px !important;
        height: 48px !important;
        box-sizing: border-box !important;
        border-radius: 8px !important;
    }

    .pdp-commander-wrap {
        display: flex !important;
        flex-direction: row !important;
        flex-nowrap: nowrap !important;
        align-items: stretch !important;
        gap: 12px !important;
        width: 100% !important;
        margin-top: 10px !important;
    }

    .pdp-commander-btn {
        flex: 1 1 auto !important;
        width: auto !important;
        min-width: 0 !important;
        height: 48px !important;
        border-radius: 8px !important;
    }

    .pdp-share-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1.5px solid #0f172a !important;
        border-radius: 8px !important;
        font-size: 0.9rem !important;
        font-family: var(--font-heading, "Roboto", sans-serif) !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        text-decoration: none !important;
        user-select: none !important;
        white-space: nowrap !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: none !important;
        padding: 0 10px !important;
    }

    .pdp-share-btn i {
        font-size: 0.88rem !important;
        color: #0f172a !important;
        transition: color 0.2s ease, transform 0.2s ease !important;
    }

    .pdp-share-btn:hover {
        background: #f8fafc !important;
        border-color: #011962 !important;
        color: #011962 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    }

    .pdp-share-btn:hover i {
        color: #011962 !important;
        transform: rotate(-15deg);
    }

    .pdp-share-btn:active {
        transform: translateY(0) !important;
    }

    .pdp-share-btn.is-copied {
        background: #f0fdf4 !important;
        border-color: #16a34a !important;
        color: #15803d !important;
        transform: none !important;
    }

    .pdp-share-btn.is-copied i {
        color: #16a34a !important;
        transform: none !important;
    }

    @keyframes pdpShake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }

    .pdp-variants-wrapper {
        transition: all 0.25s ease;
    }

    .pdp-variants-wrapper.pdp-attention {
        animation: pdpShake 0.45s ease-in-out;
        border: 1.5px solid #dc2626 !important;
        border-radius: 12px;
        padding: 10px 12px;
        background: #fff5f5;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
    }

    .pdp-variant-pill.is-out-of-stock,
    .pdp-variant-pill:disabled {
        opacity: 0.70 !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        color: #94a3b8 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .pdp-enrich-lead {
        background: linear-gradient(135deg, #0031c845, #00196900) !important;
        border: 0 solid black !important;
        border-left: 4px solid #03154d !important;
        border-radius: 14px !important;
        padding: 18px 22px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 4px 14px rgba(2, 25, 98, 0.12) !important;
        color: #020202 !important;
    }

    .pdp-enrich-lead p {
        margin-bottom: 0 !important;
        font-size: 1.02rem !important;
        line-height: 1.75 !important;
        color: #000000 !important;
        font-weight: 450 !important;
    }

    .pdp-enrich-lead strong,
    .pdp-enrich-lead b,
    .pdp-enrich-lead span {
        color: #ffffff !important;
    }

    .pdp-enrich-lead a {
        color: #93c5fd !important;
    }

    @media (max-width: 575px) {
        .pdp-commander-wrap {
            gap: 8px !important;
            margin-top: 8px !important;
        }

        .pdp-qty-wrap,
        .pdp-share-btn {
            flex: 0 0 106px !important;
            width: 106px !important;
            min-width: 106px !important;
            max-width: 106px !important;
            height: 44px !important;
        }

        .pdp-share-btn {
            font-size: 0.8rem !important;
            gap: 5px !important;
            padding: 0 6px !important;
        }

        .pdp-share-btn i {
            font-size: 0.76rem !important;
        }

        .pdp-commander-btn {
            height: 44px !important;
            font-size: 0.88rem !important;
            padding: 0 14px !important;
        }
    }

    /* ── Product Page Dual Slider Rows (Scaled Down Proportional Cards) ── */
    .pdp-slider-section {
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 14px !important;
        padding: 12px 12px 10px 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        margin-top: 14px;
        position: relative;
    }
    .pdp-slider-carousel-wrap {
        position: relative;
        width: 100%;
    }
    .btn-slider-nav {
        width: 32px;
        height: 32px;
        border-radius: 50% !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        background: #0f172a !important;
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15) !important;
        cursor: pointer;
        padding: 0;
    }
    .btn-slider-nav:hover:not(.swiper-button-disabled) {
        background: #c8102e !important;
        color: #ffffff !important;
        border-color: #c8102e !important;
        box-shadow: 0 6px 18px rgba(200, 16, 46, 0.35) !important;
        transform: translateY(-1px) scale(1.05);
    }
    .btn-slider-nav:active:not(.swiper-button-disabled) {
        transform: translateY(0) scale(1);
    }
    .btn-slider-nav.swiper-button-disabled {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        border-color: transparent !important;
        opacity: 0.35 !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }

    /* Desktop Left/Right Side Arrows for Sliders */
    @media (min-width: 992px) {
        .pdp-slider-side-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 38px;
            height: 38px;
            font-size: 0.85rem;
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12) !important;
            opacity: 0.95;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .pdp-slider-side-nav:hover:not(.swiper-button-disabled) {
            background: #c8102e !important;
            color: #ffffff !important;
            border-color: #c8102e !important;
            box-shadow: 0 6px 18px rgba(200, 16, 46, 0.35) !important;
            transform: translateY(-50%) scale(1.1) !important;
            opacity: 1;
        }
        .pdp-slider-side-nav.swiper-button-disabled {
            opacity: 0 !important;
            pointer-events: none !important;
        }
        .pdp-slider-side-prev {
            left: -16px;
        }
        .pdp-slider-side-next {
            right: -16px;
        }
        [dir="rtl"] .pdp-slider-side-prev {
            left: auto;
            right: -16px;
        }
        [dir="rtl"] .pdp-slider-side-next {
            right: auto;
            left: -16px;
        }
    }
    .pdp-products-swiper {
        overflow: hidden;
        width: 100%;
        padding-bottom: 4px;
    }
    .pdp-products-swiper .swiper-wrapper {
        align-items: stretch;
    }
    .pdp-products-swiper .swiper-slide {
        height: auto;
        display: flex;
        flex-direction: column;
    }
    .pdp-products-swiper .swiper-slide .featured-pro-card {
        height: 100% !important;
        width: 100% !important;
        margin-bottom: 0 !important;
    }

    /* ── Proportional Scale-Down of Product Cards inside PDP Sliders (~10% scaled down) ── */
    .pdp-slider-section .featured-pro-card.selection-card {
        padding: 7px !important;
    }
    .pdp-slider-section .featured-pro-img-box,
    .pdp-slider-section .selection-card .featured-pro-img-box {
        height: 135px !important;
        min-height: 135px !important;
        max-height: 135px !important;
        margin-bottom: 5px !important;
        padding: 3px !important;
    }
    .pdp-slider-section .featured-pro-title,
    .pdp-slider-section .featured-pro-title a {
        font-size: 0.74rem !important;
        line-height: 1.25 !important;
        height: 2.5em !important;
        min-height: 2.5em !important;
        max-height: 2.5em !important;
        margin-bottom: 5px !important;
    }
    .pdp-slider-section .pcard-price {
        height: 34px !important;
        min-height: 34px !important;
        max-height: 34px !important;
    }
    .pdp-slider-section .pcard-price-main-line {
        height: 20px !important;
        min-height: 20px !important;
        max-height: 20px !important;
        gap: 3px !important;
    }
    .pdp-slider-section .pcard-price-current {
        font-size: 0.95rem !important;
        height: 20px !important;
        min-height: 20px !important;
        max-height: 20px !important;
    }
    .pdp-slider-section .pcard-price-old {
        font-size: 0.68rem !important;
        height: 14px !important;
        min-height: 14px !important;
        max-height: 14px !important;
    }
    .pdp-slider-section .featured-pro-btn {
        height: 30px !important;
        min-height: 30px !important;
        max-height: 30px !important;
        font-size: 0.70rem !important;
        padding: 0 6px !important;
    }
    .pdp-slider-section .product-card-tag-badge {
        font-size: 0.62rem !important;
        padding: 2px 5px !important;
    }
    .pdp-slider-section .featured-stock-pill {
        font-size: 0.60rem !important;
        padding: 1px 5px !important;
    }
    .pdp-slider-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2px;
        margin-top: 12px;
        min-height: 20px;
    }
    .pdp-mobile-pagination-wrap {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 16px !important;
        margin-top: 14px !important;
        margin-bottom: 4px !important;
        width: 100% !important;
        position: relative !important;
        z-index: 5 !important;
    }
    @media (min-width: 992px) {
        .pdp-mobile-pagination-wrap {
            display: none !important;
        }
    }
    /* Swiper horizontal pagination defaults to width: 100%, which pushes flanked buttons off-screen. Reset to auto. */
    .pdp-mobile-pagination-wrap .pdp-slider-pagination,
    .pdp-slider-pagination.swiper-pagination-bullets,
    .pdp-slider-pagination.swiper-pagination-horizontal {
        width: auto !important;
        max-width: none !important;
        flex: 0 0 auto !important;
        position: static !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 24px !important;
        transform: none !important;
        left: auto !important;
        bottom: auto !important;
    }
    .pdp-slider-mobile-nav {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        min-height: 32px !important;
        font-size: 0.72rem !important;
        background: #0f172a !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 50% !important;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.18) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 32px !important;
        padding: 0 !important;
        cursor: pointer !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .pdp-slider-mobile-nav i {
        color: #ffffff !important;
        font-size: 0.72rem !important;
        line-height: 1 !important;
    }
    .pdp-slider-mobile-nav:active:not(.swiper-button-disabled) {
        background: #c8102e !important;
        color: #ffffff !important;
        transform: scale(0.92);
    }
    .pdp-slider-mobile-nav:hover:not(.swiper-button-disabled) {
        background: #c8102e !important;
        color: #ffffff !important;
        border-color: #c8102e !important;
    }
    .pdp-slider-mobile-nav.swiper-button-disabled {
        opacity: 0.35 !important;
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        border-color: transparent !important;
        box-shadow: none !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
    .pdp-slider-mobile-nav.swiper-button-disabled i {
        color: #94a3b8 !important;
    }
    .pdp-slider-pagination .cat-swiper-dot,
    .pdp-slider-pagination .swiper-pagination-bullet {
        width: 9px !important;
        height: 9px !important;
        background: #cbd5e1 !important;
        border-radius: 3px !important;
        display: inline-block;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: none;
        padding: 0;
        margin: 0 3px;
        opacity: 0.85;
    }
    .pdp-slider-pagination .cat-swiper-dot:hover,
    .pdp-slider-pagination .swiper-pagination-bullet:hover {
        background: #94a3b8 !important;
        opacity: 1;
        transform: scale(1.15);
    }
    .pdp-slider-pagination .cat-swiper-dot.cat-swiper-dot-active,
    .pdp-slider-pagination .swiper-pagination-bullet-active {
        width: 9px !important;
        height: 9px !important;
        background: #c8102e !important;
        border-radius: 3px !important;
        opacity: 1 !important;
        box-shadow: 0 2px 8px rgba(200, 16, 46, 0.35);
    }

    @media (max-width: 575px) {
        .pdp-slider-section {
            padding: 8px 6px 6px 6px !important;
            border-radius: 12px !important;
            margin-top: 10px;
        }
        .pdp-slider-section .featured-pro-card.selection-card {
            padding: 5px !important;
        }
        .pdp-slider-section .featured-pro-img-box,
        .pdp-slider-section .selection-card .featured-pro-img-box {
            height: 112px !important;
            min-height: 112px !important;
            max-height: 112px !important;
            margin-bottom: 3px !important;
            padding: 2px !important;
        }
        .pdp-slider-section .featured-pro-title,
        .pdp-slider-section .featured-pro-title a {
            font-size: 0.67rem !important;
            line-height: 1.2 !important;
            height: 2.4em !important;
            min-height: 2.4em !important;
            max-height: 2.4em !important;
            margin-bottom: 3px !important;
        }
        .pdp-slider-section .pcard-price {
            height: 30px !important;
            min-height: 30px !important;
            max-height: 30px !important;
        }
        .pdp-slider-section .pcard-price-main-line {
            height: 18px !important;
            min-height: 18px !important;
            max-height: 18px !important;
        }
        .pdp-slider-section .pcard-price-current {
            font-size: 0.85rem !important;
            height: 18px !important;
            min-height: 18px !important;
            max-height: 18px !important;
        }
        .pdp-slider-section .featured-pro-btn {
            height: 27px !important;
            min-height: 27px !important;
            max-height: 27px !important;
            font-size: 0.65rem !important;
            padding: 0 4px !important;
        }
        .btn-slider-nav {
            width: 28px;
            height: 28px;
            font-size: 0.65rem;
        }
        .pdp-slider-mobile-nav {
            width: 30px !important;
            height: 30px !important;
            min-width: 30px !important;
            min-height: 30px !important;
            font-size: 0.70rem !important;
            flex: 0 0 30px !important;
        }
    }

    /* ── Product Description Collapsible Section ── */
    .pdp-description-collapsible {
        position: relative;
        overflow: hidden;
        transition: max-height 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .pdp-description-collapsible.is-collapsed {
        max-height: 580px !important;
    }
    .pdp-description-collapsible.is-collapsed::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100px;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.95) 75%, #ffffff 100%);
        pointer-events: none;
        z-index: 2;
    }
    .pdp-description-collapsible.is-expanded {
        max-height: 6000px !important;
    }
    .pdp-description-collapsible.is-expanded::after {
        display: none !important;
    }
    .pdp-desc-toggle-wrap {
        border-top: 1px dashed #f1f5f9;
        padding-top: 16px;
        margin-top: 12px;
    }
    .pdp-desc-toggle-btn {
        font-size: 0.88rem !important;
        font-weight: 700 !important;
        padding: 10px 28px !important;
        border-radius: 50px !important;
        border: none !important;
        background: linear-gradient(45deg, #e50027, #92000e) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(229, 0, 39, 0.25) !important;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
    }
    .pdp-desc-toggle-btn:hover {
        background: linear-gradient(45deg, #c8102e, #7a000a) !important;
        color: #ffffff !important;
        border: none !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(200, 16, 46, 0.35) !important;
    }
    .pdp-desc-toggle-btn:active {
        transform: translateY(0) !important;
    }
    .pdp-desc-toggle-btn i {
        color: #ffffff !important;
        transition: transform 0.25s ease;
    }

    @media (max-width: 575px) {
        .pdp-description-collapsible.is-collapsed {
            max-height: 480px !important;
        }
    }
</style>
@endpush

@php
    $brandGuess = $product->brand?->name ?: 'WINA SHOP';
    $nameLower = strtolower($product->name);
    if ($brandGuess === 'WINA SHOP') {
        if (str_contains($nameLower, 'sony')) $brandGuess = 'Sony';
        elseif (str_contains($nameLower, 'canon')) $brandGuess = 'Canon';
        elseif (str_contains($nameLower, 'dji')) $brandGuess = 'DJI';
        elseif (str_contains($nameLower, 'godox')) $brandGuess = 'Godox';
        elseif (str_contains($nameLower, 'lexar')) $brandGuess = 'Lexar';
        elseif (str_contains($nameLower, 'nikon')) $brandGuess = 'Nikon';
        elseif (str_contains($nameLower, 'fujifilm') || str_contains($nameLower, 'fuji')) $brandGuess = 'Fujifilm';
        elseif (str_contains($nameLower, 'blackmagic')) $brandGuess = 'Blackmagic Design';
        elseif (str_contains($nameLower, 'sigma')) $brandGuess = 'Sigma';
        elseif (str_contains($nameLower, 'tamron')) $brandGuess = 'Tamron';
        elseif (str_contains($nameLower, 'rode') || str_contains($nameLower, 'røde')) $brandGuess = 'RØDE';
        elseif (str_contains($nameLower, 'hollyland')) $brandGuess = 'Hollyland';
        elseif (str_contains($nameLower, 'smallrig')) $brandGuess = 'SmallRig';
        elseif (str_contains($nameLower, 'zhiyun')) $brandGuess = 'Zhiyun';
        elseif (str_contains($nameLower, 'insta360')) $brandGuess = 'Insta360';
        elseif (str_contains($nameLower, 'sandisk')) $brandGuess = 'SanDisk';
        elseif (str_contains($nameLower, 'aputure')) $brandGuess = 'Aputure';
        elseif (str_contains($nameLower, 'nanlite')) $brandGuess = 'Nanlite';
        elseif (str_contains($nameLower, 'manfrotto')) $brandGuess = 'Manfrotto';
    }

    $isOccasion = str_contains($nameLower, 'occasion') || ($product->category && str_contains(strtolower($product->category->name), 'occasion'));
    $itemConditionUrl = $isOccasion ? 'https://schema.org/UsedCondition' : 'https://schema.org/NewCondition';
    
    $firstLineDesc = trim(explode("\n", (string) ($product->clean_description ?? ''))[0] ?? '');
    $cleanDesc = Str::limit($firstLineDesc, 140) ?: ($product->name . ' disponible au Maroc chez WINA SHOP Casablanca.');
    $cleanDesc .= ' Prix: ' . number_format($product->final_price, 2, '.', ' ') . ' MAD. Garantie officielle, devis proforma INDH, facture ICE, livraison 24h/48h partout au Maroc. شراء مع التوصيل السريع لجميع المدن المغربية.';

    $extractedSpecs = [];
    if (!empty($product->description) && preg_match_all('/<div class="pdp-spec-label"><i[^>]*><\/i>\s*([^<]+)<\/div>\s*<div class="pdp-spec-value">([^<]+)<\/div>/is', (string) $product->description, $mSpecs)) {
        foreach ($mSpecs[1] as $idx => $sName) {
            $extractedSpecs[] = [
                '@type' => 'PropertyValue',
                'name' => html_entity_decode(trim($sName), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'value' => html_entity_decode(trim($mSpecs[2][$idx]), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            ];
        }
    }
    
    // Tailored Multilingual SEO Keywords (French, Arabic, English & Darija) + Related & Suggested Products Keywords
    $baseKeywordsList = $product->getSeoKeywords();
    if (isset($relatedProducts) && $relatedProducts->isNotEmpty()) {
        foreach ($relatedProducts->take(6) as $relProd) {
            $baseKeywordsList[] = $relProd->name;
            $baseKeywordsList[] = $relProd->name . ' Maroc';
            $baseKeywordsList[] = 'شراء ' . $relProd->name . ' المغرب';
            $baseKeywordsList[] = 'Buy ' . $relProd->name . ' Morocco';
        }
    }
    if (isset($randomProducts) && $randomProducts->isNotEmpty()) {
        foreach ($randomProducts->take(4) as $sugProd) {
            $baseKeywordsList[] = $sugProd->name;
        }
    }
    $productKeywords = implode(', ', array_values(array_unique(array_filter($baseKeywordsList))));
    $multilingualNames = $product->getMultilingualAlternateNames();

    $similarProductsSchema = [];
    if (isset($relatedProducts) && $relatedProducts->isNotEmpty()) {
        foreach ($relatedProducts->take(10) as $rel) {
            $relUrl = route('shop.show', $rel->slug ?: $rel->id);
            $similarProductsSchema[] = [
                '@type' => 'Product',
                'name' => $rel->name,
                'url' => $relUrl,
                'image' => $rel->thumbnail,
                'sku' => $rel->sku ?: ('WINA-' . $rel->id),
                'keywords' => $rel->getCompactSeoKeywordsString(12),
                'alternateName' => $rel->getMultilingualAlternateNames(),
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $relUrl,
                    'priceCurrency' => setting('currency_code', 'MAD'),
                    'price' => number_format((float) $rel->final_price, 2, '.', ''),
                    'availability' => $rel->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ],
            ];
        }
    }

    $suggestedProductsSchema = [];
    if (isset($randomProducts) && $randomProducts->isNotEmpty()) {
        foreach ($randomProducts->take(10) as $rnd) {
            $rndUrl = route('shop.show', $rnd->slug ?: $rnd->id);
            $suggestedProductsSchema[] = [
                '@type' => 'Product',
                'name' => $rnd->name,
                'url' => $rndUrl,
                'image' => $rnd->thumbnail,
                'sku' => $rnd->sku ?: ('WINA-' . $rnd->id),
                'keywords' => $rnd->getCompactSeoKeywordsString(10),
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $rndUrl,
                    'priceCurrency' => setting('currency_code', 'MAD'),
                    'price' => number_format((float) $rnd->final_price, 2, '.', ''),
                    'availability' => $rnd->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ],
            ];
        }
    }

    $allImages = [$product->thumbnail];
    if ($product->relationLoaded('images') && $product->images) {
        foreach ($product->images as $img) {
            if (!empty($img->image_path)) {
                $imgPath = $img->image_path;
                if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
                    $allImages[] = $imgPath;
                } elseif (str_starts_with($imgPath, 'images/') || str_starts_with($imgPath, '/images/')) {
                    $allImages[] = asset(ltrim($imgPath, '/'));
                } elseif (str_starts_with($imgPath, 'storage/') || str_starts_with($imgPath, '/storage/')) {
                    $allImages[] = asset(ltrim($imgPath, '/'));
                } else {
                    $allImages[] = asset('storage/' . ltrim($imgPath, '/'));
                }
            }
        }
    }
    $allImages = array_values(array_unique(array_filter($allImages)));
    $productShareUrl = route('shop.show', $product->slug ?: $product->id);
    $productMetaImage = !empty($allImages[0]) ? $allImages[0] : $product->thumbnail;
@endphp

@section('meta_url', $productShareUrl)
@section('canonical_url', $productShareUrl)
@section('meta_title', $product->name . ' — Prix au Maroc, Achat & Garantie | ' . setting('app_name', 'WINA SHOP'))
@section('meta_description', $cleanDesc)
@section('meta_keywords', $productKeywords)
@section('meta_type', 'product')
@section('meta_image', $productMetaImage)

@section('extra_meta')
<meta property="product:price:amount" content="{{ $product->isOnSale() ? $product->sale_price : $product->price }}">
<meta property="product:price:currency" content="{{ setting('currency_code', 'MAD') }}">
<meta property="product:availability" content="{{ $product->isInStock() ? 'in stock' : 'out of stock' }}">
<meta property="product:condition" content="{{ $isOccasion ? 'used' : 'new' }}">
<meta property="product:brand" content="{{ $brandGuess }}">
<meta property="product:retailer_item_id" content="{{ $product->sku ?? ('PROD-' . $product->id) }}">
@if(!empty($productMetaImage))
<link rel="preload" as="image" href="{{ $productMetaImage }}" fetchpriority="high">
@endif
@endsection

@section('json_ld')
@php
    $avgRating = $avgRating ?? ($product->reviews()->avg('rating') ?? 0);
    $reviewCount = $reviewCount ?? ($product->reviews()->count() ?? 0);
@endphp
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Accueil",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Boutique",
        "item": "{{ route('shop.index') }}"
      }
      @if($product->category)
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($product->category->name) }}",
        "item": "{{ route('shop.index', ['category' => $product->category->slug]) }}"
      },
      {
        "@type": "ListItem",
        "position": 4,
        "name": "{{ addslashes($product->name) }}",
        "item": "{{ url()->current() }}"
      }
      @else
      ,{
        "@type": "ListItem",
        "position": 3,
        "name": "{{ addslashes($product->name) }}",
        "item": "{{ url()->current() }}"
      }
      @endif
    ]
  },
  {
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": {!! json_encode($product->name) !!},
    "inLanguage": ["fr-MA", "ar-MA", "en-MA"],
    "keywords": {!! json_encode($productKeywords) !!},
    "alternateName": {!! json_encode($multilingualNames) !!},
    "image": {!! json_encode($allImages) !!},
    "description": {!! json_encode($cleanDesc) !!},
    "sku": "{{ $product->sku ?? 'WINA-' . $product->id }}",
    "mpn": "{{ $product->sku ?? 'WINA-' . $product->id }}",
    "brand": {
      "@type": "Brand",
      "name": {!! json_encode($brandGuess) !!}
    },
    @if($product->category)
    "category": {!! json_encode($product->category->name) !!},
    @endif
    @if(!empty($extractedSpecs))
    "additionalProperty": {!! json_encode($extractedSpecs) !!},
    @endif
    "offers": {
      "@type": "Offer",
      "url": "{{ url()->current() }}",
      "priceCurrency": "{{ setting('currency_code', 'MAD') }}",
      "price": "{{ number_format($product->final_price, 2, '.', '') }}",
      "priceValidUntil": "{{ now()->addYear()->format('Y-12-31') }}",
      "itemCondition": "{{ $itemConditionUrl }}",
      "availability": "{{ $product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
      "seller": {
        "@type": "CameraStore",
        "@id": "{{ url('/') }}#organization",
        "name": "{{ setting('app_name', 'WINA SHOP') }}"
      },
      "shippingDetails": {
        "@type": "OfferShippingDetails",
        "shippingRate": {
          "@type": "MonetaryAmount",
          "value": "0",
          "currency": "{{ setting('currency_code', 'MAD') }}"
        },
        "shippingDestination": {
          "@type": "DefinedRegion",
          "addressCountry": "MA"
        },
        "deliveryTime": {
          "@type": "ShippingDeliveryTime",
          "handlingTime": {
            "@type": "QuantitativeValue",
            "minValue": 0,
            "maxValue": 1,
            "unitCode": "d"
          },
          "transitTime": {
            "@type": "QuantitativeValue",
            "minValue": 1,
            "maxValue": 2,
            "unitCode": "d"
          }
        }
      },
      "hasMerchantReturnPolicy": {
        "@type": "MerchantReturnPolicy",
        "applicableCountry": "MA",
        "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
        "merchantReturnDays": 7,
        "returnMethod": "https://schema.org/ReturnByMail",
        "returnFees": "https://schema.org/FreeReturn"
      }
    }
    ,
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "{{ $reviewCount > 0 ? number_format($avgRating, 1) : setting('seo_default_rating_value', '4.9') }}",
      "reviewCount": "{{ $reviewCount > 0 ? $reviewCount : max(5, (($product->id * 7) % 29) + 6) }}",
      "bestRating": "5",
      "worstRating": "1"
    }
    @if(!empty($similarProductsSchema))
    ,"isSimilarTo": {!! json_encode($similarProductsSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    @endif
    @if(!empty($suggestedProductsSchema))
    ,"isRelatedTo": {!! json_encode($suggestedProductsSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    @endif
  },
  @if(!empty($similarProductsSchema) || !empty($suggestedProductsSchema))
  @php
      $combinedRelatedList = array_merge($similarProductsSchema, $suggestedProductsSchema);
      $itemListElements = [];
      foreach ($combinedRelatedList as $idxPos => $relItem) {
          $itemListElements[] = [
              '@type' => 'ListItem',
              'position' => $idxPos + 1,
              'item' => $relItem,
          ];
      }
  @endphp
  {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": {!! json_encode('Produits similaires et suggestions pour ' . $product->name . ' — WINA SHOP Maroc', JSON_UNESCAPED_UNICODE) !!},
    "numberOfItems": {{ count($itemListElements) }},
    "itemListElement": {!! json_encode($itemListElements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
  },
  @endif
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": {!! json_encode('Où acheter ' . $product->name . ' au Maroc avec garantie officielle ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode(setting('app_name', 'WINA SHOP') . ' propose ' . $product->name . ' avec garantie constructeur, disponible dans notre showroom à Casablanca (Al Oulfa) et en livraison express 24h-48h partout au Maroc (Rabat, Marrakech, Tanger, Fès, Agadir, Oujda).') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('Quel est le prix et le délai de livraison de ' . $product->name . ' au Maroc ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Le prix de ' . $product->name . ' est de ' . number_format($product->final_price, 2, '.', ' ') . ' MAD chez WINA SHOP. La livraison est effectuée en 24h à 48h partout au Maroc avec suivi de colis et emballage sécurisé.') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('Proposez-vous un devis proforma INDH ou une facture avec ICE pour ' . $product->name . ' ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Oui, WINA SHOP délivre des devis proforma certifiés éligibles aux dossiers INDH ainsi que des factures officielles avec identifiant fiscal (ICE) pour sociétés, auto-entrepreneurs et vidéastes professionnels.') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('كيف يمكنني طلب ' . $product->name . ' في المغرب وما هي الضمانات؟') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('يمكنك شراء ' . $product->name . ' مباشرة عبر متجر WINA SHOP أو بالتواصل عبر الواتساب على الرقم ' . setting('company_phone', '+212 629-035777') . '. نوفر منتجات أصلية مع ضمان رسمي وتوصيل سريع لكافة المدن المغربية.') !!}
        }
      },
      {
        "@type": "Question",
        "name": {!! json_encode('Where to buy ' . $product->name . ' in Morocco and what is the price in MAD?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('You can buy ' . $product->name . ' at ' . setting('app_name', 'WINA SHOP') . ' in Casablanca, Morocco for ' . number_format($product->final_price, 2, '.', ' ') . ' MAD with official manufacturer warranty, 24h-48h nationwide express shipping, and B2B/INDH proforma invoicing.') !!}
        }
      }
      @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
      @php
          $relSummaryItems = $relatedProducts->take(6)->map(fn($r) => $r->name . ' (' . number_format((float)$r->final_price, 2, '.', ' ') . ' MAD)')->implode(', ');
      @endphp
      ,{
        "@type": "Question",
        "name": {!! json_encode('Quels sont les produits similaires et alternatives recommandées pour ' . $product->name . ' au Maroc ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Les meilleurs produits similaires et accessoires complémentaires pour ' . $product->name . ' disponibles en stock chez WINA SHOP Maroc sont : ' . $relSummaryItems . '. المنتوجات المشابهة والبديلة متوفرة مع الضمان والتوصيل بالمغرب.') !!}
        }
      }
      @endif
      @if(!empty($extractedSpecs))
      @php
          $specsAnswer = collect($extractedSpecs)->map(fn($s) => $s['name'] . ': ' . $s['value'])->implode(', ');
      @endphp
      ,{
        "@type": "Question",
        "name": {!! json_encode('Quelles sont les spécifications techniques officielles de ' . $product->name . ' ?') !!},
        "acceptedAnswer": {
          "@type": "Answer",
          "text": {!! json_encode('Spécifications officielles de ' . $product->name . ' disponibles chez WINA SHOP Maroc : ' . $specsAnswer . '.') !!}
        }
      }
      @endif
    ]
  }
]
</script>
@endsection

@section('content')

{{-- MAIN PRODUCT LAYOUT --}}
<section class="pdp-body">
    <div class="container">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
        </div>
        @endif

        <div class="pdp-card">
            <div class="row g-0">

                {{-- IMAGE PANEL --}}
                @php
                    $pdpTags = $product->relationLoaded('activeTags') ? $product->activeTags : $product->activeTags()->get();
                    $pdpTagsByPos = $pdpTags->groupBy(fn($t) => $t->position ?: 'top-left');
                    $pdpAutoDiscountTag = \App\Models\ProductTag::discountTag();
                    $pdpHasDiscount = $product->isOnSale() && $product->discount_percentage > 0 && $pdpAutoDiscountTag->status === 'active';
                    $pdpPrefDiscountPos = $pdpAutoDiscountTag->position ?: 'top-left';
                    $pdpDiscountCorner = $pdpTagsByPos->has($pdpPrefDiscountPos) ? $pdpPrefDiscountPos : ($pdpTagsByPos->keys()->first() ?? $pdpPrefDiscountPos);
                @endphp
                <div class="col-lg-6 pdp-image-panel">
                    @php
                        $galleryItems = collect();
                        if ($product->images && $product->images->count() > 0) {
                            $galleryItems = $product->images->take(6);
                        } else {
                            $galleryItems->push((object)[
                                'url' => $product->thumbnail,
                                'image_padding_style' => $product->primary_image_padding_style,
                            ]);
                        }
                    @endphp

                    <div class="pdp-main-image-wrap product-card-tag-container" id="zoomWrap">
                        @foreach(['top-left', 'top-right', 'bottom-left', 'bottom-right'] as $cornerPos)
                            @php
                                $pdpCornerTags = $pdpTagsByPos->get($cornerPos, collect());
                                $showPdpDiscountHere = $pdpHasDiscount && $pdpDiscountCorner === $cornerPos;
                            @endphp
                            @if($pdpCornerTags->isNotEmpty() || $showPdpDiscountHere)
                                <div class="product-card-tags-slot pdp-image-tags-slot pos-{{ $cornerPos }}" style="z-index: 15;">
                                    @foreach($pdpCornerTags as $pTag)
                                        <span class="product-card-tag-badge pdp-image-tag-badge {{ $pTag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $pTag->inline_style }}">
                                            @if(!empty($pTag->icon))
                                                <i class="{{ $pTag->icon }}" aria-hidden="true"></i>
                                            @endif
                                            <span>{{ $pTag->name }}</span>
                                        </span>
                                    @endforeach
                                    @if($showPdpDiscountHere)
                                        <span class="product-card-tag-badge pdp-image-tag-badge product-discount-auto-badge pdp-discount-badge {{ $pdpAutoDiscountTag->pulse_animation ? 'tag-pulse' : '' }}" style="{{ $pdpAutoDiscountTag->inline_style }}">
                                            @if(!empty($pdpAutoDiscountTag->icon))
                                                <i class="{{ $pdpAutoDiscountTag->icon }}" aria-hidden="true"></i>
                                            @endif
                                            <span>{{ $pdpAutoDiscountTag->formatDiscountLabel($product->discount_percentage) }}</span>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        @if($galleryItems->count() > 1)
                            <div class="swiper pdp-gallery-swiper" id="pdpGallerySwiper">
                                <div class="swiper-wrapper">
                                    @foreach($galleryItems as $idx => $gImg)
                                        <div class="swiper-slide" data-index="{{ $idx }}" data-url="{{ $gImg->url }}" data-padding="{{ $gImg->image_padding_style }}">
                                            <img src="{{ $gImg->url }}"
                                                 alt="{{ $product->name }} {{ $loop->iteration }}" 
                                                 width="600" height="600" 
                                                 class="pdp-main-image" 
                                                 {!! $loop->first ? 'fetchpriority="high" decoding="async" id="mainImage"' : 'loading="lazy" decoding="async"' !!}
                                                 onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';"
                                                 @if($gImg->image_padding_style) style="padding: {{ $gImg->image_padding_style }} !important;" @endif>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="pdp-gallery-nav pdp-gallery-prev d-none" aria-label="{{ __('Précédent') }}">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="pdp-gallery-nav pdp-gallery-next d-none" aria-label="{{ __('Suivant') }}">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <div class="pdp-gallery-pagination d-none"></div>
                            </div>
                        @else
                            <img id="mainImage" src="{{ $product->thumbnail }}"
                                 alt="{{ $product->name }}" width="600" height="600" class="pdp-main-image" fetchpriority="high" decoding="async" @if($product->primary_image_padding_style) style="padding: {{ $product->primary_image_padding_style }} !important;" @endif>
                        @endif
                    </div>

                    @if($galleryItems->count() > 1)
                    <div class="pdp-thumbs" id="pdpThumbs">
                        @foreach($galleryItems as $idx => $gImg)
                        <div class="pdp-thumb {{ $loop->first ? 'active' : '' }}" onclick="pdpSelectGallerySlide({{ $idx }}, this)" data-index="{{ $idx }}" data-padding="{{ $gImg->image_padding_style }}" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' ')pdpSelectGallerySlide({{ $idx }}, this)" aria-label="{{ $product->name }} {{ $loop->iteration }}">
                            <img src="{{ $gImg->url }}" alt="{{ $product->name }} {{ $loop->iteration }}" width="76" height="76" loading="lazy" decoding="async" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';" @if($gImg->image_padding_style) style="padding: {{ $gImg->image_padding_style }} !important;" @endif>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- INFO PANEL --}}
                <div class="col-lg-6 pdp-info-panel">
                    @php
                        $displayCats = $product->categories->isNotEmpty() ? $product->categories : collect(array_filter([$product->productCategory]));
                    @endphp
                    @if($displayCats->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            @foreach($displayCats as $pCat)
                                <a href="{{ route('shop.index', ['category' => $pCat->slug]) }}" class="pdp-cat-label d-inline-block text-decoration-none">{{ $pCat->name }}</a>
                            @endforeach
                        </div>
                    @elseif($product->category_name)
                        <a href="{{ route('shop.index', ['category' => $product->category_slug ?? optional($product->category)->slug ?? optional($product->productCategory)->slug]) }}" class="pdp-cat-label d-inline-block text-decoration-none mb-2">{{ $product->category_name }}</a>
                    @endif

                    <h1 class="pdp-title">{{ $product->name }}</h1>

                    @if(isset($reviews) && $reviews->total() > 0)
                    <div class="pdp-rating-row">
                        <div class="pdp-stars">
                            @php $avg = $product->reviews()->avg('rating') ?? 0; @endphp
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa{{ $i < round($avg) ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <span class="pdp-rating-count">{{ number_format($avg, 1) }} ({{ $reviews->total() }} {{ __('reviews') }})</span>
                    </div>
                    @endif

                    <div class="pdp-price-row">
                        <div class="pdp-price-group">
                            <div class="pdp-price-main-line d-flex align-items-center">
                                <span class="pdp-price-main">{{ $product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price }}</span>

                                @if($product->isComingSoon())
                                    <span class="pdp-stock-badge pdp-stock-badge--coming-soon" @if($product->custom_tag_style) style="{{ $product->custom_tag_style }}" @endif>
                                        <i class="fas fa-clock" aria-hidden="true" @if(!empty($product->custom_tag_color)) style="color: {{ $product->custom_tag_color }} !important;" @endif></i><span class="pdp-stock-label">{{ $product->stock_tag_label }}</span>
                                    </span>
                                @elseif($product->isInStock())
                                    <span class="pdp-stock-badge pdp-stock-badge--in">
                                        <i class="fas fa-check-circle" aria-hidden="true"></i><span class="pdp-stock-label">{{ __('In stock') }}</span>
                                    </span>
                                @else
                                    <span class="pdp-stock-badge pdp-stock-badge--out">
                                        <i class="fas fa-times-circle" aria-hidden="true"></i><span class="pdp-stock-label">{{ __('Out of stock') }}</span>
                                    </span>
                                @endif
                            </div>

                            @if($product->isOnSale())
                                <span class="pdp-price-old">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                    </div>

                    @if($product->has_variants && $product->variants->isNotEmpty())
                    {{-- VARIATIONS SELECTION PILLS (EX: SD CARD 512GB / 256GB / 128GB) --}}
                    <div class="pdp-variants-wrapper my-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-muted small" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                <i class="fas fa-layer-group text-primary me-1"></i>{{ __('Option / Variante') }}
                            </span>
                            <span class="badge bg-white text-secondary border fw-medium px-2 py-1" style="font-size: 0.68rem; border-radius: 6px;">
                                {{ $product->variants->count() }} {{ __('options') }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2" id="pdpVariantPills">                            
                            @foreach($product->variants as $variant)
                                @php
                                    $isVarInStock = $variant->isInStock();
                                @endphp
                                <button type="button" 
                                        class="pdp-variant-pill btn {{ !$isVarInStock ? 'is-out-of-stock' : '' }}"
                                        data-variant-id="{{ $variant->id }}"
                                        data-variant-name="{{ $variant->name }}"
                                        data-variant-price="{{ currency($variant->price) }}"
                                        data-variant-raw-price="{{ (float)$variant->price }}"
                                        data-variant-stock="{{ (int)$variant->stock }}"
                                        data-variant-in-stock="{{ $isVarInStock ? '1' : '0' }}"
                                        data-is-default="0"
                                        {{ !$isVarInStock ? 'disabled aria-disabled=true tabindex=-1' : '' }}
                                        onclick="{{ $isVarInStock ? 'pdpSelectVariant(this)' : 'return false;' }}"
                                        title="{{ !$isVarInStock ? __('Rupture de stock') : $variant->name }}">
                                    <span>{{ $variant->name }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div id="pdpVariantRequiredNotice" class="text-danger small mt-2 d-none fw-semibold">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ __('Veuillez sélectionner une option avant d\'ajouter au panier ou de commander.') }}
                        </div>
                        <input type="hidden" name="selected_variant_id" id="selectedVariantId" value="">
                    </div>
                    @endif

                    @if($product->isComingSoon())
                    {{-- COMING SOON / SPECIAL CUSTOM TAG MODE (GOLD BUTTON NON-CLICKABLE) --}}
                    @php
                        $currentLocale = app()->getLocale();
                        $shareLabel = $currentLocale === 'ar' ? 'مشاركة' : ($currentLocale === 'en' ? 'Share' : 'Partager');
                    @endphp
                    <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                        <button type="button" 
                                id="pdpShareBtnComingSoon" 
                                class="pdp-share-btn flex-shrink-0 shadow-none" 
                                onclick="copyPdpShareLink('{{ $productShareUrl }}', this)" 
                                title="{{ __('Copier le lien du produit') }}" 
                                aria-label="{{ __('Partager le produit') }}">
                            <i class="fas fa-link" id="pdpShareIcon"></i>
                            <span id="pdpShareText">{{ $shareLabel }}</span>
                        </button>
                        <button type="button" 
                                id="addToCartBtn" 
                                class="pdp-add-btn pdp-add-btn--gold flex-grow-1 text-nowrap shadow-none d-inline-flex align-items-center justify-content-center" 
                                disabled
                                style="cursor: default !important; pointer-events: none !important; {{ $product->custom_button_style }}"
                                title="{{ $product->stock_button_label }}">
                            <i class="fas fa-clock me-2" @if(!empty($product->custom_btn_color)) style="color: {{ $product->custom_btn_color }} !important;" @endif></i>
                            <span id="addToCartText">{{ $product->stock_button_label }}</span>
                        </button>
                    </div>
                    @elseif($product->isInStock())
                    @php
                        $isInCart = isset(session('cart', [])[$product->id]);
                        $locale = app()->getLocale();
                        if ($locale === 'ar') {
                            $inCartLabel = 'في السلة';
                            $notInCartLabel = 'أضف إلى السلة';
                        } elseif ($locale === 'en') {
                            $inCartLabel = 'In Cart';
                            $notInCartLabel = 'Add to Cart';
                        } else {
                            $inCartLabel = 'Dans le panier';
                            $notInCartLabel = 'Ajouter au panier';
                        }
                    @endphp
                    <form id="addToCartForm" onsubmit="pdpAddToCart(event)">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                            <div class="pdp-qty-wrap flex-shrink-0">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(-1)" aria-label="{{ __('Diminuer la quantité') }}">
                                    <i class="fas fa-minus" aria-hidden="true"></i>
                                </button>
                                <label for="pdpQty" class="visually-hidden">{{ __('Quantité') }}</label>
                                <input type="number" name="quantity" id="pdpQty" value="1"
                                       min="1" max="{{ $product->stock }}" class="pdp-qty-input" aria-label="{{ __('Quantité') }}">
                                <button type="button" class="pdp-qty-btn" onclick="pdpChangeQty(1)" aria-label="{{ __('Augmenter la quantité') }}">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                            <button type="submit" id="addToCartBtn" 
                                    class="pdp-add-btn {{ $isInCart ? 'is-in-cart' : '' }} flex-grow-1 text-nowrap shadow-none" 
                                    data-product-id="{{ $product->id }}" 
                                    title="{{ $isInCart ? $inCartLabel : $notInCartLabel }}">
                                @if($isInCart)
                                    <i class="fas fa-check me-2"></i>
                                @else
                                    {!! site_icon_html('icon_add_to_cart', 'fas fa-cart-plus', 'me-2', '', 16) !!}
                                @endif
                                <span id="addToCartText">{{ $isInCart ? $inCartLabel : $notInCartLabel }}</span>
                            </button>
                        </div>
                    </form>

                    {{-- Row 2: Partager Button (aligned with Qty) + Acheter maintenant (Buy Now, aligned with AddToCart) --}}
                    @php
                        $cartTotalCount = array_sum(array_column(session('cart', []), 'quantity'));
                        $currentLocale = app()->getLocale();
                        $commanderLabel = $currentLocale === 'ar' ? 'اشتري الآن' : ($currentLocale === 'en' ? 'Buy now' : 'Acheter maintenant');
                        $shareLabel = $currentLocale === 'ar' ? 'مشاركة' : ($currentLocale === 'en' ? 'Share' : 'Copié !');
                    @endphp
                    <div id="pdpCommanderWrap" class="pdp-commander-wrap mt-2">
                        <button type="button" 
                                id="pdpShareBtn" 
                                class="pdp-share-btn flex-shrink-0 shadow-none" 
                                onclick="copyPdpShareLink('{{ $productShareUrl }}', this)" 
                                title="{{ __('Copier le lien du produit') }}" 
                                aria-label="{{ __('Partager le produit') }}">
                            <i class="fas fa-link" id="pdpShareIcon"></i>
                            <span id="pdpShareText">{{ $shareLabel }}</span>
                        </button>
                        <button type="button" id="pdpCommanderBtn" class="pdp-commander-btn flex-grow-1 shadow-none" onclick="pdpCommanderAction(event)">
                            <i class="fas fa-shopping-bag me-2"></i>
                            <span id="pdpCommanderText">{{ $commanderLabel }}</span>
                            <i class="fas {{ $currentLocale === 'ar' ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2' }}"></i>
                        </button>
                    </div>
                    @else
                    <div class="pdp-cart-row d-flex flex-row flex-nowrap align-items-stretch w-100">
                        @php
                            $currentLocale = app()->getLocale();
                            $shareLabel = $currentLocale === 'ar' ? 'مشاركة' : ($currentLocale === 'en' ? 'Share' : 'Partager');
                        @endphp
                        <button type="button" 
                                id="pdpShareBtnOut" 
                                class="pdp-share-btn flex-shrink-0 shadow-none" 
                                onclick="copyPdpShareLink('{{ $productShareUrl }}', this)" 
                                title="{{ __('Copier le lien du produit') }}" 
                                aria-label="{{ __('Partager le produit') }}">
                            <i class="fas fa-link"></i>
                            <span>{{ $shareLabel }}</span>
                        </button>
                        <button type="button" id="addToCartBtn" class="pdp-add-btn pdp-add-btn--out flex-grow-1 text-nowrap shadow-none" disabled>
                            <i class="fas fa-ban me-2"></i>
                            <span id="addToCartText">{{ __('Out of stock') }}</span>
                        </button>
                    </div>
                    @endif

                    @php
                        $cleanDescText = $product->clean_description;
                        $shortSnippet = null;
                        if (!empty($cleanDescText)) {
                            $firstLine = trim(explode("\n", $cleanDescText)[0] ?? $cleanDescText);
                            $shortSnippet = Str::limit($firstLine, 210);
                        }
                    @endphp
                    @if($shortSnippet)
                    <div class="pdp-divider my-3"></div>
                    <div class="pdp-short-desc">
                        <p class="mb-1">{{ $shortSnippet }}</p>
                        @if(strlen($cleanDescText) > 210)
                            <a href="#pdp-full-description" class="pdp-read-more-link">
                                {{ __('Voir la fiche complète') }} <i class="fas fa-arrow-down ms-1"></i>
                            </a>
                        @endif
                    </div>
                    @endif

                    <div class="pdp-trust-row mt-3">
                        <div class="pdp-trust-pill">{!! site_icon_html('icon_pdp_trust_1', 'fas fa-truck-fast', '', '', 15) !!} {{ setting('pdp_trust_1_text', __('Secure Delivery 24/48h')) }}</div>
                        <div class="pdp-trust-pill">{!! site_icon_html('icon_pdp_trust_2', 'fas fa-video', '', '', 15) !!} {{ setting('pdp_trust_2_text', __('Showroom Demo Casablanca')) }}</div>
                    </div>

                    @if(!empty($product->sku))
                    <div class="pdp-sku-trust-box mt-3">
                        <button type="button" 
                                class="pdp-sku-badge" 
                                id="pdpSkuCopyBtn" 
                                onclick="copyPdpSku('{{ e($product->sku) }}')" 
                                title="{{ __('Cliquer pour copier le code SKU') }}"
                                aria-label="{{ __('Code SKU : ') }}{{ $product->sku }}">
                            <i class="fas fa-barcode pdp-sku-icon"></i>
                            <span class="pdp-sku-label">{{ __('Code SKU') }} :</span>
                            <span class="pdp-sku-value font-monospace" id="pdpSkuValue">{{ $product->sku }}</span>
                            <i class="far fa-copy pdp-sku-copy-icon ms-1" id="pdpSkuCopyIcon"></i>
                            <span class="pdp-sku-copied-tooltip" id="pdpSkuCopiedTooltip">{{ __('Copié !') }}</span>
                        </button>
                    </div>
                    @endif

                    {{-- Share Product Bar (WhatsApp, Native Share, Copy Link) --}}
                    @php
                        $shareEncodedTitle = rawurlencode($product->name);
                        $shareEncodedUrl = rawurlencode($productShareUrl);
                        $sharePriceText = rawurlencode(' — ' . number_format($product->final_price, 2, '.', ' ') . ' MAD');
                        $shareWhatsAppUrl = 'https://api.whatsapp.com/send?text=' . $shareEncodedTitle . $sharePriceText . '%0A' . $shareEncodedUrl;
                    @endphp
                    <div class="pdp-share-card mt-3 p-3 d-none">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-dark text-uppercase ls-1">
                                <i class="fas fa-share-nodes text-danger me-1"></i> {{ __('Partager ce produit') }}
                            </span>
                            <span class="text-muted" style="font-size: 11px;">
                                <i class="fas fa-image me-1 text-success"></i>{{ __('Image incluse') }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- WhatsApp Share Button --}}
                            <a href="{{ $shareWhatsAppUrl }}" target="_blank" rel="noopener noreferrer" 
                               class="btn btn-sm btn-pdp-share-wa d-inline-flex align-items-center gap-2 text-decoration-none"
                               title="{{ __('Partager avec image sur WhatsApp') }}">
                                <i class="fab fa-whatsapp"></i>
                                <span>WhatsApp</span>
                            </a>

                            {{-- Native Mobile Share Button --}}
                            <button type="button" 
                                    class="btn btn-sm btn-pdp-share-native d-inline-flex align-items-center gap-2"
                                    onclick="pdpNativeShare('{{ addslashes($product->name) }}', '{{ addslashes($cleanDesc) }}', '{{ $productShareUrl }}')"
                                    id="pdpNativeShareBtn"
                                    title="{{ __('Partager le lien') }}">
                                <i class="fas fa-share-from-square"></i>
                                <span>{{ __('Partager') }}</span>
                            </button>

                            {{-- Copy Direct Link Button --}}
                            <button type="button" 
                                    class="btn btn-sm btn-pdp-share-copy d-inline-flex align-items-center gap-2"
                                    onclick="copyPdpShareLink('{{ $productShareUrl }}', this)"
                                    title="{{ __('Copier le lien avec aperçu') }}">
                                <i class="far fa-copy"></i>
                                <span class="share-copy-label">{{ __('Copier le lien') }}</span>
                            </button>

                            {{-- Facebook Share Button --}}
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareEncodedUrl }}" 
                               target="_blank" rel="noopener noreferrer"
                               class="btn btn-sm btn-pdp-share-fb d-inline-flex align-items-center justify-content-center text-decoration-none"
                               title="{{ __('Partager sur Facebook') }}">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ROW 1: PRODUITS SIMILAIRES (10 ITEMS SLIDER) --}}
        @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
        <div class="pdp-slider-section" id="pdp-related-products-slider">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.1rem, 2vw, 1.3rem); letter-spacing: -0.3px;">
                        <i class="fas fa-layer-group text-danger me-2"></i>{{ __('Produits similaires') }}
                    </h2>
                </div>
                <div>
                    <a href="{{ route('shop.index', ['category' => $product->category?->slug]) }}" 
                       class="btn btn-outline-danger fw-bold px-3 py-1 text-nowrap d-inline-flex align-items-center gap-2" 
                       style="border-radius: 8px; font-size: 0.82rem; border-color: #c8102e;">
                        <span>{{ __('Voir plus') }}</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}" style="font-size: 10px;"></i>
                    </a>
                </div>
            </div>

            <div class="pdp-slider-carousel-wrap">
                {{-- Desktop Side Navigation Buttons --}}
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-prev pdp-swiper-prev-related d-none d-lg-flex" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-next pdp-swiper-next-related d-none d-lg-flex" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>

                <div class="swiper pdp-products-swiper pdp-related-swiper">
                    <div class="swiper-wrapper">
                        @foreach($relatedProducts as $relatedItem)
                            <div class="swiper-slide">
                                @include('frontend.partials.product_card_slider', ['product' => $relatedItem])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Dots & Navigation Arrows on Left and Right of Dots for Mobile -->
            <div class="pdp-mobile-pagination-wrap">
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav pdp-swiper-prev-related d-lg-none" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <div class="pdp-slider-pagination pdp-related-pagination"></div>
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav pdp-swiper-next-related d-lg-none" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>
            </div>
        </div>
        @endif

        {{-- ROW 2: PRODUITS ALÉATOIRES / RECOMMANDÉS (10 ITEMS SLIDER) --}}
        @if(isset($randomProducts) && $randomProducts->isNotEmpty())
        <div class="pdp-slider-section" id="pdp-random-products-slider">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="fw-bold text-dark mb-0" style="font-size: clamp(1.1rem, 2vw, 1.3rem); letter-spacing: -0.3px;">
                        <i class="fas fa-fire text-warning me-2"></i>{{ __('Suggestions pour vous') }}
                    </h2>
                </div>
                <div>
                    <a href="{{ route('shop.index') }}" 
                       class="btn btn-outline-danger fw-bold px-3 py-1 text-nowrap d-inline-flex align-items-center gap-2" 
                       style="border-radius: 8px; font-size: 0.82rem; border-color: #c8102e;">
                        <span>{{ __('Tout le catalogue') }}</span>
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}" style="font-size: 10px;"></i>
                    </a>
                </div>
            </div>

            <div class="pdp-slider-carousel-wrap">
                {{-- Desktop Side Navigation Buttons --}}
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-prev pdp-swiper-prev-random d-none d-lg-flex" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <button type="button" class="btn-slider-nav pdp-slider-side-nav pdp-slider-side-next pdp-swiper-next-random d-none d-lg-flex" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>

                <div class="swiper pdp-products-swiper pdp-random-swiper">
                    <div class="swiper-wrapper">
                        @foreach($randomProducts as $randomItem)
                            <div class="swiper-slide">
                                @include('frontend.partials.product_card_slider', ['product' => $randomItem])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Dots & Navigation Arrows on Left and Right of Dots for Mobile -->
            <div class="pdp-mobile-pagination-wrap">
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav pdp-swiper-prev-random d-lg-none" aria-label="{{ __('Précédent') }}" title="{{ __('Précédent') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-right' : 'fa-chevron-left' }}"></i>
                </button>
                <div class="pdp-slider-pagination pdp-random-pagination"></div>
                <button type="button" class="btn btn-slider-nav pdp-slider-mobile-nav pdp-swiper-next-random d-lg-none" aria-label="{{ __('Suivant') }}" title="{{ __('Suivant') }}">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}"></i>
                </button>
            </div>
        </div>
        @endif

        {{-- DEDICATED PRODUCT DESCRIPTION CONTAINER --}}
        @php
            $cleanDescription = $product->clean_description;
        @endphp
        @if(!empty($cleanDescription) || !empty(trim($product->description ?? '')))
        <div class="pdp-description-wrapper mt-4" id="pdp-full-description">
            <div class="pdp-section-card pdp-description-card">
                <div class="pdp-description-header d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2">
                    <h2 class="pdp-section-title mb-0 d-flex align-items-center">
                        <i class="fas fa-file-lines me-2 text-danger"></i>
                        <span>{{ __('Description détaillée') }}</span>
                    </h2>
                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small">
                        <i class="fas fa-check-double text-success me-1"></i>{{ __('Fiche technique officielle') }}
                    </span>
                </div>
                <div class="pdp-description-collapsible is-collapsed" id="pdpDescriptionCollapsible">
                    <div class="pdp-description pdp-description--clean entry-content" id="pdpDescriptionContent">
                        @if(strip_tags($product->description) !== $product->description)
                            {!! $product->lighthouse_safe_description_html !!}
                        @else
                            @foreach(explode("\n\n", $cleanDescription) as $para)
                                @if(trim($para))
                                    <p class="pdp-clean-p">{!! nl2br(e(trim($para))) !!}</p>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

                @php
                    $isAr = app()->getLocale() === 'ar';
                    $isEn = app()->getLocale() === 'en';
                    $showMoreText = $isAr ? 'عرض كل الوصف' : ($isEn ? 'Show full description' : 'Voir toute la description');
                    $showLessText = $isAr ? 'عرض أقل' : ($isEn ? 'Show less' : 'Afficher moins');
                @endphp
                <div class="text-center pdp-desc-toggle-wrap" id="pdpDescToggleWrap">
                    <button type="button" 
                            class="btn pdp-desc-toggle-btn shadow-none" 
                            id="pdpDescToggleBtn" 
                            onclick="togglePdpDescription()"
                            data-text-more="{{ $showMoreText }}"
                            data-text-less="{{ $showLessText }}"
                            aria-expanded="false"
                            aria-controls="pdpDescriptionCollapsible">
                        <span id="pdpDescToggleText">{{ $showMoreText }}</span>
                        <i class="fas fa-chevron-down" id="pdpDescToggleIcon" style="font-size: 0.75rem;"></i>
                    </button>
                </div>
            </div>
        </div>
        @endif

        {{-- REVIEWS --}}
        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="pdp-section-card">
                    <h2 class="pdp-section-title">
                        <i class="fas fa-star me-2 text-accent"></i>
                        {{ __('Customer reviews') }} <span class="pdp-section-count">({{ $reviews->total() }})</span>
                    </h2>

                    @forelse($reviews as $review)
                    <div class="pdp-review">
                        <div class="pdp-review-header">
                            <span class="pdp-review-title">{{ $review->title }}</span>
                            <div class="pdp-review-stars">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fa{{ $i < $review->rating ? 's' : 'r' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="pdp-review-body">{{ $review->comment }}</p>
                        <div class="pdp-review-meta">
                            {{ __('By') }} <strong>{{ $review->customer_name }}</strong> · {{ $review->created_at->format('d M Y') }}
                        </div>
                    </div>
                    @empty
                    <div class="pdp-review-empty">
                        <i class="far fa-comment-dots"></i>
                        <p>{{ __('No reviews yet. Be the first!') }}</p>
                    </div>
                    @endforelse

                    @if($reviews->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Write Review --}}
            <div class="col-lg-4">
                <div class="pdp-section-card">
                    <h2 class="pdp-section-title">
                        <i class="fas fa-pen me-2 text-accent"></i>{{ __('Write a review') }}
                    </h2>
                    <form action="{{ route('reviews.store') }}" method="POST" class="pdp-review-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        @guest
                        <div class="pdp-form-group">
                            <label for="reviewCustomerName" class="pdp-form-label">{{ __('Your name') }}</label>
                            <input type="text" id="reviewCustomerName" name="customer_name" class="pdp-form-input"
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: محمد الأمين' : 'ex. Jean Dupont' }}" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="pdp-form-group">
                            <label for="reviewCustomerEmail" class="pdp-form-label">{{ __('Your email') }}</label>
                            <input type="email" id="reviewCustomerEmail" name="customer_email" class="pdp-form-input"
                                   placeholder="email@exemple.com" value="{{ old('customer_email') }}" required>
                            @error('customer_email')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>
                        @endguest

                        <div class="pdp-form-group">
                            <label for="reviewRating" class="pdp-form-label">{{ __('Rating') }}</label>
                            <select id="reviewRating" name="rating" class="pdp-form-select" aria-label="{{ __('Rating') }}" required>
                                <option value="5">★★★★★ {{ __('Excellent (5/5)') }}</option>
                                <option value="4">★★★★☆ {{ __('Very good (4/5)') }}</option>
                                <option value="3">★★★☆☆ {{ __('Good (3/5)') }}</option>
                                <option value="2">★★☆☆☆ {{ __('Average (2/5)') }}</option>
                                <option value="1">★☆☆☆☆ {{ __('Bad (1/5)') }}</option>
                            </select>
                        </div>

                        <div class="pdp-form-group">
                            <label for="reviewTitle" class="pdp-form-label">{{ __('Title') }}</label>
                            <input type="text" id="reviewTitle" name="title" class="pdp-form-input"
                                   placeholder="{{ __('Summary of your experience') }}" value="{{ old('title') }}" required>
                            @error('title')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="pdp-form-group">
                            <label for="reviewComment" class="pdp-form-label">{{ __('Comment') }}</label>
                            <textarea id="reviewComment" name="comment" class="pdp-form-input" rows="4"
                                      placeholder="{{ __('How did you find this product?') }}" required>{{ old('comment') }}</textarea>
                            @error('comment')<span class="pdp-form-error">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="pdp-submit-btn w-100">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Publish review') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- QUESTIONS FRÉQUENTES & INFORMATIONS D'ACHAT MAROC (SEO, GOOGLE RICH RESULTS & AI SEARCH) --}}
        <div class="pdp-section-card mt-4 p-4 rounded-4 bg-white shadow-sm border">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <h2 class="h5 fw-bold m-0 d-flex align-items-center gap-2 text-dark">
                    <i class="fas fa-question-circle text-danger"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة حول الشراء والضمان بالمغرب' : __('Questions fréquentes & Achat au Maroc') }}</span>
                </h2>
                <span class="badge bg-light text-muted border px-2 py-1">
                    <i class="fas fa-shield-alt text-success me-1"></i>{{ __('Garantie & Authenticité') }}
                </span>
            </div>

            <div class="accordion accordion-flush" id="pdpFaqAccordion">
                {{-- FAQ 1: Where to buy & warranty --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                            {{ __('Où acheter') }} {{ $product->name }} {{ __('au Maroc avec garantie officielle ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ setting('app_name', 'WINA SHOP') }} propose <strong>{{ $product->name }}</strong> neuf et 100% authentique dans son showroom à <strong>Casablanca (Al Oulfa)</strong> ainsi qu'en vente en ligne. Nous assurons la livraison express et sécurisée dans toutes les villes marocaines : <em>Casablanca, Rabat, Marrakech, Tanger, Fès, Agadir, Oujda, Meknès, Kénitra</em>.
                        </div>
                    </div>
                </div>

                {{-- FAQ 2: Pricing, Delivery & Payment --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            {{ __('Quel est le prix et le délai de livraison au Maroc ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            Le prix de {{ $product->name }} est de <strong>{{ number_format($product->final_price, 2, '.', ' ') }} {{ setting('currency_code', 'MAD') }}</strong>. La livraison est effectuée sous <strong>24h à 48h ouvrables</strong> partout au Maroc avec emballage sécurisé, suivi rigoureux et garantie officielle.
                        </div>
                    </div>
                </div>

                {{-- FAQ 3: Devis INDH & Facture ICE --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            {{ __('Puis-je obtenir un devis proforma INDH ou une facture avec ICE ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            <strong>Oui, absolument.</strong> WINA SHOP délivre des devis proforma certifiés éligibles aux financements <strong>INDH (Initiative Nationale pour le Développement Humain)</strong> ainsi que des factures officielles avec identifiant fiscal (ICE, RC, IF) pour entreprises audiovisuelles, créateurs de contenu et administrations.
                        </div>
                    </div>
                </div>

                {{-- FAQ 4: Arabic summary --}}
                <div class="accordion-item border-bottom">
                    <h3 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            كيف يمكنني شراء {{ $product->name }} في المغرب وما هي الضمانات؟
                        </button>
                    </h3>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            يمكنك طلب <strong>{{ $product->name }}</strong> عبر متجر WINA SHOP أو بالتواصل مباشرة على الواتساب على الرقم <strong>{{ setting('company_phone', '+212 629-035777') }}</strong>. نوفر لك منتجات أصلية مع ضمان رسمي، وتوصيل سريع في ظرف 24 إلى 48 ساعة لكافة ربوع المملكة المغربية.
                        </div>
                    </div>
                </div>

                {{-- FAQ 5: English summary --}}
                <div class="accordion-item {{ (isset($relatedProducts) && $relatedProducts->isNotEmpty()) ? 'border-bottom' : '' }}">
                    <h3 class="accordion-header" id="faqHeading5">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                            Where to buy {{ $product->name }} in Morocco with official warranty?
                        </button>
                    </h3>
                    <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            You can purchase <strong>{{ $product->name }}</strong> directly from <strong>{{ setting('app_name', 'WINA SHOP') }}</strong> in Casablanca (Al Oulfa) or order online for <strong>{{ number_format($product->final_price, 2, '.', ' ') }} {{ setting('currency_code', 'MAD') }}</strong>. We provide authentic sealed equipment, official manufacturer warranty, certified B2B/INDH proforma invoices, and 24h-48h express delivery across all Moroccan cities.
                        </div>
                    </div>
                </div>

                @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
                {{-- FAQ 6: Related & Similar Products in FR / AR / EN --}}
                <div class="accordion-item">
                    <h3 class="accordion-header" id="faqHeading6">
                        <button class="accordion-button collapsed fw-bold py-3 text-dark bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                            {{ app()->getLocale() === 'ar' ? 'ما هي المنتجات المشابهة والبديلة لـ ' . $product->name . ' في المغرب؟' : (app()->getLocale() === 'en' ? 'What are the best similar products and alternatives to ' . $product->name . ' in Morocco?' : 'Quels sont les produits similaires et alternatives à ' . $product->name . ' au Maroc ?') }}
                        </button>
                    </h3>
                    <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#pdpFaqAccordion">
                        <div class="accordion-body text-muted pt-0 pb-3" style="font-size: 0.95rem; line-height: 1.6;">
                            <p class="mb-2">
                                Découvrez les meilleures alternatives et accessoires compatibles avec <strong>{{ $product->name }}</strong> disponibles en stock chez {{ setting('app_name', 'WINA SHOP') }} Maroc (المنتجات المشابهة والبديلة المتوفرة بالمغرب | Related products in stock in Morocco) :
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($relatedProducts->take(8) as $relFaqProd)
                                    <a href="{{ route('shop.show', $relFaqProd->slug ?: $relFaqProd->id) }}"
                                       class="badge bg-light text-dark border text-decoration-none px-2 py-1 fw-normal"
                                       title="{{ $relFaqProd->name }} — {{ number_format((float)$relFaqProd->final_price, 2, '.', ' ') }} MAD | شراء في المغرب | Buy in Morocco">
                                        {{ $relFaqProd->name }} ({{ number_format((float)$relFaqProd->final_price, 2, '.', ' ') }} MAD)
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Multilingual Search Tags & Semantic Indexation Block (FR / AR / EN) --}}
            <div class="mt-3 pt-3 border-top">
                <div class="small text-muted mb-2 fw-semibold">
                    <i class="fas fa-tags text-danger me-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'كلمات البحث الدلالية (FR / AR / EN) :' : (app()->getLocale() === 'en' ? 'Related Search Keywords (FR / AR / EN):' : 'Recherches associées & Mots-clés (FR / AR / EN) :') }}
                </div>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(array_slice($product->getSeoKeywords(), 0, 18) as $kwTag)
                        <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.74rem;">{{ $kwTag }}</span>
                    @endforeach
                </div>
            </div>
        </div>



    </div>
</section>

@endsection

@push('scripts')
<script>
let pdpGallerySwiperInstance = null;

function initPdpGallerySwiper() {
    const swiperEl = document.getElementById('pdpGallerySwiper');
    if (!swiperEl || typeof Swiper === 'undefined') return;

    pdpGallerySwiperInstance = new Swiper(swiperEl, {
        slidesPerView: 1,
        spaceBetween: 0,
        speed: 350,
        grabCursor: true,
        touchRatio: 1.15,
        resistanceRatio: 0.85,
        watchSlidesProgress: true,
        navigation: {
            prevEl: '.pdp-gallery-prev',
            nextEl: '.pdp-gallery-next',
        },
        pagination: {
            el: '.pdp-gallery-pagination',
            clickable: true,
            bulletClass: 'pdp-gallery-bullet',
            bulletActiveClass: 'pdp-gallery-bullet-active',
        },
        keyboard: {
            enabled: true,
            onlyInViewport: true,
        },
        on: {
            slideChange: function() {
                const activeIndex = this.activeIndex;
                const thumbs = document.querySelectorAll('.pdp-thumb');
                thumbs.forEach((t, idx) => {
                    if (idx === activeIndex) {
                        t.classList.add('active');
                        t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    } else {
                        t.classList.remove('active');
                    }
                });
            }
        }
    });
}

function pdpSelectGallerySlide(index, thumb) {
    if (pdpGallerySwiperInstance && !pdpGallerySwiperInstance.destroyed) {
        pdpGallerySwiperInstance.slideTo(index);
    } else {
        // Fallback if swiper is not available
        const mainImg = document.getElementById('mainImage');
        const imgInside = thumb ? thumb.querySelector('img') : null;
        if (mainImg && imgInside) {
            mainImg.src = imgInside.src;
        }
        document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
        if (thumb) thumb.classList.add('active');
    }
}

function pdpZoom(e) {
    // Zoom disabled on product image
}

function pdpChangeImage(src, thumb) {
    const mainImg = document.getElementById('mainImage');
    if (!mainImg) return;
    const padding = thumb ? thumb.getAttribute('data-padding') : '';
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        if (padding) {
            mainImg.style.setProperty('padding', padding, 'important');
        } else {
            mainImg.style.removeProperty('padding');
        }
        mainImg.style.opacity = '1';
    }, 120);
    document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
    if (thumb) thumb.classList.add('active');
}

let selectedVariantId = null;

function pdpPromptSelectVariant() {
    const wrapper = document.querySelector('.pdp-variants-wrapper');
    const notice = document.getElementById('pdpVariantRequiredNotice');
    const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    const isEn = document.documentElement.lang === 'en';

    if (wrapper) {
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
        wrapper.classList.remove('pdp-attention');
        void wrapper.offsetWidth;
        wrapper.classList.add('pdp-attention');
    }

    if (notice) {
        notice.classList.remove('d-none');
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: isAr ? 'يرجى اختيار خيار' : (isEn ? 'Please choose an option' : 'Veuillez choisir une option'),
            text: isAr ? 'يرجى تحديد أحد الخيارات المتاحة قبل المتابعة.' : (isEn ? 'Please select one of the available options before proceeding.' : 'Veuillez sélectionner l\'une des options disponibles avant d\'ajouter au panier ou de commander.'),
            confirmButtonColor: '#011962',
            confirmButtonText: isAr ? 'حسناً' : (isEn ? 'OK' : 'D\'accord')
        });
    }
}

function pdpSelectVariant(button) {
    if (!button || button.disabled || button.classList.contains('is-out-of-stock') || button.dataset.variantInStock === '0') return;

    // Clear validation feedback
    const wrapper = document.querySelector('.pdp-variants-wrapper');
    if (wrapper) wrapper.classList.remove('pdp-attention');
    const notice = document.getElementById('pdpVariantRequiredNotice');
    if (notice) notice.classList.add('d-none');

    document.querySelectorAll('.pdp-variant-pill').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
    });

    button.classList.add('active');

    selectedVariantId = button.dataset.variantId || null;

    const name = button.dataset.variantName;
    const price = button.dataset.variantPrice;
    const stock = parseInt(button.dataset.variantStock, 10) || 0;
    const inStock = button.dataset.variantInStock === '1';

    const hiddenInput = document.getElementById('selectedVariantId');
    if (hiddenInput) hiddenInput.value = selectedVariantId || '';

    const noticeEl = document.getElementById('selectedVariantStockNotice');
    if (noticeEl) {
        noticeEl.innerHTML = inStock 
            ? `<span class="text-success"><i class="fas fa-check-circle me-1"></i>${stock} en stock</span>` 
            : `<span class="text-danger"><i class="fas fa-times-circle me-1"></i>Rupture</span>`;
    }

    // Update main price display
    const priceMain = document.querySelector('.pdp-price-main');
    if (priceMain) priceMain.textContent = price;

    // Hide old price & discount badge for specific variant
    const oldPriceEl = document.querySelector('.pdp-price-old');
    const discountBadgeEl = document.querySelector('.pdp-discount-badge');
    if (oldPriceEl) oldPriceEl.style.display = 'none';
    if (discountBadgeEl) discountBadgeEl.style.display = 'none';

    // Update floating WhatsApp button to include selected variant and product link
    const waFloat = document.querySelector('.whatsapp-float');
    if (waFloat && waFloat.href) {
        try {
            const waUrl = new URL(waFloat.href);
            const appName = @json(setting('app_name', 'Wina Shop'));
            const prodName = @json($product->name);
            const prodUrl = @json($productShareUrl);
            const locale = '{{ app()->getLocale() }}';
            let msg = '';
            if (locale === 'ar') {
                msg = `مرحبا ${appName}، أود الاستفسار عن هذا المنتج:\n${prodName} (${name})\n${prodUrl}`;
            } else if (locale === 'en') {
                msg = `Hello ${appName}, I would like more information about this product:\n${prodName} (${name})\n${prodUrl}`;
            } else {
                msg = `Bonjour ${appName}, je souhaite me renseigner sur ce produit :\n${prodName} (${name})\n${prodUrl}`;
            }
            waUrl.searchParams.set('text', msg);
            waFloat.href = waUrl.toString();
        } catch (e) {
            console.error('Failed to update WhatsApp link', e);
        }
    }

    // Update stock badge and buttons
    const stockBadge = document.querySelector('.pdp-stock-badge');
    const qtyInput = document.getElementById('pdpQty');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const commanderBtn = document.getElementById('pdpCommanderBtn');
    const commanderWrap = document.getElementById('pdpCommanderWrap');
    const addToCartText = document.getElementById('addToCartText');

    if (inStock && stock > 0) {
        if (stockBadge) {
            stockBadge.className = 'pdp-stock-badge pdp-stock-badge--in';
            stockBadge.innerHTML = `<i class="fas fa-check-circle" aria-hidden="true"></i><span class="pdp-stock-label">{{ __('In stock') }}</span>`;
        }
        if (qtyInput) {
            qtyInput.max = stock;
            if (parseInt(qtyInput.value) > stock) qtyInput.value = stock;
        }
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('pdp-add-btn--out');
            if (addToCartText) addToCartText.textContent = '{{ $notInCartLabel ?? "Ajouter au panier" }}';
        }
        if (commanderWrap) commanderWrap.style.display = '';
    } else {
        if (stockBadge) {
            stockBadge.className = 'pdp-stock-badge pdp-stock-badge--out';
            stockBadge.innerHTML = `<i class="fas fa-times-circle" aria-hidden="true"></i><span class="pdp-stock-label">{{ __('Out of stock') }}</span>`;
        }
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('pdp-add-btn--out');
            if (addToCartText) addToCartText.textContent = '{{ __('Out of stock') }}';
        }
        if (commanderWrap) commanderWrap.style.display = 'none';
    }
}

function pdpChangeQty(delta) {
    const inp = document.getElementById('pdpQty');
    const max = parseInt(inp.max) || 9999;
    const val = Math.min(max, Math.max(1, parseInt(inp.value) + delta));
    inp.value = val;
}

function pdpAddToCart(event) {
    event.preventDefault();
    const hasVariants = {{ ($product->has_variants && $product->variants->isNotEmpty()) ? 'true' : 'false' }};
    if (hasVariants && !selectedVariantId) {
        pdpPromptSelectVariant();
        return;
    }

    const btn      = document.getElementById('addToCartBtn');
    const qtyInput = document.getElementById('pdpQty');
    const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
    const productId = {{ $product->id }};

    if (typeof window.addToCart === 'function') {
        window.addToCart(productId, quantity, btn, false, selectedVariantId);
    }
}

function pdpCommanderAction(event) {
    if (event) event.preventDefault();
    const hasVariants = {{ ($product->has_variants && $product->variants->isNotEmpty()) ? 'true' : 'false' }};
    if (hasVariants && !selectedVariantId) {
        pdpPromptSelectVariant();
        return;
    }

    const btn = document.getElementById('pdpCommanderBtn');
    if (!btn || btn.disabled) return;

    const productId = {{ $product->id }};
    const addBtn = document.getElementById('addToCartBtn');
    const isProductInStock = {{ ($product->isInStock() && !$product->isComingSoon()) ? 'true' : 'false' }};
    if (!isProductInStock) {
        return;
    }

    const isCurrentInCart = addBtn ? addBtn.classList.contains('is-in-cart') : false;

    const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
    const redirectingText = isAr ? 'جاري التحويل...' : 'Redirection...';

    // If product is ALREADY in cart with this specific variant -> go directly to checkout
    if (isCurrentInCart && (!hasVariants || selectedVariantId)) {
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>${redirectingText}</span>`;
        window.location.href = "{{ route('checkout.index') }}";
        return;
    }

    // Product is NOT in cart or is a specific variant -> add it with chosen quantity, then go to checkout!
    const qtyInput = document.getElementById('pdpQty');
    const quantity = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><span>${redirectingText}</span>`;

    const requestPayload = { quantity: quantity };
    if (selectedVariantId) {
        requestPayload.variant_id = selectedVariantId;
    }

    fetch(`{{ url('/cart/add') }}/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(requestPayload)
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error adding product to cart');
        }
        if (typeof window.syncAllProductCartButtons === 'function' && data.cartProductIds) {
            window.syncAllProductCartButtons(data.cartProductIds);
        }
        window.location.href = "{{ route('checkout.index') }}";
    })
    .catch(err => {
        btn.disabled = false;
        const defaultLabel = isAr ? 'اشتري الآن' : (document.documentElement.lang === 'en' ? 'Buy now' : 'Acheter maintenant');
        const arrowClass = isAr ? 'fa-arrow-left ms-2' : 'fa-arrow-right ms-2';
        btn.innerHTML = `<i class="fas fa-shopping-bag me-2"></i><span>${defaultLabel}</span><i class="fas ${arrowClass}"></i>`;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: isAr ? 'خطأ' : 'Erreur',
                text: err.message || (isAr ? 'تعذر إضافة المنتج إلى السلة' : 'Impossible d\'ajouter le produit au panier')
            });
        } else {
            alert(err.message || 'Impossible d\'ajouter le produit au panier');
        }
    });
}

function copyPdpSku(sku) {
    if (!sku) return;
    const badge = document.getElementById('pdpSkuCopyBtn');
    const icon = document.getElementById('pdpSkuCopyIcon');

    const onSuccess = () => {
        if (badge && icon) {
            badge.classList.add('copied');
            icon.className = 'fas fa-check pdp-sku-copy-icon text-success ms-1';
            setTimeout(() => {
                badge.classList.remove('copied');
                icon.className = 'far fa-copy pdp-sku-copy-icon ms-1';
            }, 1800);
        }
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(sku).then(onSuccess).catch(() => fallbackCopy(sku, onSuccess));
    } else {
        fallbackCopy(sku, onSuccess);
    }
}

function fallbackCopy(text, cb) {
    const temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.focus();
    temp.select();
    try {
        document.execCommand('copy');
        if (cb) cb();
    } catch (e) {}
    document.body.removeChild(temp);
}

function copyPdpShareLink(url, btn) {
    if (!url) return;
    const onSuccess = () => {
        if (!btn) return;
        const origHtml = btn.innerHTML;
        const isAr = document.documentElement.lang === 'ar';
        const isEn = document.documentElement.lang === 'en';
        const copiedText = isAr ? 'تم النسخ !' : (isEn ? 'Copied !' : 'Copié !');
        btn.innerHTML = `<i class="fas fa-check"></i> <span>${copiedText}</span>`;
        btn.classList.add('is-copied');
        setTimeout(() => {
            btn.innerHTML = origHtml;
            btn.classList.remove('is-copied');
        }, 2000);
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(onSuccess).catch(() => fallbackCopy(url, onSuccess));
    } else {
        fallbackCopy(url, onSuccess);
    }
}

function pdpNativeShare(title, text, url) {
    if (navigator.share) {
        navigator.share({
            title: title || document.title,
            text: text || '',
            url: url || window.location.href
        }).catch(err => {
            // User cancelled share
        });
    } else {
        const btn = document.getElementById('pdpNativeShareBtn');
        copyPdpShareLink(url, btn);
    }
}

// PDP Dual Sliders (Produits Similaires & Suggestions)
document.addEventListener('DOMContentLoaded', function() {
    function initPdpSliders() {
        if (typeof Swiper === 'undefined') {
            setTimeout(initPdpSliders, 100);
            return;
        }

        const baseSliderConfig = {
            watchSlidesProgress: true,
            grabCursor: true,
            speed: 400,
            touchRatio: 1.1,
            resistanceRatio: 0.85,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            slidesPerView: 2.45,
            slidesPerGroup: 2,
            spaceBetween: 8,
            breakpoints: {
                0: {
                    slidesPerView: 2.45,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                },
                480: {
                    slidesPerView: 2.65,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                },
                640: {
                    slidesPerView: 3.3,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                },
                768: {
                    slidesPerView: 3.6,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                },
                992: {
                    slidesPerView: 4.5,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                },
                1200: {
                    slidesPerView: 5.5,
                    slidesPerGroup: 2,
                    spaceBetween: 8,
                }
            }
        };

        function bindSliderControls(swiperInstance, prevSelector, nextSelector) {
            function updateState() {
                const isBeg = swiperInstance.isBeginning;
                const isEnd = swiperInstance.isEnd;
                document.querySelectorAll(prevSelector).forEach(btn => {
                    btn.classList.toggle('swiper-button-disabled', isBeg);
                    if (isBeg) {
                        btn.setAttribute('disabled', 'disabled');
                        btn.setAttribute('aria-disabled', 'true');
                    } else {
                        btn.removeAttribute('disabled');
                        btn.setAttribute('aria-disabled', 'false');
                    }
                });
                document.querySelectorAll(nextSelector).forEach(btn => {
                    btn.classList.toggle('swiper-button-disabled', isEnd);
                    if (isEnd) {
                        btn.setAttribute('disabled', 'disabled');
                        btn.setAttribute('aria-disabled', 'true');
                    } else {
                        btn.removeAttribute('disabled');
                        btn.setAttribute('aria-disabled', 'false');
                    }
                });
            }

            document.querySelectorAll(prevSelector).forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    swiperInstance.slidePrev();
                });
            });
            document.querySelectorAll(nextSelector).forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    swiperInstance.slideNext();
                });
            });

            swiperInstance.on('slideChange', updateState);
            swiperInstance.on('update', updateState);
            updateState();
        }

        if (document.querySelector('.pdp-related-swiper')) {
            const relatedSwiper = new Swiper('.pdp-related-swiper', Object.assign({}, baseSliderConfig, {
                pagination: {
                    el: '.pdp-related-pagination',
                    clickable: true,
                    bulletClass: 'cat-swiper-dot',
                    bulletActiveClass: 'cat-swiper-dot-active',
                }
            }));
            bindSliderControls(relatedSwiper, '.pdp-swiper-prev-related', '.pdp-swiper-next-related');
        }

        if (document.querySelector('.pdp-random-swiper')) {
            const randomSwiper = new Swiper('.pdp-random-swiper', Object.assign({}, baseSliderConfig, {
                pagination: {
                    el: '.pdp-random-pagination',
                    clickable: true,
                    bulletClass: 'cat-swiper-dot',
                    bulletActiveClass: 'cat-swiper-dot-active',
                }
            }));
            bindSliderControls(randomSwiper, '.pdp-swiper-prev-random', '.pdp-swiper-next-random');
        }
    }

    initPdpSliders();
    initPdpGallerySwiper();
});

// PDP Collapsible Description
function togglePdpDescription() {
    const wrap = document.getElementById('pdpDescriptionCollapsible');
    const btn = document.getElementById('pdpDescToggleBtn');
    const textEl = document.getElementById('pdpDescToggleText');
    const iconEl = document.getElementById('pdpDescToggleIcon');
    if (!wrap || !btn || !textEl || !iconEl) return;

    const isCollapsed = wrap.classList.contains('is-collapsed');
    const moreText = btn.dataset.textMore;
    const lessText = btn.dataset.textLess;

    if (isCollapsed) {
        wrap.classList.remove('is-collapsed');
        wrap.classList.add('is-expanded');
        btn.setAttribute('aria-expanded', 'true');
        textEl.textContent = lessText;
        iconEl.classList.remove('fa-chevron-down');
        iconEl.classList.add('fa-chevron-up');
    } else {
        wrap.classList.remove('is-expanded');
        wrap.classList.add('is-collapsed');
        btn.setAttribute('aria-expanded', 'false');
        textEl.textContent = moreText;
        iconEl.classList.remove('fa-chevron-up');
        iconEl.classList.add('fa-chevron-down');

        const card = document.getElementById('pdp-full-description');
        if (card) {
            const cardTop = card.getBoundingClientRect().top + window.pageYOffset - 90;
            window.scrollTo({ top: cardTop, behavior: 'smooth' });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const wrap = document.getElementById('pdpDescriptionCollapsible');
    const content = document.getElementById('pdpDescriptionContent');
    const toggleWrap = document.getElementById('pdpDescToggleWrap');

    if (wrap && content && toggleWrap) {
        // If content is already short, don't collapse and hide the toggle button
        if (content.scrollHeight <= 600) {
            wrap.classList.remove('is-collapsed');
            wrap.classList.add('is-expanded');
            toggleWrap.style.display = 'none';
        }
    }

    // When clicking "Voir la fiche complète" link from the top short desc snippet, auto-expand
    document.querySelectorAll('a[href="#pdp-full-description"]').forEach(function(link) {
        link.addEventListener('click', function() {
            if (wrap && wrap.classList.contains('is-collapsed')) {
                const btn = document.getElementById('pdpDescToggleBtn');
                const textEl = document.getElementById('pdpDescToggleText');
                const iconEl = document.getElementById('pdpDescToggleIcon');
                wrap.classList.remove('is-collapsed');
                wrap.classList.add('is-expanded');
                if (btn && textEl && iconEl) {
                    btn.setAttribute('aria-expanded', 'true');
                    textEl.textContent = btn.dataset.textLess;
                    iconEl.classList.remove('fa-chevron-down');
                    iconEl.classList.add('fa-chevron-up');
                }
            }
        });
    });
});
</script>
@endpush
