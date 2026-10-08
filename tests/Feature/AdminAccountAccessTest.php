<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminAccountAccessTest extends TestCase
{
    /**
     * Test login with the created super admin account and verify redirection to dashboard.
     */
    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@speed.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Tableau de bord');
    }

    /**
     * Test that the admin has full access to all protected dashboard modules.
     */
    public function test_admin_has_full_access_to_all_modules(): void
    {
        $admin = User::where('email', 'admin@speed.com')->first();
        $this->assertNotNull($admin);

        $this->actingAs($admin);

        $protectedRoutes = [
            '/dashboard',
            '/banners',
            '/products',
            '/categories',
            '/orders',
            '/invoices',
            '/inventory',
            '/settings',
            '/users',
            '/roles',
            '/activity-logs',
            '/reviews',
        ];

        foreach ($protectedRoutes as $route) {
            $res = $this->get($route);
            $this->assertNotEquals(
                403, 
                $res->status(), 
                "Admin should not receive 403 Forbidden on route: {$route}"
            );
            $this->assertContains(
                $res->status(), 
                [200, 302], 
                "Route {$route} should return 200 or 302 (not forbidden or error)"
            );
        }
    }
}
