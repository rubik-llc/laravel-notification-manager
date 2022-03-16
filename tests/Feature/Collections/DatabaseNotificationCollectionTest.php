<?php


use Carbon\Carbon;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use Rubik\NotificationManager\Models\DatabaseNotification;

it('marks all as seen', function () {
    $databaseNotification = DatabaseNotification::factory()->count(5)
        ->state(
            [
                'seen_at' => null,
            ],
        )->create();
    $databaseNotification->markAsSeen();
    assertDatabaseCount('notifications', 5);
    assertCount(5, DatabaseNotification::query()->seen()->get());
});

it('marks all as unseen', function () {
    $databaseNotification = DatabaseNotification::factory()->count(5)
        ->state(
            [
                'seen_at' => Carbon::now(),
            ],
        )->create();
    $databaseNotification->markAsUnseen();
    assertDatabaseCount('notifications', 5);
    assertCount(5, DatabaseNotification::query()->unseen()->get());
});
