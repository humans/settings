# Settings Property Bag

[![Latest Stable Version](https://poser.pugx.org/humans/settings/v/stable)](https://packagist.org/packages/humans/settings) [![License](https://poser.pugx.org/humans/settings/license)](https://packagist.org/packages/humans/settings)

This is a property bag for handling settings objects. This also has integration with Laravel database models out of the box. 

## Installation

You can install the package via composer:

```bash
composer require humans/settings
```

## Usage

A settings bag is a class where you can store your settings that can have fallback values if no explicit values where set. (i.e. persistence)

```php
use Humans\Settings\Setings;

class UserSettings extends Settings
{
    protected $defaults = [
        'notifications' => [
            'sms' => true,
            'email' => true,
        ],
    ];
}
```

There are two different ways to get a value from the settings bag.

`get($settings, $default = null)`

```php
(new UserSettings)->get('notifications'); 
// => ['sms' => true, 'email' => true]

(new UserSettings)->get('notifications.sms');
// => true

(new UserSettings)->get('notifications.push', false);
// => false
```

Another is chaining public properties.

```php
(new UserSettings)->notifications;
// => ['sms' => true, 'email' => true]

(new UserSettings)->notifications->sms;
// => true
```

To get all the values from the settings bag.

```php
(new UserSettings)->all();
```

### Overriding the default values

Now that we can set values, we want to apply the persisted data to our default settings.

```php
$userSettingsFromDatabase = [
    'notifications' => [
        'sms' => false,
	  ]
];

$settings = new UserSettings($userSettingsFromDatabase);

$settings->all();
# => [
#        'notifications' => [
#            'sms'   => false, <-- using the values from the database,
#            'email' => true,
#        ],
#    ]
```

Somtimes, it's a hassle storing array values in the database:

- The database might not support it.
- We don't want to do an overly complex database via relational key value stuff.

You can instead store your nested settings values via **dot notation** and this package will destructure the value into a nested array.

```php
$userSettingsFromDatabase = [
    'notifications.sms' => false,
];

$settings = new UserSettings($userSettingsFromDatabase);

$settings->all();
# => [
#        'notifications' => [
#            'sms'   => false, <-- assigned via dot notation.
#            'email' => true,
#        ],
#    ]
```

## Casting

There are times that the values that we pull out the database don't map to the actual data types we want them to be. For that we have a `$casts` attribute to handle the mapping for all the primitives we need.

```php
class UserSettings extends Settings
{
    protected $defaults = [
        'notifications' => [
            'sms'   => 1,
            'email' => 1,
        ],
    ];
  
    protected $casts = [
        'notifications.sms'   => 'boolean',
        'notifications.email' => 'boolean',
    ];
}

(new UserSettings)->get('notifications.sms');
# => true

(new UserSettings)->get('notifications.email');
# => true
```

The only two properties available right now are: `boolean` and `json`.

**BUT DON'T FRET!** You can either help us out by adding more cast implementations or even make your own from the settings class! (The code is the same).

### Adding custom casts

To add custom casts, in your custom settings class (or even a parent class for all your settings classes), create a method prefixed with `as`.

```php
class UserSettings extends Settings
{
    protected $defaults = [
        'age'            => '27',
        'hours_of_sleep' => '8',
    ];
  
    protected $casts = [
        'age' => 'integer',
    ];
  
    protected function asInteger($age)
    {
        return (int) $age;
    }
}

(new UserSettings)->get('age');
# => 27

(new UserSettings)->get('hours_of_sleep');
# => '8'
```

## Laravel Integration

Out of the box, we try to help the setup to a minimal for Laravel projects. After installing via composer, public the config file and migrations.

```bash
php artisan vendor:publish --provider="Humans\Settings\Laravel\ServiceProvider"
```

Run our new settings table migration.

```bash
php artisan migrate
```

To create the settings file:

```
php artisan settings:make UserSettings
```

And finally, add our settings trait to the model. The trait will automatically look for the settings file in the namespace of your config appended with the word `Settings.`

So if we have a `User.php`, by default it will look for the `App\Settings\UserSettings` class.

```php
use Humans\Settings\Laravel\HasSettings;

class User extends Model
{
    use HasSettings;
}

class Workspace extends Model
{
    use HasSettings;
}
```

To change the class location, you can change the `getSettingsClass` method in your model.

```php
use Humans\Settings\Laravel\HasSettings;

class User extends Model
{
    use HasSettings;

    protected function getSettingsClass()
    {
        return \App\Models\Settings\AccountSettings::class;
    }
}

use Humans\Settings\Laravel\HasSettings;

class Workspace extends Model
{
    use HasSettings;

    public function getSettingsClass()
    {
        return \App\Models\Settings\AccountSettings::class;
    }
}
```

With that, you can now access your values via the `settings` public property.

```php
User::first()->settings->notifications->sms
# => true
```

### Saving to the database

To save a single value to the database.

```
User::first()->settings->set('notifications.sms', false);
```

To save multiple values at the same time:

```php
User::first()->settings->update([
    'notifications' => [
        'sms'   => false,
        'email' => false,
    ],
]);

# or using dot notation
User::first()->settings->update([
    'notifications.sns'   => false,
    'notifications.email' => false,
]);
```