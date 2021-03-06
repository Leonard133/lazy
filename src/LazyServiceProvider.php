<?php

namespace Leonard133\Lazy;

use Illuminate\Support\ServiceProvider;
use Leonard133\Lazy\Console\GuardCommand;
use Leonard133\Lazy\Console\PackageCommand;

class LazyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lazy');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'lazy');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('lazy.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/lazy'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/lazy'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/lazy'),
            ], 'lang');*/

            // Registering package commands.
             $this->commands([
                 GuardCommand::class,
                 PackageCommand::class
             ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'lazy');

        // Register the main class to use with the facade
//        $this->app->singleton('lazy', function () {
//            return new Lazy;
//        });
    }
}
