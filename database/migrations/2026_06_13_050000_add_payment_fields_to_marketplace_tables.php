<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('cod')->after('payment_status');
            $table->string('stripe_checkout_session_id')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('stripe_checkout_session_id');
        });

        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->string('stripe_checkout_session_id')->nullable()->after('payment_status');
            $table->string('bank_receipt_path')->nullable()->after('stripe_checkout_session_id');
            $table->timestamp('paid_at')->nullable()->after('bank_receipt_path');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'stripe_checkout_session_id', 'paid_at']);
        });

        Schema::table('vendor_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'stripe_checkout_session_id', 'bank_receipt_path', 'paid_at']);
        });
    }
};
