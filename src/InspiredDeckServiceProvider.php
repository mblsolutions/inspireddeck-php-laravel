<?php

namespace MBLSolutions\InspiredDeckLaravel;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use MBLSolutions\InspiredDeck\InspiredDeck;
use MBLSolutions\InspiredDeckLaravel\Middleware\LoadInspiredDeckConfig;

class InspiredDeckServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/inspireddeck.php' => config_path('inspireddeck.php'),
        ], 'config');

        $this->registerMiddleware(LoadInspiredDeckConfig::class);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InspiredDeck::class, static function () {
            InspiredDeck::setBaseUri(config('inspireddeck.endpoint'));

            return new InspiredDeck;
        });
    }

    /**
     * Register Middleware
     *
     * @param $middleware
     * @return void
     */
    public function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];

        $kernel->pushMiddleware($middleware);
    }

}