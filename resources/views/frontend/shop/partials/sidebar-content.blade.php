{{-- Price Range Card (Budget MAD) --}}
<div class="shop-filter-card mb-3">
    <div class="shop-filter-header d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
        <div class="shop-filter-title m-0 d-flex align-items-center gap-2 fw-bold text-dark text-uppercase fs-xs" style="letter-spacing: 0.5px;">
            <i class="fas fa-coins text-danger"></i>
            <span>{{ __('Budget (MAD)') }}</span>
        </div>
        <button type="button" class="sidebar-reset-pill" id="sidebarPriceResetBtn" onclick="if(window.removeFilter){window.removeFilter('price');}" title="{{ __('Effacer le filtre prix') }}" style="{{ !(request('min_price') || request('max_price')) ? 'display: none;' : '' }}">
            <i class="fas fa-times"></i>
            <span>{{ __('Effacer') }}</span>
        </button>
    </div>

    <form id="priceFilterForm">
        {{-- Dual-Range Interactive Slider --}}
        <div class="price-slider-wrap mb-3 px-1">
            <div class="price-slider-track-container position-relative">
                <div class="price-slider-track"></div>
                <div class="price-slider-fill" id="priceSliderFill"></div>
                <input type="range" class="price-range-slider" id="priceRangeMin" min="0" max="65000" step="250" value="{{ request('min_price', 0) ?: 0 }}" aria-label="{{ __('Budget minimum') }}">
                <input type="range" class="price-range-slider" id="priceRangeMax" min="0" max="65000" step="250" value="{{ request('max_price', 65000) ?: 65000 }}" aria-label="{{ __('Budget maximum') }}">
            </div>
            <div class="price-slider-scale d-flex justify-content-between align-items-center mt-1">
                <span class="price-scale-val">0 DH</span>
                <span class="price-scale-val">65k+ DH</span>
            </div>
        </div>

        {{-- Numeric Min & Max Input Containers --}}
        <div class="shop-price-inputs-row mb-3">
            <div class="shop-price-col">
                <div class="price-field-micro-label">{{ __('Minimum') }}</div>
                <label for="sidebarMinPrice" class="price-input-box mb-0">
                    <input type="number" id="sidebarMinPrice" name="min_price" class="price-number-input shop-price-input"
                           placeholder="0" value="{{ request('min_price') }}" min="0" max="65000" step="50" inputmode="numeric" autocomplete="off">
                </label>
            </div>

            <div class="price-range-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>

            <div class="shop-price-col">
                <div class="price-field-micro-label">{{ __('Maximum') }}</div>
                <label for="sidebarMaxPrice" class="price-input-box mb-0">
                    <input type="number" id="sidebarMaxPrice" name="max_price" class="price-number-input shop-price-input"
                           placeholder="Max" value="{{ request('max_price') }}" min="0" max="65000" step="50" inputmode="numeric" autocomplete="off">
                </label>
            </div>
        </div>

        {{-- Preset Budget Chips (2x2 Grid Calibrated to Real Catalog Distribution) --}}
        <div class="sidebar-budget-grid">
            <button type="button" class="sidebar-budget-chip" data-min="0" data-max="1000">&lt; 1 000 DH</button>
            <button type="button" class="sidebar-budget-chip" data-min="1000" data-max="5000">1k – 5k DH</button>
            <button type="button" class="sidebar-budget-chip" data-min="5000" data-max="15000">5k – 15k DH</button>
            <button type="button" class="sidebar-budget-chip" data-min="15000" data-max="">+ 15k DH</button>
        </div>
    </form>
</div>

{{-- Categories Card (Rayons Pro) --}}
@php
    $catIcons = $catIcons ?? [
        'cameras-hybrides'            => 'fa-camera',
        'camera'                      => 'fa-camera',
        'objectifs-optiques'          => 'fa-circle-notch',
        'objectifs'                   => 'fa-circle-notch',
        'stabilisateurs-gimbals'      => 'fa-video',
        'stabilisateurs'              => 'fa-video',
        'eclairage-studio'            => 'fa-lightbulb',
        'lumieres-materiel-de-studio' => 'fa-lightbulb',
        'audio-micros-sans-fil'       => 'fa-microphone',
        'son'                         => 'fa-microphone',
        'drones-cine'                 => 'fa-helicopter',
        'trepieds'                    => 'fa-expand-alt',
        'sacs-de-camera'              => 'fa-suitcase',
        'batterie-chargeur'           => 'fa-battery-full',
        'carte-memoire-lecteur'       => 'fa-sd-card',
        'materiel-de-podcast'         => 'fa-podcast',
        'filtres-nd-cpl-polarise'     => 'fa-adjust',
        'matte-box'                   => 'fa-film',
        'accessoires-insta360'        => 'fa-cube',
        'kit-de-nettoyage'            => 'fa-spray-can',
    ];
