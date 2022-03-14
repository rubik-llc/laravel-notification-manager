This package helps managing Laravel notification in a easy way. And also adds some key features to already existing laravel notifications.

##Features
- Manage subscribers
- Manage notification priorities
- Manage muted notifications (mute, unmute)
- Classifies notifications according to the way they appear
- Classifies notifications according where they appear
- Mark as seen

All above can be done using NotificationManager facade or Notification class itself.

##Usage
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

##Subscribers/Unsubscribe
###Subscribe to a notification:
```php
CODE
```
or:
```php
CODE
```

###Unsubscribe to a notification:
```php
CODE
```
or:
```php
CODE
```

###Subscribe a user to a notification:
```php
CODE
```
or:
```php
CODE
```

###Unsubscribe a user to a notification:
```php
CODE
```
or:
```php
CODE
```

###Send notification to all subscribers:
```php
CODE
```

##Priority
###Set priority to a notification:
```php
CODE
```
or:
```php
CODE
```

###Unset priority to a notification:
```php
CODE
```
or:
```php
CODE
```

###Set priority to a notification as a user:
```php
CODE
```
or:
```php
CODE
```

###Unset priority to a notification as a user:
```php
CODE
```
or:
```php
CODE
```

##Mute
###Mute:
```php
CODE
```
or:
```php
CODE
```

###Unmute:
```php
CODE
```
or:
```php
CODE
```

##Alert type
###Set/update alert type:
```php
CODE
```
or:
```php
CODE
```

###Available alert types:
- NOTIFICATION_CENTER
    - value -> notification-center
- BANNER
    - value -> banner
- LOCK_SCREEN
    - value -> lock-screen

##Preview type
###Set/update preview type:
```php
CODE
```
or:
```php
CODE
```

###Available alert types:
- ALWAYS
    - value -> always
- WHEN_UNLOCKED
    - value -> when-unlocked
- NEVER
    - value -> never

##Seen
###Mark as seen
```php
CODE
```
or:
```php
CODE
```

###Mark as unseen
```php
CODE
```
or:
```php
CODE
```

###Get all seen notifications:
```php
CODE
```
###Get all unseen notifications:
```php
CODE
```

###Check if a notification is seen:
```php
CODE
```

###Check if a notification is unseen:
```php
CODE
```

###Check if a notification is prioritised:
```php
CODE
```

###Check if a notification is trivialised:
```php
CODE
```

###Check if a notification is muted:
```php
CODE
```

###Check if a notification is unmuted:
```php
CODE
``` 


