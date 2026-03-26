<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?php echo e($invoice->invoice_number); ?></title>
</head>
<body>
    <h1>Invoice #<?php echo e($invoice->invoice_number); ?></h1>
    <p>Dear <?php echo e($invoice->customer_name); ?>,</p>
    <p>Please find attached your invoice for order #<?php echo e($invoice->order ? $invoice->order->order_number : 'N/A'); ?>.</p>
    <p><strong>Total Amount:</strong> <?php echo e($invoice->formatted_total_amount); ?></p>
    <p>Thank you for your business!</p>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/emails/invoice.blade.php ENDPATH**/ ?>