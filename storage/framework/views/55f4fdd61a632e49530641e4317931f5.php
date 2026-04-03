<div class="brand-card h-100 pcard border-0 position-relative" data-product-id="<?php echo e($product->id); ?>" data-aos="fade-up" data-aos-delay="<?php echo e((($loop->index ?? 0) % 4) * 50); ?>">
    
    <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="position-absolute top-0 start-0 w-100 h-100 z-1" aria-label="View <?php echo e($product->translated_name); ?>"></a>

    
    <div class="product-v2-image position-relative overflow-hidden" style="aspect-ratio: 4/5;">
        <img src="<?php echo e($product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>" 
             alt="<?php echo e($product->translated_name); ?> - Hijab Princesses" 
             class="w-100 h-100 object-fit-cover transition-hero"
             loading="lazy"
             decoding="async">
    </div>

    
    
    <div class="product-v2-body p-3 d-flex flex-column position-relative z-2" style="pointer-events: none;">
        <h5 class="brand-heading h6 mb-2 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; pointer-events: auto;"><?php echo e($product->translated_name); ?></h5>
        
        <div class="product-v2-price mb-3" style="pointer-events: auto;">
             <?php if($product->isOnSale()): ?>
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-<?php echo e($product->id); ?>"><?php echo e($product->formatted_sale_price); ?></span>
                <span class="price-old text-muted small text-decoration-line-through ms-2"><?php echo e($product->formatted_price); ?></span>
            <?php else: ?>
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-<?php echo e($product->id); ?>"><?php echo e($product->formatted_price); ?></span>
            <?php endif; ?>
        </div>

        
        <?php 
            $sizes = $product->available_sizes;
            $colors = $product->available_colors;
        ?>

        <?php if($product->variants->count() > 0 && ($colors->count() > 0 || $sizes->count() > 0)): ?>
        <div class="pcard-variants mb-4" style="pointer-events: auto;">
            <?php if($colors->count() > 0): ?>
            <div class="d-flex align-items-center gap-2 mb-2 pcard-variant-row">
                <?php $__currentLoopData = $colors->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($color->color_image_url): ?>
                        <div class="pcard-color-dot border shadow-sm" 
                             style="width: 34px; height: 34px; cursor: pointer; border-radius: 4px; overflow: hidden;"
                             onclick="selectCardVariant(<?php echo e($product->id); ?>, 'color', '<?php echo e($color->color); ?>', this, false); event.stopPropagation();"
                             title="<?php echo e($color->color); ?>">
                         <img src="<?php echo e($color->color_image_url); ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="<?php echo e($product->translated_name); ?> - <?php echo e($color->color); ?>">
                        </div>
                    <?php else: ?>
                        <div class="pcard-color-dot border shadow-sm" 
                             style="background: <?php echo e($color->color_code ?: '#eee'); ?>; width: 24px; height: 24px; cursor: pointer; border-radius: 4px;" 
                             onclick="selectCardVariant(<?php echo e($product->id); ?>, 'color', '<?php echo e($color->color); ?>', this, false); event.stopPropagation();"
                             title="<?php echo e($color->color); ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($colors->count() > 5): ?> <span class="text-muted small">+<?php echo e($colors->count() - 5); ?></span> <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($sizes->count() > 0): ?>
            <div class="d-flex flex-wrap gap-1 pcard-variant-row">
                <?php $__currentLoopData = $sizes->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pcard-size-pill border px-2 py-0 small text-muted rounded-pill" 
                     style="font-size: 0.65rem; cursor: pointer;"
                     onclick="selectCardVariant(<?php echo e($product->id); ?>, 'size', '<?php echo e($size); ?>', this, false); event.stopPropagation();">
                    <?php echo e($size); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($sizes->count() > 4): ?> <span class="text-muted small" style="font-size: 0.65rem;">+</span> <?php endif; ?>
            </div>
            <?php endif; ?>

            <input type="hidden" id="card-selected-variant-<?php echo e($product->id); ?>" value="">
        </div>
        <script>
            if (typeof window.cardVariants === 'undefined') window.cardVariants = {};
            window.cardVariants[<?php echo e($product->id); ?>] = <?php echo $product->variants_json; ?>;
        </script>
        <?php endif; ?>

        <?php if(!$product->isInStock()): ?>
        <div class="text-danger small mb-2"><i class="fas fa-exclamation-circle me-1"></i>نفذ من المخزن</div>
        <?php endif; ?>

        <div style="pointer-events: auto;" class="mt-auto">
            <button onclick="addToCart(<?php echo e($product->id); ?>); event.stopPropagation();" class="btn btn-brand-primary w-100 btn-sm py-2 rounded-pill d-flex align-items-center justify-content-center">
                أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
            </button>
        </div>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>