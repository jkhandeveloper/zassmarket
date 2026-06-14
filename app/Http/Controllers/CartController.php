<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function index(): View
    {
        return view('market.cart', [
            'items' => $this->cart->items(),
            'totalCents' => $this->cart->totalCents(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active && $product->stock > 0 && $product->vendorStore?->isApproved(), 404);

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $this->cart->add($product, $data['quantity'] ?? 1);
        $cartCount = $this->cart->count();

        return back()
            ->with('status', "{$product->name} added to cart. {$this->cartCountLabel($cartCount)}")
            ->with('notification', [
                'message' => "{$product->name} added to cart.",
                'meta' => $this->cartCountLabel($cartCount),
                'action_label' => 'View cart',
                'action_url' => route('cart.index'),
            ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($product, $data['quantity']);
        $cartCount = $this->cart->count();

        return back()
            ->with('status', "Cart updated. {$this->cartCountLabel($cartCount)}")
            ->with('notification', [
                'message' => 'Cart updated.',
                'meta' => $this->cartCountLabel($cartCount),
                'action_label' => $cartCount > 0 ? 'Checkout' : 'Shop products',
                'action_url' => $cartCount > 0 ? route('checkout.create') : route('products.index'),
            ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cart->remove($product);
        $cartCount = $this->cart->count();

        return back()
            ->with('status', "{$product->name} removed from cart. {$this->cartCountLabel($cartCount)}")
            ->with('notification', [
                'message' => "{$product->name} removed from cart.",
                'meta' => $this->cartCountLabel($cartCount),
                'action_label' => $cartCount > 0 ? 'Checkout' : 'Shop products',
                'action_url' => $cartCount > 0 ? route('checkout.create') : route('products.index'),
            ]);
    }

    private function cartCountLabel(int $cartCount): string
    {
        return $cartCount === 1
            ? '1 product in your cart.'
            : "{$cartCount} products in your cart.";
    }
}
