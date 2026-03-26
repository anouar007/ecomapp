<?php $__env->startSection('dashboard_content'); ?>
<div class="d-flex flex-column mb-4" data-aos="fade-up">
    <h3 class="brand-heading h2 mb-2">تاريخ طلباتكِ الملكية</h3>
    <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
</div>

<div class="brand-card border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-up">
    <div class="table-responsive">
        <table class="table align-middle mb-0 font-body">
            <thead class="bg-gold-light border-bottom border-gold-subtle">
                <tr>
                    <th class="ps-4 py-3 small text-muted fw-bold">رقم الطلب</th>
                    <th class="py-3 small text-muted fw-bold">التاريخ</th>
                    <th class="py-3 small text-muted fw-bold">الحالة</th>
                    <th class="py-3 small text-muted fw-bold">الإجمالي</th>
                    <th class="pe-4 py-3 text-end small text-muted fw-bold">الإجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-bottom border-light hover-gold-subtle transition-300">
                    <td class="ps-4 fw-bold text-dark">#<?php echo e($order->order_number); ?></td>
                    <td class="text-muted small"><?php echo e($order->created_at->format('d M Y')); ?></td>
                    <td>
                        <?php
                            $statusMap = [
                                'pending' => ['bg' => 'bg-warning-subtle text-warning', 'text' => 'قيد الانتظار'],
                                'confirmed' => ['bg' => 'bg-info-subtle text-info', 'text' => 'تم التأكيد'],
                                'shipping' => ['bg' => 'bg-primary-subtle text-primary', 'text' => 'جاري الشحن'],
                                'delivered' => ['bg' => 'bg-success-subtle text-success', 'text' => 'تم التوصيل'],
                                'cancelled' => ['bg' => 'bg-danger-subtle text-danger', 'text' => 'ملغى'],
                            ];
                            $s = $statusMap[$order->status] ?? ['bg' => 'bg-secondary-subtle text-secondary', 'text' => $order->status];
                        ?>
                        <span class="badge <?php echo e($s['bg']); ?> rounded-pill px-3 py-1 font-body fw-normal" style="font-size: 0.7rem;"><?php echo e($s['text']); ?></span>
                    </td>
                    <td class="fw-bold text-dark"><?php echo e($order->formatted_total); ?></td>
                    <td class="pe-4 text-end">
                        <a href="<?php echo e(route('customer.orders.show', $order)); ?>" class="btn-brand-outline py-1 px-3 small text-decoration-none">
                            التفاصيل <i class="fas fa-search ms-1 small"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="fas fa-shopping-bag fa-4x"></i></div>
                        <h6 class="text-muted mb-4 font-body">لم نجد أي طلبات سابقة في حسابكِ.</h6>
                        <a href="<?php echo e(route('shop.index')); ?>" class="btn-brand-primary px-4">اكتشفي التشكيلة الآن</a>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($orders->hasPages()): ?>
    <div class="card-footer bg-white p-4 border-top-0">
        <?php echo e($orders->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/frontend/dashboard/orders.blade.php ENDPATH**/ ?>