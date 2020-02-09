<?php

namespace Humans\Settings\Laravel;

use Humans\Settings\Settings as SettingsBag;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Settings
{
    use ForwardsCalls;

    protected $settingsBag;
    protected $model;

    public function __construct(SettingsBag $settingsBag, $model)
    {
        $this->settingsBag = $settingsBag;
        $this->model = $model;
    }

    /**
     * Save the settings value to the database.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->update([$key => $value]);
    }

    /**
     * Save multiple values at the same time.
     *
     * @param array  $settingss
     * @return void
     */
    public function update($settings)
    {

        Collection::make(
            Arr::dot(
                call_user_func(
                    [get_class($this->settingsBag), 'withoutDefaults'], $settings
                )->toDatabase()
            )
        )->each(function ($value, $key) {
            $this->model->properties()->updateOrCreate(['key' => $key], ['value' => $value]);
        });
    }


    /**
     * Forward unimplemented methods to the settings bag.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters = [])
    {
        return $this->forwardCallTo($this->settingsBag, $method, $parameters);
    }

    /**
     * Forward public attribute calls to the settings bag.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->settingsBag->{$key};
    }
}
