@extends('market.layout', ['title' => 'Wishlist'])

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Wishlist</h1>
        <div class="mt-6 space-y-4">
            @forelse ($items as $item)
                <div class="flex items-center justify-between rounded-lg border border-zinc-200 bg-white p-4">
                    <a href="{{ route('products.show', $item->product) }}" class="font-semibold">{{ $item->product->name }}</a>
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
