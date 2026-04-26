<?php $__empty_1 = true; $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="d-flex flex-column flex-md-row gap-4 mb-4 p-4 bg-white rounded-4 shadow-sm border border-light position-relative" data-aos="fade-up">
        
        <div class="rounded-4 overflow-hidden shadow-sm mx-auto mx-md-0" style="width: 120px; height: 150px; flex-shrink: 0;">
            <img src="<?php echo e(!empty($details['image']) ? Storage::url($details['image']) : asset('images/placeholder-product.jpg')); ?>" class="w-100 h-100 object-fit-cover">
        </div>

        
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="fw-bold text-dark mb-0"><?php echo e($details['name']); ?></h5>
                <button class="btn btn-link text-danger p-0" onclick="removeItem('<?php echo e($key); ?>')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            
            <div class="d-flex gap-2 mb-3">
                <?php if(!empty($details['size'])): ?>
                    <span class="badge bg-light text-muted border fw-normal"><?php echo e(__('Size:')); ?> <?php echo e($details['size']); ?></span>
                <?php endif; ?>
                <span class="badge bg-light text-muted border fw-normal"><?php echo e(__('Price:')); ?> <?php echo e(currency($details['price'])); ?></span>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-auto">
                
                <div class="d-flex align-items-center border rounded-pill bg-light">
                    <button class="btn px-3 py-2" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] - 1); ?>)"><i class="fas fa-minus small"></i></button>
                    <span class="fw-bold px-3"><?php echo e($details['quantity']); ?></span>
                    <button class="btn px-3 py-2" onclick="updateQty('<?php echo e($key); ?>', <?php echo e($details['quantity'] + 1); ?>)"><i class="fas fa-plus small"></i></button>
                </div>

                
                <div class="text-end">
                    <span class="text-muted small d-block"><?php echo e(__('Total')); ?></span>
                    <span class="h5 fw-bold text-gold mb-0"><?php echo e(currency($details['price'] * $details['quantity'])); ?></span>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="text-center py-5">
        <h4 class="fw-bold text-muted"><?php echo e(__('Your cart is empty')); ?></h4>
    </div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/cart/partials/full-cart-items.blade.php ENDPATH**/ ?>