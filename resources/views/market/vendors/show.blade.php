@extends('market.layout', ['title' => $vendorStore->name])

@section('content')
    <section class="zm-container py-10">
        <div class="grid gap-6 rounded-lg border border-zass-linen/80 bg-white/85 p-6 shadow-soft md:grid-cols-[auto_1fr] md:p-8">
            <div class="grid h-28 w-28 place-items-center overflow-hidden rounded-md border border-zass-linen bg-zass-cream">
                @if ($vendorStore->logo_path)
                    <img src="{{ $vendorStore->logo_path }}" alt="{{ $vendorStore->name }} logo" class="h-full w-full object-cover">
                @else
                    <span class="text-4xl font-black text-zass-bark">{{ str($vendorStore->name)->substr(0, 1)->upper() }}</span>
                @endif
            </div>
            <div>
                <p class="zm-pill">Vendor store</p>
                <h1 class="mt-3 text-4xl font-black">{{ $vendorStore->name }}</h1>
                <p class="mt-3 max-w-3xl whitespace-pre-line leading-7 text-zass-bark/75">{{ $vendorStore->description ?: 'This vendor has not added a description yet.' }}</p>
                <div class="mt-5 flex flex-wrap gap-3 text-sm font-bold">
                    <span class="rounded-md border border-zass-linen bg-zass-cream px-3 py-2">{{ $vendorStore->ratingLabel() }} vendor rating</span>
                    <span class="rounded-md border border-zass-linen bg-zass-cream px-3 py-2">{{ $vendorStore->reviews_count }} review{{ $vendorStore->reviews_count === 1 ? '' : 's' }}</span>
                    <span class="rounded-md border border-zass-linen bg-zass-cream px-3 py-2">{{ $products->total() }} product{{ $products->total() === 1 ? '' : 's' }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="zm-container grid gap-8 pb-12 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
        <div>
            <div class="mb-4">
                <p class="zm-pill">Vendor reviews</p>
                <h2 class="mt-3 text-2xl font-black">Rate and review {{ $vendorStore->name }}</h2>
            </div>

            @auth
                <form method="POST" action="{{ route('vendors.reviews.store', $vendorStore) }}" class="rounded-md border border-zass-linen bg-white/85 p-5 shadow-soft">
                    @csrf
                    <div class="grid gap-4">
                        <div class="grid gap-2 text-sm font-bold">
                            <span>Rating</span>
                            @include('market.partials.rating-input', ['id' => 'vendor-rating'])
                        </div>
                        <label class="grid gap-1 text-sm font-bold">Title
                            <input name="title" value="{{ old('title') }}" maxlength="120" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                        </label>
                        <label class="grid gap-1 text-sm font-bold">Review
                            <textarea name="body" rows="4" maxlength="1500" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">{{ old('body') }}</textarea>
                        </label>
                    </div>
                    <button class="zm-btn-primary mt-4">Save vendor review</button>
                </form>
            @else
                <p class="rounded-md border border-zass-linen bg-white/80 p-5 text-sm font-semibold text-zass-bark/75">
                    <a href="{{ route('login') }}" class="font-black text-zass-bark hover:text-zass-ink">Log in</a> to rate and review this vendor.
                </p>
            @endauth

            <div class="mt-5 space-y-4">
                @forelse ($reviews as $review)
                    <article class="rounded-md border border-zass-linen bg-white/85 p-5 shadow-soft">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-black">{{ $review->title ?: 'Vendor review' }}</p>
                                <p class="mt-1 text-sm font-semibold text-zass-sage">{{ $review->user?->name ?? 'Customer' }}</p>
                            </div>
                            <span class="rounded-md bg-zass-caramel/20 px-2.5 py-1 text-sm font-black text-zass-bark">{{ $review->rating }}/5</span>
                        </div>
                        @if ($review->body)
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-zass-bark/80">{{ $review->body }}</p>
                        @endif
                    </article>
                @empty
                    <p class="rounded-md border border-zass-linen bg-white/80 p-5 text-sm text-zass-bark/75">No vendor reviews yet.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div class="mb-4">
                <p class="zm-pill">Current products</p>
                <h2 class="mt-3 text-2xl font-black">Products from this vendor</h2>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                @forelse ($products as $product)
                    @include('market.partials.product-card', ['product' => $product])
                @empty
                    <p class="rounded-md border border-zass-linen bg-white/80 p-5 text-sm text-zass-bark/75">This vendor has no active products right now.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </section>
@endsection
