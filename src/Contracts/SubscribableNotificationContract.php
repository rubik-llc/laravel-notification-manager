<?php

namespace Rubik\NotificationManager\Contracts;

use Illuminate\Support\Collection;

interface SubscribableNotificationContract
{
    public static function sendToSubscribers();

    public static function subscribers(): Collection;

    public static function subscribableNotificationType(): string;
}
