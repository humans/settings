<?php

namespace Artisan\Settings;

class Value
{
    /**
     * The value of the settings.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new value instance.
     *
     * @param  mixed  $value
     * @return Value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
}
