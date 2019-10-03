<?php

namespace Tests\Unit;

use Artisan\Settings\Settings;
use Mockery;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    function test_get_settings_from_the_database()
    {
        $model = Mockery::mock();
        $model->shouldReceive('properties->where->value')->andReturn('dim');

        $settings = new Settings($model);

        $this->assertEquals('dim', $settings->get('theme'));
    }
}
