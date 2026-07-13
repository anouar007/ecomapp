<?php $__env->startSection('meta_title', $page->meta_title); ?>
<?php $__env->startSection('meta_description', $page->meta_description); ?>

<?php $__env->startSection('content'); ?>
    <?php if(!empty($page->custom_css)): ?>
        <?php $__env->startPush('styles'); ?>
            <style>
                <?php echo $page->custom_css; ?>

            </style>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>

    <div class="page-builder-content">
        <?php $__currentLoopData = $page->content ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('partials.page-builder-block', ['block' => $block], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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