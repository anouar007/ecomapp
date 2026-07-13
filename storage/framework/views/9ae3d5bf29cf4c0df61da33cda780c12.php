<div class="row g-4">
    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-6 col-md-4">
        <div class="rounded-2 overflow-hidden bg-white h-100 border hover-product" style="border-color: #eee !important;">
            
            <div class="position-relative" style="height: 220px;">
                <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="d-block w-100 h-100">
                    <?php if($product->main_image): ?>
                        <img src="<?php echo e(Storage::url($product->main_image)); ?>" alt="<?php echo e($product->name); ?>" loading="lazy" class="w-100 h-100 product-img" style="object-fit: cover; transition: transform 0.6s ease;">
                    <?php else: ?>
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted fs-1"><i class="fas fa-pen-nib"></i></div>
                    <?php endif; ?>
                </a>

                
                <div class="position-absolute d-flex gap-2" style="top: 15px; left: 15px; z-index: 5;">
                    <?php if(!$product->isInStock()): ?>
                        <span class="bg-secondary text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 0.7rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">نفذت الكمية</span>
                    <?php elseif($product->created_at->diffInDays(now()) < 14): ?>
                        <span class="bg-white text-dark fw-bold px-3 py-1 rounded-pill" style="font-size: 0.7rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">جديد</span>
                    <?php elseif($product->isOnSale()): ?>
                        <span class="bg-danger text-white fw-bold px-3 py-1 rounded-pill" style="font-size: 0.7rem; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">−<?php echo e($product->discount_percentage); ?>%</span>
                    <?php endif; ?>
                </div>

                
                <div class="product-action-overlay d-flex align-items-center justify-content-center position-absolute top-0 start-0 w-100 h-100" style="background: rgba(255,255,255,0.85); opacity: 0; transition: opacity 0.3s ease; pointer-events: none;">
                    <div class="d-flex flex-column gap-2" style="pointer-events: auto;">
                        <?php if($product->isInStock()): ?>
                        <button class="btn btn-sm text-white fw-bold px-4 py-2" style="background-color: #4A5D23; border-radius: 4px;" onclick="addToCart(<?php echo e($product->id); ?>)">
                            <i class="fas fa-shopping-cart me-2"></i> أضف للسلة
                        </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="btn btn-sm btn-outline-dark fw-bold px-4 py-2" style="border-radius: 4px;">
                            <i class="fas fa-eye me-2"></i> التفاصيل
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="p-3 d-flex flex-column text-end" dir="rtl" style="height: calc(100% - 220px);">
                <?php if($product->category_name): ?>
                <div class="mb-1 fw-bold" style="color: #c9a65d; font-size: 0.75rem;"><?php echo e($product->category_name); ?></div>
                <?php endif; ?>
                
                <h5 class="fw-bold mb-2 flex-grow-1">
                    <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none" style="color: #1c2410; font-family: var(--font-heading); font-size: 1.1rem; line-height: 1.4;"><?php echo e(Str::limit($product->name, 45)); ?></a>
                </h5>
                
                <div class="d-flex justify-content-between align-items-end mt-2">
                    <div class="fw-bold" style="color: #c9a65d; font-size: 1.1rem;">
                        <?php if($product->isOnSale()): ?>
                            <span><?php echo e($product->formatted_sale_price); ?></span>
                            <span class="text-muted text-decoration-line-through small ms-2" style="font-size: 0.85rem;"><?php echo e($product->formatted_price); ?></span>
                        <?php else: ?>
                            <span><?php echo e($product->formatted_price); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center gap-1">
                        <div class="text-warning" style="font-size: 0.75rem;">
                            <?php for($i = 0; $i < 5; $i++): ?>
                                <i class="fa<?php echo e($i < round($product->reviews_avg_rating ?? 0) ? 's' : 'r'); ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;">(<?php echo e($product->reviews_count ?? 0); ?>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center py-5">
        <div class="p-5 bg-white rounded-4 border shadow-sm">
            <i class="fas fa-pen-nib text-muted mb-3" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mb-2">لم يتم العثور على منتجات</h5>
            <p class="text-muted mb-4">يرجى تعديل خيارات التصفية أو البحث لعرض المزيد من النتائج.</p>
            <a href="<?php echo e(route('shop.index')); ?>" class="btn text-white px-4 py-2" style="background-color: var(--primary); border-radius: 5px;">
                <i class="fas fa-redo me-2"></i> إعادة تعيين الفلاتر
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if($products->hasPages()): ?>
<div class="mt-5 d-flex justify-content-center shop-pagination">
    <?php echo e($products->links()); ?>

</div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/partials/product-grid.blade.php ENDPATH**/ ?>