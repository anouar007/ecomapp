// Customer Selection Logic
document.getElementById('customerSelect').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const customerInfo = document.getElementById('customerInfo');
    const creditInfo = document.getElementById('creditInfo');
    const creditOption = document.getElementById('creditOption');

    if (this.value === '') {
        // Walk-in customer
        document.getElementById('customerId').value = '';
        document.getElementById('customerName').value = 'Walk-in Customer';
        document.getElementById('customerEmail').value = '';
        document.getElementById('customerPhone').value = '';
        customerInfo.style.display = 'none';
        creditOption.disabled = true;
        creditOption.style.color = '#9ca3af';

        // Reset to cash if credit was selected
        if (document.getElementById('paymentMethod').value === 'credit') {
            document.getElementById('paymentMethod').value = 'cash';
        }
    } else {
        // Registered customer
        const customerId = this.value;
        const name = selectedOption.dataset.name;
        const email = selectedOption.dataset.email || '';
        const phone = selectedOption.dataset.phone || '';
        const creditLimit = parseFloat(selectedOption.dataset.creditLimit) || 0;
        const balance = parseFloat(selectedOption.dataset.balance) || 0;

        document.getElementById('customerId').value = customerId;
        document.getElementById('customerName').value = name;
        document.getElementById('customerEmail').value = email;
        document.getElementById('customerPhone').value = phone;

        // Show customer info
        customerInfo.style.display = 'block';
        document.getElementById('customerDisplayName').textContent = name;
        document.getElementById('customerContact').textContent =
            (email ? email : '') + (phone ? ' • ' + phone : '');

        // Show credit info if customer has credit limit
        if (creditLimit > 0) {
            creditInfo.style.display = 'block';
            const available = creditLimit - balance;
            const percentage = (balance / creditLimit) * 100;

            document.getElementById('creditUsed').textContent =
                `${window.formatCurrency(balance)} / ${window.formatCurrency(creditLimit)} (${window.formatCurrency(available)} available)`;
            document.getElementById('creditBar').style.width = Math.min(percentage, 100) + '%';
            document.getElementById('creditBar').style.background =
                percentage > 90 ? '#ef4444' : (percentage > 75 ? '#f59e0b' : '#10b981');

            // Enable credit option
            creditOption.disabled = false;
            creditOption.style.color = '';
        } else {
            creditInfo.style.display = 'none';
            creditOption.disabled = true;
            creditOption.style.color = '#9ca3af';
        }
    }
});

