<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use App\Services\Notifications\MarketplaceEmailService;
use App\Services\StripeCheckoutService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cart) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Your cart is empty.');
        }

        return view('market.checkout', [
            'items' => $this->cart->items(),
            'totalCents' => $this->cart->totalCents(),
            'user' => auth()->user(),
            'stripeEnabled' => (bool) config('services.stripe.enabled') && filled(config('services.stripe.secret')),
        ]);
    }

    public function store(Request $request, StripeCheckoutService $stripe, MarketplaceEmailService $emails): RedirectResponse
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Your cart is empty.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'payment_method' => ['required', 'in:cod,card'],
        ]);

        if ($data['payment_method'] === 'card' && ! $stripe->isEnabled()) {
            throw ValidationException::withMessages([
                'payment_method' => 'Card payment is not enabled right now.',
            ]);
        }

        $orders = DB::transaction(function () use ($items, $data) {
            return $items
                ->groupBy(fn (array $item) => $item['product']->vendor_store_id)
                ->map(function ($vendorItems) use ($data) {
                    $vendorStore = $vendorItems->first()['product']->vendorStore;

                    if (! $vendorStore->canAcceptOrder()) {
                        throw ValidationException::withMessages([
                            'cart' => "{$vendorStore->name} has reached its monthly order limit.",
                        ]);
                    }

                    $subtotal = $vendorItems->sum('subtotal_cents');
                    $order = Order::create([
                        'vendor_store_id' => $vendorStore->id,
                        'customer_id' => auth()->id(),
                        'order_number' => 'ZM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                        'customer_name' => $data['customer_name'],
                        'customer_email' => $data['customer_email'],
                        'customer_phone' => $data['customer_phone'] ?? null,
                        'shipping_address' => $data['shipping_address'],
                        'payment_method' => $data['payment_method'],
                        'payment_status' => $data['payment_method'] === 'cod' ? 'cod_pending' : 'unpaid',
                        'subtotal_cents' => $subtotal,
                        'shipping_cents' => 0,
                        'total_cents' => $subtotal,
                    ]);

                    foreach ($vendorItems as $item) {
                        /** @var Product $product */
                        $product = $item['product'];
                        $quantity = $item['quantity'];

                        if (! $product->is_active || ! $product->vendorStore->isApproved() || $product->stock < $quantity) {
                            throw ValidationException::withMessages([
                                'cart' => "{$product->name} is no longer available in the requested quantity.",
                            ]);
                        }

                        $order->items()->create([
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'unit_price_cents' => $product->currentPriceCents(),
                            'quantity' => $quantity,
                            'total_cents' => $product->currentPriceCents() * $quantity,
                        ]);

                        $product->decrement('stock', min($quantity, $product->stock));
                    }

                    return $order;
                })
                ->values();
        });

        if ($data['payment_method'] === 'card') {
            $session = $stripe->createSession([
                'mode' => 'payment',
                'success_url' => route('checkout.card.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.create'),
                'customer_email' => $data['customer_email'],
                'metadata[order_ids]' => $orders->pluck('id')->implode(','),
                'line_items[0][quantity]' => 1,
                'line_items[0][price_data][currency]' => strtolower(config('services.stripe.currency', 'usd')),
                'line_items[0][price_data][unit_amount]' => $orders->sum('total_cents'),
                'line_items[0][price_data][product_data][name]' => 'ZassMarket marketplace order',
            ]);

            Order::whereIn('id', $orders->pluck('id'))->update([
                'stripe_checkout_session_id' => $session['id'] ?? null,
            ]);

            return redirect()->away($session['url']);
        }

        $this->cart->clear();
        $orders->each(fn (Order $order) => $emails->orderPlaced($order));

        return redirect()->route('checkout.thank-you')->with('orders', $orders->pluck('order_number')->all());
    }

    public function cardSuccess(Request $request, StripeCheckoutService $stripe, MarketplaceEmailService $emails): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        abort_unless($sessionId, 404);

        $session = $stripe->retrieveSession($sessionId);

        if (($session['payment_status'] ?? null) !== 'paid') {
            throw ValidationException::withMessages([
                'payment_method' => 'Stripe has not marked this payment as paid yet.',
            ]);
        }

        $orders = Order::where('stripe_checkout_session_id', $sessionId)->get();

        Order::whereIn('id', $orders->pluck('id'))->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->cart->clear();
        $orders->each(fn (Order $order) => $emails->orderPlaced($order));

        return redirect()->route('checkout.thank-you')->with('orders', $orders->pluck('order_number')->all());
    }

    public function thankYou(): View
    {
        return view('market.thank-you', ['orderNumbers' => session('orders', [])]);
    }
}
