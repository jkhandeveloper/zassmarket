@props([
    'name' => 'rating',
    'id' => 'rating',
    'selected' => 5,
])

@once
    <style>
        .zm-rating-control {
            display: inline-flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 0.35rem;
        }

        .zm-rating-control input {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
            padding: 0;
        }

        .zm-rating-control label {
            display: inline-flex;
            cursor: pointer;
            color: #b5a192;
            transition: color 150ms ease, transform 150ms ease;
        }

        .zm-rating-control label svg {
            display: block;
            width: 1.75rem;
            height: 1.75rem;
            fill: currentColor;
        }

        .zm-rating-control input:checked ~ label,
        .zm-rating-control label:hover,
        .zm-rating-control label:hover ~ label {
            color: #b08463;
        }

        .zm-rating-control label:hover {
            transform: translateY(-1px);
        }

        .zm-rating-control input:focus-visible + label {
            outline: 2px solid #b08463;
            outline-offset: 3px;
            border-radius: 0.25rem;
        }
    </style>
@endonce

<div class="zm-rating-control" role="radiogroup" aria-label="Rating">
    @foreach ([5, 4, 3, 2, 1] as $rating)
        <input
            id="{{ $id }}-{{ $rating }}"
            name="{{ $name }}"
            type="radio"
            value="{{ $rating }}"
            @checked((int) old($name, $selected) === $rating)
            required
        >
        <label for="{{ $id }}-{{ $rating }}" title="{{ $rating }} star{{ $rating === 1 ? '' : 's' }}">
            <svg viewBox="0 0 20 20" aria-hidden="true">
                <path d="M10 1.7 12.5 7l5.8.7-4.3 4 1.1 5.7L10 14.5l-5.1 2.9L6 11.7l-4.3-4L7.5 7 10 1.7Z" />
            </svg>
            <span style="position:absolute;width:1px;height:1px;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;padding:0;">{{ $rating }} star{{ $rating === 1 ? '' : 's' }}</span>
        </label>
    @endforeach
</div>
