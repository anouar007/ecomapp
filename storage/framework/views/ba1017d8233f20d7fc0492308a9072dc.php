<?php if(request('search') || request('sort')): ?>
    
    <div class="d-flex align-items-center gap-3 mb-5" data-aos="fade-left">
        <div class="bg-gold rounded" style="width: 4px; height: 35px;"></div>
        <div>
            <h2 class="brand-heading mb-0 h3">نتائج البحث</h2>
            <p class="text-muted small mb-0 font-body fw-bold">تم العثory على <span class="text-gold"><?php echo e($products->count()); ?></span> قطعة</p>
        </div>
    </div>

    <?php if($products->isEmpty()): ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="brand-heading">لم نجد أي منتجات تطابق بحثك</h4>
            <a href="javascript:void(0)" onclick="loadCategory('')" class="btn btn-brand-primary rounded-pill px-4 mt-3">عرض كل المجموعة</a>
        </div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

<?php elseif(false): ?> 
<?php else: ?>
    
    <?php $__currentLoopData = $categoriesWithProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="category-section-luxe mb-5" data-aos="fade-up" id="category-<?php echo e($category->slug); ?>">
            <div class="section-premium-header">
                <h2 class="section-premium-title"><?php echo e($category->translated_name); ?></h2>
                <div class="section-premium-divider">
                    <div class="section-premium-line"></div>
                    <i class="fas fa-crown section-premium-icon"></i>
                    <div class="section-premium-line"></div>
                </div>
            </div>
            
            <div class="row g-3 g-lg-4">
                <?php $__currentLoopData = $category->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/catalog-content.blade.php ENDPATH**/ ?>