<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'cart.items';

    public function items(): Collection
    {
        $rawItems = collect(Session::get(self::SESSION_KEY, []));
        $products = Product::with(['vendorStore', 'images'])
            ->whereIn('id', $rawItems->keys())
            ->get()
            ->keyBy('id');

        return $rawItems
            ->map(function (int $quantity, int|string $productId) use ($products) {
                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal_cents' => $product->price_cents * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $items = Session::get(self::SESSION_KEY, []);
        $items[$product->id] = min($product->stock, ($items[$product->id] ?? 0) + $quantity);

        Session::put(self::SESSION_KEY, $items);
    }

    public function update(Product $product, int $quantity): void
    {
        $items = Session::get(self::SESSION_KEY, []);

        if ($quantity <= 0) {
            unset($items[$product->id]);
        } else {
            $items[$product->id] = min($product->stock, $quantity);
        }

        Session::put(self::SESSION_KEY, $items);
    }

    public function remove(Product $product): void
    {
        $items = Session::get(self::SESSION_KEY, []);
        unset($items[$product->id]);

        Session::put(self::SESSION_KEY, $items);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function totalCents(): int
    {
        return $this->items()->sum('subtotal_cents');
    }

    public function count(): int
    {
        return $this->items()->sum('quantity');
    }
}
