<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['product_id', 'category_id']);
        });

        if (Schema::hasColumn('products', 'category_id')) {
            $existing = \Illuminate\Support\Facades\DB::table('products')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.id as product_id', 'products.category_id as category_id')
                ->whereNotNull('products.category_id')
                ->get();

            $now = now();
            $records = [];
            foreach ($existing as $row) {
                $records[] = [
                    'product_id' => $row->product_id,
                    'category_id' => $row->category_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (count($records) >= 500) {
                    \Illuminate\Support\Facades\DB::table('category_product')->insertOrIgnore($records);
                    $records = [];
                }
            }
            if (!empty($records)) {
                \Illuminate\Support\Facades\DB::table('category_product')->insertOrIgnore($records);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
