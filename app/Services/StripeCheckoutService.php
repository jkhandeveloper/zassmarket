<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class StripeCheckoutService
{
    public function isEnabled(): bool
    {
        return (bool) config('services.stripe.enabled') && filled(config('services.stripe.secret'));
    }

    public function createSession(array $payload): array
    {
        if (! $this->isEnabled()) {
            throw ValidationException::withMessages([
                'payment_method' => 'Card payments are not enabled.',
            ]);
        }

        $response = Http::asForm()
            ->withToken(config('services.stripe.secret'))
            ->post('https://api.stripe.com/v1/checkout/sessions', $payload);

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'payment_method' => $response->json('error.message', 'Stripe could not create a checkout session.'),
            ]);
        }

        return $response->json();
    }

    public function retrieveSession(string $sessionId): array
    {
        if (! $this->isEnabled()) {
            throw ValidationException::withMessages([
                'payment_method' => 'Card payments are not enabled.',
            ]);
        }

        $response = Http::withToken(config('services.stripe.secret'))
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'payment_method' => $response->json('error.message', 'Stripe could not verify this payment.'),
            ]);
        }

        return $response->json();
    }
}
