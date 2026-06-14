<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorReview extends Model
{
    protected $fillable = [
        'user_id',
        'rating',
        'title',
        'body',
    ];

    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
