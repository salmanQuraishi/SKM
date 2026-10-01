<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif, 'DejaVu Sans';
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
        }

        .header-table,
        .meta-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #d90429;
            text-transform: uppercase;
            margin: 0;
        }

        .subtitle {
            font-size: 10px;
            color: #666;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .tax-invoice-title {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            text-transform: uppercase;
            color: #111;
        }

        .meta-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .meta-header {
            background-color: #f8fafc;
            font-weight: bold;
            width: 50%;
            font-size: 11px;
            text-transform: uppercase;
            color: #475569;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            padding: 8px;
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            border-top: 1px solid #333;
            font-size: 12px;
        }

        .grand-total td {
            font-weight: bold;
            font-size: 14px;
            color: #d90429;
            background: #fff1f2;
            border-top: 2px solid #d90429;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="title">AUTO<span style="color:#d90429;">LOGOS</span></div>
                    <div class="subtitle">CAR LOGOS & EMBLEMS</div>
                    <div style="margin-top: 8px; font-size: 11px; color: #555;">
                        <strong>{{ $store_name }}</strong><br>
                        {{ $store_address }}<br>
                        Phone: {{ $store_phone }} | Email: {{ $store_email }}<br>
                        <strong>GSTIN:</strong> {{ $gstin }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="tax-invoice-title">TAX INVOICE</div>
                    <div style="margin-top: 8px; font-size: 11px;">
                        <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
                        <strong>Invoice Date:</strong>
                        {{ is_string($invoice->invoice_date) ? $invoice->invoice_date : $invoice->invoice_date->format('d/m/Y') }}<br>
                        <strong>Order No:</strong> {{ $order->order_number }}<br>
                        <strong>Order Date:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                        <strong>Payment Mode:</strong> {{ strtoupper($order->payment_method) }}
                        ({{ strtoupper($order->payment_status) }})
                    </div>
                </td>
            </tr>
        </table>

        <!-- Billed & Shipped To -->
        <table class="meta-table">
            <tr>
                <td class="meta-header">BILLED TO (CUSTOMER DETAILS)</td>
                <td class="meta-header">SHIPPED TO (DELIVERY ADDRESS)</td>
            </tr>
            <tr>
                <td>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->formatted_shipping_address }}<br>
                    <strong>Phone:</strong> {{ $order->customer_phone }}<br>
                    <strong>Email:</strong> {{ $order->customer_email }}
                </td>
                <td>
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->formatted_shipping_address }}<br>
                    <strong>Phone:</strong> {{ $order->customer_phone }}<br>
                    <strong>Place of Supply:</strong> {{ $order->shipping_address['state'] ?? 'India' }}
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 12%;">HSN</th>
                    <th class="text-center" style="width: 8%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Unit Rate (&#x20B9;)</th>
                    <th class="text-right" style="width: 15%;">Amount (&#x20B9;)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->variant_name)
                                <div style="font-size: 10px; color: #666;">Variant: {{ $item->variant_name }}</div>
                            @endif
                            <div style="font-size: 9px; color: #888;">SKU: {{ $item->product_sku }}</div>
                        </td>
                        <td>8708</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach

                <!-- Summary Rows -->
                <tr class="total-row">
                    <td colspan="4" style="border: none;"></td>
                    <td class="text-right">Subtotal:</td>
                    <td class="text-right">&#x20B9;{{ number_format($order->subtotal, 2) }}</td>
                </tr>

                @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="4" style="border: none;"></td>
                        <td class="text-right" style="color: #16a34a;">Discount ({{ $order->coupon_code }}):</td>
                        <td class="text-right" style="color: #16a34a;">
                            -&#x20B9;{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                @endif

                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="text-right">GST (18% Included):</td>
                    <td class="text-right">&#x20B9;{{ number_format($order->tax_amount, 2) }}</td>
                </tr>

                <tr>
                    <td colspan="4" style="border: none;"></td>
                    <td class="text-right">Shipping & Handling:</td>
                    <td class="text-right">
                        {!! $order->shipping_amount == 0
                            ? 'FREE'
                            : '&#x20B9;' . number_format($order->shipping_amount, 2)
                        !!}
                    </td>
                </tr>

                @if($order->cod_charge > 0)
                    <tr>
                        <td colspan="4" style="border: none;"></td>
                        <td class="text-right">COD Charge:</td>
                        <td class="text-right">&#x20B9;{{ number_format($order->cod_charge, 2) }}</td>
                    </tr>
                @endif

                <tr class="grand-total">
                    <td colspan="4" style="background: none; border: none;"></td>
                    <td class="text-right">GRAND TOTAL:</td>
                    <td class="text-right">&#x20B9;{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer Notice -->
        <div class="footer">
            <p>This is a computer-generated tax invoice and requires no physical signature.<br>
                Thank you for choosing AutoLogos! For support or returns, contact info@autologos.in or +91 98765 43210.
            </p>
        </div>
    </div>
</body>

</html>