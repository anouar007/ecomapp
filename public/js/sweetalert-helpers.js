/**
 * SweetAlert2 Helper Functions
 * Provides consistent, styled alerts across the application
 * Matches the app's purple gradient design theme
 */

// Custom configuration for SweetAlert2
const swalConfig = {
    confirmButtonColor: '#667eea',
    cancelButtonColor: '#ef4444',
    customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-confirm-btn',
        cancelButton: 'swal-cancel-btn'
    },
    buttonsStyling: true,
    allowOutsideClick: false,
    allowEscapeKey: true
};

/**
 * Show success alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message (optional)
 */
window.showSuccess = function (title, message = '') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    });
};

/**
 * Show error alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 */
window.showError = function (title, message = '') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK'
    });
};

/**
 * Show warning alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 */
window.showWarning = function (title, message = '') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK'
    });
};

/**
 * Show info alert
 * @param {string} title - Alert title
 * @param {string} message - Alert message
 */
window.showInfo = function (title, message = '') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK'
    });
};

/**
 * Show confirmation dialog
 * @param {string} title - Confirmation title
 * @param {string} message - Confirmation message
 * @param {string} confirmText - Confirm button text (default: 'Yes')
 * @param {string} cancelText - Cancel button text (default: 'No')
 * @returns {Promise<boolean>} - True if confirmed, false if canceled
 */
window.confirmAction = async function (title, message, confirmText = 'Yes', cancelText = 'No') {
    const result = await Swal.fire({
        title: title,
        html: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: swalConfig.confirmButtonColor,
        cancelButtonColor: swalConfig.cancelButtonColor,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });

    return result.isConfirmed;
};

/**
 * Show stock limit reached alert
 * @param {string} productName - Product name
 * @param {number} stock - Available stock
 */
window.showStockLimit = function (productName, stock) {
    return Swal.fire({
        icon: 'warning',
        title: 'Stock Limit Reached',
        html: `Maximum stock (<strong>${stock}</strong>) reached for<br><strong>${productName}</strong>`,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK'
    });
};

/**
 * Show order success alert
 * @param {string} orderNumber - Order number
 * @param {number} total - Order total
 */
window.showOrderSuccess = function (orderNumber, total) {
    return Swal.fire({
        icon: 'success',
        title: 'Order Completed Successfully!',
        html: `
            <div style="text-align: left; margin-top: 20px;">
                <p style="margin: 8px 0;"><strong>Order #:</strong> ${orderNumber}</p>
                <p style="margin: 8px 0;"><strong>Total:</strong> $${parseFloat(total).toFixed(2)}</p>
                <p style="margin-top: 16px; color: #059669; font-weight: 600;">Thank you for your purchase!</p>
            </div>
        `,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK',
        timer: 5000,
        timerProgressBar: true
    });
};

/**
 * Show delete confirmation (specific styling for delete actions)
 * @param {string} itemType - Type of item being deleted (e.g., "product", "category")
 * @param {string} itemName - Name of the item (optional)
 * @returns {Promise<boolean>} - True if confirmed, false if canceled
 */
window.confirmDelete = async function (itemType, itemName = '') {
    const nameText = itemName ? `<br><strong>${itemName}</strong>` : '';

    const result = await Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete this ${itemType}.${nameText}<br><br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    });

    return result.isConfirmed;
};

/**
 * Show validation error with list of errors
 * @param {string} title - Alert title
 * @param {Array<string>} errors - List of error messages
 */
window.showValidationErrors = function (title, errors) {
    const errorList = errors.map(err => `<li style="text-align: left;">${err}</li>`).join('');

    return Swal.fire({
        icon: 'error',
        title: title,
        html: `<ul style="padding-left: 20px; margin: 10px 0;">${errorList}</ul>`,
        confirmButtonColor: swalConfig.confirmButtonColor,
        confirmButtonText: 'OK'
    });
};

// Add custom styles
const style = document.createElement('style');
style.textContent = `
    .swal-custom-popup {
        font-family: 'Inter', sans-serif;
        border-radius: 12px;
    }
    
    .swal-custom-title {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
    }
    
    .swal-confirm-btn,
    .swal-cancel-btn {
        font-family: 'Inter', sans-serif;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 24px;
    }
    
    .swal2-popup {
        font-family: 'Inter', sans-serif;
    }
    
    .swal2-styled.swal2-confirm {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .swal2-styled.swal2-confirm:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
    }
`;
document.head.appendChild(style);

console.log('✅ SweetAlert2 helpers loaded successfully');
