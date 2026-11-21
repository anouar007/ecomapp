<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'delivery_type')) {
            return;
        }

        DB::table('orders')
            ->whereNull('delivery_type')
            ->where(function ($q) {
                $q->where('shipping_address', 'like', 'Vente en boutique (POS)%')
                    ->orWhere('shipping_address', 'like', 'Retrait au magasin%')
                    ->orWhere('notes', 'like', 'Vente POS%');
            })
            ->update(['delivery_type' => 'store']);

        DB::table('orders')
            ->whereNull('delivery_type')
            ->update(['delivery_type' => 'ozon']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Backfill only; no destructive rollback needed.
    }
};
