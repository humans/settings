<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Fluent;
use Mockery;
use Artisan\Settings\Settings;

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

        $settings = (new Settings($model))->all();

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

        $settings = (new UserSettings($model))->all();

        $this->assertEquals([
            'appearance' => [
                'theme' => 'dark',
                'accent' => 'blue',
            ],
        ], $settings);
    }

    function test_get_a_single_setting()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->get')->once()->andReturn(
            Collection::make([
                new Fluent(['key' => 'appearance.theme',  'value' => 'light']),
                new Fluent(['key' => 'appearance.accent', 'value' => 'green']),
            ])
        );

        $settings = new Settings($model);

        $this->assertEquals(
            'light',
            $settings->get('appearance.theme')
        );
    }

    function test_cast_values()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->get')->once()->andReturn(
            Collection::make([
                new Fluent(['key' => 'notifications.sms',  'value' => '1']),
            ])
        );

        $settings = (new UserSettings($model));

        $this->assertTrue($settings->get('notifications.sms'));
    }

    function test_use_custom_cast_methods()
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('properties->get')->once()->andReturn(
            Collection::make([
                new Fluent(['key' => 'date', 'value' => '2019-01-01']),
            ])
        );

        $settings = (new UserSettings($model));

        $this->assertEquals('January 01, 2019', $settings->get('date'));
    }

    function test_persist_the_settings()
    {
        $this->markTestIncomplete("Not sure how to test this one yet");
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

    protected $casts = [
        'notifications' => [
            'sms' => 'boolean',
        ],
        'date' => 'date:F d, Y',
    ];

    protected function asDate($date, $format)
    {
        return date($format, strtotime($date));
    }
}