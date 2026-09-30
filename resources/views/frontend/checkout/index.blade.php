@extends('layouts.frontend')

@section('meta_title', 'Finaliser la Commande — ' . setting('app_name', 'LUMINA Cine & Optics'))

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Informations de livraison</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">NOM COMPLET</label>
                                    <input type="text" name="customer_name" class="form-control bg-light border-0 py-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">ADRESSE E-MAIL <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="email" name="customer_email" class="form-control bg-light border-0 py-2" placeholder="Pour la confirmation de commande">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">NUMÉRO DE TÉLÉPHONE</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control bg-light border-0 py-2" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               title="Enter 9 digits (e.g. 612345678)"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">ICE <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="text" name="ice" class="form-control bg-light border-0 py-2" placeholder="Identifiant Commun de l'Entreprise">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">ADRESSE</label>
                                    <input type="text" name="shipping_address" class="form-control bg-light border-0 py-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">
                                        VILLE DE LIVRAISON <span class="text-danger">*</span>
                                    </label>
                                    <div class="city-autocomplete-wrapper">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-danger"></i></span>
                                            <input type="text" 
                                                   name="shipping_city" 
                                                   id="shipping_city_input" 
                                                   class="form-control bg-light border-0 py-2 fw-semibold" 
                                                   placeholder="Tapez votre ville (ex. Casablanca, Agadir, فاس...)" 
                                                   autocomplete="off" 
                                                   required
                                                   value="{{ old('shipping_city') }}">
                                            <button class="btn btn-light border-0 text-muted px-3" type="button" id="city-clear-btn" style="display: none;" title="Effacer la ville">
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
                                    <label class="form-label small fw-bold text-muted">RÉGION <span class="text-muted fw-normal">(optionnel)</span></label>
                                    <input type="text" name="shipping_state" class="form-control bg-light border-0 py-2" placeholder="ex. Casablanca-Settat">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Paiement</h4>
                        <div class="alert alert-info border-0 rounded-3">
                            <i class="fas fa-info-circle me-2"></i> Pour cette boutique, vous pouvez régler par <strong>paiement à la livraison</strong> (espèces à la réception du colis) ou par virement bancaire.
                        </div>
                        <div class="form-check p-3 border rounded-3 bg-white mb-2">
                            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="cod" checked>
                            <label class="form-check-label fw-bold" for="cod">
                                Paiement à la livraison (Cash on Delivery)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white p-4 border-bottom-0">
                        <h5 class="fw-bold m-0">Récapitulatif de la commande</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($cart as $id => $details)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3 position-relative">
                                @php
                                    $chkImg = (isset($details['image']) && (str_starts_with($details['image'], 'http') || str_starts_with($details['image'], '/') || str_starts_with($details['image'], 'images/'))) 
                                        ? asset(ltrim($details['image'], '/')) 
                                        : (isset($details['image']) ? Storage::url($details['image']) : asset('images/camera/cat_cameras.jpg'));
                                @endphp
                                <img src="{{ $chkImg }}" alt="{{ $details['name'] }}" class="rounded-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary tiny-badge">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $details['name'] }}</h6>
                            </div>
                            <div class="fw-bold">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <hr class="my-4 opacity-10">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Sous-total</span>
                            <span class="fw-bold" id="checkout-subtotal" 
                                  data-subtotal="{{ $total }}"
                                  data-currency-symbol="{{ setting('currency_symbol', 'DH') }}"
                                  data-currency-decimals="{{ setting('currency_decimals', 2) }}"
                                  data-decimal-separator="{{ setting('decimal_separator', '.') }}"
                                  data-thousands-separator="{{ setting('thousands_separator', ' ') }}"
                                  data-currency-position="{{ setting('currency_position', 'after') }}">
                                {{ currency($total) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="text-muted">Frais de livraison</span>
                            <span class="fw-bold" id="checkout-shipping-cost">
                                <span class="text-muted small">Sélectionnez une ville</span>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-top">
                            <span class="h5 fw-bold mb-0">Total TTC</span>
                            <span class="h4 fw-bold text-danger mb-0" id="checkout-total">{{ currency($total) }}</span>
                        </div>

                        <button type="submit" form="checkout-form" id="checkout-submit-btn" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow">
                            Commander (<span id="btn-total-label">{{ currency($total) }}</span>)
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Retour au panier
                    </a>
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
            shippingEl.innerHTML = '<span class="text-muted small">Tapez votre ville</span>';
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
        showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> Ville sélectionnée : <strong>${escapeHtml(city.name_en)}</strong> (${escapeHtml(city.name_ar)}) — Livraison : ${formatCurrency(city.price)}`);
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
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> Ville reconnue : <strong>${escapeHtml(matched.name_en)}</strong> (${escapeHtml(matched.name_ar)}) — Livraison : ${formatCurrency(matched.price)}`);
        } else {
            // Unlisted city: apply standard 40 DH fee
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-check-circle text-primary me-1"></i> Ville validée : <strong>${escapeHtml(trimmed)}</strong> — Tarif standard : ${formatCurrency(40)}`);
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
                <span><i class="fas fa-list-ul me-1"></i> Villes proposées (${matches.length})</span>
                <span class="small fw-normal text-muted">Cliquez pour choisir</span>
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
                        <span>Continuer avec "<strong>${escapeHtml(query)}</strong>" (Ville personnalisée)</span>
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
                    <i class="fas fa-info-circle text-primary me-1"></i> Aucune ville suggérée pour "${escapeHtml(query)}"
                </div>
                <div class="small text-muted mb-2">
                    Continuez simplement la saisie : elle est <strong>automatiquement validée</strong> comme ville personnalisée (livraison : ${formatCurrency(40)}).
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                    <i class="fas fa-check me-1"></i> Valider "${escapeHtml(query)}"
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
            showStatus('matched', `<i class="fas fa-check-circle text-success me-1"></i> Ville reconnue : <strong>${escapeHtml(exact.name_en)}</strong> (${escapeHtml(exact.name_ar)}) — Livraison : ${formatCurrency(exact.price)}`);
        } else {
            // Live validation as manual custom city while typing
            updateSummary(40);
            showStatus('manual', `<i class="fas fa-pen text-primary me-1"></i> Saisie libre : <strong>${escapeHtml(raw.trim())}</strong> — Validée manuellement (Tarif standard : ${formatCurrency(40)})`);
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
