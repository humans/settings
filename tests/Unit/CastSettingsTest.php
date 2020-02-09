<?php

namespace Tests\Unit;

use Humans\Settings\Settings;
use Tests\TestCase;

class CastSettingsTest extends TestCase
{
    function test_cast_boolean()
    {
        $settings = new CastSettings([
            'notifications' => [
                'sms' => '1',
            ],
        ]);

        $this->assertTrue($settings->get('notifications.sms'));
    }

    function test_use_custom_cast_methods()
    {
        $settings = new CastSettings([
            'some_value' => '54321',
        ]);

        $this->assertEquals(['54321', '1234'], $settings->get('some_value'));
    }

    function test_prepare_the_settings_for_the_database()
    {
        $settings = new ToDatabaseSettings([
            'list' => ['Bulbasaur', 'Charmander', 'Blastoise'],
        ]);

        $this->assertEquals(
            ['list' => json_encode(['Bulbasaur', 'Charmander', 'Blastoise'])],
            $settings->toDatabase()
        );
    }
}

class CastSettings extends Settings
{
    protected $casts = [
        'notifications' => [
            'sms' => 'boolean',
        ],
        'some_value' => 'custom_cast_with_parameter:1234',
    ];

    protected function asCustomCastWithParameter($value, $params)
    {
        return [$value, $params];
    }
}

class ToDatabaseSettings extends Settings
{
    protected $casts = [
        'list' => 'json',
    ];
}