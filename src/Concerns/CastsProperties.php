<?php

namespace Artisan\Settings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CastsProperties
{
    /**
     * Check if the key is castable.
     *
     * @param  string  $key
     * @return boolean
     */
    protected function hasCast($key)
    {
        return Arr::has($this->casts, $key);
    }

    /**
     * Cast the value with some magic.
     *
     * @param  string  $key
     * @param  string  $value
     * @return mixed
     */
    protected function castProperty($key, $value)
    {
        [$method, $argument] = $this->parseCastMethod(
            Arr::get($this->casts, $key)
        );

        if (! method_exists($this, $method)) {
            throw new \Exception("Cast method [{$method}] is undefined.");
        }

        return call_user_func([$this, $method], $value, $argument);
    }

    /**
     * Get the method name and the cast parameter.
     *
     * @param  string  $name
     * @return array
     */
    protected function parseCastMethod($name)
    {
        $segments = explode(':', $name);

        if (count($segments) === 1) {
            return [$this->getCastMethodName($name), null];
        }

        [$type, $argument] = $segments;
        return [$this->getCastMethodName($type), $argument];
    }

    /**
     * Get the cast method name.
     *
     * @param  string  $type
     * @return string
     */
    protected function getCastMethodName($type)
    {
        return Str::camel('as_' . $type);
    }

    /**
     * Cast the value as a boolean.
     *
     * @return bool
     */
    protected function asBoolean($value)
    {
        return (bool) $value;
    }
}
