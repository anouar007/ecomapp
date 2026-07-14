<?php $__env->startSection('meta_title', 'تم تأكيد طلبكِ بنجاح — ' . setting('app_name', 'Hijab Princesses')); ?>

<?php $__env->startPush('styles'); ?>
<style>
.success-page {
    background-color: #ffffff;
    min-height: 100vh;
    padding: 2rem 0;
    font-family: 'Tajawal', 'Cairo', system-ui, -apple-system, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.success-card {
    background: #fff;
    border: none;
    box-shadow: none; 
    padding: 1rem;
    max-width: 500px;
    margin: 0 auto;
}
.crown-icon-wrap {
    width: 90px;
    height: 90px;
    border: 1.5px solid #c5a059;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #c5a059;
    font-size: 2.5rem;
    position: relative;
}
.crown-decor {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0; left: 0;
    pointer-events: none;
}
.crown-decor::before, .crown-decor::after {
    content: "✨";
    position: absolute;
    font-size: 1rem;
    color: #c5a059;
    opacity: 0.7;
}
.crown-decor::before { top: 10px; left: -15px; }
.crown-decor::after { bottom: 20px; right: -20px; }
.crown-decor-2::before {
    content: "✦";
    position: absolute;
    top: -10px; right: 10px;
    font-size: 0.8rem;
    color: #c5a059;
    opacity: 0.5;
}
.crown-decor-2::after {
    content: "✦";
    position: absolute;
    bottom: -5px; left: 10px;
    font-size: 0.6rem;
    color: #c5a059;
    opacity: 0.5;
}

.success-title {
    font-size: 1.6rem;
    font-weight: 900;
    color: #111827;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}
.success-title i {
    color: #c5a059;
    font-size: 1.4rem;
}
.success-subtitle {
    font-size: 0.95rem;
    color: #111827;
    font-weight: 600;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.order-summary-box {
    border: 1px solid #f5eee4;
    border-radius: 12px;
    padding: 1.25rem 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    background: #fff;
}
.order-summary-col {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
    flex-direction: row-reverse;
}
.order-summary-divider {
    width: 1px;
    height: 45px;
    background: #f5eee4;
    margin: 0 0.5rem;
}
.summary-icon {
    width: 36px;
    height: 36px;
    border: 1px solid #f5eee4;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c5a059;
    font-size: 1.1rem;
    background: #fff;
    flex-shrink: 0;
}
.summary-label {
    font-size: 0.75rem;
    color: #111827;
    font-weight: 800;
    margin-bottom: 0.2rem;
    text-align: right;
}
.summary-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: #111827;
    text-align: right;
}
.summary-value.price {
    color: #c5a059;
    font-size: 1rem;
    font-weight: 800;
}

.whats-next-divider {
    display: flex;
    align-items: center;
    text-align: center;
    color: #c5a059;
    font-weight: 800;
    font-size: 1.1rem;
    margin: 1.5rem 0 1.25rem;
}
.whats-next-divider::before,
.whats-next-divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #f5eee4;
}
.whats-next-divider::before { margin-left: 1rem; }
.whats-next-divider::after { margin-right: 1rem; }

.info-box {
    border: 1px solid #f5eee4;
    border-radius: 12px;
    padding: 0.8rem;
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    background: #ffffff;
    min-height: 60px;
    position: relative;
}
.info-box.highlight {
    background: #faf4ec;
    border-color: #faf4ec;
}
.info-icon {
    width: 40px;
    height: 40px;
    background: #faf4ec;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c5a059;
    font-size: 1.1rem;
    position: absolute;
    left: 1rem;
}
.info-box.highlight .info-icon {
    background: #ffffff;
}
.info-text {
    font-weight: 700;
    color: #111827;
    font-size: 0.9rem;
    flex: 1;
    text-align: center;
}
.info-box.highlight .info-text {
    display: flex;
    flex-direction: column;
}
.info-box.highlight .info-text small {
    font-weight: 700;
    color: #4b5563;
    font-size: 0.75rem;
    margin-bottom: 0.1rem;
}

.btn-store {
    background: #c5a059;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 1rem;
    width: 100%;
    font-weight: 800;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    transition: 0.2s;
}
.btn-store:hover {
    background: #a07840;
    color: #fff;
}

/* Compact Mobile Adjustments to fit without scrolling */
@media (max-width: 576px) {
    .success-page {
        padding: 0.5rem 0;
        min-height: calc(100vh - 120px); /* Adjusting for header/footer */
    }
    .success-card {
        padding: 0.5rem;
    }
    .crown-icon-wrap {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .success-title {
        font-size: 1.15rem;
        margin-bottom: 0.25rem;
    }
    .success-title i {
        font-size: 1.1rem;
    }
    .success-subtitle {
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
    }
    .order-summary-box {
        padding: 0.75rem 0.5rem;
        margin-bottom: 0.75rem;
    }
    .summary-icon {
        width: 28px;
        height: 28px;
        font-size: 0.9rem;
    }
    .summary-label {
        font-size: 0.65rem;
    }
    .summary-value {
        font-size: 0.75rem;
    }
    .summary-value.price {
        font-size: 0.85rem;
    }
    .order-summary-col {
        gap: 0.4rem;
    }
    .whats-next-divider {
        font-size: 0.95rem;
        margin: 0.75rem 0 0.5rem;
    }
    .info-box {
        padding: 0.5rem;
        min-height: 48px;
        margin-bottom: 0.4rem;
    }
    .info-icon {
        width: 30px;
        height: 30px;
        font-size: 0.9rem;
        left: 0.5rem;
    }
    .info-text {
        font-size: 0.8rem;
    }
    .info-box.highlight .info-text small {
        font-size: 0.65rem;
    }
    .btn-store {
        margin-top: 0.75rem;
        padding: 0.75rem;
        font-size: 0.95rem;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="success-page">
    <div class="container px-xl-5">
        <div class="success-card">
            
            <div class="text-center">
                <div class="crown-icon-wrap">
                    <i class="fas fa-crown"></i>
                    <div class="crown-decor"></div>
                    <div class="crown-decor-2"></div>
                </div>
                
                <h1 class="success-title">
                    تم استلام طلبك بنجاح
                    <i class="fas fa-check-circle"></i>
                </h1>
                
                <p class="success-subtitle">
                    <i class="fas fa-heart" style="color: #c5a059;"></i> شكراً لك على ثقتك في Hijab Princesses
                </p>
            </div>

            <div class="order-summary-box">
                <!-- Amount -->
                <div class="order-summary-col">
                    <div class="summary-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="summary-label">المبلغ عند الاستلام</div>
                        <div class="summary-value price" dir="ltr"><?php echo e($order->formatted_total); ?></div>
                    </div>
                </div>
                
                <div class="order-summary-divider"></div>
                
                <!-- Order ID -->
                <div class="order-summary-col">
                    <div class="summary-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div>
                        <div class="summary-label">رقم الطلب</div>
                        <div class="summary-value" style="font-size: 0.95rem;" dir="ltr"><?php echo e($order->order_number); ?>#</div>
                    </div>
                </div>
            </div>

            <div class="whats-next-divider">ماذا بعد؟</div>

            <div class="info-box">
                <div class="info-icon" style="border-radius: 50%;">
                    <i class="fas fa-phone-alt" style="transform: scaleX(-1);"></i>
                </div>
                <div class="info-text">
                    سنتصل بك عبر الهاتف لتأكيد الطلب
                </div>
            </div>

            <div class="info-box">
                <div class="info-icon" style="border-radius: 50%;">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="info-text">
                    سيصلك طلبك خلال 24 إلى 48 ساعة بإذن الله
                </div>
            </div>

            <div class="info-box highlight">
                <div class="info-icon" style="border-radius: 50%;">
                    <i class="fas fa-phone-alt" style="transform: scaleX(-1);"></i>
                </div>
                <div class="info-text">
                    <small>المرجو إبقاء هاتفك متاحاً</small>
                    حتى نتمكن من التواصل معك
                </div>
            </div>

            <a href="<?php echo e(route('shop.index')); ?>" class="btn-store text-decoration-none">
                العودة للمتجر <i class="fas fa-shopping-bag mx-1"></i> 
            </a>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/checkout/success.blade.php ENDPATH**/ ?>