@extends('market.layout', ['title' => 'Vendor orders'])

@section('content')
    <section class="zm-container py-8">
        <h1 class="text-3xl font-bold">Orders</h1>
        <div class="mt-6 space-y-4">
            @forelse ($orders as $order)
                <article class="rounded-lg border border-zinc-200 bg-white p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-semibold">{{ $order->order_number }}</h2>
                            <p class="text-sm text-zinc-600">{{ $order->customer_name }} - {{ $order->customer_email }}</p>
                        </div>
                        <p class="text-lg font-bold">{{ $order->formattedTotal() }}</p>
                    </div>
                    <div class="mt-4 border-t border-zinc-100 pt-4 text-sm">
                        @foreach ($order->items as $item)
                            <p>{{ $item->product_name }} x {{ $item->quantity }}</p>
                        @endforeach
                    </div>
                </article>
            @empty
                <p class="rounded-lg border border-zinc-200 bg-white p-6 text-zinc-600">No orders yet.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </section>
@endsection
