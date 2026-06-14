<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_store_id',
        'category_id',
        'name',
        'slug',
        'description',
        'price_cents',
        'discount_percent',
        'stock',
        'is_active',
        'ai_suggestions',
        'seo_title',
        'seo_description',
        'seo_keywords',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'ai_suggestions' => 'array',
            'discount_percent' => 'integer',
        ];
    }

    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('vendorStore', fn (Builder $store) => $store->approved());
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->published()
            ->where('stock', '>', 0);
    }

    public function formattedPrice(): string
    {
        return '$'.number_format($this->currentPriceCents() / 100, 2);
    }

    public function formattedOriginalPrice(): string
    {
        return '$'.number_format($this->price_cents / 100, 2);
    }

    public function currentPriceCents(): int
    {
        if (! $this->hasDiscount()) {
            return $this->price_cents;
        }

        return (int) round($this->price_cents * (100 - $this->discount_percent) / 100);
    }

    public function hasDiscount(): bool
    {
        return (int) $this->discount_percent > 0;
    }

    public function primaryImage(): ?ProductImage
    {
        return $this->images->first();
    }

    public function ratingLabel(): string
    {
        $rating = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');

        return $rating ? number_format((float) $rating, 1) : 'No ratings yet';
    }
}
