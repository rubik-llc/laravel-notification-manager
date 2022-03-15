<?php


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Rubik\NotificationManager\Models\DatabaseNotification;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertTrue;

it('marks a notification as seen', function () {
    $notification = DatabaseNotification::factory()->create();
    assertTrue($notification->seen_at === null);
    $notification->markAsSeen();
    assertTrue($notification->seen_at !== null);
});


it('marks a notification as unseen', function () {
    $notification = DatabaseNotification::factory()->create();
    assertTrue($notification->seen_at === null);
    $notification->markAsSeen();
    assertTrue($notification->seen_at !== null);
    $notification->markAsUnseen();
    assertTrue($notification->seen_at === null);
});

it('checks if a notification is seen', function () {
    $notification = DatabaseNotification::factory()->create();
    $notification->markAsSeen();
    assertTrue($notification->seen() === true);
});

it('checks if a notification is unseen', function () {
    $notification = DatabaseNotification::factory()->create();
    assertTrue($notification->unseen() === true);
});

it('checks if a notification is prioritized', function () {
    $notification = DatabaseNotification::factory()->state([
        'is_prioritized' => true,
    ])->create();
    assertTrue($notification->prioritized() === true);
});

it('checks if a notification is trivialized', function () {
    $notification = DatabaseNotification::factory()->state([
        'is_prioritized' => false,
    ])->create();
    assertTrue($notification->trivialized() === true);
});

it('checks if a notification is muted', function () {
    $notification = DatabaseNotification::factory()->state([
        'is_muted' => true,
    ])->create();
    assertTrue($notification->muted() === true);
});


it('scope only seen notification', function () {
    $user = User::factory()->create();
    DatabaseNotification::factory()->count(10)
        ->state(new Sequence(
            [
                'seen_at' => Carbon::now(),
                'notifiable_id' => $user->id,
                'notifiable_type' => get_class($user),
            ],
            [
                'seen_at' => null,
                'notifiable_id' => $user->id,
                'notifiable_type' => get_class($user),
            ],
        ))->create();
    assertCount(5, $user->seenNotifications()->get());
});


it('scope only unseen notification', function () {
    $user = User::factory()->create();
    DatabaseNotification::factory()->count(10)
        ->state(new Sequence(
            [
                'seen_at' => Carbon::now(),
                'notifiable_id' => $user->id,
                'notifiable_type' => get_class($user),
            ],
            [
                'seen_at' => null,
                'notifiable_id' => $user->id,
                'notifiable_type' => get_class($user),
            ],
        ))->create();
    assertDatabaseCount('notifications', 10);
    assertCount(5, $user->unseenNotifications()->get());
});
