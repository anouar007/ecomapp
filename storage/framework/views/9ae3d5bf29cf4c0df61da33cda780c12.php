<div class="row g-3 g-md-4">
<?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-6 col-md-4" data-aos="fade-up" data-aos-delay="<?php echo e(($loop->index % 6) * 60); ?>">
        <div class="pc">
            <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none">
                <div class="pc-img">
                    <img src="<?php echo e($product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>"
                         alt="<?php echo e($product->translated_name); ?>" loading="lazy">
                    <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index:2">
                        <?php if($product->isOnSale()): ?>
                            <span class="badge rounded-pill px-2 py-1" style="background:#ef4444;font-size:.62rem;font-weight:700">-<?php echo e($product->discount_percentage); ?>%</span>
                        <?php endif; ?>
                        <?php if(!$product->isInStock()): ?>
                            <span class="badge rounded-pill bg-dark px-2 py-1" style="font-size:.62rem"><?php echo e(__('Sold Out')); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="pc-overlay">
                        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="pc-action">
                            <i class="fas fa-eye me-1"></i><?php echo e(__('View Product')); ?>

                        </a>
                    </div>
                </div>
            </a>
            <div class="pc-body">
                <div class="pc-cat"><?php echo e($product->productCategory?->translated_name ?? __('Natural')); ?></div>
                <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="pc-name d-block"><?php echo e($product->translated_name); ?></a>
                <div class="pc-stars my-2">
                    <?php for($i=0;$i<5;$i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    <span class="text-muted" style="font-size:.68rem"> (<?php echo e($product->reviews_count ?? rand(5,30)); ?>)</span>
                </div>
                <div class="mt-2">
                    <?php if($product->isOnSale()): ?>
                        <div class="pc-sale"><?php echo e($product->formatted_sale_price); ?></div>
                        <div class="pc-old"><?php echo e($product->formatted_price); ?></div>
                    <?php else: ?>
                        <div class="pc-price"><?php echo e($product->formatted_price); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="empty-box">
            <div class="empty-ico">🔍</div>
            <h5 class="fw-800 text-dark mb-2"><?php echo e(__('No products found')); ?></h5>
            <p class="text-muted small mb-4"><?php echo e(__('Try a different search or clear your filters.')); ?></p>
            <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand btn-brand-primary px-4 py-2 text-decoration-none">
                <i class="fas fa-undo me-1"></i><?php echo e(__('View All Products')); ?>

            </a>
        </div>
    </div>
<?php endif; ?>
</div>

<?php if($products->hasPages()): ?>
<div class="mt-5 d-flex justify-content-center shop-pag">
    <?php echo e($products->links()); ?>

</div>
<?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/partials/product-grid.blade.php ENDPATH**/ ?>