// POS Terminal JavaScript - Professional Edition
window.cart = [];
window.products = [];

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('POS Terminal initialized');
    setupEventListeners();
    loadAllProducts();
});

// Setup event listeners
function setupEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const categoryBtns = document.querySelectorAll('.category-btn');

    // Search input with debounce
    let searchTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => searchProducts(), 300);
    });

    // Category filters
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            searchProducts();
        });
    });

    // Enter key to search/add (barcode scanner support)
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // If products are filtered to one, auto-add it
            if (products.length === 1) {
                addToCart(products[0].id);
                this.value = '';
                loadAllProducts();
            } else {
                searchProducts();
            }
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function (e) {
        // Ignore if in input field (except function keys)
        const isInput = ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement.tagName);

        switch (e.key) {
            case 'F2':
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
                break;
            case 'F4':
                e.preventDefault();
                if (!isInput && cart.length > 0) {
                    clearCart();
                }
                break;
            case 'F9':
                e.preventDefault();
                if (!isInput && cart.length > 0) {
                    checkout();
                }
                break;
            case 'Escape':
                if (isInput) {
                    document.activeElement.blur();
                }
                break;
        }
    });

    // Discount input listeners
    const discountAmount = document.getElementById('discountAmount');
    const discountType = document.getElementById('discountType');

    if (discountAmount && discountType) {
        discountAmount.addEventListener('input', updateTotals);
        discountType.addEventListener('change', updateTotals);
    }
}

// Load all products initially
function loadAllProducts() {
    console.log('Loading all products...');
    fetch('/pos/search?query=', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Products loaded:', data);
            products = data;
            displayProducts(data);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            const grid = document.getElementById('productsGrid');
            if (grid) {
                grid.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 12px; color: #dc2626;"></i>
                    <p>Failed to load products</p>
                    <p style="font-size: 12px; color: #64748b;">${error.message}</p>
                </div>
            `;
            }
        });
}

// Search products
function searchProducts() {
    const query = document.getElementById('searchInput').value;
    const categoryId = document.querySelector('.category-btn.active')?.dataset.category;

    let url = `/pos/search?query=${encodeURIComponent(query)}`;
    if (categoryId) {
        url += `&category_id=${categoryId}`;
    }

    console.log('Searching:', url);
    fetch(url, {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Search results:', data);
            products = data;
            displayProducts(data);
        })
        .catch(error => {
            console.error('Error searching products:', error);
        });
}

// Display products in grid with visual indicators for cart items
function displayProducts(products) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    if (products.length === 0) {
        grid.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 12px;"></i>
                <p>No products found</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = products.map(product => {
        // Check if product is in cart
        const cartItem = cart.find(item => item.product_id === product.id);
        const inCartClass = cartItem ? 'in-cart' : '';
        const quantityBadge = cartItem ?
            `<div style="position: absolute; top: 8px; left: 8px; background: #10b981; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 700; z-index: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×${cartItem.quantity}</div>` : '';

        return `
            <div class="product-card ${inCartClass}" onclick="addToCart(${product.id})" style="position: relative;">
                ${quantityBadge}
                ${product.image
                ? `<img src="${product.image}" class="product-image" alt="${product.name}">`
                : `<div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                         <i class="fas fa-image" style="font-size: 32px;"></i>
                       </div>`
            }
                <div class="product-name">${product.name}</div>
                <div class="product-price">${formatCurrency(parseFloat(product.price))}</div>
                <div class="product-stock">Stock: ${product.stock}</div>
            </div>
        `;
    }).join('');
}

// Escape catalog text before placing it in HTML.
function escapePosHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[char]);
}

