<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $store = $request->user()?->vendorStore;

        abort_unless($store?->isApproved(), 403, 'Your vendor account is waiting for approval.');

        return $next($request);
    }
}
