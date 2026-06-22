<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::defaultView('partials.pagination');

        View::composer(['layouts.admin', 'admin.*'], function ($view): void {
            $user = auth()->user();
            $view->with('isAdmin', $user?->isAdmin() ?? false);
        });
    }
}
