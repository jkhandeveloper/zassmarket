<x-guest-layout>
    <div class="mb-6">
        <p class="zm-pill">Welcome back</p>
        <h1 class="mt-4 text-3xl font-black">Log in to ZassMarket</h1>
        <p class="mt-2 text-sm leading-6 text-zass-bark/75">Continue shopping, track orders, or manage your vendor storefront.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
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
</x-guest-layout>
