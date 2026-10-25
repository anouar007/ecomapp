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
        // 1. Create product_variants table
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('name'); // e.g. "512GB", "256GB", "Noir", etc.
                $table->string('sku')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('cost_price', 10, 2)->nullable();
                $table->integer('stock')->default(0);
                $table->integer('min_stock')->default(5);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('image')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index('product_id');
                $table->index('sku');
                $table->index('status');
            });
        }

        // 2. Add variant fields to order_items
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'variant_id')) {
                    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                    $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
                }
                if (!Schema::hasColumn('order_items', 'variant_name')) {
                    $table->string('variant_name')->nullable()->after('product_name');
                }
            });
        }

        // 3. Add variant_id to inventory_movements
        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_movements', 'variant_id')) {
                    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                    $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
                    $table->index('variant_id');
                }
            });
        }

        // 4. Add variant_id to stock_alerts
        if (Schema::hasTable('stock_alerts')) {
            Schema::table('stock_alerts', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_alerts', 'variant_id')) {
                    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                    $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
                    $table->index('variant_id');
                }
            });
        }

        // 5. Add variant fields to invoice_items
        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_items', 'variant_id')) {
                    $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                    $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
                }
                if (!Schema::hasColumn('invoice_items', 'variant_name')) {
                    $table->string('variant_name')->nullable()->after('product_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (Schema::hasColumn('invoice_items', 'variant_id')) {
                    $table->dropForeign(['variant_id']);
                    $table->dropColumn('variant_id');
                }
                if (Schema::hasColumn('invoice_items', 'variant_name')) {
                    $table->dropColumn('variant_name');
                }
            });
        }

        if (Schema::hasTable('stock_alerts')) {
            Schema::table('stock_alerts', function (Blueprint $table) {
                if (Schema::hasColumn('stock_alerts', 'variant_id')) {
                    $table->dropForeign(['variant_id']);
                    $table->dropColumn('variant_id');
                }
            });
        }

        if (Schema::hasTable('inventory_movements')) {
            Schema::table('inventory_movements', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_movements', 'variant_id')) {
                    $table->dropForeign(['variant_id']);
                    $table->dropColumn('variant_id');
                }
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'variant_id')) {
                    $table->dropForeign(['variant_id']);
                    $table->dropColumn('variant_id');
                }
                if (Schema::hasColumn('order_items', 'variant_name')) {
                    $table->dropColumn('variant_name');
                }
            });
        }

        Schema::dropIfExists('product_variants');
    }
};
