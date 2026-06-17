<?php

namespace App\Application\Service\Loan;

use App\Application\Event\LoanRequestProcessedEvent;
use App\Infrastructure\Service\Notification\NotificationService;
use Symfony\Component\Notifier\Recipient\Recipient;

class LoanNotificationService
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function onLoanRequestProcessed(LoanRequestProcessedEvent $event): void
    {
        $loan = $event->loan;

        $customer = $loan->getCustomer();
        $customerName = $customer->getPresentedName();

        $content = match ($loan->getResult()) {
            true => sprintf(
                'Congratulations %s! Your loan for $%.2f has been approved.',
                $customerName,
                $loan->getAmount(),
            ),
            false => sprintf(
                'Dear %s, unfortunately your loan request has been denied. Please try again later.',
                $customerName
            )
        };

        $this->notificationService->send(
            subject: 'Loan Request Results',
            content: $content,
            recipient: new Recipient(
                email: $customer->getEmail(),
                phone: $customer->getPhone(),
            )
        );
    }
}
