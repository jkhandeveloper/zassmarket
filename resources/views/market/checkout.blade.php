@extends('market.layout', ['title' => 'Checkout'])

@section('content')
    <section class="zm-container grid gap-8 py-10 lg:grid-cols-[1fr_380px]">
        <form method="POST" action="{{ route('checkout.store') }}" class="zm-card p-6 sm:p-8">
            @csrf
            <p class="zm-pill">Secure order</p>
            <h1 class="mt-3 text-3xl font-black">Checkout</h1>
            <div class="mt-6 grid gap-4">
                <label class="grid gap-1 text-sm font-medium">Name
                    <input name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                </label>
                <label class="grid gap-1 text-sm font-medium">Email
                    <input name="customer_email" type="email" value="{{ old('customer_email', $user?->email) }}" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                </label>
                <label class="grid gap-1 text-sm font-medium">Phone
                    <input name="customer_phone" value="{{ old('customer_phone') }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                </label>
                <label class="grid gap-1 text-sm font-medium">Shipping address
                    <textarea name="shipping_address" rows="5" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">{{ old('shipping_address') }}</textarea>
                </label>
                <div class="grid gap-3">
                    <p class="text-sm font-black">Payment method</p>
                    @if ($stripeEnabled)
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-zass-linen bg-zass-cream/70 p-4 transition hover:border-zass-bark">
                            <input type="radio" name="payment_method" value="card" @checked(old('payment_method', 'card') === 'card') class="mt-1 border-zass-stone text-zass-bark focus:ring-zass-caramel">
                            <span>
                                <span class="flex items-center gap-2 font-black">
                                    <svg class="h-5 w-5 text-zass-bark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="5" width="20" height="14" rx="2" />
                                        <path d="M2 10h20" />
                                    </svg>
                                    Pay by card
                                </span>
                                <span class="mt-1 block text-sm text-zass-bark/75">Secure Stripe checkout. Works for guest and registered customers.</span>
                            </span>
                        </label>
                    @endif
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-zass-linen bg-white p-4 transition hover:border-zass-bark">
                        <input type="radio" name="payment_method" value="cod" @checked(old('payment_method', $stripeEnabled ? 'card' : 'cod') === 'cod') class="mt-1 border-zass-stone text-zass-bark focus:ring-zass-caramel">
                        <span>
                            <span class="flex items-center gap-2 font-black">
                                <svg class="h-5 w-5 text-zass-sage" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 2v20" />
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                                </svg>
                                Cash on delivery
                            </span>
                            <span class="mt-1 block text-sm text-zass-bark/75">Place the order now and pay when it arrives.</span>
                        </span>
                    </label>
                </div>
            </div>
            <button class="zm-btn-primary mt-6">Place order</button>
        </form>
        <aside class="zm-card h-fit p-6">
            <h2 class="font-black">Order summary</h2>
            <div class="mt-4 space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between gap-4 text-sm">
                        <span>{{ $item['product']->name }} x {{ $item['quantity'] }}</span>
                        <span>${{ number_format($item['subtotal_cents'] / 100, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex justify-between border-t border-zass-linen pt-4 font-black">
                <span>Total</span>
                <span class="text-zass-bark">${{ number_format($totalCents / 100, 2) }}</span>
            </div>
            <p class="mt-4 rounded-md bg-zass-cream p-3 text-xs font-semibold leading-5 text-zass-bark/75">
                Card payments can be enabled or disabled with <span class="font-black">STRIPE_ENABLED</span> in your environment.
            </p>
        </aside>
    </section>
@endsection
