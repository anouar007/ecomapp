@extends('layouts.app')

@section('title', 'Studio Qualité 4K - Gestion des Images')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                <a href="{{ route('products.index') }}" class="text-decoration-none text-muted">Produits</a>
                <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                <span class="text-dark fw-semibold">Studio Qualité 4K</span>
            </div>
            <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <span style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-sparkles"></i>
                </span>
                Studio Qualité Images 4K
            </h1>
            <p class="text-muted mb-0 small">Auditez la résolution de vos produits et appliquez les meilleurs masters studio 4K Ultra-HD (2000px - 4000px) sans compression.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Retour aux produits
            </a>
            <button type="button" onclick="bulkUpgradeAllNeedsUpgrade()" class="btn btn-primary rounded-pill px-4 py-2 btn-sm fw-semibold shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                <i class="fas fa-bolt me-1 text-warning"></i> Optimiser la sélection vers 4K
            </button>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Total Produits</span>
                    <span class="p-2 rounded-3" style="background: #f1f5f9; color: #475569;">
                        <i class="fas fa-boxes"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-dark mb-1">{{ $qualityStats['total'] }}</div>
                <div class="text-muted small">Catalogue complet</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(145deg, #ffffff, #f0fdf4); border: 1px solid #bbf7d0 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-success small fw-bold text-uppercase">4K Ultra-HD (>= 2000px)</span>
                    <span class="p-2 rounded-3" style="background: #dcfce7; color: #15803d;">
                        <i class="fas fa-sparkles"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-success mb-1">{{ $qualityStats['four_k'] }}</div>
                @php
                    $pct4k = $qualityStats['total'] > 0 ? round(($qualityStats['four_k'] / $qualityStats['total']) * 100, 1) : 0;
                @endphp
                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-success" style="width: {{ $pct4k }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 11px;">
                    <span>Couverture</span>
                    <span class="fw-bold text-success">{{ $pct4k }}%</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-primary small fw-bold text-uppercase">Full HD (1200-1999px)</span>
                    <span class="p-2 rounded-3" style="background: #e0f2fe; color: #0369a1;">
                        <i class="fas fa-hd"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-primary mb-1">{{ $qualityStats['fhd'] }}</div>
                @php
                    $pctFhd = $qualityStats['total'] > 0 ? round(($qualityStats['fhd'] / $qualityStats['total']) * 100, 1) : 0;
                @endphp
                <div class="progress" style="height: 6px; border-radius: 4px; background: #e2e8f0;">
                    <div class="progress-bar bg-primary" style="width: {{ $pctFhd }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 11px;">
                    <span>Couverture</span>
                    <span class="fw-bold text-primary">{{ $pctFhd }}%</span>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(145deg, #ffffff, #fffbeb); border: 1px solid #fde68a !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-warning small fw-bold text-uppercase">À améliorer (< 1200px)</span>
                    <span class="p-2 rounded-3" style="background: #fef3c7; color: #b45309;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                </div>
                <div class="h2 fw-bold text-warning mb-1">{{ $qualityStats['needs_upgrade'] }}</div>
                <div class="text-muted small">Images SD ou placeholders</div>
            </div>
        </div>
    </div>

    <!-- Filters & Tabs -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-3" style="background: #ffffff;">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Quality Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a href="{{ route('products.image-quality', array_merge(request()->except('quality', 'page'), [])) }}" 
                       class="nav-link rounded-pill px-3 py-1 small fw-semibold {{ !request('quality') ? 'active bg-dark' : 'text-muted bg-light' }}">
                        Tous ({{ $qualityStats['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.image-quality', array_merge(request()->except('quality', 'page'), ['quality' => 'needs_upgrade'])) }}" 
                       class="nav-link rounded-pill px-3 py-1 small fw-semibold {{ request('quality') == 'needs_upgrade' ? 'active bg-warning text-dark' : 'text-muted bg-light' }}">
                        ⚠️ À améliorer ({{ $qualityStats['needs_upgrade'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.image-quality', array_merge(request()->except('quality', 'page'), ['quality' => '4k'])) }}" 
                       class="nav-link rounded-pill px-3 py-1 small fw-semibold {{ request('quality') == '4k' ? 'active bg-success' : 'text-muted bg-light' }}">
                        ✨ 4K Ultra-HD ({{ $qualityStats['four_k'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.image-quality', array_merge(request()->except('quality', 'page'), ['quality' => 'fhd'])) }}" 
                       class="nav-link rounded-pill px-3 py-1 small fw-semibold {{ request('quality') == 'fhd' ? 'active bg-primary' : 'text-muted bg-light' }}">
                        📷 Full HD ({{ $qualityStats['fhd'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.image-quality', array_merge(request()->except('quality', 'page'), ['quality' => 'missing'])) }}" 
                       class="nav-link rounded-pill px-3 py-1 small fw-semibold {{ request('quality') == 'missing' ? 'active bg-danger' : 'text-muted bg-light' }}">
                        ❌ Placeholder / Sans image ({{ $qualityStats['missing'] }})
                    </a>
                </li>
            </ul>

            <!-- Search & Category Filter Form -->
            <form method="GET" action="{{ route('products.image-quality') }}" class="d-flex align-items-center gap-2 flex-grow-1 justify-content-end" style="max-width: 480px;">
                @if(request('quality'))
                    <input type="hidden" name="quality" value="{{ request('quality') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-pill px-3" placeholder="Rechercher par nom...">
                <select name="category" class="form-select form-select-sm rounded-pill w-auto" onchange="this.form.submit()">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Select All Toolbar -->
    <div class="d-flex align-items-center justify-content-between mb-3 px-2">
        <div class="form-check d-flex align-items-center gap-2">
            <input class="form-check-input" type="checkbox" id="selectAllCards" onchange="toggleSelectAllCards(this)">
            <label class="form-check-label small fw-semibold text-muted" for="selectAllCards">
                Tout sélectionner sur cette page (<span id="selectedCountText">0</span> sélectionné(s))
            </label>
        </div>
        <div class="text-muted small">
            Affichage de <strong>{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</strong> sur <strong>{{ $products->total() }}</strong> produits
        </div>
    </div>

    <!-- Product Grid -->
    <div class="row g-3 mb-4">
        @forelse($products as $product)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3" id="hub-col-{{ $product->id }}">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden product-hub-card" 
                 style="background: #ffffff; transition: transform 0.2s, box-shadow 0.2s;">
                
                <!-- Checkbox -->
                <div class="position-absolute top-0 start-0 m-2 z-2">
                    <input type="checkbox" class="form-check-input hub-checkbox shadow-sm" value="{{ $product->id }}" onchange="updateHubSelection()">
                </div>

                <!-- Resolution Badge -->
                <div class="position-absolute top-0 end-0 m-2 z-2" id="hub-badge-{{ $product->id }}">
                    {!! $product->image_quality_badge !!}
                </div>

                <!-- Image Frame -->
                <div style="width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 16px; border-bottom: 1px solid #f1f5f9;">
                    <img id="hub-img-{{ $product->id }}" 
                         src="{{ $product->thumbnail }}" 
                         alt="{{ $product->name }}" 
                         style="max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.3s;"
                         onerror="this.src='{{ asset('images/camera/cat_cameras.jpg') }}'">
                </div>

                <!-- Card Body -->
                <div class="card-body p-3 d-flex flex-direction-column flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-1" style="font-size: 11px;">
                            <span>{{ $product->category_name ?? 'Sans catégorie' }}</span>
                            <span class="font-monospace">{{ $product->sku }}</span>
                        </div>
                        <h6 class="fw-bold text-dark text-truncate mb-2" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h6>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-bold text-primary">{{ currency($product->price) }}</span>
                            <span class="text-muted small" id="hub-res-text-{{ $product->id }}" style="font-size: 11px;">
                                @if($product->image_width)
                                    {{ $product->image_width }}×{{ $product->image_height }} px
                                @else
                                    Standard
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="button" 
                            onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')"
                            class="btn btn-sm w-100 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-xs"
                            style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #4338ca; transition: all 0.2s;"
                            onmouseenter="this.style.background='#4f46e5'; this.style.color='#ffffff'; this.style.borderColor='#4f46e5';"
                            onmouseleave="this.style.background='#f8fafc'; this.style.color='#4338ca'; this.style.borderColor='#e2e8f0';">
                        <i class="fas fa-sparkles text-warning"></i>
                        <span>Trouver Image 4K</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center text-muted">
            <i class="fas fa-images fa-3x mb-3 text-secondary"></i>
            <h5>Aucun produit trouvé pour ce filtre</h5>
            <p class="small">Modifiez vos critères de recherche ou réinitialisez les filtres.</p>
            <a href="{{ route('products.image-quality') }}" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
                Réinitialiser les filtres
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

<!-- Modal 4K Image Finder -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); z-index: 9999; overflow-y: auto; padding: 20px; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 100%; max-width: 900px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: auto; border: 1px solid rgba(255, 255, 255, 0.2);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.01em;">Studio Recherche 4K Ultra-HD</h4>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #c7d2fe;" id="modal4kSubtitle">Sélectionnez la meilleure image studio en haute résolution</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 34px; height: 34px; border-radius: 8px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Current Image & Search Bar -->
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img id="modalCurrentImg" src="" alt="Current" style="width: 46px; height: 46px; object-fit: contain; background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 2px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">Image actuelle</div>
                        <div id="modalCurrentRes" style="font-size: 13px; font-weight: 600; color: #334155;">-</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-grow: 1; max-width: 500px;">
                    <input type="text" id="query4k" class="form-control" placeholder="Rechercher par nom de produit..." style="border-radius: 10px; font-size: 13px; padding: 9px 14px;">
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-bolt me-1"></i> Rechercher
                    </button>
                </div>
            </div>
        </div>

        <!-- Candidates Grid -->
        <div style="padding: 24px; max-height: 520px; overflow-y: auto;">
            <div id="loading4k" style="display: none; text-align: center; padding: 50px 20px;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p style="margin-top: 16px; font-weight: 600; color: #475569; font-size: 15px;">Exploration des masters 4K Ultra-HD...</p>
                <p style="font-size: 13px; color: #94a3b8;">Extraction sans compression depuis les serveurs officiels</p>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
                <!-- Candidate cards injected dynamically -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; color: #64748b;">
                <i class="fas fa-check-shield text-success me-1"></i> Qualité studio vérifiée &bull; Zéro filigrane &bull; Résolution garantie
            </div>
            <button type="button" onclick="close4kImageModal()" class="btn btn-secondary btn-sm rounded-pill px-4">
                Fermer
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let current4kProductId = null;
    let selectedHubProducts = [];

    function updateHubSelection() {
        const checkboxes = document.querySelectorAll('.hub-checkbox:checked');
        selectedHubProducts = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('selectedCountText').innerText = selectedHubProducts.length;
    }

    function toggleSelectAllCards(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.hub-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateHubSelection();
    }

    function open4kFinderModal(productId, productName, currentImg, currentRes) {
        current4kProductId = productId;
        document.getElementById('modal4kSubtitle').innerText = productName;
        document.getElementById('modalCurrentImg').src = currentImg;
        document.getElementById('modalCurrentRes').innerText = currentRes || 'Résolution standard';
        document.getElementById('query4k').value = productName;
        document.getElementById('modal4k').style.display = 'flex';
        document.body.style.overflow = 'hidden';

        trigger4kSearch();
    }

    function close4kImageModal() {
        document.getElementById('modal4k').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function trigger4kSearch() {
        if (!current4kProductId) return;

        const query = document.getElementById('query4k').value.trim();
        if (!query) return;

        const loading = document.getElementById('loading4k');
        const grid = document.getElementById('grid4k');
        const btn = document.getElementById('btnRun4kSearch');

        loading.style.display = 'block';
        grid.innerHTML = '';
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recherche...';

        fetch(`/products/${current4kProductId}/search-4k-images?q=` + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';

            if (!data.candidates || data.candidates.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-image" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                        <p style="font-weight: 600;">Aucune image studio 4K trouvée pour "${query}"</p>
                        <p style="font-size: 13px;">Essayez de simplifier le nom avec la marque et le numéro de modèle.</p>
                    </div>
                `;
                return;
            }

            data.candidates.forEach((cand) => {
                const card = document.createElement('div');
                card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
                card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.15)'; };
                card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; };

                const is4k = cand.width >= 2000;
                const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #f1f5f9; color: #475569;";

                card.innerHTML = `
                    <div style="position: relative; width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                        <img src="${cand.url}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <span style="position: absolute; top: 8px; left: 8px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                        </span>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.7); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                            ${cand.source}
                        </span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                        <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                            <i class="fas fa-check-circle"></i> Choisir cette image 4K
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';
            grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 20px; text-align: center;">Erreur de recherche : ${err.message}</div>`;
        });
    }

    function applySelected4kImage(imageUrl, button) {
        const origHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement 4K...';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        fetch(`/products/${current4kProductId}/apply-4k-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ image_url: imageUrl })
        })
        .then(res => res.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = origHtml;

            if (data.success) {
                // Update product card live in Hub
                const hubImg = document.getElementById(`hub-img-${current4kProductId}`);
                if (hubImg) {
                    hubImg.src = data.image_url + '?t=' + new Date().getTime();
                }

                const hubBadge = document.getElementById(`hub-badge-${current4kProductId}`);
                if (hubBadge && data.badge_html) {
                    hubBadge.innerHTML = data.badge_html;
                }

                const hubResText = document.getElementById(`hub-res-text-${current4kProductId}`);
                if (hubResText && data.width) {
                    hubResText.innerText = `${data.width}×${data.height} px`;
                }

                close4kImageModal();

                Swal.fire({
                    title: 'Image 4K Appliquée !',
                    text: `Résolution : ${data.width}×${data.height} pixels (${data.quality.toUpperCase()})`,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire('Erreur', data.message || 'Impossible d\'appliquer l\'image 4K', 'error');
            }
        })
        .catch(err => {
            button.disabled = false;
            button.innerHTML = origHtml;
            Swal.fire('Erreur', 'Erreur de téléchargement : ' + err.message, 'error');
        });
    }

    function bulkUpgradeAllNeedsUpgrade() {
        if (!selectedHubProducts || selectedHubProducts.length === 0) {
            Swal.fire('Aucun produit sélectionné', 'Veuillez cocher au moins un produit à mettre à niveau en 4K Studio.', 'info');
            return;
        }

        Swal.fire({
            title: 'Mise à niveau 4K Studio',
            text: `Voulez-vous rechercher et appliquer automatiquement les meilleures images 4K Ultra-HD pour les ${selectedHubProducts.length} produit(s) sélectionné(s) ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🚀 Oui, mettre à niveau en 4K !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Traitement Studio 4K en cours...',
                    text: 'Téléchargement des masters 4K Ultra-HD sans compression...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                fetch("{{ route('products.bulk-upgrade-4k') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_ids: selectedHubProducts })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Mise à niveau terminée !',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Erreur', data.message || 'Une erreur est survenue.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Erreur', 'Une erreur inattendue est survenue : ' + err.message, 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection
