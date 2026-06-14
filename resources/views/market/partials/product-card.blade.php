<article class="group zm-card overflow-hidden transition duration-300 hover:-translate-y-1 hover:shadow-lift">
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/3] overflow-hidden bg-zass-linen/35">
        @if ($product->images->first())
            <img src="{{ $product->images->first()->path }}" alt="{{ $product->images->first()->alt_text ?? $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center text-sm font-semibold text-zass-stone">No image</div>
        @endif
        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-zass-bark shadow-sm">{{ $product->category?->name ?? 'Market' }}</span>
        <span class="absolute inset-y-0 left-0 w-1/2 -translate-x-full bg-gradient-to-r from-transparent via-white/35 to-transparent opacity-0 transition group-hover:animate-shimmer group-hover:opacity-100"></span>
    </a>
    <div class="space-y-4 p-4">
        <div>
            <a href="{{ route('products.show', $product) }}" class="font-black text-zass-ink transition hover:text-zass-bark">{{ $product->name }}</a>
            <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-zass-sage">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 21h18" />
                    <path d="M5 21V7l8-4v18" />
                    <path d="M19 21V11l-6-4" />
                </svg>
                <a href="{{ route('vendors.show', $product->vendorStore->slug) }}" class="hover:text-zass-bark">{{ $product->vendorStore->name }}</a>
            </p>
            <p class="mt-2 text-xs font-bold text-zass-caramel">
                {{ $product->reviews_count ? number_format((float) $product->reviews_avg_rating, 1).' rating from '.$product->reviews_count.' review'.($product->reviews_count === 1 ? '' : 's') : 'No ratings yet' }}
            </p>
        </div>
        <div class="flex items-center justify-between">
            <span>
                <span class="block text-lg font-black text-zass-bark">{{ $product->formattedPrice() }}</span>
                @if ($product->hasDiscount())
                    <span class="text-xs font-black text-zass-stone line-through">{{ $product->formattedOriginalPrice() }}</span>
                    <span class="ml-1 text-xs font-black text-zass-caramel">{{ $product->discount_percent }}% off</span>
                @endif
            </span>
            @if ($product->stock > 0)
                <form method="POST" action="{{ route('cart.store', $product) }}">
                    @csrf
                    <button class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-zass-bark text-white shadow-sm transition hover:bg-zass-ink" title="Add to cart">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                    </button>
                </form>
            @else
                <span class="rounded-md bg-zass-linen px-3 py-2 text-xs font-black text-zass-bark/70">Out of stock</span>
            @endif
        </div>
    </div>
</article>
