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
        $this->publishes([
            __DIR__ . '/config/settings.php' => App::configPath('humans/settings.php'),
            __DIR__ . '/migrations/2019_01_01_100000_create_settings_table.php' => App::databasePath('migrations/2019_01_01_100000_create_settings_table.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeSettingsCommand::class,
            ]);
        }
    }
}
