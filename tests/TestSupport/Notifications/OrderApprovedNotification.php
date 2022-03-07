<?php

namespace Rubik\NotificationManager\Tests\TestSupport\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Rubik\NotificationManager\Contracts\SubscribableNotificationContract;
use Rubik\NotificationManager\Traits\SubscribableNotification;

class OrderApprovedNotification extends Notification implements ShouldQueue, SubscribableNotificationContract
{
    use Queueable, SubscribableNotification;

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
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->payload,
            'is_prioritized' => null,
            'is_muted' => null,
            'alert_type' => null,
            'preview_type' => null,
            'needs_authentication' => null,
            'read_at' => null,
            'seen_at' => null,
            'created_at' => Carbon::now(),
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
