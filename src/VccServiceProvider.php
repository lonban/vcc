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
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/lang', 'vcc');
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'vcc');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'vcc');
        $this->publishes([
            __DIR__.'/lang' => resource_path('lang/vcc'),
            __DIR__ . '/config/config.php' => config_path('vcc.php'),
            __DIR__.'/database/migrations/' => database_path('migrations'),
            __DIR__.'/resources/views' => base_path('resources/views/vcc'),
            __DIR__.'/public/img' => base_path('public/vcc_img'),
            __DIR__.'/public/css' => base_path('public/vcc_css'),
            __DIR__.'/public/js' => base_path('public/vcc_js'),
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
