<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Review;
use App\Models\Shipment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrdersAndReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@autologos.in')->first();
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        $address = [
            'name' => 'Rohit Sharma',
            'mobile' => '9812345678',
            'email' => 'customer@autologos.in',
            'address_line_1' => 'Flat 402, Greenfield Apartments',
            'address_line_2' => 'Sector 14, MG Road',
            'landmark' => 'Near City Center',
            'city' => 'Gurugram',
            'state' => 'Haryana',
            'pincode' => '122001',
            'country' => 'India',
        ];

        // Seed Sample Orders
        $orderConfigs = [
            [
                'order_number' => 'AL-202609-1001',
                'status' => 'delivered',
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'product_indices' => [0, 8], // Hyundai Front + S6 Monogram
                'days_ago' => 5,
                'courier' => 'BlueDart Express',
                'tracking' => 'BD982741029IN',
            ],
            [
                'order_number' => 'AL-202609-1002',
                'status' => 'shipped',
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'product_indices' => [4, 18], // Suzuki Logo + Swift Emblem
                'days_ago' => 2,
                'courier' => 'Delhivery Surface',
                'tracking' => 'DLV3849102839',
            ],
            [
                'order_number' => 'AL-202609-1003',
                'status' => 'processing',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
                'product_indices' => [5, 9], // Tata Logo + M Power Monogram
                'days_ago' => 1,
            ],
            [
                'order_number' => 'AL-202609-1004',
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'razorpay',
                'product_indices' => [16], // CRETA Lettering Badge
                'days_ago' => 0,
            ],
        ];

        foreach ($orderConfigs as $conf) {
            $createdTime = Carbon::now()->subDays($conf['days_ago']);

            $subtotal = 0;
            $itemsToCreate = [];
            foreach ($conf['product_indices'] as $idx) {
                if (isset($products[$idx])) {
                    $p = $products[$idx];
                    $subtotal += $p->active_price;
                    $itemsToCreate[] = [
                        'product_id' => $p->id,
                        'product_name' => $p->name,
                        'product_sku' => $p->sku,
                        'price' => $p->active_price,
                        'quantity' => 1,
                        'tax_rate' => 18.00,
                        'tax_amount' => round(($p->active_price * 0.18) / 1.18, 2),
                        'total' => $p->active_price,
                        'product_image' => $p->primary_image_url,
                    ];
                }
            }

            $shipping = $subtotal >= 999 ? 0.00 : 99.00;
            $codCharge = $conf['payment_method'] === 'cod' ? 50.00 : 0.00;
            $tax = round(($subtotal * 0.18) / 1.18, 2);
            $total = $subtotal + $shipping + $codCharge;

            $order = Order::updateOrCreate(
                ['order_number' => $conf['order_number']],
                [
                    'user_id' => $customer?->id,
                    'customer_name' => 'Rohit Sharma',
                    'customer_email' => 'customer@autologos.in',
                    'customer_phone' => '9812345678',
                    'billing_address' => $address,
                    'shipping_address' => $address,
                    'subtotal' => $subtotal,
                    'discount_amount' => 0.00,
                    'tax_amount' => $tax,
                    'shipping_amount' => $shipping,
                    'cod_charge' => $codCharge,
                    'total_amount' => $total,
                    'payment_method' => $conf['payment_method'],
                    'payment_status' => $conf['payment_status'],
                    'order_status' => $conf['status'],
                    'courier_name' => $conf['courier'] ?? null,
                    'tracking_number' => $conf['tracking'] ?? null,
                    'expected_delivery_date' => Carbon::now()->addDays(3),
                    'created_at' => $createdTime,
                    'updated_at' => $createdTime,
                ]
            );

            // Seed order items
            $order->items()->delete();
            foreach ($itemsToCreate as $item) {
                $order->items()->create($item);
            }

            // Seed Status History
            $order->statusHistory()->delete();
            $order->statusHistory()->create([
                'status' => 'confirmed',
                'comment' => 'Order placed successfully.',
                'notify_customer' => true,
                'created_by' => 'System',
                'created_at' => $createdTime,
            ]);

            if ($conf['status'] === 'shipped' || $conf['status'] === 'delivered') {
                $order->statusHistory()->create([
                    'status' => 'shipped',
                    'comment' => "Dispatched via {$conf['courier']} (Tracking: {$conf['tracking']}).",
                    'notify_customer' => true,
                    'created_by' => 'Order Manager',
                    'created_at' => $createdTime->copy()->addDay(),
                ]);

                // Create Shipment
                Shipment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'shipment_number' => 'SHP-' . $order->order_number,
                        'courier_name' => $conf['courier'],
                        'tracking_number' => $conf['tracking'],
                        'tracking_url' => 'https://track.bluedart.com',
                        'shipping_date' => $createdTime->copy()->addDay(),
                        'estimated_delivery' => $createdTime->copy()->addDays(4),
                        'shipment_status' => $conf['status'] === 'delivered' ? 'delivered' : 'in_transit',
                    ]
                );
            }

            if ($conf['status'] === 'delivered') {
                $order->statusHistory()->create([
                    'status' => 'delivered',
                    'comment' => 'Shipment delivered to customer with signature confirmation.',
                    'notify_customer' => true,
                    'created_by' => 'Delivery Agent',
                    'created_at' => $createdTime->copy()->addDays(3),
                ]);
            }

            // Seed Payment
            if ($conf['payment_status'] === 'paid') {
                $payment = Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'transaction_id' => 'pay_' . substr(md5($order->id), 0, 14),
                        'payment_gateway' => 'razorpay',
                        'razorpay_payment_id' => 'pay_' . substr(md5($order->id), 0, 14),
                        'razorpay_order_id' => 'order_' . substr(md5($order->id), 0, 14),
                        'amount' => $order->total_amount,
                        'currency' => 'INR',
                        'status' => 'captured',
                        'payment_method_type' => 'upi',
                    ]
                );

                Receipt::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'payment_id' => $payment->id,
                        'receipt_number' => 'REC-' . Carbon::now()->format('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                        'receipt_date' => $createdTime,
                        'amount' => $order->total_amount,
                        'payment_method' => 'Razorpay / UPI',
                    ]
                );
            }

            // Invoice
            Invoice::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'invoice_number' => 'INV-' . Carbon::now()->format('Ym') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'invoice_date' => $createdTime,
                    'subtotal' => $order->subtotal,
                    'tax_amount' => $order->tax_amount,
                    'shipping_amount' => $order->shipping_amount + $order->cod_charge,
                    'discount_amount' => $order->discount_amount,
                    'total_amount' => $order->total_amount,
                ]
            );
        }

        // Seed Customer Reviews
        $reviewData = [
            [
                'product_sku' => 'HYU-LG-FRT-01',
                'customer_name' => 'Rohit Sharma',
                'rating' => 5,
                'title' => 'Exact OEM Fit for my Creta 2022!',
                'comment' => 'The chrome shine is identical to the showroom original. Installation was super easy with the pre-applied 3M tape. Highly recommended!',
                'is_verified_purchase' => true,
            ],
            [
                'product_sku' => 'SUZ-LG-01',
                'customer_name' => 'Vikram Patel',
                'rating' => 5,
                'title' => 'Top notch quality & fast delivery',
                'comment' => 'Replaced my Swift rear damaged S logo. Looks brand new and arrived in Gurugram within 48 hours in heavy protective packing.',
                'is_verified_purchase' => true,
            ],
            [
                'product_sku' => 'MONO-M-02',
                'customer_name' => 'Amitabh Sengupta',
                'rating' => 5,
                'title' => 'Heavy solid metal badge',
                'comment' => 'Solid metallic weight, not cheap plastic. The tricolor M stripes are vibrant and glossy.',
                'is_verified_purchase' => true,
            ],
            [
                'product_sku' => 'MOD-CRETA-01',
                'customer_name' => 'Deepak Verma',
                'rating' => 5,
                'title' => 'Perfect lettering spacing',
                'comment' => 'Came with an alignment stencil, so all letters in CRETA were positioned straight and aligned on the boot lid.',
                'is_verified_purchase' => true,
            ],
        ];

        foreach ($reviewData as $r) {
            $product = Product::where('sku', $r['product_sku'])->first();
            if ($product) {
                Review::updateOrCreate(
                    ['product_id' => $product->id, 'customer_name' => $r['customer_name']],
                    [
                        'user_id' => $customer?->id,
                        'customer_email' => 'customer@autologos.in',
                        'rating' => $r['rating'],
                        'title' => $r['title'],
                        'comment' => $r['comment'],
                        'is_verified_purchase' => $r['is_verified_purchase'],
                        'status' => 'approved',
                    ]
                );
            }
        }
    }
}
