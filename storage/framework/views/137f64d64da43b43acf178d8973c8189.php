<?php $__env->startSection('title', __('Navigation Management')); ?>

<?php $__env->startSection('content'); ?>
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <?php echo e(__('Navigation Menus')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__("Manage your website's header and footer navigation")); ?></p>
        </div>
        <button type="button" class="btn-brand-primary" data-bs-toggle="modal" data-bs-target="#createMenuModal">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Create New Menu')); ?>

        </button>
    </div>

    <div class="row g-4">
        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-6">
            <div class="brand-table-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h5 class="fw-bold mb-0"><?php echo e($menu->name); ?></h5>
                        <code class="small text-primary"><?php echo e($menu->location); ?></code>
                    </div>
                    <form action="<?php echo e(route('menus.destroy', $menu)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('Delete this menu?')); ?>')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger border-0">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                
                <form action="<?php echo e(route('menus.items.update', $menu)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="p-4" id="menu-items-<?php echo e($menu->id); ?>">
                        <div class="menu-items-list">
                            <?php $__currentLoopData = $menu->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row g-2 mb-3 align-items-center menu-item-row">
                                    <div class="col-md-5">
                                        <input type="text" name="items[<?php echo e($index); ?>][label]" class="form-control form-control-sm" value="<?php echo e($item->label); ?>" placeholder="<?php echo e(__('Label')); ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="items[<?php echo e($index); ?>][link]" class="form-control form-control-sm" value="<?php echo e($item->link); ?>" placeholder="<?php echo e(__('URL')); ?> (e.g. /about or https://...)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-light text-danger w-100" onclick="this.closest('.menu-item-row').remove()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMenuItem(<?php echo e($menu->id); ?>)">
                            <i class="fas fa-plus me-1"></i> <?php echo e(__('Add Link')); ?>

                        </button>
                    </div>
                    
                    <div class="p-4 bg-light border-top text-end">
                        <button type="submit" class="btn-brand-primary btn-sm">
                            <i class="fas fa-save me-1"></i> <?php echo e(__('Save Items')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Create Menu Modal -->
    <div class="modal fade" id="createMenuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <form action="<?php echo e(route('menus.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold"><?php echo e(__('New Navigation Menu')); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted"><?php echo e(__('MENU NAME')); ?></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Main Navigation" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted"><?php echo e(__('LOCATION IDENTIFIER')); ?></label>
                            <select name="location" class="form-select" required>
                                <option value="header"><?php echo e(__('Header')); ?></option>
                                <option value="footer_main"><?php echo e(__('Footer Main')); ?></option>
                                <option value="footer_links"><?php echo e(__('Footer Quick Links')); ?></option>
                                <option value="social_sidebar"><?php echo e(__('Social Sidebar')); ?></option>
                            </select>
                            <div class="form-text small"><?php echo e(__('This ID is used to fetch the menu in the frontend code.')); ?></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><?php echo e(__('Create Menu')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function addMenuItem(menuId) {
            const container = document.querySelector(`#menu-items-${menuId} .menu-items-list`);
            const index = container.querySelectorAll('.menu-item-row').length;
            
            const html = `
                <div class="row g-2 mb-3 align-items-center menu-item-row">
                    <div class="col-md-5">
                        <input type="text" name="items[${index}][label]" class="form-control form-control-sm" placeholder="<?php echo e(__('Label')); ?>">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="items[${index}][link]" class="form-control form-control-sm" placeholder="<?php echo e(__('URL')); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-light text-danger w-100" onclick="this.closest('.menu-item-row').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/menus/index.blade.php ENDPATH**/ ?>