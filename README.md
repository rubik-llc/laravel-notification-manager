# Laravel notification manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rubik-llc/laravel-notification-manager.svg?style=flat-square)](https://packagist.org/packages/rubik-llc/laravel-notification-manager)
[![GitHub Tests Action Status](https://github.com/rubik-llc/laravel-notification-manager/workflows/run-tests/badge.svg)](https://github.com/rubik-llc/laravel-notification-manager/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://github.com/rubik-llc/laravel-notification-manager/workflows/Check%20&%20fix%20styling/badge.svg)](https://github.com/rubik-llc/laravel-notification-manager/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rubik-llc/laravel-notification-manager.svg?style=flat-square)](https://packagist.org/packages/rubik-llc/laravel-notification-manager)

Manage notifications easily in your Laravel app.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-notification-manager.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-notification-manager)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require rubik-llc/laravel-notification-manager
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-notification-manager-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-notification-manager-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-notification-manager-views"
```


## Features
- Manage subscribers
- Manage notification priorities
- Manage muted notifications (mute, unmute)
- Classifies notifications according to the way they appear
- Classifies notifications according where they appear
- Mark as seen

All above can be done using NotificationManager facade or Notification class itself.

## Usage

1. Use our “Notifiable” trait in all models you wish to send notifications(most cases Users)
    - This can be done by changing the import from “use Illuminate\Notifications\Notifiable;” to “use Rubik\NotificationManager\Traits\Notifiable;”, and also if not yet use the trait “use Notifiable”;. Your model should look like
2. Use HasNotificationSubscription trait in all models you wish to send notifications
3. Create subscribale notification by using “-s” flag in the default artisan command to create a notification.
```bash 
php artisan make:notificartion SubscribaleNotification -s
```

From now on everything is the same as a normal notification.
Below you can see how your Model and Notification should look like:

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Rubik\NotificationManager\Contracts\SubscribableNotificationContract;
use Rubik\NotificationManager\Traits\SubscribableNotification;

class TestNotification extends Notification implements SubscribableNotificationContract
{
    use Queueable, SubscribableNotification;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Subscribable type based on the name in config file
     *
     * @return string
     */
    public static function subscribableNotificationType(): string
    {
        return 'test';
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
```
If you want to convert a notification to a subscribable notification all you have to do is add SubscribaleNotification Contract and implement all methods required, and use SubscribaleNotification trait. Your notification should look like:
```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Rubik\NotificationManager\Contracts\SubscribableNotificationContract;
use Rubik\NotificationManager\Traits\SubscribableNotification;

class TestNotification extends Notification implements SubscribableNotificationContract
{
    use Queueable, SubscribableNotification;

    //Your code here

    /**
     * Subscribable type based on the name in config file
     *
     * @return string
     */
    public static function subscribableNotificationType(): string
    {
        return 'test';
    }
}
```
All changes will affect only future notifications, and if not specified different changes will affect the desired notification of the authenticated user. Will be explained below.

## Subscribers/Unsubscribe
### Subscribe to a notification:
```php
CODE
```
or:
```php
CODE
```

### Unsubscribe to a notification:
```php
CODE
```
or:
```php
CODE
```

### Subscribe a user to a notification:
```php
CODE
```
or:
```php
CODE
```

### Unsubscribe a user to a notification:
```php
CODE
```
or:
```php
CODE
```

### Send notification to all subscribers:
```php
CODE
```

## Priority
### Set priority to a notification:
```php
CODE
```
or:
```php
CODE
```

### Unset priority to a notification:
```php
CODE
```
or:
```php
CODE
```

### Set priority to a notification as a user:
```php
CODE
```
or:
```php
CODE
```

### Unset priority to a notification as a user:
```php
CODE
```
or:
```php
CODE
```

## Mute
### Mute:
```php
CODE
```
or:
```php
CODE
```

### Unmute:
```php
CODE
```
or:
```php
CODE
```

## Alert type
### Set/update alert type:
```php
CODE
```
or:
```php
CODE
```

### Available alert types:
- NOTIFICATION_CENTER
    - value -> notification-center
- BANNER
    - value -> banner
- LOCK_SCREEN
    - value -> lock-screen

## Preview type
### Set/update preview type:
```php
CODE
```
or:
```php
CODE
```

### Available alert types:
- ALWAYS
    - value -> always
- WHEN_UNLOCKED
    - value -> when-unlocked
- NEVER
    - value -> never

## Seen
### Mark as seen
```php
CODE
```
or:
```php
CODE
```

### Mark as unseen
```php
CODE
```
or:
```php
CODE
```

### Get all seen notifications:
```php
CODE
```
### Get all unseen notifications:
```php
CODE
```

### Check if a notification is seen:
```php
CODE
```

### Check if a notification is unseen:
```php
CODE
```

### Check if a notification is prioritised:
```php
CODE
```

### Check if a notification is trivialised:
```php
CODE
```

### Check if a notification is muted:
```php
CODE
```

### Check if a notification is unmuted:
```php
CODE
``` 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Yllndrit Beka](https://github.com/yllndritb)
- [Rron Nela](https://github.com/rronik)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
