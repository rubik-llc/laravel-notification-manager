<?php

use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedNotification;

it('checks if a notification is seen', function () {
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    $this->assertCount(3, OrderApprovedNotification::subscribers());
});
