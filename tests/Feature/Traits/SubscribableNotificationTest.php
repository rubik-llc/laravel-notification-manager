<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedSubscribableNotification;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function PHPUnit\Framework\assertCount;

beforeEach(function () {
    $this->loggedInUser = User::factory()->create();
    actingAs($this->loggedInUser);
    $this->approvedOrder = Order::factory()->state(fn () => [
        'approved' => true,
    ])->create();
    $this->rejectedOrder = Order::factory()->state(fn () => [
        'approved' => false,
    ])->create();
    Notification::fake();
});

it('checks if a notification is seen', function () {
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedSubscribableNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedSubscribableNotification::class);
    NotificationManager::for(User::factory()->create())->subscribe(OrderApprovedSubscribableNotification::class);
    assertCount(3, OrderApprovedSubscribableNotification::subscribers());
});

it('can subscribe to a notification using notification class', function () {
    OrderApprovedSubscribableNotification::subscribe();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);
    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can unsubscribe to a notification using notification class', function () {
    OrderApprovedSubscribableNotification::subscribe();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    OrderApprovedSubscribableNotification::unsubscribe();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});


it('can prioritize a notification using notification class', function () {
    OrderApprovedSubscribableNotification::prioritize();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can trivialize a notification using notification class', function () {
    OrderApprovedSubscribableNotification::trivialize();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can mute a notification using notification class', function () {
    OrderApprovedSubscribableNotification::mute();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can unmute a notification using notification class', function () {
    OrderApprovedSubscribableNotification::unmute();
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can set alert type of notification using notification class', function () {
    OrderApprovedSubscribableNotification::alertType(NotificationAlertType::BANNER);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});

it('can set preview type of notification using notification class', function () {
    OrderApprovedSubscribableNotification::previewType(NotificationPreviewType::ALWAYS);
    OrderApprovedSubscribableNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedSubscribableNotification::class);
    Notification::assertTimesSent(1, OrderApprovedSubscribableNotification::class);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
    ]);
});
