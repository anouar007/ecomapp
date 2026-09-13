// Pages, product cards, cart rows, tabs, and dialogs are authored in HTML.
// This file only manages interaction and persisted browser state.
const $ = selector => document.querySelector(selector);
const $$ = selector => [...document.querySelectorAll(selector)];
const defaultCart = [{ id: 0, qty: 1 }, { id: 1, qty: 1 }, { id: 2, qty: 2 }, { id: 3, qty: 1 }];

// Render every declarative icon after the deferred Lucide bundle has loaded.
window.lucide?.createIcons({ attrs: { 'stroke-width': 1.5 } });

function readStorage(key, fallback) {
  try {
    const value = JSON.parse(localStorage.getItem(key));
    return Array.isArray(value) ? value : fallback;
  } catch {
    return fallback;
  }
}

function writeStorage(key, value) {
  try { localStorage.setItem(key, JSON.stringify(value)); }
  catch { /* Keep controls usable when browser storage is unavailable. */ }
}

function validId(id) { return Number.isInteger(id) && id >= 0 && id < 8; }
let cart = readStorage('cart', defaultCart).filter(row => row && validId(row.id) && Number.isSafeInteger(row.qty) && row.qty > 0);
let favorites = new Set(readStorage('favorites', []).filter(validId));
let qty = 1;
let toastTimer;

function toast(message) {
  $('#toast').textContent = message;
  $('#toast').classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => $('#toast').classList.remove('show'), 2400);
}

function save() {
  writeStorage('cart', cart);
  updateCart();
}

function add(id, amount = 1) {
  if (!validId(id)) return;
  const row = cart.find(item => item.id === id);
  if (row) row.qty += amount;
  else cart.push({ id, qty: amount });
  save();
  toast('تمت إضافة المنتج إلى سلة التسوق');
}

function changeQty(id, amount) {
  const row = cart.find(item => item.id === id);
  if (!row) return;
  row.qty = Math.max(1, row.qty + amount);
  save();
}

function removeItem(id) {
  cart = cart.filter(row => row.id !== id);
  save();
}

function clearCart() {
  cart = [];
  save();
}

function updateCart() {
  $$('.cart-count').forEach(counter => { counter.textContent = cart.length; });
  if (!$('.cart-page')) return;
  let subtotal = 0;
  $$('[data-cart-id]').forEach(element => {
    const row = cart.find(item => item.id === Number(element.dataset.cartId));
    element.hidden = !row;
    if (!row) return;
    const total = Number(element.dataset.price) * row.qty;
    subtotal += total;
    element.querySelector('[data-cart-quantity]').textContent = row.qty;
    element.querySelector('[data-cart-total]').textContent = `${total} درهم`;
  });
  $('.cart-subtitle').textContent = `لديك ${cart.length} منتجات في سلة التسوق`;
  $('.summary-line b').textContent = `${subtotal} درهم`;
  $('.discount').textContent = `- ${subtotal ? 30 : 0} درهم`;
  $('.total strong').textContent = `${Math.max(0, subtotal - 30)} درهم`;
  $('.empty').hidden = cart.length > 0;
}

function updateFavorites() {
  $$('[data-favorite]').forEach(button => {
    const saved = favorites.has(Number(button.dataset.favorite));
    button.classList.toggle('saved', saved);
    button.setAttribute('aria-pressed', String(saved));
    const name = button.closest('.card')?.querySelector('.product-name').textContent || $('.details h1')?.textContent;
    button.setAttribute('aria-label', `${saved ? 'إزالة من المفضلة' : 'إضافة للمفضلة'}: ${name}`);
  });
}

function toggleFavorite(id) {
  favorites.has(id) ? favorites.delete(id) : favorites.add(id);
  writeStorage('favorites', [...favorites]);
  updateFavorites();
  toast(favorites.has(id) ? 'تمت الإضافة إلى المفضلة' : 'تم الحذف من المفضلة');
}

function setHomeProductFilter(filter, button) {
  $$('[data-home-filter]').forEach(tab => {
    tab.classList.toggle('active', tab === button);
    tab.setAttribute('aria-selected', String(tab === button));
  });
  $$('#home-product-track .card').forEach(card => {
    card.hidden = filter !== 'all' && card.dataset.label !== filter;
  });
  $('#home-product-carousel').scrollTo({ left: 0, behavior: 'smooth' });
}

