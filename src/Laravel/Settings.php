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

    public function __call($method, array $parameters = [])
    {
        return $this->forwardCallTo($this->settingsBag, $method, $parameters);
    }
}
