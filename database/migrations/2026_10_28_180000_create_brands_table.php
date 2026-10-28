<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create brands table
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                $table->string('website')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_featured')->default(false);
                $table->timestamps();

                $table->index('slug');
                $table->index('status');
                $table->index('sort_order');
            });
        }

        // 2. Add brand_id to products table
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('category_id')->constrained('brands')->nullOnDelete();
            });
        }

        // 3. Seed initial known brands and auto-detect brand for existing products
        $initialBrands = [
            'Sony', 'Canon', 'Nikon', 'Fujifilm', 'Panasonic', 'Leica', 'Sigma', 'Tamron',
            'DJI', 'GoPro', 'Insta360', 'Godox', 'Profoto', 'Nanlite', 'Aputure',
            'Neewer', 'K&F Concept', 'SmallRig', 'Tilta', 'Falcam', 'Ulanzi', 'Manfrotto',
            'Rode', 'Boya', 'Hollyland', 'Saramonic', 'Sennheiser', 'Zhiyun', 'Moza', 'Feiyutech',
            'Sandisk', 'Lexar', 'FeelWorld', 'Benro', 'Sirui', 'Peak Design', 'Lowepro',
            'Olympus', 'Hasselblad', 'Pentax', 'Samyang', 'Tokina', 'Gitzo', 'PGYTECH', 'Hoya',
            'Ambitful', 'NiceFoto', 'Apple', 'Cynova'
        ];

        $now = now();
        $brandIdMap = [];
        foreach ($initialBrands as $index => $brandName) {
            $slug = Str::slug($brandName);
            $existing = DB::table('brands')->where('slug', $slug)->first();
            if ($existing) {
                $brandIdMap[$brandName] = $existing->id;
            } else {
                $id = DB::table('brands')->insertGetId([
                    'name' => $brandName,
                    'slug' => $slug,
                    'status' => 'active',
                    'sort_order' => $index,
                    'is_featured' => in_array($brandName, ['Sony', 'Canon', 'DJI', 'Godox', 'Rode', 'SmallRig', 'Insta360', 'Sigma', 'Tamron']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $brandIdMap[$brandName] = $id;
            }
        }

        // Auto-assign products by detecting brand name in product title
        $products = DB::table('products')->whereNull('brand_id')->select(['id', 'name'])->get();
        foreach ($products as $prod) {
            foreach ($brandIdMap as $bName => $bId) {
                $searchPattern = $bName === 'Rode' ? '(?:Rode|RØDE)' : preg_quote($bName, '/');
                if (preg_match('/(?:\b|^)' . $searchPattern . '(?:\b|$)/iu', $prod->name)) {
                    DB::table('products')->where('id', $prod->id)->update(['brand_id' => $bId]);
                    break;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['brand_id']);
                $table->dropColumn('brand_id');
            });
        }

        Schema::dropIfExists('brands');
    }
};
