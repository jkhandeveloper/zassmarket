@extends('market.layout', ['title' => 'Customer dashboard'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Customer dashboard</h1>
                <p class="mt-1 text-zinc-600">Orders, wishlist, and account shortcuts.</p>
            </div>
            <a href="{{ route('vendor.apply') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-sm font-semibold">Open a vendor store</a>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-zinc-200 bg-white p-6">
                <h2 class="font-semibold">Recent orders</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($orders as $order)
                        <div class="flex justify-between gap-4 border-b border-zinc-100 pb-3 text-sm last:border-0">
                            <span>{{ $order->order_number }} from {{ $order->vendorStore->name }}</span>
                            <span class="font-semibold">{{ $order->formattedTotal() }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-600">No orders yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold">Wishlist</h2>
                    <a href="{{ route('customer.wishlist') }}" class="text-sm font-semibold">View all</a>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($wishlist as $item)
                        <a href="{{ route('products.show', $item->product) }}" class="block border-b border-zinc-100 pb-3 text-sm last:border-0">
                            {{ $item->product->name }} <span class="text-zinc-500">by {{ $item->product->vendorStore->name }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-zinc-600">No wishlist items yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
