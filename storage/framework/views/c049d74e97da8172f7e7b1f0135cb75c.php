<?php $__env->startSection('meta_title', 'سلة التسوق — ' . setting('app_name', 'Hijab Princesses')); ?>
<?php $__env->startSection('meta_description', 'Hijab Princesses: اكتشفي أرقى تشكيلة من العبايات والخمارات المغربية الراقية. جودة فاخرة وتوصيل سريع لكل مدن المغرب. تسوقي الآن من أناقة الأميرة.'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-surface section-py min-vh-100">
    <div class="container px-xl-5">
        
        <div class="d-flex flex-column mb-5" data-aos="fade-up">
            <h1 class="brand-heading h2 mb-2">سلة التسوق الخاصة بكِ</h1>
            <div class="bg-gold rounded" style="width: 60px; height: 4px;"></div>
        </div>

        <div class="row g-4 g-lg-5" id="cart-main-row">
            <?php if(session('cart') && count(session('cart')) > 0): ?>
                
                <div class="col-lg-8" id="cart-items-container">
                    <?php echo $__env->make('frontend.cart.partials.full-cart-items', ['cart' => session('cart')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    
                    <div class="mt-4">
                        <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-outline px-4 text-decoration-none small font-body">
                             مواصلة التسوق <i class="fas fa-arrow-left ms-2 small"></i>
                        </a>
                    </div>
                </div>

                
                <div class="col-lg-4" id="cart-summary-container">
                    <?php echo $__env->make('frontend.cart.partials.full-cart-summary', ['cart' => session('cart')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php else: ?>
                <div class="col-12 text-center py-5" data-aos="fade-up">
                    <div class="mb-5 bg-gold-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                        <i class="fas fa-shopping-bag fa-4x text-gold opacity-50"></i>
                    </div>
                    <h2 class="brand-heading mb-3">سلتكِ لا تزال بانتظارك</h2>
                    <p class="text-muted small mb-4 font-body opacity-75" data-aos="fade-up">تمتعي بتجربة تسوق استثنائية مع أرقى تصاميم أناقة الأميرة</p>
                    <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-primary px-5 py-3 text-decoration-none hvr-grow">اكتشفي التشكيلة الآن</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
/**
 * Update Quantity via AJAX (PATCH)
 */
function updateQty(id, qty) {
    if (qty < 1) return;
    
    // Optimistic UI could go here, but for now we fetch
    fetch('<?php echo e(route('cart.update')); ?>', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: id, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            refreshFullPageCart();
            if(typeof refreshMiniCart === 'function') refreshMiniCart();
        }
    })
    .catch(error => console.error('Error updating cart:', error));
}

/**
 * Remove Item via AJAX (DELETE)
 */
function removeItem(id) {
    Swal.fire({
        title: 'هل تريدين الحذف؟',
        text: "سوف يتم إزالة هذا المنتج من سلة التسوق الخاصة بكِ.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c5a059',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'نعم، احذفيه',
        cancelButtonText: 'إبقاء',
        customClass: {
            popup: 'rounded-4 border-0',
            confirmButton: 'rounded-pill px-4',
            cancelButton: 'rounded-pill px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?php echo e(route('cart.remove')); ?>', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ id: id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    refreshFullPageCart();
                    if(typeof refreshMiniCart === 'function') refreshMiniCart();
                }
            })
            .catch(error => console.error('Error removing item:', error));
        }
    });
}

/**
 * Perform Professional AJAX Refresh of the entire cart page
 */
function refreshFullPageCart() {
    // 1. Refresh Items List
    fetch('<?php echo e(route('cart.full.items')); ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const container = document.getElementById('cart-items-container');
        if (container) {
            container.innerHTML = html;
            // Re-trigger AOS if needed, or just let it be
            if (typeof AOS !== 'undefined') AOS.refresh();
        } else {
            // If container is missing (e.g. cart became empty), refresh the main row
            location.reload(); 
        }
    });

    // 2. Refresh Summary
    fetch('<?php echo e(route('cart.full.summary')); ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.text())
    .then(html => {
        const summary = document.getElementById('cart-summary-container');
        if (summary) summary.innerHTML = html;
    });
}
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/index.blade.php ENDPATH**/ ?>