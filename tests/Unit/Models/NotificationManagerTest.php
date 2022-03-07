<?php


use Illuminate\Database\Eloquent\Factories\Sequence;
use Rubik\NotificationManager\Models\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;

it('can be morphed to a user model', function ($model) {
    $notificationManager = NotificationManager::factory()->for($model::factory(), 'notifiable')->create();
    assertInstanceOf($model, $notificationManager->notifiable);
})->with([
    [User::class],
]);


it('can scope based on notification', function () {
    NotificationManager::factory()->count(10)
        ->state(new Sequence(
            ['notification' => 'order.accepted'],
            ['notification' => 'order.rejected'],
        ))->create();
    assertDatabaseCount('notification_managers', 10);
    assertCount(5, NotificationManager::forNotification('order.accepted')->get());
    assertCount(5, NotificationManager::forNotification('order.rejected')->get());

});


