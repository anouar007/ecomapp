<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Setting;
use App\Models\CustomCode;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingsVerificationTest extends TestCase
{
    public function test_settings_and_custom_codes_lifecycle()
    {
        // 1. Setup Admin
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        
        $admin = User::firstOrCreate(
            ['email' => 'settings_verifier_admin@example.com'],
            ['name' => 'Settings Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($role);

        // 2. Test Settings Index
        $response = $this->actingAs($admin)->get(route('settings.index'));
        $response->assertStatus(200);

        // 3. Test Update Setting
        // Create a dummy setting first to ensure it exists for update
        Setting::set('app_name', 'Original Name', 'string', 'general');
        
        $response = $this->actingAs($admin)->put(route('settings.update'), [
            'settings' => [
                'app_name' => 'Updated App Name'
            ]
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertEquals('Updated App Name', Setting::get('app_name'));

        // 4. Test Logo Upload
        Storage::fake('public');
        $file = UploadedFile::fake()->image('logo.jpg');
        
        $response = $this->actingAs($admin)->post(route('settings.logo'), [
            'logo' => $file
        ]);
        $response->assertRedirect();
        
        // 5. Custom Code CRUD
        // Create
        $response = $this->actingAs($admin)->post(route('custom-codes.store'), [
            'title' => 'Test Snippet',
            'type' => 'js',
            'position' => 'head',
            'content' => 'console.log("test");',
            'is_active' => 1
        ]);
        $response->assertRedirect(route('custom-codes.index'));
        $this->assertDatabaseHas('custom_codes', ['title' => 'Test Snippet']);
        
        $snippet = CustomCode::where('title', 'Test Snippet')->first();

        // Index
        $response = $this->actingAs($admin)->get(route('custom-codes.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Snippet');

        // Update
        $response = $this->actingAs($admin)->put(route('custom-codes.update', $snippet), [
            'title' => 'Updated Snippet',
            'type' => 'js',
            'position' => 'head',
            'content' => 'console.log("updated");',
            'is_active' => 1
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('custom_codes', ['title' => 'Updated Snippet']);

        // Delete
        $response = $this->actingAs($admin)->delete(route('custom-codes.destroy', $snippet));
        $response->assertRedirect();
        $this->assertDatabaseMissing('custom_codes', ['id' => $snippet->id]);
    }
}
