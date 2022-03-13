<?php

namespace Rubik\NotificationManager\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable as BaseNotifiable;
use Rubik\NotificationManager\Models\DatabaseNotification;

trait Notifiable
{
    use BaseNotifiable;

    /**
     * Get the entity's notifications.
     *
     * @return MorphMany
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's read notifications.
     *
     * @return Builder
     */
    public function seenNotifications()
    {
        return $this->notifications()->seen();
    }

    /**
     * Get the entity's unread notifications.
     *
     * @return Builder
     */
    public function unseenNotifications()
    {
        return $this->notifications()->unseen();
    }
}
