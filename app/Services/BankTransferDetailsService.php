<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Mail;

class BankTransferDetailsService
{
    public function details(): array
    {
        return collect(config('services.bank_transfer'))
            ->filter()
            ->all();
    }

    public function text(): string
    {
        return collect($this->details())
            ->map(fn ($value, $key) => str($key)->headline().': '.$value)
            ->implode("\n");
    }

    public function emailTo(string $email, SubscriptionPlan $plan): void
    {
        $details = $this->text();

        Mail::raw(
            "Please pay your ZassMarket {$plan->name} subscription by bank transfer.\n\n{$details}\n\nAfter payment, upload your receipt from the vendor billing page.",
            fn ($message) => $message->to($email)->subject('ZassMarket bank transfer details')
        );
    }
}
