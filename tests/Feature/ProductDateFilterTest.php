<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductDateFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']);

        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole('Admin');
        $admin->givePermissionTo('manage_products');

        return $admin;
    }

    protected function createProductWithDates(string $name, string $createdAt, string $updatedAt, int $sortOrder = 0): Product
    {
        $product = Product::factory()->create([
            'name' => $name,
            'status' => 'active',
            'sort_order' => $sortOrder,
        ]);

        $product->timestamps = false;
        $product->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ])->saveQuietly();

        return $product->fresh();
    }

    public function test_products_table_filters_and_sorts_by_created_and_updated_dates(): void
    {
        $admin = $this->makeAdmin();

        $oldCreatedNewUpdated = $this->createProductWithDates(
            'DateFilterOldCreatedNewUpdated ' . uniqid(),
            '2012-03-05 10:00:00',
            '2013-06-25 14:30:00',
            5
        );
        $midCreatedMidUpdated = $this->createProductWithDates(
            'DateFilterMidCreatedMidUpdated ' . uniqid(),
            '2012-03-15 12:00:00',
            '2013-06-15 09:00:00',
            0
        );
        $newCreatedOldUpdated = $this->createProductWithDates(
            'DateFilterNewCreatedOldUpdated ' . uniqid(),
            '2012-03-25 16:00:00',
            '2013-06-05 08:00:00',
            0
        );

        // 1. Page renders filter inputs and date column
        $this->actingAs($admin)
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee('name="created_from"', false)
            ->assertSee('name="created_to"', false)
            ->assertSee('name="updated_from"', false)
            ->assertSee('name="updated_to"', false)
            ->assertSee('name="sort_by"', false)
            ->assertSee('<th class="col-dates">Dates</th>', false);

        // 2. Filter by created date range (2012-03-10 to 2012-03-26)
        $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => '2012-03-10',
                'created_to' => '2012-03-26',
            ]))
            ->assertOk()
            ->assertSee($midCreatedMidUpdated->name)
            ->assertSee($newCreatedOldUpdated->name)
            ->assertDontSee($oldCreatedNewUpdated->name)
            ->assertSee('Création : 10/03/2012 → 26/03/2012')
            ->assertSee('15/03/2012');

        // 3. Filter by single created day (reversed or single bound)
        $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => '2012-03-25',
                'created_to' => '2012-03-25',
            ]))
            ->assertOk()
            ->assertSee($newCreatedOldUpdated->name)
            ->assertDontSee($oldCreatedNewUpdated->name)
            ->assertDontSee($midCreatedMidUpdated->name);

        // 4. Filter by updated date range regardless of created_at
        $this->actingAs($admin)
            ->get(route('products.index', [
                'updated_from' => '2013-06-20',
                'updated_to' => '2013-06-30',
            ]))
            ->assertOk()
            ->assertSee($oldCreatedNewUpdated->name)
            ->assertDontSee($midCreatedMidUpdated->name)
            ->assertDontSee($newCreatedOldUpdated->name)
            ->assertSee('Modification : 20/06/2013 → 30/06/2013')
            ->assertSee('25/06/2013');

        // 5. Combine created & updated date filters
        $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => '2012-03-01',
                'created_to' => '2012-03-20',
                'updated_from' => '2013-06-10',
                'updated_to' => '2013-06-20',
            ]))
            ->assertOk()
            ->assertSee($midCreatedMidUpdated->name)
            ->assertDontSee($oldCreatedNewUpdated->name)
            ->assertDontSee($newCreatedOldUpdated->name);

        // 6. Sorting by created_desc and updated_desc overrides sort_order when requested
        $createdDescHtml = $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => '2012-03-01',
                'created_to' => '2012-03-31',
                'sort_by' => 'created_desc',
            ]))
            ->assertOk()
            ->assertSee('Tri : Création (récent → ancien)')
            ->getContent();

        $posNew = strpos($createdDescHtml, $newCreatedOldUpdated->name);
        $posMid = strpos($createdDescHtml, $midCreatedMidUpdated->name);
        $posOld = strpos($createdDescHtml, $oldCreatedNewUpdated->name);
        $this->assertNotFalse($posNew);
        $this->assertNotFalse($posMid);
        $this->assertNotFalse($posOld);
        $this->assertTrue($posNew < $posMid && $posMid < $posOld);

        $updatedDescHtml = $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => '2012-03-01',
                'created_to' => '2012-03-31',
                'sort_by' => 'updated_desc',
            ]))
            ->assertOk()
            ->assertSee('Tri : Modification (récent → ancien)')
            ->getContent();

        $posUpOldCreated = strpos($updatedDescHtml, $oldCreatedNewUpdated->name);
        $posUpMid = strpos($updatedDescHtml, $midCreatedMidUpdated->name);
        $posUpNewCreated = strpos($updatedDescHtml, $newCreatedOldUpdated->name);
        $this->assertTrue($posUpOldCreated < $posUpMid && $posUpMid < $posUpNewCreated);

        // 7. Malformed date parameters do not error out
        $this->actingAs($admin)
            ->get(route('products.index', [
                'created_from' => 'not-a-date',
                'updated_to' => '99/99/9999',
            ]))
            ->assertOk();
    }
}
