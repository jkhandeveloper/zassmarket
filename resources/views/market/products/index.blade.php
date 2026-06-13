@extends('market.layout', ['title' => 'Products'])

@section('content')
    <section class="zm-container py-10">
        <div class="mb-7 rounded-lg border border-zass-linen/80 bg-white/80 p-6 shadow-soft backdrop-blur md:p-8">
            <p class="zm-pill">Marketplace catalog</p>
            <div class="mt-4 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-black sm:text-4xl">Products</h1>
                    <p class="mt-2 max-w-xl text-zass-bark/75">Browse fresh inventory from approved sellers with vendor-aware checkout.</p>
                </div>
                <form class="grid gap-2 sm:grid-cols-[1fr_180px_auto]" method="GET">
                    <input name="q" value="{{ request('q') }}" placeholder="Search products" class="rounded-md border-zass-linen bg-white text-sm focus:border-zass-caramel focus:ring-zass-caramel">
                    <select name="category" class="rounded-md border-zass-linen bg-white text-sm focus:border-zass-caramel focus:ring-zass-caramel">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button class="zm-btn-primary py-2.5">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        Filter
                    </button>
                </form>
            </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($products as $product)
                @include('market.partials.product-card', ['product' => $product])
            @empty
                <p class="zm-card p-6 text-zass-bark/75">No products match your filters.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $products->links() }}</div>
    </section>
@endsection
