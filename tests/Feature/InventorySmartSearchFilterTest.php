<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductVariant;
use App\Models\StockAlert;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InventorySmartSearchFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected function makeAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        foreach ([
            'view_inventory', 'manage_inventory', 'manage_stores', 'view_profitability',
        ] as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            if (!$role->hasPermissionTo($perm)) {
                $role->givePermissionTo($perm);
            }
        }

        $admin = User::factory()->create(['status' => 'active']);
        $admin->assignRole($role);

        return $admin;
    }

    public function test_inventory_smart_search_matches_product_page_aliases_typos_variant_sku_brand_id_and_relevance(): void
    {
        $admin = $this->makeAdmin();
        $uid = strtoupper(substr(uniqid(), -6));

        $category = Category::create([
            'name' => 'Caméras Hybrides ' . $uid,
            'slug' => 'cameras-hybrides-' . strtolower($uid),
            'status' => 'active',
        ]);

        $brand = Brand::create([
            'name' => 'ZetaOptix' . $uid,
            'status' => 'active',
        ]);

        // Primary flagship camera matching Sony A7 IV
        $camera = Product::create([
            'name' => "Sony Alpha A7 IV Boîtier {$uid}",
            'sku' => "CAM-SONY-A7IV-{$uid}",
            'price' => 24000,
            'stock' => 6,
            'stock_quantity' => 6,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
            'sort_order' => 1,
        ]);

        // Accessory with "pour Sony A7 IV" (must rank below primary body under default relevance,
        // yet rank BEFORE Sony alphabetically when sort_by=name&sort_dir=asc is requested)
        $cage = Product::create([
            'name' => "Cage SmallRig pour Sony A7 IV {$uid}",
            'sku' => "ACC-CAGE-A7IV-{$uid}",
            'price' => 950,
            'stock' => 12,
            'stock_quantity' => 12,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
            'sort_order' => 0,
        ]);

        // Product with variant SKU + unique brand
        $brandProduct = Product::create([
            'name' => "Émetteur Micro Pro {$uid}",
            'sku' => "MIC-MAIN-{$uid}",
            'price' => 1800,
            'stock' => 4,
            'stock_quantity' => 4,
            'min_stock' => 1,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);
        ProductVariant::create([
            'product_id' => $brandProduct->id,
            'name' => 'Pack Pro',
            'sku' => "VAR-GODOX-V1-PRO-{$uid}",
            'price' => 1800,
            'stock' => 4,
            'min_stock' => 1,
            'status' => 'active',
        ]);

        // 1. Canonical alias "a7m4" -> finds Sony Alpha A7 IV
        $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => 'a7m4', 'per_page' => 100]))
            ->assertOk()
            ->assertSee($camera->sku)
            ->assertSee('Filtres appliqués');

        // 2. Typo + multi-token ("snoy a7") -> finds Sony Alpha A7 IV
        $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => 'snoy a7', 'per_page' => 100]))
            ->assertOk()
            ->assertSee($camera->sku);

        // 3. Variant SKU -> finds parent product
        $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => "VAR-GODOX-V1-PRO-{$uid}"]))
            ->assertOk()
            ->assertSee($brandProduct->name)
            ->assertSee($brandProduct->sku);

        // 4. Brand name -> finds brand product
        $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => "ZetaOptix{$uid}"]))
            ->assertOk()
            ->assertSee($brandProduct->name);

        // 5. Numeric ID (#...) -> finds exact product
        $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => '#' . $camera->id]))
            ->assertOk()
            ->assertSee($camera->sku);

        // 6. Relevance ordering vs explicit alphabetical sorting
        $relResp = $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => 'Sony A7 IV', 'per_page' => 100]));
        $relResp->assertOk();
        $relIds = $relResp->viewData('products')->pluck('id')->all();
        $camPos = array_search($camera->id, $relIds, true);
        $cagePos = array_search($cage->id, $relIds, true);
        $this->assertNotFalse($camPos);
        $this->assertNotFalse($cagePos);
        $this->assertLessThan($cagePos, $camPos, 'Primary body must rank ahead of compatible accessory in relevance mode.');

        $alphaResp = $this->actingAs($admin)
            ->get(route('inventory.index', ['search' => 'Sony A7 IV', 'sort_by' => 'name', 'sort_dir' => 'asc', 'per_page' => 100]));
        $alphaResp->assertOk();
        $alphaIds = $alphaResp->viewData('products')->pluck('id')->all();
        $camAlphaPos = array_search($camera->id, $alphaIds, true);
        $cageAlphaPos = array_search($cage->id, $alphaIds, true);
        $this->assertNotFalse($camAlphaPos);
        $this->assertNotFalse($cageAlphaPos);
        $this->assertLessThan($camAlphaPos, $cageAlphaPos, 'Alphabetical sort_by=name asc must place Cage before Sony.');
    }

    public function test_inventory_category_filter_includes_subcategories_pivot_and_export_uses_smart_search(): void
    {
        $admin = $this->makeAdmin();
        $uid = strtoupper(substr(uniqid(), -6));

        $parentCat = Category::create([
            'name' => 'Cat Parent Inv ' . $uid,
            'slug' => 'cat-parent-inv-' . strtolower($uid),
            'status' => 'active',
        ]);
        $childCat = Category::create([
            'name' => 'Cat Child Inv ' . $uid,
            'slug' => 'cat-child-inv-' . strtolower($uid),
            'parent_id' => $parentCat->id,
            'status' => 'active',
        ]);
        $otherCat = Category::create([
            'name' => 'Cat Other Inv ' . $uid,
            'slug' => 'cat-other-inv-' . strtolower($uid),
            'status' => 'active',
        ]);

        // Product 1 lives in child category via primary category_id
        $childProduct = Product::create([
            'name' => "Sony Alpha A7 IV Child {$uid}",
            'sku' => "INV-CHILD-{$uid}",
            'price' => 19000,
            'stock' => 5,
            'stock_quantity' => 5,
            'min_stock' => 1,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $childCat->id,
        ]);

        // Product 2 has different primary category_id, but is attached to parentCat via categories() pivot
        $pivotProduct = Product::create([
            'name' => "Pivot Item {$uid}",
            'sku' => "INV-PIVOT-{$uid}",
            'price' => 450,
            'stock' => 8,
            'stock_quantity' => 8,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $otherCat->id,
        ]);
        $pivotProduct->categories()->attach($parentCat->id);

        $unrelatedProduct = Product::create([
            'name' => "Unrelated Item {$uid}",
            'sku' => "INV-OTHER-{$uid}",
            'price' => 300,
            'stock' => 3,
            'stock_quantity' => 3,
            'min_stock' => 1,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $otherCat->id,
        ]);

        $catResp = $this->actingAs($admin)->get(route('inventory.index', ['category_id' => $parentCat->id]));
        $catResp->assertOk()
            ->assertSee($childProduct->sku)
            ->assertSee($pivotProduct->sku)
            ->assertDontSee("inv-row-{$unrelatedProduct->id}", false);

        $filteredIds = $catResp->viewData('products')->pluck('id')->all();
        $this->assertContains($childProduct->id, $filteredIds);
        $this->assertContains($pivotProduct->id, $filteredIds);
        $this->assertNotContains($unrelatedProduct->id, $filteredIds);

        // CSV Export respects smart alias search ("a7m4")
        $exportResp = $this->actingAs($admin)->get(route('inventory.export', ['search' => 'a7m4']));
        $exportResp->assertOk();
        $csv = $exportResp->streamedContent();
        $this->assertStringContainsString($childProduct->sku, $csv);
    }

    public function test_inventory_subpages_support_smart_search(): void
    {
        $admin = $this->makeAdmin();
        $uid = strtoupper(substr(uniqid(), -6));

        $store = Store::where('is_active', true)->first() ?? Store::create([
            'name' => 'Magasin Search Test ' . $uid,
            'code' => 'SRCH-' . $uid,
            'type' => 'store',
            'is_active' => true,
            'is_default' => true,
        ]);

        $category = Category::create([
            'name' => 'Subpage Search Cat ' . $uid,
            'slug' => 'subpage-search-cat-' . strtolower($uid),
            'status' => 'active',
        ]);

        $product = Product::create([
            'name' => "Sony Alpha A7 IV Subpage {$uid}",
            'sku' => "SUB-A7IV-{$uid}",
            'price' => 22000,
            'cost_price' => 18000,
            'stock' => 0,
            'stock_quantity' => 0,
            'min_stock' => 2,
            'track_inventory' => true,
            'status' => 'active',
            'category_id' => $category->id,
        ]);

        // Entry with FIFO batch + movement + store stock + alert
        $product->adjustStock(3, 'in', $admin->id, [
            'store_id' => $store->id,
            'unit_cost' => 18000,
            'total_cost' => 54000,
            'reference_id' => "ARR-SRCH-{$uid}",
            'reason' => "Réception lot {$uid}",
        ]);

        StockAlert::create([
            'product_id' => $product->id,
            'alert_type' => 'low_stock',
            'threshold_value' => 5,
            'current_stock' => 3,
            'triggered_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('inventory.movements', ['search' => 'a7m4']))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('inventory.alerts', ['search' => 'a7m4']))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('stores.stock', ['search' => 'a7m4']))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($admin)
            ->get(route('inventory.arrivals', ['search' => 'a7m4']))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee("ARR-SRCH-{$uid}");
    }
}
