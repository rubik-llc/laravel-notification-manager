<?php

namespace Rubik\NotificationManager\Enums;

enum NotificationAlertType: string
{
    case NOTIFICATION_CENTER = 'notification-center';
    case BANNER = 'banner';
    case LOCK_SCREEN = 'lock-screen';
}
