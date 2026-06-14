@extends('market.layout', ['title' => 'Cart'])

@section('content')
    <section class="zm-container py-10">
        <p class="zm-pill">Checkout bag</p>
        <h1 class="mt-3 text-3xl font-black">Cart</h1>
        <div class="mt-6 space-y-4">
            @forelse ($items as $item)
                <div class="zm-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-black">{{ $item['product']->name }}</h2>
                        <p class="text-sm font-semibold text-zass-sage">{{ $item['product']->vendorStore->name }} - {{ $item['product']->formattedPrice() }}</p>
                        @if ($item['product']->hasDiscount())
                            <p class="mt-1 text-xs font-black text-zass-caramel">
                                {{ $item['product']->discount_percent }}% off <span class="text-zass-stone line-through">{{ $item['product']->formattedOriginalPrice() }}</span>
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input name="quantity" type="number" min="0" max="{{ $item['product']->stock }}" value="{{ $item['quantity'] }}" class="w-20 rounded-md border-zass-linen text-sm focus:border-zass-caramel focus:ring-zass-caramel">
                            <button class="zm-btn-secondary px-3 py-2">Update</button>
                        </form>
                        <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm font-black text-red-700">Remove</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="zm-card p-6 text-zass-bark/75">Your cart is empty.</p>
            @endforelse
        </div>
        <div class="zm-card mt-6 flex items-center justify-between p-5">
            <span class="font-black">Total</span>
            <span class="text-2xl font-black text-zass-bark">${{ number_format($totalCents / 100, 2) }}</span>
        </div>
        @if ($items->isNotEmpty())
            <a href="{{ route('checkout.create') }}" class="zm-btn-primary mt-4">Checkout</a>
        @endif
    </section>
@endsection
