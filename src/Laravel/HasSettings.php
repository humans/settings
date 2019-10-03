<?php

namespace Artisan\Settings\Laravel;

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
        $class = $this->getSettingsClass();

        return new $class(
            $this->properties()->get()->mapWithKeys(function ($property) {
                return [$property->key => $property->value];
            })
        );
    }

    /**
     * Guess the settings class name relative to the current file.
     *
     * @return string
     */
    protected function getSettingsClass()
    {
        $namespace = (new ReflectionClass($this))->getNamespaceName();

        $settingsNamespace = Config::get('laravel-settings.namespace');

        $class = class_basename($this) . 'Settings';

        return sprintf("%s\%s\%s", $namespace, $settingsNamespace, $class);
    }
}
