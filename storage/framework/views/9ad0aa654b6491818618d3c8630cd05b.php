<?php $__env->startSection('title', 'CPC (Income Statement)'); ?>
<?php $__env->startSection('subtitle', 'Période du : ' . $startDate . ' au ' . $endDate); ?>

<?php $__env->startSection('content'); ?>
    <table class="table">
        <thead>
            <tr>
                <th>Nature des produits et charges</th>
                <th class="text-end" style="width: 20%">Montant</th>
            </tr>
        </thead>
        <tbody>
            <!-- EXPLOITATION -->
            <tr class="section-header"><td colspan="2">I. PRODUITS D'EXPLOITATION</td></tr>
            <?php $__currentLoopData = $rows['exploitation']['produits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits d'Exploitation</td><td class="text-end"><?php echo e(number_format($exploitation['total_produits'], 2)); ?></td></tr>

            <tr class="section-header"><td colspan="2">II. CHARGES D'EXPLOITATION</td></tr>
            <?php $__currentLoopData = $rows['exploitation']['charges']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges d'Exploitation</td><td class="text-end"><?php echo e(number_format($exploitation['total_charges'], 2)); ?></td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>III. RÉSULTAT D'EXPLOITATION (I - II)</td>
                <td class="text-end"><?php echo e(number_format($resultatExploitation, 2)); ?></td>
            </tr>

            <!-- FINANCIER -->
            <tr class="section-header"><td colspan="2">IV. PRODUITS FINANCIERS</td></tr>
            <?php $__currentLoopData = $rows['financier']['produits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits Financiers</td><td class="text-end"><?php echo e(number_format($financier['total_produits'], 2)); ?></td></tr>

            <tr class="section-header"><td colspan="2">V. CHARGES FINANCIÈRES</td></tr>
            <?php $__currentLoopData = $rows['financier']['charges']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges Financières</td><td class="text-end"><?php echo e(number_format($financier['total_charges'], 2)); ?></td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>VI. RÉSULTAT FINANCIER (IV - V)</td>
                <td class="text-end"><?php echo e(number_format($resultatFinancier, 2)); ?></td>
            </tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #eaecf4; font-size: 1.1em;">
                <td>VII. RÉSULTAT COURANT (III + VI)</td>
                <td class="text-end"><?php echo e(number_format($resultatCourant, 2)); ?></td>
            </tr>

            <!-- NON COURANT -->
            <tr class="section-header"><td colspan="2">VIII. PRODUITS NON COURANTS</td></tr>
            <?php $__currentLoopData = $rows['non_courant']['produits']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Produits Non Courants</td><td class="text-end"><?php echo e(number_format($nonCourant['total_produits'], 2)); ?></td></tr>

            <tr class="section-header"><td colspan="2">IX. CHARGES NON COURANTES</td></tr>
            <?php $__currentLoopData = $rows['non_courant']['charges']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td style="padding-left: 20px;"><?php echo e($item['account']->code); ?> - <?php echo e($item['account']->name); ?></td>
                <td class="text-end"><?php echo e(number_format($item['balance'], 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr class="fw-bold bg-light"><td class="text-end">Total Charges Non Courantes</td><td class="text-end"><?php echo e(number_format($nonCourant['total_charges'], 2)); ?></td></tr>

            <tr class="fw-bold border-top border-dark" style="background-color: #f1f3f9;">
                <td>X. RÉSULTAT NON COURANT (VIII - IX)</td>
                <td class="text-end"><?php echo e(number_format($resultatNonCourant, 2)); ?></td>
            </tr>

            <!-- TOTAL -->
            <tr class="bg-primary fw-bold text-white border-top border-dark" style="font-size: 1.2em;">
                <td>XI. RÉSULTAT NET (VII + X)</td>
                <td class="text-end"><?php echo e(number_format($resultatNet, 2)); ?></td>
            </tr>

        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/accounting/reports/cpc_pdf.blade.php ENDPATH**/ ?>