<?php

use App\Models\Order;
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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'confirmed_by')) {
                $table->foreignId('confirmed_by')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')
                    ->nullable()
                    ->after('confirmed_by')
                    ->index();
            }
        });

        $validUserIds = array_flip(DB::table('users')->pluck('id')->all());

        // Backfill POS order creators from existing orders.user_id
        if (!empty($validUserIds)) {
            $posOrders = DB::table('orders')
                ->whereNull('created_by')
                ->whereNotNull('user_id')
                ->where(function ($q) {
                    $q->where('shipping_address', 'like', 'Vente en boutique (POS)%')
                        ->orWhere('notes', 'like', 'Vente POS%');
                })
                ->get(['id', 'user_id']);

            foreach ($posOrders as $po) {
                if (isset($validUserIds[(int) $po->user_id])) {
                    DB::table('orders')
                        ->where('id', $po->id)
                        ->update(['created_by' => (int) $po->user_id]);
                }
            }
        }

        // Backfill first confirming user from historical activity_logs (earliest transition out of 'pending')
        if (Schema::hasTable('activity_logs') && !empty($validUserIds)) {
            $logs = DB::table('activity_logs')
                ->where('subject_type', Order::class)
                ->where('event', 'updated')
                ->whereNotNull('user_id')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['subject_id', 'user_id', 'properties', 'created_at']);

            $firstConfirmations = [];
            foreach ($logs as $log) {
                $orderId = (int) $log->subject_id;
                $userId = (int) $log->user_id;
                if ($orderId <= 0 || isset($firstConfirmations[$orderId]) || !isset($validUserIds[$userId])) {
                    continue;
                }

                $props = is_string($log->properties)
                    ? json_decode($log->properties, true)
                    : (is_array($log->properties) ? $log->properties : []);
                if (!is_array($props)) {
                    continue;
                }

                $old = is_array($props['old'] ?? null) ? $props['old'] : [];
                $new = is_array($props['new'] ?? null) ? $props['new'] : [];
                $oldStatus = strtolower((string) ($old['status'] ?? ''));
                $newStatus = strtolower((string) ($new['status'] ?? ''));

                if ($oldStatus === 'pending' && $newStatus !== '' && $newStatus !== 'pending') {
                    $firstConfirmations[$orderId] = [
                        'confirmed_by' => $userId,
                        'confirmed_at' => $log->created_at,
                    ];
                }
            }

            foreach ($firstConfirmations as $orderId => $info) {
                DB::table('orders')
                    ->where('id', $orderId)
                    ->whereNull('confirmed_by')
                    ->update([
                        'confirmed_by' => $info['confirmed_by'],
                        'confirmed_at' => $info['confirmed_at'],
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'confirmed_at')) {
                $table->dropIndex(['confirmed_at']);
                $table->dropColumn('confirmed_at');
            }

            if (Schema::hasColumn('orders', 'confirmed_by')) {
                $table->dropConstrainedForeignId('confirmed_by');
            }

            if (Schema::hasColumn('orders', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
        });
    }
};
