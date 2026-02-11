<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #3b82f6; text-decoration: none; }
        .order-info { background: #f8fbff; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 12px; border-bottom: 2px solid #eee; color: #666; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .total-row td { border-bottom: none; font-weight: bold; font-size: 18px; color: #3b82f6; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #999; }
        .btn { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 30px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ url('/') }}" class="logo">{{ setting('app_name', 'Speed Store') }}</a>
        </div>

        <h2>Thank you for your order! 🎉</h2>
        <p>Hi {{ $order->shipping_name }},</p>
        <p>We've received your order and are getting it ready. We'll verify your details and let you know when it's on the way.</p>

        <div class="order-info">
            <strong>Order Number:</strong> #{{ $order->order_number }}<br>
            <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
            <strong>Status:</strong> {{ ucfirst($order->status) }}
        </div>

        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th style="text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="text-align: right;">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align: right;">Total:</td>
                    <td style="text-align: right;">{{ $order->formatted_total }}</td>
                </tr>
            </tbody>
        </table>

        <h3>Shipping Details</h3>
        <p>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
            {{ $order->shipping_country }}
        </p>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ route('customer.orders.show', $order) }}" class="btn">View Order Status</a>
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ setting('company_name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
