<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif, 'DejaVu Sans'; font-size: 12px; color: #333; line-height: 1.5; padding: 25px; }
        .receipt-card { max-width: 600px; margin: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; }
        .header { text-align: center; border-bottom: 2px solid #d90429; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #d90429; text-transform: uppercase; }
        .receipt-badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-weight: bold; font-size: 11px; margin-top: 5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 8px 4px; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; width: 40%; font-weight: bold; }
        .val { color: #0f172a; width: 60%; }
        .amount-box { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 15px; text-align: center; margin-bottom: 20px; }
        .amount-val { font-size: 24px; font-weight: bold; color: #d90429; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="receipt-card">
        <div class="header">
            <div class="title">AUTOLOGOS</div>
            <div style="font-size: 10px; color: #64748b; letter-spacing: 2px; text-transform: uppercase;">Payment Receipt</div>
            <div class="receipt-badge">PAYMENT CONFIRMED</div>
        </div>

        <div class="amount-box">
            <div style="font-size: 11px; color: #64748b; text-transform: uppercase;">Amount Paid</div>
            <div class="amount-val">&#x20B9;{{ number_format($receipt->amount, 2) }}</div>
            <div style="font-size: 11px; color: #16a34a; font-weight: bold;">Status: CAPTURED</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Receipt Number:</td>
                <td class="val">{{ $receipt->receipt_number }}</td>
            </tr>
            <tr>
                <td class="label">Receipt Date:</td>
                <td class="val">{{ is_string($receipt->receipt_date) ? $receipt->receipt_date : $receipt->receipt_date->format('d/m/Y h:i A') }}</td>
            </tr>
            <tr>
                <td class="label">Order Number:</td>
                <td class="val"><strong>{{ $order->order_number }}</strong></td>
            </tr>
            <tr>
                <td class="label">Payment Mode:</td>
                <td class="val">{{ $receipt->payment_method }}</td>
            </tr>
            @if($payment && $payment->transaction_id)
                <tr>
                    <td class="label">Transaction ID:</td>
                    <td class="val">{{ $payment->transaction_id }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Customer Name:</td>
                <td class="val">{{ $order->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">Customer Mobile:</td>
                <td class="val">{{ $order->customer_phone }}</td>
            </tr>
            <tr>
                <td class="label">Customer Email:</td>
                <td class="val">{{ $order->customer_email }}</td>
            </tr>
        </table>

        <div class="footer">
            {{ $store_name }} | {{ $store_phone }} | {{ $store_email }}<br>
            Thank you for your payment!
        </div>
    </div>
</body>
</html>
