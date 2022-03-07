<?php

namespace Rubik\NotificationManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Rubik\NotificationManager\Collections\CustomDatabaseNotificationCollection;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;

class Notification extends DatabaseNotification
{
    use HasFactory;

    protected $primaryKey = 'id';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'seen_at' => 'datetime',
        'alert_type' => NotificationAlertType::class,
        'preview_type' => NotificationPreviewType::class,
    ];

    /**
     * Mark the notification as seen.
     *
     * @return void
     */
    public function markAsSeen()
    {
        if (is_null($this->seen_at)) {
            $this->forceFill(['seen_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the notification as unseen.
     *
     * @return void
     */
    public function markAsUnseen()
    {
        if (!is_null($this->seen_at)) {
            $this->forceFill(['seen_at' => null])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     *
     * @return bool
     */
    public function seen(): bool
    {
        return $this->seen_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     *
     * @return bool
     */
    public function unseen(): bool
    {
        return $this->seen_at === null;
    }

    /**
     * Determine if a notification is muted.
     *
     * @return bool
     */
    public function muted(): bool
    {
        return $this->is_muted;
    }

    /**
     * Determine if a notification is prioritized.
     *
     * @return bool
     */
    public function prioritized(): bool
    {
        return $this->is_prioritized;
    }

    /**
     * Determine if a notification is trivialized.
     * @return bool
     */
    public function trivialized(): bool
    {
        return !$this->is_prioritized;
    }

    /**
     * Determine if a notification needs authentication.
     *
     * @return bool
     */
    public function needsAuthentication(): bool
    {
        return $this->needs_authentication;
    }

    /**
     * Create a new database notification collection instance.
     *
     * @param array $models
     * @return CustomDatabaseNotificationCollection
     */
    public function newCollection(array $models = []): CustomDatabaseNotificationCollection
    {
        return new CustomDatabaseNotificationCollection($models);
    }
}