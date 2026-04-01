<?php $__env->startSection('title', __('Categories Management')); ?>

<?php $__env->startSection('content'); ?>
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-folder-tree"></i>
                </div>
                <?php echo e(__('Categories Management')); ?>

            </h1>
            <p class="brand-subtitle"><?php echo e(__('Organize and manage your product hierarchy and taxonomy')); ?></p>
        </div>
        <a href="<?php echo e(route('categories.create')); ?>" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('Create Category')); ?>

        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="brand-filter-bar px-3 py-2">
        <form action="<?php echo e(route('categories.index')); ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-8 col-lg-10">
                <div class="brand-search-wrapper w-100">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           value="<?php echo e(request('search')); ?>" 
                           placeholder="<?php echo e(__('Search...')); ?>">
                </div>
            </div>
            
            <div class="col-4 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-brand-primary w-100 py-2 px-0">
                    <i class="fas fa-filter"></i>
                </button>
                
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-brand-light py-2 px-3" title="<?php echo e(__('Clear')); ?>">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Mobile Categories List -->
    <div class="d-lg-none mt-3 px-1">
        <?php $__empty_1 = true; $__currentLoopData = $categories->where('parent_id', null); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('categories.partials.category-card', ['category' => $category, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <?php if(!request('search')): ?>
                <?php $__currentLoopData = $category->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('categories.partials.category-card', ['category' => $child, 'level' => 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    
                    <?php $__currentLoopData = $child->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('categories.partials.category-card', ['category' => $grandchild, 'level' => 2], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="glass-card p-5 text-center">
                <i class="fas fa-folder-open opacity-25 mb-3" style="font-size: 48px;"></i>
                <h5 class="fw-bold"><?php echo e(__('No categories found')); ?></h5>
            </div>
        <?php endif; ?>
    </div>

    <!-- Categories Table (Desktop Only) -->
    <div class="brand-table-card d-none d-lg-block mt-4">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;"><?php echo e(__('Category Hierarchy')); ?></th>
                        <th><?php echo e(__('Slug / Identifier')); ?></th>
                        <th class="text-center"><?php echo e(__('Assigned Products')); ?></th>
                        <th class="text-center"><?php echo e(__('Status')); ?></th>
                        <th class="text-end" style="padding-right: 1.5rem;"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories->where('parent_id', null); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo $__env->make('categories.partials.category-row', ['category' => $category, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        
                        <?php if(!request('search')): ?> 
                            <?php $__currentLoopData = $category->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $__env->make('categories.partials.category-row', ['category' => $child, 'level' => 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                
                                <?php $__currentLoopData = $child->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grandchild): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo $__env->make('categories.partials.category-row', ['category' => $grandchild, 'level' => 2], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h5 class="fw-bold text-dark"><?php echo e(__('No categories found')); ?></h5>
                                <p class="text-muted"><?php echo e(__('Start by creating your first product category.')); ?></p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/categories/index.blade.php ENDPATH**/ ?>