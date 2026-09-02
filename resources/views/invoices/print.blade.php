<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Invoice') }} {{ $invoice->invoice_number }}</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #f8fafc;
            -webkit-print-color-adjust: exact;
        }

        .invoice-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            max-width: 950px;
            margin: 0 auto;
            position: relative;
            page-break-inside: avoid;
        }

        .invoice-accent-bar {
            height: 6px;
            background: linear-gradient(90deg, #6366f1 0%, #a855f7 100%);
        }

        .invoice-content {
            padding: 40px;
        }

        .company-name {
            font-size: 42px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 16px 0;
            letter-spacing: -2px;
            line-height: 1;
        }

        .company-info p {
            margin: 2px 0;
            color: #64748b;
            font-size: 15px;
        }

        .fiscal-ids {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fiscal-tag {
            background: #f8fafc;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #f1f5f9;
        }

        .invoice-details {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 32px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: auto auto;
            gap: 12px 32px;
        }

        .label-sm {
            color: #94a3b8;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .value-md {
            color: #1e293b;
            font-weight: 800;
            font-size: 18px;
        }

        .value-sm {
            color: #1e293b;
            font-weight: 600;
            font-size: 15px;
        }

        .section-title {
            color: #6366f1;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .items-table td {
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .totals-card {
            background: #f8fafc;
            padding: 24px;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            width: 320px;
            margin-left: auto;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .grand-total {
            border-top: 2px dashed #e2e8f0;
            margin-top: 24px;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .words-card {
            background: #f8fafc;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            margin-bottom: 32px;
        }

        .footer {
            background: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        @media print {
            body { 
                background: white;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
            .invoice-content {
                padding: 40px;
            }
        }

        [dir="rtl"] .items-table th { text-align: right; }
        [dir="rtl"] .invoice-details { align-items: flex-start; text-align: left; }
        [dir="rtl"] .totals-card { margin-left: 0; margin-right: auto; }
    </style>
</head>
<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="invoice-card">
        <div class="invoice-accent-bar"></div>
        <div class="invoice-content">
            <!-- Header Section -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                <!-- Company Info -->
                <div style="flex: 1;">
                    <h1 class="company-name" style="font-size: 32px; margin-bottom: 8px;">{{ setting('company_name', setting('app_name')) }}</h1>
                    <div style="color: #64748b; line-height: 1.4; font-size: 13px;">
                        {{ setting('company_address') }}
                        <div style="margin-top: 4px; display: flex; gap: 15px;">
                            @if(setting('company_phone')) <span style="display: flex; align-items: center; gap: 6px;"><i class="fas fa-phone" style="font-size: 10px; color: #6366f1;"></i> {{ setting('company_phone') }}</span> @endif
                            @if(setting('company_email')) <span style="display: flex; align-items: center; gap: 6px;"><i class="fas fa-envelope" style="font-size: 10px; color: #6366f1;"></i> {{ setting('company_email') }}</span> @endif
                        </div>
                        <div class="fiscal-ids" style="margin-top: 10px; gap: 8px;">
                            @if(setting('company_tax_id')) <span class="fiscal-tag" style="padding: 2px 8px;">{{ __('ICE') }}: {{ setting('company_tax_id') }}</span> @endif
                            @if(setting('company_registry_id')) <span class="fiscal-tag" style="padding: 2px 8px;">{{ __('RC') }}: {{ setting('company_registry_id') }}</span> @endif
                            @if(setting('company_patente')) <span class="fiscal-tag" style="padding: 2px 8px;">{{ __('Patente') }}: {{ setting('company_patente') }}</span> @endif
                            @if(setting('company_fiscal_id')) <span class="fiscal-tag" style="padding: 2px 8px;">{{ __('IF') }}: {{ setting('company_fiscal_id') }}</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                @if(setting('app_logo'))
                    <div style="background: white; padding: 6px; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="max-width: 120px; height: auto;">
                    </div>
                @endif
            </div>

            <!-- Client & Invoice Info Row -->
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 25px;">
                <!-- Client Info -->
                <div style="flex: 1;">
                    <p class="section-title" style="margin-bottom: 8px;">{{ $invoice->getBillToLabel() }}</p>
                    <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 4px 0;">{{ $invoice->customer_name }}</h3>
                    @if($invoice->ice)
                    <div style="margin-bottom: 6px;">
                        <span style="background: #f1f5f9; color: #475569; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('ICE') }}: {{ $invoice->ice }}</span>
                    </div>
                    @endif
                    <div style="color: #475569; font-size: 13px; line-height: 1.4;">
                        @php
                            $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null);
                        @endphp
                        @if($address) 
                        <div style="display: flex; align-items: start; gap: 8px; margin-bottom: 2px;">
                            <i class="fas fa-map-marker-alt" style="margin-top: 3px; color: #94a3b8; font-size: 11px;"></i>
                            <span>{{ $address }}</span>
                        </div> 
                        @endif
                        @if($invoice->customer_phone || $invoice->customer_email)
                        <div style="display: flex; gap: 15px;">
                            @if($invoice->customer_phone) 
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-phone-alt" style="color: #94a3b8; font-size: 11px;"></i>
                                <span>{{ $invoice->customer_phone }}</span>
                            </div> 
                            @endif
                            @if($invoice->customer_email) 
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-envelope" style="color: #94a3b8; font-size: 11px;"></i>
                                <span>{{ $invoice->customer_email }}</span>
                            </div> 
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="invoice-details" style="gap: 10px; text-align: right;">
                    <div class="details-grid" style="gap: 4px 20px; display: grid; grid-template-columns: auto auto;">
                        <span class="label-sm">{{ $invoice->getNumberLabel() }}</span>
                        <span class="value-md" style="font-size: 15px;">#{{ $invoice->invoice_number }}</span>
                        <span class="label-sm">{{ __('Issue Date') }}</span>
                        <span class="value-sm" style="font-size: 13px;">{{ $invoice->issued_at->translatedFormat('d M, Y') }}</span>
                        @if($invoice->due_date)
                        <span class="label-sm">{{ __('Due Date') }}</span>
                        <span class="value-sm" style="font-size: 13px; color: #ef4444;">{{ $invoice->due_date->translatedFormat('d M, Y') }}</span>
                        @endif
                        <span class="label-sm">{{ __('Status') }}</span>
                        <span class="value-sm" style="font-size: 13px; text-transform: capitalize;">{{ $invoice->payment_status }}</span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">{{ __('Description') }}</th>
                        <th style="width: 10%; text-align: center;">{{ __('Qty') }}</th>
                        <th style="width: 20%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('Unit Price') }}</th>
                        <th style="width: 20%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: #1e293b; font-size: 16px; margin-bottom: 4px;">{{ $item->product_name }}</div>
                            @if($item->product_sku)
                            <div class="product-sku">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 500;">{{ $item->quantity }}</td>
                        <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-weight: 500;">{{ $item->formatted_unit_price_ht }}</td>
                        <td style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-weight: 800; color: #1e293b;">{{ $item->formatted_total_price_ht }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div style="display: flex; gap: 60px;">
                <div style="flex: 1;">
                    <div class="words-card">
                        <p class="label-sm" style="color: #6366f1; margin-bottom: 8px;">{{ __('Total in Words') }}</p>
                        <p style="color: #475569; font-style: italic; font-size: 15px; margin: 0; line-height: 1.5;">
                            {{ __('Stopped this invoice at the sum of') }}: <strong style="color: #1e293b;">{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </p>
                    </div>
                    @if($invoice->notes)
                    <div style="padding: 0 24px; margin-bottom: 20px;">
                        <p class="label-sm" style="margin-bottom: 8px;">{{ __('Notes') }}</p>
                        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0;">{{ $invoice->notes }}</p>
                    </div>
                    @endif

                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                    <div style="padding: 0 24px; margin-top: 15px;">
                        <img src="{{ asset('storage/' . setting('company_stamp')) }}" alt="Company Stamp" style="width: {{ round(160 * intval(setting('company_stamp_scale', 100)) / 100) }}px; height: auto; object-fit: contain; display: inline-block;">
                    </div>
                    @endif
                </div>

                <div class="totals-card">
                    <div class="total-row">
                        <span style="color: #64748b; font-weight: 500; font-size: 15px;">{{ __('Subtotal') }} (HT)</span>
                        <span style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $invoice->formatted_subtotal }}</span>
                    </div>
                    <div class="total-row">
                        <span style="color: #64748b; font-weight: 500; font-size: 15px;">{{ __('Tax') }} ({{ $invoice->tax_rate }}%)</span>
                        <span style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $invoice->formatted_tax_amount }}</span>
                    </div>
                    @if($invoice->discount_amount > 0)
                    <div class="total-row">
                        <span style="color: #10b981; font-weight: 500; font-size: 15px;">{{ __('Discount') }}</span>
                        <span style="font-weight: 700; color: #10b981; font-size: 15px;">-{{ $invoice->formatted_discount_amount }}</span>
                    </div>
                    @endif
                    <div class="grand-total">
                        <div>
                            <span class="label-sm" style="display: block; margin-bottom: 4px;">{{ __('Total') }} (TTC)</span>
                            <span style="font-size: 38px; font-weight: 900; color: #6366f1; letter-spacing: -1.5px; line-height: 1;">{{ $invoice->formatted_total_amount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <p style="margin: 0;">{{ setting('company_name') }} - {{ setting('company_address') }}</p>
            <p style="margin-top: 8px; font-weight: 600; color: #6366f1;">{{ __('Thank you for your business!') }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
