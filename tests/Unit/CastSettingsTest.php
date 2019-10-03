<?php

namespace Tests\Unit;

use Artisan\Settings\Settings;
use Tests\TestCase;

class CastSettingsTest extends TestCase
{
    function test_cast_boolean()
    {
        $settings = new CastSettings;

        $this->assertTrue($settings->get('notifications.sms'));
    }

    function test_use_custom_cast_methods()
    {
        $settings = new CastSettings;

        $this->assertEquals(['54321', '1234'], $settings->get('some_value'));
    }
}

class CastSettings extends Settings
{
    protected $defaults = [
        'notifications' => [
            'sms' => '1',
        ],
        'some_value' => '54321',
    ];

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
