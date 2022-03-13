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
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedSubscribableNotification;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderRejectedSubscribableNotification;
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
    NotificationManager::subscribe(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
});

it('can subscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);
    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);
});

it('can subscribe to all notifications', function () {
    OrderApprovedSubscribableNotification::unsubscribe();
    OrderRejectedSubscribableNotification::unsubscribe();

    NotificationManager::subscribeAll();

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    OrderRejectedSubscribableNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderRejectedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderRejectedSubscribableNotification::class);
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
});

it('can subscribe a user to all notifications', function () {
    NotificationManager::for($this->user)->unsubscribe(OrderApprovedSubscribableNotification::class);
    NotificationManager::for($this->user)->unsubscribe(OrderRejectedSubscribableNotification::class);

    NotificationManager::for($this->user)->subscribeAll();

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    OrderRejectedSubscribableNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertSentTo($this->user, OrderRejectedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderRejectedSubscribableNotification::class);

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
});

it('can unsubscribe to a notification', function () {
    NotificationManager::subscribe(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);

    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    testTime()->freeze();
    NotificationManager::unsubscribe(OrderApprovedSubscribableNotification::class);

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);
});
it('can unsubscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    testTime()->freeze();
    NotificationManager::for($this->user)->unsubscribe(OrderApprovedSubscribableNotification::class);

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'unsubscribed_at' => Carbon::now(),
    ]);
});

it('can unsubscribe from all notifications', function () {
    OrderApprovedSubscribableNotification::subscribe();
    OrderRejectedSubscribableNotification::subscribe();

    testTime()->freeze();
    NotificationManager::unsubscribeAll();

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedSubscribableNotification::class);

    OrderApprovedSubscribableNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedSubscribableNotification::class);

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
});

it('can unsubscribe a user from all notifications', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedSubscribableNotification::class);
    NotificationManager::for($this->user)->subscribe(OrderRejectedSubscribableNotification::class);

    testTime()->freeze();
    NotificationManager::for($this->user)->unsubscribeAll();

    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedSubscribableNotification::class);

    OrderApprovedSubscribableNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedSubscribableNotification::class);

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
});

it('can prioritize a notification', function () {
    NotificationManager::prioritize(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'is_prioritized' => true,
    ]);
});
it('can prioritize a notification for a user', function () {
    NotificationManager::for($this->user)->prioritize(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_prioritized' => true,
    ]);
});
it('can trivialize a notification', function () {
    NotificationManager::trivialize(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'is_prioritized' => false,
    ]);
});
it('can trivialize a notification for a user', function () {
    NotificationManager::for($this->user)->trivialize(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_prioritized' => false,
    ]);
});

it('can mute a notification', function () {
    NotificationManager::mute(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'is_muted' => true,
    ]);
});
it('can mute a notification for a user', function () {
    NotificationManager::for($this->user)->mute(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_muted' => true,
    ]);
});

it('can unmute a notification', function () {
    NotificationManager::unmute(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'is_muted' => false,
    ]);
});
it('can unmute a notification for a user', function () {
    NotificationManager::for($this->user)->unmute(OrderApprovedSubscribableNotification::class);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'is_muted' => false,
    ]);
});

it('can set alert type of notification', function () {
    NotificationManager::alertType(OrderApprovedSubscribableNotification::class, NotificationAlertType::BANNER);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'alert_type' => NotificationAlertType::BANNER->value,
    ]);
});
it('can set alert type of notification for a user', function () {
    NotificationManager::for($this->user)->alertType(OrderApprovedSubscribableNotification::class, NotificationAlertType::BANNER);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'alert_type' => NotificationAlertType::BANNER->value,
    ]);
});

it('can set preview type of notification', function () {
    NotificationManager::previewType(OrderApprovedSubscribableNotification::class, NotificationPreviewType::ALWAYS);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'preview_type' => NotificationPreviewType::ALWAYS->value,
    ]);
});
it('can set preview type of notification for a user', function () {
    NotificationManager::for($this->user)->previewType(OrderApprovedSubscribableNotification::class, NotificationPreviewType::ALWAYS);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    $this->assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class($this->user),
        'notifiable_id' => $this->user->id,
        'notification' => 'order.approved',
        'preview_type' => NotificationPreviewType::ALWAYS->value,
    ]);
});

it('can not send notification to non subscribers', function () {
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedSubscribableNotification::class);
});
