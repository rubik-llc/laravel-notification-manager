<?php

namespace Rubik\NotificationManager\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     */
    public function buildPayload($notifiable, Notification $notification): array
    {
        $extraAttributes = [
            'is_prioritized' => null,
            'is_muted' => null,
            'alert_type' => null,
            'preview_type' => null,
        ];

        if (method_exists($notification, 'details')) {
            $extraAttributes = [
                'is_prioritized' => $notification->details($notifiable)->is_prioritized,
                'is_muted' => $notification->details($notifiable)->is_muted,
                'alert_type' => $notification->details($notifiable)->alert_type->value,
                'preview_type' => $notification->details($notifiable)->preview_type->value,
            ];
        }

        return array_merge($extraAttributes, [
            'id' => $notification->id,
            'type' => get_class($notification),
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null,
            'seen_at' => null,
        ]);
    }
}
