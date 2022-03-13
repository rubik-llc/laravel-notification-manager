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
     * @param Notification $notification
     *
     * @return array
     */
    public function buildPayload($notifiable, Notification $notification): array
    {
        $extraAttributes = [
            'is_prioritized' => null,
            'is_muted' => null,
            'alert_type' => null,
            'preview_type' => null,
            'needs_authentication' => null,
        ];

        if (method_exists($notification, 'details')) {
            $extraAttributes = [
                'is_prioritized' => $notification->details($notification, $notifiable)->is_prioritized,
                'is_muted' => $notification->details($notification, $notifiable)->is_muted,
                'alert_type' => $notification->details($notification, $notifiable)->alert_type->value,
                'preview_type' => $notification->details($notification, $notifiable)->preview_type->value,
                'needs_authentication' => $notification->details($notification, $notifiable)->needs_authentication,
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
