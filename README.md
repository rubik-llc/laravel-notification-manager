# Laravel notification manager

![Platform](https://img.shields.io/badge/platform-laravel-red)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/rubik-llc/laravel-notification-manager.svg)](https://packagist.org/packages/rubik-llc/laravel-notification-manager)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/rubik-llc/laravel-notification-manager/run-tests?label=tests)](https://github.com/rubik-llc/laravel-notification-manager/actions/workflows/run-tests.yml)
[![Check & fix styling](https://img.shields.io/github/workflow/status/rubik-llc/laravel-notification-manager/Check%20&%20fix%20styling?label=check%20and%20fix%20styling)](https://github.com/rubik-llc/laravel-notification-manager/actions/workflows/php-cs-fixer.yml)
[![GitHub](https://img.shields.io/github/license/rubik-llc/laravel-notification-manager)](LICENSE.md)
[![GitHub all releases](https://img.shields.io/packagist/dt/rubik-llc/laravel-notification-manager)](https://packagist.org/packages/rubik-llc/laravel-notification-manager/stats)

Manage notifications easily in your Laravel app.

## Features

- Manage subscribers
- Manage notification priorities
- Manage muted notifications (mute, unmute)
- Classifies notifications according to the way they appear
- Classifies notifications according where they appear
- Mark as seen

## Installation

You can install the package via composer:

```bash
composer require rubik-llc/laravel-notification-manager
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="notification-manager-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="notification-manager-config"
```

This is the contents of the published config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Subscribable notifications
    |--------------------------------------------------------------------------
    |
    | All notifications which we would like to be subscribable must be placed here.
    | If artisan command is used to create subscribable notification this will be autofilled
    |
    | Example:
    |   'subscribable_notifications' => [
    |       'order.accepted' => Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedSubscribableNotification,
    |       'order.rejected' => Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderRejectedSubscribableNotification,
    |   ],
    */

    'subscribable_notifications' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Channels
    |--------------------------------------------------------------------------
    |
    | All available channels must be placed here
    | A notification will be sent to all these channels if model is subscribed to all channel("*").
    | Example
    | 'channels' => "database,broadcast",
    |
    */

    'channels' => "",


];
```

## Usage

1. Use our “Notifiable” trait in all models you wish to send notifications(most cases Users)
    - This can be done by changing the import from “use Illuminate\Notifications\Notifiable;” to “use
      Rubik\NotificationManager\Traits\Notifiable;”, and also if not yet use the trait “use Notifiable”;. Your model
      should look like
2. Use HasNotificationSubscription trait in all models you wish to send notifications
3. Create subscribale notification by using “-s” flag in the default artisan command to create a notification.
4. Add this notification to config file

```bash 
php artisan make:notification SubscribaleNotification -s
```

From now on everything is the same as a normal notification. Below you can see how your Model and Notification should
look like:

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

If you want to convert a notification to a subscribable notification all you have to do is add SubscribaleNotification
Contract and implement all methods required, and use SubscribaleNotification trait. Do not forget to add this notifion
to your config. Your notification should look like:

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

All changes will affect only future notifications, and if not specified different changes will affect the desired
notification of the authenticated user. Will be explained below.

## Subscribers/Unsubscribe

### Subscribe to a notification:

```php
NotificationManager::subscribe(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::subscribe();
```

### Unsubscribe to a notification:

```php
NotificationManager::unsubscribe(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::unsubscribe();
```

### Subscribe a user to a notification:

```php
NotificationManager::for(User::find(1))->subscribe(OrderApprovedSubscribableNotification::class);
```

### Unsubscribe a user to a notification:

```php
NotificationManager::for(User::find(1))->unsubscribe(OrderApprovedSubscribableNotification::class);
```

### Subscribe to all notifications:

```php
NotificationManager::subscribeAll();
```

### Unsubscribe to all notifications:

```php
NotificationManager::unsubscribeAll();
```

### Subscribe a user to all notifications:

```php
NotificationManager::for(User::find(1))->subscribeAll();
```

### Unsubscribe a user to all notifications:

```php
NotificationManager::for(User::find(1))->unsubscribeAll();
```

### Send notification to all subscribers:

Instead of using:

```php
Notification::send(User::subscribers()->get(),new OrderApprovedSubscribableNotification($payload));
```

Use:

```php
OrderApprovedSubscribableNotification::sendToSubscribers($payload)
```

Everything passed to send to subscriber will be passed to notification constructor.

## Priority

### Set priority to a notification:

```php
NotificationManager::prioritize(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::prioritize();
```

### Unset priority to a notification:

```php
NotificationManager::trivialize(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::trivialize();
```

### Set priority to a notification as a user:

```php
NotificationManager::for(User::find(1))->prioritize(OrderApprovedSubscribableNotification::class);
```

### Unset priority to a notification as a user:

```php
NotificationManager::for(User::find(1))->trivialize(OrderApprovedSubscribableNotification::class);
```

## Mute

### Mute a notification:

```php
NotificationManager::mute(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::mute();
```

### Unmute a notification:

```php
NotificationManager::unmute(OrderApprovedSubscribableNotification::class);
```

or:

```php
OrderApprovedSubscribableNotification::unmute();
```

### Mute a notification for a user:

```php
NotificationManager::for(User::find(1))->mute(OrderApprovedSubscribableNotification::class);
```

### Unmute a notification for a user:

```php
NotificationManager::for(User::find(1))->unmute(OrderApprovedSubscribableNotification::class);
```

## Alert type

### Set/update alert type:

```php
NotificationManager::alertType(OrderApprovedSubscribableNotification::class, NotificationAlertType::BANNER);
```

or:

```php
OrderApprovedSubscribableNotification::alertType(NotificationAlertType::BANNER);
```

### Available alert types:

```php
NotificationAlertType::NOTIFICATION_CENTER;
NotificationAlertType::BANNER;
NotificationAlertType::LOCK_SCREEN;
```

those are only values and in no way they represent a logic. You can use those values to classify notifications.

## Preview type

### Set/update preview type:

```php
NotificationManager::previewType(OrderApprovedSubscribableNotification::class, NotificationPreviewType::ALWAYS);
```

or:

```php
OrderApprovedSubscribableNotification::previewType(NotificationPreviewType::ALWAYS);

```

### Available alert types:

```php
NotificationPreviewType::ALWAYS;
NotificationPreviewType::WHEN_UNLOCKED;
NotificationPreviewType::NEVER;
```

## Seen

### Mark as seen

```php
User::find(1)->notifications()->markAsSeen();
```

or:

```php
DatabaseNotification::all()->markAsSeen();
```

or:

```php
$user=User::find(1);

$user->unseenNotifications()->update(['seen_at' => now()]);
```

or:

```php
$user = App\Models\User::find(1);
 
foreach ($user->unseenNotifications as $notification) {
    $notification->markAsSeen();
}
```

### Mark as unseen

```php
User::find(1)->notifications()->markAsUnseen();
```

or:

```php
DatabaseNotification::all()->markAsUnseen();
```

or:

```php
$user->seenNotifications()->update(['seen_at' => null]);
```

or:

```php
$user = App\Models\User::find(1);
 
foreach ($user->seenNotifications as $notification) {
    $notification->markAsUnseen();
}
```

### Get all seen notifications:

```php
User::find(1)->seenNotifications();
```

### Get all unseen notifications:

```php
User::find(1)->unseenNotifications();
```

### Get all prioritized notifications:

```php
User::find(1)->prioritizedNotifications();
```

### Get all trivialized notifications:

```php
User::find(1)->trivializedNotifications();
```

### Get all muted notifications:

```php
User::find(1)->mutedNotifications();
```

### Get all unmuted notifications:

```php
User::find(1)->unmutedNotifications();
```

### Get all notifications with alert type set to 'notification-center':

```php
User::find(1)->alertNotificationCenterNotifications();
```

### Get all notifications with alert type set to 'banner':

```php
User::find(1)->alertBannerNotifications();
```

### Get all notifications with alert type set to 'lock-screen':

```php
User::find(1)->alertLockScreenNotifications();
```

### Get all notifications with preview type set to 'always':

```php
User::find(1)->previewAlwaysNotifications();
```

### Get all notifications with preview type set to 'when-unlocked':

```php
User::find(1)->previewWhenUnlockedNotifications();
```

### Get all notifications with preview type set to 'never':

```php
User::find(1)->previewNeverNotifications();
```

### Check if a notification is seen:

```php
User::find(1)->notifications()->first()->seen();
```

### Check if a notification is unseen:

```php
User::find(1)->notifications()->first()->unseen();
```

### Check if a notification is prioritised:

```php
User::find(1)->notifications()->first()->seen();
```

### Check if a notification is trivialised:

```php
User::find(1)->notifications()->first()->triavilized();
```

### Check if a notification is muted:

```php
User::find(1)->notifications()->first()->muted();
```

### Check if a notification is unmuted:

```php
User::find(1)->notifications()->first()->unmuted();
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
