<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Command\ApplyForLoan;

use App\Module\Loan\Application\Exception\CustomerNotFoundException;
use App\Module\Loan\Application\Exception\ProductNotFoundException;
use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Application\Repository\ProductReadModelRepositoryInterface;
use App\Module\Loan\Domain\Entity\Loan;
use App\Module\Loan\Domain\Exception\LoanApplicationDeniedException;
use App\Module\Loan\Domain\Repository\LoanRepositoryInterface;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class ApplyForLoanHandler
{
    public function __construct(
        private readonly ProductReadModelRepositoryInterface $productReadRepository,
        private readonly ApplicantReadModelRepositoryInterface $applicantReadRepository,
        private readonly LoanRepositoryInterface $loans,
        private readonly LoanEligibilityChecker $checker,
        private readonly UuidFactoryInterface $uuidFactory,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ApplyForLoanCommand $command): LoanDecision
    {
        $offer = $this->productReadRepository->findById($command->productId) ?? throw new ProductNotFoundException();
        $applicant = $this->applicantReadRepository->findById($command->customerId) ?? throw new CustomerNotFoundException();
        $amount = Money::fromFloat($offer->amount);

        try {
            $this->checker->isEligible($offer->terms, $applicant->credit);
            $loan = Loan::approved($this->uuidFactory, $applicant->id, $offer->id, $amount);
        } catch (LoanApplicationDeniedException $exception) {
            $loan = Loan::rejected($this->uuidFactory, $applicant->id, $offer->id, $amount, $exception->getReason());
        }

        $this->loans->save($loan);
        $this->eventBus->publish(...$loan->releaseEvents());

        return new LoanDecision($loan->getId()->toString(), $loan->isApproved());
    }
}
