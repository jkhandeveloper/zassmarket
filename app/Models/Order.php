<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_store_id',
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'stripe_checkout_session_id',
        'paid_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'subtotal_cents',
        'shipping_cents',
        'total_cents',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedTotal(): string
    {
        return '$'.number_format($this->total_cents / 100, 2);
    }
}
