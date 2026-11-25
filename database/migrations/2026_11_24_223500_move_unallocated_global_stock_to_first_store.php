<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasTable('stores') || !Schema::hasTable('product_stocks')) {
            return;
        }

        $actorId = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->value('id')
            ?? User::value('id');

        Product::allocateUnassignedGlobalStockToFirstStore($actorId);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentional no-op: store allocation reconciliation should not destroy store stock rows.
    }
};
