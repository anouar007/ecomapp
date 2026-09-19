/* Interactions for the Blade storefront. Cart state belongs to Laravel's session. */
const $ = selector => document.querySelector(selector);
const $$ = selector => [...document.querySelectorAll(selector)];
window.lucide?.createIcons({ attrs: { 'stroke-width': 1.5 } });
let toastTimer;
function toast(message) {
    const element = $('#toast');
    element.textContent = message;
    element.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => element.classList.remove('show'), 3500);
}
const money = value => `${Number(value).toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1')} درهم`;
async function request(url, method, payload = {}) {
    const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload),
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok || data.success === false) {
        throw new Error(Object.values(data.errors || {}).flat()[0] || data.message || 'تعذر إتمام العملية. يرجى المحاولة مرة أخرى.');
    }
    if (data.cartCount !== undefined) $$('.cart-count').forEach(el => el.textContent = data.cartCount);
    return data;
}
document.addEventListener('submit', async event => {
    const form = event.target.closest('[data-add-to-cart]');
    if (!form) return;
    event.preventDefault();
    const button = form.querySelector('[type="submit"]');
    if (button.disabled) return;
    button.disabled = true;
    form.setAttribute('aria-busy', 'true');
    try {
        const data = await request(form.action, 'POST', Object.fromEntries(new FormData(form)));
        toast(data.message);
    } catch (error) { toast(error.message); }
    finally { button.disabled = false; form.removeAttribute('aria-busy'); }
});
document.addEventListener('change', event => {
    if (!event.target.matches('.sizes input')) return;
    const form = event.target.closest('form');
    form.querySelectorAll('.size-option').forEach(option => option.classList.toggle('selected', !!option.querySelector(':checked')));
    form.querySelector('[data-product-price]').textContent = money(event.target.dataset.price);
    const imageScope = form.closest('[data-product-images]') || form;
    const productImage = imageScope.querySelector('[data-product-image]');
    if (productImage && event.target.dataset.image) {
        productImage.src = event.target.dataset.image;
        imageScope.querySelectorAll('.thumbnails button').forEach(button => {
            const selected = button.querySelector('img').src === productImage.src;
            button.classList.toggle('selected', selected);
            button.setAttribute('aria-pressed', String(selected));
        });
    }
    const quantity = form.querySelector('[name="quantity"]');
    if (quantity) {
        quantity.max = event.target.dataset.stock;
        quantity.value = Math.min(Number(quantity.value), Number(quantity.max));
    }
});
function adjustProductQuantity(amount) {
    const input = $('#product-quantity');
    input.value = Math.max(1, Math.min(Number(input.max), (Number(input.value) || 1) + amount));
}
// Serialize basket mutations so simultaneous clicks cannot overwrite session updates.
let cartBusy = false;
async function mutateCart(url, method, payload, onSuccess) {
    if (cartBusy) return;
    cartBusy = true;
    $$('.cart-row button, .cart-actions button').forEach(button => button.disabled = true);
    try { await request(url, method, payload); onSuccess(); updateTotals(); }
    catch (error) { toast(error.message); }
    finally {
        cartBusy = false;
        $$('.cart-row button, .cart-actions button').forEach(button => button.disabled = false);
        const clear = $('.cart-actions button');
        if (clear) clear.disabled = !$('.cart-row');
    }
}
function changeQty(button, amount) {
    const row = button.closest('.cart-row');
    const counter = row.querySelector('[data-cart-quantity]');
    const quantity = Math.max(1, Number(counter.textContent) + amount);
    if (quantity === Number(counter.textContent)) return;
    return mutateCart(document.body.dataset.cartUpdate, 'PATCH', { id: row.dataset.cartId, quantity }, () => {
        counter.textContent = quantity;
        row.querySelector('[data-cart-total]').textContent = money(Number(row.dataset.price) * quantity);
    });
}
function removeItem(button) {
    const row = button.closest('.cart-row');
    return mutateCart(document.body.dataset.cartRemove, 'DELETE', { id: row.dataset.cartId }, () => row.remove());
}
function clearCart() {
    return mutateCart(document.body.dataset.cartClear, 'DELETE', {}, () => $$('.cart-row').forEach(row => row.remove()));
}
function updateTotals() {
    const rows = $$('.cart-row');
    const total = rows.reduce((sum, row) => sum + Number(row.dataset.price) * Number(row.querySelector('[data-cart-quantity]').textContent), 0);
    $('[data-subtotal]').textContent = money(total);
    $('[data-total]').textContent = money(total);
    $('.cart-subtitle').textContent = `لديك ${rows.length} منتجات في سلة التسوق`;
    $('.empty').hidden = rows.length > 0;
    const checkout = $('.checkout');
    if (rows.length) { checkout.removeAttribute('aria-disabled'); checkout.removeAttribute('tabindex'); }
    else { checkout.setAttribute('aria-disabled', 'true'); checkout.tabIndex = -1; }
}
function checkCoupon() {
    toast($('#coupon').value.trim() ? 'لا يمكن تطبيق أكواد الخصم حالياً. تواصل معنا للمساعدة.' : 'أدخل كود الخصم أولاً');
}
document.addEventListener('click', event => {
    if (event.target.closest('[aria-disabled="true"]')) event.preventDefault();
});
async function toggleFavorite(id, button) {
    if (document.body.dataset.authenticated !== 'true') { location.href = document.body.dataset.loginUrl; return; }
    button.disabled = true;
    try {
        const data = await request(document.body.dataset.wishlistUrl, 'POST', { product_id: id });
        const saved = data.status === 'added';
        $$(`[data-favorite="${id}"]`).forEach(el => {
            el.classList.toggle('saved', saved);
            el.setAttribute('aria-pressed', String(saved));
            const name = el.closest('.card')?.querySelector('.product-name')?.textContent || $('.details h1')?.textContent;
            el.setAttribute('aria-label', `${saved ? 'إزالة من المفضلة' : 'إضافة للمفضلة'}: ${name}`);
        });
        toast(saved ? 'تمت الإضافة إلى المفضلة' : 'تم الحذف من المفضلة');
    } catch (error) { toast(error.message); }
    finally { button.disabled = false; }
}
let homeCarousel;
let homeProductCards = [];
function setupHomeCarousel() {
    const carousel = $('#home-product-carousel');
    if (!carousel) return;
    homeProductCards = $$('#home-product-track .card');
    homeProductCards.forEach((card, index) => card.dataset.initialIndex = index);
    if (!homeProductCards.length) {
        $$('[data-carousel-next], [data-carousel-prev]').forEach(button => button.disabled = true);
        return;
    }
    homeCarousel = new Swiper(carousel, {
        slidesPerView: 2,
        spaceBetween: 10,
        speed: matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 300,
        rewind: true,
        watchOverflow: true,
        breakpoints: {
            561: { spaceBetween: 16 },
            901: { slidesPerView: 4, spaceBetween: 16 },
        },
        navigation: {
            nextEl: '[data-carousel-next]',
            prevEl: '[data-carousel-prev]',
        },
        a11y: {
            prevSlideMessage: 'المنتجات السابقة',
            nextSlideMessage: 'المنتجات التالية',
            slideLabelMessage: '{{index}} / {{slidesLength}}',
        },
    });
}
function setHomeProductFilter(filter, button) {
    $$('[data-home-filter]').forEach(el => {
        el.classList.toggle('active', el === button);
        el.setAttribute('aria-selected', String(el === button));
    });
    if (!homeCarousel) return;
    const cards = homeProductCards.filter(card => filter !== 'new' || card.dataset.label === 'new');
    if (filter === 'bestseller') cards.sort((a, b) => Number(b.dataset.sales) - Number(a.dataset.sales));
    else cards.sort((a, b) => Number(a.dataset.initialIndex) - Number(b.dataset.initialIndex));
    $('#home-product-track').replaceChildren(...cards);
    homeCarousel.update();
    homeCarousel.slideTo(0, 0);
    $('#home-filter-empty').hidden = cards.length > 0;
}
function setCatalogView(list, button) {
    $('#product-grid').classList.toggle('list', list);
    $$('.view').forEach(el => { el.classList.toggle('selected', el === button); el.setAttribute('aria-pressed', String(el === button)); });
}
function toggleFilters(button) {
    const open = button.getAttribute('aria-expanded') !== 'true';
    button.setAttribute('aria-expanded', String(open));
    $('.filters').classList.toggle('expanded', open);
}
function showModal(name) {
    $$('[data-dialog]').forEach(el => el.hidden = el.dataset.dialog !== name);
    $('#modal').showModal();
}
function zoomPhoto() { $('[data-dialog="zoom"] img').src = $('#main-photo').src; showModal('zoom'); }
function selectPhoto(button) {
    $('#main-photo').src = button.querySelector('img').src;
    $$('.thumbnails button').forEach(el => { el.classList.toggle('selected', el === button); el.setAttribute('aria-pressed', String(el === button)); });
}
async function shareProduct() {
    try { await navigator.clipboard.writeText(location.href); toast('تم نسخ رابط المنتج'); }
    catch { toast('يمكنك نسخ رابط المنتج من شريط العنوان'); }
}
function tab(index, button) {
    $$('.tabs button').forEach(el => {
        el.classList.toggle('active', el === button);
        el.setAttribute('aria-selected', String(el === button));
        el.tabIndex = el === button ? 0 : -1;
    });
    $$('[data-tab-panel]').forEach(panel => panel.hidden = Number(panel.dataset.tabPanel) !== index);
}
document.addEventListener('keydown', event => {
    const tabs = event.target.closest('[role="tablist"]');
    if (!tabs || !['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) return;
    const buttons = [...tabs.querySelectorAll('[role="tab"]')];
    const index = buttons.indexOf(event.target);
    const next = event.key === 'Home' ? 0 : event.key === 'End' ? buttons.length - 1 : (index + (event.key === 'ArrowLeft' ? 1 : -1) + buttons.length) % buttons.length;
    event.preventDefault(); buttons[next].click(); buttons[next].focus();
});
function sendContact(event) {
    event.preventDefault();
    const form = event.target;
    const data = new FormData(form);
    const body = `${data.get('message')}\n\n${data.get('name')}\n${data.get('email')}\n${data.get('phone')}`;
    location.href = `mailto:${form.dataset.email}?subject=${encodeURIComponent(data.get('subject'))}&body=${encodeURIComponent(body)}`;
    toast('أكمل إرسال الرسالة من تطبيق البريد الإلكتروني.');
}
let mapScale = 1;
function zoomMap(amount) {
    mapScale = Math.max(1, Math.min(2, mapScale + amount * 0.25));
    $('.contact-map').style.backgroundSize = `${mapScale * 100}% auto`;
}
function setupPriceRange() {
    const range = $('#price-range');
    if (!range) return;
    const minInput = $('#min-price');
    const maxInput = $('#max-price');
    const ceiling = Number(maxInput.max) || 0;
    const update = changed => {
        if (changed === minInput && Number(minInput.value) > Number(maxInput.value)) minInput.value = maxInput.value;
        if (changed === maxInput && Number(maxInput.value) < Number(minInput.value)) maxInput.value = minInput.value;
        const min = Number(minInput.value);
        const max = Number(maxInput.value);
        const percent = value => ceiling ? `${value / ceiling * 100}%` : '0%';
        range.style.setProperty('--price-min', percent(min));
        range.style.setProperty('--price-max', percent(max));
        $('#min-price-value').textContent = money(min);
        $('#max-price-value').textContent = money(max);
        minInput.setAttribute('aria-valuetext', money(min));
        maxInput.setAttribute('aria-valuetext', money(max));
        minInput.style.zIndex = min > ceiling / 2 ? '2' : '1';
        maxInput.style.zIndex = min > ceiling / 2 ? '1' : '2';
    };
    minInput.addEventListener('input', () => update(minInput));
    maxInput.addEventListener('input', () => update(maxInput));
    update();
}
setupPriceRange();
setupHomeCarousel();
if (new URLSearchParams(location.search).has('page') && $('#product-tab-3')) tab(3, $('#product-tab-3'));
if ($('#toast').dataset.message) toast($('#toast').dataset.message);
JSON.parse($('#saved-favorites').textContent).forEach(id => {
    $$(`[data-favorite="${id}"]`).forEach(button => {
        button.classList.add('saved'); button.setAttribute('aria-pressed', 'true');
        button.setAttribute('aria-label', button.getAttribute('aria-label').replace('إضافة للمفضلة', 'إزالة من المفضلة'));
    });
});
document.documentElement.classList.add('js');
