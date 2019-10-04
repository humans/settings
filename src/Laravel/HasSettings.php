<?php

namespace Artisan\Settings\Laravel;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use ReflectionClass;

trait HasSettings
{
    /**
     * Get all of the settings of the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function properties()
    {
        return $this->morphMany(Models\Settings::class, 'configurable');
    }

    /*
     * Access the settings through a public variable.
     *
     * @return \Artisan\Settings\Settings
     */
    public function getSettingsAttribute()
    {
        return $this->settings();
    }

    /*
     * Create a new settings class instance.
     *
     * @return \Artisan\Settings\Settings
     */
    public function settings()
    {
        return $this->newSettings(
            $this->properties()->get()->mapWithKeys(function ($property) {
                return [$property->key => $property->value];
            })
        );
    }

    /**
     * Update the settings withoiut forcing persistence on defaults.
     *
     * @param  array  $settings
     * @return void
     */
    public function updateSettings($settings = [])
    {
        Collection::make(
            Arr::dot($this->newSettingsWithoutDefaults($settings)->toDatabase())
        )->each(function ($value, $key) {
            $this->properties()->updateOrCreate(['key' => $key], ['value' => $value]);
        });
    }

    /**
     * Guess the settings class name relative to the current file.
     *
     * @return stringCreate a new settings instance without the defaults.
     */
    protected function getSettingsClass()
    {
        $namespace = (new ReflectionClass($this))->getNamespaceName();

        $settingsNamespace = Config::get('laravel-settings.namespace');

        $class = class_basename($this) . 'Settings';

        return sprintf("%s\%s\%s", $namespace, $settingsNamespace, $class);
    }

    /**
     * Create a new settings instance.
     *
     * @param  array  $settings
     * @return \Artisan\Settigs\Settings
     */
    private function newSettings($settings = [])
    {
        $class = $this->getSettingsClass();

        return new $class($settings);
    }

    /**
     * Create a new settings instance without applying the defaults.
     *
     * @param  array  $settings
     * @return \Artisan\Settigs\Settings
     */
    private function newSettingsWithoutDefaults($settings = [])
    {
        return call_user_func([$this->getSettingsClass(), 'withoutDefaults'], $settings);
    }
}
