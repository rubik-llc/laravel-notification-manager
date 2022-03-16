<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Rubik\NotificationManager\Facades\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use Rubik\NotificationManager\Tests\TestSupport\Notifications\OrderApprovedSubscribableNotification;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('can send database notifications', function () {
    $loggedInUser = User::factory()->create();
    actingAs($loggedInUser);
    $approvedOrder = Order::factory()->state(fn() => [
        'approved' => true,
    ])->create();
    NotificationManager::subscribe(OrderApprovedSubscribableNotification::class,'database');
    OrderApprovedSubscribableNotification::sendToSubscribers($approvedOrder);

    assertDatabaseHas('notification_managers', [
        'notifiable_type' => get_class(Auth::user()),
        'notifiable_id' => Auth::id(),
        'notification' => 'order.approved',
        'unsubscribed_at' => null,
    ]);

    assertDatabaseHas('notifications', [
        'preview_type' => 'when-unlocked',
        'alert_type' => 'notification-center',
        'is_prioritized' => false,
        'is_muted' => false,
    ]);
});
