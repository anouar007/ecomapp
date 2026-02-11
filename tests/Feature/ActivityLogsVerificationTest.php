<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityLogsVerificationTest extends TestCase
{
    public function test_activity_logs_access_and_filtering()
    {
        // 1. Setup Admin with proper permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $manageSettings = Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($manageSettings);
        
        $admin = User::firstOrCreate(
            ['email' => 'logs_verifier_admin@example.com'],
            ['name' => 'Logs Admin', 'password' => bcrypt('password')]
        );
        $admin->assignRole($adminRole);

        // 2. Seed some logs
        // Assuming ActivityLog model has these fields based on controller usage
        // Note: Models might be Spatie's or custom. Controller uses App\Models\ActivityLog.
        // If it's spatie/laravel-activitylog, it might be Activity model. 
        // Based on controller `use App\Models\ActivityLog;`, we use that.
        
        $log1 = ActivityLog::create([
            'log_name' => 'default',
            'description' => 'Test Activity 1',
            'event' => 'created',
            'subject_type' => 'App\Models\product',
            'subject_id' => 1,
            'user_id' => $admin->id,
            'properties' => [],
            'created_at' => now()
        ]);

        $log2 = ActivityLog::create([
             'log_name' => 'default',
             'description' => 'Searchable Term',
             'event' => 'updated',
             'subject_type' => 'App\Models\Order',
             'subject_id' => 2,
             'user_id' => $admin->id,
             'properties' => [],
             'created_at' => now()->subDays(1)
        ]);

        // 3. Test Index
        $response = $this->actingAs($admin)->get(route('activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Activity 1');

        // 4. Test Search/Filter
        $response = $this->actingAs($admin)->get(route('activity-logs.index', ['search' => 'Searchable']));
        $response->assertStatus(200);
        $response->assertSee('Searchable Term');
        $response->assertDontSee('Test Activity 1'); // Should be filtered out

        // 5. Test Show
        $response = $this->actingAs($admin)->get(route('activity-logs.show', $log1));
        $response->assertStatus(200);
        $response->assertSee('Test Activity 1');

        // 6. Test Clear (Maintenance)
        $uniqueDesc = 'Old Activity ' . time();
        $oldLog = ActivityLog::create([
            'log_name' => 'default',
            'event' => 'created',
            'description' => $uniqueDesc,
        ]);
        
        $oldLog->timestamps = false;
        $oldLog->created_at = now()->subDays(40);
        $oldLog->save();
        
        $response = $this->actingAs($admin)->post(route('activity-logs.clear'), [
            'older_than_days' => 30
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('activity_logs', ['description' => $uniqueDesc]);
        
        // Cleanup not strictly necessary if we don't mind logs accumulating, but good practice.
    }
}
