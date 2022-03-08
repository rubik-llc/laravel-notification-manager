<?php
// config for Rubik/NotificationManager
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedNotification;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderRejectedNotification;

return [
    //User model
    'UserModel' => "",
    //Team model
    'TeamModel' => "",
    //Do you use UUID as foreign key
    'user_uuid' => false,
    //Do you use UUID as foreign key
    'team_uuid' => false,
    //Add here all notifications that you want to manage based on subscription
    'subscribable_notifications' => [
        'order.accepted' => OrderApprovedNotification::class,
        'order.rejected' => OrderRejectedNotification::class,
    ],
    //Add here all notifications that you want to manage based on subscription
    'default_channels' => "database,broadcast"
];
