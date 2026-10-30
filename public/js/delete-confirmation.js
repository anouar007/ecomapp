/**
 * Global Delete Confirmation Handler
 * Intercepts form submissions with data-confirm-delete attribute
 * and shows SweetAlert2 confirmation dialog.
 * Supports data-ajax-delete="true" for seamless deletion without page reload.
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
                    const isAjax = form.getAttribute('data-ajax-delete') === 'true' || form.getAttribute('data-ajax') === 'true';

                    if (isAjax) {
                        executeAjaxDelete(form, itemType, itemName);
                    } else {
                        // Submit the form normally
                        form.submit();
                    }
                }
            });
        }
    });

    console.log('✅ Delete confirmation handler initialized');
});

/**
 * Execute AJAX deletion without page reload
 */
function executeAjaxDelete(form, itemType, itemName) {
    const url = form.action;
    const token = form.querySelector('input[name="_token"]')?.value || 
                  document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : null;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    const formData = new FormData(form);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success === false) {
            throw new Error(data.message || 'Une erreur est survenue lors de la suppression.');
        }
        return data;
    })
    .then(data => {
        const productId = form.getAttribute('data-product-id') || 
                          form.closest('[data-product-id]')?.getAttribute('data-product-id');

        // Let listeners know item was deleted (e.g. to decrement counters, uncheck selection)
        document.dispatchEvent(new CustomEvent('item:deleted', {
            bubbles: true,
            detail: { form, itemType, itemName, productId, data }
        }));

        // Find elements to remove (both desktop row & mobile card if applicable)
        const elementsToRemove = [];
        if (productId) {
            const row = document.getElementById(`product-row-${productId}`);
            const card = document.getElementById(`mobile-product-card-${productId}`);
            if (row) elementsToRemove.push(row);
            if (card) elementsToRemove.push(card);
        }

        if (elementsToRemove.length === 0) {
            const targetSelector = form.getAttribute('data-target');
            if (targetSelector) {
                document.querySelectorAll(targetSelector).forEach(el => elementsToRemove.push(el));
            } else {
                const parent = form.closest('tr, .mobile-product-card, .card');
                if (parent) elementsToRemove.push(parent);
            }
        }

        // Animate and remove from DOM
        elementsToRemove.forEach(el => {
            // Clean up any bootstrap tooltips
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                el.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(btn => {
                    const inst = bootstrap.Tooltip.getInstance(btn);
                    if (inst) inst.dispose();
                });
            }

            el.style.transition = 'all 0.35s ease';
            el.style.opacity = '0';
            el.style.transform = 'scale(0.96)';
            setTimeout(() => {
                el.remove();
                document.dispatchEvent(new CustomEvent('item:removed-from-dom', {
                    bubbles: true,
                    detail: { productId, itemType, data }
                }));
            }, 350);
        });

        // Show SweetAlert Toast
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: 'success',
                title: data.message || `${itemName || itemType} supprimé avec succès.`
            });
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message || 'Une erreur est survenue lors de la suppression.'
            });
        } else {
            alert(error.message || 'Une erreur est survenue lors de la suppression.');
        }
    });
}
