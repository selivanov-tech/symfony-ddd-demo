<?php

declare(strict_types=1);

namespace App\Application\Loan\EventHandler;

use App\Application\Notification\NotificationSenderInterface;
use App\Domain\Loan\Entity\Loan;
use App\Domain\Loan\Event\LoanApproved;
use App\Domain\Loan\Event\LoanRejected;
use App\Domain\Loan\Repository\LoanRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\Recipient\Recipient;

final class LoanDecisionNotifier
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly NotificationSenderInterface $notifications,
    ) {
    }

    #[AsMessageHandler(bus: 'event.bus')]
    public function onApproved(LoanApproved $event): void
    {
        $this->notify($event->aggregateId());
    }

    #[AsMessageHandler(bus: 'event.bus')]
    public function onRejected(LoanRejected $event): void
    {
        $this->notify($event->aggregateId());
    }

    private function notify(string $loanId): void
    {
        $loan = $this->loans->findById($loanId);
        if ($loan === null) {
            return;
        }

        $customer = $loan->getCustomer();

        $this->notifications->send(
            subject: 'Loan Request Results',
            content: $this->content($loan),
            recipient: new Recipient(email: $customer->getEmail(), phone: $customer->getPhone()),
        );
    }

    private function content(Loan $loan): string
    {
        $name = $loan->getCustomer()->getPresentedName();

        return $loan->isApproved()
            ? sprintf('Congratulations %s! Your loan for $%.2f has been approved.', $name, $loan->getAmount()->toFloat())
            : sprintf('Dear %s, unfortunately your loan request has been denied. Please try again later.', $name);
    }
}
