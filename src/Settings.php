<?php

namespace Artisan\Settings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class Settings
{
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
     * The model to get the properties from.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Persist the settings to the database.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return void
     */
    // public function set($attribute, $value)
    // {
    //     $this->query()->updateOrCreate([
    //         'key' => $attribute,
    //     ], [
    //         'value' => $value,
    //     ]);
    // }

    /**
     * Start a new query from the settings table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    private function query()
    {
        return $this->model->properties();
    }

    /**
     * Get the model settings and fall back to either a user defined parameters
     * of the user settings file.
     *
     * @param  string  $attribute
     * @param  mixed  $default
     * @return mixed
     */
    // public function get($attribute, $default = null)
    // {
    //     if ($this->query()->where('key', $attribute)->exists()) {
    //         return $this->query()->where('key', $attribute)->value('value');
    //     }

    //     return $default ?? Arr::get($this->defaults, $attribute);
    // }

    /**
     * Get all of the settings with the default fallbacks.
     *
     * @return \Artisan\Settings\SettingsCollection
     */
    public function all()
    {
        return SettingsCollection::make($this->defaults)->map(function ($value, $key) {
            return new Fluent(compact('key', 'value'));
        })->merge(
            $this->query()->get()->map(function ($property) {
                $property->value = $this->cast($property);

                return $property;
            })
        )->unflatten();
    }

    /**
     * Cast the value if it exists.
     *
     * @param  string  $property
     * @return mixed
     */
    protected function cast($property)
    {
        if (! $cast = Arr::get($this->casts, $property->key)) {
            return $property->value;
        }

        $method = 'cast' . Str::studly($cast);

        if (! method_exists($this, $method)) {
            return $property->value;
        }

        return $this->{$method}($property->value);
    }

    /**
     * Cast the value into a boolean.
     *
     * @return boolean
     */
    protected function castBoolean($value)
    {
        return $value !== '0';
    }
}
