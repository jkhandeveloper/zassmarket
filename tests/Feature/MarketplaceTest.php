<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\VendorStore;
use App\Models\VendorSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_checkout_with_session_cart(): void
    {
        $product = $this->approvedProduct(stock: 5);

        $this
            ->withSession(['cart.items' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Guest Buyer',
                'customer_email' => 'guest@example.com',
                'customer_phone' => '555-0101',
                'shipping_address' => '42 Checkout Lane',
            ])
            ->assertRedirect(route('checkout.thank-you'));

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'guest@example.com',
            'vendor_store_id' => $product->vendor_store_id,
            'total_cents' => $product->price_cents * 2,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => $product->name,
            'quantity' => 2,
        ]);

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_public_product_list_shows_out_of_stock_active_products(): void
    {
        $product = $this->approvedProduct(stock: 0);

        $this
            ->get(route('products.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Out of stock');
    }

    public function test_pending_vendor_can_view_status_but_cannot_manage_products(): void
    {
        $this->seedRoles();
        $plan = SubscriptionPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'product_limit' => 1,
            'monthly_order_limit' => 10,
            'price_cents' => 1000,
        ]);
        $user = User::create(['name' => 'Vendor', 'email' => 'pending@example.com', 'password' => Hash::make('password')]);
        $user->assignRole('vendor');
        VendorStore::create([
            'owner_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'name' => 'Pending Shop',
            'slug' => 'pending-shop',
            'status' => VendorStore::STATUS_PENDING,
        ]);

        $this->actingAs($user)->get(route('vendor.dashboard'))->assertOk()->assertSee('pending');
        $this->actingAs($user)->get(route('vendor.products.index'))->assertForbidden();
    }

    public function test_vendor_product_limit_is_enforced(): void
    {
        $product = $this->approvedProduct(stock: 5, productLimit: 1);
        $vendor = $product->vendorStore->owner;

        $this
            ->actingAs($vendor)
            ->post(route('vendor.products.store'), [
                'name' => 'Second Product',
                'description' => 'Should not fit plan.',
                'price' => 12.50,
                'stock' => 3,
                'is_active' => 1,
            ])
            ->assertForbidden();
    }

    private function approvedProduct(int $stock = 10, int $productLimit = 10): Product
    {
        $this->seedRoles();

        $plan = SubscriptionPlan::create([
            'name' => 'Growth',
            'slug' => 'growth-'.uniqid(),
            'product_limit' => $productLimit,
            'monthly_order_limit' => 100,
            'price_cents' => 7900,
        ]);

        $vendor = User::create(['name' => 'Vendor', 'email' => uniqid().'@vendor.test', 'password' => Hash::make('password')]);
        $vendor->assignRole('vendor');

        $store = VendorStore::create([
            'owner_id' => $vendor->id,
            'subscription_plan_id' => $plan->id,
            'name' => 'Approved Shop',
            'slug' => 'approved-shop-'.uniqid(),
            'status' => VendorStore::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        VendorSubscription::create([
            'vendor_store_id' => $store->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $category = Category::create(['name' => 'Desk Gear', 'slug' => 'desk-gear-'.uniqid()]);

        return Product::create([
            'vendor_store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product-'.uniqid(),
            'description' => 'A test product.',
            'price_cents' => 2500,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function seedRoles(): void
    {
        foreach (['admin', 'vendor', 'customer'] as $role) {
            Role::findOrCreate($role);
        }
    }
}
