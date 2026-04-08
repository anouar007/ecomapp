<div class="brand-card h-100 pcard pcard-reveal border-0 position-relative" 
     data-product-id="<?php echo e($product->id); ?>" 
     data-aos="fade-up" 
     data-aos-delay="<?php echo e((($loop->index ?? 0) % 4) * 50); ?>"
     style="--reveal-delay: <?php echo e((($loop->index ?? 0) % 8) * 0.1); ?>s">
    
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

        <?php if(!$product->isInStock()): ?>
        <div class="text-danger small mt-2"><i class="fas fa-exclamation-circle me-1"></i>نفذ من المخزن</div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/partials/product_card_v2.blade.php ENDPATH**/ ?>