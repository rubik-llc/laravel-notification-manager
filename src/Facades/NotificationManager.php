<?php

namespace Rubik\NotificationManager\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Rubik\NotificationManager\Enums\NotificationAlertType;
use Rubik\NotificationManager\Enums\NotificationPreviewType;

/**
 * @see \Rubik\NotificationManager\NotificationManager
 *
 * @method static subscribableNotifications(): array
 * @method static for(Model $model): void
 * @method static subscribe($subscribableNotificationClass, string $channel = '*'): void
 * @method static unsubscribe($subscribableNotificationClass, string $channel = '*'): void
 * @method static prioritize($subscribableNotificationClass): void
 * @method static trivialize( $subscribableNotificationClass): void
 * @method static subscribeAll(string $channel = '*'): void
 * @method static unsubscribeAll(string $channel = '*'): void
 * @method static previewType($subscribableNotificationClass, NotificationPreviewType $notificationPreviewType): void
 * @method static alertType($subscribableNotificationClass, NotificationAlertType $notificationAlertType): void
 */
class NotificationManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Rubik\NotificationManager\NotificationManager::class;
    }
}
