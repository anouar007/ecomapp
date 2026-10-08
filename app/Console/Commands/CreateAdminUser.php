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
                            {email=admin@speed.com : The admin email address} 
                            {password=password : The admin password}
                            {--name=Super Administrateur : The admin name}';

    protected $description = 'Create or update an admin user with full permissions and dashboard access';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name') ?: 'Super Administrateur';

        // Ensure roles & permissions exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $adminRole->syncPermissions($allPermissions);
        }

        $admin = User::where('email', $email)->first();

        if ($admin) {
            $admin->update([
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => $admin->email_verified_at ?: now(),
            ]);
            $this->info("✓ Compte administrateur existant mis à jour avec succès !");
        } else {
            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            $this->info("✓ Nouvel administrateur créé avec succès !");
        }

        $admin->syncRoles([$adminRole]);
        if ($allPermissions->isNotEmpty()) {
            $admin->syncPermissions($allPermissions);
        }

        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Nom', $admin->name],
                ['Email', $admin->email],
                ['Mot de passe', $password],
                ['Rôle', 'Admin (Accès total au Dashboard)'],
                ['Permissions', $allPermissions->count() . ' permissions accordées'],
                ['Connexion', url('/login')],
                ['Tableau de bord', url('/dashboard')],
            ]
        );

        return 0;
    }
}
