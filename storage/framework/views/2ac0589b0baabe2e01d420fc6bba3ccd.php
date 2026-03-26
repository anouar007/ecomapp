<?php $__empty_1 = true; $__currentLoopData = session('cart', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="mc-item" id="cart-item-<?php echo e($key); ?>">
        <img src="<?php echo e(!empty($details['image']) && strval($details['image']) !== '0' ? Storage::url($details['image']) : asset('images/placeholder-product.jpg')); ?>"
             alt="<?php echo e($details['name']); ?>" class="mc-item-img">
        <div class="mc-item-info">
            <div class="mc-item-name"><?php echo e($details['name']); ?></div>
            <div class="mc-tags">
                <?php if(!empty($details['color'])): ?>
                    <span class="mc-tag"><?php echo e($details['color']); ?></span>
                <?php endif; ?>
                <?php if(!empty($details['size'])): ?>
                    <span class="mc-tag"><?php echo e($details['size']); ?></span>
                <?php endif; ?>
            </div>
            <div class="mc-item-bottom">
                <span class="mc-price"><?php echo e(currency($details['price'])); ?></span>
                <div class="mc-qty">
                    <button class="mc-qty-btn" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] - 1); ?>)">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input class="mc-qty-val" value="<?php echo e($details['quantity']); ?>" readonly>
                    <button class="mc-qty-btn" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] + 1); ?>)">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <button class="mc-delete" onclick="removeItem('<?php echo e($key); ?>')" title="حذف">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="mc-empty">
        <div class="mc-empty-icon">🛍️</div>
        <h5>سلتك فارغة</h5>
        <p>لم تقومي بإضافة أي منتج بعد.</p>
        <a href="<?php echo e(route('shop.index')); ?>" class="mc-shop-btn" data-bs-dismiss="offcanvas">ابدئي التسوق</a>
    </div>
<?php endif; ?>

<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/partials/mini-cart-items.blade.php ENDPATH**/ ?>