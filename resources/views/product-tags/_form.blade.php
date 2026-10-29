@php
    $isEdit = isset($productTag) && $productTag->exists;
    $name = old('name', $productTag->name ?? 'NEW');
    $icon = old('icon', $productTag->icon ?? 'fas fa-bolt');
    $styleType = old('style_type', $productTag->style_type ?? 'gradient');
    $bgColor = old('bg_color', $productTag->bg_color ?? '#10b981');
    $bgGradient = old('bg_gradient', $productTag->bg_gradient ?? '#059669');
    $textColor = old('text_color', $productTag->text_color ?? '#ffffff');
    $borderColor = old('border_color', $productTag->border_color ?? '');
    $shape = old('shape', $productTag->shape ?? 'rounded');
    $position = old('position', $productTag->position ?? 'top-left');
    $sizeScale = (int) old('size_scale', $productTag->size_scale ?? 100);
    $fontWeight = old('font_weight', $productTag->font_weight ?? '800');
    $uppercase = old('uppercase', $isEdit ? $productTag->uppercase : true);
    $pulseAnimation = old('pulse_animation', $isEdit ? $productTag->pulse_animation : false);
    $hasShadow = old('has_shadow', $isEdit ? $productTag->has_shadow : true);
    $status = old('status', $productTag->status ?? 'active');
    $sortOrder = old('sort_order', $productTag->sort_order ?? 0);
    $selectedIds = array_map('intval', (array) old('product_ids', $selectedProductIds ?? []));

    $sampleProduct = ($products ?? collect())->first();
    $sampleThumb = $sampleProduct ? $sampleProduct->thumbnail : asset('images/camera/cat_cameras.jpg');
    $sampleName = $sampleProduct ? $sampleProduct->name : 'Sony Alpha FX3 Cinema Line 4K';
    $samplePrice = $sampleProduct ? $sampleProduct->formatted_price : '42 900,00 MAD';

    $presetIcons = [
        '' => 'Aucune',
        'fas fa-bolt' => 'Éclair',
        'fas fa-fire' => 'Flamme',
        'fas fa-star' => 'Étoile',
        'fas fa-percent' => 'Promo %',
        'fas fa-crown' => 'VIP / Roi',
        'fas fa-tag' => 'Tag',
        'fas fa-award' => 'Médaille',
        'fas fa-gem' => 'Premium',
        'fas fa-thumbs-up' => 'Top Choix',
        'fas fa-clock' => 'Limité',
        'fas fa-check-circle' => 'Vérifié',
        'fas fa-heart' => 'Favori',
        'fas fa-gift' => 'Cadeau',
        'fas fa-rocket' => 'Boost',
        'fas fa-shield-halved' => 'Garanti',
    ];

    $quickSwatches = [
        ['#dc2626', '#991b1b', 'Rouge Wina'],
        ['#10b981', '#059669', 'Émeraude'],
        ['#f59e0b', '#ea580c', 'Ambre Feu'],
        ['#2563eb', '#1d4ed8', 'Bleu Pro'],
        ['#7c3aed', '#db2777', 'Violet Néon'],
        ['#0f172a', '#334155', 'Carbone'],
        ['#0d9488', '#0f766e', 'Turquoise'],
        ['#e11d48', '#be123c', 'Rubis'],
    ];
@endphp

