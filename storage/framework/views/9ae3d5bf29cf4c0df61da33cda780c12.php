<div class="row g-3 g-lg-4">
    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-6 col-md-4 col-lg-3">
        <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center py-5">
        <div class="mb-4">
            <i class="fas fa-search fa-4x text-muted opacity-25"></i>
        </div>
        <h3 class="brand-heading text-muted">لم نجد أي منتجات</h3>
        <p class="text-muted small">جربي اختيار فئة أخرى أو العودة للمتجر الرئيسي.</p>
        <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-outline px-5 mt-3 text-decoration-none">عرض كل المنتجات</a>
    </div>
    <?php endif; ?>
</div>

<?php if($products->hasPages()): ?>
<div class="mt-5 d-flex justify-content-center shop-pagination">
    <?php echo e($products->links()); ?>

</div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/partials/product-grid.blade.php ENDPATH**/ ?>