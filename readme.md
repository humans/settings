# Laravel Settings

This package helps to apply settings to models within a Laravel application. This uses the **property bag** pattern to have a single table for your all your model settings.

```php
class UserSettings extends Humans\Settings\Settings
{
    protected $defaults = [
        'notifications' => [
            'sms' => true,
            'email' => true,
        ],
    ];
}

User::first()->settings->get('notifications.sms'); // true
User::first()->settings->get('notifications.push', false); // false
User::first()->settings->notifications->email // true
```

## Installation
Install the package via composer.

```sh
composer require humans/laravel-settings
```

Next up is we'll publish the config.

```sh
php artisan vendor:publish --provider="Humans\Settings\ServiceProvider"
```

Migrate the settings table.

```sh
php artisan migrate
```

## Usage
Create a settings file for your model.

```sh
php artisan make:settings UserSettings
```

Add the `Humans\Settings\HasSettings` trait to the model.

```php
use Humans\Settings\HasSettings;

class User extends Model
{
    use HasSettings;
}

class Workspace extends Model
{
    use HasSettings;
}
```

By default, this will guess the class name + settings in the settings namespace. To customize this, override the `getSettingsClass` in the model

```php
class User extends Model
{
    use HasSettings;

    public function getSettingsClass()
    {
        return \App\Models\Settings\AccountSettings::class;
    }
}

class Workspace extends Model
{
    use HasSettings;

    public function getSettingsClass()
    {
        return \App\Models\Settings\AccountSettings::class;
    }
}
```


## `get($key, $default = null)`
When pulling in settings from the database, take note that if the setting doesn't exist, the function will return null.

With this method, we can pull in our settings.

```php
$user->settings()->get('notification.sms');
```
Or add a default value when it isn't found!

```php
$user->settings()->get('notification.sms', $default = true);
```

## `set($key, $value)`
```php
$user->settings->set('timezone', 'Asia/Manila');
```

all()
When fetching all of the results, it will merge all the defaults if a Settings class exists for your model. See below for more context.

```php
$user->settings->all();
```

Any dot notation based settings will be returned as an array.

```php
// notification.sms => true
// notification.email => false
// timezone => Asia/Manila
[
  'notification' => [
    'sms' => true,
    'email' => false,
  ],
  'timezone' => 'Asia/Manila',
]
```

## Defaults and Casting
There are times that these defaults might get a little too much to keep track, and we also want to make sure that the values are in their proper data type when pulling it from the database.

We can create a settings class to add the defaults, and the value transformations.

```php
<?php

namespace App\Settings;

class UserSettings
{
    protected $defaults = [
        'notification' => [
            'sms' => true,
            'email' => true,
        ],
    ];

    protected $casts = [
        'notifications' => [
            'sms' => 'boolean',
        ]
    ];
}
```

For custom casts, just in case, you can add a new method to apply the cast.

```php
<?php

namespace App\Settings;

class UserSettings
{
    protected $defaults = [
        'notifications' => [
            'sms' => false,
            'email' => true,
        ],
    ];

    protected $casts = [
        'notifications' => [
            'sms' => 'some_custom_cast',
        ]
    ];

    protected function asSomeCustomCast($value)
    {
        return 'transformed value here';
    }
}
```