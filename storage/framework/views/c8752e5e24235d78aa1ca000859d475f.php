<?php $__env->startSection('title', 'Bilan'); ?>
<?php $__env->startSection('subtitle', 'Situation au : ' . $date); ?>

<?php $__env->startSection('content'); ?>
    <table style="width: 100%; border: none; margin-bottom: 0;">
        <tr>
            <td style="width: 48%; vertical-align: top; padding-right: 1%; border: none;">
                <h3 class="text-center bg-light" style="padding: 5px; border: 1px solid #e3e6f0; margin-top: 0;">ACTIF</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Eléments</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $actif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($section['accounts']) > 0 || $section['total'] != 0): ?>
                            <tr class="section-header">
                                <td colspan="2"><?php echo e($section['name']); ?></td>
                            </tr>
                            <?php $__currentLoopData = $section['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="fw-bold">
                                <td class="text-end">Total <?php echo e($section['name']); ?></td>
                                <td class="text-end"><?php echo e(number_format($section['total'], 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                         <tr class="bg-primary fw-bold" style="color: white;">
                             <td>TOTAL ACTIF</td>
                             <td class="text-end"><?php echo e(number_format($totalActif, 2)); ?></td>
                         </tr>
                    </tfoot>
                </table>
            </td>
            <td style="width: 48%; vertical-align: top; padding-left: 1%; border: none;">
                <h3 class="text-center bg-light" style="padding: 5px; border: 1px solid #e3e6f0; margin-top: 0;">PASSIF</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Eléments</th>
                            <th class="text-end">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $passif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($section['accounts']) > 0 || $section['total'] != 0): ?>
                            <tr class="section-header">
                                <td colspan="2"><?php echo e($section['name']); ?></td>
                            </tr>
                            <?php $__currentLoopData = $section['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr class="fw-bold">
                                <td class="text-end">Total <?php echo e($section['name']); ?></td>
                                <td class="text-end"><?php echo e(number_format($section['total'], 2)); ?></td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                         <tr class="bg-primary fw-bold" style="color: white;">
                             <td>TOTAL PASSIF</td>
                             <td class="text-end"><?php echo e(number_format($totalPassif, 2)); ?></td>
                         </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/accounting/reports/bilan_pdf.blade.php ENDPATH**/ ?>