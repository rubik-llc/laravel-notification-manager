<?php

namespace Rubik\NotificationManager\Enums;

enum NotificationPreviewType: string
{
    case ALWAYS = 'always';
    case WHEN_UNLOCKED = 'when-unlocked';
    case NEVER = 'never';
}
