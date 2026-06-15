@extends('market.layout', ['title' => 'Wishlist'])

@section('content')
    <section class="zm-container py-8">
        <h1 class="text-3xl font-bold">Wishlist</h1>
        <div class="mt-6 space-y-4">
            @forelse ($items as $item)
                <div class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200 bg-white p-4">
                    <a href="{{ route('products.show', $item->product) }}" class="flex min-w-0 items-center gap-3 font-semibold">
                        @include('market.partials.product-thumb', ['product' => $item->product, 'size' => 'sm'])
                        <span class="min-w-0">
                            <span class="block truncate">{{ $item->product->name }}</span>
                            <span class="block text-sm font-medium text-zinc-500">{{ $item->product->vendorStore->name }}</span>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('wishlist.toggle', $item->product) }}">
                        @csrf
                        <button class="text-sm font-semibold text-red-700">Remove</button>
                    </form>
                </div>
            @empty
                <p class="rounded-lg border border-zinc-200 bg-white p-6 text-zinc-600">No wishlist items yet.</p>
            @endforelse
        </div>
    </section>
@endsection
