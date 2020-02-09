<?php

namespace Humans\Settings\Laravel;

use Illuminate\Support\Facades\App;

class SettingsServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/settings.php',
            'settings'
        );
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $this->publishes([
            __DIR__ . '/config/settings.php' => App::configPath('humans/settings.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeSettingsCommand::class,
            ]);
        }
    }
}
