<?php

namespace App\Infrastructure\Service\Notification;

use App\Application\Enum\NotificationChannelEnum;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Validator\Constraints as Assert;

class NotificationService
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        #[Assert\All(
            new Assert\Type(NotificationChannelEnum::class),
        )]
        private readonly array $channels,
    ) {
    }

    public function send(string $subject, string $content, Recipient $recipient): void
    {
        $notification = new Notification(
            subject: $subject,
            channels: array_map(fn (NotificationChannelEnum $channel): string => $channel->value, $this->channels)
        );

        $notification->content($content);

        $this->notifier->send($notification, $recipient);
    }
}
