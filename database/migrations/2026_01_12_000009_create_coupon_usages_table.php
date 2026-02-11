<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_email')->nullable();
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('order_total', 10, 2);
            $table->timestamps();

            $table->index('coupon_id');
            $table->index('order_id');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
