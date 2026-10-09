@extends('layouts.frontend')

@section('meta_title', __('Checkout') . ' — ' . setting('app_name', 'Full Frame House'))

@section('content')
<style>
    /* ====================================================
       CHECKOUT PAGE STYLES — FULL FRAME HOUSE
       ==================================================== */
    .checkout-wrapper {
        min-height: 65vh;
        background-color: #f8fafc;
    }

    .checkout-input {
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 12px !important;
        padding: 12px 14px !important;
        font-size: 14.5px !important;
        color: #0f172a !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }
    .checkout-input:focus {
        border-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.08) !important;
        outline: none !important;
    }
    .checkout-input-group .input-group-text {
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 13.5px !important;
        border-radius: 12px 0 0 12px !important;
        border-right: 0 !important;
    }
    .checkout-input-group .checkout-input {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    html[dir="rtl"] .checkout-input-group .input-group-text {
        border-right: 1.5px solid #cbd5e1 !important;
        border-left: 0 !important;
        border-radius: 0 12px 12px 0 !important;
    }
    html[dir="rtl"] .checkout-input-group .checkout-input {
        border-radius: 12px 0 0 12px !important;
    }

    .payment-method-card {
        border: 2px solid #0f172a !important;
        border-radius: 14px !important;
        background: #fafbfc !important;
        transition: all 0.2s ease;
    }

    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* City suggestions dropdown */
    .city-autocomplete-wrapper {
        position: relative;
    }
    .city-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-top: 4px;
        max-height: 260px;
        overflow-y: auto;
    }
    .city-dropdown-header {
        padding: 8px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }
    .city-suggestion-item {
        padding: 10px 14px;
        font-size: 13.5px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s ease;
    }
    .city-suggestion-item:last-child {
        border-bottom: none;
    }
    .city-suggestion-item:hover,
    .city-suggestion-item.active {
        background: #f1f5f9;
    }
    .city-manual-choice {
        background: #fefce8;
    }
    .city-manual-choice:hover {
        background: #fef9c3;
    }
    .city-no-match-box {
        padding: 14px;
        font-size: 13px;
        cursor: pointer;
    }

    .city-status-pill {
        border-radius: 9999px;
        padding: 6px 14px;
        font-size: 12.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .city-status-pill.matched {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }
    .city-status-pill.manual {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
</style>

<div class="checkout-wrapper py-3 py-md-5">
    <div class="container">
        
        <!-- Breadcrumb Navigation -->
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">{{ __('My Cart') }}</a></li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ __('Checkout') }}</li>
                </ol>
            </nav>
        </div>

        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h1 class="fw-bold mb-0 font-heading fs-3 text-dark">
                {{ __('Finaliser la commande') }}
            </h1>
            <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-1.5 rounded-pill border shadow-2xs text-muted small">
                <i class="fas fa-lock text-success"></i>
                <span class="fw-semibold text-dark">{{ __('Paiement sécurisé') }}</span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                
                {{-- Delivery Information Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white" style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 font-heading text-dark">
                            <i class="fas fa-truck text-primary"></i>
                            <span>{{ __('Informations de livraison') }}</span>
                        </h5>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">{{ __('Nom complet') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control checkout-input" placeholder="ex. Karim Benali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">{{ __('Numéro de téléphone') }} <span class="text-danger">*</span></label>
                                    <div class="input-group checkout-input-group">
                                        <span class="input-group-text">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control checkout-input" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               title="Entrez 9 chiffres (ex. 612345678)" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">{{ __('Adresse e-mail') }} <span class="text-muted fw-normal">({{ __('optionnel') }})</span></label>
                                    <input type="email" name="customer_email" class="form-control checkout-input" placeholder="{{ __('Pour la confirmation de commande') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        {{ __('Ville de livraison') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="city-autocomplete-wrapper">
                                        <div class="input-group checkout-input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                            <input type="text" 
                                                   name="shipping_city" 
                                                   id="shipping_city_input" 
                                                   class="form-control checkout-input fw-semibold" 
                                                   placeholder="{{ __('Tapez votre ville (ex. Casablanca, Agadir...)') }}" 
                                                   autocomplete="off" 
                                                   required
                                                   value="{{ old('shipping_city') }}">
                                            <button class="btn btn-light border-0 text-muted px-3" type="button" id="city-clear-btn" style="display: none;" title="{{ __('Effacer la ville') }}">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>

                                        {{-- Dropdown Suggestion List --}}
                                        <div id="city-suggestions-dropdown" class="city-suggestions-dropdown" style="display: none;"></div>
                                    </div>

                                    {{-- Live status indicator --}}
                                    <div id="city-status-container" class="mt-2" style="display: none;"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-dark mb-1">{{ __('Région') }} <span class="text-muted fw-normal">({{ __('optionnel') }})</span></label>
                                    <input type="text" name="shipping_state" class="form-control checkout-input" placeholder="ex. Casablanca-Settat">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">{{ __('Adresse complète') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="shipping_address" class="form-control checkout-input" placeholder="{{ __('Quartier, rue, numéro, immeuble...') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">ICE <span class="text-muted fw-normal">({{ __('optionnel pour entreprises') }})</span></label>
                                    <input type="text" name="ice" class="form-control checkout-input" placeholder="{{ __('Identifiant Commun de l\'Entreprise') }}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Payment Method Card --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4" style="border: 1px solid #e2e8f0 !important;">
                    <div class="card-body p-3 p-md-4">
                        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 font-heading text-dark">
                            <i class="fas fa-credit-card text-primary"></i>
                            <span>{{ __('Mode de Paiement') }}</span>
                        </h5>
                        
                        <div class="payment-method-card p-3 p-md-4 d-flex align-items-start gap-3">
                            <div class="form-check m-0 p-0 d-flex align-items-center pt-1">
                                <input class="form-check-input m-0" type="radio" name="payment_method" id="cod" value="cod" checked style="width: 20px; height: 20px; cursor: pointer;">
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-check-label fw-bold d-flex align-items-center justify-content-between text-dark mb-1 flex-wrap gap-2" for="cod" style="cursor: pointer;">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="fas fa-hand-holding-dollar text-success"></i> 
                                        <span>{{ __('Paiement à la livraison') }} (Cash on Delivery)</span>
                                    </span>
                                    <span class="badge bg-success-subtle text-success small fw-bold px-2.5 py-1">{{ __('Espèces') }}</span>
                                </label>
                                <p class="text-muted small mb-2 lh-sm">
                                    {{ __('Réglez en espèces directement auprès du livreur à la réception de votre colis. (Virement bancaire également disponible sur demande).') }}
                                </p>
                                <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 11.5px;">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <span>{{ __('Vérification du matériel autorisée à la livraison') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top" style="top: 100px; z-index: 10; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white p-4 border-bottom-0 pb-0">
                        <h5 class="fw-bold m-0 font-heading d-flex align-items-center gap-2 text-dark">
                            <i class="fas fa-receipt text-primary"></i>
                            <span>{{ __('Order Summary') }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3 mb-3">
                            @foreach($cart as $id => $details)
                            @php
                                $chkImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/'))) 
                                    ? asset(ltrim($details['image'], '/')) 
                                    : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                            @endphp
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative flex-shrink-0">
                                    <img src="{{ $chkImg }}" alt="{{ $details['name'] }}" class="rounded-3 border object-fit-cover shadow-2xs" style="width: 62px; height: 62px; border-color: #f1f5f9 !important;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white border border-white" style="font-size: 11px;">
                                        {{ $details['quantity'] }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="fw-bold mb-0 text-dark small text-truncate-2 lh-sm">{{ $details['name'] }}</h6>
                                </div>
                                <div class="fw-bold text-dark text-nowrap small">{{ currency($details['price'] * $details['quantity']) }}</div>
                            </div>
                            @endforeach
                        </div>
                        
                        <hr class="my-3 opacity-10">
                        
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="fw-bold text-dark" id="checkout-subtotal" 
                                  data-subtotal="{{ $total }}"
                                  data-currency-symbol="{{ setting('currency_symbol', 'DH') }}"
                                  data-currency-decimals="{{ setting('currency_decimals', 2) }}"
                                  data-decimal-separator="{{ setting('decimal_separator', '.') }}"
                                  data-thousands-separator="{{ setting('thousands_separator', ' ') }}"
                                  data-currency-position="{{ setting('currency_position', 'after') }}">
                                {{ currency($total) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 text-muted small align-items-center">
                            <span>{{ __('Frais de livraison') }}</span>
                            <span class="fw-bold text-dark" id="checkout-shipping-cost">
                                <span class="text-muted small">{{ __('Tapez votre ville') }}</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-top mb-3">
                            <span class="h6 fw-bold mb-0 text-dark">{{ __('Total TTC') }}</span>
                            <span class="h4 fw-bold text-dark mb-0" id="checkout-total">{{ currency($total) }}</span>
                        </div>

                        <button type="submit" form="checkout-form" id="checkout-submit-btn" class="btn w-100 rounded-pill fw-bold py-3 shadow-md d-flex align-items-center justify-content-center gap-2" style="background-color: #0f172a !important; border-color: #0f172a !important; color: #ffffff !important;">
                            <span>{{ __('Confirmer la commande') }}</span> (<span id="btn-total-label">{{ currency($total) }}</span>)
                            <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                        </button>

                        <div class="text-center mt-3">
                            <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center gap-1 hover-text-dark">
                                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
                                <span>{{ __('Return to cart') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('shipping_city_input');
    const dropdown = document.getElementById('city-suggestions-dropdown');
    const clearBtn = document.getElementById('city-clear-btn');
    const statusContainer = document.getElementById('city-status-container');

    const subtotalEl = document.getElementById('checkout-subtotal');
    const shippingEl = document.getElementById('checkout-shipping-cost');
    const totalEl = document.getElementById('checkout-total');
    const btnTotalEl = document.getElementById('btn-total-label');

    if (!cityInput || !subtotalEl) return;

    const allCities = @json($cities);
    const subtotal = parseFloat(subtotalEl.getAttribute('data-subtotal')) || 0;
    const symbol = subtotalEl.getAttribute('data-currency-symbol') || 'DH';
    const decimals = parseInt(subtotalEl.getAttribute('data-currency-decimals')) || 2;
    const decSep = subtotalEl.getAttribute('data-decimal-separator') || '.';
    const thoSep = subtotalEl.getAttribute('data-thousands-separator') || ' ';
    const position = subtotalEl.getAttribute('data-currency-position') || 'after';

    let activeIndex = -1;
    let visibleItems = [];

    function formatCurrency(amount) {
        const fixed = amount.toFixed(decimals);
        const parts = fixed.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thoSep);
        const formatted = parts.join(decSep);
        return position === 'before' ? symbol + ' ' + formatted : formatted + ' ' + symbol;
    }

    function updateSummary(price) {
        if (price !== null && !isNaN(price)) {
            shippingEl.innerHTML = '<span class="text-danger fw-bold">' + formatCurrency(price) + '</span>';
            const grandTotal = subtotal + price;
            totalEl.textContent = formatCurrency(grandTotal);
            if (btnTotalEl) btnTotalEl.textContent = formatCurrency(grandTotal);
        } else {
            shippingEl.innerHTML = '<span class="text-muted small">{{ __('Type your city') }}</span>';
            totalEl.textContent = formatCurrency(subtotal);
            if (btnTotalEl) btnTotalEl.textContent = formatCurrency(subtotal);
        }
    }

    function normalizeText(str) {
        return (str || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function findMatch(query) {
        if (!query) return null;
        const normQ = normalizeText(query);
        const rawQ = query.trim().toLowerCase();

        return allCities.find(c => {
            const en = normalizeText(c.name_en);
            const ar = (c.name_ar || '').trim().toLowerCase();
            return en === normQ || ar === rawQ;
        });
    }

    function showStatus(type, html) {
        statusContainer.style.display = 'block';
        statusContainer.innerHTML = `<div class="city-status-pill ${type}">${html}</div>`;
    }

    function hideStatus() {
        statusContainer.style.display = 'none';
        statusContainer.innerHTML = '';
    }

    function selectCity(city) {
        cityInput.value = city.name_en;
        clearBtn.style.display = 'block';
        dropdown.style.display = 'none';
        updateSummary(parseFloat(city.price));
        showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Selected city:') }} <strong>${escapeHtml(city.name_en)}</strong> (${escapeHtml(city.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(city.price)}`);
    }

    function selectManual(cityName) {
        const trimmed = (cityName || '').trim();
        if (!trimmed) {
            hideStatus();
            updateSummary(null);
            return;
        }

        cityInput.value = trimmed;
        clearBtn.style.display = 'block';
        dropdown.style.display = 'none';

        // Check if manual entry matches a known city name (Latin or Arabic)
        const matched = findMatch(trimmed);
        if (matched) {
            updateSummary(parseFloat(matched.price));
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Recognized city:') }} <strong>${escapeHtml(matched.name_en)}</strong> (${escapeHtml(matched.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(matched.price)}`);
        } else {
            // Unlisted city: apply standard 40 DH fee
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-check-circle text-primary me-1"></i> {{ __('Validated city:') }} <strong>${escapeHtml(trimmed)}</strong> — {{ __('Standard rate:') }} ${formatCurrency(40)}`);
        }
    }

    function renderDropdown(matches, query, exact) {
        dropdown.innerHTML = '';
        visibleItems = [];
        activeIndex = -1;

        if (matches.length > 0) {
            const header = document.createElement('div');
            header.className = 'city-dropdown-header d-flex justify-content-between align-items-center';
            header.innerHTML = `
                <span><i class="fas fa-list-ul me-1"></i> {{ __('Suggested cities') }} (${matches.length})</span>
                <span class="small fw-normal text-muted">{{ __('Click to select') }}</span>
            `;
            dropdown.appendChild(header);

            matches.forEach((c) => {
                const item = document.createElement('div');
                const isExact = exact && (exact.name_en.toLowerCase() === c.name_en.toLowerCase() || (exact.name_ar && exact.name_ar === c.name_ar));
                item.className = 'city-suggestion-item' + (isExact ? ' active' : '');
                item.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark">${escapeHtml(c.name_en)}</span>
                            <span class="text-muted ms-1">(${escapeHtml(c.name_ar)})</span>
                        </div>
                        <span class="badge bg-danger-subtle text-danger fw-bold">${formatCurrency(c.price)}</span>
                    </div>
                `;
                item.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selectCity(c);
                });
                dropdown.appendChild(item);
                visibleItems.push({ type: 'city', data: c, element: item });
            });

            // Option to continue typing / keep typed text as custom city
            const manualItem = document.createElement('div');
            manualItem.className = 'city-suggestion-item city-manual-choice';
            manualItem.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-pen text-warning me-1"></i>
                        <span>{{ __('Continue with') }} "<strong>${escapeHtml(query)}</strong>" ({{ __('Custom city') }})</span>
                    </div>
                    <span class="badge bg-secondary-subtle text-dark fw-bold">${formatCurrency(40)}</span>
                </div>
            `;
            manualItem.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectManual(query);
            });
            dropdown.appendChild(manualItem);
            visibleItems.push({ type: 'manual', data: query, element: manualItem });
        } else {
            // No matching cities found in the database
            const noMatch = document.createElement('div');
            noMatch.className = 'city-no-match-box';
            noMatch.innerHTML = `
                <div class="fw-bold text-dark mb-1">
                    <i class="fas fa-info-circle text-primary me-1"></i> {{ __('No city suggested for') }} "${escapeHtml(query)}"
                </div>
                <div class="small text-muted mb-2">
                    {{ __('Continue typing: it is automatically validated as a custom city') }} ({{ __('Shipping:') }} ${formatCurrency(40)}).
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                    <i class="fas fa-check me-1"></i> {{ __('Validate') }} "${escapeHtml(query)}"
                </button>
            `;
            noMatch.addEventListener('mousedown', function(e) {
                e.preventDefault();
                selectManual(query);
            });
            dropdown.appendChild(noMatch);
            visibleItems.push({ type: 'manual', data: query, element: noMatch });
        }

        dropdown.style.display = 'block';
    }

    function onSearch() {
        const raw = cityInput.value;
        const normQ = normalizeText(raw);

        if (!normQ) {
            dropdown.style.display = 'none';
            clearBtn.style.display = 'none';
            hideStatus();
            updateSummary(null);
            return;
        }

        clearBtn.style.display = 'block';

        // Check if query is already an exact match
        const exact = findMatch(raw);
        if (exact) {
            updateSummary(parseFloat(exact.price));
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> {{ __('Recognized city:') }} <strong>${escapeHtml(exact.name_en)}</strong> (${escapeHtml(exact.name_ar)}) — {{ __('Shipping:') }} ${formatCurrency(exact.price)}`);
        } else {
            // Live validation as manual custom city while typing
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-pen text-primary me-1"></i> {{ __('Custom entry:') }} <strong>${escapeHtml(raw.trim())}</strong> ({{ __('Standard rate:') }} ${formatCurrency(40)})`);
        }

        // Search in allCities
        const rawTrim = raw.trim().toLowerCase();
        const matches = allCities.filter(c => {
            const en = normalizeText(c.name_en);
            const ar = (c.name_ar || '').trim().toLowerCase();
            return en.includes(normQ) || ar.includes(rawTrim);
        }).sort((a, b) => {
            const aStarts = normalizeText(a.name_en).startsWith(normQ);
            const bStarts = normalizeText(b.name_en).startsWith(normQ);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return 0;
        }).slice(0, 10);

        renderDropdown(matches, raw.trim(), exact);
    }

    cityInput.addEventListener('input', onSearch);
    cityInput.addEventListener('focus', function() {
        if (cityInput.value.trim()) {
            onSearch();
        }
    });

    cityInput.addEventListener('blur', function() {
        setTimeout(function() {
            dropdown.style.display = 'none';
            const val = cityInput.value.trim();
            if (val) {
                selectManual(val);
            } else {
                hideStatus();
                updateSummary(null);
            }
        }, 200);
    });

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        const wrapper = cityInput.closest('.city-autocomplete-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Keyboard navigation: ArrowDown, ArrowUp, Enter, Escape
    cityInput.addEventListener('keydown', function(e) {
        if (dropdown.style.display === 'none' || visibleItems.length === 0) {
            if (e.key === 'Enter') {
                const val = cityInput.value.trim();
                if (val) {
                    selectManual(val);
                }
            }
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % visibleItems.length;
            highlightActive();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + visibleItems.length) % visibleItems.length;
            highlightActive();
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeIndex < visibleItems.length) {
                const item = visibleItems[activeIndex];
                if (item.type === 'city') selectCity(item.data);
                else selectManual(item.data);
            } else {
                selectManual(cityInput.value);
            }
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });

    function highlightActive() {
        visibleItems.forEach((it, idx) => {
            if (idx === activeIndex) {
                it.element.classList.add('active');
                it.element.scrollIntoView({ block: 'nearest' });
            } else {
                it.element.classList.remove('active');
            }
        });
    }

    clearBtn.addEventListener('click', function() {
        cityInput.value = '';
        clearBtn.style.display = 'none';
        dropdown.style.display = 'none';
        hideStatus();
        updateSummary(null);
        cityInput.focus();
    });

    // Ensure manual city is validated on form submit
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function() {
            const val = cityInput.value.trim();
            if (val) {
                selectManual(val);
            }
        });
    }

    // If pre-filled on load (e.g. old form input)
    if (cityInput.value.trim()) {
        selectManual(cityInput.value.trim());
    }
});
</script>
@endpush
@endsection
