<?php

namespace Api\Module\Loan\Controller;

use App\Module\Loan\Application\Command\ApplyForLoan\ApplyForLoanCommand;
use App\Module\Loan\Application\Command\ApplyForLoan\LoanDecision;
use App\Module\Loan\Application\Request\LoanUserRequest;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Loan')]
class ApplyForLoanController
{
    #[OA\RequestBody(content: new Model(type: LoanUserRequest::class))]
    #[OA\Response(
        response: 200,
        description: 'Loan decision',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
            new OA\Property(property: 'result', type: 'boolean', description: 'Whether the loan was approved'),
        ]),
    )]
    #[OA\Response(response: 404, description: 'Customer or product not found')]
    #[Route('loan/applications', name: 'apply for loan', methods: 'POST')]
    public function __invoke(
        #[MapRequestPayload] LoanUserRequest $loanRequest,
        CommandBusInterface $commandBus,
    ): JsonResponse {
        $decision = $commandBus->dispatch(new ApplyForLoanCommand(
            productId: $loanRequest->productId,
            customerId: $loanRequest->customerId,
        ));
        assert($decision instanceof LoanDecision);

        return new JsonResponse(['id' => $decision->loanId, 'result' => $decision->approved]);
    }
}
