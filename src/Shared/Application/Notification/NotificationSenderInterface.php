<?php

declare(strict_types=1);

namespace App\Shared\Application\Notification;

use Symfony\Component\Notifier\Recipient\Recipient;

interface NotificationSenderInterface
{
    public function send(string $subject, string $content, Recipient $recipient): void;
}
