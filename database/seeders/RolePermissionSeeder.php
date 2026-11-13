<?php

namespace Database\Seeders;

use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = collect(UserController::getPermissionModules())
            ->flatMap(fn (array $module) => array_keys($module['permissions']))
            ->unique()
            ->values()
            ->all();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin gets every permission
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Manager gets operational dashboard widgets, catalog, orders, POS, inventory, customers & reports
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view_dashboard',
            'dashboard_quick_actions',
            'dashboard_kpis',
            'dashboard_charts',
            'dashboard_recent_orders',
            'dashboard_stock_alerts',
            'manage_products',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_categories',
            'manage_brands',
            'manage_product_tags',
            'manage_inventory',
            'view_inventory',
            'adjust_inventory',
            'transfer_inventory',
            'manage_stores',
            'manage_orders',
            'view_orders',
            'create_orders',
            'edit_orders',
            'access_pos',
            'manage_invoices',
            'view_customers',
            'manage_customers',
            'manage_client_credits',
            'manage_expenses',
            'view_reports',
            'manage_coupons',
            'manage_reviews',
            'manage_returns',
        ]);

        // Staff gets focused daily operational permissions
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions([
            'view_dashboard',
            'dashboard_quick_actions',
            'dashboard_recent_orders',
            'dashboard_stock_alerts',
            'manage_orders',
            'view_orders',
            'create_orders',
            'edit_orders',
            'access_pos',
            'view_products',
            'view_inventory',
        ]);

        // Ensure default Super Admin account exists with full permissions
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@speed.com'],
            [
                'name' => 'Super Administrateur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('Admin');
        $adminUser->syncPermissions(Permission::all());
    }
}
