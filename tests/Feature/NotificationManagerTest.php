<?php

use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\actingAs;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedNotification;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderRejectedNotification;

beforeEach(function () {
    $this->loggedInUser = User::factory()->create();
    actingAs($this->loggedInUser);
    $this->user = User::factory()->create();
    $this->approvedOrder = Order::factory()->state(fn () => ['approved' => true])->create();
    $this->rejectedOrder = Order::factory()->state(fn () => ['approved' => false])->create();
    Notification::fake();
});

it('can subscribe to a notification', function () {
    NotificationManager::subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});


it('can subscribe to a notification using notification class', function () {
    OrderApprovedNotification::subscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can subscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
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
});

it('can subscribe a user to all notifications', function () {
    NotificationManager::for($this->user)->unsubscribe(OrderApprovedNotification::class);
    NotificationManager::for($this->user)->unsubscribe(OrderRejectedNotification::class);

    NotificationManager::for($this->user)->subscribeAll(OrderApprovedNotification::class);


    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    OrderRejectedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertSentTo($this->user, OrderRejectedNotification::class);
    Notification::assertTimesSent(1, OrderRejectedNotification::class);
});

it('can unsubscribe to a notification', function () {
    NotificationManager::subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);

    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    NotificationManager::unsubscribe(OrderApprovedNotification::class);

//    dump(OrderApprovedNotification::subscribers());
//    dump(\Rubik\NotificationManager\Models\NotificationManager::subscribed()->get());

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});


it('can unsubscribe to a notification using notification class', function () {
    OrderApprovedNotification::subscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    OrderApprovedNotification::unsubscribe();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can unsubscribe to a notification for a user', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);

    NotificationManager::for($this->user)->unsubscribe(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can unsubscribe from all notifications', function () {
    OrderApprovedNotification::subscribe();
    OrderRejectedNotification::subscribe();

    NotificationManager::unsubscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedNotification::class);
});

it('can unsubscribe a user from all notifications', function () {
    NotificationManager::for($this->user)->subscribe(OrderApprovedNotification::class);
    NotificationManager::for($this->user)->subscribe(OrderRejectedNotification::class);

    NotificationManager::for($this->user)->unsubscribeAll();

    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);

    OrderApprovedNotification::sendToSubscribers($this->rejectedOrder);
    Notification::assertTimesSent(0, OrderRejectedNotification::class);
});

it('can prioritize a notification', function () {
    NotificationManager::prioritize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can prioritize a notification using notification class', function () {
    OrderApprovedNotification::prioritize();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can prioritize a notification for a user', function () {
    NotificationManager::for($this->user)->prioritize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});


it('can trivialize a notification', function () {
    NotificationManager::trivialize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can trivialize a notification using notification class', function () {
    OrderApprovedNotification::trivialize();
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can trivialize a notification for a user', function () {
    NotificationManager::for($this->user)->trivialize(OrderApprovedNotification::class);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});


it('can set alert type of notification', function () {
    NotificationManager::alertType(OrderApprovedNotification::class, NotificationAlertType::BANNER);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can set alert type of notification using notification class', function () {
    OrderApprovedNotification::alertType(NotificationAlertType::BANNER);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can set alert type of notification for a user', function () {
    NotificationManager::for($this->user)->alertType(OrderApprovedNotification::class, NotificationAlertType::BANNER);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can set preview type of notification', function () {
    NotificationManager::previewType(OrderApprovedNotification::class, NotificationPreviewType::ALWAYS);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can set preview type of notification using notification class', function () {
    OrderApprovedNotification::previewType(NotificationPreviewType::ALWAYS);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->loggedInUser, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can set preview type of notification for a user', function () {
    NotificationManager::for($this->user)->previewType(OrderApprovedNotification::class, NotificationPreviewType::ALWAYS);
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertSentTo($this->user, OrderApprovedNotification::class);
    Notification::assertTimesSent(1, OrderApprovedNotification::class);
});

it('can not send notification to non subscribers', function () {
    OrderApprovedNotification::sendToSubscribers($this->approvedOrder);
    Notification::assertTimesSent(0, OrderApprovedNotification::class);
});
