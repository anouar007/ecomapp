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
                <span style="background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                    <i class="fas fa-sparkles"></i>
                </span>
                Studio Qualité Images 4K
            </h1>
            <p class="text-muted mb-0 small">Recherche automatique multi-sources (fabricants, distributeurs spécialisés, Amazon, B&H, web photo) et application de masters 4K Ultra-HD (1500px à 4000px).</p>
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
                    <span class="text-success small fw-bold text-uppercase">4K Ultra-HD (≥ 2000px)</span>
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
                    <span class="text-warning small fw-bold text-uppercase">À améliorer (&lt; 1200px)</span>
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
                <div style="width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 16px; border-bottom: 1px solid #f1f5f9; position: relative;">
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
                            <span class="font-monospace text-muted">{{ $product->sku }}</span>
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

                    <!-- Dual Action Buttons -->
                    <div class="d-flex gap-2">
                        <button type="button" 
                                onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')"
                                class="btn btn-sm flex-grow-1 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-xs"
                                style="background: #f8fafc; border: 1.5px solid #e2e8f0; color: #4338ca; transition: all 0.2s;"
                                onmouseenter="this.style.background='#4f46e5'; this.style.color='#ffffff'; this.style.borderColor='#4f46e5';"
                                onmouseleave="this.style.background='#f8fafc'; this.style.color='#4338ca'; this.style.borderColor='#e2e8f0';">
                            <i class="fas fa-sparkles text-warning"></i>
                            <span>Trouver Image 4K</span>
                        </button>
                        <button type="button" 
                                onclick="quickAutoUpgrade({{ $product->id }}, this)"
                                title="Mise à niveau 4K automatique rapide"
                                class="btn btn-sm rounded-pill fw-semibold shadow-xs"
                                style="background: #eef2ff; border: 1.5px solid #c7d2fe; color: #4338ca; width: 38px; display: inline-flex; align-items: center; justify-content: center;"
                                onmouseenter="this.style.background='#4338ca'; this.style.color='#ffffff';"
                                onmouseleave="this.style.background='#eef2ff'; this.style.color='#4338ca';">
                            <i class="fas fa-bolt text-warning"></i>
                        </button>
                    </div>
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

