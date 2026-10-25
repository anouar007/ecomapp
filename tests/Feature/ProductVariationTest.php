<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\InventoryMovement;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PHPUnit\Framework\Attributes\Depends;

class ProductVariationTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('currency_symbol', 'DH', 'string', 'general');
        Setting::set('currency_code', 'MAD', 'string', 'general');

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $permissions = [
            'manage_products',
            'view_products',
            'manage_inventory',
            'view_inventory',
            'manage_orders',
            'view_orders',
        ];

        foreach ($permissions as $permName) {
            $perm = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $role->givePermissionTo($perm);
        }

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_test_variation@example.com'],
            ['name' => 'Admin Test Variation', 'password' => bcrypt('password')]
        );
        $this->admin->assignRole($role);
    }

    public function test_product_creation_with_variations_and_stock_sync(): Product
    {
        $uniqueSku = 'SDCARD-' . uniqid();
        $response = $this->actingAs($this->admin)->post(route('products.store'), [
            'name' => 'Carte SD Haute Vitesse',
            'sku' => $uniqueSku,
            'price' => 500.00,
            'cost_price' => 150.00,
            'stock' => 0, // Should be auto-calculated from variants
            'min_stock' => 5,
            'status' => 'active',
            'description' => 'Carte mémoire haute performance pour caméras et drones.',
            'has_variants' => '1',
            'variants' => [
                [
                    'name' => '512GB',
                    'sku' => $uniqueSku . '-512',
                    'price' => 1000.00,
                    'cost_price' => 300.00,
                    'stock' => 10,
                    'min_stock' => 2,
                    'is_active' => '1',
                ],
                [
                    'name' => '256GB',
                    'sku' => $uniqueSku . '-256',
                    'price' => 500.00,
                    'cost_price' => 150.00,
                    'stock' => 20,
                    'min_stock' => 3,
                    'is_active' => '1',
                ],
                [
                    'name' => '128GB',
                    'sku' => $uniqueSku . '-128',
                    'price' => 200.00,
                    'cost_price' => 60.00,
                    'stock' => 5,
                    'min_stock' => 2,
                    'is_active' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('sku', $uniqueSku)->with('variants')->first();
        $this->assertNotNull($product);
        $this->assertCount(3, $product->variants);

        // Sum of stocks: 10 + 20 + 5 = 35
        $this->assertEquals(35, $product->stock);

        $v512 = $product->variants->firstWhere('name', '512GB');
        $this->assertNotNull($v512);
        $this->assertEquals(1000.00, (float)$v512->price);
        $this->assertEquals(10, $v512->stock);

        return $product;
    }

    #[Depends('test_product_creation_with_variations_and_stock_sync')]
    public function test_product_update_modifies_variants_and_syncs_stock(Product $product): Product
    {
        $v512 = $product->variants->firstWhere('name', '512GB');
        $v256 = $product->variants->firstWhere('name', '256GB');
        $v128 = $product->variants->firstWhere('name', '128GB');

        // We update 512GB stock from 10 to 15, keep 256GB at 20, and remove 128GB (omitting it from payload)
        $response = $this->actingAs($this->admin)->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => 500.00,
            'cost_price' => 150.00,
            'stock' => 0,
            'min_stock' => 5,
            'status' => 'active',
            'has_variants' => '1',
            'variants' => [
                [
                    'id' => $v512->id,
                    'name' => '512GB Pro',
                    'sku' => $v512->sku,
                    'price' => 1100.00,
                    'cost_price' => 320.00,
                    'stock' => 15,
                    'min_stock' => 2,
                    'is_active' => '1',
                ],
                [
                    'id' => $v256->id,
                    'name' => '256GB Pro',
                    'sku' => $v256->sku,
                    'price' => 550.00,
                    'cost_price' => 160.00,
                    'stock' => 20,
                    'min_stock' => 3,
                    'is_active' => '1',
                ],
            ],
        ]);

        $response->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertCount(2, $product->variants);
        // Total stock is now 15 + 20 = 35
        $this->assertEquals(35, $product->stock);

        $this->assertNull(ProductVariant::find($v128->id));

        $updated512 = ProductVariant::find($v512->id);
        $this->assertEquals('512GB Pro', $updated512->name);
        $this->assertEquals(15, $updated512->stock);
        $this->assertEquals(1100.00, (float)$updated512->price);

        return $product;
    }

    #[Depends('test_product_update_modifies_variants_and_syncs_stock')]
    public function test_inventory_adjustment_for_individual_variant(Product $product): Product
    {
        $v512 = $product->variants->firstWhere('name', '512GB Pro');
        $this->assertNotNull($v512);

        // Adjust +5 units on 512GB Pro variant
        $response = $this->actingAs($this->admin)->postJson(route('inventory.process-adjustment', $product), [
            'product_id' => $product->id,
            'variant_id' => $v512->id,
            'adjustment_type' => 'in',
            'quantity' => 5,
            'reason' => 'Nouvel arrivage stock 512GB',
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $v512->refresh();
        $product->refresh();

        // 15 + 5 = 20
        $this->assertEquals(20, $v512->stock);
        // Product total stock: 20 (512GB) + 20 (256GB) = 40
        $this->assertEquals(40, $product->stock);

        // Verify inventory movement record
        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('variant_id', $v512->id)
            ->latest()
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(5, $movement->quantity);
        $this->assertEquals(15, $movement->stock_before);
        $this->assertEquals(20, $movement->stock_after);

        return $product;
    }

    #[Depends('test_inventory_adjustment_for_individual_variant')]
    public function test_shop_show_renders_variations(Product $product): void
    {
        $response = $this->get(route('shop.show', $product->slug ?: $product->id));
        $response->assertStatus(200);

        // Should render the variant buttons
        $response->assertSee('512GB Pro');
        $response->assertSee('256GB Pro');
        $response->assertSee('data-variant-id', false);
    }

    #[Depends('test_inventory_adjustment_for_individual_variant')]
    public function test_cart_supports_variant_item_and_composite_key(Product $product): ProductVariant
    {
        $v512 = $product->variants->firstWhere('name', '512GB Pro');

        $response = $this->postJson(route('cart.add', $product->id), [
            'product_id' => $product->id,
            'variant_id' => $v512->id,
            'quantity' => 2,
        ]);

        $response->assertJson([
            'success' => true,
        ]);

        $cart = session('cart');
        $cartKey = "{$product->id}_{$v512->id}";

        $this->assertIsArray($cart);
        $this->assertArrayHasKey($cartKey, $cart);
        $this->assertEquals(2, $cart[$cartKey]['quantity']);
        $this->assertEquals($v512->id, $cart[$cartKey]['variant_id']);
        $this->assertEquals('512GB Pro', $cart[$cartKey]['variant_name']);
        $this->assertEquals((float)$v512->price, (float)$cart[$cartKey]['price']);

        return $v512;
    }

    #[Depends('test_cart_supports_variant_item_and_composite_key')]
    public function test_checkout_decrements_variant_stock_and_creates_order_item(ProductVariant $v512): void
    {
        $product = $v512->product;
        $initialVariantStock = $v512->stock;
        $initialProductStock = $product->stock;

        $cartKey = "{$product->id}_{$v512->id}";
        $cart = [
            $cartKey => [
                'id' => $product->id,
                'variant_id' => $v512->id,
                'variant_name' => $v512->name,
                'name' => $product->name,
                'quantity' => 2,
                'price' => (float)$v512->price,
            ]
        ];

        $response = $this->withSession(['cart' => $cart])->post(route('checkout.store'), [
            'customer_name' => 'Amine El Alami',
            'customer_email' => 'amine@example.com',
            'customer_phone' => '0600000000',
            'shipping_address' => 'Boulevard d Anfa',
            'shipping_city' => 'Casablanca',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect();

        $order = Order::latest()->first();
        $this->assertNotNull($order);

        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        $this->assertNotNull($orderItem);
        $this->assertEquals($v512->id, $orderItem->variant_id);
        $this->assertEquals('512GB Pro', $orderItem->variant_name);
        $this->assertEquals(2, $orderItem->quantity);

        $v512->refresh();
        $product->refresh();

        // Stock must have decremented by 2
        $this->assertEquals($initialVariantStock - 2, $v512->stock);
        $this->assertEquals($initialProductStock - 2, $product->stock);
    }
}
