<?php

namespace Tests\Unit;

use Artisan\Settings\Settings;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    function test_get_settings_from_the_database()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->query->where->exists')->andReturn(true)->once();
        $model->shouldReceive('properties->query->where->value')->andReturn('dim')->once();

        $settings = new Settings($model);

        $this->assertEquals('dim', $settings->get('theme'));
    }
}