// Update checkout function to include customer_id with credit validation
const originalCheckout = window.checkout;
window.checkout = function () {
    const customerId = document.getElementById('customerId').value;
    const customerName = document.getElementById('customerName').value;
    const customerEmail = document.getElementById('customerEmail').value;
    const customerPhone = document.getElementById('customerPhone').value;
    const paymentMethod = document.getElementById('paymentMethod').value;

    // Basic validation
    if (!customerName || cart.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please ensure customer details are filled and cart is not empty'
        });
        return;
    }

    // Calculate order total
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal * (1 + window.currencyConfig.tax_rate);

    // Credit validation for registered customers
    if (paymentMethod === 'credit') {
        if (!customerId) {
            Swal.fire({
                icon: 'warning',
                title: 'Credit Not Available',
                text: 'Please select a registered customer to use credit payment.'
            });
            return;
        }

        // Get customer credit information from the select option
        const selectElement = document.getElementById('customerSelect');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const creditLimit = parseFloat(selectedOption.dataset.creditLimit) || 0;
        const currentBalance = parseFloat(selectedOption.dataset.balance) || 0;
        const availableCredit = creditLimit - currentBalance;

        // Check if customer has credit limit
        if (creditLimit <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Credit Limit',
                text: 'This customer does not have a credit limit set. Please contact management or use a different payment method.'
            });
            return;
        }

        // Check if available credit is sufficient
        if (availableCredit < total) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Credit',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p style="margin-bottom: 15px;"><strong>⚠️ This order cannot be completed on credit.</strong></p>
                        <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span>Order Total:</span>
                                <strong style="color: #dc3545;">${window.formatCurrency(total)}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span>Credit Limit:</span>
                                <span>${window.formatCurrency(creditLimit)}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span>Current Balance:</span>
                                <span>${window.formatCurrency(currentBalance)}</span>
                            </div>
                            <hr style="margin: 10px 0;">
                            <div style="display: flex; justify-content: space-between;">
                                <span><strong>Available Credit:</strong></span>
                                <strong style="color: ${availableCredit > 0 ? '#28a745' : '#dc3545'};">${window.formatCurrency(availableCredit)}</strong>
                            </div>
                        </div>
                        <p style="margin-top: 15px; color: #6c757d; font-size: 14px;">
                            💡 Please use a different payment method or reduce the order amount.
                        </p>
                    </div>
                `,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-wide'
                }
            });
            return;
        }

        // Warn if this order will significantly reduce available credit
        const remainingAfterOrder = availableCredit - total;
        const utilizationAfterOrder = ((currentBalance + total) / creditLimit) * 100;

        if (utilizationAfterOrder > 90) {
            // Show warning but allow to proceed
            Swal.fire({
                icon: 'warning',
                title: 'High Credit Utilization',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p style="margin-bottom: 15px;">This order will use <strong>${utilizationAfterOrder.toFixed(0)}%</strong> of the customer's credit limit.</p>
                        <div style="background: #fff3cd; padding: 12px; border-radius: 8px; margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span>Available After Order:</span>
                                <strong style="color: ${remainingAfterOrder < (creditLimit * 0.1) ? '#dc3545' : '#856404'};">${window.formatCurrency(remainingAfterOrder)}</strong>
                            </div>
                        </div>
                        <p style="margin-top: 15px; color: #6c757d; font-size: 14px;">
                            Do you want to proceed?
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    processOrderAPI(customerId, customerName, customerEmail, customerPhone, paymentMethod, cart, total);
                }
            });
            return;
        }
    }

    // Proceed with order
    processOrderAPI(customerId, customerName, customerEmail, customerPhone, paymentMethod, cart, total);
};

// Separate function to handle the API call
function processOrderAPI(customerId, customerName, customerEmail, customerPhone, paymentMethod, cart, total) {
    // Get discount values
    const discountAmount = parseFloat(document.getElementById('discountAmount')?.value) || 0;
    const discountType = document.getElementById('discountType')?.value || 'percent';

    const orderData = {
        customer_id: customerId || null,
        customer_name: customerName,
        customer_email: customerEmail || null,
        customer_phone: customerPhone || null,
        payment_method: paymentMethod,
        discount_amount: discountAmount,
        discount_type: discountType,
        items: cart.map(item => ({
            product_id: item.product_id,
            quantity: item.quantity,
            price: item.price
        }))
    };

    // Show loading
    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.disabled = true;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    fetch('/pos/order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(orderData)
    })
        .then(response => response.json())
        .then(data => {
            // Reset button state
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = `Charge ${window.formatCurrency(total)}`;

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Completed!',
                    html: `
                    <p><strong>Order #:</strong> ${data.order.order_number}</p>
                    <p><strong>Total:</strong> ${window.formatCurrency(data.order.total)}</p>
                    <p><strong>Payment:</strong> ${paymentMethod.toUpperCase()}</p>
                `,
                    confirmButtonText: 'New Order'
                }).then(() => {
                    clearCart();
                    document.getElementById('customerSelect').value = '';
                    document.getElementById('customerSelect').dispatchEvent(new Event('change'));
                    // Reset discount
                    const discountInput = document.getElementById('discountAmount');
                    if (discountInput) discountInput.value = '';
                    loadAllProducts();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Order Failed',
                    text: data.message || 'An error occurred while processing the order.'
                });
            }
        })
        .catch(error => {
            // Reset button state
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = `Charge ${window.formatCurrency(total)}`;

            console.error('Order processing error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Failed to process order. Please check your connection and try again.'
            });
        });
}
