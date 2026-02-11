<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersVerificationTest extends TestCase
{
    // Using RefreshDatabase might be safer for test isolation, 
    // but on a running dev env it wipes DB. I'll avoid it and clean up manually.
    
    public function test_users_index_page_loads_for_admin()
    {
        // Ensure Admin Role exists
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        
        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'test_admin_verifier@example.com'],
            ['name' => 'Test Admin Link', 'password' => bcrypt('password')]
        );
        $admin->assignRole($adminRole);

        // Act
        $response = $this->actingAs($admin)->get(route('users.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertSee('Users'); // Assuming title
    }

    public function test_admin_can_update_user_roles()
    {
        // Setup
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::where('email', 'test_admin_verifier@example.com')->first();
        if (!$admin) {
            $admin = User::create(['name' => 'Admin', 'email' => 'test_admin_verifier@example.com', 'password' => bcrypt('password')]);
            $admin->assignRole($adminRole);
        }

        $targetUser = User::create([
            'name' => 'Target User',
            'email' => 'target_user_'.time().'@example.com',
            'password' => bcrypt('password')
        ]);
        
        $newRole = Role::create(['name' => 'Manager_'.time(), 'guard_name' => 'web']);

        // Act
        $response = $this->actingAs($admin)->put(route('users.update-roles', $targetUser), [
            'roles' => [$newRole->id]
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertTrue($targetUser->fresh()->hasRole($newRole->name));

        // Signup Cleanup
        $targetUser->delete();
        $newRole->delete(); 
    }
}
