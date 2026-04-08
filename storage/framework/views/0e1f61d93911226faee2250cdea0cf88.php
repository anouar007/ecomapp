<?php $__env->startSection('meta_title', 'تم تأكيد طلبكِ بنجاح — ' . setting('app_name', 'Hijab Princesses')); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-surface py-5 min-vh-100 d-flex align-items-center">
    <div class="container px-xl-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6" data-aos="zoom-in">
                <div class="brand-card border-0 shadow-lg p-5 bg-white">
                    <div class="mb-5">
                        <div class="bg-gold-light text-gold rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 100px; height: 100px; border: 2px solid var(--brand-gold);">
                            <i class="fas fa-crown fa-3x"></i>
                        </div>
                    </div>
                    
                    <h1 class="brand-heading h2 mb-3">تهانينا، تم تأكيد طلبكِ!</h1>
                    <p class="text-muted mb-5 font-body lead-sm">شكراً لاختياركِ **Hijab Princesses**. لقد تلقينا طلبكِ بعناية وسنباشر بتجهيزه ليكون بين يديكِ في أقرب وقت.</p>
                    
                    <div class="bg-gold-light p-4 rounded-4 mb-5 text-start font-body border border-gold-subtle">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase">رقم الطلب</span>
                            <span class="fw-bold text-dark">#<?php echo e($order->order_number); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted small fw-bold text-uppercase">التاريخ</span>
                            <span class="fw-bold text-dark"><?php echo e($order->created_at->format('d M Y')); ?></span>
                        </div>
                        <div class="bg-gold opacity-25 my-3" style="height: 1px;"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-bold text-uppercase">الإجمالي المستحق</span>
                            <span class="fw-bold text-gold h4 mb-0"><?php echo e($order->formatted_total); ?></span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-primary w-100 py-3 text-decoration-none hvr-grow">
                                مواصلة التسوق <i class="fas fa-shopping-bag ms-2"></i>
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-top border-light">
                        <p class="h5 text-dark fw-bold font-body mb-2">سنتواصل معكِ عبر الهاتف لتأكيد موعد التوصيل.</p>
                        <p class="small text-muted font-body mb-0">يرجى إبقاء هاتفكِ متاحاً لاستلام المكالمة 📞</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/checkout/success.blade.php ENDPATH**/ ?>