<?php

namespace Rubik\NotificationManager\Traits;

use Carbon\Carbon;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;
use Rubik\NotificationManager\Facades\NotificationManager as NotificationManagerFacade;
use Rubik\NotificationManager\Models\NotificationManager;

trait SubscribableNotification
{
    abstract public static function subscribableNotificationType(): string;

    abstract public function setData(): array;

    /**
     * Send a notification to all subscribers
     *
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
     * Get the notification's delivery channels.
     *
     * @param $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = $notifiable->notification(self::subscribableNotificationType());

        return new BroadcastMessage(array_merge($this->setData(), [
            'is_prioritized' => $notification->is_prioritized,
            'is_muted' => $notification->is_muted,
            'alert_type' => $notification->alert_type,
            'preview_type' => $notification->preview_type,
            'needs_authentication' => $notification->needs_authentication,
            'created_at' => Carbon::now(),
        ]));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        $notification = $notifiable->notification(self::subscribableNotificationType());

        return array_merge($this->setData(), [
            'is_prioritized' => $notification->is_prioritized,
            'is_muted' => $notification->is_muted,
            'alert_type' => $notification->alert_type,
            'preview_type' => $notification->preview_type,
            'needs_authentication' => $notification->needs_authentication,
            'read_at' => null,
            'seen_at' => null,
            'created_at' => Carbon::now(),
        ]);
    }

    public static function subscribe($channel = '*')
    {
        NotificationManagerFacade::subscribe(static::class, $channel);
    }

    public static function unsubscribe($channel = '*')
    {
        NotificationManagerFacade::unsubscribe(static::class, $channel);
    }

    public static function prioritize()
    {
        NotificationManagerFacade::prioritize(static::class);
    }

    public static function trivialize()
    {
        NotificationManagerFacade::subscribe(static::class);
    }

    public static function mute()
    {
        NotificationManagerFacade::mute(static::class);
    }

    public static function unmute()
    {
        NotificationManagerFacade::unmute(static::class);
    }

    public static function previewType(NotificationPreviewType $notificationPreviewType)
    {
        NotificationManagerFacade::previewType(static::class, $notificationPreviewType);
    }

    public static function alertType(NotificationAlertType $notificationAlertType)
    {
        NotificationManagerFacade::alertType(static::class, $notificationAlertType);
    }

    /**
     *
     * @return Collection
     */
    public static function subscribers(): Collection
    {
        return NotificationManager::query()
            ->subscribed()
            ->forNotification(self::subscribableNotificationType())
            ->get()
            ->map(fn (NotificationManager $notificationSubscription) => $notificationSubscription->notifiable)->unique();
    }
}
