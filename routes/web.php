<?php

use Illuminate\Support\Facades\Route;

// Frontend Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Payment\RazorpayWebhookController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleDependentDropdownController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\Admin\CategoryCrudController;
use App\Http\Controllers\Admin\BrandCrudController;
use App\Http\Controllers\Admin\CarModelCrudController;
use App\Http\Controllers\Admin\CarVariantCrudController;
use App\Http\Controllers\Admin\OrderCrudController;
use App\Http\Controllers\Admin\CustomerCrudController;
use App\Http\Controllers\Admin\CouponCrudController;
use App\Http\Controllers\Admin\BannerCrudController;
use App\Http\Controllers\Admin\ReviewModerationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CmsPageCrudController;
use App\Http\Controllers\Admin\ContactInquiryController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/search/suggestions', [HomeController::class, 'searchSuggestions'])->name('search.suggestions');

// Shop & Catalog
Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/product/{slug}', [ProductController::class, 'detail'])->name('product.detail');
Route::get('/category/{slug}', function ($slug) {
    return redirect()->route('shop', ['category' => $slug]);
})->name('category.products');
Route::get('/brand/{slug}', function ($slug) {
    return redirect()->route('shop', ['brand' => $slug]);
})->name('brand.products');
Route::get('/model/{slug}', function ($slug) {
    return redirect()->route('shop', ['model' => $slug]);
})->name('model.products');

// Vehicle Hierarchy Flow (Company -> Car Model -> Variant -> Products)
Route::get('/vehicles', [VehicleController::class, 'companies'])->name('vehicles');
Route::get('/vehicles/companies', [VehicleController::class, 'companies'])->name('vehicle.companies');
Route::get('/vehicle/{companySlug}', [VehicleController::class, 'cars'])->name('vehicle.cars');
Route::get('/vehicle/{companySlug}/{carSlug}', [VehicleController::class, 'variants'])->name('vehicle.variants');
Route::get('/vehicle/{companySlug}/{carSlug}/{variantSlug}', [VehicleController::class, 'products'])->name('vehicle.products');

// AJAX Dependent Dropdowns
Route::get('/ajax/models-by-brand/{brand_id}', [VehicleDependentDropdownController::class, 'getModelsByBrand'])->name('ajax.models_by_brand');
Route::get('/ajax/variants-by-model/{model_id}', [VehicleDependentDropdownController::class, 'getVariantsByModel'])->name('ajax.variants_by_model');

Route::get('/api/pincode/check', [ProductController::class, 'checkPincode'])->name('pincode.check');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::get('/cart/coupon/apply', fn() => redirect()->route('cart.index'));
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/cart/coupon/remove', fn() => redirect()->route('cart.index'));
Route::get('/cart/summary', [CartController::class, 'getSummary'])->name('cart.summary');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/checkout/verify-payment', [CheckoutController::class, 'verifyPayment'])->name('checkout.verify-payment');
Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handleWebhook'])->name('razorpay.webhook');
Route::get('/order-success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('order.track.view');

// Wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle/{product_id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/move-to-cart/{product_id}', [WishlistController::class, 'moveToCart'])->name('wishlist.move_to_cart');

// Review
Route::post('/product/{id}/review', [ReviewController::class, 'store'])->name('review.store');

// Static CMS Pages & Contact
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact/submit', [PageController::class, 'submitContact'])->name('contact.submit');

// Customer Authentication
Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [CustomerAuthController::class, 'login'])->name('login.submit');
Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

