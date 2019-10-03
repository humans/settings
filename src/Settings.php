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

    /**
     * The colelctive settings for the model.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $settings;

    /**
     * Create a settings instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Artisan\Settings\Settings
     */
    public function __construct(Model $model)
    {
        $this->settings = $this->settings($model);
    }

    /**
     * Build the settings file from the database and merge the defaults. This
     * also applies the cast with the default values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    private function settings(Model $model)
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

            return [$key => $this->castProperty($key, $value)];
        })->pipe(function ($properties) {
            $settings = [];

            $properties->each(function ($value, $key) use (&$settings) {
                Arr::set($settings, $key, $value);
            });

            return Collection::make($settings);
        });
    }

    /**
     * Get the settings by key with a default fallback.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->settings, $key, $default);
    }

    /**
     * Get the settings array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->settings->toArray();
    }
}
