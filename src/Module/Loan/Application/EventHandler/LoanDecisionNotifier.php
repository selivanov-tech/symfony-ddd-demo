<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\EventHandler;

use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Event\LoanApproved;
use App\Module\Loan\Domain\Event\LoanRejected;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Shared\Application\Notification\NotificationSenderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\Recipient\Recipient;

final class LoanDecisionNotifier
{
    public function __construct(
        private readonly LoanRepositoryInterface $loans,
        private readonly ApplicantReadModelRepositoryInterface $applicantReadRepository,
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

        $applicant = $this->applicantReadRepository->findById($loan->getCustomerId()->toString());
        if ($applicant === null) {
            return;
        }

        $this->notifications->send(
            subject: 'Loan Request Results',
            content: $this->content($loan, $applicant->name),
            recipient: new Recipient(email: $applicant->email, phone: $applicant->phone),
        );
    }

    private function content(Loan $loan, string $name): string
    {
        return $loan->isApproved()
            ? sprintf('Congratulations %s! Your loan for $%.2f has been approved.', $name, $loan->getAmount()->toFloat())
            : sprintf('Dear %s, unfortunately your loan request has been denied. Please try again later.', $name);
    }
}
