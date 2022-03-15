<?php

namespace Rubik\NotificationManager\Collections;

use Illuminate\Notifications\DatabaseNotificationCollection as BaseDatabaseNotificationCollection;

class DatabaseNotificationCollection extends BaseDatabaseNotificationCollection
{
    /**
     * Mark all notifications as seen.
     */
    public function markAsSeen()
    {
        $this->each->markAsSeen();
    }

    /**
     * Mark all notifications as unseen.
     */
    public function markAsUnseen()
    {
        $this->each->markAsUnseen();
    }
}
