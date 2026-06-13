@extends('market.layout', ['title' => 'Vendor billing'])

@section('content')
    <section class="zm-container py-10">
        <div class="mb-8 rounded-lg border border-zass-linen bg-white/85 p-6 shadow-soft backdrop-blur sm:p-8">
            <p class="zm-pill">Vendor subscription</p>
            <div class="mt-4 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <h1 class="text-3xl font-black sm:text-4xl">Billing for {{ $store->name }}</h1>
                    <p class="mt-2 max-w-2xl text-zass-bark/75">Pay by Stripe card or bank transfer. Bank details are pulled from your environment and can be emailed to the vendor.</p>
                </div>
                <a href="{{ route('vendor.dashboard') }}" class="zm-btn-secondary">Back to dashboard</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_420px]">
            <div class="space-y-5">
                @foreach ($plans as $plan)
                    @php
                        $subscription = $store->subscriptions->firstWhere('subscription_plan_id', $plan->id);
                        $isCurrent = $store->subscription_plan_id === $plan->id;
                    @endphp
                    <article class="zm-card p-5 sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-2xl font-black">{{ $plan->name }}</h2>
                                    @if ($isCurrent)
                                        <span class="rounded-full bg-zass-sage px-3 py-1 text-xs font-black uppercase tracking-wide text-white">Current</span>
                                    @endif
                                    @if ($subscription)
                                        <span class="rounded-full bg-zass-cream px-3 py-1 text-xs font-black uppercase tracking-wide text-zass-bark">{{ str($subscription->payment_status)->headline() }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-3xl font-black text-zass-bark">{{ $plan->formattedPrice() }}</p>
                                <p class="mt-2 text-sm font-semibold text-zass-sage">{{ $plan->product_limit }} products · {{ $plan->monthly_order_limit }} orders monthly</p>
                                <p class="mt-3 max-w-2xl text-sm leading-6 text-zass-bark/75">{{ $plan->description }}</p>
                            </div>
                            <div class="grid gap-2 sm:min-w-48">
                                @if ($stripeEnabled)
                                    <form method="POST" action="{{ route('vendor.billing.card') }}">
                                        @csrf
                                        <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                                        <button class="zm-btn-primary w-full">
                                            <svg class="zm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                                <path d="M2 10h20" />
                                            </svg>
                                            Pay by card
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('vendor.billing.email-bank-details') }}">
                                    @csrf
                                    <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                                    <button class="zm-btn-secondary w-full py-3">
                                        <svg class="zm-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16v16H4z" />
                                            <path d="m22 6-10 7L2 6" />
                                        </svg>
                                        Email bank details
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('vendor.billing.bank-transfer') }}" enctype="multipart/form-data" class="mt-5 rounded-lg border border-zass-linen bg-zass-cream/60 p-4">
                            @csrf
                            <input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}">
                            <label class="grid gap-2 text-sm font-black">
                                Upload bank receipt for {{ $plan->name }}
                                <input type="file" name="receipt" required accept=".jpg,.jpeg,.png,.pdf,.webp" class="rounded-md border border-zass-linen bg-white text-sm file:mr-4 file:border-0 file:bg-zass-bark file:px-4 file:py-2 file:font-bold file:text-white focus:border-zass-caramel focus:ring-zass-caramel">
                            </label>
                            <button class="zm-btn-primary mt-3 py-2.5">Submit bank receipt</button>
                        </form>
                    </article>
                @endforeach
            </div>

            <aside class="h-fit space-y-5">
                <section class="zm-card p-6">
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-md bg-zass-bark text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 21h18" />
                                <path d="M4 10h16" />
                                <path d="M6 10V7l6-4 6 4v3" />
                                <path d="M6 14v4" />
                                <path d="M10 14v4" />
                                <path d="M14 14v4" />
                                <path d="M18 14v4" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-xl font-black">Bank transfer details</h2>
                            <p class="text-sm font-semibold text-zass-sage">Configured from .env</p>
                        </div>
                    </div>
                    <dl class="mt-5 space-y-3">
                        @forelse ($bankDetails as $key => $value)
                            <div class="rounded-md bg-zass-cream p-3">
                                <dt class="text-xs font-black uppercase tracking-wide text-zass-sage">{{ str($key)->headline() }}</dt>
                                <dd class="mt-1 break-words font-bold text-zass-bark">{{ $value }}</dd>
                            </div>
                        @empty
                            <p class="rounded-md bg-red-50 p-3 text-sm font-semibold text-red-800">No bank details are configured yet.</p>
                        @endforelse
                    </dl>
                </section>

                <section class="zm-card p-6">
                    <h2 class="font-black">Payment status</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($store->subscriptions()->with('plan')->latest()->get() as $subscription)
                            <div class="rounded-md border border-zass-linen p-3">
                                <p class="font-black">{{ $subscription->plan?->name }}</p>
                                <p class="mt-1 text-sm text-zass-bark/75">{{ str($subscription->payment_method ?? 'not selected')->headline() }} · {{ str($subscription->payment_status)->headline() }}</p>
                                @if ($subscription->bank_receipt_path)
                                    <a href="{{ asset('storage/'.$subscription->bank_receipt_path) }}" class="mt-2 inline-flex text-sm font-black text-zass-bark hover:text-zass-ink" target="_blank">View receipt</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </section>
@endsection
