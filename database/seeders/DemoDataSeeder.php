<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VendorStore;
use App\Models\VendorSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['admin', 'vendor', 'customer'] as $role) {
            Role::findOrCreate($role);
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@zassmarket.test'],
            ['name' => 'Zass Admin', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        $customer = User::updateOrCreate(
            ['email' => 'customer@zassmarket.test'],
            ['name' => 'Casey Customer', 'password' => Hash::make('password')]
        );
        $customer->syncRoles(['customer']);

        $starter = SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'product_limit' => 10,
                'monthly_order_limit' => 100,
                'price_cents' => 1900,
                'description' => 'For new sellers validating their catalog.',
                'is_active' => true,
            ]
        );

        $growth = SubscriptionPlan::updateOrCreate(
            ['slug' => 'growth'],
            [
                'name' => 'Growth',
                'product_limit' => 100,
                'monthly_order_limit' => 1000,
                'price_cents' => 7900,
                'description' => 'For scaling vendors with higher order volume.',
                'is_active' => true,
            ]
        );

        $categories = collect([
            ['name' => 'Home Studio', 'slug' => 'home-studio'],
            ['name' => 'Desk Gear', 'slug' => 'desk-gear'],
            ['name' => 'Wellness', 'slug' => 'wellness'],
            ['name' => 'Travel', 'slug' => 'travel'],
        ])->map(fn ($category) => Category::updateOrCreate(['slug' => $category['slug']], $category));

        $vendorUser = User::updateOrCreate(
            ['email' => 'vendor@zassmarket.test'],
            ['name' => 'Vera Vendor', 'password' => Hash::make('password')]
        );
        $vendorUser->syncRoles(['vendor']);

        $store = VendorStore::updateOrCreate(
            ['slug' => 'northline-goods'],
            [
                'owner_id' => $vendorUser->id,
                'subscription_plan_id' => $growth->id,
                'name' => 'Northline Goods',
                'status' => VendorStore::STATUS_APPROVED,
                'support_email' => 'support@northline.test',
                'phone' => '+1 555 0100',
                'description' => 'Useful everyday products for work and home.',
                'approved_at' => now(),
            ]
        );

        VendorSubscription::updateOrCreate(
            ['vendor_store_id' => $store->id, 'subscription_plan_id' => $growth->id],
            ['status' => 'active', 'starts_at' => now()]
        );

        $pendingUser = User::updateOrCreate(
            ['email' => 'pending-vendor@zassmarket.test'],
            ['name' => 'Pending Vendor', 'password' => Hash::make('password')]
        );
        $pendingUser->syncRoles(['vendor']);

        $pendingStore = VendorStore::updateOrCreate(
            ['slug' => 'pending-finds'],
            [
                'owner_id' => $pendingUser->id,
                'subscription_plan_id' => $starter->id,
                'name' => 'Pending Finds',
                'status' => VendorStore::STATUS_PENDING,
                'support_email' => 'hello@pending.test',
                'description' => 'Awaiting admin approval.',
            ]
        );

        VendorSubscription::updateOrCreate(
            ['vendor_store_id' => $pendingStore->id, 'subscription_plan_id' => $starter->id],
            ['status' => 'active', 'starts_at' => now()]
        );

        collect([
            ['name' => 'Walnut Laptop Stand', 'price_cents' => 6800, 'stock' => 32, 'category' => 'desk-gear', 'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Compact Podcast Light', 'price_cents' => 4500, 'stock' => 18, 'category' => 'home-studio', 'image' => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Ribbed Travel Tumbler', 'price_cents' => 2900, 'stock' => 55, 'category' => 'travel', 'image' => 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=900&q=80'],
            ['name' => 'Focus Aromatherapy Kit', 'price_cents' => 3600, 'stock' => 24, 'category' => 'wellness', 'image' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=900&q=80'],
        ])->each(function (array $productData) use ($store, $categories) {
            $category = $categories->firstWhere('slug', $productData['category']);
            $product = Product::updateOrCreate(
                ['vendor_store_id' => $store->id, 'slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $category?->id,
                    'name' => $productData['name'],
                    'description' => 'Demo catalog item with vendor-owned inventory, stock, and checkout support.',
                    'price_cents' => $productData['price_cents'],
                    'stock' => $productData['stock'],
                    'is_active' => true,
                ]
            );

            $product->images()->updateOrCreate(
                ['sort_order' => 0],
                ['path' => $productData['image'], 'alt_text' => $productData['name']]
            );
        });

        $product = Product::first();
        $customer->wishlistItems()->updateOrCreate(['product_id' => $product->id]);

        $order = Order::updateOrCreate(
            ['order_number' => 'ZM-DEMO-1001'],
            [
                'vendor_store_id' => $store->id,
                'customer_id' => $customer->id,
                'status' => 'processing',
                'payment_status' => 'paid',
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'shipping_address' => '123 Market Street, Demo City',
                'subtotal_cents' => $product->price_cents,
                'shipping_cents' => 0,
                'total_cents' => $product->price_cents,
            ]
        );

        $order->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'product_name' => $product->name,
                'unit_price_cents' => $product->price_cents,
                'quantity' => 1,
                'total_cents' => $product->price_cents,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About ZassMarket',
                'body' => 'ZassMarket is a multi-vendor ecommerce SaaS marketplace demo built with Laravel, Breeze, Spatie Permission, Filament admin resources, and Blade dashboards.',
                'is_published' => true,
            ]
        );
    }
}
