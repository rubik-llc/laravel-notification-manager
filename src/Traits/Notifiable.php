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
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    /**
     * Get the entity's seen notifications.
     *
     * @return Builder
     */
    public function seenNotifications()
    {
        return $this->notifications()->seen();
    }

    /**
     * Get the entity's unseen notifications.
     *
     * @return Builder
     */
    public function unseenNotifications()
    {
        return $this->notifications()->unseen();
    }

    /**
     * Get the entity's muted notifications.
     *
     * @return Builder
     */
    public function mutedNotifications()
    {
        return $this->notifications()->muted();
    }

    /**
     * Get the entity's unmuted notifications.
     *
     * @return Builder
     */
    public function unmutedNotifications()
    {
        return $this->notifications()->unmuted();
    }

    /**
     * Get the entity's prioritized notifications.
     *
     * @return Builder
     */
    public function prioritizedNotifications()
    {
        return $this->notifications()->prioritized();
    }

    /**
     * Get the entity's trivialized notifications.
     *
     * @return Builder
     */
    public function trivializedNotifications()
    {
        return $this->notifications()->trivialized();
    }

    /**
     * Get the entity's alert type notification center notifications.
     *
     * @return Builder
     */
    public function alertNotificationCenterNotifications()
    {
        return $this->notifications()->alertNotificationCenter();
    }

    /**
     * Get the entity's alert type banner notifications.
     *
     * @return Builder
     */
    public function alertBannerNotifications()
    {
        return $this->notifications()->alertBanner();
    }

    /**
     * Get the entity's alert type lock screen notifications.
     *
     * @return Builder
     */
    public function alertLockScreenNotifications()
    {
        return $this->notifications()->alertLockScreen();
    }

    /**
     * Get the entity's preview type always notifications.
     *
     * @return Builder
     */
    public function previewAlwaysNotifications()
    {
        return $this->notifications()->previewAlways();
    }

    /**
     * Get the entity's preview type when unlocked notifications.
     *
     * @return Builder
     */
    public function previewWhenUnlockedNotifications()
    {
        return $this->notifications()->previewWhenUnlocked();
    }

    /**
     * Get the entity's preview type never notifications.
     *
     * @return Builder
     */
    public function previewNeverNotifications()
    {
        return $this->notifications()->previewNever();
    }
}
