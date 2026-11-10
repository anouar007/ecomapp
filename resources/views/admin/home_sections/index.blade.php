@extends('layouts.app')

@section('title', 'Gestion des Produits de l\'Accueil')

@push('styles')
<style>
    /* ── Tabs Navigation ── */
    .home-section-tabs {
        border-bottom: 2px solid #edf2f7;
        gap: 4px;
    }
    .home-section-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 12px 18px;
        font-size: 0.92rem;
        background: transparent;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: -2px;
    }
    .home-section-tabs .nav-link:hover {
        color: #0f172a;
        background: rgba(241, 245, 249, 0.7);
        border-bottom-color: #cbd5e1;
    }
    .home-section-tabs .nav-link.active {
        color: #c8102e !important;
        border-bottom-color: #c8102e !important;
        background: #ffffff;
        font-weight: 700;
    }
    .home-section-tabs .nav-link.active .badge {
        background: #c8102e !important;
        color: #ffffff !important;
        border-color: #c8102e !important;
    }

    /* ── Sortable Products List & Cards ── */
    .home-products-sortable-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 80px;
    }

    .home-product-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .home-product-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        transform: translateY(-1px);
    }
    .home-product-item.sortable-ghost {
        opacity: 0.35;
        background: #f1f5f9;
        border: 2px dashed #94a3b8;
    }
    .home-product-item.sortable-chosen {
        cursor: grabbing;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.14);
        border-color: #c8102e;
    }

    .drag-handle {
        color: #94a3b8;
        cursor: grab;
        padding: 6px 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
        touch-action: none;
    }
    .drag-handle:hover {
        color: #0f172a;
    }

    .order-badge {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .item-thumbnail {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        border: 1px solid #edf2f7;
        background: #ffffff;
        padding: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .item-thumbnail img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .btn-reorder-nav {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.74rem;
        padding: 0;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .btn-reorder-nav:hover {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
        transform: scale(1.05);
    }
    .btn-remove-item {
        width: 32px;
        height: 32px;
        border-radius: 8px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    /* ── Add Product Modal & Search Results ── */
    .btn-search-mode {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 600;
        border-radius: 20px;
        padding: 4px 12px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-search-mode:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .btn-search-mode.active {
        background: #c8102e !important;
        color: #ffffff !important;
        border-color: #c8102e !important;
        box-shadow: 0 2px 8px rgba(200, 16, 46, 0.25);
    }

    .search-results-wrapper {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }
    .search-results-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 14px;
        font-size: 0.8rem;
    }
    .search-results-scrollable {
        max-height: 440px;
        min-height: 240px;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        background: #ffffff;
    }
    .search-results-scrollable::-webkit-scrollbar {
        width: 7px;
    }
    .search-results-scrollable::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .search-results-scrollable::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .search-results-scrollable::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .search-result-item {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: background 0.15s ease;
    }
    .search-result-item:last-child {
        border-bottom: none;
    }
    .search-result-item:hover {
        background: #fcfdfe;
    }

    .empty-section-placeholder {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 40px 20px;
        text-align: center;
        background: #fcfdfe;
    }

    /* ── Responsive Mobile Optimizations (< 768px) ── */
    @media (max-width: 767.98px) {
        .brand-header-actions {
            width: 100%;
            display: flex;
            gap: 8px;
        }
        .brand-header-actions a {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
        .home-product-item {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            padding: 10px 12px;
        }
        .item-main-row {
            width: 100%;
        }
        .item-actions-row {
            width: 100%;
            padding-top: 8px;
            border-top: 1px dashed #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .item-price {
            text-align: left !important;
        }
        .btn-reorder-nav, .btn-remove-item {
            width: 36px;
            height: 36px;
        }
        .search-results-scrollable {
            max-height: 360px;
        }
        .search-result-item {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            padding: 10px;
        }
        .search-result-item .item-btn-wrap {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }
        .search-result-item .item-btn-wrap .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-sm-3 px-md-4 py-3">

    <!-- Executive Brand Header (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des Sections de l'Accueil</h1>
                <p class="brand-subtitle">Sélectionnez les produits et organisez leur ordre d'affichage en direct sur la boutique</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ url('/') }}" target="_blank" class="btn-brand-outline">
                <i class="fas fa-external-link-alt me-1.5" style="color: var(--primary-color)"></i> <span>Voir l'accueil</span>
            </a>
            <a href="{{ route('banners.index') }}" class="btn-brand-outline">
                <i class="fas fa-sliders-h me-1.5 text-danger"></i> <span>Slider & Bannières</span>
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- KPI Cards Row (Product Section Standard) --}}
    <div class="row g-3 mb-4">
        @foreach($sectionsData as $sKey => $sData)
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">{{ $sData['badge'] }}</div>
                        <div class="stat-kpi-value text-dark" id="kpi-count-{{ $sKey }}">{{ $sData['count'] }} <span class="fs-6 fw-normal text-muted">produits</span></div>
                        <div class="stat-kpi-sub text-muted text-truncate" style="max-width: 220px;" title="{{ $sData['current_title'] }}">
                            {{ $sData['current_title'] }}
                        </div>
                    </div>
                    <div class="stat-kpi-icon @if($sKey === 'featured') bg-warning-subtle text-warning @elseif($sKey === 'new_arrivals') bg-danger-subtle text-danger @else bg-success-subtle text-success @endif">
                        <i class="{{ $sData['icon'] }}"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Sections Manager Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <!-- Tabs Header -->
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-3 px-md-4">
            <ul class="nav nav-tabs home-section-tabs border-0 flex-nowrap overflow-x-auto" id="homeSectionsTab" role="tablist" style="scrollbar-width: none;">
                @foreach($sectionsData as $sKey => $sData)
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-nowrap {{ $activeTab === $sKey ? 'active' : '' }}" 
                            id="tab-{{ $sKey }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#pane-{{ $sKey }}" 
                            type="button" 
                            role="tab" 
                            aria-controls="pane-{{ $sKey }}" 
                            aria-selected="{{ $activeTab === $sKey ? 'true' : 'false' }}">
                        <i class="{{ $sData['icon'] }} me-1"></i>
                        <span>{{ $sData['badge'] }}</span>
                        <span class="badge rounded-pill bg-light text-dark border ms-1" id="tab-badge-{{ $sKey }}">{{ $sData['count'] }}</span>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Tab Panes Content -->
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="homeSectionsTabContent">
                @foreach($sectionsData as $sKey => $sData)
                <div class="tab-pane fade {{ $activeTab === $sKey ? 'show active' : '' }}" 
                     id="pane-{{ $sKey }}" 
                     role="tabpanel" 
                     aria-labelledby="tab-{{ $sKey }}" 
                     tabindex="0">

                    <form id="form-section-{{ $sKey }}" onsubmit="saveSection('{{ $sKey }}', event)">
                        @csrf
                        <input type="hidden" name="section" value="{{ $sKey }}">

                        <!-- Section Header Info & Actions -->
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <label for="title-{{ $sKey }}" class="form-label small fw-bold text-muted text-uppercase mb-1">
                                    Titre de la section sur l'accueil
                                </label>
                                <div class="input-group" style="max-width: 500px;">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-heading"></i></span>
                                    <input type="text" 
                                           id="title-{{ $sKey }}" 
                                           name="title" 
                                           class="form-control border-start-0 fw-semibold" 
                                           value="{{ $sData['current_title'] }}" 
                                           placeholder="{{ $sData['default_title'] }}" 
                                           required>
                                </div>
                                <div class="form-text small text-muted mt-1">{{ $sData['desc'] }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto">
                                <button type="button" 
                                        class="btn btn-outline-danger fw-bold rounded-pill px-3 py-2 flex-grow-1 flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-2" 
                                        onclick="openAddProductModal('{{ $sKey }}')">
                                    <i class="fas fa-plus"></i> <span>Ajouter des produits</span>
                                </button>
                                <button type="submit" 
                                        class="btn btn-danger fw-bold rounded-pill px-4 py-2 flex-grow-1 flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-2" 
                                        style="background: #c8102e; border-color: #c8102e;"
                                        id="btn-save-{{ $sKey }}">
                                    <i class="fas fa-save"></i> <span>Enregistrer l'ordre</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tip Banner -->
                        <div class="alert alert-light border d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 py-2 px-3 rounded-3 mb-3 small text-muted">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-arrows-up-down-left-right text-danger flex-shrink-0"></i>
                                <span><strong>Glissez-déposez</strong> les cartes pour changer l'ordre, ou utilisez les flèches <i class="fas fa-arrow-up"></i> <i class="fas fa-arrow-down"></i>. Le premier produit apparaîtra en tête.</span>
                            </div>
                            <span class="badge bg-white border text-dark fw-bold px-2 py-1 align-self-end align-self-sm-auto"><span id="live-count-{{ $sKey }}">{{ $sData['count'] }}</span> sélectionné(s)</span>
                        </div>

                        <!-- Sortable Products List -->
                        <div class="home-products-sortable-list" id="sortable-list-{{ $sKey }}">
                            @forelse($sData['products'] as $idx => $p)
                                <div class="home-product-item" data-product-id="{{ $p->id }}" id="item-{{ $sKey }}-{{ $p->id }}">
                                    <input type="hidden" name="product_ids[]" value="{{ $p->id }}">

                                    <div class="item-main-row d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
                                        <div class="drag-handle" title="Glisser pour réorganiser">
                                            <i class="fas fa-grip-vertical"></i>
                                        </div>

                                        <div class="order-badge item-order-num">#{{ $idx + 1 }}</div>

                                        <div class="item-thumbnail">
                                            <img src="{{ $p->thumbnail }}" alt="{{ $p->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                        </div>

                                        <div class="item-info flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                                                <a href="{{ route('shop.show', $p->slug ?: $p->id) }}" target="_blank" class="fw-bold text-dark text-truncate text-decoration-none hover-red item-title" style="font-size: 0.92rem;">
                                                    {{ $p->name }}
                                                </a>
                                                @if($p->isInStock())
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">En stock</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">Rupture</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 small text-muted flex-wrap item-meta" style="font-size: 0.78rem;">
                                                <span class="font-monospace text-secondary badge bg-light border px-1.5 py-0.5"><i class="fas fa-barcode me-1 text-muted"></i>Code: {{ $p->sku ?: '—' }}</span>
                                                <span>•</span>
                                                <span>{{ $p->productCategory?->name ?: 'Général' }}</span>
                                                @if($p->brand)
                                                    <span>•</span>
                                                    <span class="fw-semibold text-dark">{{ $p->brand->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="item-actions-row d-flex align-items-center justify-content-between justify-content-md-end gap-2 flex-shrink-0">
                                        <div class="item-price text-end me-1">
                                            <div class="fw-bold text-dark item-price-val" style="font-size: 0.95rem;">{{ $p->formatted_price }}</div>
                                        </div>

                                        <!-- Reorder Arrows -->
                                        <div class="d-flex align-items-center gap-1 item-arrows">
                                            <button type="button" class="btn-reorder-nav" onclick="moveProductItem('{{ $sKey }}', this, -1)" title="Monter">
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                            <button type="button" class="btn-reorder-nav" onclick="moveProductItem('{{ $sKey }}', this, 1)" title="Descendre">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                        </div>

                                        <!-- Remove Button -->
                                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-remove-item" onclick="removeProductItem('{{ $sKey }}', '{{ $p->id }}')" title="Retirer de la section">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-section-placeholder" id="empty-placeholder-{{ $sKey }}">
                                    <div class="text-muted mb-2"><i class="fas fa-box-open fs-2"></i></div>
                                    <h5 class="fw-bold text-dark mb-1">Aucun produit sélectionné</h5>
                                    <p class="text-muted small mb-3">Cliquez sur « Ajouter des produits » pour peupler cette section de l'accueil.</p>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5" onclick="openAddProductModal('{{ $sKey }}')">
                                        <i class="fas fa-plus me-1"></i> Ajouter un produit
                                    </button>
                                </div>
                            @endforelse
                        </div>

                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Products to Section (With Dashboard Search Engine & Scrollable Results) -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom px-3 px-md-4 py-3 bg-light-subtle">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="rounded-3 bg-danger text-white p-2 d-inline-flex shadow-sm"><i class="fas fa-search-plus fs-5"></i></span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="addProductModalLabel">Ajouter des produits au catalogue</h5>
                        <p class="text-muted small mb-0">Recherche par nom (tolérance 70%), code produit / SKU, ou ID (#)</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 p-md-4">
                <!-- Search Box styled like Dashboard Products Page -->
                <div class="card border rounded-3 p-2.5 mb-3 bg-white shadow-sm">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="fas fa-search text-danger"></i></span>
                        <input type="text" 
                               id="modalProductSearchInput" 
                               class="form-control border-0 shadow-none ps-2" 
                               placeholder="Rechercher par nom de produit (tolérance 70%), code produit / SKU, ou ID..." 
                               oninput="debounceModalSearch()"
                               autocomplete="off">
                        <button class="btn btn-link text-muted pe-2 text-decoration-none d-none" id="modalSearchClearBtn" type="button" onclick="clearModalSearch()" title="Effacer la recherche">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    
                    <!-- Search Mode Selector Pills -->
                    <div class="d-flex align-items-center gap-1.5 pt-2 mt-2 border-top flex-wrap" id="modalSearchModePills">
                        <span class="small fw-bold text-muted me-1" style="font-size: 0.74rem;">Rechercher par :</span>
                        <button type="button" class="btn btn-sm btn-search-mode active" data-mode="all" onclick="setModalSearchMode('all')">
                            <i class="fas fa-layer-group me-1"></i> Tous critères
                        </button>
                        <button type="button" class="btn btn-sm btn-search-mode" data-mode="name" onclick="setModalSearchMode('name')">
                            <i class="fas fa-font me-1"></i> Nom produit (IA 70%)
                        </button>
                        <button type="button" class="btn btn-sm btn-search-mode" data-mode="code" onclick="setModalSearchMode('code')">
                            <i class="fas fa-barcode me-1"></i> Code / SKU / ID (#)
                        </button>
                    </div>
                </div>

                <!-- Live Results Header & Scroll Container -->
                <div class="search-results-wrapper shadow-sm">
                    <div class="search-results-header d-flex align-items-center justify-content-between">
                        <div class="small fw-semibold text-secondary" id="modalResultsCount">
                            <span id="modalSearchSpinner" class="spinner-border spinner-border-sm text-danger me-1.5 d-none" role="status"></span>
                            <span id="modalResultsCountText">Chargement des produits...</span>
                        </div>
                        <div class="small text-muted d-none d-sm-flex align-items-center gap-1" style="font-size: 0.75rem;">
                            <i class="fas fa-arrows-up-down text-secondary"></i>
                            <span>Faites défiler pour voir la liste</span>
                        </div>
                    </div>

                    <!-- Scrollable Results Container -->
                    <div id="modalSearchResults" class="search-results-scrollable">
                        <div class="p-4 text-center text-muted">
                            <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                            Chargement des produits du catalogue...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-3 px-md-4 py-2.5 bg-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <span class="small text-muted" id="modalSelectedSummary">
                    <i class="fas fa-info-circle text-primary me-1"></i> Cliquez sur « Ajouter » pour insérer le produit dans la section en direct.
                </span>
                <button type="button" class="btn btn-dark rounded-pill px-4 w-100 w-sm-auto" data-bs-dismiss="modal">Terminé</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeSectionKey = '{{ $activeTab }}';
    let sortableInstances = {};
    let searchDebounceTimer = null;
    let currentSearchMode = 'all';

    document.addEventListener('DOMContentLoaded', function() {
        initAllSortables();

        // Sync active section when tab switches
        const tabElList = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabElList.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function(event) {
                const targetId = event.target.getAttribute('data-bs-target');
                activeSectionKey = targetId.replace('#pane-', '');
            });
        });
    });

    function initAllSortables() {
        ['featured', 'new_arrivals', 'occasion'].forEach(sec => {
            const listEl = document.getElementById(`sortable-list-${sec}`);
            if (listEl && typeof Sortable !== 'undefined') {
                if (sortableInstances[sec]) {
                    sortableInstances[sec].destroy();
                }
                sortableInstances[sec] = new Sortable(listEl, {
                    animation: 200,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function() {
                        updateOrderNumbers(sec);
                    }
                });
            }
        });
    }

    function updateOrderNumbers(sectionKey) {
        const list = document.getElementById(`sortable-list-${sectionKey}`);
        if (!list) return;

        const items = list.querySelectorAll('.home-product-item');
        items.forEach((item, idx) => {
            const badge = item.querySelector('.item-order-num');
            if (badge) badge.textContent = `#${idx + 1}`;
        });

        const count = items.length;
        const liveCountEl = document.getElementById(`live-count-${sectionKey}`);
        const tabBadgeEl = document.getElementById(`tab-badge-${sectionKey}`);
        const kpiCountEl = document.getElementById(`kpi-count-${sectionKey}`);

        if (liveCountEl) liveCountEl.textContent = count;
        if (tabBadgeEl) tabBadgeEl.textContent = count;
        if (kpiCountEl) kpiCountEl.innerHTML = `${count} <span class="fs-6 fw-normal text-muted">produits</span>`;

        // Toggle placeholder if empty
        let placeholder = document.getElementById(`empty-placeholder-${sectionKey}`);
        if (count === 0 && !placeholder) {
            list.innerHTML = `
                <div class="empty-section-placeholder" id="empty-placeholder-${sectionKey}">
                    <div class="text-muted mb-2"><i class="fas fa-box-open fs-2"></i></div>
                    <h5 class="fw-bold text-dark mb-1">Aucun produit sélectionné</h5>
                    <p class="text-muted small mb-3">Cliquez sur « Ajouter des produits » pour peupler cette section de l'accueil.</p>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5" onclick="openAddProductModal('${sectionKey}')">
                        <i class="fas fa-plus me-1"></i> Ajouter un produit
                    </button>
                </div>
            `;
        } else if (count > 0 && placeholder) {
            placeholder.remove();
        }
    }

    function moveProductItem(sectionKey, btn, direction) {
        const item = btn.closest('.home-product-item');
        if (!item) return;

        if (direction === -1 && item.previousElementSibling && item.previousElementSibling.classList.contains('home-product-item')) {
            item.parentNode.insertBefore(item, item.previousElementSibling);
        } else if (direction === 1 && item.nextElementSibling && item.nextElementSibling.classList.contains('home-product-item')) {
            item.parentNode.insertBefore(item.nextElementSibling, item);
        }

        updateOrderNumbers(sectionKey);
    }

    function removeProductItem(sectionKey, productId) {
        const item = document.getElementById(`item-${sectionKey}-${productId}`);
        if (!item) return;

        item.style.transition = 'all 0.25s ease';
        item.style.opacity = '0';
        item.style.transform = 'scale(0.95)';

        setTimeout(() => {
            item.remove();
            updateOrderNumbers(sectionKey);
        }, 220);
    }

    function openAddProductModal(sectionKey) {
        activeSectionKey = sectionKey;
        const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
        modal.show();
        loadModalProducts();
    }

    function setModalSearchMode(mode) {
        currentSearchMode = mode;
        const pills = document.querySelectorAll('#modalSearchModePills .btn-search-mode');
        pills.forEach(p => {
            if (p.getAttribute('data-mode') === mode) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });
        loadModalProducts();
    }

    function clearModalSearch() {
        const input = document.getElementById('modalProductSearchInput');
        if (input) {
            input.value = '';
            input.focus();
        }
        document.getElementById('modalSearchClearBtn').classList.add('d-none');
        loadModalProducts();
    }

    function debounceModalSearch() {
        clearTimeout(searchDebounceTimer);
        const inputVal = document.getElementById('modalProductSearchInput').value || '';
        const clearBtn = document.getElementById('modalSearchClearBtn');
        if (clearBtn) {
            if (inputVal.trim().length > 0) {
                clearBtn.classList.remove('d-none');
            } else {
                clearBtn.classList.add('d-none');
            }
        }
        searchDebounceTimer = setTimeout(loadModalProducts, 260);
    }

    function getSelectedIds(sectionKey) {
        const list = document.getElementById(`sortable-list-${sectionKey}`);
        if (!list) return [];
        return Array.from(list.querySelectorAll('input[name="product_ids[]"]')).map(inp => inp.value);
    }

    function loadModalProducts() {
        const query = (document.getElementById('modalProductSearchInput').value || '').trim();
        const resultsBox = document.getElementById('modalSearchResults');
        const spinner = document.getElementById('modalSearchSpinner');
        const countText = document.getElementById('modalResultsCountText');

        if (spinner) spinner.classList.remove('d-none');
        if (countText) countText.textContent = 'Recherche dans le catalogue...';

        const existingIds = getSelectedIds(activeSectionKey);

        const url = new URL('{{ route('home-sections.search') }}', window.location.origin);
        if (query) url.searchParams.set('q', query);
        if (currentSearchMode) url.searchParams.set('mode', currentSearchMode);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(data => {
                if (spinner) spinner.classList.add('d-none');

                const products = Array.isArray(data) ? data : (data.products || []);
                const count = data.count !== undefined ? data.count : products.length;

                if (countText) {
                    if (query) {
                        countText.innerHTML = `<strong>${count}</strong> produit(s) trouvé(s) pour « ${escapeHtml(query)} »`;
                    } else {
                        countText.innerHTML = `<strong>${count}</strong> équipements récents du catalogue`;
                    }
                }

                if (!products || products.length === 0) {
                    resultsBox.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-search me-1 text-danger"></i> Aucun produit trouvé correspondant à votre recherche.
                            <div class="small mt-1 text-secondary">Essayez un autre nom de caméra, optique ou son code SKU/ID (#...)</div>
                        </div>
                    `;
                    return;
                }

                let html = '';
                products.forEach(p => {
                    const isAlreadyAdded = existingIds.includes(String(p.id));
                    const stockBadge = p.in_stock 
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">En stock</span>`
                        : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">Rupture</span>`;

                    const codeMatchPill = p.is_code_match 
                        ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;"><i class="fas fa-check-circle me-1"></i>Code trouvé</span>`
                        : '';

                    html += `
                        <div class="search-result-item" id="modal-item-${p.id}">
                            <div class="d-flex align-items-center gap-3 min-w-0 flex-grow-1">
                                <div class="item-thumbnail flex-shrink-0" style="width: 48px; height: 48px;">
                                    <img src="${p.thumbnail}" alt="${escapeHtml(p.name)}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                                        <span class="fw-bold text-dark text-truncate" style="font-size: 0.88rem; max-width: 380px;">${escapeHtml(p.name)}</span>
                                        ${codeMatchPill}
                                        ${stockBadge}
                                    </div>
                                    <div class="small text-muted d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.76rem;">
                                        <span class="font-monospace text-secondary badge bg-light border px-1.5 py-0.5"><i class="fas fa-barcode me-1 text-muted"></i>Code: ${escapeHtml(p.sku)}</span>
                                        <span>•</span>
                                        <span>ID: #${p.id}</span>
                                        <span>•</span>
                                        <span>${escapeHtml(p.category)}</span>
                                        ${p.brand ? `<span>•</span><span class="fw-semibold text-dark">${escapeHtml(p.brand)}</span>` : ''}
                                        <span>•</span>
                                        <span class="fw-bold text-dark">${p.price}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-btn-wrap flex-shrink-0 ms-2">
                                ${isAlreadyAdded 
                                    ? `<button type="button" class="btn btn-sm btn-light border text-muted rounded-pill px-3" disabled><i class="fas fa-check me-1 text-success"></i> Ajouté</button>` 
                                    : `<button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="addProductToSection(${JSON.stringify(p).replace(/"/g, '&quot;')})"><i class="fas fa-plus me-1"></i> Ajouter</button>`
                                }
                            </div>
                        </div>
                    `;
                });

                resultsBox.innerHTML = html;
            })
            .catch(() => {
                if (spinner) spinner.classList.add('d-none');
                resultsBox.innerHTML = `
                    <div class="p-3 text-center text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Erreur lors du chargement des produits.
                    </div>
                `;
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function addProductToSection(product) {
        const list = document.getElementById(`sortable-list-${activeSectionKey}`);
        if (!list) return;

        // Remove placeholder if present
        const placeholder = document.getElementById(`empty-placeholder-${activeSectionKey}`);
        if (placeholder) placeholder.remove();

        const currentCount = list.querySelectorAll('.home-product-item').length;
        const newOrder = currentCount + 1;

        const stockBadge = product.in_stock 
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">En stock</span>`
            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">Rupture</span>`;

        const itemHtml = `
            <div class="home-product-item" data-product-id="${product.id}" id="item-${activeSectionKey}-${product.id}">
                <input type="hidden" name="product_ids[]" value="${product.id}">

                <div class="item-main-row d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
                    <div class="drag-handle" title="Glisser pour réorganiser">
                        <i class="fas fa-grip-vertical"></i>
                    </div>

                    <div class="order-badge item-order-num">#${newOrder}</div>

                    <div class="item-thumbnail">
                        <img src="${product.thumbnail}" alt="${escapeHtml(product.name)}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                    </div>

                    <div class="item-info flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                            <a href="/shop/${product.id}" target="_blank" class="fw-bold text-dark text-truncate text-decoration-none hover-red item-title" style="font-size: 0.92rem;">
                                ${escapeHtml(product.name)}
                            </a>
                            ${stockBadge}
                        </div>
                        <div class="d-flex align-items-center gap-2 small text-muted flex-wrap item-meta" style="font-size: 0.78rem;">
                            <span class="font-monospace text-secondary badge bg-light border px-1.5 py-0.5"><i class="fas fa-barcode me-1 text-muted"></i>Code: ${escapeHtml(product.sku)}</span>
                            <span>•</span>
                            <span>${escapeHtml(product.category)}</span>
                            ${product.brand ? `<span>•</span><span class="fw-semibold text-dark">${escapeHtml(product.brand)}</span>` : ''}
                        </div>
                    </div>
                </div>

                <div class="item-actions-row d-flex align-items-center justify-content-between justify-content-md-end gap-2 flex-shrink-0">
                    <div class="item-price text-end me-1">
                        <div class="fw-bold text-dark item-price-val" style="font-size: 0.95rem;">${product.price}</div>
                    </div>

                    <div class="d-flex align-items-center gap-1 item-arrows">
                        <button type="button" class="btn-reorder-nav" onclick="moveProductItem('${activeSectionKey}', this, -1)" title="Monter">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button type="button" class="btn-reorder-nav" onclick="moveProductItem('${activeSectionKey}', this, 1)" title="Descendre">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-remove-item" onclick="removeProductItem('${activeSectionKey}', '${product.id}')" title="Retirer de la section">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;

        list.insertAdjacentHTML('beforeend', itemHtml);
        updateOrderNumbers(activeSectionKey);

        // Update modal button state to "Ajouté"
        const modalItem = document.getElementById(`modal-item-${product.id}`);
        if (modalItem) {
            const btnWrap = modalItem.querySelector('.item-btn-wrap');
            if (btnWrap) {
                btnWrap.innerHTML = `<button type="button" class="btn btn-sm btn-light border text-muted rounded-pill px-3" disabled><i class="fas fa-check me-1 text-success"></i> Ajouté</button>`;
            }
        }
    }

    function saveSection(sectionKey, event) {
        if (event) event.preventDefault();

        const form = document.getElementById(`form-section-${sectionKey}`);
        const btn = document.getElementById(`btn-save-${sectionKey}`);
        if (!form || !btn) return;

        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> Enregistrement...`;

        const formData = new FormData(form);

        fetch('{{ route('home-sections.update') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = `<i class="fas fa-check me-1"></i> Enregistré !`;
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = origHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-danger');
                btn.disabled = false;
            }, 1800);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Section mise à jour !',
                    text: 'L\'ordre et les produits ont été enregistrés avec succès.',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            }
        })
        .catch(err => {
            btn.innerHTML = origHtml;
            btn.disabled = false;
            alert('Une erreur est survenue lors de l\'enregistrement.');
        });
    }
</script>
@endpush
