<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; border-left: 5px solid #3b82f6; }
        .header { margin-bottom: 20px; }
        .badge { background: #e0e7ff; color: #3b82f6; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .total { font-size: 24px; font-weight: bold; color: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">NEW ORDER</span>
            <h2>Order #{{ $order->order_number }}</h2>
        </div>

        <p>A new order has been placed by <strong>{{ $order->shipping_name }}</strong>.</p>
        
        <p class="total">{{ $order->formatted_total }}</p>

        <p>
            <strong>Payment:</strong> {{ ucfirst($order->payment_method) }} ({{ ucfirst($order->payment_status) }})<br>
            <strong>Items:</strong> {{ $order->items->count() }}
        </p>

        <p>Login to the dashboard to process this order.</p>

        <p>
            <a href="{{ route('orders.show', $order) }}" style="color: #3b82f6; text-decoration: none; font-weight: bold;">View Order in Admin Panel &rarr;</a>
        </p>
    </div>
</body>
</html>
