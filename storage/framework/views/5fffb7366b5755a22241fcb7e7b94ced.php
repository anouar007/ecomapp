<?php $__env->startSection('content'); ?>
<div style="padding: 24px; max-width: 1200px; margin: 0 auto;">
    <!-- Page Header with Actions -->
    <div style="margin-bottom: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <a href="<?php echo e(route('invoices.index')); ?>" style="color: #64748b; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo e(__('Back to Invoices')); ?>

                </a>
                <h1 style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0;"><?php echo e(__('Invoice')); ?> #<?php echo e($invoice->invoice_number); ?></h1>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="<?php echo e(route('invoices.download', $invoice)); ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-download"></i> <?php echo e(__('Download PDF')); ?>

                </a>
                <a href="<?php echo e(route('invoices.print', $invoice)); ?>" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-print"></i> <?php echo e(__('Print')); ?>

                </a>
                <?php if($invoice->canEdit()): ?>
                <a href="<?php echo e(route('invoices.edit', $invoice)); ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-edit"></i> <?php echo e(__('Edit')); ?>

                </a>
                <?php endif; ?>
                <?php if($invoice->remaining_balance > 0): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal" style="display: inline-flex; align-items: center; gap: 8px; background-color: #10b981; border-color: #10b981; color: white;">
                    <i class="fas fa-money-bill-wave"></i> <?php echo e(__('Record Payment')); ?>

                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Invoice Content Card -->
    <div style="background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 48px;">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 48px; border-bottom: 2px solid #f1f5f9; padding-bottom: 32px;">
                <!-- Company Info (Left) -->
                <div>
                    <?php if(setting('app_logo')): ?>
                        <img src="<?php echo e(asset('storage/' . setting('app_logo'))); ?>" alt="<?php echo e(__('Company Logo')); ?>" style="max-height: 80px; margin-bottom: 16px;">
                    <?php else: ?>
                        <h2 style="font-size: 32px; font-weight: 800; color: #1e293b; margin: 0 0 16px 0;"><?php echo e(setting('company_name', setting('app_name'))); ?></h2>
                    <?php endif; ?>
                    
                    <div style="color: #64748b; line-height: 1.6; font-size: 14px;">
                        <strong><?php echo e(setting('company_name', setting('app_name'))); ?></strong><br>
                        <?php echo e(setting('company_address')); ?><br>
                        <?php if(setting('company_phone')): ?> <?php echo e(__('Phone:')); ?> <?php echo e(setting('company_phone')); ?><br> <?php endif; ?>
                        <?php if(setting('company_email')): ?> <?php echo e(__('Email:')); ?> <?php echo e(setting('company_email')); ?><br> <?php endif; ?>
                        <div style="margin-top: 8px; font-size: 13px; color: #475569;">
                            <?php if(setting('company_tax_id')): ?> <span>ICE: <?php echo e(setting('company_tax_id')); ?></span><br> <?php endif; ?>
                            <?php if(setting('company_registry_id')): ?> <span>RC: <?php echo e(setting('company_registry_id')); ?></span><br> <?php endif; ?>
                            <?php if(setting('company_patente')): ?> <span>Patente: <?php echo e(setting('company_patente')); ?></span><br> <?php endif; ?>
                            <?php if(setting('company_fiscal_id')): ?> <span>IF: <?php echo e(setting('company_fiscal_id')); ?></span> <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Invoice Details (Right) -->
                <div style="text-align: right;">
                    <?php
                        $statusColors = [
                            'paid' => ['bg' => '#dcfce7', 'text' => '#166534'],
                            'unpaid' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                            'partial' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                        ];
                        $statusColor = $statusColors[$invoice->payment_status] ?? ['bg' => '#f1f5f9', 'text' => '#475569'];
                    ?>
                    <span style="background: <?php echo e($statusColor['bg']); ?>; color: <?php echo e($statusColor['text']); ?>; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 24px;">
                        <?php echo e($invoice->status_label); ?>

                    </span>
                    
                    <div style="display: grid; grid-template-columns: auto auto; gap: 8px 24px; text-align: right;">
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;"><?php echo e(__('Invoice No:')); ?></span>
                        <span style="color: #1e293b; font-weight: 700;"><?php echo e($invoice->invoice_number); ?></span>
                        
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;"><?php echo e(__('Date:')); ?></span>
                        <span style="color: #1e293b; font-weight: 600;"><?php echo e($invoice->issued_at->format('M d, Y')); ?></span>
                        
                        <?php if($invoice->due_date): ?>
                        <span style="color: #64748b; font-weight: 600; font-size: 13px;"><?php echo e(__('Due Date:')); ?></span>
                        <span style="color: #ef4444; font-weight: 600;"><?php echo e($invoice->due_date->format('M d, Y')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Client Info -->
            <div style="margin-bottom: 48px;">
                <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;"><?php echo e(__('Bill To')); ?></p>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-block; min-width: 300px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;"><?php echo e($invoice->customer_name); ?></h3>
                    <div style="color: #475569; font-size: 14px; line-height: 1.5;">
                        <?php if($invoice->customer_address): ?> <?php echo e($invoice->customer_address); ?><br> <?php endif; ?>
                        <?php if($invoice->customer_phone): ?> <?php echo e($invoice->customer_phone); ?><br> <?php endif; ?>
                        <?php if($invoice->customer_email): ?> <?php echo e($invoice->customer_email); ?> <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div style="margin-bottom: 48px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; border-radius: 8px 0 0 8px;"><?php echo e(__('Description')); ?></th>
                            <th style="padding: 12px 16px; text-align: center; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 100px;"><?php echo e(__('Qty')); ?></th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 150px;"><?php echo e(__('Unit Price')); ?></th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase; width: 150px; border-radius: 0 8px 8px 0;"><?php echo e(__('Total')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                                <div style="font-weight: 600; color: #1e293b;"><?php echo e($item->product_name); ?></div>
                                <?php if($item->product_sku): ?>
                                <div style="font-size: 12px; color: #94a3b8;"><?php echo e(__('SKU')); ?>: <?php echo e($item->product_sku); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 16px; text-align: center; color: #475569; border-bottom: 1px solid #f1f5f9;"><?php echo e($item->quantity); ?></td>
                            <td style="padding: 16px; text-align: right; color: #475569; border-bottom: 1px solid #f1f5f9;"><?php echo e($item->formatted_unit_price); ?></td>
                            <td style="padding: 16px; text-align: right; color: #1e293b; font-weight: 700; border-bottom: 1px solid #f1f5f9;"><?php echo e($item->formatted_total_price); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals & Legal -->
            <div style="display: flex; gap: 48px; border-top: 2px solid #f1f5f9; padding-top: 32px;">
                <div style="flex: 1;">
                    <!-- Amounts in Words & Notes -->
                    <div style="margin-bottom: 24px;">
                        <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;"><?php echo e(__('Total in Words')); ?></p>
                        <p style="background: #f8fafc; padding: 12px; border-radius: 8px; color: #334155; font-style: italic; border: 1px solid #e2e8f0;">
                            <?php echo e(__('Stopped this invoice at the sum of:')); ?> <strong><?php echo e($invoice->total_in_words); ?> <?php echo e(setting('currency_code', 'USD')); ?></strong>
                        </p>
                    </div>

                    <?php if($invoice->notes): ?>
                    <div style="margin-bottom: 24px;">
                        <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;"><?php echo e(__('Notes')); ?></p>
                        <p style="color: #475569; font-size: 14px;"><?php echo e($invoice->notes); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                         <p style="color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">Payment Method</p>
                         <p style="color: #1e293b; font-weight: 600;"><?php echo e(ucfirst(str_replace('_', ' ', $invoice->payment_method))); ?></p>
                    </div>
                </div>

                <div style="width: 350px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #64748b;">
                        <span><?php echo e(__('Subtotal (HT)')); ?></span>
                        <span style="font-weight: 600; color: #1e293b;"><?php echo e($invoice->formatted_subtotal); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #64748b;">
                        <span><?php echo e(__('Tax')); ?> (<?php echo e($invoice->tax_rate); ?>%)</span>
                        <span style="font-weight: 600; color: #1e293b;"><?php echo e($invoice->formatted_tax_amount); ?></span>
                    </div>
                    <?php if($invoice->discount_amount > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #10b981;">
                        <span><?php echo e(__('Discount')); ?></span>
                        <span style="font-weight: 600;">-<?php echo e($invoice->formatted_discount_amount); ?></span>
                    </div>
                    <?php endif; ?>
                    <div style="border-top: 2px solid #e2e8f0; margin-top: 16px; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 18px; font-weight: 800; color: #1e293b;"><?php echo e(__('Total (TTC)')); ?></span>
                        <span style="font-size: 24px; font-weight: 800; color: #3b82f6;"><?php echo e($invoice->formatted_total_amount); ?></span>
                    </div>
                </div>
            </div>
        </div>

<?php if($invoice->payments->count() > 0): ?>
            <!-- Payment History -->
            <div style="margin-top: 48px; border-top: 2px solid #f1f5f9; padding-top: 32px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px;"><?php echo e(__('Payment History')); ?></h3>
                
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo e(__('Date')); ?></th>
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo e(__('Method')); ?></th>
                            <th style="padding: 12px 16px; text-align: left; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo e(__('Reference')); ?></th>
                            <th style="padding: 12px 16px; text-align: right; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo e(__('Amount')); ?></th>
                            <th style="padding: 12px 16px; text-align: center; color: #475569; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo e(__('Proof')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #475569;"><?php echo e($payment->payment_date->format('M d, Y')); ?></td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-weight: 500;"><?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; color: #64748b;"><?php echo e($payment->transaction_reference ?? '-'); ?></td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: right; color: #10b981; font-weight: 600;"><?php echo e(number_format($payment->amount, 2)); ?></td>
                            <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                <?php if($payment->proof_file_path): ?>
                                <a href="<?php echo e(asset('storage/' . $payment->proof_file_path)); ?>" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                    <i class="fas fa-file-alt"></i> <?php echo e(__('View')); ?>

                                </a>
                                <?php else: ?>
                                <span style="color: #94a3b8;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>

        <!-- Legal Footer -->
        <div style="background: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; line-height: 1.6;">
            <p style="margin: 0;">
                <?php echo e(setting('company_name')); ?> - <?php echo e(setting('company_address')); ?>

            </p>
            <p style="margin: 0;">
                <?php if(setting('company_tax_id')): ?> ICE: <?php echo e(setting('company_tax_id')); ?> | <?php endif; ?>
                <?php if(setting('company_registry_id')): ?> RC: <?php echo e(setting('company_registry_id')); ?> | <?php endif; ?>
                <?php if(setting('company_patente')): ?> Patente: <?php echo e(setting('company_patente')); ?> | <?php endif; ?>
                <?php if(setting('company_fiscal_id')): ?> IF: <?php echo e(setting('company_fiscal_id')); ?> <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e(__('Record Payment')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('payments.store', $invoice)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Payment Amount')); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo e($invoice->remaining_balance); ?>" max="<?php echo e($invoice->remaining_balance); ?>" required>
                        </div>
                        <small class="text-muted"><?php echo e(__('Remaining Balance:')); ?> <?php echo e($invoice->formatted_remaining_balance); ?></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Payment Date')); ?></label>
                        <input type="date" name="payment_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Payment Method')); ?></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash"><?php echo e(__('Cash')); ?></option>
                            <option value="card"><?php echo e(__('Card')); ?></option>
                            <option value="bank_transfer"><?php echo e(__('Bank Transfer')); ?></option>
                            <option value="check"><?php echo e(__('Check')); ?></option>
                            <option value="other"><?php echo e(__('Other')); ?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Transaction Reference')); ?></label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="<?php echo e(__('e.g. Check Number, Transaction ID')); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Proof of Payment')); ?></label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo e(__('Notes')); ?></label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('Record Payment')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/invoices/show.blade.php ENDPATH**/ ?>