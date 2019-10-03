<?php

namespace Artisan\Settings\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CastsProperties
{
    private function hasCast($key)
    {
        return Arr::has($this->casts, $key);
    }

    private function cast($key, $value)
    {
        $type = Arr::get($this->casts, $key);
        $method = $this->getCastMethod($type);

        if (! method_exists($this, $method)) {
            throw new \Exception("Cast for {$type} not found.");
        }

        return call_user_func([$this, $method], $value);
    }

    private function getCastMethod($type)
    {
        return Str::camel('cast_' . $type);
    }

    private function castBoolean($value)
    {
        return $value === '1';
    }
}
