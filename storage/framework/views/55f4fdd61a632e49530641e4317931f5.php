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
                    onclick="addToCart(<?php echo e($product->id); ?>, <?php echo e($defaultVariantId); ?>)" 
                    id="add-cart-btn-<?php echo e($product->id); ?>">
                <i class="fa-solid fa-bag-shopping"></i> <?php echo e(app()->getLocale() == 'ar' ? 'أضف إلى السلة' : 'Ajouter au panier'); ?>

            </button>
        </div>
    </div>
</div>

<style>
/* Base Product Card */
.product-card {
    border: 1px solid rgba(14, 56, 32, 0.05) !important;
    border-radius: 20px !important;
    background: #fff !important;
    overflow: hidden !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01) !important;
    translate: 0 0;
    scale: 1;
    transition: translate 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), 
                box-shadow 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), 
                border-color 0.35s ease !important;
}

.product-card:hover {
    translate: 0 -10px;
    box-shadow: 0 25px 45px rgba(14, 56, 32, 0.07), 0 6px 18px rgba(0, 0, 0, 0.02) !important;
    border-color: rgba(191, 139, 67, 0.28) !important;
}

/* 3D Image Floating Platform & Scale */
.product-image-wrapper {
    background: #fff !important;
    overflow: visible !important;
}

.product-image-wrapper::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    translate: -50% 0;
    width: 140px;
    height: 10px;
    background: radial-gradient(ellipse at center, rgba(14, 56, 32, 0.07) 0%, rgba(14, 56, 32, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
    scale: 1;
    opacity: 1;
    transition: scale 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), 
                opacity 0.45s ease !important;
}

.product-card:hover .product-image-wrapper::after {
    scale: 0.78;
    opacity: 0.4;
}

.product-image-wrapper img {
    translate: 0 0;
    scale: 1;
    transition: translate 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), 
                scale 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
}

.product-card:hover .product-image-wrapper img {
    translate: 0 -8px;
    scale: 1.04;
}

/* Glassmorphic Badges */
.badge-glass {
    border-radius: 8px !important;
    padding: 6px 12px !important;
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    font-family: 'Tajawal', sans-serif;
    letter-spacing: 0.03em;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02) !important;
    border: 1px solid transparent;
    translate: 0 0;
    transition: translate 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), 
                box-shadow 0.3s ease, 
                border-color 0.3s ease !important;
}

.badge-glass-sale {
    background: rgba(239, 68, 68, 0.08) !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.22) !important;
}

.badge-glass-soldout {
    background: rgba(31, 41, 55, 0.07) !important;
    color: #1f2937 !important;
    border: 1px solid rgba(31, 41, 55, 0.18) !important;
}

.badge-glass-bestseller {
    background: rgba(191, 139, 67, 0.08) !important;
    color: #bf8b43 !important;
    border: 1px solid rgba(191, 139, 67, 0.22) !important;
}

.badge-glass-new {
    background: rgba(14, 56, 32, 0.07) !important;
    color: #0e3820 !important;
    border: 1px solid rgba(14, 56, 32, 0.2) !important;
}

.product-card:hover .badge-glass {
    translate: 0 -2px;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.04) !important;
}



/* Size Variants Toggles */
.size-pill-btn {
    font-weight: 500 !important;
    border-radius: 100px !important;
    padding: 5px 14px !important;
    font-size: 0.72rem !important;
    min-width: 45px;
    border: 1px solid rgba(14, 56, 32, 0.08) !important;
    background: #fff !important;
    color: #4b5563 !important;
    scale: 1;
    font-family: 'Tajawal', sans-serif !important;
    transition: scale 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
                border-color 0.25s ease,
                background-color 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease !important;
}

.size-pill-btn:hover {
    scale: 1.05;
    border-color: #0e3820 !important;
    color: #0e3820 !important;
    background-color: rgba(14, 56, 32, 0.02) !important;
}

.size-pill-btn.border-green {
    border-color: #bf8b43 !important;
    background-color: #fcf8f2 !important;
    color: #bf8b43 !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 6px rgba(191, 139, 67, 0.12) !important;
    scale: 1.05;
}

.size-pill-btn.border-green:hover {
    border-color: #bf8b43 !important;
    background-color: #fbf5eb !important;
    color: #bf8b43 !important;
}

/* Star Ratings Micro-Animation */
.product-card .d-flex.align-items-center.gap-1.mb-2 i {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
}

.product-card:hover .d-flex.align-items-center.gap-1.mb-2 i {
    transform: scale(1.1);
}

/* Premium Title & Text Transition */
.hover-text-green {
    transition: color 0.3s ease !important;
}

.hover-text-green:hover {
    color: #0e3820 !important;
}

/* Premium Gold-Shift Cart Button CTA */
.product-card button[id^="add-cart-btn-"] {
    position: relative;
    overflow: hidden;
    z-index: 1;
    border-radius: 12px !important;
    background: #0e3820 !important;
    border: 1px solid rgba(191, 139, 67, 0.3) !important;
    color: #fff !important;
    font-weight: 700 !important;
    padding: 12px 0 !important;
    font-family: 'Tajawal', sans-serif !important;
    box-shadow: 0 4px 12px rgba(14, 56, 32, 0.15) !important;
    translate: 0 0;
    transition: border-color 0.4s ease, 
                box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1), 
                translate 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.product-card button[id^="add-cart-btn-"]::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #bf8b43, #a97532);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.product-card button[id^="add-cart-btn-"]:hover {
    border-color: #bf8b43 !important;
    translate: 0 -2px;
    box-shadow: 0 8px 20px rgba(191, 139, 67, 0.35) !important;
}

.product-card button[id^="add-cart-btn-"]:hover::before {
    opacity: 1;
}

.product-card button[id^="add-cart-btn-"]:active {
    translate: 0 0;
}

.product-card button[id^="add-cart-btn-"] i {
    display: inline-block;
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
}

.product-card button[id^="add-cart-btn-"]:hover i {
    transform: scale(1.18) rotate(-8deg);
}

/* ── Specific Overrides for Premium Boutique Layout ── */
.pcard .product-image-wrapper {
    position: relative !important;
    padding-top: 20px !important;
    padding-bottom: 15px !important;
    height: 215px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: visible !important;
    background: #fff !important;
}

.pcard .product-image-wrapper a {
    display: inline-block !important;
    position: relative !important;
    z-index: 2 !important;
}

.pcard .product-image-wrapper img {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    bottom: auto !important;
    width: 180px !important;
    height: 180px !important;
    object-fit: contain !important;
    z-index: 2 !important;
    translate: 0 0;
    scale: 1;
    transition: translate 0.5s cubic-bezier(0.34, 1.56, 0.64, 1), 
                scale 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
}

.pcard:hover .product-image-wrapper img {
    translate: 0 -8px !important;
    scale: 1.04 !important;
}

.product-card:hover .hover-text-green {
    color: #0e3820 !important;
}
</style>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>