<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Invoice') }} {{ $invoice->invoice_number }}</title>
    <style>
        /* PDF specific resets */
        @page {
            margin: 0;
            padding: 0;
        }
        
        body {
            /* Using DejaVu Sans for broad Unicode/Arabic support in DomPDF */
            font-family: "DejaVu Sans", sans-serif;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-size: 11px;
        }

        .accent-bar {
            height: 6px;
            background: #6366f1;
            width: 100%;
        }

        .container {
            padding: 30px 40px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 5px 0;
            line-height: 1;
        }

        .company-info p {
            margin: 2px 0;
            color: #64748b;
            font-size: 10px;
        }

        .fiscal-tag {
            background: #f8fafc;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #f1f5f9;
            font-size: 9px;
            color: #94a3b8;
            font-weight: bold;
            margin-right: 5px;
        }

        .section-title {
            color: #6366f1;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }

        .client-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
            margin: 0 0 4px 0;
        }

        .label-sm {
            color: #94a3b8;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .value-md {
            color: #1e293b;
            font-weight: bold;
            font-size: 14px;
        }

        .value-sm {
            color: #1e293b;
            font-weight: bold;
            font-size: 12px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #f1f5f9;
        }

        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .totals-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #f1f5f9;
            width: 280px;
        }

        .grand-total {
            border-top: 1px dashed #e2e8f0;
            margin-top: 15px;
            padding-top: 15px;
        }

        .total-value {
            font-size: 26px;
            font-weight: bold;
            color: #6366f1;
        }

        .words-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            margin-bottom: 20px;
            width: 100%;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
            font-size: 10px;
            color: #94a3b8;
        }

        /* RTL Support for tables */
        [dir="rtl"] th { text-align: right !important; }
        [dir="rtl"] td { text-align: right !important; }
        [dir="rtl"] .text-right { text-align: left !important; }
        [dir="ltr"] .text-right { text-align: right !important; }
    </style>
