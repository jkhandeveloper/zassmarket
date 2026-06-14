@extends('market.layout', ['title' => $product->exists ? 'Edit product' : 'New product'])

@section('content')
    <section class="zm-container py-8">
        <p class="zm-pill">Vendor product studio</p>
        <h1 class="mt-3 text-3xl font-black">{{ $product->exists ? 'Edit product' : 'New product' }}</h1>

        <section class="zm-card mt-6 p-6">
            <div class="flex items-start gap-4">
                <span class="grid h-12 w-12 place-items-center rounded-md bg-zass-bark text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20" />
                        <path d="m19 9-7-7-7 7" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-xl font-black">AI title, description and SEO assistant</h2>
                    <p class="mt-1 text-sm leading-6 text-zass-bark/75">Send your draft or brief to the Python AI server. The result will prefill the product fields below.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('vendor.products.ai-copy') }}" class="mt-5 grid gap-4">
                @csrf
                <label class="grid gap-1 text-sm font-bold">Category
                    <select name="category_id" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                        <option value="">Let AI decide</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-bold">Draft product name
                        <input name="name" value="{{ old('name', $product->name) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" placeholder="e.g. Handmade leather laptop sleeve">
                    </label>
                    <label class="grid gap-1 text-sm font-bold">Audience
                        <input name="audience" value="{{ old('audience') }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" placeholder="e.g. remote workers, students">
                    </label>
                </div>
                <label class="grid gap-1 text-sm font-bold">Draft description or product brief
                    <textarea name="description" rows="4" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" placeholder="Materials, benefits, style, size, use cases">{{ old('description', $product->description) }}</textarea>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="grid gap-1 text-sm font-bold">SEO keywords
                        <input name="keywords" value="{{ old('keywords', $product->seo_keywords) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" placeholder="comma separated">
                    </label>
                    <label class="grid gap-1 text-sm font-bold">Tone
                        <select name="tone" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                            @foreach (['premium', 'friendly', 'minimal', 'playful', 'technical'] as $tone)
                                <option value="{{ $tone }}" @selected(old('tone', 'premium') === $tone)>{{ str($tone)->headline() }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <button class="zm-btn-primary w-fit">Generate AI copy</button>
            </form>

            @if (session('ai_product_copy'))
                <div class="mt-5 rounded-lg border border-zass-linen bg-zass-cream/70 p-4">
                    <h3 class="font-black">AI draft ready</h3>
                    <dl class="mt-3 grid gap-3 text-sm">
                        @foreach ([
                            'Title' => session('ai_product_copy.title'),
                            'Description' => session('ai_product_copy.description'),
                            'SEO title' => session('ai_product_copy.seo_title'),
                            'SEO description' => session('ai_product_copy.seo_description'),
                            'SEO keywords' => is_array(session('ai_product_copy.seo_keywords')) ? implode(', ', session('ai_product_copy.seo_keywords')) : session('ai_product_copy.seo_keywords'),
                        ] as $label => $value)
                            @if ($value)
                                <div>
                                    <dt class="font-black text-zass-sage">{{ $label }}</dt>
                                    <dd class="mt-1 text-zass-bark">{{ $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            @endif
        </section>

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="zm-card mt-6 grid gap-4 p-6">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif
            <label class="grid gap-1 text-sm font-bold">Category
                <select name="category_id" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                    <option value="">Uncategorized</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="grid gap-1 text-sm font-bold">Name
                <input name="name" value="{{ old('name', $product->name) }}" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
            </label>
            <label class="grid gap-1 text-sm font-bold">Description
                <textarea name="description" rows="6" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">{{ old('description', $product->description) }}</textarea>
            </label>
            <div class="grid gap-4">
                <label class="grid gap-1 text-sm font-bold">SEO title
                    <input name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" maxlength="255">
                </label>
                <label class="grid gap-1 text-sm font-bold">SEO description
                    <textarea name="seo_description" rows="3" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" maxlength="500">{{ old('seo_description', $product->seo_description) }}</textarea>
                </label>
                <label class="grid gap-1 text-sm font-bold">SEO keywords
                    <input name="seo_keywords" value="{{ old('seo_keywords', $product->seo_keywords) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel" maxlength="255" placeholder="comma separated keywords">
                </label>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="grid gap-1 text-sm font-bold">Price
                    <input name="price" type="number" min="0.01" step="0.01" value="{{ old('price', $product->exists ? $product->price_cents / 100 : '') }}" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                </label>
                <label class="grid gap-1 text-sm font-bold">Discount percent
                    <input name="discount_percent" type="number" min="0" max="95" value="{{ old('discount_percent', $product->discount_percent ?? 0) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                    <span class="text-xs font-semibold text-zass-bark/60">Use 0 for no discount.</span>
                </label>
                <label class="grid gap-1 text-sm font-bold">Stock
                    <input name="stock" type="number" min="0" value="{{ old('stock', $product->exists ? $product->stock : 1) }}" required class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
                </label>
            </div>
            <label class="grid gap-1 text-sm font-bold">Upload main product image
                <input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-md border border-zass-linen bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-zass-bark file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                <span class="text-xs font-semibold text-zass-bark/60">JPG, PNG, or WebP up to 5MB.</span>
            </label>
            <label class="grid gap-1 text-sm font-bold">Upload gallery images
                <input name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="rounded-md border border-zass-linen bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-zass-bark file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                <span class="text-xs font-semibold text-zass-bark/60">Optional. Select multiple JPG, PNG, or WebP files. Each image can be up to 5MB.</span>
            </label>
            @if ($product->images->isNotEmpty())
                <div class="grid gap-3 rounded-md border border-zass-linen bg-zass-linen/30 p-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($product->images as $image)
                        <img src="{{ $image->path }}" alt="{{ $image->alt_text ?? $product->name }}" class="h-36 w-full rounded-md object-cover">
                    @endforeach
                </div>
            @endif
            <label class="grid gap-1 text-sm font-bold">Image URL or public path fallback
                <input name="image_path" value="{{ old('image_path', $product->images->first()?->path) }}" class="rounded-md border-zass-linen focus:border-zass-caramel focus:ring-zass-caramel">
            </label>
            <label class="flex items-center gap-2 text-sm font-bold">
                <input name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true)) class="rounded border-zass-stone text-zass-bark focus:ring-zass-caramel">
                Active
            </label>
            <div>
                <button class="zm-btn-primary">Save product</button>
            </div>
        </form>

        @if ($product->exists)
            <form method="POST" action="{{ route('vendor.products.ai-suggestions', $product) }}" class="mt-4">
                @csrf
                <button class="zm-btn-secondary">Refresh saved AI analysis</button>
            </form>
        @endif

        @if ($product->ai_suggestions)
            <section class="zm-card mt-6 p-6">
                <h2 class="font-black">Saved AI analysis</h2>
                <pre class="mt-3 overflow-auto rounded-md bg-zass-ink p-4 text-sm text-white">{{ json_encode($product->ai_suggestions, JSON_PRETTY_PRINT) }}</pre>
            </section>
        @endif
    </section>
@endsection
