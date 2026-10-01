<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generateInvoicePdf(Order $order): \Barryvdh\DomPDF\PDF
    {
        $order->load(['items.product', 'items.variant', 'invoice']);
        $settings = Setting::getAllGrouped();

        $data = [
            'order' => $order,
            'invoice' => $order->invoice ?? (object)[
                'invoice_number' => 'INV-' . $order->order_number,
                'invoice_date' => $order->created_at,
            ],
            'store_name' => $settings['general']['store_name'] ?? 'AutoLogos India',
            'store_address' => $settings['general']['store_address'] ?? '123, Auto Street, New Delhi, India - 110001',
            'store_phone' => $settings['general']['store_phone'] ?? '+91 98765 43210',
            'store_email' => $settings['general']['store_email'] ?? 'info@autologos.in',
            'gstin' => $settings['general']['gstin'] ?? '07AAAAA0000A1Z5',
        ];

        return Pdf::loadView('pdf.invoice', $data)->setPaper('a4', 'portrait');
    }

    public function generateReceiptPdf(Order $order, ?Receipt $receipt = null): \Barryvdh\DomPDF\PDF
    {
        $order->load(['items', 'latestPayment']);
        $settings = Setting::getAllGrouped();

        $data = [
            'order' => $order,
            'receipt' => $receipt ?? (object)[
                'receipt_number' => 'REC-' . $order->order_number,
                'receipt_date' => $order->created_at,
                'amount' => $order->total_amount,
                'payment_method' => strtoupper($order->payment_method),
            ],
            'payment' => $order->latestPayment,
            'store_name' => $settings['general']['store_name'] ?? 'AutoLogos India',
            'store_phone' => $settings['general']['store_phone'] ?? '+91 98765 43210',
            'store_email' => $settings['general']['store_email'] ?? 'info@autologos.in',
        ];

        return Pdf::loadView('pdf.receipt', $data)->setPaper('a4', 'portrait');
    }

    public function generateShippingSlipPdf(Order $order, string $format = 'a4'): \Barryvdh\DomPDF\PDF
    {
        $order->load(['items']);
        $settings = Setting::getAllGrouped();

        $data = [
            'order' => $order,
            'store_name' => $settings['general']['store_name'] ?? 'AutoLogos India',
            'store_address' => $settings['general']['store_address'] ?? '123, Auto Street, New Delhi, India - 110001',
            'store_phone' => $settings['general']['store_phone'] ?? '+91 98765 43210',
            'store_email' => $settings['general']['store_email'] ?? 'info@autologos.in',
            'format' => $format,
        ];

        if ($format === 'thermal') {
            // Thermal printer 4x6 inches
            return Pdf::loadView('pdf.shipping_slip', $data)->setPaper([0, 0, 288, 432], 'portrait');
        }

        return Pdf::loadView('pdf.shipping_slip', $data)->setPaper('a4', 'portrait');
    }
}
