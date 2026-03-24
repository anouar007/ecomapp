<?php $__env->startSection('meta_title', $page->meta_title . ' — ' . setting('app_name', 'Hijab Princesses')); ?>
<?php $__env->startSection('meta_description', $page->meta_description); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!empty($page->custom_css)): ?>
        <?php $__env->startPush('styles'); ?>
            <style>
                <?php echo $page->custom_css; ?>

            </style>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>

    <div class="bg-surface min-vh-100">
        
        <div class="bg-gold-light py-5 mb-5 border-bottom border-gold-subtle" data-aos="fade-down">
            <div class="container px-xl-5 text-center">
                <h1 class="brand-heading h2 mb-0"><?php echo e($page->title); ?></h1>
                <div class="bg-gold rounded mx-auto mt-3" style="width: 40px; height: 3px;"></div>
            </div>
        </div>

        
        <div class="container px-xl-5 pb-5">
            <div class="brand-card border-0 shadow-sm bg-white p-4 p-lg-5 font-body" data-aos="fade-up">
                <div class="page-builder-content">
                    <?php $__empty_1 = true; $__currentLoopData = $page->content ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('partials.page-builder-block', ['block' => $block], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                            <p>لا يوجد محتوى متاح لهذه الصفحة حالياً.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if(!empty($page->custom_js)): ?>
        <?php $__env->startPush('scripts'); ?>
            <script>
                <?php echo $page->custom_js; ?>

            </script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/page.blade.php ENDPATH**/ ?>