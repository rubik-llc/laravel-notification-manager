<?php

namespace Rubik\NotificationManager\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager as NotificationManagerFacade;
use Rubik\NotificationManager\Models\NotificationManager;
use Rubik\NotificationManager\Models\NotificationManager as NotificationManagerModel;

trait SubscribableNotification
{
    abstract public static function subscribableNotificationType(): string;

    /**
     * Send a notification to all subscribers
     */
    public static function sendToSubscribers()
    {
        Notification::send(
            self::subscribers(),
            new static(...func_get_args())
        );
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return $notifiable->channels(self::subscribableNotificationType());
    }

    /**
     * Subscribe a user to a notification
     *
     * @param string $channel
     */
    public static function subscribe(string $channel = '*'): void
    {
        NotificationManagerFacade::subscribe(static::class, $channel);
    }

    /**
     * Unsubscribe a user to a notification
     *
     * @param string $channel
     */
    public static function unsubscribe(string $channel = '*'): void
    {
        NotificationManagerFacade::unsubscribe(static::class, $channel);
    }

    /**
     * Prioritize a notification for a user
     */
    public static function prioritize(): void
    {
        NotificationManagerFacade::prioritize(static::class);
    }

    /**
     * Trivialize a notification for a user
     */
    public static function trivialize(): void
    {
        NotificationManagerFacade::subscribe(static::class);
    }

    /**
     * Mute a notification for a user
     */
    public static function mute(): void
    {
        NotificationManagerFacade::mute(static::class);
    }

    /**
     * Mute a notification for a user
     */
    public static function unmute(): void
    {
        NotificationManagerFacade::unmute(static::class);
    }

    /**
     * Update preview type for a user
     *
     * @param NotificationPreviewType $notificationPreviewType
     */
    public static function previewType(NotificationPreviewType $notificationPreviewType): void
    {
        NotificationManagerFacade::previewType(static::class, $notificationPreviewType);
    }

    /**
     * Update alert type for a user
     *
     * @param NotificationAlertType $notificationAlertType
     */
    public static function alertType(NotificationAlertType $notificationAlertType): void
    {
        NotificationManagerFacade::alertType(static::class, $notificationAlertType);
    }

    /**
     * Retrieve notification details
     *
     * @param Authenticatable|Model $notifiable
     * @return NotificationManagerModel
     */
    public static function details(Authenticatable|Model $notifiable): NotificationManager
    {
        return NotificationManagerFacade::details(static::class, $notifiable);
    }

    /**
     * Return all subscribers
     *
     * @return Collection
     */
    public static function subscribers(): Collection
    {
        return NotificationManager::query()
            ->subscribed()
            ->forNotification(self::subscribableNotificationType())
            ->get()
            ->map(fn (NotificationManager $notificationSubscription) => $notificationSubscription->notifiable)
            ->unique();
    }
}
