<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\VendorStore;
use Illuminate\View\View;

class VendorStoreController extends Controller
{
    public function show(VendorStore $vendorStore): View
    {
        abort_unless($vendorStore->isApproved(), 404);

        $vendorStore->loadAvg('reviews', 'rating')->loadCount('reviews');

        $products = Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('vendor_store_id', $vendorStore->id)
            ->latest()
            ->paginate(12);

        return view('market.vendors.show', [
            'vendorStore' => $vendorStore,
            'products' => $products,
            'reviews' => $vendorStore->reviews()->with('user')->latest()->take(12)->get(),
        ]);
    }
}
