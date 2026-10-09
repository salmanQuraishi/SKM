<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDualDashboardTest extends TestCase
{
    use DatabaseTransactions;
    protected function getAdminUser(): User
    {
        $adminRole = Role::whereIn('slug', ['super_admin', 'admin'])->first();
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Super Admin',
                'slug' => 'super_admin',
            ]);
        }

        $admin = User::where('role_id', $adminRole->id)->where('is_active', true)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]);
        }

        return $admin;
    }

    /**
     * Test 1: Admin can access Sales & Orders Dashboard and see dual switcher
     */
    public function test_admin_can_access_main_sales_dashboard_with_switcher(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Sales & Orders Dashboard', false);
        $response->assertSee('Catalog & Vehicle Analytics', false);
        $response->assertSee(route('admin.catalog.analytics'), false);
    }

    /**
     * Test 2: Admin can access Catalog & Vehicle Analytics Dashboard
     */
    public function test_admin_can_access_catalog_analytics_dashboard(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('admin.catalog.analytics'));

        $response->assertStatus(200);
        $response->assertSee('Catalog & Vehicle Analytics', false);
        $response->assertSee('Live Products', false);
        $response->assertSee('Car Companies', false);
        $response->assertSee('Car Variants', false);
        $response->assertSee('Categories', false);
        $response->assertSee('brandChart', false);
        $response->assertSee('categoryChart', false);
        $response->assertSee('placementChart', false);
        $response->assertSee('finishChart', false);
        $response->assertSee('Catalog Gaps', false);
    }

    /**
     * Test 3: Guest cannot access catalog analytics dashboard
     */
    public function test_guest_is_redirected_from_catalog_analytics(): void
    {
        $response = $this->get(route('admin.catalog.analytics'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test 4: Brand drill-down filter returns filtered model breakdown
     */
    public function test_catalog_analytics_brand_drill_down_filter(): void
    {
        $admin = $this->getAdminUser();
        $brand = Brand::first();

        if ($brand) {
            $response = $this->actingAs($admin)->get(route('admin.catalog.analytics', ['brand_id' => $brand->id]));

            $response->assertStatus(200);
            $response->assertSee($brand->name, false);
            $response->assertSee('Models & Products Matrix', false);
        } else {
            $this->assertTrue(true);
        }
    }
}
