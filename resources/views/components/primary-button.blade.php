<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-md bg-zass-bark px-5 py-3 text-sm font-bold text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-zass-ink focus:outline-none focus:ring-2 focus:ring-zass-caramel focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
