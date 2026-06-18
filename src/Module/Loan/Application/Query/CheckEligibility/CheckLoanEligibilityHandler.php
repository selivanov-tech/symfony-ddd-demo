<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Query\CheckEligibility;

use App\Module\Customer\Application\Exception\CustomerNotFoundException;
use App\Module\Customer\Domain\Repository\CustomerRepositoryInterface;
use App\Module\Loan\Application\ReadModel\EligibilityView;
use App\Module\Loan\Domain\Exception\LoanApplicationDeniedException;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use App\Module\Product\Application\Exception\ProductNotFoundException;
use App\Module\Product\Domain\Repository\ProductRepositoryInterface;
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
