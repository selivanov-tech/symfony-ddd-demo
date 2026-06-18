<?php

namespace Api\Module\Loan\Controller;

use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Module\Loan\Application\Command\ApplyForLoan\LoanDecision;
use App\Module\Loan\Application\Query\CheckEligibility\CheckLoanEligibilityQuery;
use App\Module\Loan\Application\ReadModel\EligibilityView;
use App\Module\Loan\Application\Request\LoanUserRequest;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

class LoanController
{
    #[Route('loan/apply', name: 'apply for loan or check eligibility')]
    public function __invoke(
        #[MapQueryString] LoanUserRequest $loanUserRequest,
        CommandBusInterface $commandBus,
        QueryBusInterface $queryBus,
    ): Response {
        if ($loanUserRequest->onlyCheck) {
            $view = $queryBus->ask(new CheckLoanEligibilityQuery(
                productId: $loanUserRequest->productId,
                customerId: $loanUserRequest->customerId,
            ));
            assert($view instanceof EligibilityView);

            $data = ['result' => $view->eligible];
            if (!$view->eligible) {
                $data['reason'] = $view->reason;
            }

            return new JsonResponse($data);
        }

        $decision = $commandBus->dispatch(new ApplyForLoanCommand(
            productId: $loanUserRequest->productId,
            customerId: $loanUserRequest->customerId,
        ));
        assert($decision instanceof LoanDecision);

        return new JsonResponse(['id' => $decision->loanId, 'result' => $decision->approved]);
    }
}
