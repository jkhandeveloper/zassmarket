<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name', 'ZassMarket') }}</title>
        @isset($metaDescription)
            <meta name="description" content="{{ $metaDescription }}">
        @endisset
        @stack('meta')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen text-zass-ink antialiased">
        @php
            $cartCount = $cartCount ?? 0;
            $footerPages = $footerPages ?? collect();
            $notification = session('notification');
            $statusMessage = is_string(session('status')) ? session('status') : null;
        @endphp

        <header class="sticky top-0 z-40 border-b border-zass-linen/70 bg-zass-cream/90 shadow-sm backdrop-blur-xl">
            <div class="zm-container flex items-center justify-between py-4">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-zass-bark text-white shadow-soft">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 7h12l-1 12H7L6 7Z" />
                            <path d="M9 7a3 3 0 0 1 6 0" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-xl font-black tracking-tight">ZassMarket</span>
                        <span class="hidden text-xs font-semibold uppercase tracking-wide text-zass-sage sm:block">Curated multi-vendor commerce</span>
                    </span>
                </a>
                <nav class="flex items-center gap-2 text-sm font-bold text-zass-ink sm:gap-4">
                    <a href="{{ route('products.index') }}" class="rounded-md px-3 py-2 transition hover:bg-white hover:text-zass-bark">Products</a>
                    <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 rounded-md px-3 py-2 transition hover:bg-white hover:text-zass-bark" aria-label="Cart with {{ $cartCount }} {{ $cartCount === 1 ? 'product' : 'products' }}">
                        <span class="relative inline-flex">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="8" cy="21" r="1" />
                                <circle cx="19" cy="21" r="1" />
                                <path d="M2 2h3l3 14h11l2-9H7" />
                            </svg>
                            @if ($cartCount > 0)
                                <span class="absolute -right-2.5 -top-3 grid min-h-5 min-w-5 place-items-center rounded-full bg-zass-caramel px-1 text-[11px] font-black leading-none text-zass-ink shadow-sm ring-2 ring-zass-cream">
                                    {{ $cartCount > 99 ? '99+' : $cartCount }}
                                </span>
                            @endif
                        </span>
                        <span class="hidden sm:inline">Cart</span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 transition hover:bg-white hover:text-zass-bark">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md px-3 py-2 transition hover:bg-white hover:text-zass-bark">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 transition hover:bg-white hover:text-zass-bark">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-zass-bark px-4 py-2 text-white shadow-soft transition hover:bg-zass-ink">Join</a>
                    @endauth
                </nav>
            </div>
        </header>

        @if ($notification || $statusMessage)
            <div class="pointer-events-none fixed right-4 top-24 z-50 w-[calc(100%-2rem)] max-w-sm sm:right-6" role="status" aria-live="polite">
                <div class="pointer-events-auto rounded-md border border-zass-sage/30 bg-white px-4 py-3 text-sm text-zass-ink shadow-lift">
                    <div class="flex items-start gap-3">
                        <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-full bg-zass-sage/15 text-zass-sage">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-black">{{ $notification['message'] ?? $statusMessage }}</p>
                            @if (! empty($notification['meta']))
                                <p class="mt-1 font-semibold text-zass-sage">{{ $notification['meta'] }}</p>
                            @endif
                            @if (! empty($notification['action_url']) && ! empty($notification['action_label']))
                                <a href="{{ $notification['action_url'] }}" class="mt-3 inline-flex rounded-md bg-zass-bark px-3 py-2 text-xs font-black text-white transition hover:bg-zass-ink">
                                    {{ $notification['action_label'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="zm-container mt-4">
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            </div>
        @endif

        <main class="overflow-hidden">
            @yield('content')
        </main>

        <footer class="border-t border-zass-linen/70 bg-zass-ink text-zass-cream">
            <div class="zm-container grid gap-8 py-10 md:grid-cols-[1fr_auto_auto]">
                <div>
                    <p class="text-lg font-black">ZassMarket</p>
                    <p class="mt-2 max-w-md text-sm text-zass-linen">A warm, modern marketplace for approved vendors, curated catalogs, and smooth checkout.</p>
                </div>
                <nav class="grid gap-2 text-sm font-semibold text-zass-linen">
                    <a href="{{ route('products.index') }}" class="hover:text-white">Shop products</a>
                    <a href="{{ route('vendor.apply') }}" class="hover:text-white">Sell on ZassMarket</a>
                </nav>
                @if ($footerPages->isNotEmpty())
                    <nav class="grid gap-2 text-sm font-semibold text-zass-linen">
                        @foreach ($footerPages as $page)
                            <a href="{{ route('pages.show', $page) }}" class="hover:text-white">{{ $page->title }}</a>
                        @endforeach
                    </nav>
                @endif
            </div>
        </footer>
    </body>
</html>
