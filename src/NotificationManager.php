<?php

namespace Rubik\NotificationManager;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Models\NotificationManager as NotificationManagerModel;

class NotificationManager
{
    public Model|Authenticatable|null $notifiable;

    public function __construct($notifiable = null)
    {
        $this->notifiable = $notifiable ?? Auth::user();
    }

    /**
     * @param Model|Authenticatable $notifiable
     * @return $this
     */
    public function for(Model|Authenticatable $notifiable): self
    {
        $this->notifiable = $notifiable;

        return $this;
    }

    /**
     * Subscribe a user to a notification
     *
     * @param $subscribableNotificationClass
     * @param string $channel
     */
    public function subscribe($subscribableNotificationClass, string $channel = '*')
    {
        NotificationManagerModel::updateOrCreate([
            'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
        ], [
            'channel' => $channel,
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * Unsubscribe a user to a notification
     *
     * @param $subscribableNotificationClass
     * @param string $channel
     * @return void
     */
    public function unsubscribe($subscribableNotificationClass, string $channel = '*')
    {
        NotificationManagerModel::updateOrCreate([
            'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
        ], [
            'channel' => $channel,
            'unsubscribed_at' => Carbon::now(),
        ]);
    }

    /**
     * Prioritize a notification for a user
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function prioritize($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_prioritized' => true]);
    }

    /**
     * Trivialize a notification for a user
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function trivialize($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_prioritized' => false]);
    }

    /**
     * Mute a notification for a user
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function mute($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_muted' => true]);
    }

    /**
     * Mute a notification for a user
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function unmute($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_muted' => false]);
    }

    /**
     * Subscribe to all notifications for a user
     *
     * @param string $channel
     * @return void
     */
    public function subscribeAll(string $channel = '*')
    {
        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
            ])
            ->update(['unsubscribed_at' => null, 'channel' => $channel]);
    }

    /**
     * Unsubscribe form all notifications for a user
     *
     * @param string $channel
     * @return void
     */
    public function unsubscribeAll(string $channel = '*')
    {
        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
            ])
            ->update(['unsubscribed_at' => Carbon::now(), 'channel' => $channel]);
    }

    /**
     * Update alert type for a user
     *
     * @param $subscribableNotificationClass
     * @param NotificationAlertType $notificationAlertType
     * @return void
     */
    public function alertType($subscribableNotificationClass, NotificationAlertType $notificationAlertType)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['alert_type' => $notificationAlertType->value]);
    }

    /**
     * Update preview type for a user
     *
     * @param $subscribableNotificationClass
     * @param NotificationPreviewType $notificationPreviewType
     * @return void
     */
    public function previewType($subscribableNotificationClass, NotificationPreviewType $notificationPreviewType)
    {
        $this->subscribe($subscribableNotificationClass);

        NotificationManagerModel::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['preview_type' => $notificationPreviewType->value]);
    }

    /**
     * Retrieve notification details
     *
     * @param $subscribableNotificationClass
     * @param Model $notifiable
     * @return NotificationManagerModel
     */
    public function details($subscribableNotificationClass, Model $notifiable): NotificationManagerModel
    {
        return NotificationManagerModel::where([
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'notification' => $subscribableNotificationClass->subscribableNotificationType(),
        ])->first();
    }
}
