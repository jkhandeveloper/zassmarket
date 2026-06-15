@extends('market.layout', ['title' => 'ZassMarket'])

@section('content')
    <style>
        .zm-hero-content-grid {
            display: grid;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .zm-hero-content-grid {
                align-items: end;
                grid-template-columns: minmax(0, 1fr) 420px;
            }
        }

        @media (max-width: 1023px) {
            .zm-hero-vendor-panel {
                display: none;
            }
        }
    </style>

    @php
        $slides = $heroImages->values();
        $stats = [
            ['label' => 'approved vendors', 'value' => '30+'],
            ['label' => 'curated categories', 'value' => $categories->count().'+'],
            ['label' => 'fast checkout', 'value' => '24/7'],
        ];
    @endphp

    <section
        x-data="{ active: 0, total: {{ max($slides->count(), 1) }} }"
        x-init="setInterval(() => active = (active + 1) % total, 6000)"
        class="relative min-h-[min(720px,calc(84svh-73px))] overflow-hidden bg-zass-ink text-white"
        style="min-height: min(720px, calc(84svh - 73px));"
    >
        @forelse ($slides as $index => $heroImage)
            @php
                $product = $heroImage->product;
                $bundleItems = $featuredProducts
                    ->reject(fn ($bundleProduct) => $bundleProduct->id === $product->id)
                    ->take(2)
                    ->values();
            @endphp
            <article
                x-show="active === {{ $index }}"
                x-transition.opacity.duration.700ms
                class="absolute inset-0"
            >
                <img src="{{ $heroImage->path }}" alt="{{ $heroImage->alt_text ?? $product->name }}" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-zass-ink/90 via-zass-ink/58 to-zass-ink/10"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-zass-ink/88 via-transparent to-zass-ink/20"></div>

                <div class="relative z-10 flex min-h-[min(720px,calc(84svh-73px))] flex-col justify-end px-[clamp(1rem,4vw,4rem)] py-8 sm:py-12" style="min-height: min(720px, calc(84svh - 73px));">
                    <div class="zm-hero-content-grid">
                        <div class="max-w-5xl">
                            <div class="inline-flex flex-wrap items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-wide text-zass-linen backdrop-blur">
                                <span>{{ $product->category?->name ?? 'Marketplace pick' }}</span>
                                <span class="h-1 w-1 rounded-full bg-zass-linen"></span>
                                <span>{{ $product->vendorStore->name }}</span>
                            </div>
                            <h1 class="mt-5 text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">{{ $product->name }}</h1>
                            <p class="mt-5 max-w-3xl text-base leading-8 text-zass-linen sm:text-lg">{{ str($product->description)->limit(190) }}</p>
                            <div class="mt-7 flex flex-wrap items-center gap-3">
                                <a href="{{ route('products.show', $product) }}" class="inline-flex rounded-md bg-white px-5 py-3 text-sm font-black text-zass-bark shadow-soft transition hover:bg-zass-linen">Shop this slide</a>
                                <a href="{{ route('vendors.show', $product->vendorStore->slug) }}" class="inline-flex rounded-md border border-white/30 bg-white/10 px-5 py-3 text-sm font-black text-white backdrop-blur transition hover:bg-white/20">View vendor</a>
                                <span class="rounded-full bg-zass-caramel px-4 py-2 text-sm font-black text-zass-ink">{{ $product->formattedPrice() }}</span>
                            </div>
                        </div>

                        <aside class="zm-hero-vendor-panel rounded-lg border border-white/15 bg-white/10 p-5 shadow-lift backdrop-blur-xl">
                            <div class="flex items-center gap-3">
                                <div class="grid h-14 w-14 place-items-center overflow-hidden rounded-md bg-white/15">
                                    @if ($product->vendorStore->logo_path)
                                        <img src="{{ $product->vendorStore->logo_path }}" alt="{{ $product->vendorStore->name }} logo" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-xl font-black">{{ str($product->vendorStore->name)->substr(0, 1)->upper() }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-wide text-zass-linen">Featured vendor</p>
                                    <p class="text-lg font-black">{{ $product->vendorStore->name }}</p>
                                </div>
                            </div>
                            <p class="mt-4 line-clamp-3 text-sm leading-6 text-zass-linen">{{ $product->vendorStore->description ?: 'Approved ZassMarket seller with fresh catalog picks.' }}</p>

                            <div class="mt-5 border-t border-white/15 pt-5">
                                <p class="text-xs font-black uppercase tracking-wide text-zass-linen">Bundle idea</p>
                                <div class="mt-3 space-y-3">
                                    <a href="{{ route('products.show', $product) }}" class="flex items-center justify-between gap-4 text-sm font-bold">
                                        <span class="flex min-w-0 items-center gap-3">
                                            @include('market.partials.product-thumb', ['name' => $product->name, 'imagePath' => $heroImage->path, 'imageAlt' => $heroImage->alt_text ?? $product->name, 'size' => 'sm'])
                                            <span class="min-w-0 truncate">{{ $product->name }}</span>
                                        </span>
                                        <span>{{ $product->formattedPrice() }}</span>
                                    </a>
                                    @foreach ($bundleItems as $bundleProduct)
                                        <a href="{{ route('products.show', $bundleProduct) }}" class="flex items-center justify-between gap-4 text-sm font-bold text-zass-linen hover:text-white">
                                            <span class="flex min-w-0 items-center gap-3">
                                                @include('market.partials.product-thumb', ['product' => $bundleProduct, 'size' => 'sm'])
                                                <span class="min-w-0 truncate">{{ $bundleProduct->name }}</span>
                                            </span>
                                            <span>{{ $bundleProduct->formattedPrice() }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </article>
        @empty
            <div class="grid min-h-[min(720px,calc(84svh-73px))] place-items-center px-6 text-center" style="min-height: min(720px, calc(84svh - 73px));">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-zass-linen">Homepage slider</p>
                    <h1 class="mt-3 text-4xl font-black">Select product images for the homepage hero in admin.</h1>
                </div>
            </div>
        @endforelse

        @if ($slides->isNotEmpty())
            <div class="absolute bottom-5 left-0 right-0 z-20 flex flex-col gap-4 px-[clamp(1rem,4vw,4rem)] sm:flex-row sm:items-center sm:justify-between">
                <div class="flex gap-2">
                    @foreach ($slides as $index => $product)
                        <button @click="active = {{ $index }}" class="h-2.5 rounded-full transition-all" :class="active === {{ $index }} ? 'w-12 bg-white' : 'w-2.5 bg-white/45'" title="Show slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button @click="active = (active - 1 + total) % total" class="grid h-11 w-11 place-items-center rounded-md bg-white/15 text-white backdrop-blur transition hover:bg-white/25" title="Previous slide">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6" /></svg>
                    </button>
                    <button @click="active = (active + 1) % total" class="grid h-11 w-11 place-items-center rounded-md bg-white text-zass-bark transition hover:bg-zass-linen" title="Next slide">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>
            </div>
        @endif
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
