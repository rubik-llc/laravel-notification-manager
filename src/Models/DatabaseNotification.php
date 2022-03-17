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
     */
    public function markAsSeen(): void
    {
        if (is_null($this->seen_at)) {
            $this->forceFill([
                'seen_at' => $this->freshTimestamp(),
            ])->save();
        }
    }

    /**
     * Mark the notification as unseen.
     */
    public function markAsUnseen(): void
    {
        if (! is_null($this->seen_at)) {
            $this->forceFill([
                'seen_at' => null,
            ])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     * @return bool
     */
    public function seen(): bool
    {
        return $this->seen_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     * @return bool
     */
    public function unseen(): bool
    {
        return $this->seen_at === null;
    }

    /**
     * Determine if a notification is muted.
     * @return bool
     */
    public function muted(): bool
    {
        return $this->is_muted;
    }

    /**
     * Determine if a notification is prioritized.
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
        return ! $this->is_prioritized;
    }

    /**
     * Scope a query to only include seen notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopeSeen(Builder $query): Builder
    {
        return $query->whereNotNull('seen_at');
    }

    /**
     * Scope a query to only include unseen notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnseen(Builder $query): Builder
    {
        return $query->whereNull('seen_at');
    }

    /**
     * Scope a query to only include prioritized notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopePrioritized(Builder $query): Builder
    {
        return $query->where('is_prioritized', true);
    }

    /**
     * Scope a query to only include unseen notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopeTrivialized(Builder $query): Builder
    {
        return $query->where('is_prioritized', false);
    }

    /**
     * Scope a query to only include muted notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopeMuted(Builder $query): Builder
    {
        return $query->where('is_muted', true);
    }

    /**
     * Scope a query to only include not muted notifications.
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnmuted(Builder $query): Builder
    {
        return $query->where('is_muted', false);
    }

    /**
     * Scope a query to only include notifications with alert type set to notification-center.
     * @param Builder $query
     * @return Builder
     */
    public function scopeAlertNotificationCenter(Builder $query): Builder
    {
        return $query->where('alert_type', NotificationAlertType::NOTIFICATION_CENTER->value);
    }

    /**
     * Scope a query to only include notifications with alert type set to banner.
     * @param Builder $query
     * @return Builder
     */
    public function scopeAlertBanner(Builder $query): Builder
    {
        return $query->where('alert_type', NotificationAlertType::BANNER->value);
    }

    /**
     * Scope a query to only include notifications with alert type set to lock screen.
     * @param Builder $query
     * @return Builder
     */
    public function scopeAlertLockScreen(Builder $query): Builder
    {
        return $query->where('alert_type', NotificationAlertType::LOCK_SCREEN->value);
    }

    /**
     * Scope a query to only include notifications with preview type set to always.
     * @param Builder $query
     * @return Builder
     */
    public function scopePreviewAlways(Builder $query): Builder
    {
        return $query->where('preview_type', NotificationPreviewType::ALWAYS->value);
    }

    /**
     * Scope a query to only include notifications with preview type set to when unlocked.
     * @param Builder $query
     * @return Builder
     */
    public function scopePreviewWhenUnlocked(Builder $query): Builder
    {
        return $query->where('preview_type', NotificationPreviewType::WHEN_UNLOCKED->value);
    }

    /**
     * Scope a query to only include notifications with preview type set to never.
     * @param Builder $query
     * @return Builder
     */
    public function scopePreviewNever(Builder $query): Builder
    {
        return $query->where('preview_type', NotificationPreviewType::NEVER->value);
    }

    /**
     * Create a new database notification collection instance.
     * @param array $models
     * @return DatabaseNotificationCollection
     */
    public function newCollection(array $models = []): DatabaseNotificationCollection
    {
        return new DatabaseNotificationCollection($models);
    }
}
