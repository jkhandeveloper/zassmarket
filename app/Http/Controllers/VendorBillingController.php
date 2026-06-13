<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\VendorSubscription;
use App\Services\BankTransferDetailsService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VendorBillingController extends Controller
{
    public function __construct(private readonly BankTransferDetailsService $bankDetails) {}

    public function index(): View
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store, 404);

        return view('vendor.billing', [
            'store' => $store->load('plan', 'activeSubscription'),
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price_cents')->get(),
            'stripeEnabled' => (bool) config('services.stripe.enabled') && filled(config('services.stripe.secret')),
            'bankDetails' => $this->bankDetails->details(),
        ]);
    }

    public function payByCard(Request $request, StripeCheckoutService $stripe): RedirectResponse
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store, 404);

        $data = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $plan = SubscriptionPlan::findOrFail($data['subscription_plan_id']);
        $subscription = $this->syncSubscription($store, $plan, 'card', 'pending');

        $session = $stripe->createSession([
            'mode' => 'payment',
            'success_url' => route('vendor.billing.card.success', ['subscription' => $subscription]).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('vendor.billing.index'),
            'customer_email' => auth()->user()->email,
            'metadata[vendor_store_id]' => $store->id,
            'metadata[vendor_subscription_id]' => $subscription->id,
            'line_items[0][quantity]' => 1,
            'line_items[0][price_data][currency]' => strtolower(config('services.stripe.currency', 'usd')),
            'line_items[0][price_data][unit_amount]' => $plan->price_cents,
            'line_items[0][price_data][product_data][name]' => "ZassMarket {$plan->name} vendor subscription",
        ]);

        $subscription->update(['stripe_checkout_session_id' => $session['id'] ?? null]);

        return redirect()->away($session['url']);
    }

    public function cardSuccess(Request $request, VendorSubscription $subscription, StripeCheckoutService $stripe): RedirectResponse
    {
        abort_unless($subscription->vendorStore->owner_id === auth()->id(), 403);

        $sessionId = $request->query('session_id', $subscription->stripe_checkout_session_id);
        $session = $stripe->retrieveSession($sessionId);

        if (($session['payment_status'] ?? null) !== 'paid') {
            throw ValidationException::withMessages([
                'payment_method' => 'Stripe has not marked this subscription payment as paid yet.',
            ]);
        }

        $subscription->update([
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'stripe_checkout_session_id' => $sessionId,
            'paid_at' => now(),
            'status' => 'active',
            'starts_at' => $subscription->starts_at ?? now(),
        ]);

        $subscription->vendorStore->update(['subscription_plan_id' => $subscription->subscription_plan_id]);

        return redirect()->route('vendor.billing.index')->with('status', 'Subscription payment received.');
    }

    public function bankTransfer(Request $request): RedirectResponse
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store, 404);

        $data = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ]);

        $plan = SubscriptionPlan::findOrFail($data['subscription_plan_id']);
        $path = $request->file('receipt')->store('bank-receipts', 'public');
        $subscription = $this->syncSubscription($store, $plan, 'bank_transfer', 'pending_review');
        $subscription->update(['bank_receipt_path' => $path]);
        $store->update(['subscription_plan_id' => $plan->id]);

        $this->bankDetails->emailTo(auth()->user()->email, $plan);

        return redirect()->route('vendor.billing.index')->with('status', 'Bank receipt uploaded. Admin will review your subscription payment.');
    }

    public function emailBankDetails(Request $request): RedirectResponse
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store, 404);

        $data = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $this->bankDetails->emailTo(auth()->user()->email, SubscriptionPlan::findOrFail($data['subscription_plan_id']));

        return back()->with('status', 'Bank transfer details emailed to you.');
    }

    private function syncSubscription($store, SubscriptionPlan $plan, string $method, string $paymentStatus): VendorSubscription
    {
        return VendorSubscription::updateOrCreate(
            ['vendor_store_id' => $store->id, 'subscription_plan_id' => $plan->id],
            [
                'status' => 'active',
                'payment_method' => $method,
                'payment_status' => $paymentStatus,
                'starts_at' => now(),
            ]
        );
    }
}
