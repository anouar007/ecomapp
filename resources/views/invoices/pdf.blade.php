<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .invoice-container {
            padding: 30px;
        }
        
        .header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #3b82f6;
        }
        
        .header table {
            width: 100%;
        }
        
        .company-info h1 {
            font-size: 24px;
            color: #1e293b;
            margin-bottom: 8px;
        }
        
        .company-info p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .invoice-details {
            text-align: right;
        }
        
        .invoice-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
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
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .invoice-details .value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .customer-section {
            background: #f8fafc;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            border: 1px solid #e2e8f0;
        }
        
        .customer-section .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 6px;
        }
        
        .customer-section h3 {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 6px;
        }
        
        .customer-section p {
            color: #475569;
            font-size: 12px;
            margin: 3px 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        
        .items-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
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
            padding: 12px 8px;
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
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        
        .totals-section {
            margin-left: auto;
            width: 300px;
        }
        
        .totals-row {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .totals-row table {
            width: 100%;
        }
        
        .totals-row .label {
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
        }
        
        .totals-row .value {
            color: #1e293b;
            font-weight: 700;
            text-align: right;
            font-size: 13px;
        }
        
        .totals-total {
            border-top: 2px solid #e2e8f0;
            margin-top: 8px;
            padding-top: 12px !important;
        }
        
        .totals-total .label {
            font-size: 16px;
            color: #1e293b;
        }
        
        .totals-total .value {
            font-size: 18px;
            color: #3b82f6;
        }
        
        .footer-section {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-section table {
            width: 100%;
        }
        
        .footer-section h4 {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .footer-section p {
            color: #475569;
            font-size: 12px;
            margin: 3px 0;
        }
        
        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="company-info">
                            @if(setting('app_logo'))
                                <img src="{{ public_path('storage/' . setting('app_logo')) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
                            @else
                                <h1>{{ setting('company_name', setting('app_name')) }}</h1>
                            @endif
                            <p style="margin-top: 5px;">
                                <strong>{{ setting('company_name', setting('app_name')) }}</strong><br>
                                {{ setting('company_address') }}<br>
                            @if(setting('company_email'))
                                Email: {{ setting('company_email') }}<br>
                            @endif
                            @if(setting('company_phone'))
                                Phone: {{ setting('company_phone') }}<br>
                            @endif
                            <br>
                            @if(setting('company_tax_id')) ICE: {{ setting('company_tax_id') }}<br> @endif
                            @if(setting('company_registry_id')) RC: {{ setting('company_registry_id') }}<br> @endif
                            @if(setting('company_patente')) Patente: {{ setting('company_patente') }}<br> @endif
                            @if(setting('company_fiscal_id')) IF: {{ setting('company_fiscal_id') }} @endif
                            </p>
                        </div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="invoice-details">
                            <span class="invoice-status status-{{ $invoice->payment_status }}">
                                {{ $invoice->status_label }}
                            </span>
                            <div style="margin-top: 12px;">
                                <div class="label">Invoice Number</div>
                                <div class="value">{{ $invoice->invoice_number }}</div>
                                
                                <div class="label" style="margin-top: 8px;">Issue Date</div>
                                <div class="value">{{ $invoice->issued_at->format('F d, Y') }}</div>
                                
                                @if($invoice->due_date)
                                <div class="label" style="margin-top: 8px;">Due Date</div>
                                <div class="value">{{ $invoice->due_date->format('F d, Y') }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Customer Section -->
        <div class="customer-section">
            <div class="label">Bill To</div>
            <h3>{{ $invoice->customer_name }}</h3>
            @if($invoice->customer_email)
            <p>{{ $invoice->customer_email }}</p>
            @endif
            @if($invoice->customer_phone)
            <p>{{ $invoice->customer_phone }}</p>
            @endif
            @if($invoice->customer_address)
            <p>{{ $invoice->customer_address }}</p>
            @endif
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center" style="width: 80px;">Quantity</th>
                    <th class="text-right" style="width: 100px;">Unit Price</th>
                    <th class="text-right" style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        @if($item->product_sku)
                        <div class="product-sku">SKU: {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $item->formatted_unit_price }}</td>
                    <td class="text-right">{{ $item->formatted_total_price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-row">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value">{{ $invoice->formatted_subtotal }}</td>
                    </tr>
                </table>
            </div>
            <div class="totals-row">
                <table>
                    <tr>
                        <td class="label">Tax ({{ $invoice->tax_rate }}%):</td>
                        <td class="value">{{ $invoice->formatted_tax_amount }}</td>
                    </tr>
                </table>
            </div>
            @if($invoice->discount_amount > 0)
            <div class="totals-row">
                <table>
                    <tr>
                        <td class="label">Discount:</td>
                        <td class="value">-{{ $invoice->formatted_discount_amount }}</td>
                    </tr>
                </table>
            </div>
            @endif
            <div class="totals-row totals-total">
                <table>
                    <tr>
                        <td class="label">Total:</td>
                        <td class="value">{{ $invoice->formatted_total_amount }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Total Words -->
        <div style="margin-top: 20px; border: 1px solid #e2e8f0; padding: 10px; background: #f8fafc; border-radius: 4px;">
            <p style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total in Words</p>
            <p style="font-style: italic; color: #333;">Stopped this invoice at the sum of: <strong>{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong></p>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <table>
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Payment Information</h4>
                        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</p>
                        <p><strong>Created by:</strong> {{ $invoice->creator->name ?? 'N/A' }}</p>
                    </td>
                    @if($invoice->notes)
                    <td style="width: 50%; vertical-align: top;">
                        <h4>Notes</h4>
                        <p>{{ $invoice->notes }}</p>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
        
        <!-- Footer Note -->
        <div class="footer-note">
            <p style="margin-bottom: 4px;">{{ setting('company_name') }} - {{ setting('company_address') }}</p>
            <p>
                @if(setting('company_tax_id')) ICE: {{ setting('company_tax_id') }} | @endif
                @if(setting('company_registry_id')) RC: {{ setting('company_registry_id') }} | @endif
                @if(setting('company_patente')) Patente: {{ setting('company_patente') }} | @endif
                @if(setting('company_fiscal_id')) IF: {{ setting('company_fiscal_id') }} @endif
            </p>
            <p style="margin-top: 8px;">Thank you for your business!</p>
        </div>
    </div>
</body>
</html>
