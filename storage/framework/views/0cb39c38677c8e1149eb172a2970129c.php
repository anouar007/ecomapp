<?php $__env->startSection('meta_title', $product->translated_name . ' — ' . __('Ait') . ' ' . __('Oumdis')); ?>
<?php $__env->startSection('meta_description', Str::limit(strip_tags($product->translated_description), 155)); ?>
<?php $__env->startSection('meta_image', $product->main_image ? url(Storage::url($product->main_image)) : null); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Product Page ── */
.pdp-wrap { background: #f9fafb; min-height: 80vh; }

/* Gallery */
.pdp-main-img {
    border-radius: 24px; overflow: hidden;
    aspect-ratio: 4/5; position: relative;
    background: #fff;
    box-shadow: 0 8px 40px rgba(0,0,0,.08);
}
.pdp-main-img img { width:100%; height:100%; object-fit:cover; transition: transform .6s cubic-bezier(.4,0,.2,1); }
.pdp-main-img:hover img { transform: scale(1.04); }
.pdp-badge { position:absolute; top:16px; right:16px; background:#ef4444; color:#fff; font-size:.72rem; font-weight:800; padding:6px 12px; border-radius:100px; }
.pdp-thumbs { display:flex; gap:10px; margin-top:14px; overflow-x:auto; padding-bottom:4px; }
.thumb-item {
    width:76px; height:96px; flex-shrink:0; border-radius:14px; overflow:hidden;
    border:2.5px solid transparent; cursor:pointer;
    transition:border-color .2s, box-shadow .2s;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.thumb-item.border-green { border-color: #3BB878; box-shadow: 0 4px 16px rgba(59,184,120,.25); }
.thumb-item img { width:100%; height:100%; object-fit:cover; }

/* Info panel */
.pdp-info { background:#fff; border-radius:24px; padding:36px; box-shadow:0 8px 40px rgba(0,0,0,.06); height:fit-content; }
.pdp-cat { font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:1.5px; color:#3BB878; margin-bottom:8px; }
.pdp-title { font-size:clamp(1.5rem,3vw,2rem); font-weight:900; color:#111827; line-height:1.2; margin-bottom:16px; }
.pdp-rating { display:flex; align-items:center; gap:8px; margin-bottom:20px; }
.pdp-stars { font-size:.65rem; color:#f59e0b; }
.pdp-price { font-size:1.9rem; font-weight:900; color:#3BB878; }
.pdp-price-old { font-size:1rem; color:#9CA3AF; text-decoration:line-through; font-weight:600; }
.pdp-divider { height:1px; background:#f1f5f9; margin:24px 0; }
.pdp-desc { font-size:.93rem; color:#6B7280; line-height:1.75; margin-bottom:24px; }

/* Variant selectors */
.var-label { font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#374151; margin-bottom:10px; }
.size-pill {
    padding:9px 20px; border-radius:100px; font-size:.84rem; font-weight:700;
    cursor:pointer; border:2px solid #e5e7eb; color:#374151;
    transition:all .2s; white-space:nowrap;
}
.size-pill:hover { border-color:#3BB878; color:#3BB878; }
.size-pill.bg-green { background:#3BB878 !important; color:#fff !important; border-color:#3BB878; }

/* Qty + CTA */
.qty-wrap {
    display:flex; align-items:center; border:2px solid #e5e7eb;
    border-radius:100px; overflow:hidden; background:#f9fafb;
}
.qty-btn {
    width:42px; height:42px; border:none; background:transparent;
    font-size:.9rem; color:#374151; cursor:pointer; display:flex;
    align-items:center; justify-content:center; transition:background .2s;
}
.qty-btn:hover { background:#e8f7ef; color:#3BB878; }
.qty-input {
    width:48px; border:none; background:transparent; text-align:center;
    font-weight:800; font-size:1rem; color:#111827; outline:none;
}
.btn-atc {
    flex:1; background:#3BB878; color:#fff; border:none;
    border-radius:100px; font-weight:800; font-size:.95rem;
    padding:14px 24px; cursor:pointer; display:flex;
    align-items:center; justify-content:center; gap:8px;
    transition:all .3s cubic-bezier(.34,1.56,.64,1);
    box-shadow:0 6px 24px rgba(59,184,120,.3);
    white-space:nowrap;
}
.btn-atc:hover { background:#2f9461; transform:translateY(-2px); box-shadow:0 10px 32px rgba(59,184,120,.4); }
.btn-atc:active { transform:translateY(0); }

/* Trust badges */
.trust-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(110px, 1fr)); gap:12px; margin-top:24px; }
.trust-item {
    display:flex; align-items:center; gap:10px;
    background:#f9fafb; border-radius:12px; padding:12px;
    border:1px solid #f1f5f9;
}
.trust-icon { width:32px; height:32px; border-radius:50%; background:#e8f7ef; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.trust-text { font-size:.8rem; font-weight:700; color:#374151; }

/* Description tab */
.desc-section { background:#fff; border-radius:24px; padding:36px; box-shadow:0 4px 24px rgba(0,0,0,.05); margin-top:32px; }
.desc-section h3 { font-size:1.1rem; font-weight:800; color:#111827; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
.desc-section h3::after { content:''; flex:1; height:1px; background:#f1f5f9; }
.desc-body { font-size:.93rem; color:#6B7280; line-height:1.85; }

/* Stock indicator */
.stock-badge { display:inline-flex; align-items:center; gap:6px; font-size:.78rem; font-weight:700; }
.stock-dot { width:7px; height:7px; border-radius:50%; }

/* Breadcrumb */
    .hover-green { transition: all 0.3s; }
    .hover-green:hover { color: #3BB878 !important; }

    /* Shake animation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .shake { animation: shake 0.2s ease-in-out 0s 2; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="pdp-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>" class="text-green text-decoration-none fw-600"><?php echo e(__('Home')); ?></a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('shop.index')); ?>" class="text-green text-decoration-none fw-600"><?php echo e(__('Shop')); ?></a></li>
                <?php if($product->productCategory): ?>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('shop.index', ['category' => $product->productCategory->slug])); ?>" class="text-green text-decoration-none fw-600"><?php echo e($product->productCategory->translated_name); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active text-muted"><?php echo e(Str::limit($product->translated_name, 30)); ?></li>
            </ol>
        </nav>
    </div>
</div>


<div class="pdp-wrap py-5">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start">

            
            <div class="col-lg-6">
                <div class="pdp-main-img">
                    <img id="mainImage"
                         src="<?php echo e($product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg')); ?>"
                         alt="<?php echo e($product->translated_name); ?>">
                    <?php if($product->isOnSale()): ?>
                        <div class="pdp-badge">-<?php echo e($product->discount_percentage); ?>%</div>
                    <?php endif; ?>
                </div>

                <?php
                    $images = collect([$product->main_image])->merge($product->images->pluck('image_path'))->filter()->unique();
                ?>
                <?php if($images->count() > 1): ?>
                <div class="pdp-thumbs">
                    <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="thumb-item <?php echo e($loop->first ? 'border-green' : ''); ?>"
                         onclick="changeImage('<?php echo e(Storage::url($img)); ?>', this)">
                        <img src="<?php echo e(Storage::url($img)); ?>" alt="">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-6">
                <div class="pdp-info">

                        <?php if($product->variants->count() == 0): ?>
                            <?php if($product->isInStock()): ?>
                                <span class="stock-badge text-success">
                                    <span class="stock-dot bg-success"></span><?php echo e(__('In Stock')); ?>

                                </span>
                            <?php else: ?>
                                <span class="stock-badge text-danger">
                                    <span class="stock-dot bg-danger"></span><?php echo e(__('Out of Stock')); ?>

                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span id="variantStockBadge" class="stock-badge">
                                
                            </span>
                        <?php endif; ?>

                    
                    <h1 class="pdp-title"><?php echo e($product->translated_name); ?></h1>

                    
                    <div class="pdp-rating">
                        <div class="pdp-stars">
                            <?php for($i=0;$i<5;$i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                        </div>
                        <span class="small text-muted fw-600">(<?php echo e($product->reviews_count ?? rand(8,40)); ?> <?php echo e(__('reviews')); ?>)</span>
                    </div>

                    
                    <div class="d-flex align-items-baseline gap-3 mb-3" id="priceContainer">
                        <div class="pdp-price" id="displayPrice">
                            <?php echo e($product->isOnSale() ? $product->formatted_sale_price : $product->formatted_price); ?>

                        </div>
                        <?php if($product->isOnSale()): ?>
                            <div class="pdp-price-old" id="displayOldPrice"><?php echo e($product->formatted_price); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="pdp-divider"></div>

                    
                    <p class="pdp-desc"><?php echo e(Str::limit(strip_tags($product->translated_description), 200)); ?></p>

                    
                    <form id="pdpForm" onsubmit="handleAddToCart(event)">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="variant_id" id="selectedVariantId" value="">

                        <?php if($product->variants->count() > 0): ?>
                            
                            <?php $sizes = $product->getAvailableSizesAttribute(); ?>
                            <?php if($sizes->count() > 0): ?>
                                <div class="mb-4">
                                    <div class="var-label"><?php echo e(__('Select Size')); ?></div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php $__currentLoopData = $sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="size-pill"
                                             onclick="selectSize('<?php echo e($size); ?>', this)">
                                            <?php echo e($size); ?>

                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <div class="d-flex gap-3 mt-4 align-items-center" id="atc-wrapper">
                            
                            <div class="qty-wrap">
                                <button type="button" class="qty-btn" onclick="updatePdpQty(-1)">
                                    <i class="fas fa-minus" style="font-size:.65rem"></i>
                                </button>
                                <input type="number" id="pdpQty" value="1" min="1" readonly class="qty-input">
                                <button type="button" class="qty-btn" onclick="updatePdpQty(1)">
                                    <i class="fas fa-plus" style="font-size:.65rem"></i>
                                </button>
                            </div>
                            
                            <div id="atc-button-container" class="flex-grow-1">
                                <?php if($product->isInStock()): ?>
                                <button type="submit" class="btn-atc w-100">
                                    <i class="fas fa-shopping-bag"></i>
                                    <?php echo e(__('Add to Cart')); ?>

                                </button>
                                <?php else: ?>
                                <button type="button" class="btn-atc w-100" disabled style="background:#9CA3AF;box-shadow:none;cursor:not-allowed;">
                                    <i class="fas fa-times-circle"></i>
                                    <?php echo e(__('Out of Stock')); ?>

                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p id="stockLimitMsg" class="text-danger x-small fw-bold mt-2" style="display:none;">
                            <i class="fas fa-exclamation-triangle me-1"></i> <?php echo e(__('Only {stock} available in stock')); ?>

                        </p>
                    </form>

                    
                    <div class="pdp-divider"></div>
                    <div class="trust-grid">
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-shield-alt text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text"><?php echo e(__('Guaranteed Quality')); ?></span>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-truck text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text"><?php echo e(__('Fast Delivery')); ?></span>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon"><i class="fas fa-leaf text-green" style="font-size:.8rem"></i></div>
                            <span class="trust-text"><?php echo e(__('100% Natural')); ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        
        <div class="desc-section">
            <h3><?php echo e(__('Product Description')); ?></h3>
            <div class="desc-body">
                <?php echo nl2br(e($product->translated_description)); ?>

            </div>
        </div>

        
        <?php if(isset($relatedProducts) && $relatedProducts->count() > 0): ?>
        <div class="mt-5">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div>
                    <div class="section-label"><?php echo e(__('You May Also Like')); ?></div>
                    <h3 class="section-title mb-0"><?php echo e(__('Related Products')); ?></h3>
                </div>
            </div>
            <div class="row g-3">
                <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 80); ?>">
                    <?php echo $__env->make('frontend.partials.product_card_v2', ['product' => $rp], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const variants = <?php echo json_encode(json_decode($product->variants_json), 15, 512) ?>;
    let selectedSize = null;
    let currentStockLimit = <?php echo e($product->stock ?? 0); ?>;

    function changeImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumb-item').forEach(i => i.classList.remove('border-green'));
        el.classList.add('border-green');
    }

    function selectSize(size, el) {
        selectedSize = size;
        document.querySelectorAll('.size-pill').forEach(i => i.classList.remove('bg-green', 'text-white'));
        el.classList.add('bg-green', 'text-white');
        checkVariant();
    }

    function checkVariant() {
        const variant = variants.find(v => v.size == selectedSize);
        if (variant) {
            document.getElementById('selectedVariantId').value = variant.id;
            
            // Update Price HTML
            const priceDisplay = document.getElementById('displayPrice');
            const priceContainer = document.getElementById('priceContainer');
            
            priceDisplay.innerText = variant.formatted_price;
            
            // Handle Old Price (Sale)
            let oldPriceElem = document.getElementById('displayOldPrice');
            if (variant.is_on_sale) {
                if (!oldPriceElem) {
                    oldPriceElem = document.createElement('div');
                    oldPriceElem.id = 'displayOldPrice';
                    oldPriceElem.className = 'pdp-price-old';
                    priceContainer.appendChild(oldPriceElem);
                }
                oldPriceElem.innerText = variant.formatted_original_price;
            } else if (oldPriceElem) {
                oldPriceElem.remove();
            }
            
            // Update stock info
            currentStockLimit = variant.stock;
            updateStockUI(variant.stock);
            
            // Reset qty to 1 on size change
            const qtyInput = document.getElementById('pdpQty');
            qtyInput.value = 1;
            document.getElementById('stockLimitMsg').style.display = 'none';
        }
    }

    function updateStockUI(stock) {
        const badge = document.getElementById('variantStockBadge');
        const atcContainer = document.getElementById('atc-button-container');
        const qtyWrapper = document.querySelector('.qty-wrap');
        const limitMsg = document.getElementById('stockLimitMsg');

        if (!badge) return;

        if (stock > 0) {
            badge.innerHTML = `<span class="stock-dot bg-success"></span><span class="text-success"><?php echo e(__('In Stock')); ?> (${stock})</span>`;
            atcContainer.innerHTML = `
                <button type="submit" class="btn-atc w-100">
                    <i class="fas fa-shopping-bag"></i>
                    <?php echo e(__('Add to Cart')); ?>

                </button>`;
            qtyWrapper.style.opacity = '1';
            qtyWrapper.style.pointerEvents = 'auto';
            limitMsg.style.display = 'none';
        } else {
            badge.innerHTML = `<span class="stock-dot bg-danger"></span><span class="text-danger"><?php echo e(__('Out of Stock')); ?></span>`;
            atcContainer.innerHTML = `
                <button type="button" class="btn-atc w-100" disabled style="background:#9CA3AF;box-shadow:none;cursor:not-allowed;">
                    <i class="fas fa-times-circle"></i>
                    <?php echo e(__('Out of Stock')); ?>

                </button>`;
            qtyWrapper.style.opacity = '0.5';
            qtyWrapper.style.pointerEvents = 'none';
            limitMsg.style.display = 'none';
        }
    }

    function updatePdpQty(delta) {
        const inp = document.getElementById('pdpQty');
        let newVal = parseInt(inp.value) + delta;
        const limitMsg = document.getElementById('stockLimitMsg');

        if (newVal < 1) newVal = 1;
        
        if (newVal > currentStockLimit) {
            newVal = currentStockLimit;
            if (currentStockLimit > 0) {
                limitMsg.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> ${"<?php echo e(__('Only {stock} available in stock')); ?>".replace('{stock}', currentStockLimit)}`;
                limitMsg.style.display = 'block';
                
                // Shake effect on the input
                inp.classList.add('shake');
                setTimeout(() => inp.classList.remove('shake'), 500);
            }
        } else {
            limitMsg.style.display = 'none';
        }
        
        inp.value = newVal;
    }

    function handleAddToCart(e) {
        e.preventDefault();
        const variantId = document.getElementById('selectedVariantId').value;
        const qty = parseInt(document.getElementById('pdpQty').value);

        if (variants.length > 0 && !variantId) {
            Swal.fire({ 
                icon: 'warning', 
                title: '<?php echo e(__('Please select a size')); ?>', 
                text: '<?php echo e(__('Please choose a size before adding to cart.')); ?>',
                confirmButtonColor: '#3BB878'
            });
            return;
        }

        if (qty > currentStockLimit) {
            Swal.fire({ 
                icon: 'error', 
                title: '<?php echo e(__('Stock Limit')); ?>', 
                text: '<?php echo e(__('Sorry, we only have {stock} items left in stock.')); ?>'.replace('{stock}', currentStockLimit),
                confirmButtonColor: '#3BB878'
            });
            return;
        }

        fetch(`<?php echo e(url('/cart/add')); ?>/<?php echo e($product->id); ?>`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ quantity: qty, variant_id: variantId })
        }).then(r => r.json()).then(data => {
            if(data.success) {
                Swal.fire({ icon: 'success', title: '<?php echo e(__('Added to cart!')); ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                document.getElementById('header-cart-count').innerText = data.cartCount;
                refreshMiniCart();
                const miniCartElement = document.getElementById('miniCart');
                if (miniCartElement) {
                    const miniCart = new bootstrap.Offcanvas(miniCartElement);
                    miniCart.show();
                }
            } else {
                Swal.fire({ icon: 'error', title: '<?php echo e(__('Oops!')); ?>', text: data.message || '<?php echo e(__('Could not add item to cart.')); ?>' });
            }
        }).catch(err => {
            console.error('Cart Error:', err);
            Swal.fire({ icon: 'error', title: '<?php echo e(__('Error')); ?>', text: '<?php echo e(__('Something went wrong. Please try again.')); ?>' });
        });
    }

    // Initialize stock UI and default variant
    document.addEventListener('DOMContentLoaded', () => {
        if (variants.length === 0) {
            updateStockUI(<?php echo e($product->stock ?? 0); ?>);
        } else {
            // Auto-select the first variant pill
            const firstPill = document.querySelector('.size-pill');
            if (firstPill) {
                firstPill.click();
            }
        }
    });
</script>


<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "<?php echo e($product->translated_name); ?>",
  "image": [
    "<?php echo e($product->main_image ? url(Storage::url($product->main_image)) : asset('images/placeholder-product.jpg')); ?>"
  ],
  "description": "<?php echo e(Str::limit(strip_tags($product->translated_description), 160)); ?>",
  "sku": "<?php echo e($product->sku); ?>",
  "brand": {
    "@type": "Brand",
    "name": "<?php echo e(setting('app_name', 'Coop Ait Oumdis')); ?>"
  },
  "offers": {
    "@type": "Offer",
    "url": "<?php echo e(url()->current()); ?>",
    "priceCurrency": "MAD",
    "price": "<?php echo e($product->isOnSale() ? $product->sale_price : $product->price); ?>",
    "availability": "<?php echo e($product->isInStock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'); ?>",
    "itemCondition": "https://schema.org/NewCondition"
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/shop/show.blade.php ENDPATH**/ ?>