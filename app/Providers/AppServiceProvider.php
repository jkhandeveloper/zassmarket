<?php

namespace App\Providers;

use App\Models\Page;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('market.layout', function ($view) {
            $view->with([
                'cartCount' => app(CartService::class)->count(),
                'footerPages' => $this->footerPages(),
            ]);
        });
    }

    private function footerPages(): Collection
    {
        try {
            return Page::published()->orderBy('title')->get(['title', 'slug']);
        } catch (Throwable $exception) {
            Log::error('Footer page links could not be loaded.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return collect();
        }
    }
}
