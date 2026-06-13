@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-bold text-zass-ink']) }}>
    {{ $value ?? $slot }}
</label>
