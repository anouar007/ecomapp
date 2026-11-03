<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add blacklist and tracking columns to customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'is_blacklisted')) {
                $table->boolean('is_blacklisted')->default(false)->index()->after('status');
            }
            if (!Schema::hasColumn('customers', 'blacklist_reason')) {
                $table->text('blacklist_reason')->nullable()->after('is_blacklisted');
            }
            if (!Schema::hasColumn('customers', 'blacklisted_at')) {
                $table->timestamp('blacklisted_at')->nullable()->after('blacklist_reason');
            }
            if (!Schema::hasColumn('customers', 'last_order_at')) {
                $table->timestamp('last_order_at')->nullable()->index()->after('total_orders');
            }
        });

        // Add customer_id to orders
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->index()->after('user_id');
            }
        });

        // Backfill existing orders with customers
        try {
            $orders = DB::table('orders')->orderBy('id', 'asc')->get();
            foreach ($orders as $order) {
                $customer = null;
                $phone = !empty($order->customer_phone) ? trim($order->customer_phone) : null;
                $email = !empty($order->customer_email) ? trim($order->customer_email) : null;

                if ($phone) {
                    $customer = Customer::where('phone', $phone)->first();
                }
                if (!$customer && $email) {
                    $customer = Customer::where('email', $email)->first();
                }

                // If customer does not exist, create one
                if (!$customer) {
                    $cleanEmail = $email ?: ('client_' . ($phone ? preg_replace('/[^0-9]/', '', $phone) : $order->id) . '@wina.local');
                    $customer = Customer::create([
                        'name' => $order->customer_name ?: 'Client #' . $order->order_number,
                        'email' => $cleanEmail,
                        'phone' => $phone,
                        'address' => $order->shipping_address,
                        'city' => $order->shipping_city,
                        'status' => 'active',
                        'total_orders' => 0,
                        'total_spent' => 0,
                        'last_order_at' => $order->created_at,
                    ]);
                }

                // Associate customer to order
                DB::table('orders')->where('id', $order->id)->update([
                    'customer_id' => $customer->id,
                ]);
            }

            // Recalculate each customer's metrics
            $customers = Customer::all();
            foreach ($customers as $c) {
                $custOrders = DB::table('orders')->where('customer_id', $c->id);
                $totalOrders = (clone $custOrders)->count();
                $totalSpent = (clone $custOrders)->sum('total');
                $lastOrder = (clone $custOrders)->orderBy('created_at', 'desc')->first();

                $c->update([
                    'total_orders' => $totalOrders,
                    'total_spent' => $totalSpent,
                    'last_order_at' => $lastOrder ? $lastOrder->created_at : $c->created_at,
                ]);
            }
        } catch (\Throwable $e) {
            // Silently continue if database has unusual seed data
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $cols = ['is_blacklisted', 'blacklist_reason', 'blacklisted_at', 'last_order_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