// Each variation has its own cart line and stock limit.
window.addToCart = async function (productId, variantId = null) {
    const product = products.find(p => p.id === productId);
    if (!product) return false;
    const variants = product.variants || [];
    if (variants.length && variantId === null) {
        const query = document.getElementById('searchInput').value.trim();
        const scanned = variants.find(v => v.sku && v.sku === query && v.stock > 0);
        if (scanned) {
            variantId = scanned.id;
        } else {
            const options = Object.fromEntries(variants.filter(v => v.stock > 0).map(v => [v.id,
                `${v.label} — ${formatCurrency(Number(v.price))} — Stock: ${v.stock}`]));
            if (!Object.keys(options).length) return false;
            const result = await Swal.fire({
                titleText: product.name,
                input: 'select', inputOptions: options, inputPlaceholder: 'Choose a variation',
                showCancelButton: true, confirmButtonText: 'Add to cart',
                inputValidator: value => !value ? 'Choose a variation' : undefined,
                didOpen: () => {
                    const select = Swal.getInput();
                    const preview = document.createElement('img');
                    preview.alt = product.name;
                    preview.style.cssText = 'display:none;max-width:160px;max-height:160px;margin:12px auto;object-fit:contain';
                    select.insertAdjacentElement('afterend', preview);
                    select.addEventListener('change', () => {
                        const selected = variants.find(v => v.id === Number(select.value));
                        preview.style.display = selected ? 'block' : 'none';
                        if (selected) preview.src = selected.image;
                    });
                }
            });
            if (!result.isConfirmed) return false;
            variantId = Number(result.value);
        }
    }
    const variant = variants.find(v => v.id === variantId);
    if (variants.length && !variant) return false;
    const stock = variant ? variant.stock : product.stock;
    const name = variant ? `${product.name} (${variant.label})` : product.name;
    const key = `${productId}:${variantId ?? 'simple'}`;
    const existingItem = cart.find(item => item.key === key);
    if ((existingItem?.quantity || 0) >= stock) {
        showStockLimit(name, stock);
        return false;
    }
    if (existingItem) {
        existingItem.quantity++;
        existingItem.stock = stock;
    } else {
        cart.push({key, product_id: productId, variant_id: variantId,
            name, price: variant ? variant.price : product.price, quantity: 1, stock});
    }
    updateCart();
    return true;
};

window.removeFromCart = function (key) {
    cart = cart.filter(item => item.key !== key);
    updateCart();
};

window.updateQuantity = function (key, delta) {
    const item = cart.find(item => item.key === key);
    if (!item) return;
    const newQuantity = item.quantity + delta;
    if (newQuantity <= 0) {
        removeFromCart(key);
    } else if (newQuantity <= item.stock) {
        item.quantity = newQuantity;
        updateCart();
    } else {
        showStockLimit(item.name, item.stock);
    }
};

