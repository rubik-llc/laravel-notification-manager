<?php

namespace Rubik\NotificationManager\Collections;

use Illuminate\Notifications\DatabaseNotificationCollection;

class CustomDatabaseNotificationCollection extends DatabaseNotificationCollection
{
    /**
     * Mark all notifications as seen.
     *
     * @return void
     */
    public function markAsSeen()
    {
        $this->each->markAsSeen();
    }

    /**
     * Mark all notifications as unseen.
     *
     * @return void
     */
    public function markAsUnseen()
    {
        $this->each->markAsUnseen();
    }
}
