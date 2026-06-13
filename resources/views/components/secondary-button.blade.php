<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-md border border-zass-stone/70 bg-white px-5 py-3 text-sm font-bold text-zass-ink shadow-sm transition hover:-translate-y-0.5 hover:border-zass-bark hover:text-zass-bark focus:outline-none focus:ring-2 focus:ring-zass-caramel focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
