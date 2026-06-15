<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        return view('customer.dashboard', [
            'orders' => auth()->user()->orders()->with('vendorStore')->latest()->take(10)->get(),
            'wishlist' => auth()->user()->wishlistItems()->with(['product.vendorStore', 'product.images'])->latest()->take(10)->get(),
        ]);
    }

    public function wishlist(): View
    {
        return view('customer.wishlist', [
            'items' => auth()->user()->wishlistItems()->with(['product.vendorStore', 'product.images'])->latest()->get(),
        ]);
    }

    public function toggleWishlist(Product $product): RedirectResponse
    {
        $user = auth()->user();
        $existing = $user->wishlistItems()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from wishlist.';
        } else {
            WishlistItem::create(['user_id' => $user->id, 'product_id' => $product->id]);
            $message = 'Added to wishlist.';
        }

        $wishlistCount = $user->wishlistItems()->count();

        return back()
            ->with('status', "{$message} {$this->wishlistCountLabel($wishlistCount)}")
            ->with('notification', [
                'message' => $message,
                'meta' => $this->wishlistCountLabel($wishlistCount),
                'action_label' => 'View wishlist',
                'action_url' => route('customer.wishlist'),
            ]);
    }

    private function wishlistCountLabel(int $wishlistCount): string
    {
        return $wishlistCount === 1
            ? '1 product in your wishlist.'
            : "{$wishlistCount} products in your wishlist.";
    }
}
