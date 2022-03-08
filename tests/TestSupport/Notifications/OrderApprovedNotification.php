<?php

namespace Rubik\NotificationManager\Tests\TestSupport\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Rubik\NotificationManager\Contracts\SubscribableNotificationContract;
use Rubik\NotificationManager\Traits\SubscribableNotification;

class OrderApprovedNotification extends Notification implements ShouldQueue, SubscribableNotificationContract
{
    use Queueable;
    use SubscribableNotification;

    /**
     * @var Model
     */
    protected Model $payload;

    /**
     * Create a new notification instance.
     * @param Model $payload
     */
    public function __construct(Model $payload)
    {
        $this->afterCommit();
        $this->payload = $payload;
    }

    /**
     * Get the array representation of payload.
     *
     * @return array
     */
    public function setData(): array
    {
        return [
            'data' => $this->payload,
        ];
    }

    /**
     * @return string
     */
    public static function subscribableNotificationType(): string
    {
        return 'order.approved';
    }
}
