<?php

namespace Lonban\Vcc;

use Illuminate\Support\ServiceProvider;
use Lonban\Vcc\Facades\VccFacade;

class VccServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'vcc');
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->mergeConfigFrom(__DIR__ . '/config/vcc.php', 'vcc');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'vcc');
        $this->publishes([
            __DIR__.'/resources/views' => base_path('resources/views/vcc'),
            __DIR__ . '/config/vcc.php' => config_path('vcc.php'),
            __DIR__.'/lang' => resource_path('lang/vcc'),
            __DIR__.'/database/migrations/' => database_path('migrations')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Vcc', function ($app) {
            return new VccFacade($app['session'], $app['config']);
        });
    }
}
