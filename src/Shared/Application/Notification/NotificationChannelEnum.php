<?php

namespace App\Shared\Application\Notification;

enum NotificationChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
