<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExpenseInterfaceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
        $this->admin = User::where('email', 'admin@speed.com')->firstOrFail();
    }

    public function test_expenses_page_renders_kpis_chart_and_custom_date_filter(): void
    {
        Expense::create([
            'expense_number' => 'EXP-UI-1',
            'title' => 'Loyer Bureau',
            'category' => 'rent',
            'amount' => 3500,
            'expense_date' => '2026-11-10',
            'payment_method' => 'bank_transfer',
            'status' => 'paid',
            'user_id' => $this->admin->id,
        ]);

        Expense::create([
            'expense_number' => 'EXP-UI-2',
            'title' => 'Facture Internet',
            'category' => 'utilities',
            'amount' => 450,
            'expense_date' => '2026-11-11',
            'payment_method' => 'cash',
            'status' => 'pending',
            'user_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/expenses?period=custom&start_date=2026-11-01&end_date=2026-11-13');

        $response->assertStatus(200);
        $response->assertSee('Gestion des Dépenses', false);
        $response->assertSee('expenseTrendChart', false);
        $response->assertSee('expenseCategoryChart', false);
        $response->assertSee('name="start_date"', false);
        $response->assertSee('name="end_date"', false);
        $response->assertSee('Loyer Bureau', false);

        $pendingOnly = $this->actingAs($this->admin)
            ->get('/expenses?period=custom&start_date=2026-11-01&end_date=2026-11-13&status=pending');
        $pendingOnly->assertStatus(200);
        $pendingOnly->assertSee('Facture Internet', false);
        $pendingOnly->assertDontSee('Loyer Bureau', false);
    }
}

