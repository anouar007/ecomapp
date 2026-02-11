<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
</head>
<body>
    <h1>Invoice #{{ $invoice->invoice_number }}</h1>
    <p>Dear {{ $invoice->customer_name }},</p>
    <p>Please find attached your invoice for order #{{ $invoice->order ? $invoice->order->order_number : 'N/A' }}.</p>
    <p><strong>Total Amount:</strong> {{ $invoice->formatted_total_amount }}</p>
    <p>Thank you for your business!</p>
</body>
</html>
