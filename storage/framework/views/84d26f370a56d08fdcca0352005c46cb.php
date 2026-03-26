<?php $__env->startSection('title', __('Chart of Accounts')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Chart of Accounts')); ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
            <i class="fas fa-plus me-2"></i> <?php echo e(__('New Account')); ?>

        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="accountsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Code')); ?></th>
                            <th><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Type')); ?></th>
                            <th><?php echo e(__('Class')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="font-weight-bold"><?php echo e($account->code); ?></td>
                            <td><?php echo e($account->name); ?></td>
                            <td>
                                <span class="badge 
                                    <?php if($account->type == 'Asset'): ?> bg-info
                                    <?php elseif($account->type == 'Liability'): ?> bg-warning
                                    <?php elseif($account->type == 'Equity'): ?> bg-primary
                                    <?php elseif($account->type == 'Revenue'): ?> bg-success
                                    <?php elseif($account->type == 'Expense'): ?> bg-danger
                                    <?php else: ?> bg-secondary
                                    <?php endif; ?>">
                                    <?php echo e(__($account->type)); ?>

                                </span>
                            </td>
                            <td><?php echo e($account->class); ?></td>
                            <td>
                                <?php if($account->is_active): ?>
                                    <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo e(__('Inactive')); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <?php echo e($accounts->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('accounting.accounts.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('New Account')); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Code')); ?></label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. 5141">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Name')); ?></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Banque Populaire">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Type')); ?></label>
                        <select name="type" class="form-select" required>
                            <option value="Asset"><?php echo e(__('Asset')); ?></option>
                            <option value="Liability"><?php echo e(__('Liability')); ?></option>
                            <option value="Equity"><?php echo e(__('Equity')); ?></option>
                            <option value="Revenue"><?php echo e(__('Revenue')); ?></option>
                            <option value="Expense"><?php echo e(__('Expense')); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Class (PCGM)')); ?></label>
                        <select name="class" class="form-select" required>
                            <option value="1">1 - <?php echo e(__('Financement Permanent')); ?></option>
                            <option value="2">2 - <?php echo e(__('Actif Immobilisé')); ?></option>
                            <option value="3">3 - <?php echo e(__('Actif Circulant')); ?></option>
                            <option value="4">4 - <?php echo e(__('Passif Circulant')); ?></option>
                            <option value="5">5 - <?php echo e(__('Trésorerie')); ?></option>
                            <option value="6">6 - <?php echo e(__('Charges')); ?></option>
                            <option value="7">7 - <?php echo e(__('Produits')); ?></option>
                            <option value="8">8 - <?php echo e(__('Résultat')); ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('Create Account')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/accounting/accounts.blade.php ENDPATH**/ ?>