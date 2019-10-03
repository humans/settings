<?php

namespace Tests\Unit;

use Artisan\Settings\Settings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;
use Mockery;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    function test_get_all_of_the_settings()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->get')->once()->andReturn(
            Collection::make([
                new Fluent(['key' => 'appearance.theme',  'value' => 'light']),
                new Fluent(['key' => 'appearance.accent', 'value' => 'green']),
            ])
        );

        $settings = (new Settings($model))->all()->toArray();

        $this->assertEquals([
            'appearance' => [
                'theme' => 'light',
                'accent' => 'green',
            ],
        ], $settings);
    }

    function test_get_settings_with_fallback()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->get')->once()->andReturn(
            Collection::make()
        );

        $settings = (new UserSettings($model))->all()->toArray();

        $this->assertEquals([
            'appearance' => [
                'theme' => 'dark',
                'accent' => 'blue',
            ],
        ], $settings);
    }
}

class UserSettings extends Settings
{
    protected $defaults = [
        'appearance' => [
            'theme' => 'dark',
            'accent' => 'blue',
        ],
    ];
}