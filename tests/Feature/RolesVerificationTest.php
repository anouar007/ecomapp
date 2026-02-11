<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesVerificationTest extends TestCase
{
    // Avoiding RefreshDatabase to persist current dev state, handling cleanup manually.

    public function test_roles_crud_lifecycle()
    {
        // 1. Setup Admin User
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::firstOrCreate(
            ['email' => 'roles_verifier_admin@example.com'],
            ['name' => 'Roles Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($adminRole);

        // 2. Test Create Role
        $roleName = 'Test_Role_' . time();
        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'name' => $roleName,
            'permissions' => [] // Optional
        ]);
        
        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('roles', ['name' => $roleName]);
        $role = Role::where('name', $roleName)->first();

        // 3. Test Read (Index)
        $response = $this->actingAs($admin)->get(route('roles.index'));
        $response->assertStatus(200);
        $response->assertSee($roleName);

        // 4. Test Update
        $updatedRoleName = 'Updated_Role_' . time();
        $response = $this->actingAs($admin)->put(route('roles.update', $role), [
            'name' => $updatedRoleName
        ]);
        
        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', ['name' => $updatedRoleName]);
        $this->assertDatabaseMissing('roles', ['name' => $roleName]);

        // 5. Test Delete
        $response = $this->actingAs($admin)->delete(route('roles.destroy', $role));
        
        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['name' => $updatedRoleName]);
        
        // Cleanup User
        // $admin->delete(); // Keep admin for future tests if needed, or delete.
    }
}
