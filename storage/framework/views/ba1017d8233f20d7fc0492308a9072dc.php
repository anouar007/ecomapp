<?php if($products->isEmpty()): ?>
    <div class="text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4 opacity-25"></i>
        <h3 class="brand-heading h4 mb-3"><?php echo e(__('No products currently available')); ?></h3>
        <p class="text-muted font-body mb-0"><?php echo e(__('We are working on adding new pieces soon, stay tuned!')); ?></p>
    </div>
<?php else: ?>
    <div class="row g-3 g-lg-4" id="products-grid">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/catalog-content.blade.php ENDPATH**/ ?>