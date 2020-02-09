<?php

namespace Tests\Unit;

use Tests\TestCase;
use Humans\Settings\Settings;

class SettingsTest extends TestCase
{
    function test_get_all_of_the_settings()
    {
        $settings = new DefaultSettings([
            'appearance' => [
                'theme' => 'light',
                'accent' => 'green',
            ],
        ]);

        $this->assertEquals([
            'appearance' => [
                'theme' => 'light',
                'accent' => 'green',
                'font_size' => '16px',
            ],
        ], $settings->all());
    }

    function test_get_settings_with_fallback()
    {
        $settings = new DefaultSettings;

        $this->assertEquals([
            'appearance' => [
                'theme' => 'dark',
                'accent' => 'blue',
                'font_size' => '16px',
            ],
        ], $settings->all());
    }

    function test_get_a_single_setting()
    {
        $settings = new Settings([
            'appearance' => [
                'theme' => 'light',
                'accent' => 'green',
            ],
        ]);

        $this->assertEquals(
            'light',
            $settings->get('appearance.theme')
        );
    }

    function test_get_a_single_setting_with_a_fallback()
    {
        $settings = new Settings;

        $this->assertEquals(
            'fallback',
            $settings->get('huh', 'fallback')
        );
    }

    function test_get_settings_magically()
    {
        $settings = new MagicSettings;

        $this->assertEquals('active', $settings->status);

        $this->assertEquals(['a', 'b', 'c', 'd'], $settings->letters);

        $this->assertEquals('dark', $settings->appearance->theme);
    }

    function test_ignore_defaults()
    {
        $settings = DefaultSettings::withoutDefaults([
            'hello' => 'world',
        ]);

        $this->assertEquals([
            'hello' => 'world',
        ], $settings->all());
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

class DefaultSettings extends Settings
{
    protected $defaults = [
        'appearance' => [
            'theme' => 'dark',
            'accent' => 'blue',
            'font_size' => '16px',
        ],
    ];
}