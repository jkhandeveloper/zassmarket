@extends('market.layout', ['title' => 'Vendor products'])

@section('content')
    <section class="zm-container py-8">
        <div class="flex items-end justify-between">
            <div>
                <h1 class="text-3xl font-bold">Products</h1>
                <p class="mt-1 text-zinc-600">{{ $store->products()->count() }} of {{ $store->productLimit() }} products used.</p>
            </div>
            <a href="{{ route('vendor.products.create') }}" class="rounded-md bg-zinc-950 px-4 py-3 text-sm font-semibold text-white">New product</a>
        </div>
        <div class="mt-6 overflow-hidden rounded-lg border border-zinc-200 bg-white">
            @forelse ($products as $product)
                <div class="flex items-center justify-between border-b border-zinc-100 p-4 last:border-0">
                    <div>
                        <p class="font-semibold">{{ $product->name }}</p>
                        <p class="text-sm text-zinc-600">{{ $product->category?->name ?? 'Uncategorized' }} - {{ $product->formattedPrice() }}</p>
                    </div>
                    <a href="{{ route('vendor.products.edit', $product) }}" class="text-sm font-semibold">Edit</a>
                </div>
            @empty
                <p class="p-6 text-zinc-600">No products yet.</p>
            @endforelse
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    </section>
@endsection
