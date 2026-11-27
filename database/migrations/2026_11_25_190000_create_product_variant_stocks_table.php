<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_variant_stocks')) {
            Schema::create('product_variant_stocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
                $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
                $table->integer('quantity')->default(0);
                $table->timestamps();
                $table->unique(['product_variant_id', 'store_id'], 'pvs_variant_store_unique');
            });
        }

        // Backfill: distribute each variant's current stock into the stores that already hold the
        // product's stock (largest first); any remainder goes to the default / first active store.
        $fallbackStoreId = DB::table('stores')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        $variantsByProduct = DB::table('product_variants')
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->get(['id', 'product_id', 'stock'])
            ->groupBy('product_id');

        $now = now();
        foreach ($variantsByProduct as $productId => $variants) {
            $capacity = DB::table('product_stocks')
                ->where('product_id', $productId)
                ->where('quantity', '>', 0)
                ->orderByDesc('quantity')
                ->pluck('quantity', 'store_id')
                ->map(fn ($q) => (int) $q)
                ->all();

            $rows = [];
            foreach ($variants as $variant) {
                $remaining = (int) $variant->stock;
                foreach ($capacity as $storeId => $cap) {
                    if ($remaining <= 0) {
                        break;
                    }
                    if ($cap <= 0) {
                        continue;
                    }
                    $take = min($cap, $remaining);
                    $rows[$variant->id][$storeId] = ($rows[$variant->id][$storeId] ?? 0) + $take;
                    $capacity[$storeId] = $cap - $take;
                    $remaining -= $take;
                }
                if ($remaining > 0) {
                    $target = $fallbackStoreId ?? array_key_first($capacity);
                    if ($target) {
                        $rows[$variant->id][$target] = ($rows[$variant->id][$target] ?? 0) + $remaining;
                    }
                }
            }

            foreach ($rows as $variantId => $stores) {
                foreach ($stores as $storeId => $qty) {
                    $exists = DB::table('product_variant_stocks')
                        ->where('product_variant_id', $variantId)
                        ->where('store_id', $storeId)
                        ->exists();
                    if (!$exists) {
                        DB::table('product_variant_stocks')->insert([
                            'product_variant_id' => $variantId,
                            'store_id' => $storeId,
                            'quantity' => $qty,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_stocks');
    }
};
