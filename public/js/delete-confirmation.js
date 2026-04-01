/**
 * Global Delete & Action Confirmation Handler
 * Intercepts form submissions with data-confirm-delete attribute
 * and button clicks with data-swal-confirm attribute
 */

document.addEventListener('DOMContentLoaded', function () {
    // Handle form delete confirmations
    document.addEventListener('submit', function (e) {
        const form = e.target;

        // Check if this is a delete confirmation form
        if (form.hasAttribute('data-confirm-delete')) {
            e.preventDefault();
            const itemType = form.getAttribute('data-item-type') || (isRTL ? 'عنصر' : 'item');
            const itemName = form.getAttribute('data-item-name') || '';

            // Show confirmation dialog
            window.confirmDelete(itemType, itemName).then(confirmed => {
                if (confirmed) {
                    form.submit();
                }
            });
        }
    });

    // Handle button action confirmations (generic)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-swal-confirm]');
        if (btn) {
            e.preventDefault();
            const message = btn.getAttribute('data-swal-confirm') || swali18n.areYouSure;
            const title = btn.getAttribute('data-swal-title') || '';
            const type = btn.getAttribute('data-swal-type') || 'warning';

            window.confirmAction(title, message).then(confirmed => {
                if (confirmed) {
                    // If it's a link, navigate
                    if (btn.tagName === 'A' && btn.href) {
                        window.location.href = btn.href;
                    } 
                    // If it has a callback function name
                    else if (btn.hasAttribute('data-callback')) {
                        const callback = window[btn.getAttribute('data-callback')];
                        if (typeof callback === 'function') {
                            callback(btn);
                        }
                    }
                    // Default: try to click parent form or just trigger click without intercepting
                    else {
                        btn.removeAttribute('data-swal-confirm');
                        btn.click();
                    }
                }
            });
        }
    });

    console.log('✅ Action confirmation handler initialized');
});
