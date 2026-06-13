@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-md border-zass-linen bg-white shadow-sm focus:border-zass-caramel focus:ring-zass-caramel disabled:opacity-60']) }}>
