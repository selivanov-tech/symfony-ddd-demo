<?php

namespace Api\Module\Loan\Controller;

use App\Module\Loan\Application\Query\CheckEligibility\CheckLoanEligibilityQuery;
use App\Module\Loan\Application\ReadModel\EligibilityView;
use App\Module\Loan\Application\Request\LoanUserRequest;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Loan')]
class CheckEligibilityController
{
    #[OA\Response(
        response: 200,
        description: 'Eligibility result',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'result', type: 'boolean'),
            new OA\Property(property: 'reason', type: 'string', nullable: true, description: 'Present only when not eligible'),
        ]),
    )]
    #[Route('loan/eligibility', name: 'check loan eligibility', methods: 'GET')]
    public function __invoke(
        #[MapQueryString] LoanUserRequest $loanRequest,
        QueryBusInterface $queryBus,
    ): JsonResponse {
        $view = $queryBus->ask(new CheckLoanEligibilityQuery(
            productId: $loanRequest->productId,
            customerId: $loanRequest->customerId,
        ));
        assert($view instanceof EligibilityView);

        $data = ['result' => $view->eligible];
        if (!$view->eligible) {
            $data['reason'] = $view->reason;
        }

        return new JsonResponse($data);
    }
}
