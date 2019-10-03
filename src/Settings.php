<?php

namespace Artisan\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function set($attribute, $value)
    {
        $this->model->properties()->updateOrCreate([
            'key' => $attribute,
        ], [
            'value' => $value,
        ]);
    }

    public function get($attribute, $default = null)
    {
        return $this
            ->model
            ->properties()
            ->where('key', $attribute)
            ->value('value') ?? $default ?? Arr::get($this->defaults, $attribute);
    }

    public function all()
    {
        $settings = [];

        Collection::make($this->defaults)->map(function ($value, $key) {
            return new Fluent(compact('key', 'value'));
        })->merge(
            $this->model->properties->map(function ($property) {
                $property->value = $this->cast($property);

                return $property;
            })
        )->each(function ($property) use (&$settings) {
            Arr::set($settings, $property->key, $property->value);
        });

        return $settings;
    }

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

    protected function castBoolean($value)
    {
        return $value !== '0';
    }
}
