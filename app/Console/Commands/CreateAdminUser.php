<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUser extends Command
{
    protected $signature = 'make:admin 
                            {email : The admin email address} 
                            {password : The admin password}
                            {--name=Admin : The admin name}';

    protected $description = 'Create or update an admin user with full access (all permissions)';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');

        // Ensure roles and permissions exist
        if (Permission::count() === 0) {
            $this->info('Seeding roles and permissions...');
            $this->call('db:seed', ['--class' => 'Database\Seeders\RolePermissionSeeder', '--force' => true]);
        }

        // Find existing user or create a new one
        $admin = User::firstOrNew(['email' => $email]);
        $isNew = !$admin->exists;

        $admin->name = $name ?: ($admin->name ?? 'Admin');
        $admin->password = Hash::make($password);
        $admin->email_verified_at = now();
        $admin->save();

        // Ensure Admin role has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Assign Admin role to user
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole($adminRole);
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        if ($isNew) {
            $this->info("✓ Admin user created successfully with FULL ACCESS!");
        } else {
            $this->info("✓ Existing user updated to Admin with FULL ACCESS!");
        }

        $this->info("Email: {$email}");
        $this->info("Role: Admin (" . $adminRole->permissions()->count() . " permissions granted)");
        $this->info("Login URL: " . url('/login'));

        return 0;
    }
}
