<?php

namespace Artisan\Settings;

class Settings
{
    protected $defaults = [];

    protected $casts = [];

    protected $model;

    public function setModel($model)
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
            ->value('value') ?? $default;
    }
}
