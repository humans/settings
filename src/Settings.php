<?php

namespace Humans\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Settings
{
    use Concerns\CastsProperties,
        Concerns\ToDatabase;

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
    protected $settings = [];

    /**
     * Create a new settings instance without the defaults.
     *
     * @param  array  $settings
     * @return \Humans\Settings\Settings
     */
    public static function withoutDefaults($settings = [])
    {
        return (new static)->setDefaults([])->setSettings($settings);
    }

    /**
     * Create a settings instance while checking for defaults.
     *
     * @param  array  $settings
     * @param  boolean  $hasDefaults
     * @return \Humans\Settings\Settings
     */
    public function __construct($settings = [])
    {
        $this->settings = $this->parse($settings);
    }

    /**
     * Set new settings to parse.
     *
     * @param  array  $settings
     * @return \Humans\Settings\Settings
     */
    public function setSettings($settings)
    {
        $this->settings = $this->parse($settings);

        return $this;
    }

    /**
     * Set the defaults.
     *
     * @param  array  $defaults
     * @return \Humans\Settings\Settings
     */
    public function setDefaults($defaults = [])
    {
        $this->defaults = $defaults;

        return $this;
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
    public function all()
    {
        return $this->settings->toArray();
    }

    /**
     * Magically get the settings through nested public attributes.
     *
     * @param  string  $key
     * @return mixed|\Humans\Settings\Proxy
     */
    public function __get($key)
    {
        return Proxy::settings($this->get($key));
    }

    /**
     * Build the settings file from the database and merge the defaults. This
     * also applies the cast with the default values.
     *
     * @param  array  $settings
     * @return \Illuminate\Support\Collection
     */
    public function parse($settings = [])
    {
        return Collection::make(
            Arr::dot($this->defaults)
        )->merge(
            Arr::dot($settings)
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
}
