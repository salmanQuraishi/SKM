<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Find or create the definitive unified Customer role
        $customerRole = Role::where('slug', 'customer')->first();
        if (!$customerRole) {
            $customerRole = Role::create([
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Standard customer store account with shopping, order tracking, and profile access.',
                'is_admin' => false,
                'is_system' => true,
            ]);
        } else {
            $customerRole->update([
                'name' => 'Customer',
                'description' => 'Standard customer store account with shopping, order tracking, and profile access.',
                'is_admin' => false,
                'is_system' => true,
            ]);
        }

        // 2. Detach any permissions from Customer role
        $customerRole->permissions()->sync([]);

        // 3. Find any redundant 'user' role
        $userRole = Role::where('slug', 'user')->first();
        if ($userRole && $userRole->id !== $customerRole->id) {
            // Reassign any users mapped to 'user' role to 'customer' role
            User::where('role_id', $userRole->id)->update(['role_id' => $customerRole->id]);

            // Detach permissions and delete redundant role
            DB::table('role_permission')->where('role_id', $userRole->id)->delete();
            $userRole->delete();
        }

        // 4. Ensure any users with null role_id are set to customer
        User::whereNull('role_id')->update(['role_id' => $customerRole->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible if ever needed
    }
};
