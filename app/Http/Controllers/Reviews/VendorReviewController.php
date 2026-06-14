<?php

namespace App\Http\Controllers\Reviews;

use App\Http\Controllers\Controller;
use App\Models\VendorStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorReviewController extends Controller
{
    public function store(Request $request, VendorStore $vendorStore): RedirectResponse
    {
        abort_unless($vendorStore->isApproved(), 404);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:1500'],
        ]);

        $vendorStore->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data,
        );

        return back()
            ->with('status', 'Vendor review saved.')
            ->with('notification', [
                'message' => 'Vendor review saved.',
                'meta' => 'Thanks for sharing your seller experience.',
            ]);
    }
}
