<?php

namespace Rubik\NotificationManager\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;

class NotificationManager extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'notification',
        'alert_type',
        'preview_type',
        'is_prioritized',
        'is_muted',
        'notifiable_id',
        'notifiable_type',
        'channel',
        'unsubscribed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'alert_type' => NotificationAlertType::class,
        'preview_type' => NotificationPreviewType::class,
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Defines polymorphic relation between Notification Subscription and any other Model
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope only subscriptions for a specific notification
     *
     * @param $query
     */
    public function scopeForNotification($query, string $notification): void
    {
        $query->where('notification', $notification);
    }

    /**
     * Scope only subscriptions for with null unsubscribed
     *
     * @param $query
     */
    public function scopeSubscribed($query): void
    {
        $query->whereNull('unsubscribed_at');
    }
}
