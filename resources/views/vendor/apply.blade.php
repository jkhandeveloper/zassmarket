@extends('market.layout', ['title' => 'Apply as vendor'])

@section('content')
    <section class="zm-container py-8">
        <h1 class="text-3xl font-bold">Vendor application</h1>
        @if ($store)
            <div class="mt-6 rounded-lg border border-zinc-200 bg-white p-6">
                <p>Your application for <strong>{{ $store->name }}</strong> is {{ $store->status }}.</p>
            </div>
        @else
            <form method="POST" action="{{ route('vendor.apply.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4 rounded-lg border border-zinc-200 bg-white p-6">
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
                <label class="grid gap-1 text-sm font-medium">Store logo
                    <input name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm">
                    <span class="text-xs text-zinc-500">Optional. JPG, PNG, or WebP up to 2MB.</span>
                </label>
                <label class="grid gap-1 text-sm font-medium">Description
                    <textarea name="description" rows="5" class="rounded-md border-zinc-300">{{ old('description') }}</textarea>
                </label>
                <button class="rounded-md bg-zinc-950 px-5 py-3 text-sm font-semibold text-white">Submit application</button>
            </form>
        @endif
    </section>
@endsection