<style>
    /* Studio Layout & Cards */
    .studio-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px !important;
        padding: 22px 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .studio-card:hover {
        border-color: #cbd5e1;
    }

    .studio-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 18px;
        border-bottom: 1px solid #f1f5f9;
    }

    .studio-step-badge {
        width: 30px;
        height: 30px;
        border-radius: 8px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.82rem;
        font-weight: 700 !important;
        flex-shrink: 0;
    }

    .studio-card-title {
        font-size: 1rem;
        font-weight: 700 !important;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .studio-card-subtitle {
        font-size: 0.78rem;
        color: #64748b;
        margin: 2px 0 0 0;
    }

    .studio-label {
        font-size: 0.8rem;
        font-weight: 700 !important;
        color: #1e293b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* 1. Preset Studio Cards Grid */
    .studio-presets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }

    .studio-preset-tile {
        border: 1.5px solid #e2e8f0;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 10px !important;
        padding: 12px 10px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: all 0.18s ease;
        position: relative;
        text-align: center;
    }

    .studio-preset-tile:hover {
        border-color: #dc2626;
        background: #fffafa;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(220, 38, 38, 0.08);
    }

    .studio-preset-tile.active {
        border-color: #dc2626;
        background: #fef2f2;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    }

    .studio-preset-meta {
        font-size: 0.68rem;
        color: #64748b;
        font-weight: 600 !important;
    }

    /* Quick Text Chips */
    .quick-text-chip {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 600 !important;
        padding: 3px 9px;
        border-radius: 6px !important;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .quick-text-chip:hover {
        border-color: #dc2626;
        color: #dc2626;
        background: #fef2f2;
    }

    /* 2. Icon Picker Grid */
    .studio-icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
        gap: 8px;
    }

    .studio-icon-tile {
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        border-radius: 8px !important;
        padding: 8px 6px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 0.71rem;
        color: #475569;
        font-weight: 600 !important;
        transition: all 0.15s ease;
    }

    .studio-icon-tile i {
        font-size: 1rem;
        color: #334155;
        transition: transform 0.15s ease, color 0.15s ease;
    }

    .studio-icon-tile:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .studio-icon-tile.active {
        border-color: #dc2626;
        background: #fef2f2;
        color: #dc2626;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.1);
    }

    .studio-icon-tile.active i {
        color: #dc2626;
        transform: scale(1.12);
    }

    /* 3. Visual Style & Shape Option Cards */
    .visual-choice-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    @media (max-width: 767.98px) {
        .visual-choice-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .visual-choice-card {
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        border-radius: 10px !important;
        padding: 10px 8px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        text-align: center;
        transition: all 0.15s ease;
    }

    .visual-choice-card:hover {
        border-color: #94a3b8;
        background: #f8fafc;
    }

    .visual-choice-card.active {
        border-color: #dc2626;
        background: #fef2f2;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.12);
    }

    .visual-choice-preview {
        width: 48px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.58rem;
        font-weight: 800 !important;
        letter-spacing: 0.04em;
    }

    .visual-choice-label {
        font-size: 0.72rem;
        font-weight: 700 !important;
        color: #1e293b;
        line-height: 1.1;
    }

    /* Color Swatches & Pickers */
    .color-swatch-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .color-palette-btn {
        width: 28px;
        height: 28px;
        border-radius: 50% !important;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 1px #cbd5e1, 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        padding: 0;
    }

    .color-palette-btn:hover {
        transform: scale(1.15);
        box-shadow: 0 0 0 2px #dc2626;
    }

    .color-input-box {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 8px !important;
        padding: 4px 8px;
        gap: 8px;
    }

    .color-input-box input[type="color"] {
        width: 28px;
        height: 28px;
        border: none;
        padding: 0;
        background: transparent;
        cursor: pointer;
        border-radius: 6px !important;
        flex-shrink: 0;
    }

    .color-input-box input[type="text"] {
        border: none;
        background: transparent;
        font-size: 0.8rem;
        font-weight: 700 !important;
        color: #0f172a;
        width: 100%;
        outline: none;
        font-family: monospace !important;
    }

    /* 4. Interactive 4-Corner Mini Product Card Map */
    .corner-map-box {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 12px !important;
        padding: 14px;
        position: relative;
        height: 158px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .corner-map-center-icon {
        text-align: center;
        color: #94a3b8;
        pointer-events: none;
    }

    .corner-hotspot {
        position: absolute;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        border-radius: 8px !important;
        padding: 6px 11px;
        font-size: 0.74rem;
        font-weight: 700 !important;
        color: #334155;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
    }

    .corner-hotspot:hover {
        border-color: #dc2626;
        color: #dc2626;
    }

    .corner-hotspot.active {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28);
    }

    .corner-hotspot.pos-tl { top: 10px; left: 10px; }
    .corner-hotspot.pos-tr { top: 10px; right: 10px; }
    .corner-hotspot.pos-bl { bottom: 10px; left: 10px; }
    .corner-hotspot.pos-br { bottom: 10px; right: 10px; }

    /* Scale Preset Pills & Metrics Bar */
    .scale-pill-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
        font-size: 0.74rem;
        font-weight: 700 !important;
        padding: 5px 11px;
        border-radius: 8px !important;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .scale-pill-btn.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .scale-metrics-strip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px !important;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 0.75rem;
    }

    /* Toggle Feature Cards */
    .toggle-feature-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px !important;
        background: #f8fafc;
        margin-bottom: 8px;
        cursor: pointer;
    }

    .toggle-feature-row:last-child {
        margin-bottom: 0;
    }

    /* 5. Product Selector List */
    .product-assign-list {
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 10px !important;
        background: #f8fafc;
        padding: 6px;
    }

    .product-assign-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 10px;
        border-radius: 8px !important;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.12s ease;
        cursor: pointer;
        margin-bottom: 3px;
    }

    .product-assign-item:hover {
        background: #ffffff;
        border-color: #e2e8f0;
    }

    .product-assign-item.is-selected {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .product-assign-thumb {
        width: 36px;
        height: 36px;
        border-radius: 6px !important;
        object-fit: contain;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 2px;
        flex-shrink: 0;
    }

    /* Right Column: Sticky Live Storefront Simulator */
    .simulator-sticky-wrap {
        position: sticky;
        top: 86px;
    }

    .simulator-viewport-tabs {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 5px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px !important;
        margin-bottom: 12px;
    }

    .sim-viewport-tab {
        border: none;
        background: transparent;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700 !important;
        padding: 6px 4px;
        border-radius: 7px !important;
        cursor: pointer;
        text-align: center;
        transition: all 0.15s ease;
    }

    .sim-viewport-tab.active {
        background: #ffffff;
        color: #dc2626;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .simulator-canvas {
        background:
            radial-gradient(circle at 20% 20%, rgba(220, 38, 38, 0.04), transparent 45%),
            radial-gradient(circle at 80% 80%, rgba(15, 23, 42, 0.05), transparent 50%),
            #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 12px !important;
        padding: 20px 12px;
        min-height: 390px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
        overflow: hidden;
    }

    /* Authentic Storefront Product Card inside Simulator */
    .sim-store-card {
        width: 245px;
        max-width: 100%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px !important;
        padding: 4.5%;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.07);
        container-type: inline-size;
        transition: width 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sim-store-img-box {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1;
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 8px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 8px;
        padding: 8%;
    }

    .sim-store-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .sim-store-card:hover .sim-store-img-box img {
        transform: scale(1.04);
    }

    .sim-corner-slot {
        position: absolute;
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: clamp(3px, 1.4cqw, 6px);
        pointer-events: none;
        max-width: 88%;
    }

    .sim-corner-slot.pos-top-left {
        top: clamp(5px, 3.2cqw, 10px);
        left: clamp(5px, 3.2cqw, 10px);
        align-items: flex-start;
    }

    .sim-corner-slot.pos-top-right {
        top: clamp(5px, 3.2cqw, 10px);
        right: clamp(5px, 3.2cqw, 10px);
        align-items: flex-end;
    }

    .sim-corner-slot.pos-bottom-left {
        bottom: clamp(5px, 3.2cqw, 10px);
        left: clamp(5px, 3.2cqw, 10px);
        align-items: flex-start;
    }

    .sim-corner-slot.pos-bottom-right {
        bottom: clamp(5px, 3.2cqw, 10px);
        right: clamp(5px, 3.2cqw, 10px);
        align-items: flex-end;
    }

    .sim-store-title {
        font-size: clamp(0.68rem, 5cqw, 0.86rem);
        font-weight: 600 !important;
        color: #0f172a;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.5em;
        margin-bottom: 6px;
    }

    .sim-store-price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 4px;
        margin-bottom: 8px;
    }

    .sim-store-price {
        font-size: clamp(0.72rem, 5.5cqw, 0.94rem);
        font-weight: 800 !important;
        color: #dc2626;
        white-space: nowrap;
    }

    .sim-store-stock {
        font-size: clamp(0.52rem, 3.5cqw, 0.66rem);
        font-weight: 700 !important;
        color: #059669;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        padding: 1px 6px;
        border-radius: 6px !important;
        white-space: nowrap;
    }

    .sim-store-cta {
        width: 100%;
        background: #dc2626;
        color: #ffffff;
        border: none;
        border-radius: 7px !important;
        padding: clamp(5px, 3cqw, 8px) 8px;
        font-size: clamp(0.62rem, 4.3cqw, 0.78rem);
        font-weight: 700 !important;
        text-align: center;
    }

    .sim-card-caption {
        font-size: 0.68rem;
        font-weight: 700 !important;
        color: #475569;
        text-align: center;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
</style>

<div class="row g-4">
    <!-- COLONNE GAUCHE : STUDIO DE CONFIGURATION -->
    <div class="col-12 col-xl-7">

        <!-- ÉTAPE 1 : MODÈLES PRÉDÉFINIS STUDIO -->
        <div class="studio-card">
            <div class="studio-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="studio-step-badge" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                        <i class="fas fa-wand-magic-sparkles"></i>
                    </span>
                    <div>
                        <h2 class="studio-card-title">Modèles Studio Prêts à l'Emploi</h2>
                        <p class="studio-card-subtitle">Cliquez sur un design populaire pour pré-configurer votre badge en un clic</p>
                    </div>
                </div>
                <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">8 modèles Pro</span>
            </div>

            <div class="studio-presets-grid product-card-tag-container" style="container-type: normal;">
                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'NEW', 'fas fa-bolt', 'gradient', '#10b981', '#059669', '#ffffff', '', 'rounded', 'top-left', 100, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: linear-gradient(135deg,#10b981,#059669); --tag-color:#fff; --tag-radius:6px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-bolt"></i><span>NEW</span>
                    </span>
                    <span class="studio-preset-meta">Dégradé Émeraude</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'TOP SELLER', 'fas fa-fire', 'gradient', '#f59e0b', '#ea580c', '#ffffff', '', 'pill', 'top-left', 100, true)">
                    <span class="product-card-tag-badge" style="--tag-bg: linear-gradient(135deg,#f59e0b,#ea580c); --tag-color:#fff; --tag-radius:999px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-fire"></i><span>TOP SELLER</span>
                    </span>
                    <span class="studio-preset-meta">Pilule Feu Pulsante</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'PROMO', 'fas fa-percent', 'solid', '#dc2626', '#991b1b', '#ffffff', '', 'rounded', 'top-right', 105, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: #dc2626; --tag-color:#fff; --tag-radius:6px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-percent"></i><span>PROMO</span>
                    </span>
                    <span class="studio-preset-meta">Rouge Impact</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'EXCLUSIF', 'fas fa-crown', 'solid', '#0f172a', '#334155', '#fbbf24', '#fbbf24', 'rounded', 'bottom-left', 95, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: #0f172a; --tag-color:#fbbf24; --tag-border:1px solid #fbbf24; --tag-radius:6px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-crown"></i><span>EXCLUSIF</span>
                    </span>
                    <span class="studio-preset-meta">Noir & Or VIP</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'FLASH DEAL', 'fas fa-bolt', 'gradient', '#7c3aed', '#db2777', '#ffffff', '', 'pill', 'top-left', 105, true)">
                    <span class="product-card-tag-badge" style="--tag-bg: linear-gradient(135deg,#7c3aed,#db2777); --tag-color:#fff; --tag-radius:999px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-bolt"></i><span>FLASH DEAL</span>
                    </span>
                    <span class="studio-preset-meta">Néon Cyber</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'ÉDITION PRO', 'fas fa-gem', 'glass', '#0f172a', '#1e293b', '#ffffff', '#ffffff', 'square', 'bottom-right', 95, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: rgba(15,23,42,0.85); --tag-color:#fff; --tag-border:1px solid rgba(255,255,255,0.4); --tag-radius:2px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-gem"></i><span>ÉDITION PRO</span>
                    </span>
                    <span class="studio-preset-meta">Verre Dépoli</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'COUP DE CŒUR', 'fas fa-heart', 'outline', '#e11d48', '#be123c', '#e11d48', '#e11d48', 'pill', 'top-right', 95, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: #fff; --tag-color:#e11d48; --tag-border:1.5px solid #e11d48; --tag-radius:999px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-heart"></i><span>COUP DE CŒUR</span>
                    </span>
                    <span class="studio-preset-meta">Contour Rubis</span>
                </div>

                <div class="studio-preset-tile" onclick="applyTagTemplate(this, 'GARANTI 2 ANS', 'fas fa-shield-halved', 'gradient', '#2563eb', '#1d4ed8', '#ffffff', '', 'leaf', 'bottom-left', 90, false)">
                    <span class="product-card-tag-badge" style="--tag-bg: linear-gradient(135deg,#2563eb,#1d4ed8); --tag-color:#fff; --tag-radius:10px 2px 10px 2px; font-size:0.72rem !important; padding:4px 10px !important;">
                        <i class="fas fa-shield-halved"></i><span>GARANTI 2 ANS</span>
                    </span>
                    <span class="studio-preset-meta">Feuille Confiance</span>
                </div>
            </div>
        </div>

        <!-- ÉTAPE 2 : IDENTITÉ DU BADGE (TEXTE & ICÔNE) -->
        <div class="studio-card">
            <div class="studio-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="studio-step-badge" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        1
                    </span>
                    <div>
                        <h2 class="studio-card-title">Identité du Badge : Texte & Icône</h2>
                        <p class="studio-card-subtitle">Définissez l'intitulé court et l'icône qui accompagneront votre badge</p>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-7">
                    <label class="studio-label" for="tagNameInput">
                        <span>Libellé du Badge <span class="text-danger">*</span></span>
                        <span class="text-muted fw-normal" style="font-size: 0.72rem;" id="charCountLabel">{{ strlen($name) }}/60</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="tagNameInput"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ $name }}"
                           maxlength="60"
                           placeholder="Ex: NEW, TOP SELLER, PROMO -20%..."
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="d-flex align-items-center gap-1.5 flex-wrap mt-2">
                        <span class="text-muted" style="font-size: 0.7rem;">Suggestions :</span>
                        @foreach(['NEW', 'NOUVEAU', 'TOP SELLER', 'PROMO', 'BEST SELLER', 'EXCLUSIF', 'STOCK LIMITÉ'] as $sug)
                            <button type="button" class="quick-text-chip" onclick="setTagText('{{ $sug }}')">{{ $sug }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <label class="studio-label" for="tagIconInput">
                        <span>Classe Icône FontAwesome</span>
                        <span class="text-muted fw-normal" style="font-size: 0.7rem;">Optionnel</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i id="currentIconPreview" class="{{ $icon ?: 'fas fa-ban text-muted' }}"></i>
                        </span>
                        <input type="text"
                               name="icon"
                               id="tagIconInput"
                               class="form-control border-start-0 font-monospace"
                               style="font-size: 0.82rem;"
                               value="{{ $icon }}"
                               placeholder="fas fa-bolt">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectIconTile('')" title="Retirer l'icône">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <label class="studio-label mb-2">
                        <span>Sélecteur visuel d'icône</span>
                    </label>
                    <div class="studio-icon-grid" id="presetIconButtons">
                        @foreach($presetIcons as $iconClass => $iconLabel)
                            <div class="studio-icon-tile {{ $icon === $iconClass ? 'active' : '' }}" data-icon="{{ $iconClass }}" onclick="selectIconTile('{{ $iconClass }}')">
                                @if($iconClass)
                                    <i class="{{ $iconClass }}"></i>
                                @else
                                    <i class="fas fa-ban text-muted"></i>
                                @endif
                                <span>{{ $iconLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- ÉTAPE 3 : COULEURS, STYLE & FORME -->
        <div class="studio-card">
            <div class="studio-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="studio-step-badge" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        2
                    </span>
                    <div>
                        <h2 class="studio-card-title">Style Visuel, Couleurs & Géométrie</h2>
                        <p class="studio-card-subtitle">Personnalisez le rendu chromatique et la forme des angles</p>
                    </div>
                </div>
            </div>

            <!-- Sélecteur Visuel du Type de Style -->
            <div class="mb-3">
                <label class="studio-label">Mode de Rendu (Style)</label>
                <input type="hidden" name="style_type" id="tagStyleType" value="{{ $styleType }}">
                <div class="visual-choice-grid" id="styleChoiceGrid">
                    <div class="visual-choice-card {{ $styleType === 'solid' ? 'active' : '' }}" data-style="solid" onclick="selectStyleType('solid')">
                        <div class="visual-choice-preview" style="background:#dc2626; color:#fff; border-radius:5px;">TAG</div>
                        <span class="visual-choice-label">Solide</span>
                    </div>
                    <div class="visual-choice-card {{ $styleType === 'gradient' ? 'active' : '' }}" data-style="gradient" onclick="selectStyleType('gradient')">
                        <div class="visual-choice-preview" style="background:linear-gradient(135deg,#dc2626,#7c3aed); color:#fff; border-radius:5px;">TAG</div>
                        <span class="visual-choice-label">Dégradé</span>
                    </div>
                    <div class="visual-choice-card {{ $styleType === 'outline' ? 'active' : '' }}" data-style="outline" onclick="selectStyleType('outline')">
                        <div class="visual-choice-preview" style="background:#fff; color:#dc2626; border:1.5px solid #dc2626; border-radius:5px;">TAG</div>
                        <span class="visual-choice-label">Contour</span>
                    </div>
                    <div class="visual-choice-card {{ $styleType === 'soft' ? 'active' : '' }}" data-style="soft" onclick="selectStyleType('soft')">
                        <div class="visual-choice-preview" style="background:rgba(220,38,38,0.14); color:#dc2626; border:1px solid rgba(220,38,38,0.3); border-radius:5px;">TAG</div>
                        <span class="visual-choice-label">Pastel Soft</span>
                    </div>
                    <div class="visual-choice-card {{ $styleType === 'glass' ? 'active' : '' }}" data-style="glass" onclick="selectStyleType('glass')">
                        <div class="visual-choice-preview" style="background:rgba(15,23,42,0.82); color:#fff; border:1px solid rgba(255,255,255,0.4); border-radius:5px;">TAG</div>
                        <span class="visual-choice-label">Verre Dépoli</span>
                    </div>
                </div>
            </div>

            <!-- Sélecteur Visuel de la Forme -->
            <div class="mb-4">
                <label class="studio-label">Géométrie des Angles (Forme)</label>
                <input type="hidden" name="shape" id="tagShape" value="{{ $shape }}">
                <div class="visual-choice-grid" id="shapeChoiceGrid">
                    <div class="visual-choice-card {{ $shape === 'rounded' ? 'active' : '' }}" data-shape="rounded" onclick="selectShapeType('rounded')">
                        <div class="visual-choice-preview" style="background:#1e293b; color:#fff; border-radius:6px;">6px</div>
                        <span class="visual-choice-label">Arrondi</span>
                    </div>
                    <div class="visual-choice-card {{ $shape === 'pill' ? 'active' : '' }}" data-shape="pill" onclick="selectShapeType('pill')">
                        <div class="visual-choice-preview" style="background:#1e293b; color:#fff; border-radius:999px;">Pilule</div>
                        <span class="visual-choice-label">Pilule</span>
                    </div>
                    <div class="visual-choice-card {{ $shape === 'square' ? 'active' : '' }}" data-shape="square" onclick="selectShapeType('square')">
                        <div class="visual-choice-preview" style="background:#1e293b; color:#fff; border-radius:2px;">Carré</div>
                        <span class="visual-choice-label">Carré Net</span>
                    </div>
                    <div class="visual-choice-card {{ $shape === 'leaf' ? 'active' : '' }}" data-shape="leaf" onclick="selectShapeType('leaf')">
                        <div class="visual-choice-preview" style="background:#1e293b; color:#fff; border-radius:10px 2px 10px 2px;">Feuille</div>
                        <span class="visual-choice-label">Feuille</span>
                    </div>
                    <div class="visual-choice-card {{ $shape === 'ribbon' ? 'active' : '' }}" data-shape="ribbon" onclick="selectShapeType('ribbon')">
                        <div class="visual-choice-preview" style="background:#1e293b; color:#fff; border-radius:4px 4px 4px 0px;">Ruban</div>
                        <span class="visual-choice-label">Ruban</span>
                    </div>
                </div>
            </div>

            <!-- Palettes Rapides -->
            <div class="mb-3">
                <label class="studio-label">
                    <span>Palettes Chromatiques Rapides</span>
                    <span class="text-muted fw-normal" style="font-size: 0.7rem;">1 clic applique Fond 1 + Dégradé 2</span>
                </label>
                <div class="color-swatch-bar">
                    @foreach($quickSwatches as [$c1, $c2, $cLabel])
                        <button type="button"
                                class="color-palette-btn"
                                title="{{ $cLabel }}"
                                style="background: linear-gradient(135deg, {{ $c1 }} 0%, {{ $c2 }} 100%);"
                                onclick="applyColorPair('{{ $c1 }}', '{{ $c2 }}')"></button>
                    @endforeach
                </div>
            </div>

            <!-- Sélecteurs de Couleurs Précis -->
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="studio-label">Couleur Principale</label>
                    <div class="color-input-box">
                        <input type="color" id="bgColorPicker" value="{{ $bgColor }}">
                        <input type="text" name="bg_color" id="bgColorInput" value="{{ $bgColor }}">
                    </div>
                </div>

                <div class="col-6 col-md-3" id="gradientColorGroup">
                    <label class="studio-label">Couleur Dégradé 2</label>
                    <div class="color-input-box">
                        <input type="color" id="bgGradientPicker" value="{{ $bgGradient ?: '#059669' }}">
                        <input type="text" name="bg_gradient" id="bgGradientInput" value="{{ $bgGradient ?: '#059669' }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="studio-label">Couleur Texte</label>
                    <div class="color-input-box">
                        <input type="color" id="textColorPicker" value="{{ $textColor }}">
                        <input type="text" name="text_color" id="textColorInput" value="{{ $textColor }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="studio-label">
                        <span>Bordure</span>
                        <a href="javascript:void(0)" onclick="clearBorderColor()" class="text-muted text-decoration-none" style="font-size: 0.68rem;">Aucune</a>
                    </label>
                    <div class="color-input-box">
                        <input type="color" id="borderColorPicker" value="{{ $borderColor ?: '#ffffff' }}">
                        <input type="text" name="border_color" id="borderColorInput" value="{{ $borderColor }}" placeholder="Aucune">
                    </div>
                </div>
            </div>
        </div>

        <!-- ÉTAPE 4 : ÉCHELLE PROPORTIONNELLE (CQW) & POSITION SUR LA CARTE -->
        <div class="studio-card">
            <div class="studio-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="studio-step-badge" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        3
                    </span>
                    <div>
                        <h2 class="studio-card-title">Taille Proportionnelle & Position sur la Carte</h2>
                        <p class="studio-card-subtitle">Le badge se redimensionne automatiquement selon la largeur de la carte produit (`cqw`)</p>
                    </div>
                </div>
                <span class="badge bg-danger px-2.5 py-1.5" id="sizeScaleValueBadge" style="font-size: 0.82rem; font-weight: 700 !important;">{{ $sizeScale }}%</span>
            </div>

            <!-- Contrôleur d'Échelle Proportionnelle -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <label class="studio-label mb-0">Coefficient d'échelle relative (50% à 180%)</label>
                    <div class="d-flex gap-1.5 flex-wrap">
                        @foreach([75 => 'XS (75%)', 90 => 'S (90%)', 100 => 'Standard (100%)', 115 => 'L (115%)', 135 => 'XL (135%)'] as $scaleVal => $scaleLabel)
                            <button type="button" class="scale-pill-btn {{ $sizeScale === $scaleVal ? 'active' : '' }}" data-scale="{{ $scaleVal }}" onclick="setScaleValue({{ $scaleVal }})">
                                {{ $scaleLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <input type="range" name="size_scale" id="tagSizeScale" class="form-range" min="50" max="180" step="5" value="{{ $sizeScale }}">

                <div class="scale-metrics-strip mt-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-mobile-screen text-primary"></i>
                        <span class="text-muted">Sur Carte Mobile (155px) :</span>
                        <strong class="text-dark" id="metricMobileSize">~9.5px</strong>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-desktop text-danger"></i>
                        <span class="text-muted">Sur Carte Desktop (260px) :</span>
                        <strong class="text-dark" id="metricDesktopSize">~12.2px</strong>
                    </div>
                </div>
            </div>

            <!-- Carte Interactive des 4 Coins + Typographie -->
            <div class="row g-3">
                <div class="col-12 col-md-7">
                    <label class="studio-label">Emplacement sur l'image du produit (Cliquez sur un coin)</label>
                    <input type="hidden" name="position" id="tagPositionInput" value="{{ $position }}">
                    <div class="corner-map-box">
                        <div class="corner-hotspot pos-tl {{ $position === 'top-left' ? 'active' : '' }}" data-pos="top-left" onclick="selectCornerPosition('top-left')">
                            <i class="fas fa-arrows-to-dot"></i> Haut Gauche
                        </div>
                        <div class="corner-hotspot pos-tr {{ $position === 'top-right' ? 'active' : '' }}" data-pos="top-right" onclick="selectCornerPosition('top-right')">
                            Haut Droite <i class="fas fa-arrows-to-dot"></i>
                        </div>
                        <div class="corner-map-center-icon">
                            <i class="fas fa-camera fa-2x mb-1 opacity-50"></i>
                            <div style="font-size: 0.68rem; font-weight: 600;">ZONE IMAGE CARTE</div>
                        </div>
                        <div class="corner-hotspot pos-bl {{ $position === 'bottom-left' ? 'active' : '' }}" data-pos="bottom-left" onclick="selectCornerPosition('bottom-left')">
                            <i class="fas fa-arrows-to-dot"></i> Bas Gauche
                        </div>
                        <div class="corner-hotspot pos-br {{ $position === 'bottom-right' ? 'active' : '' }}" data-pos="bottom-right" onclick="selectCornerPosition('bottom-right')">
                            Bas Droite <i class="fas fa-arrows-to-dot"></i>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <label class="studio-label">Typographie & Effets Visuels</label>
                    <select name="font_weight" id="tagFontWeight" class="form-select form-select-sm mb-2">
                        <option value="600" {{ $fontWeight == '600' ? 'selected' : '' }}>Épaisseur : Semi-Gras (600)</option>
                        <option value="700" {{ $fontWeight == '700' ? 'selected' : '' }}>Épaisseur : Gras (700)</option>
                        <option value="800" {{ $fontWeight == '800' ? 'selected' : '' }}>Épaisseur : Extra-Gras (800)</option>
                        <option value="900" {{ $fontWeight == '900' ? 'selected' : '' }}>Épaisseur : Impact (900)</option>
                    </select>

                    <label class="toggle-feature-row" for="tagUppercase">
                        <span class="small fw-semibold text-dark"><i class="fas fa-font text-muted me-1.5"></i>Texte en MAJUSCULES</span>
                        <input class="form-check-input m-0" type="checkbox" name="uppercase" id="tagUppercase" value="1" {{ $uppercase ? 'checked' : '' }}>
                    </label>

                    <label class="toggle-feature-row" for="tagHasShadow">
                        <span class="small fw-semibold text-dark"><i class="fas fa-clone text-muted me-1.5"></i>Ombre portée 3D</span>
                        <input class="form-check-input m-0" type="checkbox" name="has_shadow" id="tagHasShadow" value="1" {{ $hasShadow ? 'checked' : '' }}>
                    </label>

                    <label class="toggle-feature-row" for="tagPulseAnimation">
                        <span class="small fw-semibold text-dark"><i class="fas fa-wave-square text-danger me-1.5"></i>Pulsation animée</span>
                        <input class="form-check-input m-0" type="checkbox" name="pulse_animation" id="tagPulseAnimation" value="1" {{ $pulseAnimation ? 'checked' : '' }}>
                    </label>
                </div>
            </div>
        </div>

        <!-- ÉTAPE 5 : ATTRIBUTION AUX PRODUITS & PARAMÈTRES -->
        <div class="studio-card">
            <div class="studio-card-header">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="studio-step-badge" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                        4
                    </span>
                    <div>
                        <h2 class="studio-card-title">Attribution aux Produits & Publication</h2>
                        <p class="studio-card-subtitle">Sélectionnez les produits qui afficheront immédiatement ce badge</p>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary px-2.5 py-1.5" id="selectedProductsCounter">{{ count($selectedIds) }} produit(s) lié(s)</span>
            </div>

            <input type="hidden" name="sync_products" value="1">

            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-2">
                <div class="input-group input-group-sm" style="max-width: 340px;">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="tagProductSearchInput" class="form-control" placeholder="Rechercher par nom ou SKU...">
                </div>

                <div class="d-flex gap-1.5">
                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.74rem;" id="btnFilterSelectedOnly" onclick="toggleFilterSelectedProducts()">
                        <i class="fas fa-check-double text-danger me-1"></i>Sélectionnés uniquement
                    </button>
                    <button type="button" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.74rem;" onclick="bulkToggleVisibleProducts(true)">
                        Tout cocher
                    </button>
                    <button type="button" class="btn btn-sm btn-light border py-1 px-2 text-danger" style="font-size: 0.74rem;" onclick="bulkToggleVisibleProducts(false)">
                        Tout décocher
                    </button>
                </div>
            </div>

            <div class="product-assign-list mb-3" id="tagProductsListContainer">
                @foreach($products ?? [] as $prod)
                    @php $isChecked = in_array((int) $prod->id, $selectedIds); @endphp
                    <label class="product-assign-item {{ $isChecked ? 'is-selected' : '' }}"
                           data-search="{{ strtolower($prod->name . ' ' . $prod->sku) }}"
                           for="tag_prod_{{ $prod->id }}">
                        <input class="form-check-input tag-product-checkbox m-0 flex-shrink-0"
                               type="checkbox"
                               name="product_ids[]"
                               value="{{ $prod->id }}"
                               id="tag_prod_{{ $prod->id }}"
                               {{ $isChecked ? 'checked' : '' }}>
                        <img src="{{ $prod->thumbnail }}"
                             alt="{{ $prod->name }}"
                             class="product-assign-thumb"
                             loading="lazy"
                             onerror="this.onerror=null;this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                        <div class="flex-grow-1 text-truncate">
                            <div class="text-dark fw-semibold text-truncate" style="font-size: 0.8rem;">{{ $prod->name }}</div>
                            <div class="text-muted" style="font-size: 0.68rem;">
                                SKU: <code>{{ $prod->sku }}</code>
                                @if($prod->productCategory)
                                    • {{ $prod->productCategory->name }}
                                @endif
                            </div>
                        </div>
                        <span class="text-danger fw-bold flex-shrink-0" style="font-size: 0.76rem;">
                            {{ currency($prod->price) }}
                        </span>
                    </label>
                @endforeach
            </div>

            <div class="row g-3 pt-2 border-top">
                <div class="col-12 col-md-6">
                    <label class="studio-label">Statut de Visibilité</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>🟢 Actif (Affiché sur la boutique)</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>⚪ Inactif (Brouillon masqué)</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="studio-label">
                        <span>Ordre de priorité</span>
                        <span class="text-muted fw-normal" style="font-size: 0.7rem;">1 = affiché en premier</span>
                    </label>
                    <input type="number" name="sort_order" class="form-control" value="{{ $sortOrder }}" min="0">
                </div>
            </div>
        </div>
    </div>

    <!-- COLONNE DROITE : SIMULATEUR BOUTIQUE TEMPS RÉEL -->
    <div class="col-12 col-xl-5">
        <div class="simulator-sticky-wrap">
            <div class="studio-card mb-0">
                <div class="studio-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="studio-step-badge" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                            <i class="fas fa-eye"></i>
                        </span>
                        <div>
                            <h2 class="studio-card-title">Simulateur Boutique Temps Réel</h2>
                            <p class="studio-card-subtitle">Rendu exact avec mise à l'échelle Container Query (`cqw`)</p>
                        </div>
                    </div>
                    <span class="badge bg-dark" id="simCardWidthBadge">245px</span>
                </div>

                <!-- Sélecteur de produit réel pour le test -->
                <div class="mb-2">
                    <select id="simProductSwitcher" class="form-select form-select-sm" onchange="switchSimulatedProduct(this)">
                        @foreach(($products ?? collect())->take(25) as $idx => $pItem)
                            <option value="{{ $pItem->id }}"
                                    data-name="{{ $pItem->name }}"
                                    data-price="{{ $pItem->formatted_price }}"
                                    data-thumb="{{ $pItem->thumbnail }}">
                                📷 Tester sur : {{ Str::limit($pItem->name, 42) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Onglets de taille d'écran / Comparaison -->
                <div class="simulator-viewport-tabs">
                    <button type="button" class="sim-viewport-tab" data-mode="150" onclick="setSimulatorMode(this, 150)">
                        📱 Mobile
                    </button>
                    <button type="button" class="sim-viewport-tab" data-mode="205" onclick="setSimulatorMode(this, 205)">
                        📲 Tablette
                    </button>
                    <button type="button" class="sim-viewport-tab active" data-mode="255" onclick="setSimulatorMode(this, 255)">
                        💻 Desktop
                    </button>
                    <button type="button" class="sim-viewport-tab" data-mode="compare" onclick="setSimulatorMode(this, 'compare')">
                        ⚖️ Comparer
                    </button>
                </div>

                <!-- Curseur de largeur continue -->
                <div id="simWidthSliderWrap" class="mb-2">
                    <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.71rem;">
                        <span>Grille Mobile (135px)</span>
                        <span>Glissez pour redimensionner la carte</span>
                        <span>Grille Large (300px)</span>
                    </div>
                    <input type="range" id="simCardWidthSlider" class="form-range" min="135" max="300" value="255">
                </div>

                <!-- Scène d'aperçu -->
                <div class="simulator-canvas" id="simulatorCanvas">
                    <!-- Carte Principale (Desktop / Redimensionnable) -->
                    <div>
                        <div class="sim-card-caption" id="primaryCardCaption" style="display: none;">💻 Carte Desktop (235px)</div>
                        <div class="sim-store-card" id="simulatedProductCard" style="width: 255px;">
                            <div class="sim-store-img-box">
                                <div class="sim-corner-slot pos-{{ $position }}" id="simulatedCornerSlot">
                                    <span class="product-card-tag-badge" id="livePreviewBadge">
                                        <i id="livePreviewIcon" class="{{ $icon }}" style="{{ empty($icon) ? 'display:none;' : '' }}"></i>
                                        <span id="livePreviewText">{{ $name }}</span>
                                    </span>
                                </div>
                                <img id="simProductImage1" src="{{ $sampleThumb }}" alt="Preview" onerror="this.onerror=null;this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </div>
                            <div>
                                <div class="sim-store-title" id="simProductTitle1">{{ $sampleName }}</div>
                                <div class="sim-store-price-row">
                                    <span class="sim-store-price" id="simProductPrice1">{{ $samplePrice }}</span>
                                    <span class="sim-store-stock"><i class="fas fa-check-circle me-1"></i>En stock</span>
                                </div>
                                <div class="sim-store-cta">Ajouter au panier</div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte Secondaire (Mobile 145px en mode Comparaison Côte-à-Côte) -->
                    <div id="compareMobileCardWrap" style="display: none;">
                        <div class="sim-card-caption">📱 Carte Mobile (145px)</div>
                        <div class="sim-store-card" style="width: 145px;">
                            <div class="sim-store-img-box">
                                <div class="sim-corner-slot pos-{{ $position }}" id="simulatedCornerSlot2">
                                    <span class="product-card-tag-badge" id="livePreviewBadge2">
                                        <i id="livePreviewIcon2" class="{{ $icon }}" style="{{ empty($icon) ? 'display:none;' : '' }}"></i>
                                        <span id="livePreviewText2">{{ $name }}</span>
                                    </span>
                                </div>
                                <img id="simProductImage2" src="{{ $sampleThumb }}" alt="Preview Mobile" onerror="this.onerror=null;this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                            </div>
                            <div>
                                <div class="sim-store-title" id="simProductTitle2">{{ $sampleName }}</div>
                                <div class="sim-store-price-row">
                                    <span class="sim-store-price" id="simProductPrice2">{{ $samplePrice }}</span>
                                    <span class="sim-store-stock"><i class="fas fa-check-circle"></i></span>
                                </div>
                                <div class="sim-store-cta">Ajouter au panier</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'Action -->
                <div class="mt-3 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn-brand-primary flex-grow-1 justify-content-center py-2.5 shadow-sm">
                        <i class="fas fa-check-circle me-1.5"></i>
                        {{ $isEdit ? 'Enregistrer les modifications' : 'Créer et activer ce badge' }}
                    </button>
                    <a href="{{ route('product-tags.index') }}" class="btn btn-light border py-2.5 px-3 text-muted fw-semibold text-decoration-none">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let filterSelectedOnly = false;

function hexToRgbaJs(hex, alpha) {
    hex = (hex || '#dc2626').replace('#', '').trim();
    if (hex.length === 3) {
        hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    }
    if (hex.length !== 6) {
        return `rgba(220, 38, 38, ${alpha})`;
    }
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function updateLiveTagPreview() {
    const nameInput = document.getElementById('tagNameInput');
    const name = nameInput.value || 'BADGE';
    document.getElementById('charCountLabel').textContent = `${nameInput.value.length}/60`;

    const icon = document.getElementById('tagIconInput').value.trim();
    const styleType = document.getElementById('tagStyleType').value;
    const shape = document.getElementById('tagShape').value;
    const bg1 = document.getElementById('bgColorInput').value || '#dc2626';
    const bg2 = document.getElementById('bgGradientInput').value || bg1;
    const text = document.getElementById('textColorInput').value || '#ffffff';
    const border = document.getElementById('borderColorInput').value.trim();
    const scaleVal = parseInt(document.getElementById('tagSizeScale').value || '100', 10);
    const scale = (scaleVal / 100).toFixed(2);
    const weight = document.getElementById('tagFontWeight').value || '800';
    const uppercase = document.getElementById('tagUppercase').checked;
    const hasShadow = document.getElementById('tagHasShadow').checked;
    const pulse = document.getElementById('tagPulseAnimation').checked;
    const pos = document.getElementById('tagPositionInput').value || 'top-left';

    document.getElementById('sizeScaleValueBadge').textContent = scaleVal + '%';
    document.getElementById('gradientColorGroup').style.opacity = styleType === 'gradient' ? '1' : '0.4';

    // Update calculated metrics for Mobile (155px) and Desktop (260px)
    const mobileFontPx = (Math.max(8.3, Math.min(12.2, 155 * 0.038)) * (scaleVal / 100)).toFixed(1);
    const desktopFontPx = (Math.max(8.3, Math.min(12.2, 260 * 0.038)) * (scaleVal / 100)).toFixed(1);
    document.getElementById('metricMobileSize').textContent = `Police ~${mobileFontPx}px`;
    document.getElementById('metricDesktopSize').textContent = `Police ~${desktopFontPx}px`;

    // Shape radius
    let radius = '6px';
    if (shape === 'pill') radius = '999px';
    else if (shape === 'square') radius = '2px';
    else if (shape === 'leaf') radius = '10px 2px 10px 2px';
    else if (shape === 'ribbon') radius = '4px 4px 4px 0px';

    // Style computation
    let backgroundCss = bg1;
    let borderCss = border ? `1px solid ${border}` : 'none';
    let colorCss = text;
    let backdropCss = 'none';

    if (styleType === 'gradient') {
        backgroundCss = `linear-gradient(135deg, ${bg1} 0%, ${bg2} 100%)`;
    } else if (styleType === 'outline') {
        backgroundCss = 'rgba(255, 255, 255, 0.95)';
        borderCss = `1.5px solid ${border || bg1}`;
        colorCss = bg1;
        backdropCss = 'blur(4px)';
    } else if (styleType === 'soft') {
        backgroundCss = hexToRgbaJs(bg1, 0.15);
        borderCss = `1px solid ${border || hexToRgbaJs(bg1, 0.32)}`;
        colorCss = bg1;
        backdropCss = 'blur(6px)';
    } else if (styleType === 'glass') {
        backgroundCss = hexToRgbaJs(bg1, 0.80);
        borderCss = `1px solid ${border || 'rgba(255,255,255,0.38)'}`;
        colorCss = text;
        backdropCss = 'blur(8px)';
    }

    const shadowCss = hasShadow
        ? `0 3px 10px ${hexToRgbaJs(bg1, 0.28)}, 0 1px 3px rgba(0,0,0,0.12)`
        : 'none';

    const currentIconPreview = document.getElementById('currentIconPreview');
    currentIconPreview.className = icon ? icon : 'fas fa-ban text-muted';

    // Apply to both primary & comparison badges
    [
        ['livePreviewBadge', 'livePreviewText', 'livePreviewIcon', 'simulatedCornerSlot'],
        ['livePreviewBadge2', 'livePreviewText2', 'livePreviewIcon2', 'simulatedCornerSlot2']
    ].forEach(([bId, tId, iId, sId]) => {
        const badge = document.getElementById(bId);
        const textEl = document.getElementById(tId);
        const iconEl = document.getElementById(iId);
        const slotEl = document.getElementById(sId);
        if (!badge) return;

        textEl.textContent = name;
        if (icon) {
            iconEl.className = icon;
            iconEl.style.display = 'inline-block';
        } else {
            iconEl.style.display = 'none';
        }

        badge.style.setProperty('--tag-scale', scale);
        badge.style.setProperty('--tag-bg', backgroundCss);
        badge.style.setProperty('--tag-color', colorCss);
        badge.style.setProperty('--tag-border', borderCss);
        badge.style.setProperty('--tag-radius', radius);
        badge.style.setProperty('--tag-weight', weight);
        badge.style.setProperty('--tag-transform', uppercase ? 'uppercase' : 'none');
        badge.style.setProperty('--tag-shadow', shadowCss);
        badge.style.setProperty('--tag-backdrop', backdropCss);
        badge.classList.toggle('tag-pulse', pulse);

        slotEl.className = `sim-corner-slot pos-${pos}`;
    });
}

function setTagText(txt) {
    document.getElementById('tagNameInput').value = txt;
    updateLiveTagPreview();
}

function selectIconTile(iconClass) {
    document.getElementById('tagIconInput').value = iconClass;
    document.querySelectorAll('.studio-icon-tile').forEach(el => {
        el.classList.toggle('active', el.dataset.icon === iconClass);
    });
    updateLiveTagPreview();
}

function selectStyleType(styleVal) {
    document.getElementById('tagStyleType').value = styleVal;
    document.querySelectorAll('#styleChoiceGrid .visual-choice-card').forEach(el => {
        el.classList.toggle('active', el.dataset.style === styleVal);
    });
    updateLiveTagPreview();
}

function selectShapeType(shapeVal) {
    document.getElementById('tagShape').value = shapeVal;
    document.querySelectorAll('#shapeChoiceGrid .visual-choice-card').forEach(el => {
        el.classList.toggle('active', el.dataset.shape === shapeVal);
    });
    updateLiveTagPreview();
}

function applyColorPair(c1, c2) {
    document.getElementById('bgColorInput').value = c1;
    document.getElementById('bgColorPicker').value = c1;
    document.getElementById('bgGradientInput').value = c2;
    document.getElementById('bgGradientPicker').value = c2;
    updateLiveTagPreview();
}

function clearBorderColor() {
    document.getElementById('borderColorInput').value = '';
    updateLiveTagPreview();
}

function selectCornerPosition(posVal) {
    document.getElementById('tagPositionInput').value = posVal;
    document.querySelectorAll('.corner-hotspot').forEach(el => {
        el.classList.toggle('active', el.dataset.pos === posVal);
    });
    updateLiveTagPreview();
}

function setScaleValue(scaleVal) {
    document.getElementById('tagSizeScale').value = scaleVal;
    document.querySelectorAll('.scale-pill-btn').forEach(el => {
        el.classList.toggle('active', parseInt(el.dataset.scale, 10) === scaleVal);
    });
    updateLiveTagPreview();
}

function setSimulatorMode(btnEl, mode) {
    document.querySelectorAll('.sim-viewport-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');

    const primaryCard = document.getElementById('simulatedProductCard');
    const primaryCaption = document.getElementById('primaryCardCaption');
    const compareWrap = document.getElementById('compareMobileCardWrap');
    const sliderWrap = document.getElementById('simWidthSliderWrap');
    const slider = document.getElementById('simCardWidthSlider');
    const badge = document.getElementById('simCardWidthBadge');

    if (mode === 'compare') {
        sliderWrap.style.display = 'none';
        primaryCaption.style.display = 'block';
        compareWrap.style.display = 'block';
        primaryCard.style.width = '230px';
        badge.textContent = '230px vs 145px';
    } else {
        sliderWrap.style.display = 'block';
        primaryCaption.style.display = 'none';
        compareWrap.style.display = 'none';
        primaryCard.style.width = mode + 'px';
        slider.value = mode;
        badge.textContent = mode + 'px';
    }
}

function switchSimulatedProduct(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;
    const name = opt.dataset.name;
    const price = opt.dataset.price;
    const thumb = opt.dataset.thumb;

    ['1', '2'].forEach(suffix => {
        document.getElementById('simProductTitle' + suffix).textContent = name;
        document.getElementById('simProductPrice' + suffix).textContent = price;
        document.getElementById('simProductImage' + suffix).src = thumb;
    });
}

function applyTagTemplate(tileEl, name, icon, styleType, bg1, bg2, text, border, shape, pos, scale, pulse) {
    document.querySelectorAll('.studio-preset-tile').forEach(t => t.classList.remove('active'));
    if (tileEl) tileEl.classList.add('active');

    document.getElementById('tagNameInput').value = name;
    document.getElementById('bgColorInput').value = bg1;
    document.getElementById('bgColorPicker').value = bg1;
    document.getElementById('bgGradientInput').value = bg2;
    document.getElementById('bgGradientPicker').value = bg2;
    document.getElementById('textColorInput').value = text;
    document.getElementById('textColorPicker').value = text;
    document.getElementById('borderColorInput').value = border;
    if (border && /^#[0-9A-Fa-f]{6}$/.test(border)) {
        document.getElementById('borderColorPicker').value = border;
    }
    document.getElementById('tagPulseAnimation').checked = !!pulse;

    selectIconTile(icon);
    selectStyleType(styleType);
    selectShapeType(shape);
    selectCornerPosition(pos);
    setScaleValue(scale);
}

function filterProductAssignRows() {
    const q = (document.getElementById('tagProductSearchInput')?.value || '').toLowerCase().trim();
    document.querySelectorAll('.product-assign-item').forEach(row => {
        const cb = row.querySelector('.tag-product-checkbox');
        const matchesSearch = !q || row.dataset.search.includes(q);
        const matchesSelected = !filterSelectedOnly || (cb && cb.checked);
        row.style.display = (matchesSearch && matchesSelected) ? '' : 'none';
    });
}

function toggleFilterSelectedProducts() {
    filterSelectedOnly = !filterSelectedOnly;
    const btn = document.getElementById('btnFilterSelectedOnly');
    btn.classList.toggle('btn-danger', filterSelectedOnly);
    btn.classList.toggle('text-white', filterSelectedOnly);
    btn.classList.toggle('btn-light', !filterSelectedOnly);
    filterProductAssignRows();
}

function bulkToggleVisibleProducts(checkState) {
    document.querySelectorAll('.product-assign-item').forEach(row => {
        if (row.style.display !== 'none') {
            const cb = row.querySelector('.tag-product-checkbox');
            if (cb) {
                cb.checked = checkState;
                row.classList.toggle('is-selected', checkState);
            }
        }
    });
    updateSelectedProductsCount();
}

function updateSelectedProductsCount() {
    const count = document.querySelectorAll('.tag-product-checkbox:checked').length;
    document.getElementById('selectedProductsCounter').textContent = `${count} produit(s) lié(s)`;
}

document.addEventListener('DOMContentLoaded', function () {
    // Sync color pickers and hex inputs
    [
        ['bgColorPicker', 'bgColorInput'],
        ['bgGradientPicker', 'bgGradientInput'],
        ['textColorPicker', 'textColorInput'],
        ['borderColorPicker', 'borderColorInput']
    ].forEach(([pickerId, inputId]) => {
        const picker = document.getElementById(pickerId);
        const input = document.getElementById(inputId);
        picker?.addEventListener('input', () => {
            input.value = picker.value;
            updateLiveTagPreview();
        });
        input?.addEventListener('input', () => {
            if (/^#[0-9A-Fa-f]{6}$/.test(input.value.trim())) {
                picker.value = input.value.trim();
            }
            updateLiveTagPreview();
        });
    });

    ['tagNameInput', 'tagIconInput', 'tagSizeScale', 'tagFontWeight', 'tagUppercase', 'tagHasShadow', 'tagPulseAnimation'].forEach(id => {
        const el = document.getElementById(id);
        el?.addEventListener('input', updateLiveTagPreview);
        el?.addEventListener('change', updateLiveTagPreview);
    });

    // Continuous width slider
    document.getElementById('simCardWidthSlider')?.addEventListener('input', function () {
        const px = this.value;
        document.getElementById('simulatedProductCard').style.width = px + 'px';
        document.getElementById('simCardWidthBadge').textContent = px + 'px';
    });

    // Product search & selection
    document.getElementById('tagProductSearchInput')?.addEventListener('input', filterProductAssignRows);

    document.querySelectorAll('.tag-product-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            this.closest('.product-assign-item')?.classList.toggle('is-selected', this.checked);
            updateSelectedProductsCount();
        });
    });

    updateLiveTagPreview();
});
</script>
@endpush
