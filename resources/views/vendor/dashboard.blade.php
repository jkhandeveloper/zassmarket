@extends('market.layout', ['title' => 'Vendor dashboard'])

@section('content')
    <section class="zm-container py-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold">Vendor dashboard</h1>
                <p class="mt-1 text-zinc-600">{{ $store?->name ?? 'No store yet' }}</p>
            </div>
            @if ($store?->isApproved())
                <div class="flex gap-3">
                    <a href="{{ route('vendor.products.create') }}" class="rounded-md bg-zinc-950 px-4 py-3 text-sm font-semibold text-white">New product</a>
                    <a href="{{ route('vendor.orders.index') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-sm font-semibold">Orders</a>
                    <a href="{{ route('vendor.billing.index') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-sm font-semibold">Billing</a>
                </div>
            @elseif ($store)
                <a href="{{ route('vendor.billing.index') }}" class="rounded-md bg-zass-bark px-4 py-3 text-sm font-semibold text-white">Complete billing</a>
            @endif
        </div>

        @if (! $store)
            <div class="mt-8 rounded-lg border border-zinc-200 bg-white p-6">
                <p class="text-zinc-700">Start by submitting your vendor application.</p>
                <a href="{{ route('vendor.apply') }}" class="mt-4 inline-flex rounded-md bg-zinc-950 px-4 py-3 text-sm font-semibold text-white">Apply</a>
            </div>
        @elseif (! $store->isApproved())
            <div class="mt-8 rounded-lg border border-amber-200 bg-amber-50 p-6 text-amber-900">
                Your vendor application is {{ $store->status }}. Admin approval is required before product and order tools unlock.
            </div>
        @else
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <p class="text-sm text-zinc-600">Products</p>
                    <p class="mt-2 text-3xl font-bold">{{ $store->products()->count() }} / {{ $store->productLimit() }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <p class="text-sm text-zinc-600">Orders this month</p>
                    <p class="mt-2 text-3xl font-bold">{{ $store->monthlyOrdersCount() }} / {{ $store->monthlyOrderLimit() }}</p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <p class="text-sm text-zinc-600">Plan</p>
                    <p class="mt-2 text-3xl font-bold">{{ $store->plan?->name }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg border border-zinc-200 bg-white p-6">
                    <h2 class="font-semibold">Latest products</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($products as $product)
                            <a href="{{ route('vendor.products.edit', $product) }}" class="flex items-center gap-3 border-b border-zinc-100 pb-3 text-sm last:border-0">
                                @include('market.partials.product-thumb', ['product' => $product, 'size' => 'sm'])
                                <span class="min-w-0">
                                    <span class="block truncate font-semibold text-zass-ink">{{ $product->name }}</span>
                                    <span class="text-zinc-500">{{ $product->formattedPrice() }}</span>
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-zinc-600">No products yet.</p>
                        @endforelse
                    </div>
                </section>
                <section class="rounded-lg border border-zinc-200 bg-white p-6">
                    <h2 class="font-semibold">Latest orders</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($orders as $order)
                            <div class="flex justify-between border-b border-zinc-100 pb-3 text-sm last:border-0">
                                <span>{{ $order->order_number }}</span>
                                <span class="font-semibold">{{ $order->formattedTotal() }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-600">No orders yet.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </section>
@endsection
