@extends('market.layout', ['title' => 'Apply as vendor'])

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Vendor application</h1>
        @if ($store)
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-6">
                <p>Your application for <strong>{{ $store->name }}</strong> is {{ $store->status }}.</p>
            </div>
        @else
            <form method="POST" action="{{ route('vendor.apply.store') }}" class="mt-6 grid gap-4 rounded-lg border border-zinc-200 bg-white p-6">
                @csrf
                <label class="grid gap-1 text-sm font-medium">Plan
                    <select name="subscription_plan_id" required class="rounded-md border-zinc-300">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->formattedPrice() }} - {{ $plan->product_limit }} products</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid gap-1 text-sm font-medium">Store name
                    <input name="name" value="{{ old('name') }}" required class="rounded-md border-zinc-300">
                </label>
                <label class="grid gap-1 text-sm font-medium">Support email
                    <input name="support_email" type="email" value="{{ old('support_email', auth()->user()->email) }}" required class="rounded-md border-zinc-300">
                </label>
                <label class="grid gap-1 text-sm font-medium">Phone
                    <input name="phone" value="{{ old('phone') }}" class="rounded-md border-zinc-300">
                </label>
                <label class="grid gap-1 text-sm font-medium">Description
                    <textarea name="description" rows="5" class="rounded-md border-zinc-300">{{ old('description') }}</textarea>
                </label>
                <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-semibold text-white">Submit application</button>
            </form>
        @endif
    </section>
@endsection
