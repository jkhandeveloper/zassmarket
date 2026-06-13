<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('admin') => redirect()->route('filament.admin.pages.dashboard'),
            $user->hasRole('vendor') => redirect()->route('vendor.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }
}
