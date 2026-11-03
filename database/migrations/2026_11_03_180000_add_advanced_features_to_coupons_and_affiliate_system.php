<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('min_product_price', 10, 2)->nullable()->after('min_order_amount');
            $table->decimal('max_product_price', 10, 2)->nullable()->after('min_product_price');
            $table->boolean('is_affiliate')->default(false)->after('first_order_only');
            $table->foreignId('affiliate_user_id')->nullable()->after('is_affiliate')->constrained('users')->nullOnDelete();
            $table->foreignId('affiliate_customer_id')->nullable()->after('affiliate_user_id')->constrained('customers')->nullOnDelete();
            $table->string('affiliate_points_type')->default('fixed')->after('affiliate_customer_id'); // 'fixed' or 'percentage'
            $table->decimal('affiliate_points_value', 10, 2)->default(0)->after('affiliate_points_type');
            $table->integer('total_affiliate_points_awarded')->default(0)->after('affiliate_points_value');
        });

        // 2. Add affiliate_points to users table
        if (!Schema::hasColumn('users', 'affiliate_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('affiliate_points')->default(0)->after('avatar');
            });
        }

        // 3. Add affiliate_points_awarded to coupon_usages table
        if (!Schema::hasColumn('coupon_usages', 'affiliate_points_awarded')) {
            Schema::table('coupon_usages', function (Blueprint $table) {
                $table->integer('affiliate_points_awarded')->default(0)->after('order_total');
            });
        }

        // 4. Add coupon tracking to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('discount')->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code', 50)->nullable()->after('coupon_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_code']);
        });

        if (Schema::hasColumn('coupon_usages', 'affiliate_points_awarded')) {
            Schema::table('coupon_usages', function (Blueprint $table) {
                $table->dropColumn('affiliate_points_awarded');
            });
        }

        if (Schema::hasColumn('users', 'affiliate_points')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('affiliate_points');
            });
        }

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['affiliate_user_id']);
            $table->dropForeign(['affiliate_customer_id']);
            $table->dropColumn([
                'min_product_price',
                'max_product_price',
                'is_affiliate',
                'affiliate_user_id',
                'affiliate_customer_id',
                'affiliate_points_type',
                'affiliate_points_value',
                'total_affiliate_points_awarded',
            ]);
        });
    }
};

