<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PdfService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderCrudController extends Controller
{
    protected OrderService $orderService;
    protected PdfService $pdfService;
    protected WhatsAppService $whatsAppService;

    public function __construct(
        OrderService $orderService,
        PdfService $pdfService,
        WhatsAppService $whatsAppService
    ) {
        $this->orderService = $orderService;
        $this->pdfService = $pdfService;
        $this->whatsAppService = $whatsAppService;
    }

    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'user', 'latestPayment']);

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest('id')->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('order_status', 'pending')->count(),
            'confirmed' => Order::where('order_status', 'confirmed')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'packed' => Order::where('order_status', 'packed')->count(),
            'shipped' => Order::where('order_status', 'shipped')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(int $id)
    {
        $order = Order::with(['items.product', 'items.variant', 'statusHistory', 'shipment', 'invoice', 'receipt', 'payments', 'user'])->findOrFail($id);

        $whatsappConfirmMsg = $this->whatsAppService->getOrderConfirmationMessage($order);
        $whatsappShipMsg = $this->whatsAppService->getShippingUpdateMessage($order);

        $whatsappConfirmUrl = $this->whatsAppService->getDirectChatUrl($order->customer_phone, $whatsappConfirmMsg);
        $whatsappShipUrl = $this->whatsAppService->getDirectChatUrl($order->customer_phone, $whatsappShipMsg);

        return view('admin.orders.show', compact('order', 'whatsappConfirmUrl', 'whatsappShipUrl'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'order_status' => 'required|in:pending,confirmed,processing,packed,shipped,out_for_delivery,delivered,cancelled,returned,refunded',
            'comment' => 'nullable|string|max:500',
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'expected_delivery_date' => 'nullable|date',
        ]);

        if ($request->filled('expected_delivery_date')) {
            $order->expected_delivery_date = $request->expected_delivery_date;
            $order->save();
        }

        $adminName = Auth::user() ? Auth::user()->name : 'Admin';

        $this->orderService->updateOrderStatus(
            $order,
            $request->order_status,
            $request->comment,
            $request->courier_name,
            $request->tracking_number,
            $adminName
        );

        return redirect()->back()->with('success', "Order #{$order->order_number} status updated to " . strtoupper($request->order_status));
    }

    public function downloadInvoice(int $id)
    {
        $order = Order::findOrFail($id);
        $pdf = $this->pdfService->generateInvoicePdf($order);
        return $pdf->download("Tax-Invoice-{$order->order_number}.pdf");
    }

    public function streamInvoice(int $id)
    {
        $order = Order::findOrFail($id);
        $pdf = $this->pdfService->generateInvoicePdf($order);
        return $pdf->stream("Tax-Invoice-{$order->order_number}.pdf");
    }

    public function downloadShippingSlip(int $id, Request $request)
    {
        $order = Order::findOrFail($id);
        $format = $request->get('format', 'a4');
        $pdf = $this->pdfService->generateShippingSlipPdf($order, $format);
        return $pdf->download("ShippingSlip-{$order->order_number}.pdf");
    }

    public function streamShippingSlip(int $id, Request $request)
    {
        $order = Order::findOrFail($id);
        $format = $request->get('format', 'a4');
        $pdf = $this->pdfService->generateShippingSlipPdf($order, $format);
        return $pdf->stream("ShippingSlip-{$order->order_number}.pdf");
    }

    public function downloadReceipt(int $id)
    {
        $order = Order::findOrFail($id);
        $pdf = $this->pdfService->generateReceiptPdf($order);
        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }

    public function sendWhatsAppNotification(Request $request, int $id)
    {
        $order = Order::findOrFail($id);
        $type = $request->get('type', 'confirmation');

        $message = $type === 'shipping'
            ? $this->whatsAppService->getShippingUpdateMessage($order)
            : $this->whatsAppService->getOrderConfirmationMessage($order);

        $sent = $this->whatsAppService->sendCloudMessage($order->customer_phone, $message);

        if ($sent) {
            return redirect()->back()->with('success', 'WhatsApp notification dispatched via Cloud API!');
        }

        // Fallback: Redirect to direct WhatsApp web URL
        $directUrl = $this->whatsAppService->getDirectChatUrl($order->customer_phone, $message);
        return redirect()->away($directUrl);
    }
}
