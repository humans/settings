<?php

namespace Humans\Settings\Laravel;

use Humans\Settings\Settings as SettingsBag;
use Illuminate\Support\Traits\ForwardsCalls;

class Settings
{
    use ForwardsCalls;

    public function __construct(SettingsBag $settingsBag)
    {
        $this->settingsBag = $settingsBag;
    }

    /**
     * Save the settings value to the database.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function set($key, $value)
    {
    }

    /**
     * Save multiple values at the same time.
     *
     * @param array  $settingss
     * @return mixed
     */
    public function update($settings)
    {
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
