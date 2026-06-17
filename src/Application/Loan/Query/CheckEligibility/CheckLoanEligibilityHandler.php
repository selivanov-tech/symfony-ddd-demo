<?php

declare(strict_types=1);

namespace App\Application\Loan\Query\CheckEligibility;

use App\Application\Exception\Customer\CustomerNotFoundException;
use App\Application\Exception\Product\ProductNotFoundException;
use App\Application\Loan\ReadModel\EligibilityView;
use App\Domain\Customer\Repository\CustomerRepositoryInterface;
use App\Domain\Loan\Exception\LoanApplicationDeniedException;
use App\Domain\Loan\Service\LoanEligibilityChecker;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class CheckLoanEligibilityHandler
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CustomerRepositoryInterface $customers,
        private readonly LoanEligibilityChecker $checker,
    ) {
    }

    public function __invoke(CheckLoanEligibilityQuery $query): EligibilityView
    {
        $product = $this->products->findById($query->productId) ?? throw new ProductNotFoundException();
        $customer = $this->customers->findById($query->customerId) ?? throw new CustomerNotFoundException();

        try {
            $this->checker->isEligible($product, $customer);
        } catch (LoanApplicationDeniedException $exception) {
            return new EligibilityView(eligible: false, reason: $exception->getPublicReason());
        }

        return new EligibilityView(eligible: true);
    }
}