function moveHomeCarousel(direction) {
  const carousel = $('#home-product-carousel');
  const item = carousel?.querySelector('.card:not([hidden])');
  if (!item) return;
  const step = item.getBoundingClientRect().width + 16;
  const max = carousel.scrollWidth - carousel.clientWidth;
  const rtl = getComputedStyle(carousel).direction === 'rtl';
  const position = Math.abs(carousel.scrollLeft);
  const next = position - direction * step;
  carousel.scrollTo({ left: (rtl ? -1 : 1) * (next > max ? 0 : next < 0 ? max : next), behavior: 'smooth' });
}

let searchTerm = new URLSearchParams(location.search).get('q')?.trim() || '';
function filterProducts() {
  const categories = $$('[name=category]:checked').map(input => input.value);
  const price = Number($('#price-range').value);
  let count = 0;
  $$('#product-grid .card').forEach(card => {
    const matches = (!categories.length || categories.includes(card.dataset.category)) &&
      Number(card.dataset.price) <= price && card.querySelector('.product-name').textContent.includes(searchTerm);
    card.hidden = !matches;
    if (matches) count++;
  });
  $('#result-count').textContent = `عرض ${count} منتجات`;
  $('#no-results').hidden = count > 0;
}

function resetFilters() {
  $$('.filters input').forEach(input => { input.checked = false; });
  $('#price-range').value = 500;
  $('#range-value').textContent = '500 درهم+';
  searchTerm = '';
  $('.tools input[name=q]').value = '';
  const url = new URL(location.href);
  url.searchParams.delete('q');
  url.searchParams.delete('category');
  history.replaceState(null, '', url);
  filterProducts();
}

function sortProducts(order) {
  const cards = $$('#product-grid .card');
  cards.sort((a, b) => order === 'low' ? a.dataset.price - b.dataset.price :
    order === 'high' ? b.dataset.price - a.dataset.price : a.dataset.productId - b.dataset.productId);
  cards.forEach(card => $('#product-grid').append(card));
}

function showModal(name) {
  $$('[data-dialog]').forEach(section => { section.hidden = section.dataset.dialog !== name; });
  $('#modal').showModal();
}

function account() { showModal('account'); }
function storyMore() { showModal('story'); }
function checkout() {
  if (!cart.length) return toast('سلة التسوق فارغة');
  showModal('checkout');
}

function zoomPhoto() {
  $('[data-dialog=zoom] img').src = $('#main-photo').src;
  showModal('zoom');
}

async function shareProduct() {
  try {
    await navigator.clipboard.writeText(location.href);
    toast('تم نسخ رابط المنتج');
  } catch {
    toast('يمكنك نسخ رابط المنتج من شريط العنوان');
  }
}

function tab(index, button) {
  $$('.tabs button').forEach(tab => { tab.classList.toggle('active', tab === button); });
  $$('[data-tab-panel]').forEach(panel => { panel.hidden = Number(panel.dataset.tabPanel) !== index; });
}

function sendContact(event) {
  event.preventDefault();
  toast('الإرسال غير متاح حالياً، يرجى التواصل عبر الهاتف أو البريد الإلكتروني');
}

document.addEventListener('click', event => {
  const button = event.target.closest('.sizes button');
  if (!button) return;
  button.parentElement.querySelectorAll('button').forEach(item => {
    item.classList.toggle('selected', item === button);
  });
});

// Keep old bookmarked product URLs working; all site links use static pages.
const legacyId = new URLSearchParams(location.search).get('id');
if (location.pathname.endsWith('/product.html') && /^[1-7]$/.test(legacyId || '')) {
  location.replace(`product-${legacyId}.html`);
}

if ($('#product-grid')) {
  const category = new URLSearchParams(location.search).get('category');
  $$('[name=category]').forEach(input => { input.checked = input.value === category; });
  $('.tools input[name=q]').value = searchTerm;
  $('#price-range').addEventListener('input', event => {
    $('#range-value').textContent = `${event.target.value} درهم`;
  });
  filterProducts();
}

window.addEventListener('storage', event => {
  if (event.key === 'cart' || event.key === null) {
    cart = readStorage('cart', defaultCart).filter(row => row && validId(row.id) && Number.isSafeInteger(row.qty) && row.qty > 0);
    updateCart();
  }
  if (event.key === 'favorites' || event.key === null) {
    favorites = new Set(readStorage('favorites', []).filter(validId));
    updateFavorites();
  }
});

updateCart();
updateFavorites();
