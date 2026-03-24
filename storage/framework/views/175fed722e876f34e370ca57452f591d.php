<?php $__env->startSection('meta_title', 'قائمة أمنياتي الملكية — ' . setting('app_name', 'Hijab Princesses')); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-surface section-py min-vh-100">
    <div class="container px-xl-5">
        
        <div class="d-flex flex-column mb-5" data-aos="fade-up">
            <h1 class="brand-heading h2 mb-2">قائمة أمنياتكِ الملكية</h1>
            <div class="bg-gold rounded" style="width: 60px; height: 4px;"></div>
        </div>

        <?php if($wishlistItems->count() > 0): ?>
        <div class="row g-3 g-lg-4">
            <?php $__currentLoopData = $wishlistItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $product = $item->product; ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up">
                <div class="brand-card border-0 shadow-sm h-100 bg-white overflow-hidden hvr-float">
                    <div class="position-relative">
                        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="d-block">
                            <?php $pImg = $product->main_image ? Storage::url($product->main_image) : asset('images/placeholder-product.jpg'); ?>
                            <img src="<?php echo e($pImg); ?>" class="card-img-top object-fit-cover" alt="<?php echo e($product->name); ?>" style="aspect-ratio: 3/4; height: auto;">
                        </a>
                        <button class="btn btn-white shadow-sm rounded-circle position-absolute top-0 end-0 m-2 wishlist-btn text-gold" 
                                onclick="removeFromWishlist(event, <?php echo e($product->id); ?>, this)"
                                data-product-id="<?php echo e($product->id); ?>"
                                style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--brand-gold-subtle);">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="card-body p-3 text-center">
                        <h6 class="brand-heading mb-2 h6 text-truncate">
                            <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="text-decoration-none text-dark hover-gold transition-300"><?php echo e($product->name); ?></a>
                        </h6>
                        <p class="text-gold fw-bold mb-3 font-body small"><?php echo e($product->formatted_price); ?></p>
                        <a href="<?php echo e(route('shop.show', $product->id)); ?>" class="btn-brand-outline w-100 py-1 small text-decoration-none">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            <?php echo e($wishlistItems->links()); ?>

        </div>
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <div class="mb-5 bg-gold-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 140px; height: 140px;">
                <i class="far fa-heart fa-4x text-gold opacity-50"></i>
            </div>
            <h2 class="brand-heading mb-3">قائمة أمنياتكِ خالية حالياً</h2>
            <p class="text-muted mb-5 font-body text-center mx-auto" style="max-width: 400px;">لا تدعي أجمل العبايات والخمارات تفوتكِ. أضيفيها هنا لتتسوقيها لاحقاً.</p>
            <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-primary px-5 py-3 text-decoration-none hvr-grow">اكتشفي التشكيلة الآن</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function removeFromWishlist(e, productId, btn) {
    Swal.fire({
        title: 'حذف من القائمة؟',
        text: "هل تريدين إزالة هذا المنتج من قائمة أمنياتكِ؟",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#c5a059',
        cancelButtonColor: '#1e293b',
        confirmButtonText: 'نعم، إزالة',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("<?php echo e(route('wishlist.toggle')); ?>", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'removed') {
                    btn.closest('.col-6').remove();
                    if(document.querySelectorAll('.col-6').length === 0) {
                        location.reload();
                    }
                    Swal.fire({ toast:true, position:'top-end', icon:'success', title:'تمت الإزالة بنجاح', showConfirmButton:false, timer:2000 });
                }
            })
            .catch(console.error);
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/wishlist/index.blade.php ENDPATH**/ ?>