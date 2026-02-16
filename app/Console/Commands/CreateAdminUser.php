<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
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

        // Create admin user
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Assign Admin role (make sure roles are seeded first)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $admin->assignRole($adminRole);

        $this->info("✓ Admin user created successfully!");
        $this->info("Email: {$email}");
        $this->info("You can now login with this account.");

        return 0;
    }
}
