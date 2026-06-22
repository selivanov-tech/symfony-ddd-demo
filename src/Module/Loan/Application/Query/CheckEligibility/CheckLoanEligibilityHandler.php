<?php

declare(strict_types=1);

namespace App\Module\Loan\Application\Query\CheckEligibility;

use App\Module\Loan\Application\Exception\CustomerNotFoundException;
use App\Module\Loan\Application\Exception\ProductNotFoundException;
use App\Module\Loan\Application\ReadModel\EligibilityView;
use App\Module\Loan\Application\Repository\ApplicantReadModelRepositoryInterface;
use App\Module\Loan\Application\Repository\ProductReadModelRepositoryInterface;
use App\Module\Loan\Domain\Exception\LoanApplicationDeniedException;
use App\Module\Loan\Domain\Service\LoanEligibilityChecker;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class CheckLoanEligibilityHandler
{
    public function __construct(
        private readonly ProductReadModelRepositoryInterface $productReadRepository,
        private readonly ApplicantReadModelRepositoryInterface $applicantReadRepository,
        private readonly LoanEligibilityChecker $checker,
    ) {
    }

    public function __invoke(CheckLoanEligibilityQuery $query): EligibilityView
    {
        $offer = $this->productReadRepository->findById($query->productId) ?? throw new ProductNotFoundException();
        $applicant = $this->applicantReadRepository->findById($query->customerId) ?? throw new CustomerNotFoundException();

        try {
            $this->checker->isEligible($offer->terms, $applicant->credit);
        } catch (LoanApplicationDeniedException $exception) {
            return new EligibilityView(eligible: false, reason: $exception->getPublicReason());
        }

        return new EligibilityView(eligible: true);
    }
}
