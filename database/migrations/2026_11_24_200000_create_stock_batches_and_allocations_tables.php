<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('stock_batches')) {
            Schema::create('stock_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
                $table->foreignId('shipment_id')->nullable()->constrained('shipments')->nullOnDelete();
                $table->foreignId('shipment_item_id')->nullable()->constrained('shipment_items')->nullOnDelete();
                $table->foreignId('inventory_movement_id')->nullable()->constrained('inventory_movements')->nullOnDelete();

                $table->string('source_type', 32)->default('inventory_in'); // shipment | inventory_in | opening_stock
                $table->string('reference', 100)->nullable();
                $table->string('supplier_name')->nullable();
                $table->text('notes')->nullable();

                $table->timestamp('arrived_at')->useCurrent()->index();
                $table->unsignedInteger('initial_quantity');
                $table->unsignedInteger('remaining_quantity');
                $table->unsignedInteger('sold_quantity')->default(0);
                $table->unsignedInteger('manual_out_quantity')->default(0);

                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->decimal('total_cost', 14, 2)->default(0);
                $table->decimal('realized_revenue', 14, 2)->default(0);
                $table->decimal('realized_cost', 14, 2)->default(0);
                $table->decimal('realized_profit', 14, 2)->default(0);

                $table->timestamp('first_sold_at')->nullable();
                $table->timestamp('sold_out_at')->nullable();
                $table->string('status', 20)->default('active'); // active | selling | waiting | sold_out

                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['product_id', 'remaining_quantity', 'arrived_at', 'id'], 'stock_batches_fifo_idx');
                $table->index(['product_id', 'variant_id', 'remaining_quantity', 'arrived_at'], 'stock_batches_variant_fifo_idx');
                $table->index(['status', 'arrived_at'], 'stock_batches_status_arrived_idx');
            });
        }

        if (!Schema::hasTable('stock_batch_allocations')) {
            Schema::create('stock_batch_allocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_batch_id')->constrained('stock_batches')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->foreignId('order_item_id')->nullable()->constrained('order_items')->nullOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
                $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();

                $table->unsignedInteger('quantity');
                $table->decimal('unit_cost', 12, 2)->default(0);
                $table->decimal('unit_sell_price', 12, 2)->default(0);
                $table->decimal('total_cost', 14, 2)->default(0);
                $table->decimal('total_revenue', 14, 2)->default(0);
                $table->decimal('profit', 14, 2)->default(0);
                $table->timestamp('allocated_at')->useCurrent()->index();
                $table->timestamps();

                $table->index(['stock_batch_id', 'allocated_at'], 'stock_batch_alloc_batch_time_idx');
                $table->index('order_id');
                $table->index('order_item_id');
                $table->index('invoice_id');
            });
        }

        // Idempotent backfill for any existing received + stock-applied shipment items
        if (Schema::hasTable('shipments') && Schema::hasTable('shipment_items')) {
            $existingRows = DB::table('shipment_items')
                ->join('shipments', 'shipment_items.shipment_id', '=', 'shipments.id')
                ->where('shipments.status', 'received')
                ->where('shipments.stock_applied', true)
                ->select(
                    'shipment_items.*',
                    'shipments.reference as shipment_reference',
                    'shipments.supplier_name',
                    'shipments.store_id',
                    'shipments.received_date',
                    'shipments.notes as shipment_notes',
                    'shipments.created_by',
                    'shipments.created_at as shipment_created_at'
                )
                ->orderBy('shipments.received_date', 'asc')
                ->orderBy('shipment_items.id', 'asc')
                ->get();

            $firstByProduct = [];
            foreach ($existingRows as $row) {
                $already = DB::table('stock_batches')
                    ->where('shipment_item_id', $row->id)
                    ->exists();
                if ($already) {
                    continue;
                }

                $qty = max(1, (int) $row->quantity);
                $key = $row->product_id . ':' . ($row->variant_id ?? '0');
                $isFirst = !isset($firstByProduct[$key]);
                $firstByProduct[$key] = true;

                $arrivedAt = $row->received_date
                    ? Carbon::parse($row->received_date)->startOfDay()
                    : ($row->shipment_created_at ? Carbon::parse($row->shipment_created_at) : now());

                $unitCost = round((float) $row->landed_unit_cost, 2);
                $totalCost = round((float) $row->subtotal_cost, 2);

                DB::table('stock_batches')->insert([
                    'product_id' => $row->product_id,
                    'variant_id' => $row->variant_id,
                    'store_id' => $row->store_id,
                    'shipment_id' => $row->shipment_id,
                    'shipment_item_id' => $row->id,
                    'inventory_movement_id' => null,
                    'source_type' => 'shipment',
                    'reference' => $row->shipment_reference,
                    'supplier_name' => $row->supplier_name,
                    'notes' => $row->shipment_notes,
                    'arrived_at' => $arrivedAt->toDateTimeString(),
                    'initial_quantity' => $qty,
                    'remaining_quantity' => $qty,
                    'sold_quantity' => 0,
                    'manual_out_quantity' => 0,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'realized_revenue' => 0,
                    'realized_cost' => 0,
                    'realized_profit' => 0,
                    'first_sold_at' => null,
                    'sold_out_at' => null,
                    'status' => $isFirst ? 'active' : 'waiting',
                    'created_by' => $row->created_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_batch_allocations');
        Schema::dropIfExists('stock_batches');
    }
};
