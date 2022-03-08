<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\actingAs;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedNotification;

beforeEach(function () {
    $this->loggedInUser = User::factory()->create();
    actingAs($this->loggedInUser);
    $this->approvedOrder = Order::factory()->state(fn () => ['approved' => true])->create();
    $this->rejectedOrder = Order::factory()->state(fn () => ['approved' => false])->create();
    Notification::fake();
});

it('checks if a notification is seen', function () {
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedNotification::class);
    $this->assertCount(3, OrderApprovedNotification::subscribers());
});

it('can subscribe to a notification using notification class', function () {
    OrderApprovedNotification::subscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unsubscribe to a notification using notification class', function () {
    OrderApprovedNotification::subscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    OrderApprovedNotification::unsubscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can prioritize a notification using notification class', function () {
    OrderApprovedNotification::prioritize();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertTrue(! ! $notification->toBroadcast(Auth::user())->data['is_prioritized']);
        $this->assertTrue(! ! $notification->toDatabase(Auth::user())['is_prioritized']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can trivialize a notification using notification class', function () {
    OrderApprovedNotification::trivialize();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertFalse(! ! $notification->toBroadcast(Auth::user())->data['is_prioritized']);
        $this->assertFalse(! ! $notification->toDatabase(Auth::user())['is_prioritized']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can mute a notification using notification class', function () {
    OrderApprovedNotification::mute();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertTrue(! ! $notification->toBroadcast(Auth::user())->data['is_muted']);
        $this->assertTrue(! ! $notification->toDatabase(Auth::user())['is_muted']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unmute a notification using notification class', function () {
    OrderApprovedNotification::unmute();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertFalse(! ! $notification->toBroadcast(Auth::user())->data['is_muted']);
        $this->assertFalse(! ! $notification->toDatabase(Auth::user())['is_muted']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can set alert type of notification using notification class', function () {
    OrderApprovedNotification::alertType(NotificationAlertType::BANNER);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertEquals(NotificationAlertType::BANNER, $notification->toBroadcast(Auth::user())->data['alert_type']);
        $this->assertEquals(NotificationAlertType::BANNER, $notification->toDatabase(Auth::user())['alert_type']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can set preview type of notification using notification class', function () {
    OrderApprovedNotification::previewType(NotificationPreviewType::ALWAYS);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class, function ($notification) {
        $this->assertEquals(NotificationPreviewType::ALWAYS, $notification->toBroadcast(Auth::user())->data['preview_type']);
        $this->assertEquals(NotificationPreviewType::ALWAYS, $notification->toDatabase(Auth::user())['preview_type']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});
