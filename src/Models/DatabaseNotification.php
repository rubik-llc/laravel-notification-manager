<?php

namespace Rubik\NotificationManager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Rubik\NotificationManager\Collections\DatabaseNotificationCollection;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;

class DatabaseNotification extends BaseDatabaseNotification
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
        'data' => 'array',
        'read_at' => 'datetime',
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
     * Scope a query to only include seen notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeSeen(Builder $query)
    {
        return $query->whereNotNull('seen_at');
    }

    /**
     * Scope a query to only include unseen notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnseen(Builder $query)
    {
        return $query->whereNull('seen_at');
    }

    /**
     * Create a new database notification collection instance.
     *
     * @param array $models
     * @return DatabaseNotificationCollection
     */
    public function newCollection(array $models = []): DatabaseNotificationCollection
    {
        return new DatabaseNotificationCollection($models);
    }
}
