@extends('market.layout')

@section('content')
    <section class="bg-zass-cream py-12">
        <div class="zm-container max-w-4xl">
            <div class="mb-8">
                <p class="text-sm font-bold uppercase tracking-wide text-zass-sage">AI product image analysis</p>
                <h1 class="mt-2 text-3xl font-black text-zass-ink">Test product image upload</h1>
            </div>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <form method="POST" action="{{ route('ai.product-image.store') }}" enctype="multipart/form-data" class="rounded-md border border-zass-linen bg-white p-6 shadow-soft">
                    @csrf

                    <label for="image" class="block text-sm font-bold text-zass-ink">Product image</label>
                    <input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-3 block w-full rounded-md border border-zass-linen bg-zass-cream px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-zass-bark file:px-4 file:py-2 file:text-sm file:font-bold file:text-white"
                        required
                    >
                    @error('image')
                        <p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-zass-bark px-4 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-zass-ink">
                        Analyze image
                    </button>
                </form>

                <div class="rounded-md border border-zass-linen bg-white p-6 shadow-soft">
                    <h2 class="text-xl font-black text-zass-ink">AI result</h2>

                    @isset($result)
                        @if (! ($result['ok'] ?? false))
                            <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                                {{ $result['message'] ?? 'AI image analysis is temporarily unavailable. Please try again later.' }}
                            </div>
                        @endif

                        <dl class="mt-5 space-y-4 text-sm">
                            <div>
                                <dt class="font-bold text-zass-sage">Product name</dt>
                                <dd class="mt-1 text-zass-ink">{{ $result['product_name'] ?: 'Not available' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zass-sage">Description</dt>
                                <dd class="mt-1 whitespace-pre-line text-zass-ink">{{ $result['description'] ?: 'Not available' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zass-sage">SEO title</dt>
                                <dd class="mt-1 text-zass-ink">{{ $result['seo_title'] ?: 'Not available' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zass-sage">Meta description</dt>
                                <dd class="mt-1 text-zass-ink">{{ $result['meta_description'] ?: 'Not available' }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zass-sage">Tags</dt>
                                <dd class="mt-2 flex flex-wrap gap-2">
                                    @forelse (($result['tags'] ?? []) as $tag)
                                        <span class="rounded-md bg-zass-linen px-2.5 py-1 text-xs font-bold text-zass-ink">{{ $tag }}</span>
                                    @empty
                                        <span class="text-zass-ink">Not available</span>
                                    @endforelse
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-4 text-sm text-zass-ink/75">Upload an image to see the generated product details.</p>
                    @endisset
                </div>
            </div>
        </div>
    </section>
@endsection
