/**
 * SweetAlert2 Helper Functions
 * Provides consistent, styled alerts across the application
 * Matches the app's purple gradient design theme
 */

// Detect RTL for localization
const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

// Translation strings
const swali18n = {
    ok: isRTL ? 'موافق' : 'OK',
    cancel: isRTL ? 'إلغاء' : 'Cancel',
    confirm: isRTL ? 'تأكيد' : 'Confirm',
    yes: isRTL ? 'نعم' : 'Yes',
    no: isRTL ? 'لا' : 'No',
    delete: isRTL ? 'نعم، احذف' : 'Yes, delete',
    confirmText: isRTL ? 'نعم، أنا متأكد' : 'Yes, I am sure',
    areYouSure: isRTL ? 'هل أنت متأكد؟' : 'Are you sure?',
    cannotUndone: isRTL ? 'لا يمكن التراجع عن هذا الإجراء!' : 'This action cannot be undone!'
};

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
 */
window.showSuccess = function (title, message = '') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonText: swali18n.ok,
        timer: 3000,
        timerProgressBar: true
    });
};

/**
 * Show error alert
 */
window.showError = function (title, message = '') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: swali18n.ok
    });
};

/**
 * Show warning alert
 */
window.showWarning = function (title, message = '') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: swali18n.ok
    });
};

/**
 * Show info alert
 */
window.showInfo = function (title, message = '') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        confirmButtonText: swali18n.ok
    });
};

/**
 * Show confirmation dialog
 */
window.confirmAction = async function (title, message, confirmText = swali18n.yes, cancelText = swali18n.no) {
    const result = await Swal.fire({
        title: title || swali18n.areYouSure,
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
 * Show delete confirmation
 */
window.confirmDelete = async function (itemType, itemName = '') {
    const nameText = itemName ? `<br><strong>${itemName}</strong>` : '';

    const result = await Swal.fire({
        title: swali18n.areYouSure,
        html: `${isRTL ? 'أنت على وشك حذف' : 'You are about to delete this'} ${itemType}.${nameText}<br><br>${swali18n.cannotUndone}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: swali18n.delete,
        cancelButtonText: swali18n.cancel
    });

    return result.isConfirmed;
};

/**
 * Show validation error with list of errors
 */
window.showValidationErrors = function (title, errors) {
    const errorList = errors.map(err => `<li style="text-align: ${isRTL ? 'right' : 'left'};">${err}</li>`).join('');

    return Swal.fire({
        icon: 'error',
        title: title,
        html: `<ul style="padding-${isRTL ? 'right' : 'left'}: 20px; margin: 10px 0;">${errorList}</ul>`,
        confirmButtonText: swali18n.ok
    });
};

// Add custom styles for premium look
if (!document.getElementById('swal-custom-styles')) {
    const style = document.createElement('style');
    style.id = 'swal-custom-styles';
    style.textContent = `
        .swal-custom-popup {
            font-family: 'Inter', 'Cairo', sans-serif;
            border-radius: var(--border-radius, 12px);
            padding: 1.5rem;
        }
        
        .swal-custom-title {
            font-weight: 700;
            color: #1e293b;
        }
        
        .swal-confirm-btn,
        .swal-cancel-btn {
            font-weight: 600;
            border-radius: 30px !important;
            padding: 10px 28px !important;
        }
        
        .swal2-styled.swal2-confirm {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%) !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }
        
        .swal2-styled.swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3) !important;
        }
        
        .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }
        
        div:where(.swal2-container) button:where(.swal2-styled):where(.swal2-deny) {
            border-radius: 30px !important;
        }
    `;
    document.head.appendChild(style);
}

console.log('✅ SweetAlert2 helpers updated with RTL support');
