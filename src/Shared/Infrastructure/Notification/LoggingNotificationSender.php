<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Notification;

use App\Shared\Application\Notification\NotificationSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final class LoggingNotificationSender implements NotificationSenderInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function send(string $subject, string $content, Recipient $recipient): void
    {
        $this->logger->info($subject, [
            'email' => $recipient->getEmail(),
            'phone' => $recipient->getPhone(),
            'content' => $content,
        ]);
    }
}
