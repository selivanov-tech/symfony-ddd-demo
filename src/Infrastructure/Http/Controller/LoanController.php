<?php

namespace App\Infrastructure\Http\Controller;

use App\Application\Request\Loan\LoanUserRequest;
use App\Application\Service\Loan\LoanApplier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LoanController
{
    #[Route('loan/apply', name: 'apply for loan or check eligibility')]
    public function __invoke(
        #[MapQueryString] LoanUserRequest $loanUserRequest,
        LoanApplier $loanApplicationService,
        SerializerInterface $serializer
    ): Response {
        $loanApplicationService->setRequest($loanUserRequest);

        if ($loanUserRequest->onlyCheck) {
            $result = $loanApplicationService->isEligible();

            // todo: wrap $data with new LoanEligibilityCheckResultResource($result)
            //  (which will store OpenApi attributes)
            $data = ['result' => $result->success];

            if ($result->success === false) {
                $data['reason'] = $result->exception->getPublicReason();
            }

            return new JsonResponse($data);
        }

        $result = $loanApplicationService->applyForLoan();

        // todo: wrap $data with new LoanApplyResultResource($result)
        //   (which will store OpenApi attributes)
        $data = [
            'id' => $result->getId(),
            'result' => $result->isApproved(),
        ];

        return new JsonResponse($data);
    }
}
