<?php

namespace Rubik\NotificationManager;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;

class NotificationManager
{
    public Model|Authenticatable|null $notifiable;

    public function __construct()
    {
        $this->notifiable = Auth::user();
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
     * @param $subscribableNotificationClass
     * @param string $channel
     */
    public function subscribe($subscribableNotificationClass, string $channel = '*')
    {
        Models\NotificationManager::updateOrCreate([
            'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
        ], [
            'channel' => $channel,
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * @param $subscribableNotificationClass
     * @param string $channel
     * @return void
     */
    public function unsubscribe($subscribableNotificationClass, string $channel = '*')
    {
        Models\NotificationManager::updateOrCreate([
            'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            'notifiable_type' => get_class($this->notifiable),
            'notifiable_id' => $this->notifiable->id,
        ], [
            'channel' => $channel,
            'unsubscribed_at' => Carbon::now(),
        ]);
    }

    /**
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function prioritize($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_prioritized' => true]);
    }

    /**
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function trivialize($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_prioritized' => false]);
    }


    /**
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function mute($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_muted' => true]);
    }

    /**
     *
     * @param $subscribableNotificationClass
     * @return void
     */
    public function unmute($subscribableNotificationClass)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['is_muted' => false]);
    }

    /**
     * @param string $channel
     * @return void
     */
    public function subscribeAll(string $channel = '*')
    {
        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
            ])
            ->update(['unsubscribed_at' => null, 'channel' => $channel]);
    }

    /**
     * @param string $channel
     * @return void
     */
    public function unsubscribeAll(string $channel = '*')
    {
        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
            ])
            ->update(['unsubscribed_at' => Carbon::now(), 'channel' => $channel]);
    }

    /**
     *
     * @param $subscribableNotificationClass
     * @param NotificationAlertType $notificationAlertType
     * @return void
     */
    public function alertType($subscribableNotificationClass, NotificationAlertType $notificationAlertType)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['alert_type' => $notificationAlertType->value]);
    }

    /**
     *
     * @param $subscribableNotificationClass
     * @param NotificationPreviewType $notificationPreviewType
     * @return void
     */
    public function previewType($subscribableNotificationClass, NotificationPreviewType $notificationPreviewType)
    {
        $this->subscribe($subscribableNotificationClass);

        Models\NotificationManager::query()
            ->where([
                'notifiable_type' => get_class($this->notifiable),
                'notifiable_id' => $this->notifiable->id,
                'notification' => $subscribableNotificationClass::subscribableNotificationType(),
            ])->update(['preview_type' => $notificationPreviewType->value]);
    }
}
