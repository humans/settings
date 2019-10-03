<?php

namespace Artisan\Settings\Laravel;

class SettingsServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/laravel-settings.php',
            'laravel-settings'
        );
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->publishes([
            __DIR__ . '/../../config/laravel-settings.php' => config_path('laravel-settings.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeSettingsCommand::class,
            ]);
        }
    }
}
