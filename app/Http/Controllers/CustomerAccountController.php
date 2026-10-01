<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        $totalOrdersCount = $user->orders()->count();
        $defaultAddress = $user->defaultAddress ?: $user->addresses()->first();

        return view('frontend.account.dashboard', compact('user', 'recentOrders', 'totalOrdersCount', 'defaultAddress'));
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('frontend.account.orders', compact('orders'));
    }

    public function orderDetail(string $orderNumber)
    {
        $order = Auth::user()->orders()
            ->where('order_number', $orderNumber)
            ->with(['items.product', 'statusHistory', 'shipment', 'invoice', 'receipt', 'latestPayment'])
            ->firstOrFail();

        return view('frontend.account.order_detail', compact('order'));
    }

    public function downloadInvoice(string $orderNumber, PdfService $pdfService)
    {
        $order = Auth::user()->orders()->where('order_number', $orderNumber)->firstOrFail();
        $pdf = $pdfService->generateInvoicePdf($order);
        return $pdf->download("Invoice-{$order->order_number}.pdf");
    }

    public function downloadReceipt(string $orderNumber, PdfService $pdfService)
    {
        $order = Auth::user()->orders()->where('order_number', $orderNumber)->firstOrFail();
        $pdf = $pdfService->generateReceiptPdf($order);
        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }

    public function addresses()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('frontend.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|digits:6',
            'address_type' => 'required|in:Home,Office,Other',
        ]);

        $isDefault = $request->boolean('is_default') || Auth::user()->addresses()->count() === 0;

        if ($isDefault) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        Auth::user()->addresses()->create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'email' => $request->email ?: Auth::user()->email,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => 'India',
            'address_type' => $request->address_type,
            'is_default' => $isDefault,
        ]);

        return redirect()->route('account.addresses')->with('success', 'Address saved successfully.');
    }

    public function deleteAddress(int $id)
    {
        Auth::user()->addresses()->where('id', $id)->delete();
        return redirect()->route('account.addresses')->with('success', 'Address deleted.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('account.dashboard')->with('success', 'Profile updated successfully.');
    }
}
