<?php

namespace App\Providers;

use App\Core\KTBootstrap;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Debugbar', Debugbar::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Update defaultStringLength
        Builder::defaultStringLength(191);

        KTBootstrap::init();

        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
