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
        $model->shouldReceive('properties->where->exists')->andReturn(true)->once();
        $model->shouldReceive('properties->where->value')->andReturn('dim')->once();

        $settings = new Settings($model);

        $this->assertEquals('dim', $settings->get('theme'));
    }

    function test_get_settings_with_fallback()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->where->exists')->andReturn(false)->once();

        $settings = new Settings($model);

        $this->assertEquals('light', $settings->get('theme', 'light'));
    }

    function test_get_settings_from_the_class_fallback()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->where->exists')->andReturn(false)->once();

        $settings = new UserSettings($model);

        $this->assertEquals('dark', $settings->get('theme'));
    }
}

class UserSettings extends Settings
{
    protected $defaults = [
        'theme' => 'dark',
    ];
}