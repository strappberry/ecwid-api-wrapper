<?php

namespace Strappberry\EcwidApi;

use Illuminate\Support\ServiceProvider;
use Strappberry\EcwidApi\Services\EcwidApiService;

class EcwidApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/ecwid-api.php', 'ecwid-api'
        );

        $this->app->singleton(EcwidApiService::class, function ($app) {
            return new EcwidApiService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../publishable/config/ecwid-api.php' => config_path('ecwid-api.php'),
        ]);
    }
}
