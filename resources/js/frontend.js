/**
 * Coop Ait Oumdis — Frontend JS Bundle
 * Managed by Vite. Do not add CDN <script> tags in Blade templates.
 */

// ── Third-party imports ──────────────────────────────────────
import 'bootstrap';
import AOS from 'aos';
import Swiper from 'swiper/bundle';
import Swal from 'sweetalert2';

// ── Make globals available for inline Blade script blocks ────
window.AOS   = AOS;
window.Swiper = Swiper;
window.Swal  = Swal;

// ── AOS Init ─────────────────────────────────────────────────
AOS.init({ duration: 700, once: true, offset: 60, easing: 'ease-out-cubic' });

// ── Sticky Header ─────────────────────────────────────────────
window.addEventListener('scroll', () => {
    document.getElementById('mainHeader')?.classList.toggle('scrolled', window.scrollY > 20);
    const btn = document.getElementById('scrollTopBtn');
    if (btn) btn.classList.toggle('visible', window.scrollY > 200);
});

// ─────────────────────────────────────────────────────────────
// CART HELPERS
// These functions are called from inline onclick attributes in
// Blade templates, so they must be on window.
// ─────────────────────────────────────────────────────────────

/**
 * Sync ALL cart badge elements across the page.
 */
function syncCartBadges(count) {
    document.querySelectorAll('[data-cart-count]').forEach(el => {
        el.textContent = count;
    });
}

/**
 * Refresh the mini-cart drawer content via AJAX.
 */
window.refreshMiniCart = function () {
    const itemsEl  = document.getElementById('mini-cart-items');
    const footerEl = document.getElementById('mini-cart-footer');
    if (itemsEl)  fetch(window.__cartMiniUrl,       { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.text()).then(html => { itemsEl.innerHTML = html; });
    if (footerEl) fetch(window.__cartMiniFooterUrl,  { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.text()).then(html => { footerEl.innerHTML = html; });
};

/**
 * addToCart — fixed: receives event as first parameter.
 */
window.addToCart = function (productId, variantId = null, event) {
    const btn  = event?.currentTarget ?? event?.target;
    const orig = btn?.innerHTML;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

    fetch(`${window.__baseUrl}/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN':   window.__csrfToken,
            'Content-Type':   'application/json',
            'Accept':         'application/json',
        },
        body: JSON.stringify({ quantity: 1, variant_id: variantId }),
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: window.__i18n?.addedToCart ?? 'Added to cart!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
            });
            syncCartBadges(data.cartCount);
            // Pulse animation on header badge
            const badge = document.querySelector('[data-cart-count]');
            if (badge) {
                badge.classList.remove('cart-badge-pulse');
                void badge.offsetWidth; // reflow
                badge.classList.add('cart-badge-pulse');
            }
            window.refreshMiniCart();
        } else {
            Swal.fire({
                icon: 'warning',
                title: data.message ?? (window.__i18n?.outOfStock ?? 'Insufficient stock'),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
            });
            if (btn) { btn.disabled = false; btn.innerHTML = orig; }
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = orig; }
    });
};

/**
 * updateQty — called from mini-cart partial.
 */
window.updateQty = function (id, qty) {
    if (qty < 1) { window.removeItem(id); return; }
    fetch(window.__cartUpdateUrl, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':  window.__csrfToken,
            'Accept':        'application/json',
        },
        body: JSON.stringify({ id, quantity: qty }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            syncCartBadges(data.cartCount);
            window.refreshMiniCart();
        } else {
            Swal.fire({ icon: 'warning', title: data.message ?? 'Insufficient stock', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
        }
    });
};

/**
 * removeItem — called from mini-cart partial.
 */
window.removeItem = function (id) {
    Swal.fire({
        title: window.__i18n?.removeItem ?? 'Remove this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3BB878',
        cancelButtonText:  window.__i18n?.keep ?? 'Keep',
        confirmButtonText: window.__i18n?.remove ?? 'Remove',
    }).then(result => {
        if (result.isConfirmed) {
            fetch(window.__cartRemoveUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':  window.__csrfToken,
                    'Accept':        'application/json',
                },
                body: JSON.stringify({ id }),
            })
            .then(r => r.json())
            .then(data => {
                syncCartBadges(data.cartCount);
                window.refreshMiniCart();
            });
        }
    });
};

/**
 * selectProductSize — called from product card size pills.
 */
window.selectProductSize = function (productId, variantId, displayPrice, originalPrice, inStock) {
    // Update active pill UI
    document.querySelectorAll(`[id^="size-pill-${productId}-"]`)?.forEach(el => {
        el.classList.remove('border-green', 'bg-green-light', 'text-green', 'fw-bold');
        el.classList.add('border-light', 'bg-white', 'text-dark');
    });
    const activePill = document.getElementById(`size-pill-${productId}-${variantId}`);
    if (activePill) {
        activePill.classList.remove('border-light', 'bg-white', 'text-dark');
        activePill.classList.add('border-green', 'bg-green-light', 'text-green', 'fw-bold');
    }

    // Update price display
    const mainPrice   = document.querySelector(`.price-container-${productId} .main-price`);
    const origPriceEl = document.querySelector(`.price-container-${productId} .original-price`);
    if (mainPrice) mainPrice.textContent = displayPrice;
    if (origPriceEl) {
        if (originalPrice) {
            origPriceEl.textContent = originalPrice;
            origPriceEl.classList.remove('d-none');
        } else {
            origPriceEl.textContent = '';
            origPriceEl.classList.add('d-none');
        }
    }

    // Update the cart button's onclick with the new variant
    const btn = document.getElementById(`add-cart-btn-${productId}`);
    if (btn) btn.setAttribute('onclick', `addToCart(${productId}, ${variantId}, event)`);
};
