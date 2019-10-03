<?php

namespace Artisan\Settings;

class Proxy
{
    /**
     * Create a new proxy from the settings array.
     *
     * @param  array  $array
     * @return \Artisan\Settings\Proxy
     */
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Get value or a proxy.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function settings($value)
    {
        if (static::isAssociativeArray($value)) {
            return new static($value);
        }

        return $value;
    }

    /**
     * Check if the given value is an associative array.
     *
     * @param  mixed  $value
     * @return boolean
     */
    public static function isAssociativeArray($value)
    {
        if (! is_array($value) || count($value) === 0) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }

    /**
     * Create a new proxy or return the value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = $this->array[$key];

        if (! static::isAssociativeArray($value)) {
            return $value;
        }

        return new Proxy($key, $value);
    }
}
