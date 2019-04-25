<?php

namespace Artisan\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\App;

trait HasSettings
{
    public function properties()
    {
        return $this->morphMany(Models\Settings::class, 'configurable');
    }

    public function getSettingsAttribute()
    {
        return $this->settings();
    }

    public function settings()
    {
        $instance = $this->instance();

        $instance->setModel($this);

        return $instance;
    }

    public function instance()
    {
        $class = Arr::get(
            Config::get('laravel-settings.classes'),
            get_class($this),
            Settings::class
        );

        return App::make($class);
    }
}