// Customer Account Portal (Protected)
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders');
    Route::get('/order/{order_number}', [CustomerAccountController::class, 'orderDetail'])->name('order.detail');
    Route::get('/order/{order_number}/invoice', [CustomerAccountController::class, 'downloadInvoice'])->name('order.invoice');
    Route::get('/order/{order_number}/receipt', [CustomerAccountController::class, 'downloadReceipt'])->name('order.receipt');
    Route::get('/addresses', [CustomerAccountController::class, 'addresses'])->name('addresses');
    Route::post('/address/store', [CustomerAccountController::class, 'storeAddress'])->name('address.store');
    Route::delete('/address/{id}', [CustomerAccountController::class, 'deleteAddress'])->name('address.delete');
    Route::post('/profile/update', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (/admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/catalog-analytics', [DashboardController::class, 'catalogAnalytics'])->name('catalog.analytics');

        // Products
        Route::resource('products', ProductCrudController::class);
        Route::post('products/{id}/duplicate', [ProductCrudController::class, 'duplicate'])->name('products.duplicate');
        Route::delete('products/image/{id}', [ProductCrudController::class, 'deleteImage'])->name('products.delete_image');
        Route::get('products/image/{id}/delete', [ProductCrudController::class, 'deleteImage'])->name('products.image.delete');
        Route::post('products/bulk/action', [ProductCrudController::class, 'bulkAction'])->name('products.bulk_action');
        Route::post('products/bulk', [ProductCrudController::class, 'bulkAction'])->name('products.bulk');

        // Categories
        Route::resource('categories', CategoryCrudController::class);

        // Brands
        Route::resource('brands', BrandCrudController::class);

        // Car Models
        Route::resource('models', CarModelCrudController::class);

        // Car Variants
        Route::resource('variants', CarVariantCrudController::class);

        // Orders
        Route::resource('orders', OrderCrudController::class)->only(['index', 'show']);
        Route::post('orders/{id}/status', [OrderCrudController::class, 'updateStatus'])->name('orders.update_status');
        Route::post('orders/{id}/status/update', [OrderCrudController::class, 'updateStatus'])->name('orders.status.update');
        Route::get('orders/{id}/invoice/download', [OrderCrudController::class, 'downloadInvoice'])->name('orders.invoice.download');
        Route::get('orders/{id}/invoice/stream', [OrderCrudController::class, 'streamInvoice'])->name('orders.invoice.stream');
        Route::get('orders/{id}/shipping-slip/download', [OrderCrudController::class, 'downloadShippingSlip'])->name('orders.shipping_slip.download');
        Route::get('orders/{id}/shipping/download', [OrderCrudController::class, 'downloadShippingSlip'])->name('orders.shipping.download');
        Route::get('orders/{id}/shipping-slip/stream', [OrderCrudController::class, 'streamShippingSlip'])->name('orders.shipping_slip.stream');
        Route::get('orders/{id}/receipt/download', [OrderCrudController::class, 'downloadReceipt'])->name('orders.receipt.download');
        Route::post('orders/{id}/whatsapp', [OrderCrudController::class, 'sendWhatsAppNotification'])->name('orders.send_whatsapp');

        // Customers
        Route::resource('customers', CustomerCrudController::class)->only(['index', 'show', 'destroy']);
        Route::post('customers/{id}/toggle-status', [CustomerCrudController::class, 'toggleStatus'])->name('customers.toggle_status');
        Route::post('customers/{id}/toggle', [CustomerCrudController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Coupons
        Route::resource('coupons', CouponCrudController::class);

        // Banners
        Route::resource('banners', BannerCrudController::class);

        // Reviews
        Route::resource('reviews', ReviewModerationController::class)->only(['index', 'destroy']);
        Route::post('reviews/{id}/status', [ReviewModerationController::class, 'updateStatus'])->name('reviews.update_status');
        Route::post('reviews/{id}/status/update', [ReviewModerationController::class, 'updateStatus'])->name('reviews.status');

        // Reports
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/sales/export', [ReportController::class, 'exportSalesCsv'])->name('reports.sales.export');

        // CMS Pages
        Route::resource('pages', CmsPageCrudController::class);

        // Contact Inquiries
        Route::resource('inquiries', ContactInquiryController::class)->only(['index', 'destroy']);
        Route::post('inquiries/{id}/read', [ContactInquiryController::class, 'markAsRead'])->name('inquiries.read');

        // Settings & System Cache
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::match(['post', 'put'], 'settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('clear-cache', [SettingController::class, 'clearCache'])->name('cache.clear');
    });
});

/*
|--------------------------------------------------------------------------
| Storage Asset Fallback Route (For Shared Hosting & Environments without Symlinks)
|--------------------------------------------------------------------------
*/
Route::get('storage/{path}', function ($path) {
    if (str_contains($path, '..') || str_contains($path, './')) {
        abort(404);
    }

    $filePath = storage_path('app/public/' . ltrim($path, '/'));
    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404);
    }

    $realPath = realpath($filePath);
    $realStorage = realpath(storage_path('app/public'));
    if (!$realPath || !$realStorage || !str_starts_with($realPath, $realStorage)) {
        abort(404);
    }

    $mime = mime_content_type($realPath) ?: 'application/octet-stream';
    return response()->file($realPath, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.fallback');