<!-- ==============================================================
     MODAL STUDIO RECHERCHE 4K ULTRA-HD MULTI-SOURCES
     ============================================================== -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); z-index: 9999; overflow-y: auto; padding: 20px; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 100%; max-width: 1000px; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; margin: auto; border: 1px solid rgba(255, 255, 255, 0.2); display: flex; flex-direction: column; max-height: 90vh;">
        
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 46px; height: 46px; border-radius: 14px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 style="margin: 0; font-size: 19px; font-weight: 700; letter-spacing: -0.01em;">Studio Recherche 4K Ultra-HD</h4>
                        <span class="badge rounded-pill bg-success px-2 py-1" style="font-size: 11px;">Multi-Sources Web &amp; Fabricants</span>
                    </div>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #c7d2fe;" id="modal4kSubtitle">Sélectionnez la meilleure image studio en haute résolution</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 36px; height: 36px; border-radius: 10px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.2)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Current Image Bar & Mode Navigation Tabs -->
        <div style="padding: 14px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <img id="modalCurrentImg" src="" alt="Current" style="width: 44px; height: 44px; object-fit: contain; background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 2px;">
                <div>
                    <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">Image Actuelle</div>
                    <div id="modalCurrentRes" style="font-size: 13px; font-weight: 600; color: #334155;">-</div>
                </div>
            </div>

            <!-- Mode Switcher Tabs -->
            <div class="d-flex gap-2">
                <button type="button" id="tabBtnSearch" onclick="switchModalTab('search')" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold active-modal-tab" style="background: #4f46e5; color: white; border: none;">
                    <i class="fas fa-globe-americas me-1"></i> Recherche Web &amp; Fabricants
                </button>
                <button type="button" id="tabBtnUrl" onclick="switchModalTab('url')" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold" style="background: #e2e8f0; color: #475569; border: none;">
                    <i class="fas fa-link me-1"></i> URL Directe
                </button>
                <button type="button" id="tabBtnUpload" onclick="switchModalTab('upload')" class="btn btn-sm rounded-pill px-3 py-1 fw-semibold" style="background: #e2e8f0; color: #475569; border: none;">
                    <i class="fas fa-cloud-upload-alt me-1"></i> Téléverser Fichier
                </button>
            </div>
        </div>

        <!-- ================= PANEL 1: WEB & OFFICIAL CANDIDATES ================= -->
        <div id="panelSearch" style="display: flex; flex-direction: column; overflow: hidden; flex: 1;">
            <!-- Search Input & Filters -->
            <div style="padding: 14px 24px; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="position: relative; flex-grow: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                        <input type="text" id="query4k" class="form-control" placeholder="Rechercher nom, modèle ou mots-clés du produit..." style="border-radius: 12px; font-size: 13px; padding: 10px 14px 10px 38px; border-color: #cbd5e1;" onkeydown="if(event.key === 'Enter') trigger4kSearch()">
                    </div>
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; border-radius: 12px; padding: 10px 22px; font-size: 13px; font-weight: 600; white-space: nowrap; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">
                        <i class="fas fa-bolt me-1 text-warning"></i> Explorer le Web
                    </button>
                </div>

                <!-- Filter Chips -->
                <div class="d-flex align-items-center gap-2 flex-wrap" id="candidateFilterBar" style="display: none !important;">
                    <span class="text-muted small fw-bold me-1" style="font-size: 11px;">Filtrer :</span>
                    <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-chip active" data-filter="all" onclick="filterCandidates('all', this)" style="font-size: 11px; background: #0f172a; color: white;">
                        Tous (<span id="countAll">0</span>)
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-chip" data-filter="4k" onclick="filterCandidates('4k', this)" style="font-size: 11px; background: #dcfce7; color: #15803d;">
                        ✨ 4K Ultra-HD (<span id="count4k">0</span>)
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-chip" data-filter="fhd" onclick="filterCandidates('fhd', this)" style="font-size: 11px; background: #e0f2fe; color: #0369a1;">
                        📷 Full HD (<span id="countFhd">0</span>)
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-chip" data-filter="official" onclick="filterCandidates('official', this)" style="font-size: 11px; background: #fef3c7; color: #b45309;">
                        🏛️ Fabricants (<span id="countOfficial">0</span>)
                    </button>
                    <button type="button" class="btn btn-xs rounded-pill px-2 py-1 filter-chip" data-filter="web" onclick="filterCandidates('web', this)" style="font-size: 11px; background: #f1f5f9; color: #475569;">
                        🌐 Distributeurs Web (<span id="countWeb">0</span>)
                    </button>
                </div>
            </div>

            <!-- Candidates Grid Content Area -->
            <div style="padding: 20px 24px; overflow-y: auto; flex: 1;">
                <div id="loading4k" style="display: none; text-align: center; padding: 60px 20px;">
                    <div class="spinner-border text-primary" style="width: 3.2rem; height: 3.2rem;" role="status"></div>
                    <p style="margin-top: 18px; font-weight: 700; color: #1e1b4b; font-size: 16px;">Exploration des masters 4K Ultra-HD...</p>
                    <p style="font-size: 13px; color: #64748b; max-width: 500px; margin: 0 auto;">Recherche en cours auprès des catalogues officiels et des distributeurs mondiaux (Amazon, B&amp;H, Adorama, boutiques cinéma)...</p>
                </div>
                
                <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px;">
                    <!-- Candidate cards injected dynamically -->
                </div>
            </div>
        </div>

        <!-- ================= PANEL 2: DIRECT IMAGE URL ================= -->
        <div id="panelUrl" style="display: none; padding: 28px; overflow-y: auto; flex: 1;">
            <div style="max-width: 650px; margin: 0 auto; background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 16px; padding: 26px;">
                <div class="text-center mb-3">
                    <span style="width: 50px; height: 50px; border-radius: 14px; background: #e0e7ff; color: #4338ca; display: inline-flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fas fa-link"></i>
                    </span>
                    <h5 class="fw-bold text-dark mt-2 mb-1">Coller une URL d'image directe</h5>
                    <p class="text-muted small mb-0">Vous avez trouvé une image 4K sur un site fournisseur ou un CDN ? Collez son lien direct ici pour la prévisualiser et l'appliquer.</p>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-dark">URL de l'image (JPG, PNG, WEBP)</label>
                    <div class="input-group">
                        <input type="url" id="directImageUrlInput" class="form-control" placeholder="https://exemple.com/images/produit-4k.jpg" style="border-radius: 10px 0 0 10px; font-size: 13px;">
                        <button type="button" onclick="previewDirectUrl()" class="btn btn-dark" style="border-radius: 0 10px 10px 0; font-size: 13px; font-weight: 600;">
                            <i class="fas fa-eye me-1"></i> Prévisualiser
                        </button>
                    </div>
                </div>

                <div id="directUrlPreviewContainer" style="display: none; background: white; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; margin-top: 18px;">
                    <div style="height: 240px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc; border-radius: 8px; margin-bottom: 14px;">
                        <img id="directUrlImgPreview" src="" alt="Direct Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-success" id="directUrlBadge">4K Ultra-HD</span>
                            <span class="text-muted small ms-2" id="directUrlDims">-</span>
                        </div>
                    </div>
                    <button type="button" id="btnApplyDirectUrl" onclick="applyDirectUrl()" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                        <i class="fas fa-check-circle me-1"></i> Appliquer cette image au produit
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= PANEL 3: UPLOAD LOCAL FILE ================= -->
        <div id="panelUpload" style="display: none; padding: 28px; overflow-y: auto; flex: 1;">
            <div style="max-width: 650px; margin: 0 auto; background: #f8fafc; border: 2px dashed #818cf8; border-radius: 16px; padding: 32px; text-align: center;">
                <span style="width: 56px; height: 56px; border-radius: 16px; background: #ede9fe; color: #6d28d9; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 12px;">
                    <i class="fas fa-cloud-upload-alt"></i>
                </span>
                <h5 class="fw-bold text-dark mb-1">Téléverser une image haute résolution</h5>
                <p class="text-muted small mb-3">Glissez-déposez votre fichier ici ou cliquez pour parcourir vos dossiers (Jusqu'à 25 Mo, résolutions 4K acceptées).</p>

                <input type="file" id="localFileInput" accept="image/*" style="display: none;" onchange="handleFileSelected(this)">
                <button type="button" onclick="document.getElementById('localFileInput').click()" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold mb-3">
                    <i class="fas fa-folder-open me-1"></i> Parcourir mes fichiers
                </button>

                <div id="uploadPreviewContainer" style="display: none; background: white; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0; margin-top: 14px; text-align: left;">
                    <div style="height: 240px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc; border-radius: 8px; margin-bottom: 12px;">
                        <img id="uploadImgPreview" src="" alt="Upload Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-success" id="uploadBadge">4K Ultra-HD</span>
                            <span class="text-muted small ms-2" id="uploadDims">-</span>
                            <span class="text-muted small ms-2" id="uploadSize">-</span>
                        </div>
                    </div>
                    <button type="button" id="btnApplyUpload" onclick="uploadAndApplyFile()" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
                        <i class="fas fa-arrow-up-from-bracket me-1"></i> Téléverser &amp; Appliquer en Studio 4K
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; color: #64748b;">
                <i class="fas fa-shield-check text-success me-1"></i> Qualité studio vérifiée &bull; Zéro filigrane &bull; Résolution garantie 4K UHD / Full HD
            </div>
            <button type="button" onclick="close4kImageModal()" class="btn btn-secondary btn-sm rounded-pill px-4">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- ==============================================================
     LIGHTBOX PREVIEW MODAL (FULL RESOLUTION ZOOM)
     ============================================================== -->
<div id="modalLightbox" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.92); backdrop-filter: blur(10px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div style="position: relative; max-width: 95vw; max-height: 95vh; display: flex; flex-direction: column; align-items: center;">
        <button type="button" onclick="closeLightbox()" style="position: absolute; top: -45px; right: 0; background: rgba(255,255,255,0.2); border: none; color: white; width: 38px; height: 38px; border-radius: 50%; font-size: 18px; cursor: pointer;">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightboxImg" src="" alt="Full Zoom" style="max-width: 90vw; max-height: 80vh; object-fit: contain; border-radius: 8px; box-shadow: 0 20px 60px rgba(0,0,0,0.8); background: #ffffff;">
        <div style="margin-top: 14px; display: flex; align-items: center; gap: 14px;">
            <span class="badge bg-success px-3 py-2" id="lightboxDims" style="font-size: 13px;">4K Ultra-HD</span>
            <button type="button" id="btnLightboxApply" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none;">
                <i class="fas fa-check-circle me-1"></i> Choisir cette image 4K
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let current4kProductId = null;
    let selectedHubProducts = [];
    let currentCandidates = [];
    let activeFilter = 'all';
    let selectedUploadFile = null;

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

    function switchModalTab(tab) {
        document.getElementById('panelSearch').style.display = tab === 'search' ? 'flex' : 'none';
        document.getElementById('panelUrl').style.display = tab === 'url' ? 'block' : 'none';
        document.getElementById('panelUpload').style.display = tab === 'upload' ? 'block' : 'none';

        const btnSearch = document.getElementById('tabBtnSearch');
        const btnUrl = document.getElementById('tabBtnUrl');
        const btnUpload = document.getElementById('tabBtnUpload');

        [btnSearch, btnUrl, btnUpload].forEach(b => {
            b.style.background = '#e2e8f0';
            b.style.color = '#475569';
        });

        if (tab === 'search') {
            btnSearch.style.background = '#4f46e5';
            btnSearch.style.color = '#ffffff';
        } else if (tab === 'url') {
            btnUrl.style.background = '#4f46e5';
            btnUrl.style.color = '#ffffff';
        } else if (tab === 'upload') {
            btnUpload.style.background = '#4f46e5';
            btnUpload.style.color = '#ffffff';
        }
    }

    function open4kFinderModal(productId, productName, currentImg, currentRes) {
        current4kProductId = productId;
        document.getElementById('modal4kSubtitle').innerText = productName;
        document.getElementById('modalCurrentImg').src = currentImg;
        document.getElementById('modalCurrentRes').innerText = currentRes || 'Résolution standard';
        document.getElementById('query4k').value = productName;
        document.getElementById('modal4k').style.display = 'flex';
        document.body.style.overflow = 'hidden';

        switchModalTab('search');
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
        const filterBar = document.getElementById('candidateFilterBar');

        loading.style.display = 'block';
        grid.innerHTML = '';
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exploration...';
        filterBar.style.setProperty('display', 'none', 'important');

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
            btn.innerHTML = '<i class="fas fa-bolt me-1 text-warning"></i> Explorer le Web';

            if (!data.candidates || data.candidates.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 48px; color: #64748b;">
                        <i class="fas fa-image" style="font-size: 40px; margin-bottom: 14px; color: #cbd5e1;"></i>
                        <p style="font-weight: 700; font-size: 16px;">Aucune image haute résolution trouvée pour "${query}"</p>
                        <p style="font-size: 13px;">Essayez de simplifier le nom (ex: "Sony A7 IV" ou "Godox V1") ou collez une URL directe via l'onglet ci-dessus.</p>
                    </div>
                `;
                return;
            }

            currentCandidates = data.candidates;
            renderCandidates();
        })
        .catch(err => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1 text-warning"></i> Explorer le Web';
            grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 24px; text-align: center;">Erreur de recherche : ${err.message}</div>`;
        });
    }

    function renderCandidates() {
        const grid = document.getElementById('grid4k');
        const filterBar = document.getElementById('candidateFilterBar');
        grid.innerHTML = '';

        let countAll = currentCandidates.length;
        let count4k = currentCandidates.filter(c => c.is_4k || (c.width >= 2000 || c.height >= 2000)).length;
        let countFhd = currentCandidates.filter(c => c.is_fhd || (c.width >= 1200 || c.height >= 1200)).length;
        let countOfficial = currentCandidates.filter(c => c.is_official).length;
        let countWeb = currentCandidates.filter(c => !c.is_official).length;

        document.getElementById('countAll').innerText = countAll;
        document.getElementById('count4k').innerText = count4k;
        document.getElementById('countFhd').innerText = countFhd;
        document.getElementById('countOfficial').innerText = countOfficial;
        document.getElementById('countWeb').innerText = countWeb;

        filterBar.style.setProperty('display', 'flex', 'important');

        const filtered = currentCandidates.filter(c => {
            if (activeFilter === '4k') return c.is_4k || (c.width >= 2000 || c.height >= 2000);
            if (activeFilter === 'fhd') return c.is_fhd || (c.width >= 1200 || c.height >= 1200);
            if (activeFilter === 'official') return c.is_official;
            if (activeFilter === 'web') return !c.is_official;
            return true;
        });

        if (filtered.length === 0) {
            grid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 30px; color: #94a3b8;">Aucun résultat pour ce filtre spécifique.</div>`;
            return;
        }

        filtered.forEach((cand, idx) => {
            const card = document.createElement('div');
            card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.25s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
            card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 10px 25px rgba(99, 102, 241, 0.18)'; card.style.transform = 'translateY(-2px)'; };
            card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; card.style.transform = 'translateY(0)'; };

            const is4k = cand.width >= 2000 || cand.height >= 2000;
            const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #e0f2fe; color: #0369a1;";
            const sourcePillColor = cand.is_official ? "background: #fef3c7; color: #b45309;" : "background: rgba(15,23,42,0.75); color: #ffffff;";

            card.innerHTML = `
                <div style="position: relative; width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 12px; border-bottom: 1px solid #f1f5f9;">
                    <img src="${cand.thumbnail || cand.url}" alt="Candidate Preview" style="max-width: 100%; max-height: 100%; object-fit: contain; cursor: zoom-in;" onclick="openLightbox('${cand.url.replace(/'/g, "\\'")}', '${cand.width}×${cand.height} px')">
                    
                    <span style="position: absolute; top: 10px; left: 10px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                    </span>

                    <button type="button" onclick="openLightbox('${cand.url.replace(/'/g, "\\'")}', '${cand.width}×${cand.height} px')" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.85); border: 1px solid #cbd5e1; width: 28px; height: 28px; border-radius: 6px; font-size: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #334155;">
                        <i class="fas fa-search-plus"></i>
                    </button>

                    <span style="position: absolute; bottom: 8px; left: 10px; ${sourcePillColor} font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 4px; max-width: 80%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                        ${cand.source}
                    </span>
                </div>

                <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; flex-grow: 1; justify-content: space-between;">
                    <div style="font-size: 11.5px; color: #475569; font-weight: 500; line-height: 1.3; max-height: 32px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                        ${cand.title || 'Master Packshot'}
                    </div>

                    <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 9px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.2);">
                        <i class="fas fa-check-circle"></i> Choisir cette image 4K
                    </button>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    function filterCandidates(type, chipBtn) {
        activeFilter = type;
        document.querySelectorAll('.filter-chip').forEach(c => {
            c.style.background = '#f1f5f9';
            c.style.color = '#475569';
        });
        chipBtn.style.background = '#0f172a';
        chipBtn.style.color = '#ffffff';
        renderCandidates();
    }

    function openLightbox(url, dims) {
        document.getElementById('lightboxImg').src = url;
        document.getElementById('lightboxDims').innerText = dims || 'Haute Définition';
        document.getElementById('btnLightboxApply').onclick = function() {
            closeLightbox();
            applySelected4kImage(url);
        };
        document.getElementById('modalLightbox').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('modalLightbox').style.display = 'none';
    }

    function previewDirectUrl() {
        const url = document.getElementById('directImageUrlInput').value.trim();
        if (!url) return;

        const img = document.getElementById('directUrlImgPreview');
        const container = document.getElementById('directUrlPreviewContainer');
        const badge = document.getElementById('directUrlBadge');
        const dims = document.getElementById('directUrlDims');

        img.onload = function() {
            container.style.display = 'block';
            const w = this.naturalWidth;
            const h = this.naturalHeight;
            dims.innerText = `${w}×${h} px`;
            if (w >= 2000 || h >= 2000) {
                badge.innerText = '4K Ultra-HD';
                badge.className = 'badge bg-success';
            } else if (w >= 1200 || h >= 1200) {
                badge.innerText = 'Full HD';
                badge.className = 'badge bg-primary';
            } else {
                badge.innerText = 'Standard';
                badge.className = 'badge bg-secondary';
            }
        };
        img.onerror = function() {
            Swal.fire('Erreur', 'Impossible de charger l\'image depuis cette URL. Vérifiez le lien.', 'error');
        };
        img.src = url;
    }

    function applyDirectUrl() {
        const url = document.getElementById('directImageUrlInput').value.trim();
        if (!url) return;
        applySelected4kImage(url, document.getElementById('btnApplyDirectUrl'));
    }

    function handleFileSelected(input) {
        if (input.files && input.files[0]) {
            selectedUploadFile = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('uploadImgPreview');
                const container = document.getElementById('uploadPreviewContainer');
                const badge = document.getElementById('uploadBadge');
                const dims = document.getElementById('uploadDims');
                const size = document.getElementById('uploadSize');

                img.onload = function() {
                    container.style.display = 'block';
                    const w = this.naturalWidth;
                    const h = this.naturalHeight;
                    dims.innerText = `${w}×${h} px`;
                    size.innerText = (selectedUploadFile.size / (1024 * 1024)).toFixed(2) + ' Mo';

                    if (w >= 2000 || h >= 2000) {
                        badge.innerText = '4K Ultra-HD';
                        badge.className = 'badge bg-success';
                    } else if (w >= 1200 || h >= 1200) {
                        badge.innerText = 'Full HD';
                        badge.className = 'badge bg-primary';
                    } else {
                        badge.innerText = 'Standard';
                        badge.className = 'badge bg-secondary';
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(selectedUploadFile);
        }
    }

    function uploadAndApplyFile() {
        if (!selectedUploadFile || !current4kProductId) return;

        const btn = document.getElementById('btnApplyUpload');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléversement en cours...';

        const formData = new FormData();
        formData.append('image_file', selectedUploadFile);

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        fetch(`/products/${current4kProductId}/upload-4k-image`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origText;

            if (data.success) {
                updateCardInHub(current4kProductId, data);
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
                Swal.fire('Erreur', data.message || 'Impossible d\'appliquer l\'image', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origText;
            Swal.fire('Erreur', 'Erreur de téléversement : ' + err.message, 'error');
        });
    }

    function applySelected4kImage(imageUrl, button = null) {
        let origHtml = '';
        if (button) {
            origHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement 4K...';
        }

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
            if (button) {
                button.disabled = false;
                button.innerHTML = origHtml;
            }

            if (data.success) {
                updateCardInHub(current4kProductId, data);
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
            if (button) {
                button.disabled = false;
                button.innerHTML = origHtml;
            }
            Swal.fire('Erreur', 'Erreur de téléchargement : ' + err.message, 'error');
        });
    }

    function quickAutoUpgrade(productId, btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        fetch("{{ route('products.bulk-upgrade-4k') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ product_ids: [productId] })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origHtml;

            if (data.success && data.data && data.data.upgraded > 0) {
                const detail = data.data.details[0];
                const hubImg = document.getElementById(`hub-img-${productId}`);
                if (hubImg && detail.url) {
                    hubImg.src = detail.url + '?t=' + new Date().getTime();
                }
                const hubResText = document.getElementById(`hub-res-text-${productId}`);
                if (hubResText && detail.new_resolution) {
                    hubResText.innerText = detail.new_resolution + ' px';
                }
                const hubBadge = document.getElementById(`hub-badge-${productId}`);
                if (hubBadge) {
                    hubBadge.innerHTML = detail.quality === '4k' 
                        ? '<span class="badge bg-success shadow-sm px-2 py-1" style="font-size: 10px; font-weight: 700; letter-spacing: 0.05em;"><i class="fas fa-sparkles me-1 text-warning"></i>4K UHD</span>'
                        : '<span class="badge bg-primary shadow-sm px-2 py-1" style="font-size: 10px; font-weight: 700; letter-spacing: 0.05em;"><i class="fas fa-hd me-1"></i>FHD</span>';
                }

                Swal.fire({
                    title: 'Auto 4K Réussi !',
                    text: `Le produit a été optimisé en ${detail.new_resolution} (${detail.quality.toUpperCase()})`,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire('Info', 'Aucune image 4K automatique immédiate. Ouvrez le Studio 4K pour choisir parmi les distributeurs web.', 'info');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            Swal.fire('Erreur', err.message, 'error');
        });
    }

    function updateCardInHub(productId, data) {
        const hubImg = document.getElementById(`hub-img-${productId}`);
        if (hubImg) {
            hubImg.src = data.image_url + '?t=' + new Date().getTime();
        }

        const hubBadge = document.getElementById(`hub-badge-${productId}`);
        if (hubBadge && data.badge_html) {
            hubBadge.innerHTML = data.badge_html;
        }

        const hubResText = document.getElementById(`hub-res-text-${productId}`);
        if (hubResText && data.width) {
            hubResText.innerText = `${data.width}×${data.height} px`;
        }
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
