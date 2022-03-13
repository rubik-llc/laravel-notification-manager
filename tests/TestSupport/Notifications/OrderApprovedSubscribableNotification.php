<?php

namespace Rubik\NotificationManager\Tests\TestSupport\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Rubik\NotificationManager\Contracts\SubscribableNotificationContract;
use Rubik\NotificationManager\Traits\SubscribableNotification;

class OrderApprovedSubscribableNotification extends Notification implements SubscribableNotificationContract
{
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
        $this->payload = $payload;
    }


    /**
     * @return string
     */
    public static function subscribableNotificationType(): string
    {
        return 'order.approved';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->payload->toArray();
    }
}
