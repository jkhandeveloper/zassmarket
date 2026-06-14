@extends('market.layout', ['title' => 'Register'])

@section('content')
    <section class="zm-container zm-auth-shell">
        <div class="flex min-h-[560px] flex-col justify-center rounded-lg border border-zass-linen/70 bg-white/70 p-6 shadow-soft backdrop-blur sm:p-8 lg:p-10">
            <p class="zm-pill">Create account</p>
            <h1 class="mt-5 max-w-3xl text-4xl font-black tracking-tight text-zass-ink sm:text-5xl">Join ZassMarket</h1>
            <p class="mt-5 max-w-2xl text-lg leading-8 text-zass-bark/75">Save wishlist picks, checkout faster, review products, and apply for a vendor storefront when you are ready.</p>
            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                @foreach (['Fast checkout', 'Product reviews', 'Vendor access'] as $item)
                    <div class="rounded-md border border-zass-linen/70 bg-white/80 p-4 shadow-sm">
                        <p class="text-sm font-black text-zass-bark">{{ $item }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="zm-auth-panel self-center">
            <div class="mb-6">
                <p class="zm-pill">New account</p>
                <h2 class="mt-4 text-3xl font-black">Register</h2>
                <p class="mt-2 text-sm leading-6 text-zass-bark/75">Create your customer account first. Vendor tools are available after sign in.</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a class="text-sm font-bold text-zass-bark hover:text-zass-ink" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button>
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
@endsection
