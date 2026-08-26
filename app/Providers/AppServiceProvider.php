<?php

namespace App\Providers;

use App\Services\StoreSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

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
        Model::preventLazyLoading(! app()->isProduction());

        view()->composer('*', function (View $view): void {
            $view->with('store', app(StoreSettings::class)->get());
        });
    }
}
