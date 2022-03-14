<?php

namespace Rubik\NotificationManager\Tests\TestSupport\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Models\NotificationManager;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;
use function collect;

class NotificationManagerFactory extends Factory
{


    protected $model = NotificationManager::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'notification' => $this->faker->randomElement(collect(config('notification-manager.subscribable_notifications'))->keys()->all()),
            'notifiable_id' => User::factory()->create()->id,
            'notifiable_type' => 'App/User',
            'channel' => "*",
            'preview_type' => NotificationPreviewType::WHEN_UNLOCKED->value,
            'alert_type' => NotificationAlertType::NOTIFICATION_CENTER->value,
            'unsubscribed_at' => null,
            'is_prioritized' => false,
            'is_muted' => false,
            'needs_authentication' => true,
        ];
    }
}
