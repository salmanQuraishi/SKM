<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_gateway')->default('cod'); // razorpay, cod
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable()->index();
            $table->string('razorpay_signature')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('INR');
            $table->string('status')->default('pending'); // pending, captured, failed, refunded
            $table->string('payment_method_type')->nullable(); // upi, card, netbanking, cod
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('shipment_number')->unique()->index();
            $table->string('courier_name');
            $table->string('tracking_number')->index();
            $table->string('tracking_url')->nullable();
            $table->date('shipping_date')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->string('shipment_status')->default('manifested');
            $table->string('shipping_slip_path')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('invoice_number')->unique()->index();
            $table->date('invoice_date');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('receipt_number')->unique()->index();
            $table->date('receipt_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
    }
};
