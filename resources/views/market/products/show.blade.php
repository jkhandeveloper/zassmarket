@extends('market.layout', [
    'title' => $product->seo_title ?: $product->name,
    'metaDescription' => $product->seo_description ?: str($product->description)->limit(155),
])

@section('content')
    <section class="zm-container grid gap-8 py-8 lg:grid-cols-[1.05fr_.95fr]">
        <div
            x-data="{ activeImage: 0, lightboxOpen: false, totalImages: {{ max($product->images->count(), 1) }} }"
            @keydown.window.escape="lightboxOpen = false"
            @keydown.window.arrow-left="if (lightboxOpen) activeImage = (activeImage - 1 + totalImages) % totalImages"
            @keydown.window.arrow-right="if (lightboxOpen) activeImage = (activeImage + 1) % totalImages"
            class="overflow-hidden rounded-lg border border-zass-linen bg-zass-linen/35 shadow-lift"
        >
            @if ($product->images->isNotEmpty())
                <div class="relative h-[min(72svh,720px)] min-h-[360px] bg-zass-ink/5 lg:min-h-[560px]">
                    @foreach ($product->images as $index => $image)
                        <button
                            type="button"
                            @click="activeImage = {{ $index }}; lightboxOpen = true"
                            x-show="activeImage === {{ $index }}"
                            x-transition.opacity.duration.300ms
                            class="absolute inset-0 block h-full w-full cursor-zoom-in bg-zass-linen/25"
                            title="View image full size"
                        >
                            <img
                                src="{{ $image->path }}"
                                alt="{{ $image->alt_text ?? $product->name }}"
                                class="h-full w-full object-cover"
                            >
                        </button>
                    @endforeach

                    @if ($product->images->count() > 1)
                        <button type="button" @click="activeImage = (activeImage - 1 + totalImages) % totalImages" class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-md bg-white/85 text-zass-bark shadow-soft backdrop-blur transition hover:bg-white" title="Previous image">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" /></svg>
                        </button>
                        <button type="button" @click="activeImage = (activeImage + 1) % totalImages" class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-md bg-white/85 text-zass-bark shadow-soft backdrop-blur transition hover:bg-white" title="Next image">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                        </button>
                    @endif

                    <span class="absolute bottom-4 right-4 rounded-full bg-zass-ink/75 px-3 py-1.5 text-xs font-black text-white shadow-soft backdrop-blur">
                        Click image to view full size
                    </span>
                </div>

                @if ($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2 bg-white/80 p-3 sm:grid-cols-6">
                        @foreach ($product->images as $index => $image)
                            <button type="button" @click="activeImage = {{ $index }}" class="overflow-hidden rounded-md border transition" :class="activeImage === {{ $index }} ? 'border-zass-bark ring-2 ring-zass-caramel/40' : 'border-zass-linen hover:border-zass-caramel'" title="Show image {{ $index + 1 }}">
                                <img src="{{ $image->path }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-16 w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <div
                    x-cloak
                    x-show="lightboxOpen"
                    x-transition.opacity.duration.200ms
                    class="fixed inset-0 z-50 bg-zass-ink/95 p-4 text-white sm:p-6"
                    role="dialog"
                    aria-modal="true"
                >
                    <button type="button" @click="lightboxOpen = false" class="absolute right-4 top-4 z-10 grid h-11 w-11 place-items-center rounded-md bg-white/10 text-white backdrop-blur transition hover:bg-white/20" title="Close image viewer">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                    <div class="flex h-full items-center justify-center">
                        @foreach ($product->images as $index => $image)
                            <img
                                src="{{ $image->path }}"
                                alt="{{ $image->alt_text ?? $product->name }}"
                                x-show="activeImage === {{ $index }}"
                                x-transition.opacity.duration.200ms
                                class="max-h-full max-w-full object-contain"
                            >
                        @endforeach
                    </div>

                    @if ($product->images->count() > 1)
                        <button type="button" @click="activeImage = (activeImage - 1 + totalImages) % totalImages" class="absolute left-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-md bg-white/10 text-white backdrop-blur transition hover:bg-white/20" title="Previous image">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" /></svg>
                        </button>
                        <button type="button" @click="activeImage = (activeImage + 1) % totalImages" class="absolute right-4 top-1/2 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-md bg-white/10 text-white backdrop-blur transition hover:bg-white/20" title="Next image">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                        </button>

                        <div class="absolute bottom-4 left-1/2 flex max-w-[calc(100%-2rem)] -translate-x-1/2 gap-2 overflow-x-auto rounded-lg bg-black/30 p-2 backdrop-blur">
                            @foreach ($product->images as $index => $image)
                                <button type="button" @click="activeImage = {{ $index }}" class="h-16 w-16 shrink-0 overflow-hidden rounded-md border transition" :class="activeImage === {{ $index }} ? 'border-white ring-2 ring-zass-caramel' : 'border-white/25 hover:border-white/70'" title="Show image {{ $index + 1 }}">
                                    <img src="{{ $image->path }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="flex h-[min(72svh,720px)] min-h-[360px] items-center justify-center font-semibold text-zass-stone lg:min-h-[560px]">No image</div>
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
                Sold by <a href="{{ route('vendors.show', $product->vendorStore->slug) }}" class="text-zass-bark hover:text-zass-ink">{{ $product->vendorStore->name }}</a>
            </p>
            <div class="mt-5">
                <div class="rounded-md border border-zass-linen bg-zass-cream/70 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-zass-sage">Product rating</p>
                    <p class="mt-1 text-2xl font-black text-zass-bark">{{ $product->ratingLabel() }}</p>
                    <p class="text-sm font-semibold text-zass-bark/70">{{ $product->reviews_count }} review{{ $product->reviews_count === 1 ? '' : 's' }}</p>
                </div>
            </div>
            <div class="mt-7 flex flex-wrap items-end gap-3">
                <p class="text-4xl font-black text-zass-bark">{{ $product->formattedPrice() }}</p>
                @if ($product->hasDiscount())
                    <p class="pb-1 text-lg font-black text-zass-stone line-through">{{ $product->formattedOriginalPrice() }}</p>
                    <span class="mb-1 rounded-md bg-zass-caramel/20 px-2.5 py-1 text-sm font-black text-zass-bark">{{ $product->discount_percent }}% off</span>
                @endif
            </div>
            <p class="mt-6 whitespace-pre-line leading-8 text-zass-bark/80">{{ $product->description }}</p>
            @if ($product->stock > 0)
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
            @else
                <div class="mt-8 rounded-md border border-zass-linen bg-zass-linen/45 p-4 text-sm font-bold text-zass-bark/75">
                    This product is visible in the catalog, but it is currently out of stock.
                </div>
            @endif
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

    @if ($sameNameProducts->isNotEmpty())
        <section class="zm-container pb-12">
            <div class="mb-6">
                <p class="zm-pill">Same product name</p>
                <h2 class="mt-3 text-3xl font-black">More listings named {{ $product->name }}</h2>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($sameNameProducts as $sameNameProduct)
                    @include('market.partials.product-card', ['product' => $sameNameProduct])
                @endforeach
            </div>
        </section>
    @endif

    @if ($sameCategoryProducts->isNotEmpty())
        <section class="zm-container pb-12">
            <div class="mb-6">
                <p class="zm-pill">Same category</p>
                <h2 class="mt-3 text-3xl font-black">More from {{ $product->category?->name ?? 'this category' }}</h2>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($sameCategoryProducts as $sameCategoryProduct)
                    @include('market.partials.product-card', ['product' => $sameCategoryProduct])
                @endforeach
            </div>
        </section>
    @endif

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

    <section class="zm-container pb-14">
        <div>
            <div class="mb-4">
                <p class="zm-pill">Product reviews</p>
                <h2 class="mt-3 text-2xl font-black">What customers and vendors say about this product</h2>
            </div>

            @auth
                <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="rounded-md border border-zass-linen bg-white/85 p-5 shadow-soft">
                    @csrf
                    <div class="grid gap-4">
                        <div class="grid gap-2 text-sm font-bold">
                            <span>Rating</span>
                            @include('market.partials.rating-input', ['id' => 'product-rating'])
                        </div>
                        <label class="grid gap-1 text-sm font-bold">Title
                            <input name="title" value="{{ old('title') }}" maxlength="120" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                        </label>
                        <label class="grid gap-1 text-sm font-bold">Review
                            <textarea name="body" rows="4" maxlength="1500" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">{{ old('body') }}</textarea>
                        </label>
                    </div>
                    <button class="zm-btn-primary mt-4">Save product review</button>
                </form>
            @else
                <p class="rounded-md border border-zass-linen bg-white/80 p-5 text-sm font-semibold text-zass-bark/75">
                    <a href="{{ route('login') }}" class="font-black text-zass-bark hover:text-zass-ink">Log in</a> to rate and review this product.
                </p>
            @endauth

            <div class="mt-5 space-y-4">
                @forelse ($productReviews as $review)
                    <article class="rounded-md border border-zass-linen bg-white/85 p-5 shadow-soft">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-black">{{ $review->title ?: 'Product review' }}</p>
                                <p class="mt-1 text-sm font-semibold text-zass-sage">{{ $review->user?->name ?? 'Customer' }}</p>
                            </div>
                            <span class="rounded-md bg-zass-caramel/20 px-2.5 py-1 text-sm font-black text-zass-bark">{{ $review->rating }}/5</span>
                        </div>
                        @if ($review->body)
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-zass-bark/80">{{ $review->body }}</p>
                        @endif
                    </article>
                @empty
                    <p class="rounded-md border border-zass-linen bg-white/80 p-5 text-sm text-zass-bark/75">No product reviews yet.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