// Update cart display
function updateCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');

    // Update cart count
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-cart-plus" style="font-size: 48px; margin-bottom: 12px;"></i>
                <p>Cart is empty</p>
                <p style="font-size: 12px; color: #64748b;">Click on products to add them</p>
            </div>
        `;
        document.getElementById('checkoutBtn').disabled = true;
    } else {
        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${escapePosHtml(item.name)}</div>
                    <div class="cart-item-price">${formatCurrency(parseFloat(item.price))} × ${item.quantity} = ${formatCurrency(item.price * item.quantity)}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="updateQuantity('${item.key}', -1)" title="Decrease">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div class="qty-display">${item.quantity}</div>
                    <button class="qty-btn" onclick="updateQuantity('${item.key}', 1)" title="Increase">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="remove-btn" onclick="removeFromCart('${item.key}')" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
        document.getElementById('checkoutBtn').disabled = false;
    }

    updateTotals();

    // Refresh product display to update visual indicators
    if (products.length > 0) {
        displayProducts(products);
    }
}

// Update totals with discount support
function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Calculate discount
    let discount = 0;
    const discountAmountInput = document.getElementById('discountAmount');
    const discountTypeSelect = document.getElementById('discountType');
    const discountRow = document.getElementById('discountRow');
    const discountDisplay = document.getElementById('discountDisplay');

    if (discountAmountInput && discountTypeSelect) {
        const discountValue = parseFloat(discountAmountInput.value) || 0;
        const discountType = discountTypeSelect.value;

        if (discountValue > 0) {
            if (discountType === 'percent') {
                discount = (subtotal * Math.min(discountValue, 100)) / 100;
            } else {
                discount = Math.min(discountValue, subtotal);
            }

            // Show discount row
            if (discountRow) {
                discountRow.style.display = 'flex';
                discountDisplay.textContent = '-' + formatCurrency(discount);
            }
        } else {
            if (discountRow) {
                discountRow.style.display = 'none';
            }
        }
    }

    // Store discount for checkout
    window.currentDiscount = discount;

    const discountedSubtotal = subtotal - discount;
    const tax = discountedSubtotal * (window.currencyConfig.tax_rate || 0);
    const total = discountedSubtotal + tax;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(tax);
    document.getElementById('total').textContent = formatCurrency(total);

    // Update charge button text
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn && cart.length > 0) {
        checkoutBtn.textContent = `Charge ${formatCurrency(total)}`;
    }
}

/**
 * Clear all items from the cart
 * Shows confirmation dialog before clearing
 * Provides feedback for empty cart scenario
 */
window.clearCart = function () {
    // Check if cart is empty
    if (!cart || cart.length === 0) {
        showInfo('Empty Cart', 'Cart is already empty!');
        return;
    }

    // Get current cart count for confirmation message
    const itemCount = cart.length;
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

    // Show contextual confirmation message
    const confirmMessage = `<div style="text-align: left; margin-top: 10px;">
                              <p><strong>Items:</strong> ${itemCount} product(s)</p>
                              <p><strong>Total quantity:</strong> ${totalItems}</p>
                              <p style="margin-top: 12px; color: #ef4444;">This action cannot be undone.</p>
                            </div>`;

    // Request user confirmation
    confirmAction('Clear the cart?', confirmMessage, 'Yes, clear it', 'Cancel').then(confirmed => {
        if (confirmed) {
            // Clear the cart
            cart = [];

            // Update the UI
            updateCart();

            // Show success feedback
            console.log('✅ Cart cleared successfully');

            // Optional: Show subtle success message
            // You can uncomment this if you want visual feedback
            // showSuccess('Cart Cleared', 'All items have been removed from the cart');
        }
    }).catch(() => {
        // User canceled or error - log for debugging
        console.log('❌ Clear cart operation canceled by user');
    });
}

// Checkout
window.checkout = function () {
    const customerName = document.getElementById('customerName').value.trim();
    const customerEmail = document.getElementById('customerEmail').value.trim();
    const customerPhone = document.getElementById('customerPhone').value.trim();
    const paymentMethod = document.getElementById('paymentMethod').value;

    // Validate customer name
    if (!customerName || customerName.trim() === '') {
        showWarning('Required Field', 'Please enter customer name');
        document.getElementById('customerName').focus();
        return;
    }
    // Check if cart is empty
    if (cart.length === 0) {
        showWarning('Empty Cart', 'Please add items to cart before checkout');
        return;
    }

    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.disabled = true;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    const orderData = {
        customer_name: customerName,
        customer_email: customerEmail || null,
        customer_phone: customerPhone || null,
        payment_method: paymentMethod,
        items: cart.map(item => ({
            product_id: item.product_id,
            variant_id: item.variant_id,
            quantity: item.quantity,
            price: item.price
        }))
    };

    // Get CSRF token from meta tag or hidden input
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfInput = document.querySelector('input[name="_token"]');
    const csrfToken = csrfMeta ? csrfMeta.content : (csrfInput ? csrfInput.value : '');

    fetch('/pos/order', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(orderData)
    })
        .then(response => response.json())
        .then(data => {
            // Success
            if (data.success) {
                showOrderSuccess(data.order.order_number, data.order.total);

                // Reset form
                cart = [];
                updateCart();
                document.getElementById('customerName').value = '';
                document.getElementById('customerEmail').value = '';
                document.getElementById('customerPhone').value = '';
                document.getElementById('paymentMethod').value = 'cash';

                // Reload products to update stock
                loadAllProducts();
            } else {
                showError('Order Failed', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Processing Error', 'Failed to process order. Please try again.');
        })
        .finally(() => {
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i class="fas fa-check-circle"></i> Complete Order';
        });
}
