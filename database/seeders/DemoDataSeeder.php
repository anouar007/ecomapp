<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Testimonial;
use App\Models\Coupon;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo data for testing the shop and dashboard.
     */
    public function run(): void
    {
        $this->command->info('Seeding demo data...');

        // 1. Create Users
        $this->seedUsers();
        
        // 2. Create Categories
        $categories = $this->seedCategories();
        
        // 3. Create Products
        $products = $this->seedProducts($categories);
        
        // 4. Create Orders
        $this->seedOrders($products);
        
        // 5. Create Testimonials
        $this->seedTestimonials();
        
        // 6. Create Coupons
        $this->seedCoupons();
        
        // 7. Create Product Reviews
        $this->seedProductReviews($products);

        $this->command->info('Demo data seeding complete!');
    }

    private function seedUsers(): void
    {
        // Admin already exists via RolePermissionSeeder
        
        // Regular customers
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['name' => 'Mike Johnson', 'email' => 'mike@example.com'],
            ['name' => 'Sarah Wilson', 'email' => 'sarah@example.com'],
            ['name' => 'David Brown', 'email' => 'david@example.com'],
        ];

        foreach ($customers as $customer) {
            User::firstOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✓ Users seeded');
    }

    private function seedCategories(): array
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Latest electronic gadgets and devices',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400',
                'icon' => 'fa-microchip',
            ],
            [
                'name' => 'Clothing',
                'description' => 'Fashion apparel for all occasions',
                'image' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400',
                'icon' => 'fa-tshirt',
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Everything for your home and garden',
                'image' => 'https://images.unsplash.com/photo-1484101403633-5e6e7f9ec2c5?w=400',
                'icon' => 'fa-home',
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports equipment and activewear',
                'image' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=400',
                'icon' => 'fa-futbol',
            ],
            [
                'name' => 'Books',
                'description' => 'Books, e-books, and audiobooks',
                'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=400',
                'icon' => 'fa-book',
            ],
            [
                'name' => 'Beauty',
                'description' => 'Skincare, makeup, and personal care',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400',
                'icon' => 'fa-spa',
            ],
        ];

        $created = [];
        foreach ($categories as $index => $cat) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'image' => $cat['image'],
                    'icon' => $cat['icon'],
                    'status' => 'active',
                    'sort_order' => $index + 1,
                ]
            );
            $created[] = $category;
        }

        $this->command->info('✓ Categories seeded: ' . count($created));
        return $created;
    }

    private function seedProducts(array $categories): array
    {
        $products = [
            // Electronics
            ['category' => 0, 'name' => 'Wireless Bluetooth Headphones', 'price' => 79.99, 'sale_price' => 59.99, 'stock' => 50, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400'],
            ['category' => 0, 'name' => 'Smart Watch Pro', 'price' => 299.99, 'sale_price' => null, 'stock' => 25, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400'],
            ['category' => 0, 'name' => 'Portable Power Bank 20000mAh', 'price' => 49.99, 'sale_price' => null, 'stock' => 100, 'image' => 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400'],
            ['category' => 0, 'name' => 'Wireless Charging Pad', 'price' => 34.99, 'sale_price' => 24.99, 'stock' => 75, 'image' => 'https://images.unsplash.com/photo-1586816879360-004f5b0c51e3?w=400'],
            
            // Clothing
            ['category' => 1, 'name' => 'Premium Cotton T-Shirt', 'price' => 29.99, 'sale_price' => null, 'stock' => 200, 'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400'],
            ['category' => 1, 'name' => 'Classic Denim Jeans', 'price' => 89.99, 'sale_price' => 69.99, 'stock' => 80, 'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400'],
            ['category' => 1, 'name' => 'Leather Jacket', 'price' => 199.99, 'sale_price' => null, 'stock' => 30, 'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400'],
            ['category' => 1, 'name' => 'Running Sneakers', 'price' => 129.99, 'sale_price' => 99.99, 'stock' => 60, 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400'],
            
            // Home & Garden
            ['category' => 2, 'name' => 'Modern Table Lamp', 'price' => 59.99, 'sale_price' => null, 'stock' => 40, 'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400'],
            ['category' => 2, 'name' => 'Indoor Plant Set', 'price' => 45.99, 'sale_price' => 39.99, 'stock' => 55, 'image' => 'https://images.unsplash.com/photo-1459411552884-841db9b3cc2a?w=400'],
            ['category' => 2, 'name' => 'Decorative Throw Pillows', 'price' => 34.99, 'sale_price' => null, 'stock' => 120, 'image' => 'https://images.unsplash.com/photo-1586105251261-72a756497a11?w=400'],
            
            // Sports
            ['category' => 3, 'name' => 'Yoga Mat Premium', 'price' => 39.99, 'sale_price' => null, 'stock' => 90, 'image' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400'],
            ['category' => 3, 'name' => 'Adjustable Dumbbells Set', 'price' => 149.99, 'sale_price' => 129.99, 'stock' => 35, 'image' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=400'],
            ['category' => 3, 'name' => 'Fitness Tracker Band', 'price' => 59.99, 'sale_price' => null, 'stock' => 70, 'image' => 'https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?w=400'],
            
            // Books
            ['category' => 4, 'name' => 'Bestseller Novel Collection', 'price' => 24.99, 'sale_price' => 19.99, 'stock' => 150, 'image' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400'],
            ['category' => 4, 'name' => 'Business Strategy Guide', 'price' => 34.99, 'sale_price' => null, 'stock' => 85, 'image' => 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400'],
            
            // Beauty
            ['category' => 5, 'name' => 'Skincare Essentials Kit', 'price' => 79.99, 'sale_price' => 64.99, 'stock' => 65, 'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400'],
            ['category' => 5, 'name' => 'Luxury Perfume', 'price' => 120.00, 'sale_price' => null, 'stock' => 45, 'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400'],
            ['category' => 5, 'name' => 'Makeup Brush Set', 'price' => 44.99, 'sale_price' => 34.99, 'stock' => 100, 'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400'],
        ];

        $created = [];
        foreach ($products as $prod) {
            $category = $categories[$prod['category']];
            
            $product = Product::firstOrCreate(
                ['sku' => 'SKU-' . strtoupper(Str::random(8))],
                [
                    'name' => $prod['name'],
                    'description' => 'High-quality ' . strtolower($prod['name']) . '. Perfect for everyday use with premium materials and craftsmanship.',
                    'price' => $prod['price'],
                    'cost_price' => $prod['price'] * 0.6,
                    'sale_price' => $prod['sale_price'],
                    'sale_end_date' => $prod['sale_price'] ? now()->addDays(rand(5, 30)) : null,
                    'stock' => $prod['stock'],
                    'min_stock' => 10,
                    'category_id' => $category->id,
                    'status' => 'active',
                    'image' => $prod['image'],
                ]
            );
            $created[] = $product;
        }

        $this->command->info('✓ Products seeded: ' . count($created));
        return $created;
    }

    private function seedOrders(array $products): void
    {
        $users = User::whereNotIn('email', ['admin@example.com'])->get();
        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $paymentStatuses = ['pending', 'paid', 'paid', 'paid']; // Most should be paid
        
        $orderCount = 0;
        foreach ($users as $user) {
            // Create 2-4 orders per user
            $numOrders = rand(2, 4);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $statusIndex = rand(0, 3);
                $subtotal = 0;
                $itemsData = [];
                
                // Add 1-4 random products
                $orderProducts = collect($products)->random(rand(1, 4));
                foreach ($orderProducts as $product) {
                    $qty = rand(1, 3);
                    $price = $product->sale_price ?? $product->price;
                    $subtotal += $price * $qty;
                    
                    $itemsData[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $price * $qty,
                    ];
                }
                
                $tax = $subtotal * 0.1;
                $shipping = $subtotal > 100 ? 0 : 9.99;
                $total = $subtotal + $tax + $shipping;
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => '+1' . rand(1000000000, 9999999999),
                    'shipping_address' => rand(100, 999) . ' Main Street',
                    'shipping_city' => ['New York', 'Los Angeles', 'Chicago', 'Houston', 'Phoenix'][rand(0, 4)],
                    'shipping_state' => ['NY', 'CA', 'IL', 'TX', 'AZ'][rand(0, 4)],
                    'shipping_zip' => rand(10000, 99999),
                    'shipping_country' => 'USA',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping_cost' => $shipping,
                    'discount' => 0,
                    'total' => $total,
                    'status' => $statuses[$statusIndex],
                    'payment_status' => $paymentStatuses[$statusIndex],
                    'payment_method' => ['credit_card', 'paypal', 'stripe'][rand(0, 2)],
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
                
                foreach ($itemsData as $item) {
                    OrderItem::create(array_merge($item, ['order_id' => $order->id]));
                }
                
                $orderCount++;
            }
        }

        $this->command->info('✓ Orders seeded: ' . $orderCount);
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            ['name' => 'Emily R.', 'role' => 'Customer', 'content' => 'Amazing quality products and super fast delivery! Will definitely order again.', 'rating' => 5],
            ['name' => 'Michael T.', 'role' => 'Premium Member', 'content' => 'Great shopping experience. The customer service team was very helpful.', 'rating' => 5],
            ['name' => 'Sarah K.', 'role' => 'Shopper', 'content' => 'Love the variety of products. Found exactly what I was looking for.', 'rating' => 4],
            ['name' => 'James W.', 'role' => 'Customer', 'content' => 'Best online store I have used. Premium quality at great prices!', 'rating' => 5],
            ['name' => 'Lisa M.', 'role' => 'VIP Member', 'content' => 'Fast shipping and excellent packaging. Very impressed!', 'rating' => 5],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name']],
                [
                    'role' => $t['role'],
                    'content' => $t['content'],
                    'rating' => $t['rating'],
                ]
            );
        }

        $this->command->info('✓ Testimonials seeded: ' . count($testimonials));
    }

    private function seedCoupons(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $adminId = $admin ? $admin->id : 1;

        $coupons = [
            ['code' => 'WELCOME10', 'name' => 'Welcome 10%', 'type' => 'percentage', 'value' => 10, 'min_order' => 50],
            ['code' => 'SAVE20', 'name' => 'Save 20%', 'type' => 'percentage', 'value' => 20, 'min_order' => 100],
            ['code' => 'FLAT15', 'name' => 'Flat $15 Off', 'type' => 'fixed', 'value' => 15, 'min_order' => 75],
            ['code' => 'FREESHIP', 'name' => 'Free Shipping', 'type' => 'free_shipping', 'value' => 0, 'min_order' => 50],
        ];

        foreach ($coupons as $c) {
            Coupon::firstOrCreate(
                ['code' => $c['code']],
                [
                    'name' => $c['name'],
                    'type' => $c['type'],
                    'value' => $c['value'],
                    'min_order_amount' => $c['min_order'],
                    'valid_from' => now(),
                    'valid_to' => now()->addMonths(3),
                    'status' => 'active',
                    'usage_limit' => 100,
                    'usage_count' => rand(0, 20),
                    'created_by' => $adminId,
                ]
            );
        }

        $this->command->info('✓ Coupons seeded: ' . count($coupons));
    }

    private function seedProductReviews(array $products): void
    {
        $reviewTemplates = [
            ['title' => 'Excellent product!', 'comment' => 'Exactly what I was looking for. Great quality and fast shipping.', 'rating' => 5],
            ['title' => 'Very satisfied', 'comment' => 'Works perfectly. Would definitely recommend to others.', 'rating' => 5],
            ['title' => 'Good value for money', 'comment' => 'Decent product at a reasonable price. No complaints.', 'rating' => 4],
            ['title' => 'Met my expectations', 'comment' => 'Product arrived on time and matches the description.', 'rating' => 4],
            ['title' => 'Amazing quality', 'comment' => 'The quality exceeded my expectations. Will buy again!', 'rating' => 5],
            ['title' => 'Pretty good', 'comment' => 'Nice product overall. Minor issues but nothing major.', 'rating' => 4],
            ['title' => 'Love it!', 'comment' => 'This is my second purchase. Absolutely love this product!', 'rating' => 5],
            ['title' => 'Solid purchase', 'comment' => 'Good build quality. Does exactly what it should.', 'rating' => 4],
            ['title' => 'Highly recommended', 'comment' => 'Great product, great price, great service. What more could you ask for?', 'rating' => 5],
            ['title' => 'Worth every penny', 'comment' => 'Initially hesitant about the price but it is definitely worth it.', 'rating' => 5],
        ];

        $customerNames = ['Emily R.', 'Michael T.', 'Sarah K.', 'James W.', 'Lisa M.', 'Robert B.', 'Jennifer L.', 'William C.'];
        
        $reviewCount = 0;
        foreach ($products as $product) {
            // Add 2-4 reviews per product
            $numReviews = rand(2, 4);
            $usedTemplates = [];
            
            for ($i = 0; $i < $numReviews; $i++) {
                // Get a random template that hasn't been used for this product
                do {
                    $templateIndex = array_rand($reviewTemplates);
                } while (in_array($templateIndex, $usedTemplates) && count($usedTemplates) < count($reviewTemplates));
                
                $usedTemplates[] = $templateIndex;
                $template = $reviewTemplates[$templateIndex];
                $customerName = $customerNames[array_rand($customerNames)];
                
                ProductReview::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'customer_name' => $customerName,
                        'title' => $template['title'],
                    ],
                    [
                        'customer_email' => strtolower(str_replace([' ', '.'], '', $customerName)) . '@example.com',
                        'rating' => $template['rating'],
                        'comment' => $template['comment'],
                        'verified_purchase' => rand(0, 1) ? true : false,
                        'status' => 'approved',
                        'featured' => rand(0, 10) > 8 ? true : false,
                        'helpful_count' => rand(0, 25),
                        'approved_at' => now()->subDays(rand(1, 30)),
                        'created_at' => now()->subDays(rand(1, 60)),
                    ]
                );
                $reviewCount++;
            }
        }

        $this->command->info('✓ Product reviews seeded: ' . $reviewCount);
    }
}
