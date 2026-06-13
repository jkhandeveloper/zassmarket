<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ZassMarket') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zass-ink antialiased">
        <div class="relative grid min-h-screen overflow-hidden lg:grid-cols-[1fr_520px]">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(208,185,167,.55),transparent_34rem),linear-gradient(135deg,#F7F1EC_0%,#fff_45%,#D0B9A7_140%)]"></div>
            <section class="hidden p-10 lg:flex lg:flex-col lg:justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-zass-bark text-white shadow-soft">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 7h12l-1 12H7L6 7Z" />
                            <path d="M9 7a3 3 0 0 1 6 0" />
                        </svg>
                    </span>
                    <span class="text-2xl font-black">ZassMarket</span>
                </a>
                <div class="max-w-2xl">
                    <p class="zm-pill">Warm commerce experience</p>
                    <h1 class="mt-5 text-5xl font-black tracking-tight">A marketplace account that feels like the storefront.</h1>
                    <p class="mt-5 text-lg leading-8 text-zass-bark/75">Sign in to save products, manage orders, open a vendor store, and continue checkout across devices.</p>
                    <div class="mt-8 grid grid-cols-3 gap-3">
                        @foreach (['Wishlist', 'Orders', 'Vendor tools'] as $item)
                            <div class="rounded-lg border border-zass-linen/70 bg-white/70 p-4 shadow-sm backdrop-blur">
                                <p class="text-sm font-black text-zass-bark">{{ $item }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <p class="text-sm font-semibold text-zass-sage">Secure Breeze auth styled for ZassMarket.</p>
            </section>

            <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:bg-zass-ink">
                <div class="w-full max-w-md">
                    <div class="mb-6 text-center lg:hidden">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-md bg-zass-bark text-white shadow-soft">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M6 7h12l-1 12H7L6 7Z" />
                                    <path d="M9 7a3 3 0 0 1 6 0" />
                                </svg>
                            </span>
                            <span class="text-xl font-black">ZassMarket</span>
                        </a>
                    </div>
                    <div class="rounded-lg border border-zass-linen/80 bg-white/95 p-6 shadow-lift backdrop-blur sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
