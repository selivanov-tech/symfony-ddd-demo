<?php

namespace Api\Module\Customer\Controller;

use App\Module\Customer\Application\Command\UpdateCustomer\UpdateCustomerCommand;
use App\Module\Customer\Application\Query\GetCustomer\GetCustomerQuery;
use App\Module\Customer\Application\ReadModel\CustomerView;
use App\Module\Customer\Application\Request\CustomerUpdateRequest;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use App\Shared\Application\Bus\Query\QueryBusInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\UuidV7;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Customer')]
class UpdateCustomerController
{
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))]
    #[OA\RequestBody(content: new Model(type: CustomerUpdateRequest::class))]
    #[OA\Response(response: 200, description: 'The updated customer', content: new Model(type: CustomerView::class))]
    #[OA\Response(response: 400, description: 'Validation failed')]
    #[OA\Response(response: 404, description: 'Customer not found')]
    #[Route('customer/{id}', name: 'edit customer', methods: 'PATCH')]
    public function __invoke(
        UuidV7 $id,
        Request $request,
        CommandBusInterface $commandBus,
        QueryBusInterface $queryBus,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
    ): JsonResponse {
        $updateDTO = new CustomerUpdateRequest($request->toArray());

        $errors = $validator->validate($updateDTO, groups: ['CustomerUpdateRequest']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($updateDTO, $errors);
        }

        $commandBus->dispatch(new UpdateCustomerCommand(
            id: (string) $id,
            email: $updateDTO->email,
            phone: $updateDTO->phone,
            birthday: $updateDTO->birthday,
            firstName: $updateDTO->firstName,
            lastName: $updateDTO->lastName,
            ssn: $updateDTO->ssn,
            ficoScore: $updateDTO->ficoScore,
            address: $updateDTO->address?->toArray(),
            monthlyIncome: $updateDTO->monthlyIncome,
        ));

        $view = $queryBus->ask(new GetCustomerQuery((string) $id));

        return JsonResponse::fromJsonString($serializer->serialize($view, 'json'));
    }
}
