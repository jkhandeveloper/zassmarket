@extends('market.layout', ['title' => 'Thank you'])

@section('content')
    <section class="zm-container py-16 text-center">
        <h1 class="text-3xl font-bold">Thanks for your order.</h1>
        <p class="mt-3 text-zinc-600">We created {{ count($orderNumbers) }} vendor order{{ count($orderNumbers) === 1 ? '' : 's' }}.</p>
        @if ($orderNumbers)
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-5 text-left">
                @foreach ($orderNumbers as $number)
                    <p class="font-mono text-sm">{{ $number }}</p>
                @endforeach
            </div>
        @endif
        <a href="{{ route('products.index') }}" class="mt-6 inline-flex rounded-md bg-zinc-950 px-5 py-3 text-sm font-semibold text-white">Continue shopping</a>
    </section>
@endsection
