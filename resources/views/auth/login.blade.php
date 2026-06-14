@extends('market.layout', ['title' => 'Log in'])

@section('content')
    <section class="zm-container zm-auth-shell">
        <div class="flex min-h-[520px] flex-col justify-center rounded-lg border border-zass-linen/70 bg-white/70 p-6 shadow-soft backdrop-blur sm:p-8 lg:p-10">
            <p class="zm-pill">Welcome back</p>
            <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-tight text-zass-ink sm:text-5xl">Log in to ZassMarket</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-zass-bark/75">Continue shopping, track orders, save products, or manage your vendor storefront from the same marketplace experience.</p>
            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                @foreach (['Wishlist', 'Orders', 'Vendor tools'] as $item)
                    <div class="rounded-md border border-zass-linen/70 bg-white/80 p-4 shadow-sm">
                        <p class="text-sm font-black text-zass-bark">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="zm-auth-panel self-center">
            <div class="mb-6">
                <p class="zm-pill">Secure access</p>
                <h2 class="mt-4 text-3xl font-black">Log in</h2>
                <p class="mt-2 text-sm leading-6 text-zass-bark/75">Use your customer or vendor account.</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4 block">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-zass-stone text-zass-bark shadow-sm focus:ring-zass-caramel" name="remember">
                        <span class="ms-2 text-sm font-semibold text-zass-bark/75">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    @if (Route::has('password.request'))
                        <a class="text-sm font-bold text-zass-bark hover:text-zass-ink" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button>
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
                <p class="mt-6 text-center text-sm font-semibold text-zass-bark/75">
                    New here?
                    <a href="{{ route('register') }}" class="font-black text-zass-bark hover:text-zass-ink">Create an account</a>
                </p>
            </form>
        </div>
    </section>
@endsection
