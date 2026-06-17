<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Loan\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Application\Loan\Command\ApplyForLoan\LoanDecision;
use App\Application\Loan\Query\CheckEligibility\CheckLoanEligibilityQuery;
use App\Application\Loan\ReadModel\EligibilityView;
use App\Application\Request\Loan\LoanUserRequest;
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
