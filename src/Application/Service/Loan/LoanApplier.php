<?php

namespace App\Application\Service\Loan;

use App\Application\DTO\Loan\LoanEligibilityResultDTO;
use App\Application\Event\LoanRequestProcessedEvent;
use App\Application\Exception\Customer\CustomerNotFoundException;
use App\Application\Exception\Product\ProductNotFoundException;
use App\Application\Request\Loan\LoanUserRequest;
use App\Domain\Customer\Entity\Customer;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use App\Domain\Loan\Entity\Loan;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Loan\Repository\LoanRepositoryInterface;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Shared\Domain\ValueObject\Money;
use Psr\EventDispatcher\EventDispatcherInterface;

class LoanApplier
{
    private Product $product;
    private Customer $customer;

    public function __construct(
        private readonly ProductRepositoryInterface $productRepo,
        private readonly CustomerRepositoryInterface $customerRepo,
        private readonly LoanRepositoryInterface $loanRepo,
        private readonly LoanEligibilityChecker $loanChecker,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function setRequest(LoanUserRequest $loanUserRequest): self
    {
        $product = $this->productRepo->findById($loanUserRequest->productId);
        if (is_null($product)) {
            throw new ProductNotFoundException();
        }

        $this->product = $product;

        $customer = $this->customerRepo->findById($loanUserRequest->customerId);
        if (is_null($customer)) {
            throw new CustomerNotFoundException();
        }

        $this->customer = $customer;

        return $this;
    }

    public function isEligible(): LoanEligibilityResultDTO
    {
        try {
            $this->loanChecker->isEligible($this->product, $this->customer);
        } catch (LoanApplicationDeniedException $ex) {
            return new LoanEligibilityResultDTO(success: false, exception: $ex);
        }

        return new LoanEligibilityResultDTO(success: true);
    }

    public function applyForLoan(): Loan
    {
        $eligibilityResult = $this->isEligible();
        $amount = Money::fromFloat($this->product->getAmount());

        $loan = $eligibilityResult->success
            ? Loan::approved($this->customer, $this->product, $amount)
            : Loan::rejected($this->customer, $this->product, $amount, (string) $eligibilityResult->exception?->getReason());

        $this->loanRepo->save($loan);

        $this->eventDispatcher->dispatch(new LoanRequestProcessedEvent(loan: $loan));

        return $loan;
    }
}
