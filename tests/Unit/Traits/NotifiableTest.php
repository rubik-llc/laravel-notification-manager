<?php


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Auth;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Models\DatabaseNotification;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('scope only notification with preview type never', function ($state, string $scope, $count) {
    $databaseNotification = DatabaseNotification::factory()->count(2)
        ->state(
            new Sequence(
                array_merge(
                    $state,
                    ['notifiable_id' => Auth::id(), 'notifiable_type' => get_class(Auth::user())]
                ),
                ['notifiable_id' => Auth::id(), 'notifiable_type' => get_class(Auth::user())]
            )
        )->create();
    assertDatabaseCount('notifications', 2);
    assertCount($count, Auth::user()->$scope);
})->with([
    [['seen_at' => Carbon::now()], 'seenNotifications', 1],
    [['seen_at' => null], 'unseenNotifications', 2],
    [['is_muted' => 1], 'mutedNotifications', 1],
    [['is_muted' => 0], 'unmutedNotifications', 2],
    [['is_prioritized' => 1], 'prioritizedNotifications', 1],
    [['is_prioritized' => 0], 'trivializedNotifications', 2],
    [['alert_type' => NotificationAlertType::LOCK_SCREEN->value], 'alertLockScreenNotifications', 1],
    [['alert_type' => NotificationAlertType::BANNER->value], 'alertBannerNotifications', 1],
    [['alert_type' => NotificationAlertType::NOTIFICATION_CENTER->value], 'alertNotificationCenterNotifications', 2],
    [['preview_type' => NotificationPreviewType::NEVER->value], 'previewNeverNotifications', 1],
    [['preview_type' => NotificationPreviewType::WHEN_UNLOCKED->value], 'previewWhenUnlockedNotifications', 2],
    [['preview_type' => NotificationPreviewType::ALWAYS->value], 'previewAlwaysNotifications', 1],
]);
