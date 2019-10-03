<?php

namespace Artisan\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

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
        $class = Arr::get(
            Config::get('laravel-settings.classes'),
            get_class($this),
            Settings::class
        );

        return new $class($this);
    }
}
