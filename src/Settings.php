<?php

namespace Artisan\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Settings
{
    use Concerns\CastsProperties;

    /**
     * The default settings if there are no settings found in the database.
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * The attribtes that will be casted.
     *
     * @var array
     */
    protected $casts = [];

    protected $settings = [];

    public function __construct(Model $model)
    {
        $this->settings = $this->settings($model);
    }

    /**
     * Get all of the settings with the default fallbacks.
     *
     * @return \Illuminate\Support\Collection
     */
    private function settings($model)
    {
        return Collection::make(
            Arr::dot($this->defaults)
        )->merge(
            $model->properties()->get()->mapWithKeys(function ($property) {
                return [$property->key => $property->value];
            })
        )->mapWithKeys(function ($value, $key) {
            if (! $this->hasCast($key)) {
                return [$key => $value];
            }

            return [$key => $this->cast($key, $value)];
        })->pipe(function ($properties) {
            $settings = [];

            $properties->each(function ($value, $key) use (&$settings) {
                Arr::set($settings, $key, $value);
            });

            return Collection::make($settings);
        });
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->settings, $key, $default);
    }

    public function toArray()
    {
        return $this->settings->toArray();
    }
}
