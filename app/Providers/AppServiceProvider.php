<?php

namespace App\Providers;

use App\Support\BusinessProfile;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

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
        Paginator::useBootstrap();

        View::composer('*', function ($view): void {
            $view->with('companyProfile', BusinessProfile::company());
            $view->with('businessDirectory', BusinessProfile::businesses());
        });
    }
}
