<?php
// config for Rubik/NotificationManager


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
