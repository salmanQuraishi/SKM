<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerCrudController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->where(function ($q) {
                $q->whereHas('role', function ($sub) {
                    $sub->where('slug', 'customer');
                })->orWhereNull('role_id');
            });

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->latest('id')->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(int $id)
    {
        $customer = User::with(['orders.items', 'addresses'])->findOrFail($id);
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }

    public function toggleStatus(int $id)
    {
        $customer = User::findOrFail($id);
        $customer->is_active = !$customer->is_active;
        $customer->save();

        $status = $customer->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Customer {$customer->name} has been {$status}.");
    }

    public function destroy(int $id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer record deleted.');
    }
}
