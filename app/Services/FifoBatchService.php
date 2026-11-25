<?php

namespace App\Services;

use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\StockBatch;
use App\Models\StockBatchAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FifoBatchService
{
    /**
     * Generate a unique sequential batch reference (e.g. LOT-20261124-0001).
     */
    public function generateReference(string $prefix = 'LOT'): string
    {
        $datePrefix = $prefix . '-' . now()->format('Ymd') . '-';

        $lastRef = StockBatch::where('reference', 'LIKE', $datePrefix . '%')
            ->orderByDesc('id')
            ->value('reference');

        $nextNum = 1;
        if ($lastRef) {
            $suffix = substr((string) $lastRef, strlen($datePrefix));
            if (is_numeric($suffix)) {
                $nextNum = ((int) $suffix) + 1;
            }
        }

        $candidate = $datePrefix . str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        while (StockBatch::where('reference', $candidate)->exists()) {
            $nextNum++;
            $candidate = $datePrefix . str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    /**
     * Preserve pre-existing stock as an opening FIFO batch before adding a newer arrival
     * so older units are always depleted first before touching newer stock.
     */
    public function ensureOpeningBatchIfNeeded(
        Product $product,
        int $stockBefore,
        ?float $costBefore,
        Carbon $newArrivedAt,
        ?int $variantId = null,
        ?int $storeId = null,
        ?int $userId = null
    ): ?StockBatch {
        if ($stockBefore <= 0) {
            return null;
        }

        $alreadyHasBatches = StockBatch::where('product_id', $product->id)
            ->when(
                $variantId !== null,
                fn ($q) => $q->where('variant_id', $variantId),
                fn ($q) => $q->whereNull('variant_id')
            )
            ->exists();

        if ($alreadyHasBatches) {
            return null;
        }

        $unitCost = round(max(0.0, (float) ($costBefore ?? $product->cost_price ?? 0)), 2);
        $totalCost = round($unitCost * $stockBefore, 2);

        $fallbackOlder = $newArrivedAt->copy()->subSecond();
        $productCreated = $product->created_at ? Carbon::parse($product->created_at) : $fallbackOlder;
        $openingArrivedAt = $productCreated->lt($fallbackOlder) ? $productCreated : $fallbackOlder;

        return StockBatch::create([
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'store_id' => $storeId,
            'source_type' => 'opening_stock',
            'reference' => 'STOCK-INITIAL',
            'supplier_name' => null,
            'notes' => 'Stock existant avant suivi FIFO par arrivage',
            'arrived_at' => $openingArrivedAt,
            'initial_quantity' => $stockBefore,
            'remaining_quantity' => $stockBefore,
            'sold_quantity' => 0,
            'manual_out_quantity' => 0,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'realized_revenue' => 0,
            'realized_cost' => 0,
            'realized_profit' => 0,
            'status' => 'active',
            'created_by' => $userId ?? Auth::id(),
        ]);
    }

    /**
     * Create a FIFO batch from a received supplier shipment line item.
     */
    public function createBatchFromShipmentItem(
        Shipment $shipment,
        ShipmentItem $item,
        ?InventoryMovement $movement = null,
        int $stockBefore = 0,
        ?float $costBefore = null
    ): StockBatch {
        $existing = StockBatch::where('shipment_item_id', $item->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($shipment, $item, $movement, $stockBefore, $costBefore) {
            $product = $item->relationLoaded('product') && $item->product
                ? $item->product
                : Product::findOrFail($item->product_id);

            $arrivedAt = now();
            if ($shipment->received_date) {
                $arrivedAt = $shipment->received_date->isToday()
                    ? now()
                    : $shipment->received_date->copy()->startOfDay();
            }

            $userId = $shipment->created_by ?? Auth::id();
            $variantId = $item->variant_id ? (int) $item->variant_id : null;
            $storeId = $shipment->store_id ? (int) $shipment->store_id : null;

            $this->ensureOpeningBatchIfNeeded(
                $product,
                $stockBefore,
                $costBefore,
                $arrivedAt,
                $variantId,
                null,
                $userId
            );

            $qty = max(1, (int) $item->quantity);
            $unitCost = round(max(0.0, (float) $item->landed_unit_cost), 2);
            $totalCost = round(max(0.0, (float) $item->subtotal_cost), 2);

            $batch = StockBatch::create([
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'store_id' => $storeId,
                'shipment_id' => $shipment->id,
                'shipment_item_id' => $item->id,
                'inventory_movement_id' => $movement?->id,
                'source_type' => 'shipment',
                'reference' => $shipment->reference,
                'supplier_name' => $shipment->supplier_name,
                'notes' => $shipment->notes,
                'arrived_at' => $arrivedAt,
                'initial_quantity' => $qty,
                'remaining_quantity' => $qty,
                'sold_quantity' => 0,
                'manual_out_quantity' => 0,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'realized_revenue' => 0,
                'realized_cost' => 0,
                'realized_profit' => 0,
                'status' => 'active',
                'created_by' => $userId,
            ]);

            $this->refreshQueueStatuses($product->id, $variantId);

            return $batch->fresh();
        });
    }

    /**
     * Create a FIFO batch when stock is added through an inventory entry movement.
     */
    public function createBatchFromInventoryIn(
        Product $product,
        InventoryMovement $movement,
        ?ProductVariant $variant = null,
        ?int $storeId = null,
        int $stockBefore = 0,
        ?float $costBefore = null,
        array $options = []
    ): StockBatch {
        $existing = StockBatch::where('inventory_movement_id', $movement->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($product, $movement, $variant, $storeId, $stockBefore, $costBefore, $options) {
            $arrivedAt = $movement->created_at ? Carbon::parse($movement->created_at) : now();
            if (!empty($options['arrived_at'])) {
                $rawArrived = trim((string) $options['arrived_at']);
                $parsed = Carbon::parse($rawArrived);
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawArrived)) {
                    $arrivedAt = $parsed->isToday() ? now() : $parsed->startOfDay();
                } else {
                    $arrivedAt = $parsed;
                }
            }

            $userId = $movement->created_by ?? Auth::id();
            $variantId = $variant?->id ? (int) $variant->id : ($movement->variant_id ? (int) $movement->variant_id : null);

            $this->ensureOpeningBatchIfNeeded(
                $product,
                $stockBefore,
                $costBefore,
                $arrivedAt,
                $variantId,
                null,
                $userId
            );

            $qty = max(1, (int) $movement->quantity);
            $unitCost = $movement->unit_cost !== null
                ? round(max(0.0, (float) $movement->unit_cost), 2)
                : round(max(0.0, (float) ($variant?->cost_price ?? $product->cost_price ?? $costBefore ?? 0)), 2);

            $totalCost = $movement->total_cost !== null
                ? round(max(0.0, (float) $movement->total_cost), 2)
                : round($unitCost * $qty, 2);

            $rawRef = $options['batch_reference'] ?? ($options['reference_id'] ?? null);
            $reference = !empty($rawRef)
                ? mb_substr(trim((string) $rawRef), 0, 100)
                : $this->generateReference('LOT');

            $batch = StockBatch::create([
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'store_id' => $storeId,
                'inventory_movement_id' => $movement->id,
                'source_type' => 'inventory_in',
                'reference' => $reference,
                'supplier_name' => !empty($options['supplier_name']) ? mb_substr(trim((string) $options['supplier_name']), 0, 255) : null,
                'notes' => !empty($options['reason']) ? (string) $options['reason'] : $movement->reason,
                'arrived_at' => $arrivedAt,
                'initial_quantity' => $qty,
                'remaining_quantity' => $qty,
                'sold_quantity' => 0,
                'manual_out_quantity' => 0,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'realized_revenue' => 0,
                'realized_cost' => 0,
                'realized_profit' => 0,
                'status' => 'active',
                'created_by' => $userId,
            ]);

            $this->refreshQueueStatuses($product->id, $variantId);

            return $batch->fresh();
        });
    }

    /**
     * Allocate sold units for an OrderItem strictly FIFO (idempotent per order_item_id).
     *
     * @return StockBatchAllocation[]
     */
    public function allocateForOrderItem(OrderItem $item, ?Carbon $soldAt = null): array
    {
        if (StockBatchAllocation::where('order_item_id', $item->id)->exists()) {
            return [];
        }

        $qty = max(0, (int) $item->quantity);
        if ($qty <= 0 || !$item->product_id) {
            return [];
        }

        $product = $item->relationLoaded('product') && $item->product
            ? $item->product
            : Product::find($item->product_id);

        if (!$product) {
            return [];
        }

        $unitSellPrice = ($item->subtotal !== null && $qty > 0)
            ? round((float) $item->subtotal / $qty, 2)
            : round((float) $item->price, 2);

        return $this->allocateForSale($product, $qty, $unitSellPrice, [
            'order_id' => $item->order_id,
            'order_item_id' => $item->id,
            'variant_id' => $item->variant_id ? (int) $item->variant_id : null,
            'store_id' => $item->store_id ? (int) $item->store_id : null,
            'allocated_at' => $soldAt ?? now(),
        ]);
    }

    /**
     * Deduct sold quantity from the oldest open arrival batch(es) in strict FIFO order.
     *
     * @return StockBatchAllocation[]
     */
    public function allocateForSale(Product $product, int $quantity, float $unitSellPrice, array $context = []): array
    {
        $quantity = max(0, $quantity);
        if ($quantity <= 0) {
            return [];
        }

        return DB::transaction(function () use ($product, $quantity, $unitSellPrice, $context) {
            $orderId = isset($context['order_id']) ? (int) $context['order_id'] : null;
            $orderItemId = isset($context['order_item_id']) ? (int) $context['order_item_id'] : null;
            $invoiceId = isset($context['invoice_id']) ? (int) $context['invoice_id'] : null;
            $variantId = !empty($context['variant_id']) ? (int) $context['variant_id'] : null;
            $storeId = !empty($context['store_id']) ? (int) $context['store_id'] : null;
            $allocatedAt = isset($context['allocated_at']) && $context['allocated_at'] instanceof Carbon
                ? $context['allocated_at']
                : now();

            $query = StockBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0);

            if ($variantId !== null) {
                $query->where(function ($q) use ($variantId) {
                    $q->where('variant_id', $variantId)->orWhereNull('variant_id');
                })->orderByRaw('CASE WHEN variant_id = ? THEN 0 ELSE 1 END ASC', [$variantId]);
            } else {
                $query->whereNull('variant_id');
            }

            if ($storeId !== null) {
                $query->orderByRaw('CASE WHEN store_id = ? OR store_id IS NULL THEN 0 ELSE 1 END ASC', [$storeId]);
            }

            $openBatches = $query->orderBy('arrived_at', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            if ($openBatches->isEmpty()) {
                return [];
            }

            $remainingToAllocate = $quantity;
            $allocations = [];
            $touchedQueues = [];
            $unitSellPrice = round(max(0.0, $unitSellPrice), 2);

            foreach ($openBatches as $batch) {
                if ($remainingToAllocate <= 0) {
                    break;
                }

                $available = (int) $batch->remaining_quantity;
                if ($available <= 0) {
                    continue;
                }

                $take = min($remainingToAllocate, $available);
                $unitCost = round((float) $batch->unit_cost, 2);
                $allocCost = round($take * $unitCost, 2);
                $allocRevenue = round($take * $unitSellPrice, 2);
                $allocProfit = round($allocRevenue - $allocCost, 2);

                $allocation = StockBatchAllocation::create([
                    'stock_batch_id' => $batch->id,
                    'order_id' => $orderId,
                    'order_item_id' => $orderItemId,
                    'invoice_id' => $invoiceId,
                    'product_id' => $product->id,
                    'variant_id' => $batch->variant_id ?? $variantId,
                    'store_id' => $storeId ?? $batch->store_id,
                    'quantity' => $take,
                    'unit_cost' => $unitCost,
                    'unit_sell_price' => $unitSellPrice,
                    'total_cost' => $allocCost,
                    'total_revenue' => $allocRevenue,
                    'profit' => $allocProfit,
                    'allocated_at' => $allocatedAt,
                ]);

                $batch->remaining_quantity = max(0, $available - $take);
                $batch->sold_quantity = (int) $batch->sold_quantity + $take;
                $batch->realized_revenue = round((float) $batch->realized_revenue + $allocRevenue, 2);
                $batch->realized_cost = round((float) $batch->realized_cost + $allocCost, 2);
                $batch->realized_profit = round((float) $batch->realized_revenue - (float) $batch->realized_cost, 2);
                $batch->first_sold_at = $batch->first_sold_at ?? $allocatedAt;

                if ($batch->remaining_quantity === 0) {
                    $batch->sold_out_at = $allocatedAt;
                }

                $batch->save();

                $allocations[] = $allocation;
                $touchedQueues[$batch->product_id . ':' . ($batch->variant_id ?? 'null')] = [
                    'product_id' => (int) $batch->product_id,
                    'variant_id' => $batch->variant_id ? (int) $batch->variant_id : null,
                ];

                $remainingToAllocate -= $take;
            }

            foreach ($touchedQueues as $pair) {
                $this->refreshQueueStatuses($pair['product_id'], $pair['variant_id']);
            }

            return $allocations;
        });
    }

    /**
     * Reverse all FIFO batch allocations linked to a cancelled/restored/deleted order.
     */
    public function reverseAllocationsForOrder(Order $order): void
    {
        $this->reverseAllocationsQuery(
            StockBatchAllocation::where('order_id', $order->id)
        );
    }

    /**
     * Reverse FIFO batch allocations linked to a specific OrderItem (e.g. item replacement).
     */
    public function reverseAllocationsForOrderItem(OrderItem $item): void
    {
        $this->reverseAllocationsQuery(
            StockBatchAllocation::where('order_item_id', $item->id)
        );
    }

    /**
     * Reverse FIFO batch allocations linked directly to a standalone Invoice.
     */
    public function reverseAllocationsForInvoice(Invoice $invoice): void
    {
        $this->reverseAllocationsQuery(
            StockBatchAllocation::where('invoice_id', $invoice->id)
        );
    }

    /**
     * Shared transactional reversal logic for any scoped allocation query.
     */
    protected function reverseAllocationsQuery($query): void
    {
        DB::transaction(function () use ($query) {
            $allocations = (clone $query)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->get();

            if ($allocations->isEmpty()) {
                return;
            }

            $touchedBatchIds = [];
            $touchedQueues = [];

            foreach ($allocations as $alloc) {
                $batch = StockBatch::whereKey($alloc->stock_batch_id)
                    ->lockForUpdate()
                    ->first();

                if ($batch) {
                    $restoreQty = (int) $alloc->quantity;
                    $batch->remaining_quantity = min(
                        (int) $batch->initial_quantity,
                        (int) $batch->remaining_quantity + $restoreQty
                    );
                    $batch->sold_quantity = max(0, (int) $batch->sold_quantity - $restoreQty);
                    $batch->realized_revenue = round(max(0.0, (float) $batch->realized_revenue - (float) $alloc->total_revenue), 2);
                    $batch->realized_cost = round(max(0.0, (float) $batch->realized_cost - (float) $alloc->total_cost), 2);
                    $batch->realized_profit = round((float) $batch->realized_revenue - (float) $batch->realized_cost, 2);

                    if ($batch->remaining_quantity > 0) {
                        $batch->sold_out_at = null;
                    }

                    $batch->save();

                    $touchedBatchIds[$batch->id] = $batch->id;
                    $touchedQueues[$batch->product_id . ':' . ($batch->variant_id ?? 'null')] = [
                        'product_id' => (int) $batch->product_id,
                        'variant_id' => $batch->variant_id ? (int) $batch->variant_id : null,
                    ];
                }

                $alloc->delete();
            }

            // Recompute first_sold_at accurately from remaining allocations
            foreach ($touchedBatchIds as $batchId) {
                $batch = StockBatch::whereKey($batchId)->first();
                if (!$batch) {
                    continue;
                }

                if ((int) $batch->sold_quantity <= 0) {
                    $batch->first_sold_at = null;
                    $batch->realized_revenue = 0;
                    $batch->realized_cost = 0;
                    $batch->realized_profit = 0;
                } else {
                    $earliestAlloc = StockBatchAllocation::where('stock_batch_id', $batchId)->min('allocated_at');
                    $batch->first_sold_at = $earliestAlloc ? Carbon::parse($earliestAlloc) : $batch->first_sold_at;
                }

                $batch->save();
            }

            foreach ($touchedQueues as $pair) {
                $this->refreshQueueStatuses($pair['product_id'], $pair['variant_id']);
            }
        });
    }

    /**
     * Deduct non-sale stock losses/removals FIFO without inflating sales revenue or profit.
     */
    public function consumeManualOut(
        Product $product,
        int $quantity,
        ?int $variantId = null,
        ?int $storeId = null,
        ?Carbon $at = null
    ): void {
        $quantity = max(0, $quantity);
        if ($quantity <= 0) {
            return;
        }

        DB::transaction(function () use ($product, $quantity, $variantId, $storeId, $at) {
            $atTime = $at ?? now();

            $query = StockBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0);

            if ($variantId !== null) {
                $query->where(function ($q) use ($variantId) {
                    $q->where('variant_id', $variantId)->orWhereNull('variant_id');
                })->orderByRaw('CASE WHEN variant_id = ? THEN 0 ELSE 1 END ASC', [$variantId]);
            } else {
                $query->whereNull('variant_id');
            }

            if ($storeId !== null) {
                $query->orderByRaw('CASE WHEN store_id = ? OR store_id IS NULL THEN 0 ELSE 1 END ASC', [$storeId]);
            }

            $openBatches = $query->orderBy('arrived_at', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            if ($openBatches->isEmpty()) {
                return;
            }

            $remaining = $quantity;
            $touchedQueues = [];

            foreach ($openBatches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $available = (int) $batch->remaining_quantity;
                if ($available <= 0) {
                    continue;
                }

                $take = min($remaining, $available);
                $batch->remaining_quantity = max(0, $available - $take);
                $batch->manual_out_quantity = (int) $batch->manual_out_quantity + $take;

                if ($batch->remaining_quantity === 0 && $batch->sold_out_at === null) {
                    $batch->sold_out_at = $atTime;
                }

                $batch->save();

                $touchedQueues[$batch->product_id . ':' . ($batch->variant_id ?? 'null')] = [
                    'product_id' => (int) $batch->product_id,
                    'variant_id' => $batch->variant_id ? (int) $batch->variant_id : null,
                ];

                $remaining -= $take;
            }

            foreach ($touchedQueues as $pair) {
                $this->refreshQueueStatuses($pair['product_id'], $pair['variant_id']);
            }
        });
    }

    /**
     * Recompute FIFO queue statuses (sold_out / selling / active / waiting) for a product/variant.
     */
    public function refreshQueueStatuses(int $productId, ?int $variantId = null): void
    {
        $batches = StockBatch::where('product_id', $productId)
            ->when(
                $variantId !== null,
                fn ($q) => $q->where('variant_id', $variantId),
                fn ($q) => $q->whereNull('variant_id')
            )
            ->orderBy('arrived_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $foundFirstOpen = false;

        foreach ($batches as $batch) {
            if ((int) $batch->remaining_quantity <= 0) {
                $newStatus = 'sold_out';
                if ($batch->sold_out_at === null) {
                    $batch->sold_out_at = $batch->updated_at ?? now();
                }
            } elseif ((int) $batch->sold_quantity > 0) {
                $newStatus = 'selling';
                $batch->sold_out_at = null;
                $foundFirstOpen = true;
            } elseif (!$foundFirstOpen) {
                $newStatus = 'active';
                $batch->sold_out_at = null;
                $foundFirstOpen = true;
            } else {
                $newStatus = 'waiting';
                $batch->sold_out_at = null;
            }

            if ($batch->status !== $newStatus || $batch->isDirty('sold_out_at')) {
                $batch->status = $newStatus;
                $batch->save();
            }
        }
    }
}
