<?php $total = 0; ?>
<?php $__empty_1 = true; $__currentLoopData = session('cart', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $total += $details['price'] * $details['quantity']; ?>
    <div class="cart-item bg-white p-3 rounded-4 shadow-sm mb-3 position-relative border border-light" id="cart-item-<?php echo e($key); ?>">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3 position-relative">
                <img src="<?php echo e(!empty($details['image']) && strval($details['image']) !== '0' ? Storage::url($details['image']) : asset('images/placeholder-product.jpg')); ?>" alt="<?php echo e($details['name']); ?>" class="rounded-3 object-fit-cover" style="width: 80px; height: 100px;">
                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary text-white border-0 shadow-sm" style="font-size: 0.75rem;"><?php echo e($details['quantity']); ?></span>
            </div>
            <div class="flex-grow-1 min-w-0">
                <h6 class="fw-bold mb-1 pe-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?php echo e($details['name']); ?>"><?php echo e($details['name']); ?></h6>
                
                <div class="d-flex gx-2 small text-muted mb-2">
                    <?php if(isset($details['color'])): ?>
                        <span class="me-2"><i class="fas fa-circle ms-1" style="font-size: 0.6rem; color: <?php echo e($details['color']); ?>;"></i>اللون: <?php echo e($details['color']); ?></span>
                    <?php endif; ?>
                    <?php if(isset($details['size'])): ?>
                        <span>المقاس: <?php echo e($details['size']); ?></span>
                    <?php endif; ?>
                    <?php if(!isset($details['color']) && !isset($details['size'])): ?>
                        <span><?php echo e($details['category_name'] ?? 'منتج'); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="text-primary fw-bold" style="font-size: 1.1rem;"><?php echo e(currency($details['price'])); ?></span>
                    
                    <div class="quantity-control bg-light rounded-pill d-flex align-items-center px-1 border">
                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] - 1); ?>)">
                            <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                        </button>
                        <input type="text" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold p-0" value="<?php echo e($details['quantity']); ?>" readonly style="width: 30px;">
                        <button class="btn btn-sm btn-link text-dark text-decoration-none p-1 border-0" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] + 1); ?>)">
                            <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn btn-sm text-danger position-absolute top-0 end-0 mt-2 me-2 opacity-50" onclick="removeItem('<?php echo e($key); ?>')" title="حذف">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-5 mt-4">
        <div class="mb-4 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
            <i class="fas fa-shopping-basket fa-3x text-muted opacity-25"></i>
        </div>
        <h5 class="fw-bold text-dark">السلة فارغة</h5>
        <p class="text-muted small mb-4">لم تقومي بإضافة أي منتج بعد.</p>
        <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-primary rounded-pill px-5 shadow-sm">ابدئي التسوق</a>
    </div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/partials/mini-cart-items.blade.php ENDPATH**/ ?>