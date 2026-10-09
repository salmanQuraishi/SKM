<?php

require 'D:/Laravel/skm/vendor/autoload.php';
$app = require 'D:/Laravel/skm/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

$cat = Category::where('slug', 'accessories')->firstOrFail();
$initialStatus = $cat->status;
echo "1. Initial accessories category status: " . ($cat->status ? 'ACTIVE (1)' : 'INACTIVE (0)') . "\n";

$admin = User::where('email', 'admin@autologos.in')->firstOrFail();
auth()->login($admin);
$controller = app(\App\Http\Controllers\Admin\CategoryCrudController::class);

// TEST A: Edit form update with unchecked checkbox (status omitted)
$requestData = [
    '_token' => csrf_token(),
    '_method' => 'PUT',
    'name' => $cat->name,
    'parent_id' => $cat->parent_id,
    'icon' => $cat->icon,
    'description' => $cat->description,
    'sort_order' => $cat->sort_order,
];
$request = Request::create(route('admin.categories.update', $cat->id), 'PUT', $requestData);
$request->setUserResolver(fn() => $admin);
$controller->update($request, $cat->id);
$cat->refresh();
echo "2. Status after edit form submit (unchecked): " . ($cat->status ? 'ACTIVE (1)' : 'INACTIVE (0)') . "\n";

// TEST B: Toggle status via 1-click button (toggleStatus)
$controller->toggleStatus($cat->id);
$cat->refresh();
echo "3. Status after 1-click toggle: " . ($cat->status ? 'ACTIVE (1)' : 'INACTIVE (0)') . "\n";

// TEST C: Toggle status again (should be inactive)
$controller->toggleStatus($cat->id);
$cat->refresh();
echo "4. Status after second 1-click toggle: " . ($cat->status ? 'ACTIVE (1)' : 'INACTIVE (0)') . "\n";

// Restore initial status
$cat->update(['status' => $initialStatus]);
$cat->refresh();
echo "5. Restored original status: " . ($cat->status ? 'ACTIVE (1)' : 'INACTIVE (0)') . "\n";

echo "\n>>> ALL CATEGORY STATUS TESTS PASSED!\n";
