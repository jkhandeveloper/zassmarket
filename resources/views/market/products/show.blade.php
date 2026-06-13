@extends('market.layout', [
    'title' => $product->seo_title ?: $product->name,
    'metaDescription' => $product->seo_description ?: str($product->description)->limit(155),
])

@section('content')
    <section class="zm-container grid gap-8 py-10 lg:grid-cols-[1.05fr_.95fr]">
        <div class="overflow-hidden rounded-lg border border-zass-linen bg-zass-linen/35 shadow-lift">
            @if ($product->images->first())
                <img src="{{ $product->images->first()->path }}" alt="{{ $product->name }}" class="h-full min-h-96 w-full object-cover">
            @else
                <div class="flex min-h-96 items-center justify-center font-semibold text-zass-stone">No image</div>
            @endif
        </div>
        <div class="zm-card p-6 sm:p-8">
            <p class="zm-pill">{{ $product->category?->name ?? 'Marketplace' }}</p>
            <h1 class="mt-4 text-4xl font-black tracking-tight">{{ $product->name }}</h1>
            <p class="mt-3 flex items-center gap-2 font-semibold text-zass-sage">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M5 21V7l8-4v18" />
                    <path d="M19 21V11l-6-4" />
                </svg>
                Sold by {{ $product->vendorStore->name }}
            </p>
            <p class="mt-7 text-4xl font-black text-zass-bark">{{ $product->formattedPrice() }}</p>
            <p class="mt-6 whitespace-pre-line leading-8 text-zass-bark/80">{{ $product->description }}</p>
            <form method="POST" action="{{ route('cart.store', $product) }}" class="mt-8 flex gap-3">
                @csrf
                <input name="quantity" type="number" min="1" max="{{ $product->stock }}" value="1" class="w-24 rounded-md border-zass-linen bg-white focus:border-zass-caramel focus:ring-zass-caramel">
                <button class="zm-btn-primary">
                    <svg class="zm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="8" cy="21" r="1" />
                        <circle cx="19" cy="21" r="1" />
                        <path d="M2 2h3l3 14h11l2-9H7" />
                    </svg>
                    Add to cart
                </button>
            </form>
            @auth
                <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="mt-3">
                    @csrf
                    <button class="inline-flex items-center gap-2 text-sm font-black text-zass-bark hover:text-zass-ink">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" />
                        </svg>
                        Toggle wishlist
                    </button>
                </form>
            @endauth
        </div>
    </section>

    @if ($recommendedProducts->isNotEmpty())
        <section class="zm-container pb-12">
            <div class="mb-6">
                <p class="zm-pill">AI recommendations</p>
                <h2 class="mt-3 text-3xl font-black">Customers also discover</h2>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($recommendedProducts as $recommendedProduct)
                    @include('market.partials.product-card', ['product' => $recommendedProduct])
                @endforeach
            </div>
        </section>
    @endif
@endsection
