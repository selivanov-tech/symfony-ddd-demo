<?php

namespace App\Infrastructure\Event\Subscriber;

use App\Application\Event\LoanRequestProcessedEvent;
use App\Application\Service\Loan\LoanNotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoanEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoanNotificationService $loanNotificationService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoanRequestProcessedEvent::class => 'onLoanRequestProcessed',
        ];
    }

    public function onLoanRequestProcessed(LoanRequestProcessedEvent $event): void
    {
        $this->loanNotificationService->onLoanRequestProcessed($event);
    }
}
