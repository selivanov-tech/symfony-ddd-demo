<?php

declare(strict_types=1);

namespace App\Application\Loan\Command\ApplyForLoan;

use App\Application\Exception\Customer\CustomerNotFoundException;
use App\Application\Exception\Product\ProductNotFoundException;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use App\Domain\Loan\Entity\Loan;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Loan\Repository\LoanRepositoryInterface;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\Identity\UuidFactoryInterface;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class ApplyForLoanHandler
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CustomerRepositoryInterface $customers,
        private readonly LoanRepositoryInterface $loans,
        private readonly LoanEligibilityChecker $checker,
        private readonly UuidFactoryInterface $uuidFactory,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ApplyForLoanCommand $command): LoanDecision
    {
        $product = $this->products->findById($command->productId) ?? throw new ProductNotFoundException();
        $customer = $this->customers->findById($command->customerId) ?? throw new CustomerNotFoundException();
        $amount = Money::fromFloat($product->getAmount());

        try {
            $this->checker->isEligible($product, $customer);
            $loan = Loan::approved($this->uuidFactory, $customer, $product, $amount);
        } catch (LoanApplicationDeniedException $exception) {
            $loan = Loan::rejected($this->uuidFactory, $customer, $product, $amount, $exception->getReason());
        }

        $this->loans->save($loan);
        $this->eventBus->publish(...$loan->releaseEvents());

        return new LoanDecision($loan->getId()->toString(), $loan->isApproved());
    }
}
