<?php

namespace Api\Module\Customer\Controller;

use App\Module\Customer\Application\Command\CreateCustomer\CreateCustomerCommand;
use App\Module\Customer\Application\Request\CustomerCreateRequest;
use App\Shared\Application\Bus\Command\CommandBusInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Customer')]
class CreateCustomerController
{
    #[OA\RequestBody(content: new Model(type: CustomerCreateRequest::class))]
    #[OA\Response(
        response: 200,
        description: 'Customer created',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        ]),
    )]
    #[OA\Response(response: 400, description: 'Validation failed')]
    #[Route('customer/create', name: 'create customer', methods: 'POST')]
    public function __invoke(
        Request $request,
        CommandBusInterface $commandBus,
        ValidatorInterface $validator,
    ): JsonResponse {
        $createDTO = new CustomerCreateRequest($request->toArray());

        $errors = $validator->validate($createDTO, groups: ['CustomerCreateRequest']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($createDTO, $errors);
        }

        $id = $commandBus->dispatch(new CreateCustomerCommand(
            email: (string) $createDTO->email,
            phone: (string) $createDTO->phone,
            birthday: (string) $createDTO->birthday,
            firstName: (string) $createDTO->firstName,
            lastName: (string) $createDTO->lastName,
            ssn: (string) $createDTO->ssn,
            ficoScore: (int) $createDTO->ficoScore,
            address: $createDTO->address?->toArray() ?? [],
            monthlyIncome: (int) $createDTO->monthlyIncome,
        ));

        return new JsonResponse(['id' => $id]);
    }
}
