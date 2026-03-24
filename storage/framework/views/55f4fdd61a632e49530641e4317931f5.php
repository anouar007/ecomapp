<div class="brand-card h-100 pcard border-0" data-product-id="<?php echo e($product->id); ?>">
    <div class="product-v2-image position-relative overflow-hidden" style="aspect-ratio: 4/5;">
        <img src="<?php echo e($product->main_image ? (Str::startsWith($product->main_image, 'http') ? $product->main_image : Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>" alt="<?php echo e($product->translated_name); ?>" class="w-100 h-100 object-fit-cover">
        <div class="product-v2-overlay">
            <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="btn-overlay brand-heading text-uppercase small" style="letter-spacing: 1px;">
                <i class="fas fa-eye me-2"></i> التفاصيل
            </a>
        </div>
    </div>
    <div class="product-v2-body p-3 d-flex flex-column">
        <h5 class="brand-heading h6 mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;"><?php echo e($product->translated_name); ?></h5>
        
        <div class="product-v2-price mb-3">
             <?php if($product->isOnSale()): ?>
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-<?php echo e($product->id); ?>"><?php echo e($product->formatted_sale_price); ?></span>
                <span class="price-old text-muted small text-decoration-line-through ms-2"><?php echo e($product->formatted_price); ?></span>
            <?php else: ?>
                <span class="price-sale h5 fw-bold text-gold mb-0" id="pcard-price-<?php echo e($product->id); ?>"><?php echo e($product->formatted_price); ?></span>
            <?php endif; ?>
        </div>

        
        <?php if($product->variants->count() > 0): ?>
        <div class="pcard-variants mb-3">
             <?php 
                $sizes = $product->available_sizes;
                $colors = $product->available_colors;
            ?>

            <?php if($colors->count() > 0): ?>
            <div class="d-flex align-items-center gap-2 mb-2">
                <?php $__currentLoopData = $colors->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pcard-color-dot border" 
                     style="background: <?php echo e($color->color_code ?: '#eee'); ?>; width: 14px; height: 14px; cursor: pointer; border-radius: 50%;" 
                     onclick="selectCardVariant(<?php echo e($product->id); ?>, 'color', '<?php echo e($color->color); ?>', this, false)"
                     title="<?php echo e($color->color); ?>">
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($colors->count() > 5): ?> <span class="text-muted small">+<?php echo e($colors->count() - 5); ?></span> <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($sizes->count() > 0): ?>
            <div class="d-flex flex-wrap gap-1">
                <?php $__currentLoopData = $sizes->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pcard-size-pill border px-2 py-0 small text-muted rounded-pill" 
                     style="font-size: 0.65rem; cursor: pointer;"
                     onclick="selectCardVariant(<?php echo e($product->id); ?>, 'size', '<?php echo e($size); ?>', this, false)">
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

        <button onclick="addToCart(<?php echo e($product->id); ?>)" class="btn-brand-primary w-100 btn-sm py-2 mt-auto">
            أضيفي للسلة <i class="fas fa-cart-plus ms-2"></i>
        </button>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>