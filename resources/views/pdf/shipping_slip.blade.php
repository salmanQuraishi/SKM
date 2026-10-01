<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shipping Slip - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: {{ $format === 'thermal' ? '10px' : '12px' }}; color: #111; margin: 0; padding: {{ $format === 'thermal' ? '10px' : '20px' }}; }
        .slip-box { width: 100%; border: 2px dashed #000; padding: 15px; box-sizing: border-box; }
        .header { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .brand { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .badge-courier { font-size: 14px; font-weight: bold; border: 1px solid #000; padding: 4px 8px; display: inline-block; float: right; }
        .address-box { margin-bottom: 15px; border: 1px solid #000; padding: 10px; }
        .addr-title { font-weight: bold; text-transform: uppercase; font-size: 10px; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-bottom: 6px; }
        .table-items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-items th, .table-items td { border: 1px solid #ccc; padding: 4px 6px; font-size: 10px; }
        .table-items th { background: #eee; text-align: left; }
        .barcode { text-align: center; margin-top: 15px; font-family: 'Courier New', Courier, monospace; letter-spacing: 4px; font-size: 16px; font-weight: bold; border: 1px solid #000; padding: 6px; }
    </style>
</head>
<body>
    <div class="slip-box">
        <div class="header">
            <span class="brand">AUTOLOGOS</span>
            <span class="badge-courier">{{ strtoupper($order->payment_method) }} | {{ $order->payment_method === 'cod' ? 'COLLECT: &#x20B9;' . number_format($order->total_amount, 2) : 'PREPAID' }}</span>
            <div style="clear: both;"></div>
            <div style="font-size: 9px; color: #444; margin-top: 4px;">Express Automotive Logistics Packing Slip</div>
        </div>

        <!-- Deliver To -->
        <div class="address-box">
            <div class="addr-title">SHIP TO (DELIVERY RECIPIENT):</div>
            <div style="font-size: 13px; font-weight: bold;">{{ $order->customer_name }}</div>
            <div>{{ $order->formatted_shipping_address }}</div>
            <div style="margin-top: 4px; font-weight: bold; font-size: 13px;">PIN: {{ $order->shipping_address['pincode'] ?? 'N/A' }}</div>
            <div style="margin-top: 2px;"><strong>Contact:</strong> +91 {{ $order->customer_phone }}</div>
        </div>

        <!-- Return Address -->
        <div style="font-size: 9px; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 6px;">
            <strong>RETURN IF UNDELIVERED TO:</strong><br>
            {{ $store_name }} - {{ $store_address }} | Phone: {{ $store_phone }}
        </div>

        <!-- Package Contents -->
        <table class="table-items">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th style="width: 25%;">SKU</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }} {{ $item->variant_name ? "({$item->variant_name})" : '' }}</td>
                        <td>{{ $item->product_sku }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $item->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order & Tracking Barcode Block -->
        <div class="barcode">
            * {{ $order->order_number }} *
        </div>
        <div style="text-align: center; font-size: 9px; color: #666; margin-top: 4px;">
            Order Date: {{ $order->created_at->format('d/m/Y') }} | Courier: {{ $order->courier_name ?: 'Standard Express' }}
        </div>
    </div>
</body>
</html>
