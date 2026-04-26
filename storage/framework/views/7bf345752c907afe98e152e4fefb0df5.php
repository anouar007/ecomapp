<?php
    $cart = session('cart', []);
    $total = 0;
    foreach($cart as $details) {
        $total += $details['price'] * $details['quantity'];
    }
?>

<?php if(count($cart) > 0): ?>
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="small fw-bold text-muted text-uppercase"><?php echo e(__('Subtotal')); ?></span>
            <span class="h5 fw-bold text-dark mb-0"><?php echo e(currency($total)); ?></span>
        </div>
        
        <div class="bg-white border rounded-3 p-2 mb-3 text-center">
            <p class="x-small text-muted mb-0"><i class="fas fa-info-circle text-gold me-1"></i> <?php echo e(__('Delivery determined at checkout')); ?></p>
        </div>

        <a href="<?php echo e(route('checkout.index')); ?>" class="btn-brand btn-brand-primary w-100 justify-content-center py-3 rounded-pill shadow-sm mb-2">
            <?php echo e(__('Checkout')); ?> <i class="fas fa-arrow-right ms-2"></i>
        </a>
        
        <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-link w-100 text-decoration-none text-muted small fw-bold py-2">
            <?php echo e(__('View Shopping Cart')); ?>

        </a>
    </div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/partials/mini-cart-footer.blade.php ENDPATH**/ ?>