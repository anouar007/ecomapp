@extends('layouts.app')

@section('title', 'Gestion des Produits de l\'Accueil')

@push('styles')
<style>
    .home-section-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        font-size: 0.95rem;
        background: transparent;
        border-radius: 0;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .home-section-tabs .nav-link:hover {
        color: #0f172a;
        background: rgba(241, 245, 249, 0.6);
        border-bottom-color: #cbd5e1;
    }
    .home-section-tabs .nav-link.active {
        color: #c8102e !important;
        border-bottom-color: #c8102e !important;
        background: transparent;
    }

    .home-products-sortable-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-height: 80px;
    }

    .home-product-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
        cursor: grab;
        position: relative;
    }
    .home-product-item:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        transform: translateY(-1px);
    }
    .home-product-item.sortable-ghost {
        opacity: 0.35;
        background: #f1f5f9;
        border: 2px dashed #94a3b8;
    }
    .home-product-item.sortable-chosen {
        cursor: grabbing;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        border-color: #c8102e;
    }

    .drag-handle {
        color: #94a3b8;
        cursor: grab;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }
    .drag-handle:hover {
        color: #0f172a;
    }

    .order-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
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
        padding: 2px;
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
        background: #f8fafc;
    }

    .btn-reorder-nav {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        padding: 0;
        transition: all 0.15s ease;
    }
    .btn-reorder-nav:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .empty-section-placeholder {
        border: 2px dashed #e2e8f0;
        border-radius: 14px;
        padding: 40px 20px;
        text-align: center;
        background: #fcfdfe;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Executive Brand Header -->
    <div class="brand-header mb-4">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des Sections de l'Accueil</h1>
                <p class="brand-subtitle">Sélectionnez les produits et organisez leur ordre d'affichage en direct sur la page d'accueil</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-external-link-alt"></i> <span>Voir l'accueil</span>
            </a>
            <a href="{{ route('banners.index') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-sliders-h"></i> <span>Slider & Bannières</span>
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

    {{-- KPI Cards Row --}}
    <div class="row g-3 mb-4">
        @foreach($sectionsData as $sKey => $sData)
        <div class="col-12 col-md-4">
            <div class="stat-kpi-card h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">{{ $sData['badge'] }}</div>
                        <div class="stat-kpi-value text-dark" id="kpi-count-{{ $sKey }}">{{ $sData['count'] }} <span class="fs-6 fw-normal text-muted">produits</span></div>
                        <div class="stat-kpi-sub text-truncate" style="max-width: 220px;" title="{{ $sData['current_title'] }}">
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
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <!-- Tabs Header -->
        <div class="card-header bg-white border-bottom pt-3 pb-0 px-3 px-md-4">
            <ul class="nav nav-tabs home-section-tabs border-0 flex-nowrap overflow-x-auto" id="homeSectionsTab" role="tablist">
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
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button type="button" 
                                        class="btn btn-outline-danger fw-bold rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" 
                                        onclick="openAddProductModal('{{ $sKey }}')">
                                    <i class="fas fa-plus"></i> <span>Ajouter des produits</span>
                                </button>
                                <button type="submit" 
                                        class="btn btn-brand-primary fw-bold rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2" 
                                        id="btn-save-{{ $sKey }}">
                                    <i class="fas fa-save"></i> <span>Enregistrer l'ordre</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tip Banner -->
                        <div class="alert alert-light border d-flex align-items-center justify-content-between py-2 px-3 rounded-3 mb-3 small text-muted">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-arrows-up-down-left-right text-danger"></i>
                                <span><strong>Glissez-déposez</strong> les cartes pour changer l'ordre, ou utilisez les flèches <i class="fas fa-arrow-up"></i> <i class="fas fa-arrow-down"></i>. Le premier produit apparaîtra en tête de ligne.</span>
                            </div>
                            <span class="badge bg-white border text-dark fw-bold px-2 py-1"><span id="live-count-{{ $sKey }}">{{ $sData['count'] }}</span> sélectionné(s)</span>
                        </div>

                        <!-- Sortable Products List -->
                        <div class="home-products-sortable-list" id="sortable-list-{{ $sKey }}">
                            @forelse($sData['products'] as $idx => $p)
                                <div class="home-product-item" data-product-id="{{ $p->id }}" id="item-{{ $sKey }}-{{ $p->id }}">
                                    <input type="hidden" name="product_ids[]" value="{{ $p->id }}">

                                    <div class="drag-handle" title="Glisser pour réorganiser">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>

                                    <div class="order-badge item-order-num">#{{ $idx + 1 }}</div>

                                    <div class="item-thumbnail">
                                        <img src="{{ $p->thumbnail }}" alt="{{ $p->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    </div>

                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center gap-2 mb-0.5">
                                            <a href="{{ route('shop.show', $p->slug ?: $p->id) }}" target="_blank" class="fw-bold text-dark text-truncate text-decoration-none hover-red" style="font-size: 0.92rem;">
                                                {{ $p->name }}
                                            </a>
                                            @if($p->isInStock())
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">En stock</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill py-0.5 px-2" style="font-size: 0.68rem;">Rupture</span>
                                            @endif
                                        </div>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <span class="font-monospace text-secondary">SKU: {{ $p->sku ?: '—' }}</span>
                                            <span>•</span>
                                            <span>{{ $p->productCategory?->name ?: 'Non catégorisé' }}</span>
                                            @if($p->brand)
                                                <span>•</span>
                                                <span class="fw-semibold text-dark">{{ $p->brand->name }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-end me-2">
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $p->formatted_price }}</div>
                                    </div>

                                    <!-- Reorder Arrows -->
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn-reorder-nav" onclick="moveProductItem('{{ $sKey }}', this, -1)" title="Monter">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button type="button" class="btn-reorder-nav" onclick="moveProductItem('{{ $sKey }}', this, 1)" title="Descendre">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </div>

                                    <!-- Remove Button -->
                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeProductItem('{{ $sKey }}', '{{ $p->id }}')" title="Retirer de la section">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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