</head>
<body>
    <div class="accent-bar"></div>
    <div class="container" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        <!-- Header Section -->
        <table style="width: 100%; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
            <tr>
                <td style="width: 70%; vertical-align: top;">
                    <h1 class="company-name">{{ setting('company_name', setting('app_name')) }}</h1>
                    <div class="company-info">
                        <p>{{ setting('company_address') }}</p>
                        <p>
                            @if(setting('company_phone')) {{ setting('company_phone') }} @endif
                            @if(setting('company_phone') && setting('company_email')) | @endif
                            @if(setting('company_email')) {{ setting('company_email') }} @endif
                        </p>
                        <div style="margin-top: 8px;">
                            @if(setting('company_tax_id')) <span class="fiscal-tag">{{ __('ICE') }}: {{ setting('company_tax_id') }}</span> @endif
                            @if(setting('company_registry_id')) <span class="fiscal-tag">{{ __('RC') }}: {{ setting('company_registry_id') }}</span> @endif
                        </div>
                    </div>
                </td>
                <td style="width: 30%; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; vertical-align: top;">
                    @if(setting('app_logo'))
                        @php
                            $logoPath = public_path('storage/' . setting('app_logo'));
                        @endphp
                        @if(file_exists($logoPath))
                            <img src="data:image/{{ pathinfo($logoPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($logoPath)) }}" style="max-width: 100px; height: auto;">
                        @endif
                    @endif
                </td>
            </tr>
        </table>

        <!-- Client & Details -->
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <div class="section-title">{{ $invoice->getBillToLabel() }}</div>
                    <div class="client-name">{{ $invoice->customer_name }}</div>
                    @if($invoice->ice)
                    <div style="margin-bottom: 5px;"><span class="fiscal-tag" style="background: #f1f5f9;">{{ __('ICE') }}: {{ $invoice->ice }}</span></div>
                    @endif
                    <div style="color: #475569; font-size: 11px; line-height: 1.4;">
                        @php $address = $invoice->customer_address ?: ($invoice->order->shipping_address ?? null); @endphp
                        @if($address) <p style="margin: 2px 0;">{{ $address }}</p> @endif
                        <p style="margin: 2px 0;">
                            @if($invoice->customer_phone) {{ $invoice->customer_phone }} @endif
                            @if($invoice->customer_phone && $invoice->customer_email) | @endif
                            @if($invoice->customer_email) {{ $invoice->customer_email }} @endif
                        </p>
                    </div>
                </td>
                <td style="width: 35%; vertical-align: top;">
                    <div style="background: #f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #f1f5f9;">
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="label-sm">{{ $invoice->getNumberLabel() }}</div>
                                    <div class="value-md">#{{ $invoice->invoice_number }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 8px;">
                                    <div class="label-sm">{{ __('Issue Date') }}</div>
                                    <div class="value-sm">{{ $invoice->issued_at->translatedFormat('d M, Y') }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 8px;">
                                    <div class="label-sm">{{ __('Status') }}</div>
                                    <div class="value-sm" style="text-transform: capitalize;">{{ $invoice->payment_status }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%; text-align: left;">{{ __('Description') }}</th>
                    <th style="width: 10%; text-align: center;">{{ __('Qty') }}</th>
                    <th style="width: 20%; text-align: right;">{{ __('Unit Price') }}</th>
                    <th style="width: 20%; text-align: right;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold; color: #1e293b; font-size: 12px;">{{ $item->product_name }}</div>
                        @if($item->product_sku)
                        <div style="font-size: 9px; color: #94a3b8;">{{ __('SKU') }}: {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $item->formatted_unit_price_ht }}</td>
                    <td class="text-right" style="font-weight: bold; color: #1e293b;">{{ $item->formatted_total_price_ht }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Row -->
        <table style="width: 100%;">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <div class="words-card">
                        <div class="label-sm" style="color: #6366f1; margin-bottom: 5px;">{{ __('Total in Words') }}</div>
                        <div style="font-style: italic; color: #475569; font-size: 11px;">
                            {{ __('Stopped this invoice at the sum of') }}: <br>
                            <strong style="color: #1e293b;">{{ $invoice->total_in_words }} {{ setting('currency_code', 'USD') }}</strong>
                        </div>
                    </div>
                    @if($invoice->notes)
                    <div style="padding: 0 10px;">
                        <div class="label-sm" style="margin-bottom: 4px;">{{ __('Notes') }}</div>
                        <div style="font-size: 10px; color: #64748b; line-height: 1.4;">{{ $invoice->notes }}</div>
                    </div>
                    @endif

                    @if(($withStamp ?? $invoice->with_stamp ?? true) && setting('company_stamp'))
                        @php
                            $stampPath = public_path('storage/' . setting('company_stamp'));
                        @endphp
                        @if(file_exists($stampPath))
                        <div style="margin-top: 15px; padding: 0 10px;">
                            <img src="data:image/{{ pathinfo($stampPath, PATHINFO_EXTENSION) }};base64,{{ base64_encode(file_get_contents($stampPath)) }}" style="max-height: 80px; max-width: 160px; object-fit: contain;">
                        </div>
                        @endif
                    @endif
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <div class="totals-card" style="margin-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: auto;">
                        <table style="width: 100%;">
                            <tr>
                                <td class="label-sm" style="padding-bottom: 8px;">{{ __('Subtotal') }} (HT)</td>
                                <td class="text-right" style="font-weight: bold; padding-bottom: 8px;">{{ $invoice->formatted_subtotal }}</td>
                            </tr>
                            <tr>
                                <td class="label-sm" style="padding-bottom: 8px;">{{ __('Tax') }} ({{ $invoice->tax_rate }}%)</td>
                                <td class="text-right" style="font-weight: bold; padding-bottom: 8px;">{{ $invoice->formatted_tax_amount }}</td>
                            </tr>
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td class="label-sm" style="color: #10b981; padding-bottom: 8px;">{{ __('Discount') }}</td>
                                <td class="text-right" style="font-weight: bold; color: #10b981; padding-bottom: 8px;">-{{ $invoice->formatted_discount_amount }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="2" class="grand-total">
                                    <div class="label-sm" style="margin-bottom: 2px;">{{ __('Total') }} (TTC)</div>
                                    <div class="total-value text-right">{{ $invoice->formatted_total_amount }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p style="margin: 0;">{{ setting('company_name') }} - {{ setting('company_address') }}</p>
            <p style="margin-top: 5px; font-weight: bold; color: #6366f1;">{{ __('Thank you for your business!') }}</p>
        </div>
    </div>
</body>
</html>
