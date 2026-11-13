<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementEnhancementTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdminActor(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');

        return $admin;
    }

    protected function makeDelegatedManagerActor(): User
    {
        $perm = Permission::firstOrCreate(['name' => 'manage_users', 'guard_name' => 'web']);
        $actor = User::factory()->create(['status' => 'active']);
        $actor->givePermissionTo($perm);

        return $actor;
    }

    public function test_search_filters_scope_status_per_page_and_custom_roles_work_without_errors(): void
    {
        $admin = $this->makeAdminActor();

        $customRole = Role::firstOrCreate(['name' => 'CaissierTestRole', 'guard_name' => 'web']);
        $perm = Permission::firstOrCreate(['name' => 'access_pos', 'guard_name' => 'web']);
        $customRole->givePermissionTo($perm);

        $matchUser = User::factory()->create([
            'name' => 'YoussefRechercheUnique',
            'email' => 'youssef.unique@winashop.test',
            'phone' => '0612349988',
            'job_title' => 'Superviseur Caisse',
            'status' => 'inactive',
            'last_login_at' => null,
        ]);
        $matchUser->assignRole($customRole);

        $suspendedUser = User::factory()->create([
            'name' => 'SalmaSuspendueUnique',
            'status' => 'suspended',
        ]);
        $suspendedUser->assignRole('Staff');

        $onlineUser = User::factory()->create([
            'name' => 'KarimEnLigneUnique',
            'status' => 'active',
            'last_login_at' => now()->subMinutes(2),
        ]);
        $onlineUser->assignRole('Manager');

        $customerOnly = User::factory()->create([
            'name' => 'ClientSansRoleUnique',
            'status' => 'active',
        ]);

        // Search by name, email, phone, and job_title must not throw BadMethodCallException (scopeSearch bug)
        foreach (['YoussefRechercheUnique', 'youssef.unique@winashop.test', '0612349988', 'Superviseur Caisse'] as $term) {
            $response = $this->actingAs($admin)->get(route('users.index', ['search' => $term]));
            $response->assertStatus(200)
                ->assertSee('YoussefRechercheUnique')
                ->assertDontSee('KarimEnLigneUnique');
        }

        // Custom role filter + custom UI card class
        $this->actingAs($admin)
            ->get(route('users.index', ['role' => 'CaissierTestRole']))
            ->assertStatus(200)
            ->assertSee('YoussefRechercheUnique')
            ->assertSee('CaissierTestRole')
            ->assertSee('selected-custom')
            ->assertDontSee('SalmaSuspendueUnique');

        // Blocked status filter includes both inactive & suspended
        $this->actingAs($admin)
            ->get(route('users.index', ['status' => 'blocked']))
            ->assertStatus(200)
            ->assertSee('YoussefRechercheUnique')
            ->assertSee('SalmaSuspendueUnique')
            ->assertDontSee('KarimEnLigneUnique');

        // Online status filter returns recently active users only
        $this->actingAs($admin)
            ->get(route('users.index', ['status' => 'online']))
            ->assertStatus(200)
            ->assertSee('KarimEnLigneUnique')
            ->assertDontSee('YoussefRechercheUnique');

        // Scope filter: team vs customers
        $this->actingAs($admin)
            ->get(route('users.index', ['scope' => 'team']))
            ->assertStatus(200)
            ->assertSee('YoussefRechercheUnique')
            ->assertDontSee('ClientSansRoleUnique');

        $this->actingAs($admin)
            ->get(route('users.index', ['scope' => 'customers']))
            ->assertStatus(200)
            ->assertSee('ClientSansRoleUnique')
            ->assertDontSee('YoussefRechercheUnique');

        // Per-page pagination parameter renders cleanly
        $this->actingAs($admin)
            ->get(route('users.index', ['per_page' => 25]))
            ->assertStatus(200);
    }

    public function test_csv_export_respects_filters_and_streams_utf8_bom_csv(): void
    {
        $admin = $this->makeAdminActor();

        $included = User::factory()->create([
            'name' => 'ExportIncludedAgent',
            'email' => 'export.included@winashop.test',
            'job_title' => 'Logistique',
            'status' => 'active',
        ]);
        $included->assignRole('Staff');

        $excluded = User::factory()->create([
            'name' => 'ExportExcludedAgent',
            'email' => 'export.excluded@winashop.test',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('users.export', ['search' => 'ExportIncludedAgent', 'status' => 'active']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));

        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('ExportIncludedAgent', $csv);
        $this->assertStringContainsString('export.included@winashop.test', $csv);
        $this->assertStringNotContainsString('ExportExcludedAgent', $csv);
    }

    public function test_bulk_actions_update_targets_safely_and_enforce_admin_boundaries(): void
    {
        $admin = $this->makeAdminActor();
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        $u1 = User::factory()->create(['status' => 'active']);
        $u1->assignRole($staffRole);
        $u2 = User::factory()->create(['status' => 'active']);
        $u2->assignRole($staffRole);

        // Bulk deactivate (includes self, which must remain active)
        $this->actingAs($admin)
            ->postJson(route('users.bulk-action'), [
                'action' => 'deactivate',
                'user_ids' => [$u1->id, $u2->id, $admin->id],
            ])
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('processed', 2);

        $this->assertSame('inactive', $u1->fresh()->status);
        $this->assertSame('inactive', $u2->fresh()->status);
        $this->assertSame('active', $admin->fresh()->status);

        // Bulk activate + assign role
        $this->actingAs($admin)
            ->postJson(route('users.bulk-action'), [
                'action' => 'activate',
                'user_ids' => [$u1->id, $u2->id],
            ])
            ->assertStatus(200)
            ->assertJsonPath('processed', 2);

        $this->assertSame('active', $u1->fresh()->status);

        $this->actingAs($admin)
            ->postJson(route('users.bulk-action'), [
                'action' => 'assign_role',
                'user_ids' => [$u1->id, $u2->id],
                'role' => $managerRole->name,
            ])
            ->assertStatus(200)
            ->assertJsonPath('processed', 2);

        $this->assertTrue($u1->fresh()->hasRole('Manager'));
        $this->assertTrue($u2->fresh()->hasRole('Manager'));

        // Non-Admin with manage_users cannot assign Admin role in bulk
        $delegated = $this->makeDelegatedManagerActor();
        $this->actingAs($delegated)
            ->postJson(route('users.bulk-action'), [
                'action' => 'assign_role',
                'user_ids' => [$u1->id],
                'role' => 'Admin',
            ])
            ->assertStatus(403);

        // Non-Admin cannot bulk-mutate a batch containing an Admin user (atomic pre-check blocks u1 mutation too)
        $this->actingAs($delegated)
            ->postJson(route('users.bulk-action'), [
                'action' => 'deactivate',
                'user_ids' => [$u1->id, $admin->id],
            ])
            ->assertStatus(403);

        $this->assertSame('active', $u1->fresh()->status);
    }

    public function test_privilege_escalation_show_json_validation_and_session_revocation(): void
    {
        $admin = $this->makeAdminActor();
        $delegated = $this->makeDelegatedManagerActor();
        $staffUser = User::factory()->create([
            'status' => 'active',
            'remember_token' => 'existing-remember-token-1234',
        ]);
        $staffUser->assignRole('Staff');

        // Non-Admin cannot inspect an Admin user via show()
        $this->actingAs($delegated)
            ->getJson(route('users.show', $admin))
            ->assertStatus(403);

        // Allowed target loads cleanly with direct_permissions + roles
        $this->actingAs($delegated)
            ->getJson(route('users.show', $staffUser))
            ->assertStatus(200)
            ->assertJsonPath('id', $staffUser->id)
            ->assertJsonPath('roles.0', 'Staff');

        // JSON validation returns 422
        $this->actingAs($admin)
            ->postJson(route('users.store'), [
                'name' => '',
                'email' => 'not-an-email',
                'password' => '123',
                'status' => 'active',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->insert([
                'id' => 'test-session-' . $staffUser->id,
                'user_id' => $staffUser->id,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'PHPUnit',
                'payload' => 'payload',
                'last_activity' => time(),
            ]);
        }

        // Suspending user via JSON update succeeds and revokes remember_token + sessions + logs change
        $this->actingAs($admin)
            ->putJson(route('users.update', $staffUser), [
                'name' => 'Staff Updated Name',
                'email' => $staffUser->email,
                'status' => 'suspended',
                'roles' => ['Staff'],
            ])
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $freshStaff = $staffUser->fresh();
        $this->assertSame('suspended', $freshStaff->status);
        $this->assertNotSame('existing-remember-token-1234', $freshStaff->remember_token);
        if (Schema::hasTable('sessions')) {
            $this->assertDatabaseMissing('sessions', ['id' => 'test-session-' . $staffUser->id]);
        }
        $this->assertTrue(
            ActivityLog::where('event', 'updated')
                ->where('subject_id', $staffUser->id)
                ->exists()
        );

        // Self-delete returns 403 JSON; deleting target succeeds via JSON
        $this->actingAs($admin)
            ->deleteJson(route('users.destroy', $admin))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->deleteJson(route('users.destroy', $staffUser))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $staffUser->id]);
    }
}
