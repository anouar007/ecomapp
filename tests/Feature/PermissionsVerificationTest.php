<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionsVerificationTest extends TestCase
{
    public function test_permissions_crud_lifecycle()
    {
        // 1. Setup Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::firstOrCreate(
            ['email' => 'perms_verifier_admin@example.com'],
            ['name' => 'Perms Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($adminRole);

        // 2. Test Create Permission
        $permName = 'test_permission_' . time();
        $response = $this->actingAs($admin)->post(route('permissions.store'), [
            'name' => $permName
        ]);
        
        $response->assertRedirect(route('permissions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('permissions', ['name' => $permName]);
        
        $permission = Permission::where('name', $permName)->first();

        // 3. Test Read
        $response = $this->actingAs($admin)->get(route('permissions.index'));
        $response->assertStatus(200);
        $response->assertSee($permName);

        // 4. Test Update
        $updatedPermName = 'updated_permission_' . time();
        $response = $this->actingAs($admin)->put(route('permissions.update', $permission), [
            'name' => $updatedPermName
        ]);
        
        $response->assertRedirect(route('permissions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('permissions', ['name' => $updatedPermName]);

        // 5. Test Delete
        $response = $this->actingAs($admin)->delete(route('permissions.destroy', $permission));
        
        $response->assertRedirect(route('permissions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('permissions', ['name' => $updatedPermName]);
    }
}
