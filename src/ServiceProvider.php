<?php

namespace Artisan\Settings;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $settings = [];

    public function register()
    {
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
