<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\actingAs;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedNotification;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderRejectedNotification;
use function Spatie\PestPluginTestTime\testTime;

beforeEach(function () {
    $this->loggedInUser = User::factory()->create();
    actingAs($this->loggedInUser);
    $this->user = User::factory()->create();
    $this->approvedOrder = Order::factory()->state(fn () => ['approved' => true])->create();
    $this->rejectedOrder = Order::factory()->state(fn () => ['approved' => false])->create();
    Notification::fake();
});

it('can subscribe to a notification', function () {
    NotificationManager::subscribe(OrderApprovedNotification::class, 'database');
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can subscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can subscribe to all notifications', function () {
    OrderApprovedNotification::unsubscribe();
    OrderRejectedNotification::unsubscribe();

    NotificationManager::subscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    OrderRejectedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderRejectedNotification::class);
    Notification::assertTimesSent(1, OrderRejectedNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.rejected',
        'unsubscribed_at' => null,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can subscribe a user to all notifications', function () {
    NotificationManager::for($this->user)->unsubscribe(OrderApprovedNotification::class);
    NotificationManager::for($this->user)->unsubscribe(OrderRejectedNotification::class);

    NotificationManager::for($this->user)->subscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    OrderRejectedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertSentTo($this->user, OrderRejectedNotification::class);
    Notification::assertTimesSent(1, OrderRejectedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.rejected',
        'unsubscribed_at' => null,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unsubscribe to a notification', function () {
    NotificationManager::subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);

    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    testTime()->freeze();
    NotificationManager::unsubscribe(OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);

//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can unsubscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    testTime()->freeze();
    NotificationManager::for($this->user)->unsubscribe(OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);

//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unsubscribe from all notifications', function () {
    OrderApprovedNotification::subscribe();
    OrderRejectedNotification::subscribe();

    testTime()->freeze();
    NotificationManager::unsubscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.rejected',
        'unsubscribed_at' => Carbon::now(),
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unsubscribe a user from all notifications', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    NotificationManager::for($this->user)->subscribe(OrderRejectedNotification::class);

    testTime()->freeze();
    NotificationManager::for($this->user)->unsubscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.rejected',
        'unsubscribed_at' => Carbon::now(),
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can prioritize a notification', function () {
    NotificationManager::prioritize(OrderApprovedNotification::class);
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
        'is_prioritized' => true,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can prioritize a notification for a user', function () {
    NotificationManager::for($this->user)->prioritize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertTrue(! ! $notification->toBroadcast($this->user)->data['is_prioritized']);
        $this->assertTrue(! ! $notification->toDatabase($this->user)['is_prioritized']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_prioritized' => true,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can trivialize a notification', function () {
    NotificationManager::trivialize(OrderApprovedNotification::class);
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
        'is_prioritized' => false,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can trivialize a notification for a user', function () {
    NotificationManager::for($this->user)->trivialize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertFalse(! ! $notification->toBroadcast($this->user)->data['is_prioritized']);
        $this->assertFalse(! ! $notification->toDatabase($this->user)['is_prioritized']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_prioritized' => false,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can mute a notification', function () {
    NotificationManager::mute(OrderApprovedNotification::class);
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
        'is_muted' => true,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can mute a notification for a user', function () {
    NotificationManager::for($this->user)->mute(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertTrue(! ! $notification->toBroadcast($this->user)->data['is_muted']);
        $this->assertTrue(! ! $notification->toDatabase($this->user)['is_muted']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_muted' => true,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can unmute a notification', function () {
    NotificationManager::unmute(OrderApprovedNotification::class);
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
        'is_muted' => false,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can unmute a notification for a user', function () {
    NotificationManager::for($this->user)->unmute(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertFalse(! ! $notification->toBroadcast($this->user)->data['is_muted']);
        $this->assertFalse(! ! $notification->toDatabase($this->user)['is_muted']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_muted' => false,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can set alert type of notification', function () {
    NotificationManager::alertType(OrderApprovedNotification::class, NotificationAlertType::BANNER);
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
        'alert_type' => NotificationAlertType::BANNER->value,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can set alert type of notification for a user', function () {
    NotificationManager::for($this->user)->alertType(OrderApprovedNotification::class, NotificationAlertType::BANNER);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertEquals(NotificationAlertType::BANNER, $notification->toBroadcast($this->user)->data['alert_type']);
        $this->assertEquals(NotificationAlertType::BANNER, $notification->toDatabase($this->user)['alert_type']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'alert_type' => NotificationAlertType::BANNER->value,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can set preview type of notification', function () {
    NotificationManager::previewType(OrderApprovedNotification::class, NotificationPreviewType::ALWAYS);
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
        'preview_type' => NotificationPreviewType::ALWAYS->value,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});


it('can set preview type of notification for a user', function () {
    NotificationManager::for($this->user)->previewType(OrderApprovedNotification::class, NotificationPreviewType::ALWAYS);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class, function ($notification) {
        $this->assertEquals(NotificationPreviewType::ALWAYS, $notification->toBroadcast($this->user)->data['preview_type']);
        $this->assertEquals(NotificationPreviewType::ALWAYS, $notification->toDatabase($this->user)['preview_type']);

        return true;
    });
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'preview_type' => NotificationPreviewType::ALWAYS->value,
    ]);
//    $this->assertDatabaseHas('notifications', [
//        'data' => json_encode($this->approvedOrder)
//    ]);
});

it('can not send notification to non subscribers', function () {
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);
});