@endphp
<div class="shop-filter-card">
    <div class="shop-filter-header d-flex align-items-center justify-content-between mb-2.5 pb-2 border-bottom">
        <div class="shop-filter-title m-0 d-flex align-items-center gap-2 fw-bold text-dark text-uppercase fs-xs" style="letter-spacing: 0.5px;">
            <i class="fas fa-layer-group text-danger"></i>
            <span>{{ __('Rayons Pro') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="sidebar-reset-pill shop-cat-link" id="sidebarCatResetBtn" data-slug="" data-name="{{ __('Tout le catalogue') }}" title="{{ __('Voir tous les rayons') }}" style="{{ !request('category') ? 'display: none;' : '' }}">
                <i class="fas fa-times"></i>
                <span>{{ __('Effacer') }}</span>
            </button>
            <span class="badge rounded-pill bg-light text-muted border font-monospace small px-2 py-0.5">
                {{ $categories->count() }}
            </span>
        </div>
    </div>

    {{-- Live Filter Search Input for Rayons --}}
    <div class="cat-pro-search-box mb-2">
        <i class="fas fa-search cat-search-icon"></i>
        <input type="text" class="cat-pro-search-input" id="sidebarCatSearchInput" placeholder="{{ __('Filtrer les rayons...') }}" autocomplete="off">
    </div>

    {{-- Direct Visible Category List (No scroll, all categories shown) --}}
    <div class="shop-cat-list-wrapper" id="sidebarCatList">
        <div class="shop-cat-list">
            <button type="button" class="cat-mega-item shop-cat-link w-100 {{ !request('category') ? 'active' : '' }}" data-slug="" data-name="{{ __('Tout le catalogue') }}">
                <div class="d-flex align-items-center gap-2 me-1 flex-grow-1" style="min-width: 0;">
                    <span class="cat-slot-box">
                        <i class="fas fa-th-large cat-slot-icon" style="font-size: 1.28em !important; margin: auto !important;"></i>
                    </span>
                    <span class="cat-mega-name">{{ __('Tout le catalogue') }}</span>
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="badge cat-mega-count rounded-pill">{{ \App\Models\Product::where('status','active')->count() }}</span>
                    <i class="fas fa-check cat-item-check"></i>
                </div>
            </button>

            @foreach($categories as $cat)
            @php
                $cImg = $cat->image_url;
                $cIcon = !empty($cat->icon) ? (str_starts_with($cat->icon, 'fa') ? $cat->icon : 'fas fa-' . $cat->icon) : ($catIcons[$cat->slug] ?? 'fas fa-camera');
            @endphp
            <button type="button" class="cat-mega-item shop-cat-link w-100 {{ request('category') == $cat->slug ? 'active' : '' }}" data-slug="{{ $cat->slug }}" data-name="{{ $cat->name }}">
                <div class="d-flex align-items-center gap-2 me-1 flex-grow-1" style="min-width: 0;">
                    <span class="cat-slot-box">
                        @if($cImg)
                            <img src="{{ $cImg }}" alt="{{ $cat->name }}" width="30" height="30" class="cat-slot-media" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                            <i class="{{ $cIcon }} cat-slot-icon" style="display: none;"></i>
                        @else
                            <i class="{{ $cIcon }} cat-slot-icon"></i>
                        @endif
                    </span>
                    <span class="cat-mega-name">{{ $cat->name }}</span>
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="badge cat-mega-count rounded-pill">{{ $cat->products_count }}</span>
                    <i class="fas fa-check cat-item-check"></i>
                </div>
            </button>
            @endforeach
        </div>

        <div class="sidebar-cat-no-results text-center py-3 text-muted small" style="display: none;">
            <i class="fas fa-search me-1 opacity-50"></i>{{ __('Aucun rayon trouvé') }}
        </div>
    </div>
</div>


