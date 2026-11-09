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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // e.g. ARR-2026-0001
            $table->string('supplier_name');
            $table->string('invoice_number')->nullable();
            $table->string('tracking_number')->nullable();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->date('received_date');
            $table->enum('status', ['received', 'in_transit', 'draft'])->default('received');
            
            // Financial costs of the shipment
            $table->decimal('shipping_cost', 12, 2)->default(0); // Fret / Transport
            $table->decimal('customs_cost', 12, 2)->default(0);  // Douane
            $table->decimal('other_fees', 12, 2)->default(0);    // Autres frais annexes
            $table->decimal('total_items_cost', 12, 2)->default(0); // Total net d'achat marchandises
            $table->decimal('total_landed_cost', 12, 2)->default(0); // Total coût rendu magasin
            
            $table->boolean('stock_applied')->default(false);
            $table->boolean('update_catalog_costs')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['received_date', 'status']);
        });

        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedBigInteger('variant_id')->nullable();
            
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0); // Coût d'achat unitaire fournisseur pour cet arrivage
            $table->decimal('allocated_fees_per_unit', 12, 2)->default(0); // Quote-part frais de transport/douane
            $table->decimal('landed_unit_cost', 12, 2)->default(0); // Coût unitaire de revient rendu
            $table->decimal('subtotal_cost', 12, 2)->default(0); // quantity * landed_unit_cost
            $table->decimal('selling_price_at_arrival', 12, 2)->nullable();
            $table->decimal('margin_percentage', 6, 2)->nullable();
            $table->timestamps();
            
            $table->index(['shipment_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
    }
};
