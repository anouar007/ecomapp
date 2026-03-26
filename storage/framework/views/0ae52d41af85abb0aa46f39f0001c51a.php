<div class="card h-100 border border-light shadow-hover rounded-0 product-card overflow-hidden">
    <div class="position-relative overflow-hidden group">
        <a href="<?php echo e(route('shop.show', $product->id)); ?>">
            <?php if($product->main_image): ?>
            <img src="<?php echo e(Storage::url($product->main_image)); ?>" class="card-img-top p-3 transition-transform duration-500 group-hover:scale-110" alt="<?php echo e($product->translated_name); ?>" style="height: 220px; object-fit: contain;">
            <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center text-muted col-12" style="height: 220px;">
                <i class="fas fa-image fa-3x opacity-25"></i>
            </div>
            <?php endif; ?>
        </a>

        <!-- Badges -->
        <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-2">
            <?php if(isset($showDiscount) && $showDiscount && $product->isOnSale()): ?>
                <span class="badge bg-danger rounded-0 shadow-sm text-uppercase small">-<?php echo e($product->discount_percentage); ?>%</span>
            <?php endif; ?>
            <?php if(!$product->isInStock()): ?>
                <span class="badge bg-secondary rounded-0 shadow-sm text-uppercase small">Out of Stock</span>
            <?php elseif($product->created_at->diffInDays(now()) < 7): ?>
                <span class="badge bg-primary rounded-0 shadow-sm text-uppercase small">New</span>
            <?php elseif($product->hasLowStock()): ?>
                <span class="badge bg-warning text-dark rounded-0 shadow-sm text-uppercase small">Low Stock</span>
            <?php endif; ?>
        </div>

         <!-- Hover Actions (Bottom Slide Up) -->
         <div class="position-absolute bottom-0 start-0 w-100 p-2 product-actions d-flex justify-content-center gap-2 pb-3 bg-white bg-opacity-90 transition-transform translate-y-full group-hover:translate-y-0" style="z-index: 2;">
             <?php if($product->isInStock()): ?>
             <button class="btn btn-sm btn-primary rounded-0 square-icon" onclick="addToCart(<?php echo e($product->id); ?>)" title="Add to Cart">
                 <i class="fas fa-shopping-cart"></i>
             </button>
             <?php else: ?>
             <button class="btn btn-sm btn-secondary rounded-0 square-icon" disabled title="Out of Stock" style="cursor: not-allowed;">
                 <i class="fas fa-shopping-cart"></i>
             </button>
             <?php endif; ?>
             <button class="btn btn-sm btn-outline-dark rounded-0 square-icon" onclick="openQuickView(<?php echo e($product->id); ?>)" title="Quick View">
                 <i class="fas fa-eye"></i>
             </button>
         </div>
    </div>
    
    <div class="card-body p-3 pt-2 text-center border-top border-light">
        <div class="mb-1 text-muted small text-uppercase fw-bold ls-1" style="font-size: 10px"><?php echo e($product->category_name); ?></div>
        <h6 class="card-title fw-bold mb-2 text-truncate">
            <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none text-dark stretched-link hover-text-primary" style="font-size: 14px"><?php echo e($product->translated_name); ?></a>
        </h6>
        
        <div class="d-flex justify-content-center align-items-center gap-2">
            <h5 class="fw-bold text-primary m-0 fs-6" id="pcard-price-slider-<?php echo e($product->id); ?>"><?php echo e($product->formatted_price); ?></h5>
            <?php if($product->isOnSale()): ?>
                <small class="text-decoration-line-through text-muted" style="font-size: 11px"><?php echo e($product->formatted_sale_price); ?></small>
            <?php endif; ?>
        </div>

        
        <?php if($product->variants->count() > 0): ?>
        <div class="pcard-variants justify-content-center mt-3">
             <?php 
                $sizes = $product->available_sizes;
                $colors = $product->available_colors;
            ?>

            <?php if($colors->count() > 0): ?>
            <div class="pcard-variant-row justify-content-center">
                <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pcard-color-dot" 
                     style="background: <?php echo e($color->color_code ?: '#eee'); ?>" 
                     onclick="selectCardVariant(<?php echo e($product->id); ?>, 'color', '<?php echo e($color->color); ?>', this, true)"
                     title="<?php echo e($color->color); ?>">
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <?php if($sizes->count() > 0): ?>
            <div class="pcard-variant-row justify-content-center mt-1">
                <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pcard-size-pill" 
                     onclick="selectCardVariant(<?php echo e($product->id); ?>, 'size', '<?php echo e($size); ?>', this, true)">
                    <?php echo e($size); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>

            <input type="hidden" id="card-selected-variant-slider-<?php echo e($product->id); ?>" value="">
            <script>
                if (typeof cardVariants === 'undefined') window.cardVariants = {};
                window.cardVariants[<?php echo e($product->id); ?>] = <?php echo $product->variants_json; ?>;
            </script>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .square-icon {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hover-text-primary:hover {
        color: var(--primary-color) !important;
    }
    .group:hover .product-actions {
        transform: translateY(0) !important;
    }
    /* Fallback for translating */
    .translate-y-full { transform: translateY(100%); }
    .transition-transform { transition: transform 0.3s ease; }
</style>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_slider.blade.php ENDPATH**/ ?>