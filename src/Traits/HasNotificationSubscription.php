<?php

namespace Rubik\NotificationManager\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rubik\NotificationManager\Models\NotificationManager;

trait HasNotificationSubscription
{
    /**
     * Defines polymorphic relation between any Model and Notification Subscription
     *
     * @return MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(NotificationManager::class, 'notifiable');
    }

    /**
     * @param string $notification
     * @return array
     */
    public function channels(string $notification): array
    {
        return explode(",", $this->notification($notification)->channel);
    }

    /**
     * @param string $notification
     * @return NotificationManager
     */
    public function notification(string $notification): NotificationManager
    {
        return $this->subscriptions()->forNotification($notification)->first();
    }

}
