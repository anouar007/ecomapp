<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }} {{ $invoice->invoice_number }}</title>
    <!-- Font Awesome 4.7 (better compatibility with DomPDF) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: {{ app()->getLocale() === 'ar' ? '"DejaVu Sans", sans-serif' : '"Helvetica", "Arial", sans-serif' }};
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 13px;
        }
        .accent-bar {
            height: 8px;
            background: #6366f1;
            width: 100%;
        }
        .container {
            padding: 40px 50px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 40px;
        }
        .company-name {
            font-size: 38px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 10px 0;
            line-height: 1;
        }
        .company-info p {
            color: #64748b;
            margin: 2px 0;
            font-size: 13px;
        }
        .invoice-title-block {
            text-align: right;
        }
        .label-sm {
            color: #94a3b8;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .value-lg {
            color: #1e293b;
            font-weight: bold;
            font-size: 18px;
        }
        .value-md {
            color: #1e293b;
            font-weight: bold;
            font-size: 14px;
        }
        .section-title {
            color: #6366f1;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        .client-name {
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #1e293b;
        }
        .client-info p {
            color: #475569;
            margin: 3px 0;
            font-size: 14px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
            border: 1px solid #f1f5f9;
        }
        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .totals-table {
            width: 320px;
            border-spacing: 0;
            background: #f8fafc;
            border-radius: 16px;
        }
        .totals-table td {
            padding: 10px 20px;
        }
        .grand-total {
            border-top: 1px dashed #e2e8f0;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }
        .total-value {
            font-size: 28px;
            font-weight: bold;
            color: #6366f1;
        }
        .words-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            margin-bottom: 30px;
        }
        .footer {
            position: fixed;
            bottom: 30px;
            left: 50px;
            right: 50px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
            font-size: 11px;
            color: #94a3b8;
        }
        .fiscal-tag {
            background: #f8fafc;
            padding: 2px 6px;
            border: 1px solid #f1f5f9;
            margin-right: 5px;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="accent-bar"></div>
    <div class="container" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 60%; vertical-align: top; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                    <div class="company-info">
                        <h1 class="company-name">{{ setting('company_name', setting('app_name')) }}</h1>
                        <p style="margin-bottom: 5px;">{{ setting('company_address') }}</p>
                        <div style="margin-top: 8px;">
                            @if(setting('company_email')) 
                            <span style="margin-right: 15px; display: inline-block;">
                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNjM2NmYxIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTQgNGgxNmMxLjEgMCAyIC45IDIgMnYEyYzAgMS4xLS45IDItMiAyaC0xNmMtMS4xIDAtMi0uOS0yLTJWNmMwLTEuMS45LTIgMi0yeiIvPjxwb2x5bGluZSBwb2ludHM9IjIyLDYgMTIsMTMgMiw2Ii8+PC9zdmc+" style="width: 8px; height: 8px; vertical-align: middle; margin-right: 4px;">
                                <span style="color: #64748b; font-size: 11px;">{{ setting('company_email') }}</span>
                            </span> 
                            @endif
                            @if(setting('company_phone')) 
                            <span style="display: inline-block;">
                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjNjM2NmYxIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTIyIDE2LjkydjNhMiAyIDAgMCAxLTIuMTggMiAxOS43OSAxOS43OSIDAgMCAxLTguNjMtMy4wNyAxOS41IDE5LjUgMCAwIDEtNi02IDE5Ljc5IDE5Ljc5IDAgMCAxLTMuMDctOC42N0EyIDIgMCAwIDEgNC4xMSAyaDNhMiAyIDAgMCAxIDIgMS43MiAxMi44NCAxMi44NCAwIDAgMCAuNyAyLjgxIDIgMiAwIDAgMS0uNDUgMi4xMUw4LjA5IDkuOTFhMTYgMTYgMCAwIDAgNiA2bDEuMjctMS4yN2EyIDIgMCAwIDEgMi4xMS0uNDUgMTIuODQgMTIuODQgMCAwIDAgMi44MS43QTIgMiAwIDAgMSAyMiAxNi45MnoiLz48L3N2Zz4=" style="width: 8px; height: 8px; vertical-align: middle; margin-right: 4px;">
                                <span style="color: #64748b; font-size: 11px;">{{ setting('company_phone') }}</span>
                            </span> 
                            @endif
                        </div>
                        <div style="margin-top: 15px;">
                            @if(setting('company_tax_id')) <span class="fiscal-tag">{{ __('ICE') }}: {{ setting('company_tax_id') }}</span> @endif
                            @if(setting('company_registry_id')) <span class="fiscal-tag">{{ __('RC') }}: {{ setting('company_registry_id') }}</span> @endif
                            @if(setting('company_patente')) <span class="fiscal-tag">{{ __('Patente') }}: {{ setting('company_patente') }}</span> @endif
                            @if(setting('company_fiscal_id')) <span class="fiscal-tag">{{ __('IF') }}: {{ setting('company_fiscal_id') }}</span> @endif
                        </div>
                    </div>
                </td>
                <td style="width: 40%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: top;">
                    @if(setting('app_logo') && file_exists(public_path('storage/' . setting('app_logo'))))
                        <div style="margin-bottom: 20px;">
                            @php
                                $path = public_path('storage/' . setting('app_logo'));
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_get_contents($path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            @endphp
                            <img src="{{ $base64 }}" alt="Logo" style="max-width: 150px; height: auto;">
                        </div>
                    @endif
                    <div class="invoice-title-block">
                        <div class="label-sm">{{ $invoice->getNumberLabel() }}</div>
                        <div class="value-lg">#{{ $invoice->invoice_number }}</div>
                        <div class="label-sm" style="margin-top: 10px;">{{ __('Issue Date') }}</div>
                        <div class="value-md">{{ $invoice->issued_at->translatedFormat('d M, Y') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Client Section -->
        <div style="margin-bottom: 40px; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
            <div class="section-title">{{ $invoice->getBillToLabel() }}</div>
            <div class="client-info">
                <h3 class="client-name">{{ $invoice->customer_name }}</h3>
                @if($invoice->ice)
                <p style="margin-bottom: 5px;"><span class="fiscal-tag" style="background: #f1f5f9; padding: 2px 6px; border-radius: 3px; font-size: 10px;">{{ __('ICE') }}: {{ $invoice->ice }}</span></p>
                @endif
                @php
                    $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null);
                @endphp
                <div style="margin-top: 5px;">
                    @if($address) 
                    <div style="margin-bottom: 5px;">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOTRhM2I4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTIxIDEwYzAgNy05IDEzLTkgMTNzLTktNi05LTEzYTkgOSAwIDAgMSAxOCAwemIvPjxjaXJjbGUgY3g9IjEyIiBjeT0iMTAiIHI9IjMiLz48L3N2Zz4=" style="width: 8px; height: 8px; vertical-align: middle; margin-right: 6px;">
                        <span style="color: #475569; font-size: 12px;">{{ $address }}</span>
                    </div> 
                    @endif
                    @if($invoice->customer_phone) 
                    <div style="margin-bottom: 5px;">
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOTRhM2I4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTIyIDE2LjkydjNhMiAyIDAgMCAxLTIuMTggMiAxOS43OSAxOS43OSIDAgMCAxLTguNjMtMy4wNyAxOS41IDE5LjUgMCAwIDEtNi02IDE5Ljc5IDE5Ljc5IDAgMCAxLTMuMDctOC42N0EyIDIgMCAwIDEgNC4xMSAyaDNhMiAyIDAgMCAxIDIgMS43MiAxMi44NCAxMi44NCAwIDAgMCAuNyAyLjgxIDIgMiAwIDAgMS0uNDUgMi4xMUw4LjA5IDkuOTFhMTYgMTYgMCAwIDAgNiA2bDEuMjctMS4yN2EyIDIgMCAwIDEgMi4xMS0uNDUgMTIuODQgMTIuODQgMCAwIDAgMi44MS43QTIgMiAwIDAgMSAyMiAxNi45MnoiLz48L3N2Zz4=" style="width: 8px; height: 8px; vertical-align: middle; margin-right: 6px;">
                        <span style="color: #475569; font-size: 12px;">{{ $invoice->customer_phone }}</span>
                    </div> 
                    @endif
                    @if($invoice->customer_email) 
                    <div>
                        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjOTRhM2I4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCI+PHBhdGggZD0iTTQgNGgxNmMxLjEgMCAyIDQuOSAyIDydjEyYzAgMS4xLS45IDItMiAyaC0xNmMtMS4xIDAtMi0uOS0yLTJWNmMwLTEuMS45LTIgMi0yeiIvPjxwb2x5bGluZSBwb2ludHM9IjIyLDYgMTIsMTMgMiw2Ii8+PC9zdmc+" style="width: 8px; height: 8px; vertical-align: middle; margin-right: 6px;">
                        <span style="color: #475569; font-size: 12px;">{{ $invoice->customer_email }}</span>
                    </div> 
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">{{ __('Description') }}</th>
                    <th style="width: 10%; text-align: center;">{{ __('Qty') }}</th>
                    <th style="width: 20%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('Unit Price') }}</th>
                    <th style="width: 20%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                        <div style="font-weight: bold; color: #1e293b;">{{ $item->product_name }}</div>
                        @if($item->product_sku)
                        <div style="font-size: 11px; color: #94a3b8;">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ $item->formatted_unit_price }}</td>
                    <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-weight: bold;">{{ $item->formatted_total_price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Notes -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};">
                    <div class="words-card">
                        <div class="label-sm" style="color: #6366f1; margin-bottom: 5px;">{{ __('Total in Words') }}</div>
                        <div style="font-style: italic; color: #475569; font-size: 12px;">
                            {{ __('Stopped this invoice at the sum of') }}: <br>
                            <strong style="color: #1e293b;">{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </div>
                    </div>
                    @if($invoice->notes)
                    <div style="padding-left: 10px; text-align: left;">
                        <div class="label-sm">{{ __('Notes') }}</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 5px;">{{ $invoice->notes }}</div>
                    </div>
                    @endif
                </td>

                <td style="width: 50%; vertical-align: top;">
                    <table class="totals-table" style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: auto;">
                        <tr>
                            <td class="label-sm">{{ __('Subtotal') }} (HT)</td>
                            <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_subtotal }}</td>
                        </tr>
                        <tr>
                            <td class="label-sm">{{ __('Tax') }} ({{ $invoice->tax_rate }}%)</td>
                            <td style="text-align: right; font-weight: bold;">{{ $invoice->formatted_tax_amount }}</td>
                        </tr>
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td class="label-sm" style="color: #10b981;">{{ __('Discount') }}</td>
                            <td style="text-align: right; font-weight: bold; color: #10b981;">-{{ $invoice->formatted_discount_amount }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="label-sm">{{ __('Total') }} (TTC)</td>
                            <td style="text-align: right;"><span class="total-value">{{ $invoice->formatted_total_amount }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>{{ setting('company_name') }} - {{ setting('company_address') }}</p>
            <p style="font-weight: bold; color: #6366f1; margin-top: 5px;">{{ __('Thank you for your business!') }}</p>
        </div>
    </div>
</body>
</html>
