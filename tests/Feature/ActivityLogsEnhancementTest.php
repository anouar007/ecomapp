<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogsEnhancementTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::factory()->create([
            'name' => 'AuditAdminUnique',
            'email' => 'audit.admin.unique@winashop.test',
            'status' => 'active',
        ]);
        $admin->assignRole('Admin');

        return $admin;
    }

    public function test_activity_logs_filtering_search_by_ip_and_user_and_date_range(): void
    {
        $admin = $this->makeAdmin();
        $operator = User::factory()->create([
            'name' => 'NadiaOperatriceUnique',
            'email' => 'nadia.unique@winashop.test',
        ]);

        $targetLog = ActivityLog::create([
            'user_id' => $operator->id,
            'subject_type' => \App\Models\Product::class,
            'subject_id' => 9991,
            'event' => 'updated',
            'description' => 'Mise à jour tarif produit X',
            'properties' => [
                'old' => ['price' => 100],
                'new' => ['price' => 120],
            ],
            'ip_address' => '192.168.77.42',
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0.0.0 Safari/537.36',
        ]);
        $targetLog->forceFill(['created_at' => now()->subDays(2)])->saveQuietly();

        $securityLog = ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'deleted',
            'description' => 'Suppression critique test',
            'ip_address' => '10.0.0.5',
        ]);

        $exportLog = ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'exported',
            'description' => 'Export CSV test unique',
            'ip_address' => '10.0.0.8',
        ]);

        // Search by IP address
        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['search' => '192.168.77.42']))
            ->assertStatus(200)
            ->assertSee('Mise à jour tarif produit X')
            ->assertSee('Chrome • macOS')
            ->assertDontSee('Suppression critique test');

        // Search by operator name
        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['search' => 'NadiaOperatriceUnique']))
            ->assertStatus(200)
            ->assertSee('Mise à jour tarif produit X')
            ->assertDontSee('Suppression critique test');

        // Filter by event=security group
        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['event' => 'security']))
            ->assertStatus(200)
            ->assertSee('Suppression critique test')
            ->assertDontSee('Mise à jour tarif produit X');

        // Filter by date range (start_date + end_date)
        $twoDaysAgo = now()->subDays(2)->toDateString();
        $this->actingAs($admin)
            ->get(route('activity-logs.index', [
                'start_date' => $twoDaysAgo,
                'end_date' => $twoDaysAgo,
            ]))
            ->assertStatus(200)
            ->assertSee('Mise à jour tarif produit X')
            ->assertDontSee('Export CSV test unique');

        // Selectable per_page
        $this->actingAs($admin)
            ->get(route('activity-logs.index', ['per_page' => 25]))
            ->assertStatus(200);
    }

    public function test_activity_logs_csv_export_respects_filters(): void
    {
        $admin = $this->makeAdmin();

        ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'exported',
            'description' => 'LogExportMatchUnique',
            'ip_address' => '172.16.0.10',
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'login',
            'description' => 'LogExportExcludedUnique',
            'ip_address' => '172.16.0.11',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('activity-logs.export', ['search' => 'LogExportMatchUnique']));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));

        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('LogExportMatchUnique', $csv);
        $this->assertStringContainsString('172.16.0.10', $csv);
        $this->assertStringNotContainsString('LogExportExcludedUnique', $csv);
    }

    public function test_activity_log_changes_supports_paired_role_status_props_and_json_show(): void
    {
        $admin = $this->makeAdmin();

        $roleLog = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => 'updated',
            'description' => 'Modification des accès utilisateur',
            'properties' => [
                'old_status' => 'active',
                'new_status' => 'suspended',
                'old_roles' => ['Staff'],
                'new_roles' => ['Manager'],
            ],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0',
        ]);

        $changes = $roleLog->changes;
        $this->assertArrayHasKey('status', $changes);
        $this->assertSame('active', $changes['status']['old']);
        $this->assertSame('suspended', $changes['status']['new']);
        $this->assertArrayHasKey('roles', $changes);
        $this->assertSame('Firefox • Windows', $roleLog->user_agent_summary);

        $this->actingAs($admin)
            ->getJson(route('activity-logs.show', $roleLog))
            ->assertStatus(200)
            ->assertJsonPath('id', $roleLog->id)
            ->assertJsonPath('user_agent_summary', 'Firefox • Windows')
            ->assertJsonPath('changes.status.old', 'active')
            ->assertJsonPath('changes.status.new', 'suspended');

        // Full HTML show view also renders extra properties cleanly
        $this->actingAs($admin)
            ->get(route('activity-logs.show', $roleLog))
            ->assertStatus(200)
            ->assertSee('Firefox • Windows')
            ->assertSee('Propriétés & Contexte Enregistré', false);
    }

    public function test_clear_old_logs_supports_json_and_records_purge_audit(): void
    {
        $admin = $this->makeAdmin();

        $oldLog = ActivityLog::create([
            'user_id' => $admin->id,
            'event' => 'created',
            'description' => 'OldPurgeTargetUnique',
        ]);
        $oldLog->forceFill(['created_at' => now()->subDays(100)])->saveQuietly();

        $this->actingAs($admin)
            ->postJson(route('activity-logs.clear'), ['older_than_days' => 90])
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('activity_logs', ['id' => $oldLog->id]);
        $this->assertTrue(
            ActivityLog::where('event', 'cleared')
                ->where('user_id', $admin->id)
                ->exists()
        );
    }
}

