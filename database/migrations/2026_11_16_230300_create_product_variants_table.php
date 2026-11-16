<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('size', 60);
                $table->decimal('price', 10, 2);
                $table->boolean('is_default')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
            return;
        }

        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'product_id')) {
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('product_variants', 'size')) {
                $table->string('size', 60)->default('')->after('product_id');
            }
            if (!Schema::hasColumn('product_variants', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('size');
            }
            if (!Schema::hasColumn('product_variants', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('price');
            }
            if (!Schema::hasColumn('product_variants', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_default');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
