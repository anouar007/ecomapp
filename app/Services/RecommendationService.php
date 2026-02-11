<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get product recommendations for a customer.
     */
    public function getRecommendations(Customer $customer, int $limit = 10): array
    {
        $cacheKey = "recommendations_customer_{$customer->id}";

        return Cache::remember($cacheKey, 3600, function() use ($customer, $limit) {
            $recommendations = [];

            // 1. Products from same categories as customer's previous orders
            $recommendations = array_merge(
                $recommendations,
                $this->getRelatedProducts($customer, $limit)
            );

            // 2. Top-rated products
            $recommendations = array_merge(
                $recommendations,
                $this->getTopRatedProducts($limit)
            );

            // 3. Trending products
            $recommendations = array_merge(
                $recommendations,
                $this->getTrendingProducts($limit)
            );

            // Remove duplicates and limit
            return array_slice(array_unique($recommendations, SORT_REGULAR), 0, $limit);
        });
    }

    protected function getRelatedProducts(Customer $customer, int $limit)
    {
        // Get categories from customer's previous orders
        $categoryIds = $customer->orders()
            ->with('items.product.category')
            ->get()
            ->pluck('items.*.product.category.id')
            ->flatten()
            ->unique()
            ->toArray();

        if (empty($categoryIds)) {
            return [];
        }

        return Product::whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getTopRatedProducts(int $limit)
    {
        return Product::select('products.*')
            ->join('product_reviews', 'products.id', '=', 'product_reviews.product_id')
            ->where('product_reviews.status', 'approved')
            ->where('products.status', 'active')
            ->groupBy('products.id')
            ->orderByRaw('AVG(product_reviews.rating) DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    protected function getTrendingProducts(int $limit)
    {
        // Products with most sales in last 30 days
        return Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->where('products.status', 'active')
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Clear recommendation cache for a customer.
     */
    public function clearCache(Customer $customer): void
    {
        Cache::forget("recommendations_customer_{$customer->id}");
    }
}