<!-- Modal: Add Products to Section -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-3 bg-danger-subtle text-danger p-2 d-inline-flex"><i class="fas fa-plus"></i></span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="addProductModalLabel">Ajouter des produits à la section</h5>
                        <p class="text-muted small mb-0">Recherchez et ajoutez des équipements en un clic</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search Box -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" 
                           id="modalProductSearchInput" 
                           class="form-control border-start-0" 
                           placeholder="Rechercher par nom de caméra, optique, accessoire ou code SKU..." 
                           oninput="debounceModalSearch()">
                    <button class="btn btn-outline-secondary" type="button" onclick="loadModalProducts()">Rechercher</button>
                </div>

                <!-- Live Results Container -->
                <div id="modalSearchResults" class="border rounded-3 overflow-hidden" style="max-height: 420px; overflow-y: auto;">
                    <div class="p-4 text-center text-muted">
                        <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                        Chargement des produits du catalogue...
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2.5">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Terminé</button>
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

    function debounceModalSearch() {
        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(loadModalProducts, 280);
    }

    function getSelectedIds(sectionKey) {
        const list = document.getElementById(`sortable-list-${sectionKey}`);
        if (!list) return [];
        return Array.from(list.querySelectorAll('input[name="product_ids[]"]')).map(inp => inp.value);
    }

    function loadModalProducts() {
        const query = document.getElementById('modalProductSearchInput').value || '';
        const resultsBox = document.getElementById('modalSearchResults');
        resultsBox.innerHTML = `
            <div class="p-4 text-center text-muted">
                <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                Recherche dans le catalogue...
            </div>
        `;

        const existingIds = getSelectedIds(activeSectionKey);

        const url = new URL('{{ route('home-sections.search') }}', window.location.origin);
        url.searchParams.set('q', query);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.json())
            .then(products => {
                if (!products || products.length === 0) {
                    resultsBox.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-search me-1"></i> Aucun produit trouvé correspondant à votre recherche.
                        </div>
                    `;
                    return;
                }

                let html = '';
                products.forEach(p => {
                    const isAlreadyAdded = existingIds.includes(String(p.id));
                    html += `
                        <div class="search-result-item" id="modal-item-${p.id}">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="item-thumbnail" style="width: 44px; height: 44px;">
                                    <img src="${p.thumbnail}" alt="${p.name}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                </div>
                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;">${p.name}</div>
                                    <div class="small text-muted d-flex align-items-center gap-2">
                                        <span>SKU: ${p.sku}</span>
                                        <span>•</span>
                                        <span>${p.category}</span>
                                        <span>•</span>
                                        <span class="fw-bold text-dark">${p.price}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                ${isAlreadyAdded 
                                    ? `<button type="button" class="btn btn-sm btn-light border text-muted rounded-pill px-3" disabled><i class="fas fa-check me-1"></i> Ajouté</button>` 
                                    : `<button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="addProductToSection(${JSON.stringify(p).replace(/"/g, '&quot;')})"><i class="fas fa-plus me-1"></i> Ajouter</button>`
                                }
                            </div>
                        </div>
                    `;
                });

                resultsBox.innerHTML = html;
            })
            .catch(() => {
                resultsBox.innerHTML = `
                    <div class="p-3 text-center text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i> Erreur lors du chargement des produits.
                    </div>
                `;
            });
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

                <div class="drag-handle" title="Glisser pour réorganiser">
                    <i class="fas fa-grip-vertical"></i>
                </div>

                <div class="order-badge item-order-num">#${newOrder}</div>

                <div class="item-thumbnail">
                    <img src="${product.thumbnail}" alt="${product.name}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                </div>

                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-0.5">
                        <a href="/shop/${product.id}" target="_blank" class="fw-bold text-dark text-truncate text-decoration-none hover-red" style="font-size: 0.92rem;">
                            ${product.name}
                        </a>
                        ${stockBadge}
                    </div>
                    <div class="d-flex align-items-center gap-2 small text-muted">
                        <span class="font-monospace text-secondary">SKU: ${product.sku}</span>
                        <span>•</span>
                        <span>${product.category}</span>
                        ${product.brand ? `<span>•</span><span class="fw-semibold text-dark">${product.brand}</span>` : ''}
                    </div>
                </div>

                <div class="text-end me-2">
                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">${product.price}</div>
                </div>

                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn-reorder-nav" onclick="moveProductItem('${activeSectionKey}', this, -1)" title="Monter">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button type="button" class="btn-reorder-nav" onclick="moveProductItem('${activeSectionKey}', this, 1)" title="Descendre">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>

                <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="removeProductItem('${activeSectionKey}', '${product.id}')" title="Retirer de la section">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

        list.insertAdjacentHTML('beforeend', itemHtml);
        updateOrderNumbers(activeSectionKey);

        // Update modal button state to "Ajouté"
        const modalItem = document.getElementById(`modal-item-${product.id}`);
        if (modalItem) {
            const btnWrap = modalItem.querySelector('div:last-child');
            if (btnWrap) {
                btnWrap.innerHTML = `<button type="button" class="btn btn-sm btn-light border text-muted rounded-pill px-3" disabled><i class="fas fa-check me-1"></i> Ajouté</button>`;
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
            btn.classList.remove('btn-brand-primary');
            btn.classList.add('btn-success');

            setTimeout(() => {
                btn.innerHTML = origHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-brand-primary');
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

