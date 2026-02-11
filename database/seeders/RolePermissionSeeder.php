<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view_dashboard',
            
            // Access Control
            'manage_users',
            'manage_roles',
            
            // Products & Inventory
            'manage_products',
            'manage_categories',
            'manage_inventory',
            'manage_coupons',
            'manage_reviews',
            'manage_returns',
            
            // Sales & Customers
            'manage_orders',
            'manage_invoices',
            'manage_customers',
            
            // Financials
            'manage_accounting',
            
            // Reports & Analytics
            'view_reports',
            'view_analytics',
            
            // System
            'manage_settings',
            'manage_content',     // Pages, Menus, Media
            'view_activity_logs',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'view_dashboard',
            'manage_products',
            'manage_categories',
            'manage_inventory',
            'manage_orders',
            'manage_invoices',
            'manage_customers',
            'view_reports',
            'manage_coupons',
            'manage_reviews',
            'manage_returns',
        ]);

        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions([
            'view_dashboard',
            'manage_orders',
            'manage_products', // Maybe read-only? But standard permission is manage
        ]);

        // Ensure Admin User has Admin Role (if exists)
        $adminUser = \App\Models\User::where('email', 'admin@speed.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Admin');
        }
    }
}
