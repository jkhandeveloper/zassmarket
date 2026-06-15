@php
    $product = $product ?? null;
    $name = $name ?? $product?->name ?? 'Product';
    $imagePath = $imagePath ?? null;
    $image = $product?->images?->first();
    $imageAlt = $imageAlt ?? $image?->alt_text ?? $name;
    $size = $size ?? 'md';
@endphp

<span class="{{ $size === 'sm' ? 'h-12 w-12' : 'h-16 w-16' }} grid shrink-0 place-items-center overflow-hidden rounded-md bg-zass-linen/60 text-zass-stone">
    @if ($imagePath || $image)
        <img src="{{ $imagePath ?? $image->path }}" alt="{{ $imageAlt }}" class="h-full w-full object-cover">
    @else
        <svg class="{{ $size === 'sm' ? 'h-5 w-5' : 'h-6 w-6' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <path d="m8 14 2.5-2.5L14 15l2-2 3 3" />
            <circle cx="9" cy="8" r="1.5" />
        </svg>
    @endif
</span>
