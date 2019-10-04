<?php

namespace Artisan\Settings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait PreparesProperties
{
    public function toDatabase()
    {
        $properties = $this->settings->toArray();

        Collection::make(
            Arr::dot($this->casts)
        )->each(function ($type, $name) use (&$properties) {
            if (! Arr::has($properties, $name)) {
                return;
            }

            Arr::set($properties, $name, $this->prepare($name, $type));
        });

        return $properties;
    }

    protected function prepare($name, $type)
    {
        $value = Arr::get($this->settings, $name);

        switch ($type) {
            case 'array':
            case 'json':
                return json_encode($value);
        }

        return $value;
    }
}
