<div class="product-card pcard h-100" data-product-id="<?php echo e($product->id); ?>"
     data-aos="fade-up" data-aos-delay="<?php echo e((($loop->index ?? 0) % 4) * 80); ?>">

    
    <div class="product-image-wrapper">
        <img src="<?php echo e($product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>"
             alt="<?php echo e($product->translated_name); ?>" loading="lazy">

        
        <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-1" style="z-index:2;">
            <?php if($product->isOnSale()): ?>
                <span class="badge px-2 py-1 rounded-pill" style="background:#ef4444;font-size:.7rem;">-<?php echo e($product->discount_percentage); ?>% <?php echo e(__('OFF')); ?></span>
            <?php endif; ?>
            <?php if(!$product->isInStock()): ?>
                <span class="badge bg-dark px-2 py-1 rounded-pill" style="font-size:.7rem;"><?php echo e(__('Sold Out')); ?></span>
            <?php endif; ?>
        </div>

        
        <div class="product-card-overlay">
            <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="btn btn-sm bg-white text-dark fw-700 rounded-pill px-3 shadow">
                <i class="fas fa-eye me-1 text-green"></i> <?php echo e(__('View')); ?>

            </a>
        </div>
    </div>

    
    <div class="p-3 bg-white">
        <?php if($product->productCategory): ?>
            <span class="x-small fw-700 text-green text-uppercase ls-1 d-block mb-1"><?php echo e($product->productCategory->translated_name); ?></span>
        <?php endif; ?>
        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none">
            <h6 class="fw-700 text-dark lh-base mb-0" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.8rem;"><?php echo e($product->translated_name); ?></h6>
        </a>

        <div class="d-flex align-items-center gap-1 my-2">
            <?php for($i=0; $i<5; $i++): ?> <i class="fas fa-star" style="font-size:.6rem;color:#f59e0b;"></i> <?php endfor; ?>
            <span class="x-small text-muted ms-1">(<?php echo e(rand(8,52)); ?>)</span>
        </div>

            <div class="mt-2">
                <?php if($product->isOnSale()): ?>
                    <div class="fw-800 text-green" style="font-size:1.05rem;"><?php echo e($product->formatted_sale_price); ?></div>
                    <div class="text-muted text-decoration-line-through x-small"><?php echo e($product->formatted_price); ?></div>
                <?php else: ?>
                    <div class="fw-800 text-dark" style="font-size:1.05rem;"><?php echo e($product->formatted_price); ?></div>
                <?php endif; ?>
            </div>
    </div>
</div>
<style>
.fw-800{font-weight:800}.x-small{font-size:.75rem}.ls-1{letter-spacing:1px}
</style>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>