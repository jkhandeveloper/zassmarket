<?php

namespace App\Services\Notifications;

use App\Mail\MarketplaceNotificationMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorStore;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MarketplaceEmailService
{
    public function customerRegistered(User $user): void
    {
        $this->sendToAdmins(new MarketplaceNotificationMail(
            subjectLine: 'New customer registered',
            headline: 'New customer registered',
            lines: ['A new customer account was created on ZassMarket.'],
            facts: [
                'Customer' => $user->name,
                'Email' => $user->email,
            ],
        ));

        $this->send($user->email, new MarketplaceNotificationMail(
            subjectLine: 'Welcome to ZassMarket',
            headline: 'Welcome to ZassMarket',
            lines: ['Your customer account is ready. You can now shop, save wishlist items, and checkout faster.'],
            facts: [
                'Account' => $user->email,
            ],
            actionLabel: 'Start shopping',
            actionUrl: route('products.index'),
        ));
    }

    public function vendorRegistered(VendorStore $store): void
    {
        $store->loadMissing(['owner', 'plan']);

        $this->sendToAdmins(new MarketplaceNotificationMail(
            subjectLine: 'New vendor application',
            headline: 'New vendor application',
            lines: ['A vendor submitted an application and needs admin review.'],
            facts: [
                'Vendor' => $store->name,
                'Owner' => $store->owner?->name,
                'Email' => $store->owner?->email,
                'Plan' => $store->plan?->name,
                'Status' => $store->status,
            ],
        ));

        $this->send($store->owner?->email, new MarketplaceNotificationMail(
            subjectLine: 'Vendor application received',
            headline: 'Vendor application received',
            lines: ['Your vendor application has been submitted. Please complete billing and wait for admin approval before managing products.'],
            facts: [
                'Store' => $store->name,
                'Plan' => $store->plan?->name,
                'Status' => $store->status,
            ],
            actionLabel: 'View vendor dashboard',
            actionUrl: route('vendor.dashboard'),
        ));
    }

    public function productCreated(Product $product): void
    {
        $product->loadMissing(['vendorStore.owner', 'category']);

        $this->sendToAdmins(new MarketplaceNotificationMail(
            subjectLine: 'New vendor product added',
            headline: 'New vendor product added',
            lines: ['A vendor added a new product to the marketplace catalog.'],
            facts: $this->productFacts($product),
        ));

        $this->send($product->vendorStore?->owner?->email, new MarketplaceNotificationMail(
            subjectLine: 'Your product was added',
            headline: 'Your product was added',
            lines: ['Your product is now saved in your vendor dashboard.'],
            facts: $this->productFacts($product),
            actionLabel: 'Manage products',
            actionUrl: route('vendor.products.index'),
        ));
    }

    public function orderPlaced(Order $order): void
    {
        $order->loadMissing(['vendorStore.owner', 'items']);

        $this->sendToAdmins(new MarketplaceNotificationMail(
            subjectLine: "New order {$order->order_number}",
            headline: 'New order placed',
            lines: ['A new vendor order was placed on ZassMarket.'],
            facts: $this->orderFacts($order),
        ));

        $this->send($order->vendorStore?->owner?->email, new MarketplaceNotificationMail(
            subjectLine: "New order {$order->order_number}",
            headline: 'You received a new order',
            lines: ['A customer placed an order containing products from your store.'],
            facts: $this->orderFacts($order),
            actionLabel: 'View orders',
            actionUrl: route('vendor.orders.index'),
        ));
    }

    private function sendToAdmins(MarketplaceNotificationMail $mail): void
    {
        foreach ($this->adminRecipients() as $email) {
            $this->send($email, clone $mail);
        }
    }

    private function send(?string $email, MarketplaceNotificationMail $mail): void
    {
        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send($mail);
        } catch (Throwable $exception) {
            Log::error('Marketplace email failed.', [
                'recipient' => $email,
                'subject' => $mail->subjectLine,
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    private function adminRecipients(): array
    {
        $configured = config('mail.admin_address');
        $adminUsers = User::role('admin')->pluck('email')->all();

        return collect([$configured, ...$adminUsers])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string|int|null>
     */
    private function productFacts(Product $product): array
    {
        return [
            'Product' => $product->name,
            'Vendor' => $product->vendorStore?->name,
            'Category' => $product->category?->name,
            'Price' => $product->formattedPrice(),
            'Discount' => $product->hasDiscount() ? "{$product->discount_percent}% off {$product->formattedOriginalPrice()}" : 'No discount',
            'Stock' => $product->stock,
            'Status' => $product->is_active ? 'Active' : 'Inactive',
        ];
    }

    /**
     * @return array<string, string|int|null>
     */
    private function orderFacts(Order $order): array
    {
        return [
            'Order' => $order->order_number,
            'Vendor' => $order->vendorStore?->name,
            'Customer' => $order->customer_name,
            'Customer email' => $order->customer_email,
            'Payment' => $order->payment_method,
            'Total' => $order->formattedTotal(),
            'Items' => $order->items->map(fn ($item) => "{$item->product_name} x {$item->quantity}")->implode(', '),
        ];
    }
}
