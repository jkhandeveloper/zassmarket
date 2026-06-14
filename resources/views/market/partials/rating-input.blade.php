@props([
    'name' => 'rating',
    'id' => 'rating',
    'selected' => 5,
])

<div class="zm-star-input" role="radiogroup" aria-label="Rating">
    @foreach ([1, 2, 3, 4, 5] as $rating)
        <span class="inline-flex">
            <input
                id="{{ $id }}-{{ $rating }}"
                name="{{ $name }}"
                type="radio"
                value="{{ $rating }}"
                @checked((int) old($name, $selected) === $rating)
                required
            >
            <label for="{{ $id }}-{{ $rating }}" title="{{ $rating }} star{{ $rating === 1 ? '' : 's' }}">
                @for ($star = 1; $star <= $rating; $star++)
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10 1.7 12.5 7l5.8.7-4.3 4 1.1 5.7L10 14.5l-5.1 2.9L6 11.7l-4.3-4L7.5 7 10 1.7Z" />
                    </svg>
                @endfor
                <span class="sr-only">{{ $rating }} star{{ $rating === 1 ? '' : 's' }}</span>
            </label>
        </span>
    @endforeach
</div>
