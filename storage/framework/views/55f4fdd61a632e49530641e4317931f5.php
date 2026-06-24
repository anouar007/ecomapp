<div class="product-card pcard h-100 position-relative d-flex flex-column" id="product-card-<?php echo e($product->id); ?>" data-product-id="<?php echo e($product->id); ?>"
     data-aos="fade-up" data-aos-delay="<?php echo e((($loop->index ?? 0) % 4) * 80); ?>">

    
    <div class="position-absolute w-100 d-flex justify-content-end align-items-start p-3" style="z-index: 5; top: 0; left: 0;">
        <div class="d-flex flex-column gap-2 align-items-end">
            <?php if($product->isOnSale()): ?>
                <span class="badge badge-glass badge-glass-sale">
                    -<?php echo e($product->discount_percentage); ?>%
                </span>
            <?php endif; ?>
            <?php if(!$product->isInStock()): ?>
                <span class="badge badge-glass badge-glass-soldout">
                    <?php echo e(__('Sold Out')); ?>

                </span>
            <?php else: ?>
                <?php if(($product->reviews_avg_rating && $product->reviews_avg_rating >= 4.5) || $product->id % 2 === 0): ?>
                    <span class="badge badge-glass badge-glass-bestseller">
                        <?php echo e(app()->getLocale() == 'ar' ? 'الأكثر مبيعاً' : (app()->getLocale() == 'fr' ? 'Bestseller' : 'Bestseller')); ?>

                    </span>
                <?php endif; ?>
                <?php if($product->is_new || $loop->index === 0): ?>
                    <span class="badge badge-glass badge-glass-new">
                        <?php echo e(app()->getLocale() == 'ar' ? 'جديد' : 'Nouveau'); ?>

                    </span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="product-image-wrapper position-relative text-center bg-white" style="padding-top: 20px; padding-bottom: 15px;">
        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="d-inline-block">
            <img src="<?php echo e($product->main_image ? (Str::startsWith($product->main_image,'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>"
                 alt="<?php echo e($product->translated_name); ?>" loading="lazy" class="object-fit-contain" style="width: 180px; height: 180px;">
        </a>
    </div>

    
    <div class="px-3 pb-3 d-flex flex-column flex-grow-1 bg-white text-start">
        
        
        <div class="d-flex align-items-center gap-1 mb-2 justify-content-start">
            <span class="x-small text-muted" style="font-family: 'Tajawal', sans-serif; font-size: 0.75rem;">(128)</span>
            <span class="x-small fw-bold text-dark" style="font-family: 'Tajawal', sans-serif; font-size: 0.8rem;">4.9</span>
            <?php for($i=0; $i<5; $i++): ?> 
                <i class="fas fa-star" style="font-size:.7rem; color:#f59e0b;"></i> 
            <?php endfor; ?>
        </div>

        
        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none mb-3">
            <h6 class="fw-bold text-dark lh-base hover-text-green transition-all m-0" style="font-family: 'Tajawal', sans-serif; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:2.6rem; font-size: 1rem;">
                <?php echo e($product->translated_name); ?>

            </h6>
        </a>

        
        <?php
            $activeVariants = $product->variants->where('status', 'active');
        ?>
        <?php if($activeVariants->count() > 0): ?>
            <div class="product-sizes-container mb-3 d-flex align-items-center justify-content-between">
                <div class="x-small text-muted" style="font-family: 'Tajawal', sans-serif; font-size: 0.8rem;">
                    <?php echo e(app()->getLocale() == 'ar' ? 'الحجم' : 'Taille'); ?>

                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end size-list-<?php echo e($product->id); ?>">
                    <?php $__currentLoopData = $activeVariants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" 
                                class="btn size-pill-btn <?php echo e($index === 0 ? 'border-green bg-green-light text-green fw-bold' : 'border-light bg-white text-dark'); ?>" 
                                onclick="selectProductSize(<?php echo e($product->id); ?>, <?php echo e($variant->id); ?>, '<?php echo e(currency($variant->display_price)); ?>', '<?php echo e($variant->isOnSale() ? currency($variant->price) : ''); ?>', <?php echo e($variant->isInStock() ? 'true' : 'false'); ?>)"
                                id="size-pill-<?php echo e($product->id); ?>-<?php echo e($variant->id); ?>">
                            <?php echo e($variant->size); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="mb-3 d-flex align-items-center justify-content-start gap-2">
            <div class="price-container-<?php echo e($product->id); ?> d-flex align-items-center gap-2">
                <?php
                    $firstVariant = $activeVariants->first();
                    $displayPrice = $firstVariant ? $firstVariant->display_price : ($product->sale_price ?: $product->price);
                    $originalPrice = $firstVariant ? $firstVariant->price : $product->price;
                    $isOnSale = $firstVariant ? $firstVariant->isOnSale() : $product->isOnSale();
                ?>
                
                <?php if($isOnSale): ?>
                    <div class="text-muted text-decoration-line-through original-price" style="font-family: 'Tajawal', sans-serif; font-size: 0.9rem;"><?php echo e(currency($originalPrice)); ?></div>
                    <div class="fw-bold text-dark fs-5 main-price" style="font-family: 'Tajawal', sans-serif;"><?php echo e(currency($displayPrice)); ?></div>
                <?php else: ?>
                    <div class="text-muted text-decoration-line-through original-price d-none" style="font-family: 'Tajawal', sans-serif; font-size: 0.9rem;"></div>
                    <div class="fw-bold text-dark fs-5 main-price" style="font-family: 'Tajawal', sans-serif;"><?php echo e(currency($displayPrice)); ?></div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="mt-auto">
            <?php
                $defaultVariantId = $firstVariant ? $firstVariant->id : 'null';
            ?>
            <button class="btn w-100 d-flex align-items-center justify-content-center gap-2 add-to-cart-btn-<?php echo e($product->id); ?>"
                    type="button"
                    onclick="addToCart(<?php echo e($product->id); ?>, <?php echo e($defaultVariantId); ?>, event)"
                    id="add-cart-btn-<?php echo e($product->id); ?>">
                <i class="fa-solid fa-bag-shopping"></i>
                <?php echo e(app()->getLocale() == 'ar' ? 'أضف إلى السلة' : 'Ajouter au panier'); ?>

            </button>
        </div>
    </div>
</div>




<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>