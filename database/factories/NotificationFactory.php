<?php

namespace Rubik\NotificationManager\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Models\Notification;
use Rubik\NotificationManager\Tests\TestSupport\Models\Order;
use Rubik\NotificationManager\Tests\TestSupport\Models\User;

class NotificationFactory extends Factory
{

    protected $model = Notification::class;


    /**
     * Default type value
     *
     * @var string
     */
    private string $type = "Rubik\\NotificationManager\\Tests\\TestSupport\\Notifications\\OrderApprovedNotification.php";


    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'type' => $this->type,
            'notifiable_type' => "App\\Models\\User",
            'notifiable_id' => User::factory()->create()->id,
            'data' => json_encode(Order::factory()->create()),
            'read_at' => Carbon::parse($this->faker->dateTime())->format('Y-m-d H:i:s'),
            'seen_at' => null,
            'is_prioritized' => false,
            'is_muted' => false,
            'needs_authentication' => true,
            'preview_type' => NotificationPreviewType::WHEN_UNLOCKED->value,
            'alert_type' => NotificationAlertType::NOTIFICATION_CENTER->value,
        ];
    }
}