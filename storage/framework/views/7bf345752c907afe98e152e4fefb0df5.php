<?php
    $cart = session('cart', []);
    $total = 0;
    $originalTotal = 0;
    foreach($cart as $details) {
        $total += $details['price'] * $details['quantity'];
        $originalTotal += ($details['original_price'] ?? $details['price']) * $details['quantity'];
    }
    $savings = $originalTotal - $total;
?>
<?php if(count($cart) > 0): ?>

<div class="mc-footer">
    <div class="mc-shipping">
        <i class="fas fa-truck me-1"></i> التوصيل: 20 درهم الدار البيضاء و 30 درهم النواحي
و 40 درهم باقي المدن
    </div>
    <div class="mc-total-row">
        <span class="mc-total-label">المجموع الفرعي</span>
        <span class="mc-total-val" id="mini-cart-total"><?php echo e(currency($total)); ?></span>
    </div>
    <?php if($savings > 0): ?>
        <div class="mc-total-row text-danger" style="margin-top: -5px; font-size: 0.85rem;">
            <span>لقد وفرتِ:</span>
            <span class="fw-bold"><?php echo e(currency($savings)); ?></span>
        </div>
    <?php endif; ?>
    <a href="<?php echo e(route('checkout.index')); ?>" class="mc-checkout-btn">
        <i class="fas fa-check-circle"></i>
        <span>إتمام الطلب</span>
    </a>
    <a href="<?php echo e(route('cart.index')); ?>" class="mc-view-btn">عرض السلة كاملة</a>
</div>
<?php endif; ?>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/partials/mini-cart-footer.blade.php ENDPATH**/ ?>