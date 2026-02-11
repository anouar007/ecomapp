/**
 * Global Delete Confirmation Handler
 * Intercepts form submissions with data-confirm-delete attribute
 * and shows SweetAlert2 confirmation dialog
 */

document.addEventListener('DOMContentLoaded', function () {
    // Handle delete confirmations
    document.addEventListener('submit', function (e) {
        const form = e.target;

        // Check if this is a delete confirmation form
        if (form.hasAttribute('data-confirm-delete')) {
            e.preventDefault();
            const itemType = form.getAttribute('data-item-type') || 'item';
            const itemName = form.getAttribute('data-item-name') || '';

            // Show confirmation dialog
            confirmDelete(itemType, itemName).then(confirmed => {
                if (confirmed) {
                    // Submit the form
                    form.submit();
                }
            });
        }
    });

    console.log('✅ Delete confirmation handler initialized');
});
