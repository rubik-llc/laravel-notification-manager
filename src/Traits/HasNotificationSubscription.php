<?php

namespace Rubik\NotificationManager\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Rubik\NotificationManager\Models\Notification;
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
        return explode(",", $this->subscriptions()->forNotification($notification)->first()->channel);
    }

//    /**
//     * Defines polymorphic relation between User and Notifications
//     *
//     * @return MorphMany
//     */
//    public function notifications(): MorphMany
//    {
//        return $this->morphMany(Notification::class, 'notifiable');
//    }
}
