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
        $settings = (new BasicSettings)->all();

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

    function test_persist_the_settings()
    {
        $this->markTestIncomplete("Not sure how to test this one yet");
    }

    function test_get_settings_magically()
    {
        $settings = new MagicSettings;

        $this->assertEquals('active', $settings->status);

        $this->assertEquals(['a', 'b', 'c', 'd'], $settings->letters);

        $this->assertEquals('dark', $settings->appearance->theme);
    }
}

class MagicSettings extends Settings
{
    protected $defaults = [
        'appearance' => [
            'theme' => 'dark',
            'accent' => 'blue',
        ],
        'letters' => ['a', 'b', 'c', 'd'],
        'status' => 'active',
    ];
}

class BasicSettings extends Settings
{
    protected $defaults = [
        'appearance' => [
            'theme' => 'dark',
            'accent' => 'blue',
        ],
    ];
}