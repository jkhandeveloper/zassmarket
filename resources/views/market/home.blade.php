@extends('market.layout', ['title' => 'ZassMarket'])

@section('content')
    @php
        $slides = $featuredProducts->take(4)->values();
        $stats = [
            ['label' => 'approved vendors', 'value' => '30+'],
            ['label' => 'curated categories', 'value' => $categories->count().'+'],
            ['label' => 'fast checkout', 'value' => '24/7'],
        ];
    @endphp

    <section class="relative">
        <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white/80 to-transparent"></div>
        <div class="zm-container grid gap-10 py-10 lg:grid-cols-[1fr_520px] lg:py-16">
            <div class="flex flex-col justify-center">
                <div class="zm-pill animate-fade-up">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20" />
                        <path d="m17 5-5-3-5 3" />
                        <path d="m17 19-5 3-5-3" />
                    </svg>
                    Multi-vendor SaaS marketplace
                </div>
                <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-tight text-zass-ink sm:text-5xl lg:text-6xl">
                    Shop warm essentials from approved independent sellers.
                </h1>
                <p class="mt-5 max-w-2xl text-lg leading-8 text-zass-bark/80">
                    Discover crafted goods, compare vendors, save favorites, and check out as a guest or registered customer in a marketplace built for real ecommerce workflows.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('products.index') }}" class="zm-btn-primary">
                        <svg class="zm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                        Browse products
                    </a>
                    <a href="{{ route('vendor.apply') }}" class="zm-btn-secondary">
                        <svg class="zm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 21h18" />
                            <path d="M5 21V7l8-4v18" />
                            <path d="M19 21V11l-6-4" />
                        </svg>
                        Become a vendor
                    </a>
                </div>
                <div class="mt-10 grid max-w-xl grid-cols-3 gap-3">
                    @foreach ($stats as $stat)
                        <div class="rounded-lg border border-zass-linen/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                            <p class="text-2xl font-black text-zass-bark">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wide text-zass-sage">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div
                x-data="{ active: 0, total: {{ max($slides->count(), 1) }} }"
                x-init="setInterval(() => active = (active + 1) % total, 4500)"
                class="relative min-h-[520px]"
            >
                <div class="absolute right-4 top-4 z-20 rounded-full bg-white/85 px-4 py-2 text-xs font-black uppercase tracking-wide text-zass-bark shadow-soft backdrop-blur">
                    New season picks
                </div>
                <div class="relative h-full overflow-hidden rounded-lg border border-zass-linen bg-zass-ink shadow-lift">
                    @forelse ($slides as $index => $product)
                        <a
                            href="{{ route('products.show', $product) }}"
                            x-show="active === {{ $index }}"
                            x-transition.opacity.duration.500ms
                            class="absolute inset-0"
                        >
                            @if ($product->images->first())
                                <img src="{{ $product->images->first()->path }}" alt="{{ $product->name }}" class="h-full w-full object-cover opacity-90">
                            @else
                                <div class="h-full w-full bg-zass-stone"></div>
                            @endif
                            <span class="absolute inset-0 bg-gradient-to-t from-zass-ink via-zass-ink/25 to-transparent"></span>
                            <span class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                <span class="block text-sm font-bold text-zass-linen">{{ $product->vendorStore->name }}</span>
                                <span class="mt-2 block text-3xl font-black">{{ $product->name }}</span>
                                <span class="mt-3 inline-flex rounded-full bg-white px-4 py-2 text-sm font-black text-zass-bark">{{ $product->formattedPrice() }}</span>
                            </span>
                        </a>
                    @empty
                        <div class="absolute inset-0 grid place-items-center text-zass-linen">Add products to power the homepage slider.</div>
                    @endforelse
                </div>
                <div class="absolute -bottom-5 left-8 right-8 z-20 flex items-center justify-between rounded-lg border border-zass-linen bg-white/90 p-3 shadow-soft backdrop-blur">
                    <div class="flex gap-2">
                        @foreach ($slides as $index => $product)
                            <button @click="active = {{ $index }}" class="h-2.5 rounded-full transition-all" :class="active === {{ $index }} ? 'w-9 bg-zass-bark' : 'w-2.5 bg-zass-stone/70'" title="Show slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <button @click="active = (active - 1 + total) % total" class="grid h-9 w-9 place-items-center rounded-md bg-zass-cream text-zass-bark transition hover:bg-zass-linen" title="Previous slide">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" /></svg>
                        </button>
                        <button @click="active = (active + 1) % total" class="grid h-9 w-9 place-items-center rounded-md bg-zass-bark text-white transition hover:bg-zass-ink" title="Next slide">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="zm-container py-10">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ([['Secure checkout', 'Guest and registered orders with vendor-aware routing.'], ['Vendor limits', 'Plan-based product and monthly order controls.'], ['AI suggestions', 'FastAPI-backed product copy suggestions for vendors.']] as $item)
                <div class="zm-card flex gap-4 p-5 transition hover:-translate-y-1 hover:shadow-lift">
                    <span class="grid h-12 w-12 place-items-center rounded-md bg-zass-sage text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 7 10 17l-5-5" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="font-black">{{ $item[0] }}</h3>
                        <p class="mt-1 text-sm leading-6 text-zass-bark/75">{{ $item[1] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="zm-container py-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="zm-pill">Shop by category</p>
                <h2 class="mt-3 text-3xl font-black">Curated shelves</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-black text-zass-bark hover:text-zass-ink">View all products</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group rounded-lg border border-zass-linen/80 bg-gradient-to-br from-white to-zass-cream p-6 shadow-soft transition hover:-translate-y-1 hover:shadow-lift">
                    <span class="grid h-12 w-12 place-items-center rounded-md bg-zass-bark text-white transition group-hover:bg-zass-sage">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                        </svg>
                    </span>
                    <h3 class="mt-5 text-lg font-black">{{ $category->name }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zass-sage">{{ $category->products_count ?? $category->products()->count() }} products</p>
                </a>
            @endforeach
        </div>
    </section>

    <section class="zm-container py-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="zm-pill">Featured inventory</p>
                <h2 class="mt-3 text-3xl font-black">Fresh from approved vendors</h2>
            </div>
            <a href="{{ route('products.index') }}" class="zm-btn-secondary py-2">View all</a>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredProducts as $product)
                @include('market.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </section>

    @if ($recommendedProducts->isNotEmpty())
        <section class="zm-container py-10">
            <div class="rounded-lg border border-zass-linen bg-white/85 p-6 shadow-soft backdrop-blur sm:p-8">
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="zm-pill">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20" />
                                <path d="m19 9-7-7-7 7" />
                            </svg>
                            AI picks
                        </p>
                        <h2 class="mt-3 text-3xl font-black">Suggested for your next cart</h2>
                        <p class="mt-2 text-sm font-semibold text-zass-bark/70">Powered by your Python AI service with a local marketplace fallback.</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-black text-zass-bark hover:text-zass-ink">Explore more</a>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($recommendedProducts as $product)
                        @include('market.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="my-10 bg-zass-ink text-white">
        <div class="zm-container grid gap-8 py-12 lg:grid-cols-[.9fr_1.1fr]">
            <div>
                <p class="text-sm font-black uppercase tracking-wide text-zass-linen">Vendor commerce</p>
                <h2 class="mt-3 text-3xl font-black">Launch a storefront with controls already built in.</h2>
                <p class="mt-4 max-w-xl leading-7 text-zass-linen">Vendor approval, subscription tiers, product caps, order limits, AI product suggestions, and order dashboards are ready from day one.</p>
                <a href="{{ route('vendor.apply') }}" class="mt-6 inline-flex rounded-md bg-white px-5 py-3 text-sm font-black text-zass-bark transition hover:bg-zass-linen">Start selling</a>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="rounded-lg border border-white/15 bg-white/10 p-5 backdrop-blur">
                        <h3 class="font-black">{{ $plan->name }}</h3>
                        <p class="mt-3 text-3xl font-black text-zass-linen">{{ $plan->formattedPrice() }}</p>
                        <p class="mt-3 text-sm leading-6 text-zass-linen">{{ $plan->product_limit }} products and {{ $plan->monthly_order_limit }} orders monthly.</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="zm-container pb-14 pt-6">
        <div class="rounded-lg border border-zass-linen bg-gradient-to-r from-zass-bark via-zass-caramel to-zass-sage p-1 shadow-lift">
            <div class="grid gap-6 rounded-[7px] bg-white/95 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-zass-sage">Marketplace notes</p>
                    <h2 class="mt-2 text-2xl font-black">Find new arrivals, save wishlist picks, and return when you are ready.</h2>
                </div>
                <a href="{{ route('products.index') }}" class="zm-btn-primary">Explore the catalog</a>
            </div>
        </div>
    </section>
@endsection
