<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo e($invoice->invoice_number); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 40px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .company-info h1 {
            font-size: 28px;
            color: #1e293b;
            margin-bottom: 10px;
        }
        
        .company-info p {
            color: #64748b;
            font-size: 14px;
        }
        
        .invoice-details {
            text-align: right;
        }
        
        .invoice-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        
        .status-paid {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-unpaid {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-partial {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .invoice-details .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .invoice-details .value {
            font-size: 16px;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .customer-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        
        .customer-section .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .customer-section h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .customer-section p {
            color: #475569;
            font-size: 14px;
            margin: 4px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .items-table th.text-center {
            text-align: center;
        }
        
        .items-table th.text-right {
            text-align: right;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
        }
        
        .items-table td {
            padding: 16px 12px;
        }
        
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table td.text-right {
            text-align: right;
        }
        
        .product-name {
            font-weight: 600;
            color: #1e293b;
        }
        
        .product-sku {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }
        
        .totals-section {
            margin-left: auto;
            width: 350px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .totals-row .label {
            color: #64748b;
            font-weight: 600;
        }
        
        .totals-row .value {
            color: #1e293b;
            font-weight: 700;
        }
        
        .totals-total {
            border-top: 2px solid #e2e8f0;
            margin-top: 10px;
            padding-top: 16px !important;
        }
        
        .totals-total .label {
            font-size: 18px;
            color: #1e293b;
        }
        
        .totals-total .value {
            font-size: 22px;
            color: #3b82f6;
        }
        
        .footer-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
        }
        
        .footer-section h4 {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .footer-section p {
            color: #475569;
            font-size: 14px;
            margin: 4px 0;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 13px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1><?php echo e(setting('company_name', setting('app_name'))); ?></h1>
                <p><?php echo e(setting('company_address')); ?><br>
                <?php if(setting('company_email')): ?>
                    Email: <?php echo e(setting('company_email')); ?><br>
                <?php endif; ?>
                <?php if(setting('company_phone')): ?>
                    Phone: <?php echo e(setting('company_phone')); ?><br>
                <?php endif; ?>
                <br>
                <?php if(setting('company_tax_id')): ?>
                    ICE: <?php echo e(setting('company_tax_id')); ?><br>
                <?php endif; ?>
                <?php if(setting('company_registry_id')): ?>
                    RC: <?php echo e(setting('company_registry_id')); ?>

                <?php endif; ?>
                </p>
            </div>
            <div class="invoice-details">
                <span class="invoice-status status-<?php echo e($invoice->payment_status); ?>">
                    <?php echo e($invoice->status_label); ?>

                </span>
                <div style="margin-top: 15px;">
                    <div class="label">Invoice Number</div>
                    <div class="value"><?php echo e($invoice->invoice_number); ?></div>
                    
                    <div class="label" style="margin-top: 10px;">Issue Date</div>
                    <div class="value"><?php echo e($invoice->issued_at->format('F d, Y')); ?></div>
                    
                    <?php if($invoice->due_date): ?>
                    <div class="label" style="margin-top: 10px;">Due Date</div>
                    <div class="value"><?php echo e($invoice->due_date->format('F d, Y')); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Customer Section -->
        <div class="customer-section">
            <div class="label">Bill To</div>
            <h3><?php echo e($invoice->customer_name); ?></h3>
            <?php if($invoice->customer_email): ?>
            <p><?php echo e($invoice->customer_email); ?></p>
            <?php endif; ?>
            <?php if($invoice->customer_phone): ?>
            <p><?php echo e($invoice->customer_phone); ?></p>
            <?php endif; ?>
            <?php if($invoice->customer_address): ?>
            <p><?php echo e($invoice->customer_address); ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center" style="width: 100px;">Quantity</th>
                    <th class="text-right" style="width: 120px;">Unit Price</th>
                    <th class="text-right" style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <div class="product-name"><?php echo e($item->product_name); ?></div>
                        <?php if($item->product_sku): ?>
                        <div class="product-sku">SKU: <?php echo e($item->product_sku); ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                    <td class="text-right"><?php echo e($item->formatted_unit_price); ?></td>
                    <td class="text-right"><?php echo e($item->formatted_total_price); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-row">
                <span class="label">Subtotal:</span>
                <span class="value"><?php echo e($invoice->formatted_subtotal); ?></span>
            </div>
            <div class="totals-row">
                <span class="label">Tax (<?php echo e($invoice->tax_rate); ?>%):</span>
                <span class="value"><?php echo e($invoice->formatted_tax_amount); ?></span>
            </div>
            <?php if($invoice->discount_amount > 0): ?>
            <div class="totals-row">
                <span class="label">Discount:</span>
                <span class="value">-<?php echo e($invoice->formatted_discount_amount); ?></span>
            </div>
            <?php endif; ?>
            <div class="totals-row totals-total">
                <span class="label">Total:</span>
                <span class="value"><?php echo e($invoice->formatted_total_amount); ?></span>
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="footer-section">
            <div>
                <h4>Payment Information</h4>
                <p><strong>Method:</strong> <?php echo e(ucfirst(str_replace('_', ' ', $invoice->payment_method))); ?></p>
                <p><strong>Created by:</strong> <?php echo e($invoice->creator->name ?? 'N/A'); ?></p>
            </div>
            <?php if($invoice->notes): ?>
            <div style="max-width: 45%;">
                <h4>Notes</h4>
                <p><?php echo e($invoice->notes); ?></p>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer Note -->
        <div class="footer-note">
            Thank you for your business!
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/invoices/print.blade.php ENDPATH**/ ?>