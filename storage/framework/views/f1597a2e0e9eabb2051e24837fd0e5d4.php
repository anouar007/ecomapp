<?php $__env->startSection('meta_title', 'الفريق — أساتذة ديوان للخط العربي'); ?>
<?php $__env->startSection('meta_description', 'تعرف على نخبة أساتذة الخط العربي في مركز ديوان.'); ?>

<?php $__env->startSection('content'); ?>

<section style="background-color: #f5f0e8; padding: 60px 0 80px;" dir="rtl">
    <div class="container">

        <div class="text-center mb-5">
            <h1 class="fw-bold mb-2" style="font-family: var(--font-heading); font-size: 2.5rem; color: #1c2410;">فريقنا</h1>
            <p class="text-muted" style="font-size: 0.95rem;">نخبة من أساتذة الخط العربي الأصيل</p>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <a href="<?php echo e(url('/team/ibrahim')); ?>" class="text-decoration-none">
                    <div class="bg-white rounded-3 overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,0.06);">
                        <div style="height: 300px; overflow: hidden;">
                            <img src="<?php echo e(asset('images/master_khalid_ahmed.png')); ?>" alt="الأستاذ محمد العسري" style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1" style="font-family: var(--font-heading); color: #1c2410; font-size: 1.05rem;">الأستاذ محمد العسري</h5>
                            <p class="text-muted mb-2" style="font-size: 0.82rem;">خطاط متخصص في خط الثلث والديواني</p>
                            <div style="color: #d4a843; font-size: 0.8rem; letter-spacing: 2px;">★★★★★</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(url('/team/abdulqasim')); ?>" class="text-decoration-none">
                    <div class="bg-white rounded-3 overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,0.06);">
                        <div style="height: 300px; overflow: hidden;">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=500" alt="الأستاذ عبد القاسم السوداني" style="width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1" style="font-family: var(--font-heading); color: #1c2410; font-size: 1.05rem;">الأستاذ عبد القاسم السوداني</h5>
                            <p class="text-muted mb-2" style="font-size: 0.82rem;">خبير خط الديواني والكوفي</p>
                            <div style="color: #d4a843; font-size: 0.8rem; letter-spacing: 2px;">★★★★★</div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="<?php echo e(url('/team/said')); ?>" class="text-decoration-none">
                    <div class="bg-white rounded-3 overflow-hidden" style="box-shadow: 0 2px 16px rgba(0,0,0,0.06);">
                        <div style="height: 300px; overflow: hidden;">
                            <img src="<?php echo e(asset('images/master_khalid_ahmed.png')); ?>" alt="الأستاذ سعيد الصدى" style="width: 100%; height: 100%; object-fit: cover; object-position: center; filter: saturate(0.7); transition: transform 0.5s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1" style="font-family: var(--font-heading); color: #1c2410; font-size: 1.05rem;">الأستاذ سعيد الصدى</h5>
                            <p class="text-muted mb-2" style="font-size: 0.82rem;">مؤرخ وفيلسوف الخط</p>
                            <div style="color: #d4a843; font-size: 0.8rem; letter-spacing: 2px;">★★★★☆</div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/team.blade.php ENDPATH**/ ?>