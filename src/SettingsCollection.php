<?php

namespace Artisan\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SettingsCollection extends Collection
{
    public function unflatten()
    {
        return $this->pipe(function ($properties) {
            $settings = [];

            $properties->each(function ($property) use (&$settings) {
                Arr::set($settings, $property->key, $property->value);
            });

            return self::make($settings);
        });
    }
}
