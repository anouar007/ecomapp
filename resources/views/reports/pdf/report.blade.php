<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #1e293b;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #64748b;
        }
        .metrics {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .metric {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .metric-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f1f5f9;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #cbd5e1;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            color: #1e293b;
            border-left: 4px solid #3b82f6;
            padding-left: 12px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales Report</h1>
        <p>{{ $startDate->format('F d, Y') }} - {{ $endDate->format('F d, Y') }}</p>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="metrics">
        <div class="metric">
            <div class="metric-label">Total Revenue</div>
            <div class="metric-value">${{ number_format($metrics['total_revenue'], 2) }}</div>
        </div>
        <div class="metric">
            <div class="metric-label">Total Orders</div>
            <div class="metric-value">{{ number_format($metrics['total_orders']) }}</div>
        </div>
        <div class="metric">
            <div class="metric-label">Products Sold</div>
            <div class="metric-value">{{ number_format($metrics['products_sold']) }}</div>
        </div>
        <div class="metric">
            <div class="metric-label">Avg Order Value</div>
            <div class="metric-value">${{ number_format($metrics['avg_order_value'], 2) }}</div>
        </div>
    </div>

    <h2 class="section-title">Top Performing Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity Sold</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $product)
            <tr>
                <td>{{ $product['product_name'] }}</td>
                <td>{{ $product['quantity_sold'] }}</td>
                <td>${{ number_format($product['revenue'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="section-title">Revenue by Category</h2>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryBreakdown as $category)
            <tr>
                <td>{{ $category->category_name }}</td>
                <td>${{ number_format($category->revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>E-commerce Platform - Sales Report</p>
        <p>This report contains confidential business information</p>
    </div>
</body>
</html>
