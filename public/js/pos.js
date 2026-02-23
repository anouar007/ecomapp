// POS Terminal JavaScript - Decimal Quantities Edition
window.cart = [];
window.products = [];

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('POS Terminal initialized (decimal qty support)');
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
        headers: { 'Accept': 'application/json' }
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
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
    if (categoryId) url += `&category_id=${categoryId}`;

    fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            products = data;
            displayProducts(data);
        })
        .catch(error => console.error('Error searching products:', error));
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
        const cartItem = cart.find(item => item.product_id === product.id);
        const inCartClass = cartItem ? 'in-cart' : '';
        const qtyLabel = cartItem ? formatQty(cartItem.quantity) : '';
        const quantityBadge = cartItem
            ? `<div style="position: absolute; top: 8px; left: 8px; background: #10b981; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 700; z-index: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">×${qtyLabel}</div>`
            : '';

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
                <div class="product-stock">Stock: ${formatQty(product.stock)}</div>
            </div>
        `;
    }).join('');
}

// ─── Add to cart ────────────────────────────────────
window.addToCart = function (productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.product_id === productId);

    if (existingItem) {
        // Default step is 1; bump by 1 each click
        const newQty = parseFloat((existingItem.quantity + 1).toFixed(4));
        if (newQty <= parseFloat(product.stock)) {
            existingItem.quantity = newQty;
        } else {
            showStockLimit(product.name, product.stock);
            return;
        }
    } else {
        cart.push({
            product_id: productId,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            stock: parseFloat(product.stock)
        });
    }

    updateCart();
};

// ─── Remove from cart ───────────────────────────────
window.removeFromCart = function (productId) {
    cart = cart.filter(item => item.product_id !== productId);
    updateCart();
};

// ─── Update quantity by a delta (supports decimals) ─
window.updateQuantity = function (productId, delta) {
    const item = cart.find(item => item.product_id === productId);
    if (!item) return;

    const newQuantity = parseFloat((item.quantity + delta).toFixed(4));

    if (newQuantity <= 0) {
        removeFromCart(productId);
    } else if (newQuantity <= item.stock) {
        item.quantity = newQuantity;
        updateCart();
    } else {
        showStockLimit(item.name, item.stock);
    }
};

// ─── Set quantity directly (from input field) ───────
window.setQuantity = function (productId, inputEl) {
    const item = cart.find(item => item.product_id === productId);
    if (!item) return;

    let val = parseFloat(inputEl.value);

    // Guard: must be a positive number
    if (isNaN(val) || val < 0) {
        inputEl.value = formatQty(item.quantity);
        return;
    }

    // Guard: cannot exceed stock (only for tracked stock > 0)
    if (item.stock > 0 && val > item.stock) {
        showStockLimit(item.name, item.stock);
        val = item.stock;
        inputEl.value = formatQty(val);
    }

    if (val === 0) {
        removeFromCart(productId);
        return;
    }

    item.quantity = parseFloat(val.toFixed(4));
    updateCart();
};

// ─── Format quantity for display (trim trailing zeros) ──
window.formatQty = function (qty) {
    const n = parseFloat(qty);
    if (isNaN(n)) return '0';
    // Show up to 3 decimal places, strip trailing zeros
    return n % 1 === 0 ? n.toString() : parseFloat(n.toFixed(3)).toString();
};

// ─── Update cart display ─────────────────────────────
function updateCart() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');

    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = formatQty(totalItems);
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
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${formatCurrency(parseFloat(item.price))} × ${formatQty(item.quantity)} = ${formatCurrency(item.price * item.quantity)}</div>
                </div>
                <div class="cart-item-controls">
                    <button class="qty-btn" onclick="updateQuantity(${item.product_id}, -1)" title="Decrease">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input
                        type="number"
                        class="qty-display qty-input"
                        value="${formatQty(item.quantity)}"
                        min="0"
                        step="0.001"
                        style="width:62px; text-align:center; border:1px solid #e2e8f0; border-radius:6px; padding:2px 4px; font-size:13px; font-weight:600;"
                        onchange="setQuantity(${item.product_id}, this)"
                        onblur="setQuantity(${item.product_id}, this)"
                    >
                    <button class="qty-btn" onclick="updateQuantity(${item.product_id}, 1)" title="Increase">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="remove-btn" onclick="removeFromCart(${item.product_id})" title="Remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
        document.getElementById('checkoutBtn').disabled = false;
    }

    updateTotals();

    if (products.length > 0) {
        displayProducts(products);
    }
}

// ─── Update totals with discount support ─────────────
function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

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
            if (discountRow) {
                discountRow.style.display = 'flex';
                discountDisplay.textContent = '-' + formatCurrency(discount);
            }
        } else {
            if (discountRow) discountRow.style.display = 'none';
        }
    }

    window.currentDiscount = discount;

    const discountedSubtotal = subtotal - discount;
    const tax = discountedSubtotal * (window.currencyConfig.tax_rate || 0);
    const total = discountedSubtotal + tax;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('tax').textContent = formatCurrency(tax);
    document.getElementById('total').textContent = formatCurrency(total);

    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn && cart.length > 0) {
        checkoutBtn.textContent = `Charge ${formatCurrency(total)}`;
    }
}

// ─── Clear cart ──────────────────────────────────────
window.clearCart = function () {
    if (!cart || cart.length === 0) {
        showInfo('Empty Cart', 'Cart is already empty!');
        return;
    }

    const itemCount = cart.length;
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

    const confirmMessage = `<div style="text-align: left; margin-top: 10px;">
                              <p><strong>Items:</strong> ${itemCount} product(s)</p>
                              <p><strong>Total quantity:</strong> ${formatQty(totalItems)}</p>
                              <p style="margin-top: 12px; color: #ef4444;">This action cannot be undone.</p>
                            </div>`;

    confirmAction('Clear the cart?', confirmMessage, 'Yes, clear it', 'Cancel').then(confirmed => {
        if (confirmed) {
            cart = [];
            updateCart();
            console.log('✅ Cart cleared successfully');
        }
    }).catch(() => {
        console.log('❌ Clear cart operation canceled by user');
    });
};

// ─── Checkout ────────────────────────────────────────
window.checkout = function () {
    const customerName = document.getElementById('customerName').value.trim();
    const customerEmail = document.getElementById('customerEmail').value.trim();
    const customerPhone = document.getElementById('customerPhone').value.trim();
    const paymentMethod = document.getElementById('paymentMethod').value;

    if (!customerName || customerName.trim() === '') {
        showWarning('Required Field', 'Please enter customer name');
        document.getElementById('customerName').focus();
        return;
    }
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
            quantity: item.quantity,   // already a float
            price: item.price
        }))
    };

    const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
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
            if (data.success) {
                showOrderSuccess(data.order.order_number, data.order.total);

                cart = [];
                updateCart();
                document.getElementById('customerName').value = '';
                document.getElementById('customerEmail').value = '';
                document.getElementById('customerPhone').value = '';
                document.getElementById('paymentMethod').value = 'cash';

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
};
