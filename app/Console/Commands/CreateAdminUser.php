<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin 
                            {email : The admin email address} 
                            {password : The admin password}
                            {--name=Admin : The admin name}';

    protected $description = 'Create a new admin user with all permissions';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all standard permissions if they don't exist
        $permissions = [
            'view_dashboard', 'manage_users', 'manage_roles',
            'manage_products', 'manage_categories', 'manage_inventory',
            'manage_coupons', 'manage_reviews', 'manage_returns',
            'manage_orders', 'manage_invoices', 'manage_customers',
            'manage_accounting', 'view_reports', 'view_analytics',
            'manage_settings', 'manage_content', 'view_activity_logs',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role and sync ALL permissions to it
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Create admin user and assign the Admin role
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole($adminRole);

        $this->info("✓ Admin user created successfully!");
        $this->info("  Email:       {$email}");
        $this->info("  Role:        Admin");
        $this->info("  Permissions: " . Permission::count() . " permissions assigned");
        $this->info("You can now login with this account.");

        return 0;
    }
}
