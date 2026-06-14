<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class VendorStore extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'owner_id',
        'subscription_plan_id',
        'name',
        'slug',
        'status',
        'support_email',
        'phone',
        'description',
        'logo_path',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (VendorStore $store): void {
            if ($store->status === self::STATUS_APPROVED && ! $store->approved_at) {
                $store->approved_at = now();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(VendorSubscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(VendorSubscription::class)->where('status', 'active')->latestOfMany();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(VendorReview::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function approve(): void
    {
        $this->forceFill([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
        ])->save();
    }

    public function productLimit(): int
    {
        return (int) ($this->plan?->product_limit ?? 0);
    }

    public function monthlyOrderLimit(): int
    {
        return (int) ($this->plan?->monthly_order_limit ?? 0);
    }

    public function canCreateProduct(): bool
    {
        $limit = $this->productLimit();

        return $limit === 0 || $this->products()->count() < $limit;
    }

    public function monthlyOrdersCount(?Carbon $date = null): int
    {
        $date ??= now();

        return $this->orders()
            ->whereBetween('created_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
            ->count();
    }

    public function canAcceptOrder(): bool
    {
        $limit = $this->monthlyOrderLimit();

        return $limit === 0 || $this->monthlyOrdersCount() < $limit;
    }

    public function ratingLabel(): string
    {
        $rating = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');

        return $rating ? number_format((float) $rating, 1) : 'No ratings yet';
    }
}
